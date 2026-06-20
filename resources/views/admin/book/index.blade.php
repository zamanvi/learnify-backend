@extends('layouts.admin')
@section('title')
    All Book
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Book</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">All Books</h3>
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    {{-- <th>Slug</th> --}}
                                    <th>Page View</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($books as $book)
                                    <tr>
                                        <td>{{ $book->title }}</td>
                                        {{-- <td>{{ $book->slug }}</td> --}}
                                        <td>{{ $book->pageview }}</td>
                                        <td>
                                            <div class="iq-card-header-toolbar d-flex align-items-center">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle text-primary" id="dropdownMenuButton5"
                                                        data-toggle="dropdown">
                                                        <a href="#" class="align-items-center"><i
                                                                class="ri-more-fill"></i></a>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right"
                                                        aria-labelledby="dropdownMenuButton5">
                                                        <a class="dropdown-item" href="{{ route('book.edit', $book->slug) }}"><i
                                                                class="ri-eye-fill mr-2"></i>Edit</a>
                                                        {{-- <a class="dropdown-item" href="{{ route('chapter.index', ['slug' => $book->slug,'id'=> $book->id]) }}"> --}}
                                                        <a class="dropdown-item" href="{{ route('chapter.index', $book->slug) }}">
                                                            <i class="ri-eye-fill mr-2"></i>Chapter</a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <div class="">
                            {{ $books->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Create Book</h3>
                        <form method="POST" action="{{ route('book.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="title">Book Title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    placeholder="Book title">
                            </div>
                            <div class="form-group">
                                <label for="slug">Book Url</label>
                                <input type="text" name="slug" class="form-control" id="slug"
                                    placeholder="Book Url">
                            </div>
                            <input type="submit" class="btn btn-primary" value="Submit" />
                            <input type="reset" class="btn iq-bg-danger" value="Cancel" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
