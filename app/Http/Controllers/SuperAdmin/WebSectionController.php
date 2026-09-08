<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Book;
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
        $chapters = WebChapter::where('section_id', $section->id)->orderBy('order')->orderBy('id')->paginate(10);

        // For the "Copy from Book Chapter" convenience tools - read-only
        // lookups, never touch/modify the app's Book data.
        $bookChapters = BookChapter::with('book')->orderBy('book_id')->orderBy('title')->get();
        $books = Book::orderBy('title')->get();
        $alreadyCopiedBookChapterIds = WebChapter::where('section_id', $section->id)
            ->whereNotNull('source_book_chapter_id')
            ->pluck('source_book_chapter_id')
            ->all();

        return view('admin.websections.chapters', compact(
            'section', 'chapters', 'bookChapters', 'books', 'alreadyCopiedBookChapterIds'
        ));
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

        $webChapter = $this->copyOneBookChapter($bookChapter, $request->section_id);
        $lessonCount = $webChapter->lessons()->count();

        return redirect(route('websections.lessons', $webChapter->slug))
            ->with('success', "Copied \"{$bookChapter->title}\" with {$lessonCount} lesson(s) - review before publishing.");
    }

    /**
     * Same idea as copyFromBook, but for an entire Book at once - copies
     * every chapter (+ items) that hasn't already been copied into this
     * Section before, skipping any that have (matched by
     * source_book_chapter_id, not by title, so a manually-renamed copy
     * still counts as "already copied"). Still strictly read-only against
     * Book/BookChapter/BookItem.
     */
    public function copyAllFromBook(Request $request)
    {
        $request->validate([
            'section_id' => 'required|exists:sections,id',
            'book_id' => 'required|exists:books,id',
        ]);

        $alreadyCopiedIds = WebChapter::where('section_id', $request->section_id)
            ->whereNotNull('source_book_chapter_id')
            ->pluck('source_book_chapter_id')
            ->all();

        $bookChapters = BookChapter::with('items')
            ->where('book_id', $request->book_id)
            ->whereNotIn('id', $alreadyCopiedIds)
            ->get();

        $copiedChapters = 0;
        $copiedLessons = 0;
        foreach ($bookChapters as $bookChapter) {
            $webChapter = $this->copyOneBookChapter($bookChapter, $request->section_id);
            $copiedChapters++;
            $copiedLessons += $webChapter->lessons()->count();
        }

        $section = Section::find($request->section_id);
        $message = $copiedChapters > 0
            ? "Copied {$copiedChapters} chapter(s) with {$copiedLessons} lesson(s) total - review before publishing."
            : 'Nothing to copy - every chapter in this book is already copied into this section.';

        return redirect(route('websections.chapters', $section->slug))->with('success', $message);
    }

    private function copyOneBookChapter(BookChapter $bookChapter, int $sectionId): WebChapter
    {
        $slug = make_slug($bookChapter->title);
        while (WebChapter::where('slug', $slug)->exists()) {
            $slug = set_increment_slug(WebChapter::class, $slug);
        }

        $webChapter = WebChapter::create([
            'section_id' => $sectionId,
            'source_book_chapter_id' => $bookChapter->id,
            'title' => $bookChapter->title,
            'slug' => $slug,
            'description' => null,
            'order' => 0,
            'is_active' => true,
        ]);

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
        }

        return $webChapter;
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
        $lessons = WebLesson::where('web_chapter_id', $chapter->id)->orderBy('order')->orderBy('id')->paginate(10);
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
