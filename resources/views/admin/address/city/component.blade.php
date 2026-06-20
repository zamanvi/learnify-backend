<div class="col-sm-12 col-lg-6">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between">
            <div class="iq-header-title">
                <h4 class="card-title">Address</h4>
            </div>
            <div class="iq-header-title">
                <h4 class="card-title"> <a href="{{ route('city.create', $division->id) }}">Add City</a></h4>
            </div>
        </div>
        <div class="iq-card-body">
            <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                aria-describedby="user-list-page-info">
                <thead>
                    <tr>
                        <th>I'd</th>
                        <th>Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($citylist as $list)
                        <tr>
                            <td>{{ '#' . $list->id }}</td>
                            {{-- <td>{{ $list->division->country->name }}</td> --}}
                            {{-- <td>{{ $list->division->name }}</td> --}}
                            <td><a href="{{ route('upazila.index', $list->id) }}">{{ $list->name }}</a></td>
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
                                            <a class="dropdown-item" href="{{ route('city.edit', $list->id) }}"><i
                                                    class="ri-pencil-fill mr-2"></i>Edit</a>
                                            {{-- <a class="dropdown-item" href="{{ route('upazila.create', $list->id) }}"><i
                                                    class="ri-pencil-fill mr-2"></i>Create Upozila</a> --}}
                                            <form method="POST" action="{{ route('city.delete', $list->id) }}">
                                                @csrf
                                                @method('delete')
                                                <button href="" class="dropdown-item"><i
                                                        class="ri-delete-bin-6-fill mr-2"></i>Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
