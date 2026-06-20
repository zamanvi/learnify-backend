<div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">All chapters</h3>
            <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                aria-describedby="user-list-page-info">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Type</th>
                        <th>Image</th>
                        <th width="5%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($chapters as $chapter)
                        <tr>
                            <td>{{ $chapter->title }}</td>
                            <td>{{ ucfirst($chapter->type) }}</td>
                            <td>{{ $chapter->image_path }}</td>
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
                                            <a class="dropdown-item" href="{{ route('chapters.lessons.create', $chapter->id) }}"><i
                                                class="ri-eye-fill mr-2"></i>Create Lesson</a>
                                            <a class="dropdown-item" href="{{ route('chapters.edit', $chapter->id) }}"><i
                                                    class="ri-eye-fill mr-2"></i>Edit</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="">
                {{ $chapters->links() }}
            </div>
        </div>
    </div>
</div>
