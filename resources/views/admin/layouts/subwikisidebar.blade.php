<li class="nav-item">
    <a href="#" class="nav-link"  onclick="linkclickable({{$sub_blog->id}})">
        <p>{{$sub_blog->title}}</p>
        @if(count($sub_blog->allChildren)>0)
            <i class="right fas fa-angle-left"></i>
        @endif
    </a>
    @if(count($sub_blog->allChildren)>0)
    <ul class="nav nav-treeview" style="background-color: black;">
        @foreach($sub_blog->allChildren as $sub_sub_blog)
            @include('admin.layouts.subwikisidebar', ['sub_blog' => $sub_sub_blog])
        @endforeach
    </ul>
    @endif
</li>