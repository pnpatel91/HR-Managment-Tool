<a class="nav-link" data-toggle="dropdown" href="#">
    <i class="far fa-bell"></i>
    <span class="badge badge-warning navbar-badge">{{count(auth()->user()->unreadNotifications)}}</span>
</a>
<div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
    <span class="dropdown-header">{{count(auth()->user()->unreadNotifications)}} Notifications</span>
    <div class="dropdown-divider"></div>
    @inject('DashboardController', 'App\Http\Controllers\Admin\DashboardController')
    @if(count(auth()->user()->unreadNotifications)>0)
        @foreach(auth()->user()->unreadNotifications as $notification)
            <a href="{{ action('Admin\DashboardController@notificationMarkAsRead', $notification->id) }}" class="dropdown-item">
                <!-- Message Start -->
                <div class="media">
                    <img src="{{ auth()->user()->getImageUrlAttribute($notification->data['user_id']) }}" alt="User Avatar" class="img-size-50 mr-3 img-circle">
                    <div class="media-body">
                        <h3 class="dropdown-item-title">
                            {{$notification->data['user_name']}}
                            <!-- <span class="float-right text-sm text-danger"><i class="fas fa-star"></i></span> -->
                        </h3>
                        <p class="text-sm">{{$notification->data['text']}} <span class="noti-title">{{$notification->data['name']}}</span></p>
                        <p class="text-sm text-muted"><i class="far fa-clock mr-1"></i> {{ $DashboardController::timeDiff($notification->created_at) }}</p>
                    </div>
                </div>
                <!-- Message End -->
            </a>
            <div class="dropdown-divider"></div>
        @endforeach  
        <a href="{{ action('Admin\DashboardController@notificationMarkAllAsRead', auth()->user()->id) }}" class="dropdown-item dropdown-footer">Clear All Notification</a>
    @else
        <p class="text-center">Notification not found</p>
    @endif

    
</div>