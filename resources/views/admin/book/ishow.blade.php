@extends('admin.index')
@section('title')
    Edit Book Chapter Lesson
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item" aria-current="page">Books</li>
            <li class="breadcrumb-item" aria-current="page">Chapters</li>
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
                                    <th>Type</th>
                                    <th>Page View</th>
                                    <th width="5%">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($bookItems as $bookItem)
                                <tr>
                                    <td>{{ $bookItem->title }}</td>
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
                        <h3 class="card-title">View Lesson</h3>
                        <p> <span>Lesson Title: </span> {{ $bookItemf->title }} </p>
                        <p> <span>Lesson Type: </span> {{ $bookItemf->type }} </p>
                        <p> <span>Short Details: </span> {{ $bookItemf->short_details }} </p>
                        <p> <span>Audio Text: </span> {{ $bookItemf->audio_text }} </p>
                        <p> <span>Link: </span> {{ $bookItemf->link }} </p>
                        <p> <span>Keyword: </span> {{ $bookItemf->keyword }} </p>
                        <p> <span>Lesson Details: <br></span>
                            @php
                                echo $bookItemf->details
                            @endphp
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
