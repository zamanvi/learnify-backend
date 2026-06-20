            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Question</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                            aria-describedby="user-list-page-info">
                            <thead>
                                <tr>
                                    <th>Contest</th>
                                    <th>Q Name</th>
                                    <th>Answer</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($contestQuestions as $contestQuestion)
                                    <tr>
                                        <td>{{ $contestQuestion->contest->name }}</td>
                                        <td>{{ $contestQuestion->name }}</td>
                                        <td>{{ 'Option ' . $contestQuestion->option5 }}</td>
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
                                                        <a class="dropdown-item" href="{{ route('contest.question.show', $contestQuestion->id) }}"><i
                                                                class="ri-eye-fill mr-2"></i>View</a>
                                                        <a class="dropdown-item" href="{{ route('contest.question.edit', $contestQuestion->id) }}"><i
                                                                class="ri-pencil-fill mr-2"></i>Edit</a>
                                                        <a class="dropdown-item" href="{{ route('contest.question.delete', $contestQuestion->id) }}"><i
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
                            {{ $contestQuestions->links() }}
                        </div>
                    </div>
                </div>
            </div>
