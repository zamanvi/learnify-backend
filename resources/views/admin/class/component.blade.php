            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">All Class {{ '(' . 'eventCount' . ')' }}</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($classlist as $list)
                                    <tr>
                                        <td>{{ $list->name }}</td>
                                        <td>
                                            <div class="iq-card-header-toolbar d-flex align-items-center">
                                                <div class="dropdown">
                                                    <span class="dropdown-toggle text-primary" id="dropdownMenuButton5"
                                                        data-toggle="dropdown">
                                                        <a href="" class="align-items-center"><i class="ri-more-fill"></i></a>
                                                    </span>
                                                    <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton5">
                                                        <a class="dropdown-item" href="/event/{{ $list->id }}"><i class="ri-eye-fill mr-2"></i>View</a>
                                                        <a class="dropdown-item" href="/event/{{ $list->id }}/edit"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                                        <form method="POST" action="/event/{{$list->id}}">
                                                            @csrf
                                                            @method('delete')
                                                            <button href="" class="dropdown-item"><i class="ri-delete-bin-6-fill mr-2"></i>Delete</button>
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
                            {{ $classlist->links() }}
                        </div>
                    </div>
                </div>
            </div>
