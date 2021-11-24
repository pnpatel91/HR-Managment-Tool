
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="{{ url('/') }}" class="brand-link">
        <img src="{{ auth()->user()->getImageUrlAttribute() }}" alt="{{auth()->user()->name}}" class="brand-image img-circle elevation-3"
            style="opacity: .8">
        <span class="brand-text font-weight-light">{{ auth()->user()->name }}</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="mt-2">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" placeholder="Search" aria-label="Search" id="search" onkeyup="fun_sidebar_search(this);">
            </div>
            <div class="sidebar-search-results" style="display:none;">
                
            </div>
        </div>
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
               
                <li class="nav-item has-treeview menu-open">
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ url('admin') }}" class="nav-link {{ Route::is('admin.') ? 'active' : '' }}">
                                <i class="fas fa-backward nav-icon"></i>
                                <p>Dashboard</p>
                            </a>
                        </li>


                        @foreach($categories as $category)
                        @if(count($category->wikiBlogs)>0)
                        <li class="nav-item has-treeview">
                            <a href="#" class="nav-link">
                                <i class="nav-icon fas fa-angle-right"></i>
                                <p>
                                    {{$category->name}}
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview" style="background-color: black;">
                                @foreach($category->wikiBlogs as $wikiBlog)
                                    <li class="nav-item">
                                        <a href="#" class="nav-link" onclick="linkclickable({{$wikiBlog->id}})">
                                            <p>{{$wikiBlog->title}}</p>
                                            @if(count($wikiBlog->allChildren)>0)
                                            <i class="right fas fa-angle-left"></i>
                                            @endif
                                        </a>

                                        @if(count($wikiBlog->allChildren)>0)
                                        <ul class="nav nav-treeview" style="background-color: black;">
                                            @foreach($wikiBlog->allChildren as $sub_blog)
                                                @include('admin.layouts.subwikisidebar', ['sub_blog' => $sub_blog])
                                            @endforeach
                                        </ul>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </li>
                         @endif
                        @endforeach
                    </ul>
                </li>
                
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>

