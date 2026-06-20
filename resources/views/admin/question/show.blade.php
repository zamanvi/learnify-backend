@extends('admin.index')
@section('title')
    Contests
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Question - {{ $contestQuestion->name }}</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            @include('admin.question.component')
            <div class="col-sm-12 col-lg-6">
                <div class="iq-card">
                    <div class="iq-card-header d-flex justify-content-between">
                        <div class="iq-header-title">
                            <h4 class="card-title">Question "{{ $contestQuestion->name }}"</h4>
                        </div>
                    </div>
                    <div class="iq-card-body">
                        <h5 class="card-title">Contest Name: <span class="text text-primary">{{ $contestQuestion->contest->name }}</span></h5>
                        <h5 class="card-title">Question Name: <span class="text text-primary">{{ $contestQuestion->name }}</span></h5>
                        <h5 class="card-title">Option 1: <span class="text text-primary">{{ $contestQuestion->option1 }}</span></h5>
                        <h5 class="card-title">Option 2: <span class="text text-primary">{{ $contestQuestion->option2 }}</span></h5>
                        <h5 class="card-title">Option 3: <span class="text text-primary">{{ $contestQuestion->option3 }}</span></h5>
                        <h5 class="card-title">Option 4: <span class="text text-primary">{{ $contestQuestion->option4 }}</span></h5>
                        <h5 class="card-title">Currect Answer: <span class="text text-primary">{{ 'Option ' . $contestQuestion->option5 }}</span></h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
