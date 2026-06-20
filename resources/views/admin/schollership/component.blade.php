<div class="col-sm-12 col-lg-6">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between">
            <div class="iq-header-title">
                <h4 class="card-title"> ScholarShip</h4>
            </div>
        </div>
        <div class="iq-card-body">
            <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                aria-describedby="user-list-page-info">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Enroll</th>
                        <th>Win</th>
                        <th>Price</th>
                        <th>P.V</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($scholarShips as $scholarShip)
                    <tr>
                        <td>{{ $scholarShip->title }}</td>
                        <td>{{ $scholarShip->enroll_limit }}</td>
                        <td>{{ $scholarShip->winner_limit }}</td>
                        <td>{{ $scholarShip->price }}</td>
                        <td>{{ $scholarShip->pageview }}</td>
                        <td>{{ $scholarShip->status }}</td>
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
                                            href="{{ route('scholarship.show', $scholarShip->slug) }}"><i
                                                class="ri-eye-fill mr-2"></i>View</a>
                                        @if (!$scholarShip->is_publish)
                                        <a class="dropdown-item"
                                            href="{{ route('scholarship.edit', $scholarShip->slug) }}"><i
                                                class="ri-pencil-fill mr-2"></i>Edit</a>
                                        @endif
                                        <a class="dropdown-item"
                                            href="{{ route('scholarship.delete', $scholarShip->slug) }}"><i
                                                class="ri-delete-bin-6-fill mr-2"></i>Delete</a>
                                        <a class="dropdown-item"
                                            href="{{ route('scholarship.participant', $scholarShip->slug) }}"><i
                                                class="ri-delete-bin-6-fill mr-2"></i>Participant</a>
                                        @if ($scholarShip->is_publish)
                                            <a class="dropdown-item"
                                                href="{{ route('scholarship.result', $scholarShip->slug) }}"><i
                                                    class="ri-delete-bin-6-fill mr-2"></i>Result</a>
                                        @else
                                            <a class="dropdown-item"
                                                href="{{ route('scholarship.publish', $scholarShip->slug) }}"><i
                                                    class="ri-delete-bin-6-fill mr-2"></i>Publish</a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No Data found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="">
                {{ $scholarShips->links() }}
            </div>
        </div>
    </div>
</div>
