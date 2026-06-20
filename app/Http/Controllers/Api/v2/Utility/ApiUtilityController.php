<?php

namespace App\Http\Controllers\Api\v2\Utility;

use App\Http\Controllers\Controller;
use App\Models\AllClass;
use App\Models\Blog;
use App\Models\City;
use App\Models\Country;
use App\Models\Division;
use App\Models\Slider;
use App\Models\Upazila;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Http\Request;

class ApiUtilityController extends Controller
{
    use HttpAppResponse;
    // All Blogs start
    public function slider(Request $request)
    {
        $type = 'web';
        if ($request->has('type')) {
            $type = $request->type;
        }
        $sliders = Slider::where('type', $type)->get(['type', 'short_description', 'image_path']);

        return $this->apiResponse(['sliders' => $sliders], true, 'All sliders for web.', AppResponse::HTTP_OK);
    }
    public function all_blog(Request $request)
    {
        $perPage = 10;
        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        }
        $blogs = Blog::latest()->select(['id', 'slug', 'name', 'image_path', 'short_description', 'pageview'])->paginate($perPage);

        return $this->apiResponse(['blogs' => $blogs], true, 'All Blog read successful.', AppResponse::HTTP_OK);
    }
    public function blog_item($id)
    {
        $blog = Blog::select('id', 'user_id', 'name', 'image_path', 'short_description', 'description', 'keyword', 'pageview')
            ->where('id', $id)
            ->first();
        if (!$blog) {
            return $this->apiResponse([], false, 'Blog not found.', AppResponse::HTTP_NOT_FOUND);
        }
        $blog->pageview = $blog->pageview + 1;
        $blog->save();
        return $this->apiResponse(['blog' => $blog], true, 'Blog read successful.', AppResponse::HTTP_OK);
    }
    public function blog_item_slug($slug)
    {
        $blog = Blog::select('id', 'slug', 'user_id', 'name', 'image_path', 'short_description', 'description', 'keyword', 'pageview')
            ->where('slug', $slug)
            ->first();
        if (!$blog) {
            return $this->apiResponse([], false, 'Blog not found.', AppResponse::HTTP_NOT_FOUND);
        }
        $blog->pageview = $blog->pageview + 1;
        $blog->save();
        return $this->apiResponse(['blog' => $blog], true, 'Blog read successful.', AppResponse::HTTP_OK);
    }
    // All Class start
    public function all_class()
    {
        $allclass = AllClass::get(['id', 'name']);
        return $this->apiResponse(['allclass' => $allclass],true,'All Class read succesfull.',AppResponse::HTTP_OK);
    }
    public function single_class($id)
    {
        $aclass = AllClass::find($id);
        return $this->apiResponse(['class' => $aclass],true,'All Class read succesfull.',AppResponse::HTTP_OK);
    }
    // All Country start
    public function all_country()
    {
        $countrys = Country::where('is_active', 'on')->get(['id', 'code', 'name']);
        return $this->apiResponse(['countries' => $countrys],true,'All Country read succesfull.',AppResponse::HTTP_OK);
    }
    public function single_country(Request $request)
    {
        $id = $request->id;
        $country = Country::find($id);
        return $this->apiResponse(['country' => $country],true,'Singla Country read succesfull.',AppResponse::HTTP_OK);
    }
    public function division($id)
    {
        $divisions = Division::where('country_id', $id)->get(['id', 'name']);
        return $this->apiResponse(['divisions' => $divisions],true,'All Division read succesfull.',AppResponse::HTTP_OK);
    }
    public function single_division(Request $request)
    {
        $id = $request->id;
        $division = Division::find($id);
        return $this->apiResponse(['division' => $division],true,'Singla Division read succesfull.',AppResponse::HTTP_OK);
    }
    public function city($id)
    {
        $cities = City::where('division_id', $id)->get(['id', 'name']);
        return $this->apiResponse(['cities' => $cities],true,'All Cities read succesfull.',AppResponse::HTTP_OK);
    }
    public function single_city(Request $request)
    {
        $id = $request->id;
        $city = City::find($id);
        return $this->apiResponse(['city' => $city],true,'Singla City read succesfull.',AppResponse::HTTP_OK);
    }
    // All Upazila start
    public function upazila($id)
    {
        $upazilas = Upazila::where('city_id', $id)->get(['id', 'name']);
        return $this->apiResponse(['upazilas' => $upazilas],true,'All Upazilas read succesfull.',AppResponse::HTTP_OK);
    }
    public function single_upazila(Request $request)
    {
        $id = $request->id;
        $upazila = Upazila::find($id);
        return $this->apiResponse(['upazila' => $upazila],true,'Singla Upazila read succesfull.',AppResponse::HTTP_OK);
    }
}
