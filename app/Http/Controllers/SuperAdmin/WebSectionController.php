<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Section;
use App\Models\WebChapter;
use App\Models\WebLesson;
use Illuminate\Http\Request;

/**
 * Admin management for the website ("Book 2" / masterenglishbook.com)
 * Section (Bangladesh/International) -> WebChapter -> WebLesson structure.
 *
 * Deliberately separate from BookController (App\Models\Book/BookChapter/
 * BookItem, the existing app's own content) and ChapterController/
 * LessonController (App\Models\Chapter/Lesson, the app's Vocabulary
 * system) - see WebChapter/WebLesson model docblocks.
 */
class WebSectionController extends Controller
{
    public function index()
    {
        $sections = Section::orderBy('order')->get();
        return view('admin.websections.index', compact('sections'));
    }

    public function chapters($sectionSlug)
    {
        $section = Section::where('slug', $sectionSlug)->firstOrFail();
        $chapters = WebChapter::where('section_id', $section->id)->orderBy('order')->paginate(10);
        return view('admin.websections.chapters', compact('section', 'chapters'));
    }

    public function chapterStore(Request $request)
    {
        $chapter = WebChapter::createStore($request);
        if ($chapter) {
            return back()->with('success', $request['title'] . ' - chapter added...!');
        }
        return back()->with('warning', 'Error - check all data and submit again...!');
    }

    public function chapterEdit($slug)
    {
        $chapter = WebChapter::where('slug', $slug)->firstOrFail();
        $section = Section::find($chapter->section_id);
        return view('admin.websections.chapter_edit', compact('chapter', 'section'));
    }

    public function chapterUpdate(Request $request, $id)
    {
        $updated = WebChapter::updateStore($request, $id);
        if ($updated) {
            $chapter = WebChapter::find($id);
            return redirect(route('websections.chapter.edit', $chapter->slug))
                ->with('success', $request['title'] . ' - chapter updated...!');
        }
        return back()->with('warning', 'Error - check all data and submit again...!');
    }

    public function chapterDelete($id)
    {
        $chapter = WebChapter::find($id);
        if (!$chapter) {
            return back()->with('warning', 'Chapter not found...!');
        }
        $sectionSlug = optional(Section::find($chapter->section_id))->slug;
        WebLesson::where('web_chapter_id', $id)->delete();
        $chapter->delete();
        return redirect(route('websections.chapters', $sectionSlug))->with('success', 'Chapter deleted...!');
    }

    public function lessons($chapterSlug)
    {
        $chapter = WebChapter::where('slug', $chapterSlug)->firstOrFail();
        $lessons = WebLesson::where('web_chapter_id', $chapter->id)->orderBy('order')->paginate(10);
        return view('admin.websections.lessons', compact('chapter', 'lessons'));
    }

    public function lessonStore(Request $request)
    {
        $lesson = WebLesson::createStore($request);
        if ($lesson) {
            return back()->with('success', $request['title'] . ' - lesson added...!');
        }
        return back()->with('warning', 'Error - check all data and submit again...!');
    }

    public function lessonEdit($slug)
    {
        $lesson = WebLesson::where('slug', $slug)->firstOrFail();
        $chapter = WebChapter::find($lesson->web_chapter_id);
        return view('admin.websections.lesson_edit', compact('lesson', 'chapter'));
    }

    public function lessonUpdate(Request $request, $id)
    {
        $updated = WebLesson::updateStore($request, $id);
        if ($updated) {
            $lesson = WebLesson::find($id);
            return redirect(route('websections.lesson.edit', $lesson->slug))
                ->with('success', $request['title'] . ' - lesson updated...!');
        }
        return back()->with('warning', 'Error - check all data and submit again...!');
    }

    public function lessonDelete($id)
    {
        $lesson = WebLesson::find($id);
        if (!$lesson) {
            return back()->with('warning', 'Lesson not found...!');
        }
        $chapterSlug = optional(WebChapter::find($lesson->web_chapter_id))->slug;
        $lesson->delete();
        return redirect(route('websections.lessons', $chapterSlug))->with('success', 'Lesson deleted...!');
    }
}
