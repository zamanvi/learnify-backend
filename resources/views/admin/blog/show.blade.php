@extends('admin.index')
@section('title')
    blog Panel
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Show blog</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.blog.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="d-flex">
                            <h4 class="card-title">blog <span class="text text-primary">{{ $blog->name }}</span></h4>
                        </div>
                        <div class="d-flex">
                            <h4 class="card-title mr-2"> <a href="{{ route('blog.create') }}">Create</a></h4>
                            <h4 class="card-title"> <a href="{{ route('blog.edit', $blog->id) }}">Edit</a></h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <img height="250" width="250" src="{{ get_file($blog->image_path, 'blog') }}">
                        <h5 class="card-title mt-2">Blog Name: <span class="text text-primary">{{ $blog->name }}</span></h5>
                        <h5 class="card-title mt-2">Blog Keyword: <span class="text text-primary">{{ $blog->keyword }}</span></h5>
                        <h5 class="card-title mt-2">Blog Url: <span class="text text-primary">{{ $blog->slug }}</span></h5>
                        <h5 class="card-title mt-2">Blog Short Description: <br><span class="text text-primary">{{ $blog->short_description }}</span></h5>
                        <h5 class="card-title">Blog Description: <span class="text text-primary">
                            @php
                                echo $blog->description;
                            @endphp</span>
                        </h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
