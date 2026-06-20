@extends('admin.index')
@section('title')
    Edit Book Chapter
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Book</li>
            <li class="breadcrumb-item active" aria-current="page">Chapter</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">All Chapters</h3>
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Page View</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookChapters as $bookChapter)
                                    <tr>
                                        <td>{{ $bookChapter->title }}</td>
                                        <td>{{ $bookChapter->type }}</td>
                                        <td>{{ $bookChapter->pageview }}</td>
                                        <td>
                                            <div class="iq-card-header-toolbar d-flex align-items-center">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle text-primary" id="dropdownMenuButton5"
                                                        data-toggle="dropdown">
                                                        <a href="" class="align-items-center"><i
                                                                class="ri-more-fill"></i></a>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="dropdownMenuButton5">
                                                        <a class="dropdown-item"
                                                            href="{{ route('chapter.edit', $bookChapter->slug) }}"><i
                                                                class="ri-eye-fill mr-2"></i>Edit</a>
                                                        {{-- <a class="dropdown-item"
                                                            href="{{ route('chapter.delete', $bookChapter->id) }}">
                                                            <i class="ri-eye-fill mr-2"></i>Delete</a> --}}
                                                        <a class="dropdown-item"
                                                            href="{{ route('item.index', $bookChapter->slug) }}">
                                                            <i class="ri-eye-fill mr-2"></i>Post</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="">
                            {{ $bookChapters->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Edit Chapter</h3>
                        <form method="POST" action="{{ route('chapter.update', $bookChapterf->id) }}">
                            @csrf
                            @method('PUT')
                            <div class="form-group">
                                <label for="book_id">Book Title</label>
                                <input readonly class="form-control" id="book_id" value="{{ $book->title }}" />
                                <input hidden class="form-control" name="book_id" value="{{ $book->id }}" />
                            </div>
                            <div class="form-group">
                                <label for="title">Chapter Title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    value="{{ $bookChapterf->title }}">
                            </div>
                            <div class="form-group">
                                <label for="type">Chapter Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option @selected($bookChapterf->type == 'web') value="web">Web</option>
                                    <option @selected($bookChapterf->type == 'grammar') value="grammar">Grammar</option>
                                    <option @selected($bookChapterf->type == 'daily_vocabulary') value="daily_vocabulary">Daily Vocabulary</option>
                                    <option @selected($bookChapterf->type == 'writing_reading') value="writing_reading">Writing & Reading</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="slug">Chapter Url</label>
                                <input required type="text" name="slug" class="form-control" id="slug"
                                    value="{{ $bookChapterf->slug }}">
                            </div>
                            <input type="submit" class="btn btn-primary" value="Update" />
                            <input type="reset" class="btn iq-bg-danger" value="Cancel" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
