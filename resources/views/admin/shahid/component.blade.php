<div class="col-sm-12 col-lg-6">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between">
            <div class="iq-header-title">
                <h4 class="card-title">shahid {{ '(' . $shahid_count . ')' }}</h4>
            </div>
        </div>
        <div class="iq-card-body">
            <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                aria-describedby="user-list-page-info">
                <thead>
                    <tr>
                        <th>Photo</th>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Page View</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($shahids as $shahid)
                        <tr>
                            <td>
                                <img src="{{ get_file($shahid->thumbnail_path, 'user') }}" height="50">
                            </td>
                            <td>{{ $shahid->name }}</td>
                            <td>{{ $shahid->address }}</td>
                            <td>{{ $shahid->pageview }}</td>
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
                                            <a class="dropdown-item"
                                                href="{{ route('shahid.show', $shahid->slug) }}"><i
                                                    class="ri-eye-fill mr-2"></i>View</a>
                                            <a class="dropdown-item"
                                                href="{{ route('shahid.edit', $shahid->slug) }}"><i
                                                    class="ri-pencil-fill mr-2"></i>Edit</a>
                                            <a class="dropdown-item"
                                                href="{{ route('shahid.delete', $shahid->id) }}"><i
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
                {{ $shahids->links() }}
            </div>
        </div>
    </div>
</div>
