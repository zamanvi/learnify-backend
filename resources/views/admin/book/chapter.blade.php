@extends('admin.index')
@section('title')
    All Book Chapter
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
                                        <td>
                                            <select class="form-control form-control-sm chapter-type-select" data-id="{{ $bookChapter->id }}" style="min-width:140px;">
                                                <option value="web"              @selected($bookChapter->type == 'web')>Web</option>
                                                <option value="grammar"          @selected($bookChapter->type == 'grammar')>Grammar</option>
                                                <option value="daily_vocabulary" @selected($bookChapter->type == 'daily_vocabulary')>Daily Vocabulary</option>
                                                <option value="writing_reading"  @selected($bookChapter->type == 'writing_reading')>Writing</option>
                                            </select>
                                        </td>
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
                            {{ $bookChapters->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h3 class="card-title">Create Chapter</h3>
                        <form method="POST" action="{{ route('chapter.store') }}">
                            @csrf
                            <div class="form-group">
                                <label for="book_id">Book Title</label>
                                <input readonly class="form-control" id="book_id" value="{{ $book->title }}" />
                                <input hidden name="book_id" value="{{ $book->id }}" />
                            </div>
                            <div class="form-group">
                                <label for="title">Chapter Title</label>
                                <input required type="text" name="title" class="form-control" id="title"
                                    placeholder="Chapter title">
                            </div>
                            <div class="form-group">
                                <label for="type">Chapter Type</label>
                                <select name="type" id="type" class="form-control">
                                    <option value="web" @selected(request('type') == 'web')>Web</option>
                                    <option value="grammar" @selected(request('type') == 'grammar')>Grammar</option>
                                    <option value="daily_vocabulary" @selected(request('type') == 'daily_vocabulary')>Daily Vocabulary</option>
                                    <option value="writing_reading" @selected(request('type') == 'writing_reading')>Writing & Reading</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="slug">Chapter Url</label>
                                <input type="text" name="slug" class="form-control" id="slug"
                                    placeholder="Chapter url">
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
@push('scripts')
<script>
document.querySelectorAll('.chapter-type-select').forEach(function(select) {
    select.addEventListener('change', function() {
        var id   = this.dataset.id;
        var type = this.value;
        var sel  = this;
        sel.disabled = true;
        fetch('{{ url("chapter/update-type") }}/' + id, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ type: type })
        })
        .then(r => r.json())
        .then(data => {
            sel.disabled = false;
            sel.style.background = data.success ? '#d4edda' : '#f8d7da';
            setTimeout(() => sel.style.background = '', 1500);
        })
        .catch(() => { sel.disabled = false; sel.style.background = '#f8d7da'; });
    });
});
</script>
@endpush
