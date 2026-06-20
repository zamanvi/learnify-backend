<?php

namespace App\Http\Controllers\Api\v2\Utility;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApiProfileController extends Controller
{
    use HttpAppResponse;
    private static function increment_slug($slug)
    {
        $max = User::where('slug', 'LIKE', "{$slug}%")->latest('id')->value('slug');
        if ($max) {
            $parts = explode('-', $max);
            $number = intval(end($parts));
            $slug = $slug . '-' . ($number + 1);
        } else {
            $slug = $slug . '-1';
        }
        return $slug . '-' . get_random_number(10);
    }
    // View My Profile start
    public function all_user(Request $request)
    {
        $paginat = get_pagination(10); // change value while setting configure
        if ($request->has('per_page')) {
            $paginat = $request->per_page;
        }
        $users = User::where('status', true)->select('id', 'name', 'email', 'redrose_id', 'user_type', 'as_user')->paginate($paginat);
        return $this->apiResponse(['users' => $users], true, 'Get all User.', AppResponse::HTTP_OK);
    }
    public function teacher_profile($slug)
    {
        $user = User::where('slug', $slug)->first();
        return $this->apiResponse(['user' => $user], true, 'View teacher profile data.', AppResponse::HTTP_OK);
    }
    public function all_teacher(Request $request)
    {
        $paginat = get_pagination(10); // change value while setting configure
        if ($request->has('per_page')) {
            $paginat = $request->per_page;
        }
        $users = User::where('status', true)->where('as_user', 'teacher')->where('update_status', 1)->select('id', 'slug', 'name', 'email', 'gender', 'redrose_id', 'user_type', 'as_user', 'title', 'institute', 'tuition_subject', 'profile_photo_path')->paginate($paginat);
        return $this->apiResponse(['users' => $users], true, 'Get all User.', AppResponse::HTTP_OK);
    }
    public function search_teacher(Request $request)
    {
        $searchParams = [
            'title' => $request->search_data,
            'place_of_learning' => $request->search_data,
        ];
        $paginat = 10;
        if ($request->has('per_page')) {
            $paginat = $request->per_page;
        }
        $teachers = $this->search($request->search_data, $paginat);
        return $this->apiResponse(['teachers' => $teachers], true, 'Get search teachers.', AppResponse::HTTP_OK);
    }

    public static function search($param, $perPage)
    {
        $query = User::where('status', true)->where('as_user', 'teacher')->where('update_status', 1)->select('id', 'slug', 'title', 'place_of_learning', 'through_of_learning', 'medium', 'institute', 'group_subject', 'tuition_subject', 'degree', 'area_country', 'area_division', 'area_city', 'area_upazila', 'area_post_office', 'area_union', 'area_village', 'name', 'email', 'user_type', 'as_user', 'gender', 'profile_photo_path');
        $query->where(function ($query) use ($param) {
            $searchableFields = ['title', 'place_of_learning', 'through_of_learning', 'medium', 'institute', 'group_subject', 'tuition_subject', 'degree', 'area_country', 'area_division', 'area_city', 'area_upazila', 'area_post_office', 'area_union', 'area_village', 'name', 'email', 'gender'];
            foreach ($searchableFields as $field) {
                $query->orWhere($field, 'LIKE', '%' . $param . '%');
            }
        });
        return $query->paginate($perPage);
    }

    public function all_student(Request $request)
    {
        $paginat = 10;
        if ($request->has('per_page')) {
            $paginat = $request->per_page;
        }
        $users = User::where('status', true)->where('as_user', 'student')->select('id', 'name', 'email', 'redrose_id', 'user_type', 'as_user', 'profile_photo_path')->paginate($paginat);
        return $this->apiResponse(['users' => $users], true, 'Get all User.', AppResponse::HTTP_OK);
    }
    public function single_user($id)
    {
        $user = User::find($id);
        return $this->apiResponse(['user' => $user], true, 'Get Single User.', AppResponse::HTTP_OK);
    }
    public function search_user(Request $request)
    {
        $paginat = 10;
        if ($request->has('per_page')) {
            $paginat = $request->per_page;
        }
        $query = User::select('id', 'name', 'email', 'redrose_id', 'user_type', 'as_user');
        if ($request->filled('search_data')) {
            $searchTerm = $request->search_data;
            $query->where(function ($query) use ($searchTerm) {
                $query
                    ->where('name', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('email', 'LIKE', '%' . $searchTerm . '%')
                    ->orWhere('redrose_id', 'LIKE', '%' . $searchTerm . '%');
            });
        } else {
            return $this->apiResponse('', false, 'Search data is required for search...!', AppResponse::HTTP_NOT_FOUND);
        }
        $users = $query->paginate($paginat);
        if ($users->isEmpty()) {
            return $this->apiResponse('', false, 'No data found, please try with another data...!', AppResponse::HTTP_NOT_FOUND);
        } else {
            return $this->apiResponse(['users' => $users], true, 'Search result...!', AppResponse::HTTP_OK);
        }
    }
    public function my_profile()
    {
        $user = Auth::user();
        $interval = (new DateTime($user->date))->diff(new DateTime());
        $final_days = $interval->format('%a');
        $redrose_id = 'off';
        if ($final_days > 365 || $user->once == 'no') {
            $redrose_id = 'on';
        }
        return $this->apiResponse(
            [
                'redrose_id_edit' => $redrose_id,
                'user' => $user,
            ],
            true,
            'Profile data read succesfull.',
            200,
        );
    }
    // Update Profile start
    public function update_info(Request $request)
    {
        $data = [];
        $user = Auth::user();
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo_path'] = upload_file($request->file('profile_photo'));
            User::where('id', $user->id)->update($data);
        }
        if ($request->has('redrose_id')) {
            $data['redrose_id'] = $request->input('redrose_id');
            $data['date'] = now()->toDateString();
            $data['once'] = 'yes';
            $message = 'Update Information with redrose id';
            User::where('id', $user->id)->update($data);
        } else {
            $message = 'Update Information without redrose id';
        }
        $data = $request->only(['name', 'bio', 'designation', 'birthday', 'gender', 'about', 'phone', 'address', 'upazila_id', 'city_id', 'division_id', 'country_id', 'company_name']);
        set_update_info($user, $data);
        User::where('id', $user->id)->update($data);
        return $this->apiResponse($data, true, $message, AppResponse::HTTP_OK);
    }
    public function change_url(Request $request)
    {
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug(get_random_number(15));
        $message = $request->slug != null ? 'Update your custom url...!' : 'Update url by random...!';
        while (User::where('slug', $slug)->exists()) {
            $slug = $this->increment_slug($slug);
        }
        $user = Auth::user();
        $user->update(['slug' => $slug]);
        return $this->apiResponse('', true, $message, AppResponse::HTTP_OK);
    }

