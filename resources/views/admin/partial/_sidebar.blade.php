<div class="iq-sidebar">
    <div class="iq-sidebar-logo d-flex justify-content-between">
        <a href="/superadmin">
            <span>Redrose</span>
        </a>
        <div class="iq-menu-bt align-self-center">
            <div class="wrapper-menu">
                <div class="line-menu half start"></div>
                <div class="line-menu"></div>
                <div class="line-menu half end"></div>
            </div>
        </div>
    </div>
    <div id="sidebar-scrollbar">
        <nav class="iq-sidebar-menu">
            <ul id="iq-sidebar-toggle" class="iq-menu">
                @if (Auth::user()->user_type == 1)
                    <li class="iq-menu-title @if (Route::is('adminlist')) active @endif"><i class="ri-separator"></i><span>Super Admin</span></li>
                    <li class="@if (Route::is('adminlist')) active @endif"><a href="{{ route('adminlist') }}"><i class="lab la-elementor"></i>Admin</a></li>
                    {{-- <li><a href="{{ route('event.index') }}"><i class="lab la-elementor"></i>Event Management</a></li> --}}

                    <li class="@if (Route::is(['chapters.index', 'chapters.show', 'chapters.edit', 'lessons.index', 'lessons.show', 'lessons.edit' , 'words.index', 'words.show', 'words.edit', 'chapters.lessons.create', 'chapters.lessons.words.create'])) active @endif">
                        <a href="{{ route('chapters.index') }}" class="iq-waves-effect collapsed">
                            <i class="ri-home-4-line"></i><span>Vocabulary Management</span></a>
                    </li>
                    <li><a href="{{ route('notices.index') }}"><i class="lab la-elementor"></i>Notices Management</a></li>
                    <li><a href="{{ route('contest.index') }}"><i class="lab la-elementor"></i>Contest Management</a></li>
                    <li><a href="{{ route('modeltest.index') }}"><i class="lab la-elementor"></i>Modeltest Management</a></li>
                    <li><a href="{{ route('book.index') }}"><i class="lab la-elementor"></i>Book Management</a></li>
                    <li><a href="{{ route('wizard.chapter.index') }}"><i class="lab la-elementor"></i>Wizard Management</a></li>
                    <li class="@if (Route::is(['alluser',])) active @endif">
                        <a href="#alluser" class="iq-waves-effect collapsed" data-toggle="collapse"
                            aria-expanded="false"><i class="ri-home-4-line"></i><span>User Management</span><i
                                class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="alluser" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                            <li class="@if (Route::is(['alluser',])) active @endif"><a href="{{ route('alluser') }}"><i class="las la-house-damage"></i>All User</a></li>
                            <li><a href="#"><i class="las la-house-damage"></i>As a Student</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#asateacher" class="iq-waves-effect collapsed" data-toggle="collapse"
                            aria-expanded="false"><i class="ri-home-4-line"></i><span>As A Teacher</span><i
                                class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="asateacher" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                            <li><a href="{{route('admin.approval.teacher.index')}}"><i class="las la-house-damage"></i>Approved Teacher</a></li>
                            <li><a href="{{route('admin.approval.teacher.pending')}}"><i class="las la-house-damage"></i>Pending Teacher</a></li>
                            <li><a href="{{route('admin.approval.teacher.unapproved')}}"><i class="las la-house-damage"></i>Unapproved Teacher</a></li>
                        </ul>
                    </li>
                    <li>
                        <a href="#settings" class="iq-waves-effect collapsed" data-toggle="collapse"
                            aria-expanded="false"><i class="ri-home-4-line"></i><span>Settings</span><i
                                class="ri-arrow-right-s-line iq-arrow-right"></i></a>
                        <ul id="settings" class="iq-submenu collapse" data-parent="#iq-sidebar-toggle">
                            <li><a href="/allclass"><i class="lab la-elementor"></i>Class</a></li>
                            <li><a href="{{ route('country.index') }}"><i class="las la-house-damage"></i>Address</a>
                            </li>
                            <li><a href="{{ route('page.create') }}"><i class="las la-house-damage"></i>Pages</a></li>
                            <li><a href="/slider" class="iq-waves-effect"><i class="ri-record-circle-line iq-arrow-left"></i> <span>Slider</span></a></li>
                            <li><a href="{{ route('app-version') }}" class="iq-waves-effect"><i class="ri-record-circle-line iq-arrow-left"></i> <span>App Version</span></a></li>
                        </ul>
                    </li>
                    <li class="@if (Route::is(['scholarship.create', 'scholarship.show', 'scholarship.edit'])) active @endif">
                        <a href="{{ route('scholarship.create') }}" class="iq-waves-effect collapsed"><i
                                class="ri-record-circle-line iq-arrow-left"></i>
                            <span>Scholarship</span>
                        </a>
                    </li>
                    <li class="@if (Route::is(['blog.create', 'blog.show', 'blog.edit'])) active @endif">
                        <a href="{{ route('blog.create') }}" class="iq-waves-effect collapsed"><i
                                class="ri-record-circle-line iq-arrow-left"></i>
                            <span>Blog Management</span>
                        </a>
                    </li>
                    {{-- <li class="@if (Route::is(['shahid.create', 'shahid.show', 'shahid.edit'])) active @endif">
                        <a href="{{ route('shahid.create') }}" class="iq-waves-effect collapsed"><i
                                class="ri-record-circle-line iq-arrow-left"></i>
                            <span>Shahid Management</span>
                        </a>
                    </li> --}}
                    <li>
                        <a href="/supportlist" class="iq-waves-effect collapsed"><i
                                class="ri-record-circle-line iq-arrow-left"></i>
                            <span>Support</span>
                        </a>
                    </li>
                    <li class="@if (Route::is('notification.logs')) active @endif">
                        <a href="{{ route('notification.logs') }}" class="iq-waves-effect collapsed"><i
                                class="lab la-elementor"></i>
                            <span>📬 Notification Logs</span>
                        </a>
                    </li>
                    <li>
                        <a href="/clear-cash" class="iq-waves-effect collapsed"><i
                                class="ri-record-circle-line iq-arrow-left"></i>
                            <span>Clear Cash</span>
                        </a>
                    </li>
                @endif
            </ul>
        </nav>
        <div class="p-3"></div>
    </div>
</div>
