@extends('layouts.admin')

@section('title')
    RedRose
@endsection
@section('breadcrumb')
    <nav aria-label="breadcrumb">
        <ul class="breadcrumb">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Super Admin</li>
        </ul>
    </nav>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                    <div class="iq-card-body pb-0">
                        <div class="rounded-circle iq-card-icon iq-bg-primary"><i class="ri-exchange-dollar-fill"></i>
                        </div>
                        <span class="float-right line-height-6"><a href="/adminlist">Admin</a></span>
                        <div class="clearfix"></div>
                        <div class="text-center">
                            <h2 class="mb-0"><span class="counter">{{ $a_user }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                    <div class="iq-card-body pb-0">
                        <div class="rounded-circle iq-card-icon iq-bg-success"><i class="ri-group-line"></i></div>
                        <span class="float-right line-height-6"><a href="{{ route('alluser') }}">Users</a></span>
                        <div class="clearfix"></div>
                        <div class="text-center">
                            <h2 class="mb-0"><span class="counter">{{ ($g_user + $t_user + $s_user) - $a_user }}</span></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                    <div class="iq-card-body pb-0">
                        <div class="rounded-circle iq-card-icon iq-bg-warning"><i class="ri-bar-chart-grouped-line"></i></div>
                        <span class="float-right line-height-6"><a href="{{ route('contest.index') }}">Contest</a></span>
                        <div class="clearfix"></div>
                        <div class="text-center">
                            <h2 class="mb-0"><span></span><spanclass="counter">{{ $contest_count }}</spanclass=></h2>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-3">
                <div class="iq-card iq-card-block iq-card-stretch iq-card-height overflow-hidden">
                    <div class="iq-card-body pb-0">
                        <div class="rounded-circle iq-card-icon iq-bg-warning"><i class="ri-bar-chart-grouped-line"></i></div>
                        <span class="float-right line-height-6"><a href="{{ route('scholarship.create') }}">ScholarShip</a></span>
                        <div class="clearfix"></div>
                        <div class="text-center">
                            <h2 class="mb-0"><span></span><spanclass="counter">{{ $scholarShip_count }}</spanclass=></h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
