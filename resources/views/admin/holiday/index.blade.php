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
                    @can('create holiday')
                    <a href="{{ route('admin.holiday.create') }}" class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="popup-modal-button">
                        <span tooltip="Create new holiday." flow="right"><i class="fas fa-plus"></i></span>
                    </a>
                    @endcan
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Holidays</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Holidays</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Holidays List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive list-table-wrapper">
                        <table class="table table-hover dataTable no-footer" id="table" width="100%">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Holiday Date</th>
                                <th>Branches</th>
                                <th>Created By</th>
                                @if (auth()->user()->can('edit holiday') || auth()->user()->can('delete holiday'))
                                <th class="noExport" style="width: 100px;">Action</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                                @forelse ($holidays as $holiday)
                                <tr @if($holiday->next_holiday() == $holiday->id) class='datatable-active' @endif>
                                    <td class="col-2">{{ $holiday->name }}</td>
                                    <td  class="col-2">{{ date_format(date_create($holiday->holiday_date), "Y/m/d") }}</td>
                                    <td class="col-4">@foreach($holiday->branches as $branch)<span class="selection_choice">{{$branch->company->name}} - {{$branch->name}}</span>@endforeach</td>
                                    <td class="col-2"><img src="{{$holiday->creator->getImageUrlAttribute($holiday->creator->id)}}" alt="user_id_{{$holiday->creator->name}}" class="profile-user-img-small img-circle"> {{$holiday->creator->name}}</td>
                                    @if (auth()->user()->can('edit holiday') || auth()->user()->can('delete holiday'))
                                    <td  class="col-2">
                                        @can('edit holiday')
                                        <a href="{{ route('admin.holiday.edit', ['holiday' => $holiday->id]) }}" 
                                            class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-button">
                                            <span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span>
                                        </a>
                                        @endcan 
                                        @can('delete holiday')
                                        <form method="post" class="float-left delete-form"
                                            action="{{ route('admin.holiday.destroy', ['holiday' => $holiday->id ]) }}">
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
                                    <td colspan="5">There is no holiday.</td>
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
</script>

@endsection
