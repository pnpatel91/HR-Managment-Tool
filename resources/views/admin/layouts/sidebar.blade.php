
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ auth()->user()->getImageUrlAttribute() }}" alt="{{auth()->user()->name}}" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">{{ auth()->user()->name }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
       
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
               
                <li class="nav-item has-treeview menu-open">
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin') }}" class="nav-link {{ Route::is('admin.') ? 'active' : '' }}">
                                <i class="fas fa-home nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>

                        @can('view user')
                        <li class="nav-item">
                            <a href="{{ url('admin/user') }}" class="nav-link {{ Route::is('admin.user.*') || Route::is('admin.user.*') || Route::is('admin.profile.*') ? 'active' : '' }}">
                                <i class="fas fa-users nav-icon"></i>
                                <p>Employee</p>
                            </a>
                        </li>
                        @endcan

                        <li class="nav-item">
                            <a href="{{ url('admin/user-treeview') }}" class="nav-link {{ Route::is('admin.user-treeview')  ? 'active' : '' }}">
                                <i class="fas fa-users nav-icon" aria-hidden="true"></i>
                                <p>Employee - Tree View</p>
                            </a>
                        </li>

                        @if(auth()->user()->hasRole('superadmin') || auth()->user()->hasRole('Team Leader')  || auth()->user()->hasRole('Developer') )
                        <li class="nav-item has-treeview {{ (Route::is('admin.attendance.*') && !Route::is('admin.attendance.employee')) || Route::is('admin.leave.*') || (Route::is('admin.rota.*') && !Route::is('admin.rota.employee'))  ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ 
                                (Route::is('admin.attendance.*') && !Route::is('admin.attendance.employee')) || Route::is('admin.leave.*') || Route::is('admin.wikiCategory.*') || Route::is('admin.wikiBlog.*') || (Route::is('admin.rota.*') && !Route::is('admin.rota.employee')) ? 'active' : '' }}">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>
                                    Admin Panel
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="background-color: black;">

                                @can('view attendance')
                                <li class="nav-item">
                                    <a href="{{ url('admin/attendance') }}" class="nav-link {{ Route::is('admin.attendance.*') && !Route::is('admin.attendance.employee')  ? 'active' : '' }}">
                                        <i class="fa fa-clock-o  nav-icon" aria-hidden="true"></i>
                                        <p>Attendance</p>
                                    </a>
                                </li>
                                @endcan

                                @can('view leave - admin')
                                <li class="nav-item">
                                    <a href="{{ url('admin/leave') }}" class="nav-link {{ Route::is('admin.leave.*')  ? 'active' : '' }}">
                                        <i class="fas fa-briefcase nav-icon" aria-hidden="true"></i>
                                        <p>Leave</p>
                                    </a>
                                </li>
                                @endcan 

                                @can('view rota')
                                <li class="nav-item">
                                    <a href="{{ url('admin/rota') }}" class="nav-link {{ Route::is('admin.rota.*') && !Route::is('admin.rota.employee')  ? 'active' : '' }}">
                                        <i class="fas fa-calendar-alt nav-icon"></i>
                                        <p>Rota</p>
                                    </a>
                                </li>
                                @endcan  

                                @can('view Wiki Category')
                                <li class="nav-item">
                                    <a href="{{ url('admin/wikiCategory') }}" class="nav-link {{ Route::is('admin.wikiCategory.*')  ? 'active' : '' }}">
                                        <i class="fa fa-list-alt nav-icon" aria-hidden="true"></i>
                                        <p>Wiki Category</p>
                                    </a>
                                </li>
                                @endcan  

                                @can('view Wiki Blog')
                                <li class="nav-item">
                                    <a href="{{ url('admin/wikiBlog') }}" class="nav-link {{ Route::is('admin.wikiBlog.*')  ? 'active' : '' }}">
                                        <i class="fa fa-list-alt nav-icon" aria-hidden="true"></i>
                                        <p>Wiki Edit</p>
                                    </a>
                                </li>
                                @endcan  
                            </ul>
                        </li>
                        @endif
                        
                        <li class="nav-item">
                            <a href="{{ url('admin/attendance/employee') }}" class="nav-link {{ Route::is('admin.attendance.employee')  ? 'active' : '' }}">
                                <i class="fa fa-clock-o  nav-icon" aria-hidden="true"></i>
                                <p>Attendance</p>
                            </a>
                        </li>

                        

                        @can('view leave - employee')
                        <li class="nav-item">
                            <a href="{{ url('admin/leave-employee') }}" class="nav-link {{ Route::is('admin.leave-employee.*') ? 'active' : '' }}">
                                <i class="fas fa-briefcase nav-icon" aria-hidden="true"></i>
                                <p>Leave</p>
                            </a>
                        </li>
                        @endcan 

                        

                        <li class="nav-item">
                            <a href="{{ url('admin/rota/employee') }}" class="nav-link {{ Route::is('admin.rota.employee') ? 'active' : '' }}">
                                <i class="fas fa-calendar-alt nav-icon"></i>
                                <p>Rota</p>
                            </a>
                        </li>

                        @if(auth()->user()->hasRole('superadmin'))
                        <li class="nav-item has-treeview {{ Route::is('admin.role.*') || Route::is('admin.company.*') || Route::is('admin.branch.*') || Route::is('admin.department.*') || Route::is('admin.rota_template.*') ? 'menu-open' : '' }}">
                            <a href="#" class="nav-link {{ 
                                Route::is('admin.role.*') || Route::is('admin.company.*') || Route::is('admin.branch.*') || Route::is('admin.department.*') || Route::is('admin.rota_template.*') ? 'active' : '' }}">
                                <i class="nav-icon fas fa-cogs"></i>
                                <p>
                                    Settings
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="background-color: black;">

                                @can('view role')
                                <li class="nav-item">
                                    <a href="{{ route('admin.role.index') }}" class="nav-link {{ Route::is('admin.role.*') ? 'active' : '' }}">
                                        <i class="fas fa-file-alt nav-icon"></i>
                                        <p>Role & Permission</p>
                                    </a>
                                </li>
                                @endcan

                                @can('view company')
                                <li class="nav-item">
                                    <a href="{{ url('admin/company') }}" class="nav-link {{ Route::is('admin.company.*')  ? 'active' : '' }}">
                                        <i class="fas fa-warehouse nav-icon"></i>
                                        <p>Company</p>
                                    </a>
                                </li>
                                @endcan 

                                @can('view branch')
                                <li class="nav-item">
                                    <a href="{{ url('admin/branch') }}" class="nav-link {{ Route::is('admin.branch.*') ? 'active' : '' }}">
                                        <i class="fas fa-code-branch nav-icon"></i>
                                        <p>Branch</p>
                                    </a>
                                </li>
                                @endcan 

                                @can('view department')
                                <li class="nav-item">
                                    <a href="{{ url('admin/department') }}" class="nav-link {{ Route::is('admin.department.*') ? 'active' : '' }}">
                                        <i class="far fa-id-card nav-icon"></i>
                                        <p>Department</p>
                                    </a>
                                </li>
                                @endcan

                                @can('view rota_template')
                                <li class="nav-item">
                                    <a href="{{ url('admin/rota_template') }}" class="nav-link {{ Route::is('admin.rota_template.*') ? 'active' : '' }}">
                                        <i class="far fa-calendar-plus nav-icon" aria-hidden="true"></i>
                                        <p>Rota Template</p>
                                    </a>
                                </li>
                                @endcan 
                            </ul>
                        </li>
                        @endif

                        @can('view holiday')
                        <li class="nav-item">
                            <a href="{{ url('admin/holiday') }}" class="nav-link {{ Route::is('admin.holiday.*')  ? 'active' : '' }}">
                                <i class="fas fa-glass-cheers nav-icon"></i>
                                <p>Public Holiday</p>
                            </a>
                        </li>
                        @endcan 

                        <li class="nav-item">
                            <a href="{{ url('admin/wikiBlogView') }}" class="nav-link {{ Route::is('admin.wikiBlogView.*')  ? 'active' : '' }}">
                                <i class="fa fa-book nav-icon" aria-hidden="true"></i>
                                <p>Documentation</p>
                            </a>
                        </li>

                        @if(auth()->user()->hasRole('developer'))
                        <li class="nav-item">
                            <a href="{{ url('maileclipse') }}" class="nav-link" target="_blank">
                                <i class="fas fa-envelope-open-text nav-icon" aria-hidden="true"></i>
                                <p>Email Templates</p>
                            </a>
                        </li>
                        @endif
                    </ul>
                </li>
                
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>