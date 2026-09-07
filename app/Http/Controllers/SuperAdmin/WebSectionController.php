<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\BookChapter;
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

        // For the "Copy from Book Chapter" convenience tool - read-only lookup,
        // does not touch/modify the app's Book data in any way.
        $bookChapters = BookChapter::with('book')->orderBy('book_id')->orderBy('title')->get();

        return view('admin.websections.chapters', compact('section', 'chapters', 'bookChapters'));
    }

    /**
     * Convenience tool: copy an existing app BookChapter (+ its BookItems)
     * into a new WebChapter (+ WebLessons) under a Section. Read-only against
     * Book/BookChapter/BookItem - never writes to them, so the app's shared
     * content is completely unaffected. Lets admins reuse content (e.g.
     * Grammar chapters) on the website without hand-retyping it, while
     * keeping the app and website data stores fully independent.
     */
    public function copyFromBook(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'book_chapter_id' => 'required|exists:book_chapters,id',
        ]);

        $bookChapter = BookChapter::with('items')->find($request->book_chapter_id);
        if (!$bookChapter) {
            return back()->with('warning', 'Book chapter not found...!');
        }

        $slug = make_slug($bookChapter->title);
        while (WebChapter::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(WebChapter::class, $slug);
        }

        $webChapter = WebChapter::create([
            'section_id' => $request->section_id,
            'title' => $bookChapter->title,
            'slug' => $slug,
            'description' => null,
            'order' => 0,
            'is_active' => true,
        ]);

        $copiedCount = 0;
        foreach ($bookChapter->items as $item) {
            $lessonSlug = make_slug($item->title);
            while (WebLesson::where('slug', $lessonSlug)->exists()) {
                $lessonSlug = set_increment_slug(WebLesson::class, $lessonSlug);
            }
            WebLesson::create([
                'web_chapter_id' => $webChapter->id,
                'title' => $item->title,
                'slug' => $lessonSlug,
                'content' => $item->details,
                'order' => 0,
                'is_active' => true,
            ]);
            $copiedCount++;
        }

        return redirect(route('websections.lessons', $webChapter->slug))
            ->with('success', "Copied \"{$bookChapter->title}\" with {$copiedCount} lesson(s) - review before publishing.");
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
