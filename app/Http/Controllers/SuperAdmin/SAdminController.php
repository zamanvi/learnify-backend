<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\AllClass;
use App\Models\Page;
use Illuminate\Http\Request;
use App\Models\User;
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
            'user_type' => ['required', 'in:1'],
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
    public function superadmin_slug(Request $request)
    {
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
