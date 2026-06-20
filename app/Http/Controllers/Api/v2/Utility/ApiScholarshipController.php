<?php
namespace App\Http\Controllers\Api\v2\Utility;
use App\Http\Controllers\Controller;
use App\Models\ScholarShip;
use App\Models\ScholarShipEnroll;
use App\Models\ScholarShipResult;
use App\Models\User;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Http\Request;
class ApiScholarshipController extends Controller
{
    use HttpAppResponse;
    function scholarship(Request $request) {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('status', true);
        $is_publish = !$status;
        $message = $status ? 'All active scholarships read successfully!' : 'All inactive scholarships read successfully!';
        $scholarShips = ScholarShip::validForEnrollment($status, $is_publish)
            ->paginate($perPage, [
                'title', 'price', 'enroll_limit', 'winner_limit', 'date', 'time',
                'slug', 'short_description', 'description', 'image_path', 'sponsor',
                'sponsor_image_path', 'pageview', 'status'
            ]);
        return $this->apiResponse(['scholarShips' => $scholarShips], true, $message, AppResponse::HTTP_OK);
    }
    function scholarship_active(Request $request) {
        $perPage = $request->input('per_page', 10);
        $status = $request->input('per_page', true);
        $is_publish = !$status;
        $scholarShips = ScholarShip::validForEnrollment($status, $is_publish)
            ->paginate($perPage, [
                'title', 'price', 'enroll_limit', 'winner_limit', 'date', 'time',
                'slug', 'short_description', 'description', 'image_path', 'sponsor',
                'sponsor_image_path', 'pageview', 'status'
            ]);
        return $this->apiResponse(['scholarShips' => $scholarShips], true, 'All active scholarships read successful.!', AppResponse::HTTP_OK);
    }
    function scholarship_inactive(Request $request) {
        $perPage = $request->input('per_page', 10);
        $status = false;
        $is_publish = true;
        $scholarShips = ScholarShip::validForEnrollment($status, $is_publish)
            ->paginate($perPage, [
                'title', 'price', 'enroll_limit', 'winner_limit', 'date', 'time',
                'slug', 'short_description', 'description', 'image_path', 'sponsor',
                'sponsor_image_path', 'pageview', 'status'
            ]);
        return $this->apiResponse(['scholarShips' => $scholarShips], true, 'All active scholarships read successful.!', AppResponse::HTTP_OK);
    }
    function scholarship_show($slug) {
        $scholarShip = ScholarShip::where('slug', $slug)->first();
        if ($scholarShip) {
            return $this->apiResponse(['scholarShip' => $scholarShip], true, 'All active scholarships read successful.!', AppResponse::HTTP_OK);
        } else {
            return $this->apiResponse('', true, 'Could not find scholarships.!', AppResponse::HTTP_NOT_FOUND);
        }
    }
    function scholarship_enroll($slug) {
        $scholarShip = ScholarShip::where('slug', $slug)->first();
        if ($scholarShip) {

            if (!$scholarShip->isValidForEnrollmentCheck()) {
                return $this->apiResponse('', true, 'This scholarship is not available for enrollment at the moment.', AppResponse::HTTP_CONFLICT);
            }

            $enroll_limit = $scholarShip->enroll_limit;
            $user = auth()->user();
            if (ScholarShipEnroll::isUserEnrolled($user->id, $scholarShip->id)) {
                return $this->apiResponse('', true, 'You already enrolled in this scholarship.', AppResponse::HTTP_CONFLICT);
            }
            if (ScholarShipEnroll::enrollmentCount($scholarShip->id) >= $enroll_limit) {
                return $this->apiResponse('', true, 'Enrollment limit reached for this scholarship.', AppResponse::HTTP_CONFLICT);
            }
            ScholarShipEnroll::create([
                'user_id' => $user->id,
                'scholar_ship_id' => $scholarShip->id,
            ]);
            return $this->apiResponse('', true, 'Scholarship Enrollment successful!', AppResponse::HTTP_OK);
        } else {
            return $this->apiResponse('', true, 'Could not find scholarships.!', AppResponse::HTTP_NOT_FOUND);
        }
    }
    function scholarship_get_result($slug) {
        $scholarShip = ScholarShip::where('slug', $slug)->first();
        if (!$scholarShip) {
            return $this->apiResponse('', true, 'Could not find scholarships.!', AppResponse::HTTP_NOT_FOUND);
        }
        if (!$scholarShip->isValidForResult()) {
            return $this->apiResponse('', true, 'This scholarship is not not published yeat.', AppResponse::HTTP_CONFLICT);
        }

        $scholarShip->makeHidden(['user_id', 'is_publish', 'status', 's_country', 's_division', 's_city', 's_upazila', 'created_at', 'updated_at']);

        $scholarShipResults = ScholarShipResult::where('scholar_ship_id', $scholarShip->id)
            ->get(['user_id', 'order_by', 'is_winner']);

        $scholarShipResults = $scholarShipResults->sortBy('order_by');

        $userIds = $scholarShipResults->pluck('user_id');

        $users = User::whereIn('id', $userIds)
            ->get(['id', 'name', 'email', 'redrose_id', 'profile_photo_path']);

        $users = $users->map(function ($user) use ($scholarShipResults) {
            $result = $scholarShipResults->firstWhere('user_id', $user->id);
            $user->order_by = $result->order_by;
            $user->is_winner = $result->is_winner;
            return $user;
        });
        $users = $users->sortBy('order_by')->values();
        return $this->apiResponse(['scholarShip' => $scholarShip, 'users' => $users], true, 'Scholarship Result...!', AppResponse::HTTP_OK);
    }
}
