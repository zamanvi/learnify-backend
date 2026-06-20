<?php

namespace App\Http\Controllers\Api\v2\Utility;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BookChapter;
use App\Models\BookItem;
use App\Traits\AppResponse;
use App\Traits\HttpAppResponse;
use Illuminate\Http\Request;

class ApiBookController extends Controller
{
    use HttpAppResponse;
    public function book_index(Request $request)
    {
        $pageinate = 150;
        if ($request->has('per_page')) {
            $pageinate = $request->per_page;
        }
        $books = Book::select('id', 'title', 'slug', 'status', 'pageview')->paginate($pageinate);
        return $this->apiResponse(['books' => $books], true, 'Read all books.', AppResponse::HTTP_OK);
    }
    public function book_show(Request $request, $slug)
    {
        $book = Book::where('slug', $slug)->first();
        $book->increment('pageview');
        $book->makeHidden(['created_at', 'updated_at']);
        return $this->apiResponse(['book' => $book], true, 'Read Single book.', AppResponse::HTTP_OK);
    }
    public function book_chapter_index(Request $request) {
        $perPage = 150;
        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        }
        $bookChapters = BookChapter::with('book:id,title')
            ->select('id', 'book_id', 'title', 'slug', 'status', 'pageview');
        if ($request->has('book_slug')) {
            $book = Book::where('slug', $request->book_slug)->first();
            $bookChapters->where('book_id', $book->id);
            $message = 'Read all Chapters by book';
        } else {
            $message = 'Read all Chapters';
        }
        $bookChapters = $bookChapters->paginate($perPage);
        $bookChapters->transform(function ($chapter) {
            $chapter->book_title = $chapter->book ? $chapter->book->title : null;
            unset($chapter->book);
            return $chapter;
        });
        return $this->apiResponse(['chapters' => $bookChapters], true, $message, AppResponse::HTTP_OK);
    }
    public function book_chapter_show(Request $request, $slug)
    {
        $bookChapter = BookChapter::with('book:id,title')->where('slug', $slug)->first();
        $bookChapter->increment('pageview');
        if (!$bookChapter) {
            return $this->apiResponse(null, false, 'Chapter not found', AppResponse::HTTP_NOT_FOUND);
        }

        $bookChapter->book_title = $bookChapter->book ? $bookChapter->book->title : null;
        unset($bookChapter->book);
        $bookChapter->makeHidden(['created_at', 'updated_at']);
        return $this->apiResponse(['chapter' => $bookChapter], true, 'Read Single Chapter', AppResponse::HTTP_OK);
    }
    public function book_chapter_index2(Request $request) {
        $perPage = 150;
        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        }
        $bookChapters = BookChapter::where('status', true)->with('book:id,title')->select('id', 'book_id', 'title', 'type', 'slug', 'status', 'pageview');

        if ($request->has('type')) {
            $bookChapters = BookChapter::where('status', true)->where('type', $request->type)->with('book:id,title')
            ->select('id', 'book_id', 'title', 'type', 'slug', 'status', 'pageview');
        }

        if ($request->has('book_slug')) {
            $book = Book::where('slug', $request->book_slug)->first();
            $bookChapters->where('book_id', $book->id);
            $message = 'Read all Chapters by book';
        } else {
            $message = 'Read all Chapters';
        }
        $bookChapters = $bookChapters->paginate($perPage);
        $bookChapters->transform(function ($chapter) {
            $chapter->book_title = $chapter->book ? $chapter->book->title : null;
            unset($chapter->book);
            return $chapter;
        });
        return $this->apiResponse(['chapters' => $bookChapters], true, $message, AppResponse::HTTP_OK);
    }
    public function book_item_index(Request $request)
    {
        $perPage = 150;
        if ($request->has('per_page')) {
            $perPage = $request->per_page;
        }
        $itemsQuery = BookItem::with(['chapter:id,title,book_id', 'chapter.book:id,title'])
            ->select('id', 'slug', 'chapter_id', 'title', 'pageview');
        $message = 'Read all Items';
        if ($request->has('chapter_slug')) {
            $bookChapter = BookChapter::where('slug', $request->chapter_slug)->first();
            $itemsQuery->where('chapter_id', $bookChapter->id);
            $message = 'Read all Items by book';
        }
        $bookItem = $itemsQuery->paginate($perPage);
        $bookItem->transform(function ($item) {
            $item->book_title = $item->chapter && $item->chapter ? $item->chapter->book->title : null;
            $item->chapter_title = $item->chapter && $item->chapter ? $item->chapter->title : null;
            unset($item->chapter);
            return $item;
        });
        return $this->apiResponse(['items' => $bookItem], true, $message, AppResponse::HTTP_OK);
    }
    public function book_item_show(Request $request, $slug)
    {
        $bookItem = BookItem::with('chapter.book')->where('slug', $slug)->first();
        if (!$bookItem) {
            return $this->apiResponse(null, false, 'Book item not found', AppResponse::HTTP_NOT_FOUND);
        }
        $bookItem->increment('pageview');
        $bookItem->book_title = $bookItem->chapter && $bookItem->chapter->book ? $bookItem->chapter->book->title : null;
        $bookItem->chapter_title = $bookItem->chapter ? $bookItem->chapter->title : null;
        unset($bookItem->chapter);
        $bookItem->makeHidden(['created_at', 'updated_at']);
        return $this->apiResponse(['item' => $bookItem], true, 'Read Single Item', AppResponse::HTTP_OK);
    }
}
