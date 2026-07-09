<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AllClass;
use App\Models\ModelTestResult;
use App\Models\Blog;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ScholarShip;
use App\Models\ScholarShipEnroll;
use App\Models\ScholarShipResult;
use App\Models\Slider;
use App\Repositories\Interfaces\SettingRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use App\Traits\HttpWebResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Jetstream\Jetstream;
use Laravel\Fortify\Rules\Password;

class SAdminController extends Controller
{
    use HttpWebResponse;
    public function adminlist()
    {
        // $adminlist = User::paginate(10);
        $adminlist = User::get();
        $allclasslist = AllClass::get();
        return view('admin.admin.index', [
            'adminlist' => $adminlist,
            'allclasslist' => $allclasslist,
        ]);
    }
    public function adminview($id)
    {
        // $adminlist = User::paginate(10);
        $admin = User::find($id);
        $adminlist = User::get();
        return view('admin.admin.show', [
            'admin' => $admin,
            'adminlist' => $adminlist,
        ]);
    }

    public function admindelete($id)
    {
        // $adminlist = User::paginate(10);
        $admin = User::find($id);
        $admin->delete();
        return redirect('/adminlist')->with('warning', 'Admin deleted successful.!');
    }
    public function createadmin(Request $request)
    {
        $request->validate([
            'user_type' => 'required',
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', new Password(), 'confirmed'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);
        $redrose_id = str_replace(' ', '', Str::lower($request['name']) . rand(100, 99999));
        $user = User::create([
            'user_type' => $request['user_type'],
            'name' => $request['name'],
            'email' => $request['email'],
            'password' => Hash::make($request['password']),
            'redrose_id' => $redrose_id,
            'date' => now()->toDateString(),
            'once' => 'no',
            'points' => '10',
            'class' => $request['class'],
        ])->id;
        $this->notification($user, $request['name'] . ' Admin Created successful.!',  'admin', '1');
        return redirect('/adminlist')->with('success', $request['name'] . ' - Admin Created successfull.!');
    }

    public function alluser()
    {
        $users = User::where('user_type', '2')->paginate(20);
        return view('admin.pages.user', compact('users'));
    }
    public function admin_approval_teacher_index()
    {
        return $this->admin_approval_teacher('Approved');
    }
    public function admin_approval_teacher_pending()
    {
        return $this->admin_approval_teacher('Pending');
    }
    public function admin_approval_teacher_unapproved()
    {
        return $this->admin_approval_teacher('Unapproved');
    }

    public function admin_approval_teacher($viewType)
    {
        $query = User::where('as_user', 'teacher');
        switch ($viewType) {
            case 'Pending':
                $query->where('update_status', 2);
                break;
            case 'Unapproved':
                $query->where('update_status', 3);
                break;
            default:
                $query->where('update_status', 1);
                break;
        }
        $users = $query->paginate(20);
        return view('admin.approval.index', compact('users', 'viewType', 'query'));
    }

    public function admin_approval_teacher_edit($id, $type)
    {
        $user = User::find($id);
        return view('admin.approval.edit', compact('user', 'type'));
    }
    public function admin_approval_teacher_approve(Request $request, $id)
    {
        $user = User::find($id);
        $data = [];
        $fillableFields = ['title_description', 'about_teacher', 'about_teaching', 'remark', 'about_student'];
        foreach ($fillableFields as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->input($field);
            }
        }
        $user->update(array_merge($data, ['as_user' => 'teacher', 'is_first' => false, 'status' => true, 'update_status' => $request->type]));
        $route = $request->type == 1 ? 'admin.approval.teacher.index' : ($request->type == 2 ? 'admin.approval.teacher.pending' : 'admin.approval.teacher.unapproved');
        return redirect()->route($route)->with('success', 'Teacher Update successfull...!');
    }

    public function slider()
    {
        $sliders = Slider::get();
        return view('admin.pages.slider', compact('sliders'));
    }
    public function slider_store(Request $request)
    {
        $request->validate([
            'type' => 'required',
            'image_path' => 'required',
        ]);
        $image = upload_file($request->image_path);
        Slider::create([
            'type' => $request['type'],
            'short_description' => $request['short_description'],
            'image_path' => $image,
        ]);
        $this->notification(Auth::user()->id, $request['type'] . ' slider added successful.!',  'slider', '1');
        return back()->with('success', $request['type'] . ' - slider added successful.!');
    }

    public function app_version(SettingRepositoryInterface $repo)
    {
        $app_version = $repo->getSetting('app_version');
        $app_version_text = $repo->getSetting('app_version_text');
        return view('admin.pages.app-version', compact('app_version', 'app_version_text'));
    }
    public function app_version_store(Request $request, SettingRepositoryInterface $repo)
    {
        $request->validate([
            'app_version' => 'required',
            'app_version_text' => 'required',
        ]);
        $data['app_version'] = $request['app_version'];
        $data['app_version_text'] = $request['app_version_text'];
        $repo->storeOrUpdate($data);
        return back()->with('success', 'App Version store successful.!');
    }

    public function modeltest_result()
    {
        $modeltestresultlist = ModelTestResult::orderby('total_mark', 'desc')->paginate(20);
        return view('admin.pages.mresult', [
            'modeltestresultlist' => $modeltestresultlist,
        ]);
    }
    public function blog_create()
    {
        $bloglist = Blog::paginate(20);
        return view('admin.blog.index', [
            'bloglist' => $bloglist
        ]);
    }
    public function blog_store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required'
        ]);
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" create new blog "' . $request['name'] . '" successful.!',  'blog', '1');
        $blogs = Blog::createStore($request);
        if ($blogs) {
            return back()->with('success', 'New blog "' . $request['name'] . '" created successfull.!');
        } else {
            return back()->with('warning', 'Error check all data again and submit again...!');
        }
    }
    public function blog_show($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        $bloglist = Blog::paginate(20);
        return view('admin.blog.show', [
            'bloglist' => $bloglist,
            'blog' => $blog
        ]);
    }
    public function blog_edit($slug)
    {
        $blog = Blog::where('slug', $slug)->first();
        $bloglist = Blog::paginate(20);
        return view('admin.blog.edit', [
            'bloglist' => $bloglist,
            'blog' => $blog
        ]);
    }
    public function blog_update(Request $request, $id)
    {
        $user = Auth::user();
        $blog = Blog::find($id);
        $blogs = Blog::updateStore($request, $id);
        $this->notification($user->id, '"' . $user->name . '" update blog "' . $blog->name . '" successful.!',  'blog', '1');
        if ($blogs) {
            return redirect(route('blog.show', Blog::find($id)->slug))->with('success', 'blog "' . $blog->name . '" update successfull.!');
        } else {
            return back()->with('warning', 'Error check all data again and submit again...!');
        }
    }
    public function blog_delete($id)
    {
        $user = Auth::user();
        $blog = Blog::find($id);
        $blog->delete();
        $this->notification($user->id, '"' . $user->name . '" delete blog "' . $blog->name . '" successful.!',  'blog', '1');
        return redirect(route('blog.create'))->with('warning', 'blog "' . $blog->name . '" delete successfull.!');
    }
    public function page_create()
    {
        $pagelist = Page::paginate(20);
        return view('admin.page.index', [
            'pagelist' => $pagelist
        ]);
    }
    public function page_store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'description' => 'required',
        ]);
        $user = Auth::user();
        $this->notification($user->id, '"' . $user->name . '" create new page "' . $request['name'] . '" successful.!',  'page', '1');
        Page::create([
            'title' => $request->title,
            'type' => $request->type,
            'slug' => Str::slug($request->title),
            'description' => $request->description,
        ]);
        return back()->with('success', 'New page "' . $request['title'] . '" created successfull.!');
    }
    public function page_show($id)
    {
        $page = Page::find($id);
        $pagelist = Page::paginate(20);
        return view('admin.page.show', [
            'pagelist' => $pagelist,
            'page' => $page
        ]);
    }
    public function page_edit($id)
    {
        $page = Page::find($id);
        $pagelist = Page::paginate(20);
        return view('admin.page.edit', [
            'pagelist' => $pagelist,
            'page' => $page
        ]);
    }
    public function page_update(Request $request, $id)
    {
        $user = Auth::user();
        $page = Page::find($id);
        Page::where('id', $id)->update([
            'title' => $request['title'],
            'description' => $request['description']
        ]);
        $this->notification($user->id, '"' . $user->name . '" update page "' . $page->name . '" successful.!',  'page', '1');
        return redirect(route('page.show', $id))->with('success', 'page "' . $page->name . '" update successfull.!');
    }
    public function page_delete($id)
    {
        $user = Auth::user();
        $page = Page::find($id);
        $page->delete();
        $this->notification($user->id, '"' . $user->name . '" delete page "' . $page->name . '" successful.!',  'page', '1');
        return redirect(route('page.create'))->with('warning', 'page "' . $page->name . '" delete successfull.!');
    }
    public function scholarship_create()
    {
        return view('admin.schollership.index');
    }
    public function scholarship_store(Request $request)
    {
        $scholarShip = ScholarShip::createStore($request);
        if ($scholarShip) {
            $user = Auth::user();
            $this->notification($user->id, '"' . $user->name . '" create new ScholarShip "' . $request['title'] . '" successful.!',  'scholarShip', '1');
            return back()->with('success', 'New ScholarShip "' . $request['title'] . '" created successfull.!');
        } else {
            return back()->with('error', 'New ScholarShip "' . $request['title'] . '" can not created successfull.!');
        }
    }
    public function scholarship_show($slug)
    {
        $scholarShip = ScholarShip::where('slug', $slug)->first();
        $is_show = 'view';
        return view('admin.schollership.show', compact('scholarShip', 'is_show'));
    }
    public function scholarship_edit($slug)
    {
        $scholarShip = ScholarShip::where('slug', $slug)->first();
        return view('admin.schollership.edit', compact('scholarShip'));
    }
    public function scholarship_update(Request $request, $id)
    {
        $scholarShip = ScholarShip::updateStore($request, $id);
        if ($scholarShip) {
            $user = Auth::user();
            $this->notification($user->id, '"' . $user->name . '" update ScholarShip "' . $request['title'] . '" successful.!',  'scholarShip', '1');
            return back()->with('success', 'ScholarShip "' . $request['title'] . '" update successfull.!');
        } else {
            return back()->with('error', 'ScholarShip "' . $request['title'] . '" can not update successfull.!');
        }
    }
    public function scholarship_delete($slug)
    {
        $user = Auth::user();
        $scholarShip = ScholarShip::where('slug', $slug)->first();
        $scholarShip->delete();
        $this->notification($user->id, '"' . $user->name . '" delete scholarShip "' . $scholarShip->title . '" successful.!',  'scholarShip', '1');
        return redirect(route('page.create'))->with('warning', 'scholarShip "' . $scholarShip->title . '" delete successfull.!');
    }
    public function scholarship_publish($slug)
    {
        $scholarShip = ScholarShip::with('enrollments')->where('slug', $slug)->first();
        if ($scholarShip) {
            if ($scholarShip->status && !$scholarShip->is_publish) {
                $userIds = $scholarShip->enrollments->pluck('user_id')->toArray();
                ScholarShipResult::where('scholar_ship_id', $scholarShip->id)->delete();
                $results = [];
                $randomizedUsers = collect($userIds)->map(function ($userId) {
                    return ['user_id' => $userId, 'random' => rand()];
                })->sortBy('random')->values();
                $winnerLimit = $scholarShip->winner_limit;
                foreach ($randomizedUsers as $index => $user) {
                    $isWinner = $index < $winnerLimit ? true : false;
                    $result = ScholarShipResult::create([
                        'user_id' => $user['user_id'],
                        'scholar_ship_id' => $scholarShip->id,
                        'order_by' => $index + 1,
                        'is_winner' => $isWinner,
                    ]);
                    $results[] = $result;
                }
                $scholarShip->is_publish = true;
                $scholarShip->status = false;
                $scholarShip->save();
                return back()->with('success', 'This Scholarship successfully published Now');
            } else {
                return back()->with('error', 'This Scholarship already published');
            }
        }else {
            return back()->with('error', 'Scholarship not found');
        }
    }
    public function scholarship_result($slug)
    {
        $scholarShip = ScholarShip::where('slug', $slug)->first();
        if ($scholarShip) {
            if ($scholarShip->is_publish && !$scholarShip->status) {
                $scholarShipResults = ScholarShipResult::where('scholar_ship_id', $scholarShip->id)->get();
                $is_show = 'result';
                return view('admin.schollership.show', compact('scholarShip', 'is_show', 'scholarShipResults'));
            } else {
                return back()->with('error', 'This Scholarship not published yeat');
            }
        }else {
            return back()->with('error', 'Scholarship not found');
        }
    }
    public function scholarship_participant($slug)
    {
        $scholarShip = ScholarShip::with('enrollments')->where('slug', $slug)->first();
        if ($scholarShip) {
            $scholarShipEnrolls = ScholarShipEnroll::where('scholar_ship_id', $scholarShip->id)->get();
            $is_show = 'participant';
            return view('admin.schollership.show', compact('scholarShip', 'is_show', 'scholarShipEnrolls'));
        }else {
            return back()->with('error', 'Scholarship not found');
        }
    }
    public function superadmin_slug(Request $request)
    {
        if ($request->type == 'blog') {
            DB::transaction(function () {
                Blog::chunk(100, function ($blogs) {
                    foreach ($blogs as $blog) {
                        $slug = 'slug-' . $blog->id . '-' . get_random_number(10);
                        $blog->update(['slug' => $slug]);
                    }
                });
            });
        }
        elseif ($request->type == 'book') {
            DB::transaction(function () {
                Blog::chunk(100, function ($blogs) {
                    foreach ($blogs as $blog) {
                        $slug = 'slug-' . $blog->id . '-' . get_random_number(10);
                        $blog->update(['slug' => $slug]);
                    }
                });
            });
        }
        else {
            DB::transaction(function () {
                User::chunk(100, function ($users) {
                    foreach ($users as $user) {
                        $slug = 'abmn-slug-' . $user->id . '-' . get_random_number(10);
                        $user->update(['slug' => $slug]);
                    }
                });
            });
        }
    }
}
