@extends('admin.index')
@section('title')
    Wizard Chapters
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Wizard</li>
            <li class="breadcrumb-item active" aria-current="page">Chapters</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">All Wizard Chapters</h3>
                        <table class="table table-striped table-bordered mt-1">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Subtitle</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($chapters as $chapter)
                                    <tr>
                                        <td>{{ $chapter->title }}</td>
                                        <td>{{ $chapter->subtitle }}</td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="{{ route('wizard.chapter.edit', $chapter->id) }}">Edit</a>
                                            <a class="btn btn-sm btn-info" href="{{ route('wizard.story.index', $chapter->id) }}">Stories</a>
                                            <a class="btn btn-sm btn-danger" href="{{ route('wizard.chapter.delete', $chapter->id) }}"
                                                onclick="return confirm('Delete this chapter and all its stories?');">Delete</a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div>{{ $chapters->links() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ isset($chapterf) ? 'Edit Chapter' : 'Create Chapter' }}</h3>
                        <form method="POST"
                            action="{{ isset($chapterf) ? route('wizard.chapter.update', $chapterf->id) : route('wizard.chapter.store') }}">
                            @csrf
                            @if (isset($chapterf))
                                @method('PUT')
                            @endif
                            <div class="form-group">
                                <label for="title">Chapter Title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    value="{{ $chapterf->title ?? '' }}" placeholder="যেমন: ইতিহাসের অদ্ভুত পাতা">
                            </div>
                            <div class="form-group">
                                <label for="subtitle">Subtitle</label>
                                <input type="text" name="subtitle" class="form-control" id="subtitle"
                                    value="{{ $chapterf->subtitle ?? '' }}" placeholder="যেমন: বিশ্বাস না হওয়া সত্যি">
                            </div>
                            <div class="form-group">
                                <label for="order_by">Order</label>
                                <input type="number" name="order_by" class="form-control" id="order_by"
                                    value="{{ $chapterf->order_by ?? 0 }}">
                            </div>
                            @if (isset($chapterf))
                                <div class="form-group form-check">
                                    <input type="checkbox" name="status" class="form-check-input" id="status"
                                        @checked($chapterf->status)>
                                    <label class="form-check-label" for="status">Active</label>
                                </div>
                            @endif
                            <input type="submit" class="btn btn-primary" value="Submit">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
