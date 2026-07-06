@extends('layouts.admin')

@section('title')
    Notification Logs
@endsection

@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Notification Logs</li>
        </ul>
    </nav>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Notification Logs</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Type</th>
                                        <th>Title</th>
                                        <th>Body</th>
                                        <th>Recipients</th>
                                        <th>Sent At</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($logs as $log)
                                        <tr>
                                            <td>{{ $loop->iteration + ($logs->currentPage() - 1) * $logs->perPage() }}</td>
                                            <td><span class="badge badge-primary">{{ $log->type }}</span></td>
                                            <td>{{ $log->title }}</td>
                                            <td>{{ $log->body }}</td>
                                            <td>{{ number_format($log->recipients_count) }}</td>
                                            <td>{{ $log->sent_at ? $log->sent_at->format('d M Y, h:i A') : '-' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="6" class="text-center">No notification logs found.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            {{ $logs->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
