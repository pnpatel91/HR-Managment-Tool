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
                    <!--ADD NEW ITEM-->
                    @can('create Wiki Category')
                    <a href="{{ route('admin.wikiCategory.create') }}" class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="popup-modal-button">
                        <span tooltip="Create new wiki category." flow="right"><i class="fas fa-plus"></i></span>
                    </a>
                    @endcan
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Wiki Category</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Wiki Category</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Wiki Category List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive list-table-wrapper">
                        <table class="table table-hover dataTable no-footer" id="table" width="100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Created By</th>
                                <th>Status</th>
                                @if (auth()->user()->can('edit Wiki Category') || auth()->user()->can('delete Wiki Category'))
                                <th class="noExport" style="width: 100px;">Action</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                                @forelse ($wikiCategories as $wikiCategory)
                                <tr>
                                    <td class="col-2">{{ $wikiCategory->name }}</td>
                                    <td class="col-2"><img src="{{$wikiCategory->creator->getImageUrlAttribute($wikiCategory->creator->id)}}" alt="user_id_{{$wikiCategory->creator->name}}" class="profile-user-img-small img-circle"> {{$wikiCategory->creator->name}}</td>
                                    <td>
                                        <div class="dropdown action-label">
                                            <a class="btn btn-white btn-sm btn-rounded dropdown-toggle" href="#" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-dot-circle-o @if($wikiCategory->status=='Active') text-success @else text-danger @endif "></i> @if($wikiCategory->status=='Active') Active @else Inactive @endif </a>
                                            <div class="dropdown-menu dropdown-menu-right" style="">
                                                <a class="dropdown-item" href="#" onclick="funChangeStatus({{$wikiCategory->id}},1); return false;"><i class="fa fa-dot-circle-o text-success"></i> Active</a>
                                                <a class="dropdown-item" href="#" onclick="funChangeStatus({{$wikiCategory->id}},0); return false;"><i class="fa fa-dot-circle-o text-danger"></i> Inactive</a>
                                            </div>
                                        </div>
                                    </td>
                                    @if (auth()->user()->can('edit Wiki Category') || auth()->user()->can('delete Wiki Category'))
                                    <td  class="col-2">
                                        @can('edit Wiki Category')
                                        <a href="{{ route('admin.wikiCategory.edit', ['wikiCategory' => $wikiCategory->id]) }}" 
                                            class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button">
                                            <span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span>
                                        </a>
                                        @endcan 
                                        @can('delete Wiki Category')
                                        <form method="post" class="float-left delete-form"
                                            action="{{ route('admin.wikiCategory.destroy', ['wikiCategory' => $wikiCategory->id ]) }}">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <span tooltip="Delete" flow="up"><i class="fas fa-trash-alt"></i></span>
                                            </button>
                                        </form>
                                        @endcan
                                    </td>
                                    @endif
                                    
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">There is no wiki category.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                </div>
                <!-- /.card-body -->
            </div>
        </div>

    </div>
</div>


<script>
function datatables() {
    $("#table").load(location.href + " #table");
    window.location.reload();
}

function datatables_firstcall() {
    var table = $('#table').DataTable({
        dom: 'RBfrtip',
        buttons: [],
        aaSorting     : [[1, 'asc']],
        "bDestroy": true
    });
}

datatables_firstcall();

/*For user status change*/
    function funChangeStatus(id,status) {
        $("#pageloader").fadeIn();
        $.ajax({
          url : '{{ route('admin.wikiCategory.ajax.change_status') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "id": id,
            "status": status
            },
          type: 'get',
          dataType: 'json',
          success: function( result )
          {
            datatables();
            $("#pageloader").hide();
          }
        });
    }
</script>

@endsection
