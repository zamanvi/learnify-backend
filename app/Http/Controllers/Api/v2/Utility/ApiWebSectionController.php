<?php

namespace App\Http\Controllers\Api\v2\Utility;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\WebChapter;
use App\Models\WebLesson;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Http\Request;

/**
 * Public, read-only API for the website ("Book 2" / masterenglishbook.com)
 * Section (Bangladesh/International) -> WebChapter -> WebLesson structure.
 *
 * Mirrors ApiBookController's shape/conventions (same apiResponse trait,
 * same pagination style) so the frontend can consume it the same way it
 * already consumes /api/v2/app/book/... - but reads exclusively from
 * Section/WebChapter/WebLesson, never touching Book/BookChapter/BookItem.
 * Only is_active rows are ever returned - draft/inactive content (e.g. a
 * freshly-copied chapter awaiting review) stays invisible until an admin
 * flips it active.
 */
class ApiWebSectionController extends Controller
{
    use HttpAppResponse;

    public function section_index(Request $request)
    {
        $sections = Section::where('is_active', true)
            ->orderBy('order')
            ->select('id', 'name', 'slug', 'description', 'order')
            ->get();
        return $this->apiResponse(['sections' => $sections], true, 'Read all sections.', AppResponse::HTTP_OK);
    }

    public function chapter_index(Request $request)
    {
        $perPage = 150;
        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        }
        $chapters = WebChapter::where('is_active', true)
            ->with('section:id,name,slug')
            ->orderBy('order')->orderBy('id')
            ->select('id', 'section_id', 'title', 'slug', 'description', 'order');

        if ($request->has('section_slug')) {
            $section = Section::where('slug', $request->section_slug)->first();
            if (!$section) {
                return $this->apiResponse(null, false, 'Section not found', AppResponse::HTTP_NOT_FOUND);
            }
            $chapters->where('section_id', $section->id);
            $message = 'Read all chapters by section';
        } else {
            $message = 'Read all chapters';
        }

        $chapters = $chapters->paginate($perPage);
        $chapters->transform(function ($chapter) {
            $chapter->section_name = $chapter->section ? $chapter->section->name : null;
            $chapter->section_slug = $chapter->section ? $chapter->section->slug : null;
            unset($chapter->section);
            return $chapter;
        });

        return $this->apiResponse(['chapters' => $chapters], true, $message, AppResponse::HTTP_OK);
    }

    public function chapter_show(Request $request, $slug)
    {
        $chapter = WebChapter::with('section:id,name,slug')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$chapter) {
            return $this->apiResponse(null, false, 'Chapter not found', AppResponse::HTTP_NOT_FOUND);
        }

        $chapter->section_name = $chapter->section ? $chapter->section->name : null;
        $chapter->section_slug = $chapter->section ? $chapter->section->slug : null;
        unset($chapter->section);
        $chapter->makeHidden(['created_at', 'updated_at']);

        return $this->apiResponse(['chapter' => $chapter], true, 'Read single chapter', AppResponse::HTTP_OK);
    }

    public function lesson_index(Request $request)
    {
        $perPage = 150;
        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        }
        $lessonsQuery = WebLesson::where('is_active', true)
            ->with(['chapter:id,title,slug,section_id', 'chapter.section:id,name,slug'])
            ->orderBy('order')->orderBy('id')
            ->select('id', 'web_chapter_id', 'title', 'slug', 'order');

        $message = 'Read all lessons';
        if ($request->has('chapter_slug')) {
            $chapter = WebChapter::where('slug', $request->chapter_slug)->first();
            if (!$chapter) {
                return $this->apiResponse(null, false, 'Chapter not found', AppResponse::HTTP_NOT_FOUND);
            }
            $lessonsQuery->where('web_chapter_id', $chapter->id);
            $message = 'Read all lessons by chapter';
        }

        $lessons = $lessonsQuery->paginate($perPage);
        $lessons->transform(function ($lesson) {
            $lesson->chapter_title = $lesson->chapter ? $lesson->chapter->title : null;
            $lesson->section_name = $lesson->chapter && $lesson->chapter->section ? $lesson->chapter->section->name : null;
            $lesson->section_slug = $lesson->chapter && $lesson->chapter->section ? $lesson->chapter->section->slug : null;
            unset($lesson->chapter);
            return $lesson;
        });

        return $this->apiResponse(['lessons' => $lessons], true, $message, AppResponse::HTTP_OK);
    }

    public function lesson_show(Request $request, $slug)
    {
        $lesson = WebLesson::with(['chapter:id,title,slug,section_id', 'chapter.section:id,name,slug'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$lesson) {
            return $this->apiResponse(null, false, 'Lesson not found', AppResponse::HTTP_NOT_FOUND);
        }

        $lesson->chapter_title = $lesson->chapter ? $lesson->chapter->title : null;
        $lesson->section_name = $lesson->chapter && $lesson->chapter->section ? $lesson->chapter->section->name : null;
        $lesson->section_slug = $lesson->chapter && $lesson->chapter->section ? $lesson->chapter->section->slug : null;
        unset($lesson->chapter);
        $lesson->makeHidden(['created_at', 'updated_at']);

        return $this->apiResponse(['lesson' => $lesson], true, 'Read single lesson', AppResponse::HTTP_OK);
    }
}
