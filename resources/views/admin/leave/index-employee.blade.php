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
                    @can('create leave - employee')
                    <a href="{{ route('admin.leave-employee.create') }}" class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="popup-modal-button">
                        <span tooltip="Create new leave." flow="right"><i class="fas fa-plus"></i></span>
                    </a>
                    @endcan
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Leaves</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Leaves</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Leaves List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive list-table-wrapper">
                        <div class="form-row" id="search"> 

                            <div class="form-group col-md-3">
                                <span>search by leave date</span>
                                <input class="search-area form-control" type="text" name="datefilter" id="daterange" value="" />  
                            </div>

                            <div class="mt-4 form-group col-md-2">
                                <button type="button  form-control" id="Clear_Filters" class="btn btn-primary"><i class="fas fa-sync"></i></button>
                            </div>

                        </div>
                        <table class="table table-hover dataTable no-footer" id="table" width="100%">
                            <thead>
                            <tr>
                                <th>Leave Type</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Status</th>
                                <th>Approved by</th>
                                <th>Company - Branch</th>
                                <th class="noExport">Action</th>
                                <th class="noExport">User Id</th>
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
        aaSorting     : [[1, 'asc']],
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
            url     : '{{ url('admin/leave-employee/ajax/data') }}',
            dataType: 'json'
        },
        columns       : [
            {data: 'leave_type', name: 'leave_type'},
            {data: 'start_at', name: 'start_at'},
            {data: 'end_at', name: 'end_at'},
            {data: 'leave_status', name: 'leave_status'},
            {data: 'leave_approved_by', name: 'leave_approved_by'},
            {data: 'branch', name: 'branch'},
            {data: 'action', name: 'action', orderable: false, searchable: false,
                fnCreatedCell: function (nTd, sData, oData, iRow, iCol) {
                    $("a", nTd).tooltip({container: 'body'});
                }
            },

            {data: 'search_username', name: 'search_username', visible: false}
        ],
    });

    // Date range vars
    minDateFilter = "";
    maxDateFilter = "";

    $('input[name="datefilter"]').on('apply.daterangepicker', function(ev, picker) {
            $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
            minDateFilter = Date.parse(picker.startDate);
            maxDateFilter = Date.parse(picker.endDate);

            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                var mindate = Date.parse(data[1]);
                var maxdate = Date.parse(data[2]);

                if (
                (isNaN(minDateFilter) && isNaN(maxDateFilter)) ||
                (isNaN(minDateFilter) && mindate <= maxDateFilter) ||
                (minDateFilter <= maxdate && isNaN(maxDateFilter)) ||
                (minDateFilter <= maxdate && mindate <= maxDateFilter)
                ) {
                    return true;
                }
                
                return false;
            });
            table.draw();

            if(this.value == ''){
                table.search( '' ).columns().search( '' ).draw();
            }
    });

    $('input[name="datefilter"]').on('cancel.daterangepicker', function(ev, picker) {
          $(this).val('');
          location.reload();
    });

    $('#Clear_Filters').click(function () {
        $('#Clear_Filters').attr("disabled", true);
        $('input[name="datefilter"]').val('');
        location.reload();
    });
    
}

datatables();



$('input[name="datefilter"]').daterangepicker({
  autoUpdateInput: false,   
});

</script>


    

@endsection
