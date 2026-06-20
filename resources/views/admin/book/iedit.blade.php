@extends('admin.index')
@section('title')
    Edit Book Post Lesson
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Books</li>
            <li class="breadcrumb-item active" aria-current="page">Lessons</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">All Lesson</h3>
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    {{-- <th>Slug</th> --}}
                                    <th>Type</th>
                                    <th>Page View</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookItems as $bookItem)
                                <tr>
                                    <td>{{ $bookItem->title }}</td>
                                    {{-- <td>{{ $bookItem->slug }}</td> --}}
                                    <td>{{ $bookItem->type }}</td>
                                    <td>{{ $bookItem->pageview }}</td>
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
                                                    <a class="dropdown-item" href="{{ route('item.show', $bookItem->slug) }}"><i
                                                        class="ri-eye-fill mr-2"></i>Show</a>
                                                    <a class="dropdown-item" href="{{ route('item.edit', $bookItem->slug) }}"><i
                                                        class="ri-pencil-fill mr-2"></i>Edit</a>
                                                    <a class="dropdown-item"
                                                        href="{{ route('item.delete', $bookItem->id) }}"><i
                                                            class="ri-delete-bin-6-fill mr-2"></i>Delete</a>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="">
                            {{ $bookItems->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Edit Post</h3>
                        <form method="POST" action="{{ route('item.update', $bookItemf->id) }}" >
                            @csrf
                            @method('PUT')

                            <div class="form-group">
                                <label for="title">Post Title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    value="{{ $bookItemf->title }}">
                            </div>
                            <div class="form-group">
                                <label for="slug">Post Url</label>
                                <input type="text" name="slug" class="form-control" id="slug"
                                    value="{{ $bookItemf->slug }}">
                            </div>
                            <div class="form-group">
                                <label for="type">Select Type</label>
                                <select name="type" id="type" required class="form-control">
                                    <option @selected($bookItemf->type == 'audio') value="audio">Audio</option>
                                    <option @selected($bookItemf->type == 'normal') value="normal">Normal</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <div class="iq-card-body m-0 p-0">
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="short_details-tab" data-toggle="pill"
                                                href="#short_details" role="tab" aria-controls="short_details"
                                                aria-selected="true">Post Short Details</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="keyword-tab" data-toggle="pill"
                                                href="#keyword" role="tab" aria-controls="keyword"
                                                aria-selected="false">Keyword</a>
                                        </li>
                                    </ul>
                                    <div class="tab-content" id="pills-tabContent-2">
                                        <div class="tab-pane fade show active" id="short_details" role="tabpanel"
                                            aria-labelledby="short_details-tab">
                                            <input required type="text" name="short_details" class="form-control"
                                                id="short_details" value="{{ $bookItemf->short_details }}">
                                        </div>
                                        <div class="tab-pane fade" id="keyword" role="tabpanel"
                                            aria-labelledby="keyword-tab">
                                            <input type="text" name="keyword" class="form-control"
                                                id="keyword" value="{{ $bookItemf->keyword }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="audio_text">Audio Text</label>
                                <textarea type="text" name="audio_text" class="form-control" cols="10" id="audio_text">{{ $bookItemf->audio_text }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="details">Post Details</label>
                                <textarea class="form-control" name="details" id="editor1" rows="5">{{ $bookItemf->details }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="link">Post Link</label>
                                <input type="text" name="link" class="form-control" id="link"
                                    value="{{ $bookItemf->link }}">
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
