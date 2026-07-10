<?php

namespace App\Http\Controllers\Api\v2\Utility;

use App\Http\Controllers\Controller;
use App\Models\WizardChapter;
use App\Models\WizardStory;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Http\Request;

class ApiWizardController extends Controller
{
    use HttpAppResponse;

    public function chapters(Request $request)
    {
        $perPage = $request->has('per_page') ? $request->per_page : 50;
        $chapters = WizardChapter::where('status', true)
            ->orderBy('order_by')
            ->select('id', 'title', 'subtitle')
            ->paginate($perPage);
        return $this->apiResponse(['chapters' => $chapters], true, 'Read all Wizard chapters', AppResponse::HTTP_OK);
    }

    public function stories(Request $request)
    {
        $perPage = $request->has('per_page') ? $request->per_page : 50;
        $storiesQuery = WizardStory::where('status', true)->orderBy('order_by')
            ->select('id', 'chapter_id', 'hook_title', 'meta');

        if ($request->has('chapter_id')) {
            $storiesQuery->where('chapter_id', $request->chapter_id);
        }

        $stories = $storiesQuery->paginate($perPage);
        return $this->apiResponse(['stories' => $stories], true, 'Read all Wizard stories', AppResponse::HTTP_OK);
    }

    public function story_show(Request $request, $id)
    {
        $story = WizardStory::where('status', true)->find($id);
        if (!$story) {
            return $this->apiResponse(null, false, 'Story not found', AppResponse::HTTP_NOT_FOUND);
        }
        $story->makeHidden(['created_at', 'updated_at', 'status', 'order_by']);
        return $this->apiResponse(['story' => $story], true, 'Read Single Wizard story', AppResponse::HTTP_OK);
    }
}
