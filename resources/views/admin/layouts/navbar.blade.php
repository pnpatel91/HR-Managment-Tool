
<link rel="stylesheet" href="{{ asset('public/css/admin/navbar.css') }}">

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <!-- Left navbar links -->
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
        </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
        <!-- Messages Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-comments"></i>
                <span class="badge badge-danger navbar-badge">3</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{ auth()->user()->getImageUrlAttribute() }}" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Brad Diesel
                                <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">Call me whenever you can...</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{ auth()->user()->getImageUrlAttribute() }}" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                John Pierce
                                <span class="float-right text-sm text-muted"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">I got your message bro</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <!-- Message Start -->
                    <div class="media">
                        <img src="{{ auth()->user()->getImageUrlAttribute() }}" alt="User Avatar" class="img-size-50 img-circle mr-3">
                        <div class="media-body">
                            <h3 class="dropdown-item-title">
                                Nora Silvester
                                <span class="float-right text-sm text-warning"><i class="fas fa-star"></i></span>
                            </h3>
                            <p class="text-sm">The subject goes here</p>
                            <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> 4 Hours Ago</p>
                        </div>
                    </div>
                    <!-- Message End -->
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Messages</a>
            </div>
        </li>
        <!-- Notifications Dropdown Menu -->
        <li class="nav-item dropdown">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <i class="far fa-bell"></i>
                <span class="badge badge-warning navbar-badge">15</span>
            </a>
            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <span class="dropdown-header">15 Notifications</span>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-envelope mr-2"></i> 4 new messages
                    <span class="float-right text-muted text-sm">3 mins</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-users mr-2"></i> 8 friend requests
                    <span class="float-right text-muted text-sm">12 hours</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item">
                    <i class="fas fa-file mr-2"></i> 3 new reports
                    <span class="float-right text-muted text-sm">2 days</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
            </div>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="dropdown" href="#">
                <img src="{{ auth()->user()->getImageUrlAttribute() }}" alt="{{auth()->user()->name}}" class="profile-user-img-small img-circle elevation-3">
                <span class="brand-text font-weight-light">{{ auth()->user()->name }}</span>
            </a>

            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <div class="p-3">
                    <ul class="dropdown-user">
                        <li>
                            <div class="dw-user-box">
                                <div class="u-img"><img src="{{ auth()->user()->getImageUrlAttribute() }}" id="topnav_dropdown_avatar" alt="user"></div>
                                <div class="u-text">
                                    <h4 id="topnav_dropdown_full_name">{{ auth()->user()->name }}</h4>
                                    <p class="text-muted" id="topnav_dropdown_email">{{ auth()->user()->email }}</p>
                                    <a href="{{ url('admin/profile/edit') }}" class="btn btn-rounded btn-danger btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form">Update Avatar</a>
                                </div>
                            </div>
                        </li>

                        <li role="separator" class="divider"></li>
                        <!--PUNCH-IN & PUNCH-OUT-->
                        @php($attendance = App\Attendance::where('created_by',auth()->user()->id)->latest('attendance_at')->take(1)->get())

                        <li>
                            <div class="card text-center" id="navbar_current_Status">
                              <div class="card-header">
                                Current Status
                              </div>
                              <div class="card-body">
                                @if(isset($attendance[0]))
                                    @if($attendance[0]->status=='punch_in') 
                                        @php( $statusNavbar='punch_out' )
                                        <p class="card-text mt-2">In at {{$attendance[0]->attendance_at}} </p>
                                        <a href=""  onclick="event.preventDefault();document.getElementById('attendance').submit();" class="btn btn-rounded btn-danger btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form mt-2">Punch Out</a>
                                    @else 
                                        @php( $statusNavbar='punch_in' )
                                        <p class="card-text mt-2">Out at {{$attendance[0]->attendance_at}} </p> 
                                        <a href=""  onclick="event.preventDefault();document.getElementById('attendance').submit();" class="btn btn-rounded btn-success btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form mt-2">Punch In</a>
                                    @endif
                                @else 
                                    @php( $statusNavbar='punch_in' )
                                    <p class="card-text mt-2">Wel Come, You are first time login. </p>
                                    <a href=""  onclick="event.preventDefault();document.getElementById('attendance').submit();" class="btn btn-rounded btn-success btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form mt-2">Punch In</a>
                                @endif
                                
                                
                                <form action="{{ route('admin.attendance.store') }}" method="post" id="attendance">
                                    @csrf
                                    <input type="hidden" name="status" id="status" value="{{$statusNavbar}}">
                                    <input type="hidden" name="latitude" id="latitudeNavbar" value="">
                                    <input type="hidden" name="longitude" id="longitudeNavbar" value="">
                                </form>
                                    
                              </div>
                            </div>
                        </li>

                        <li role="separator" class="divider"></li>
                        <!--my profile-->
                        <li>
                            <a href="{{ url('admin/profile') }}" class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form" >
                                <i class="ti-user p-r-4"></i>
                                View Profile</a>
                        </li>

                        <!--update password-->
                        <li>
                            <a href="{{ route('admin.profile.edit.password') }}" id="topnavUpdatePasswordButton" class="edit-add-modal-button js-ajax-ux-request reset-target-modal-form">
                                <i class="ti-lock p-r-4"></i>
                                Update Password</a>
                        </li>

                        <li role="separator" class="divider"></li>
                        <li>
                            <a href="{{ route('logout') }}"  onclick="event.preventDefault();document.getElementById('logout').submit();">
                            <i class="fa fa-power-off p-r-4"></i> Logout</a>
                            <form action="{{ route('logout') }}" method="post" id="logout">
                                @csrf
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </li>
    </ul>
</nav>

