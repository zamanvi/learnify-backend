<div class="col-sm-12 col-lg-6">
    <div class="iq-card">
        <div class="iq-card-header d-flex justify-content-between">
            <div class="iq-header-title">
                <h4 class="card-title">Contest {{ '(' . $contest_count . ')' }}</h4>
            </div>
            <div class="iq-header-title">
                <h4 class="card-title"> <a href="{{ route('contest.create') }}">Create Contest</a></h4>
            </div>
        </div>
        <div class="iq-card-body">
            <table id="user-contest-table" class="table table-striped table-bordered mt-1" role="grid"
                aria-describedby="user-contest-page-info">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Date</th>
                        <th>Price</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($contests as $contest)
                        <tr>
                            <td>{{ $contest->name }}</td>
                            <td>{{ $contest->date }}</td>
                            <td>{{ $contest->price }}</td>
                            <td>{{ $contest->status ? 'Active' : 'Inactive' ; }}</td>
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
                                                href="{{ route('contest.show', $contest->slug) }}"><i
                                                    class="ri-eye-fill mr-2"></i>View</a>
                                            <a class="dropdown-item"
                                                href="{{ route('contest.edit', $contest->slug) }}"><i
                                                    class="ri-pencil-fill mr-2"></i>Edit</a>
                                            <a class="dropdown-item"
                                                href="{{ route('contest.delete', $contest->id) }}"><i
                                                    class="ri-delete-bin-6-fill mr-2"></i>Delete</a>
                                            <a class="dropdown-item"
                                                href="{{ route('contest.question.create', $contest->slug) }}"><i
                                                    class="ri-eye-fill mr-2"></i>Question</a>
                                            <a class="dropdown-item"
                                                href="{{ route('contest.result', $contest->slug) }}"><i
                                                    class="ri-eye-fill mr-2"></i>Result</a>
                                            <a class="dropdown-item"
                                                href="{{ route('contest.participant', $contest->slug) }}"><i
                                                    class="ri-eye-fill mr-2"></i>Participant</a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="">
                {{ $contests->links() }}
            </div>
        </div>
    </div>
</div>
