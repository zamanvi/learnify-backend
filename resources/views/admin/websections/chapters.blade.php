@extends('layouts.admin')
@section('title')
    {{ $section->name }} - Chapters
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('websections.index') }}">Website Sections</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $section->name }}</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">{{ $section->name }} — Chapters</h3>
                        <table class="table table-striped table-bordered mt-1">
                            <thead>
                                <tr>
                                    <th>Order</th>
                                    <th>Title</th>
                                    <th>Active</th>
                                    <th width="15%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($chapters as $chapter)
                                    <tr>
                                        <td>{{ $chapter->order }}</td>
                                        <td>{{ $chapter->title }}</td>
                                        <td>
                                            @if ($chapter->is_active)
                                                <span class="badge badge-success">Active</span>
                                            @else
                                                <span class="badge badge-secondary">Inactive</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('websections.chapter.edit', $chapter->slug) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                                            <a href="{{ route('websections.lessons', $chapter->slug) }}" class="btn btn-sm btn-outline-secondary">Lessons</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No chapters yet.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        {{ $chapters->links() }}
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Add Chapter</h3>
                        <form method="POST" action="{{ route('websections.chapter.store') }}">
                            @csrf
                            <input type="hidden" name="section_id" value="{{ $section->id }}">
                            <div class="form-group">
                                <label>Section</label>
                                <input readonly class="form-control" value="{{ $section->name }}">
                            </div>
                            <div class="form-group">
                                <label for="title">Chapter Title</label>
                                <input required type="text" name="title" class="form-control" id="title" placeholder="Chapter title">
                            </div>
                            <div class="form-group">
                                <label for="slug">Chapter Url (optional)</label>
                                <input type="text" name="slug" class="form-control" id="slug" placeholder="auto-generated if left blank">
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <textarea name="description" class="form-control" id="description" rows="3"></textarea>
                            </div>
                            <div class="form-group">
                                <label for="order">Order</label>
                                <input type="number" name="order" class="form-control" id="order" value="0">
                            </div>
                            <div class="form-check mb-3">
                                <input type="checkbox" class="form-check-input" name="is_active" id="is_active" checked>
                                <label class="form-check-label" for="is_active">Active</label>
                            </div>
                            <input type="submit" class="btn btn-primary" value="Submit">
                            <input type="reset" class="btn iq-bg-danger" value="Cancel">
                        </form>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Copy from Book Chapter</h3>
                        <p class="text-muted">Reuse an existing app chapter's content (e.g. Grammar) here instead of
                            retyping it. This only <strong>reads</strong> the Book chapter - nothing there is changed,
                            and the app is completely unaffected. Review the copy afterwards before publishing.</p>
                        <form method="POST" action="{{ route('websections.chapter.copy-from-book') }}">
                            @csrf
                            <input type="hidden" name="section_id" value="{{ $section->id }}">
                            <div class="form-group">
                                <label for="book_chapter_id">Book Chapter</label>
                                <select required name="book_chapter_id" id="book_chapter_id" class="form-control">
                                    <option value="">— Select a chapter to copy —</option>
                                    @foreach ($bookChapters as $bc)
                                        <option value="{{ $bc->id }}">
                                            {{ optional($bc->book)->title }} — {{ $bc->title }} ({{ $bc->type }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <input type="submit" class="btn btn-outline-primary" value="Copy into this Section">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
