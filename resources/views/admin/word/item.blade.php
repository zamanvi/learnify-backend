<div class="col-md-6">
    <div class="card">
        <div class="card-body">
            <h3 class="card-title">All words</h3>
            <table id="user-list-table" class="table table-striped table-bordered mt-1" role="grid"
                aria-describedby="user-list-page-info">
                <thead>
                    <tr>
                        <th>Word</th>
                        <th>Meaning</th>
                        <th>Type</th>
                        <th width="5%">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($words as $word)
                        <tr>
                            <td>{{ $word->word }}</td>
                            <td>{{ $word->meaning }}</td>
                            <td>{{ $word->type }}</td>
                            <td>
                                <div class="iq-card-header-toolbar d-flex align-items-center">
                                    <div class="dropdown">
                                        <span class="dropdown-toggle text-primary" id="dropdownMenuButton"
                                            data-toggle="dropdown">
                                            <a href="#" class="align-items-center"><i
                                                    class="ri-more-fill"></i></a>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-right"
                                            aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="{{ route('words.show', $word->id) }}"><i
                                                    class="ri-eye-fill mr-2"></i>Show</a>
                                            <a class="dropdown-item" href="{{ route('words.edit', $word->id) }}"><i
                                                    class="ri-eye-fill mr-2"></i>Edit</a>
                                            <form action="{{ route('words.destroy', $word->id) }}" method="POST"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item"
                                                    onclick="return confirm('Are you sure you want to delete this word?')">
                                                    <i class="ri-delete-bin-fill mr-2"></i> Delete
                                                </button>
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
                {{ $words->links() }}
            </div>
        </div>
    </div>
</div>
