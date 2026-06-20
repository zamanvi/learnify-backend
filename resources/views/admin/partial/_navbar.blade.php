<div class="iq-top-navbar">
    <div class="iq-navbar-custom">
        <div class="iq-sidebar-logo">
            <div class="top-logo">
                <a href="/superadmin" class="logo">
                    {{-- <img src="images/logo.png" class="img-fluid" alt=""> --}}
                    <span>RedRose</span>
                </a>
            </div>
        </div>
        <div class="navbar-breadcrumb">
            <h5 class="mb-0">Dashboard</h5>
            @yield('breadcrumb')
        </div>
        <nav class="navbar navbar-expand-lg navbar-light p-0">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <i class="ri-menu-3-line"></i>
            </button>
            <div class="iq-menu-bt align-self-center">
                <div class="wrapper-menu">
                    <div class="line-menu half start"></div>
                    <div class="line-menu"></div>
                    <div class="line-menu half end"></div>
                </div>
            </div>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav ml-auto navbar-list">
                    @if (Auth::user()->user_type == 1)
                        <li class="nav-item">
                            <a href="#" class="search-toggle iq-waves-effect">
                                <i class="ri-notification-2-line"></i>
                                <span class="bg-danger dots"></span>
                            </a>
                            <div class="iq-sub-dropdown">
                                <div class="iq-card iq-card-block iq-card-stretch iq-card-height shadow-none m-0">
                                    <div class="iq-card-body p-0 ">
                                        <div class="bg-danger p-3">
                                            <h5 class="mb-0 text-white">All Notifications
                                                {{ $notifications->count() }}
                                                <a href="/notification"
                                                    class="badge badge-info text text-danger float-right">View All</a>
                                            </h5>
                                        </div>
                                        @foreach ($notifications as $key => $list)
                                            @if ($key < 5)
                                                <div class="iq-sub-card media align-items-center">
                                                    <div class="media-body">
                                                        <h6 class="mb-0 ">{{ $list->name }}</h6>
                                                        @if ($list->status == 1)
                                                            <small class="float-right font-size-12">Unread</small>
                                                        @else
                                                            <small class="float-right font-size-12">Read</small>
                                                        @endif
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </li>
                    @endif
                    <li class="nav-item iq-full-screen">
                        <a href="#" class="iq-waves-effect" id="btnFullscreen">
                            <i class="ri-fullscreen-line"></i>
                        </a>
                    </li>
                </ul>
            </div>
            <ul class="navbar-list">
                <li>
                    <a href="#" class="search-toggle iq-waves-effect text-white">
                        @if (Auth::user()->profile_photo_path != null)
                            <img src="{{ Auth::user()->profile_photo_path }}" class="img-fluid rounded-circle mr-3"
                                alt="user">
                        @else
                            <img src="{{ asset('images/user_01.jpg') }}" class="img-fluid rounded-circle mr-3"
                                alt="user">
                        @endif
                    </a>
                    <div class="iq-sub-dropdown iq-user-dropdown">
                        <div class="iq-card iq-card-block iq-card-stretch iq-card-height shadow-none m-0">
                            <div class="iq-card-body p-0 ">
                                <div class="bg-primary p-3">
                                    <h5 class="mb-0 text-white line-height">{{ Auth::user()->name }}</h5>
                                    <span class="text-white font-size-12">Online</span>
                                </div>
                                <a href="#" class="iq-sub-card iq-bg-primary-hover">
                                    <div class="media align-items-center">
                                        <div class="rounded iq-card-icon iq-bg-primary">
                                            <i class="ri-file-user-line"></i>
                                        </div>
                                        <div class="media-body ml-3">
                                            <h6 class="mb-0 ">My Profile</h6>
                                            <p class="mb-0 font-size-12">View personal profile details.</p>
                                        </div>
                                    </div>
                                </a>
                                <div class="d-inline-block w-100 text-center p-3">
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button class="iq-bg-danger iq-sign-btn" href="sign-in.html"
                                            role="button">Logout<i class="ri-login-box-line ml-2"></i></button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </nav>
    </div>
</div>
