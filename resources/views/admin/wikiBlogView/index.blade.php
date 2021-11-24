@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="message">
            @include('message.alert')
        </div>
        
        <div class="row col-sm-12 page-titles">
            <div class="col-lg-5 p-b-9 align-self-center text-left  " id="list-page-actions-container">
                <div id="list-page-actions">
                    
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Documentation</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Documentation</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>

        <div class="col-md-12">
            <div class="card" style="display:none;">
                <div class="card-header">
                    <h3 class="card-title" id="wiki-title"></h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body" id="wiki-body">
                    
                </div>
                <!-- /.card-body -->
            </div>
        </div>

    </div>
</div>

<script type="text/javascript">
    function linkclickable(id) {
        
        $.ajax({
          url : '{{ route('admin.wikiBlogView.ajax.get_blog_details') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "id": id
            },
          type: 'post',
          dataType: 'json',
          success: function( result )
          {
            console.log(result);
            $('#wiki-title').html(result.title);
            $('#wiki-body').html(result.description);
            $(".card").fadeIn();
            $(".sidebar-search-results").fadeOut();
          }
        });
    }

    function fun_sidebar_search() {
        var search = $('#search').val();
        if(search!=''){
            $.ajax({
              url : '{{ route('admin.wikiBlogView.ajax.search') }}',
              data: {
                "_token": "{{ csrf_token() }}",
                "search": search
                },
              type: 'post',
              dataType: 'html',
              success: function( result )
              {
                console.log(result);
                $('.sidebar-search-results').html(result);
                $(".sidebar-search-results").fadeIn();
              }
            });  
        }
    }
</script>

@endsection