    public function public_profile($id)
    {
        $user = User::find($id);
        $data = [
            'user' => $user,
        ];
        return $this->apiResponse($data, true, 'View public profile data.', AppResponse::HTTP_OK);
    }

    public function switch_as_teacher(Request $request)
    {
        $user = Auth::user();
        $data = $request->only(['title', 'title_description', 'expected_salary', 'period_class', 'duration', 'place_of_learning', 'through_of_learning', 'tuition_type', 'tuition_class', 'tuition_subject', 'tuition_time', 'medium', 'status_for_tuition', 'about_teacher', 'about_teaching', 'degree', 'year', 'institute', 'group_subject', 'result', 'area_country', 'area_division', 'area_city', 'area_upazila', 'area_post_office', 'area_union', 'area_village', 'area_road_house', 'whatsapp', 'facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'tiktok', 'wechat']);
        $remark = 'Under review for 72 hours.';
        $slug = $request->slug != null ? make_slug($request->slug) : make_slug($request->title);
        while (User::where('slug', $slug)->exists()) {
            $slug = $this->increment_slug($slug);
        }
        $data['slug'] = $slug;
        if ($request->is_final) {
            $teacher_status = 2;
            $data['as_user'] = 'teacher';
            $data['status'] = false;
            $data['update_status'] = $teacher_status;
            $data['remark'] = $remark;
            set_update_info($user, $data);
            return $this->apiResponse(['teacher' => $data, 'status' => 0, 'remark' => $remark], true, $remark, AppResponse::HTTP_OK);
        } elseif ($user->is_first) {
            set_update_info($user, $data);
            return $this->apiResponse(['teacher' => $data], true, 'Data Update successfull.', AppResponse::HTTP_OK);
        } else {
            $teacher_status = 2;
            $data['as_user'] = 'teacher';
            $data['status'] = false;
            $data['update_status'] = $teacher_status;
            set_update_info($user, $data);
            return $this->apiResponse(['teacher' => $data, 'status' => 0, 'remark' => $remark], true, 'Under review for 72 hours.', AppResponse::HTTP_OK);
        }
    }
    public function switch_as_student(Request $request)
    {
        $data = [];
        $user = Auth::user();
        $fillableFields = ['institution', 'class_department', 'roll', 'group_subject', 'about_student'];
        foreach ($fillableFields as $field) {
            if ($request->filled($field)) {
                $data[$field] = $request->input($field);
            }
        }
        $rules = [
            'institution' => 'required',
            'class_department' => 'required',
            'roll' => 'required',
            'group_subject' => 'required',
            'about_student' => 'required',
        ];
        if ($this->request_validator($data, $rules)) {
            return $this->apiResponse('', false, 'Make sure you need to fill all the required parameters.', 200);
        } else {
            $data['as_user'] = 'student';
            set_update_info($user, $data);
            return $this->apiResponse(['student' => $data, 'status' => $user->status], true, 'Dear Student, your data update was successful, and your account is under review. Please wait for admin response.', AppResponse::HTTP_OK);
        }
    }
    public function delete_user(Request $request)
    {
        $user = Auth::user();

        if ($request->request_type == 1) {
            //general
            $request->user()->currentAccessToken()->delete();
            $user->delete();
        } else if ($request->request_type == 2) {
            //teacher
            $fillableFields = ['title', 'title_description', 'expected_salary', 'period_class', 'duration', 'place_of_learning', 'through_of_learning', 'tuition_type', 'tuition_class', 'tuition_subject', 'tuition_time', 'medium', 'status_for_tuition', 'about_teacher', 'about_teaching', 'degree', 'year', 'institute', 'group_subject', 'result', 'area_country', 'area_division', 'area_city', 'area_upazila', 'area_post_office', 'area_union', 'area_village', 'area_road_house', 'whatsapp', 'facebook', 'twitter', 'instagram', 'linkedin', 'pinterest', 'tiktok', 'wechat'];
            foreach ($fillableFields as $field) {
                $data[$field] = null;
            }
            $data['as_user'] = 'general';
            $data['remark'] = null;
            $data['update_status'] = 0;
            $data['slug'] = null;
            set_update_info($user, $data);
        }else {
            //student
            $fillableFields = ['institution', 'class_department', 'roll', 'group_subject', 'about_student'];
            foreach ($fillableFields as $field) {
                $data[$field] = null;
            }
            $data['as_user'] = 'general';
            $data['remark'] = null;
            $data['update_status'] = 0;
            set_update_info($user, $data);
        }
        return $this->apiResponse('', true, 'Account delete successful...!', AppResponse::HTTP_OK);
    }
}
