            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Address => Countries</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>I'd</th>
                                    <th>Name</th>
                                    <th>Is Active</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($countrylist as $list)
                                    <tr>
                                        <td>{{ '#' . $list->id }}</td>
                                        <td>
                                            @if ($list->is_active == 'on')
                                                <a href="{{ route('division.index', $list->id) }}">{{ $list->name }}</a></td>
                                            @else
                                               {{ $list->name }}
                                            @endif
                                        </td>
                                        <td>
                                            @if ($list->is_active == 'on')
                                                <span class="text text-success"> Active</span>
                                            @else
                                                <span class="text text-danger"> Inactive</span>
                                            @endif
                                        </td>
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
                                                            href="{{ route('country.edit', $list->id) }}"><i
                                                                class="ri-pencil-fill mr-2"></i>Edit</a>
                                                        @if ($list->is_active == 'on')
                                                            <form method="POST"
                                                                action="{{ route('country.inactive', $list->id) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="is_active" value="off">
                                                                <input type="hidden" name="name"
                                                                    value="{{ $list->name }}">
                                                                <button href="" class="dropdown-item"><i
                                                                        class="ri-pencil-fill mr-2"></i>Inactive</button>
                                                            </form>
                                                        @else
                                                            <form method="POST"
                                                                action="{{ route('country.active', $list->id) }}">
                                                                @csrf
                                                                @method('PUT')
                                                                <input type="hidden" name="is_active" value="on">
                                                                <input type="hidden" name="name"
                                                                    value="{{ $list->name }}">
                                                                <button href="" class="dropdown-item"><i
                                                                        class="ri-pencil-fill mr-2"></i>Active</button>
                                                            </form>
                                                        @endif
                                                        {{-- @if ($list->is_active == 'on')
                                                            <a class="dropdown-item"
                                                                href="{{ route('division.create', $list->id) }}"><i
                                                                    class="ri-eye-fill mr-2"></i>Create Division</a>
                                                        @endif --}}
                                                        <form method="POST"
                                                            action="{{ route('country.delete', $list->id) }}">
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
                        <div class="">
                            {{ $countrylist->links() }}
                        </div>
                    </div>
                </div>
            </div>
