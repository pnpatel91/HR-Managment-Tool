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
                    @php($attendance = App\Attendance::where('created_by',auth()->user()->id)->latest('attendance_at')->take(1)->get())
                    
                    @if(isset($attendance[0]))
                        @if($attendance[0]->status=='punch_in') 
                            @php( $statusNavbar='punch_out' )
                            <a href=""  onclick="event.preventDefault();document.getElementById('attendance').submit();" class="btn btn-rounded btn-danger btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form mt-2">Punch Out</a>
                        @else 
                            @php( $statusNavbar='punch_in' )
                            <a href=""  onclick="event.preventDefault();document.getElementById('attendance').submit();" class="btn btn-rounded btn-success btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form mt-2">Punch In</a>
                        @endif
                    @else 
                        @php( $statusNavbar='punch_in' )
                        <a href=""  onclick="event.preventDefault();document.getElementById('attendance').submit();" class="btn btn-rounded btn-success btn-sm edit-add-modal-button js-ajax-ux-request reset-target-modal-form mt-2">Punch In</a>
                    @endif
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Attendances</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Attendances</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Attendances Activity List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive list-table-wrapper">
                        <table class="table table-hover dataTable no-footer" id="table" width="100%">
                            <thead>
                            <tr>
                                <th>Activity</th>
                                <th>Company - Branch</th>
                                <th>User Name</th>
                                <th>Last Edit By</th>
                                <th>Created At</th>
                                <th>Updated At</th>
                                <th class="noExport">User Id</th>
                                <th class="noExport">ID</th>
                            </tr>
                            </thead>
                            <tbody></tbody>
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

    var table = $('#table').DataTable({
        dom: 'Rltipr',
        buttons: [],
        select: true,
        aaSorting     : [[7, 'desc']],
        iDisplayLength: 25,
        stateSave     : true,
        responsive    : true,
        fixedHeader   : true,
        processing    : true,
        serverSide    : false,
        "bDestroy"    : true,
        pagingType    : "full_numbers",
        "bLengthChange": false,
        ajax          : {
            url     : '{{ url('admin/attendance/ajax/datatables_employee') }}',
            dataType: 'json'
        },
        columns       : [
            {data: 'activity', name: 'activity'},
            {data: 'branch', name: 'branch'},
            {data: 'username', name: 'username'},
            {data: 'editor', name: 'editor', visible: false},
            {data: 'created_at', name: 'created_at', visible: false},
            {data: 'updated_at', name: 'updated_at', visible: false},

            {data: 'search_username', name: 'search_username', visible: false},
            {data: 'id', name: 'id', visible: false}
        ],
    });
}

datatables();

</script>


    

@endsection
