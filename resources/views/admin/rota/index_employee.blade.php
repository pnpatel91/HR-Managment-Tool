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
                    @can('create rota')
                    <a href="{{ route('admin.rota.create') }}" class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="popup-modal-button">
                        <span tooltip="create new rota." flow="right"><i class="fas fa-plus"></i></span>
                    </a>
                    @endcan
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Rota</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Rota</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Rota List</h3>
                    <div class="text-right">
                        <button type="button  form-control" id="viewgrid" class="btn btn-primary active" onclick="changeview(1)"><i class="fa fa-bars"></i></button> 
                        <button type="button  form-control" id="viewlist" class="btn btn-primary" onclick="changeview(0)"><i class="fa fa-th"></i></button>
                    </div>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="col-md-12">
                        
                        <div class="form-row pt-3" id="search"> 

                            <div class="form-group col-md-3">
                                <span>Search by rota date</span>
                                <input class="search-area form-control" type="text" name="datefilter" id="daterange" value="" />  
                            </div>

                            <div class="mt-4 form-group col-md-3">
                                <button type="button  form-control" id="search" class="btn btn-primary" onclick="load_table_data()">Search</button>
                            </div>
                            <div class="mt-4 form-group col-md-6 text-right">
                                <button type="button  form-control" id="search" class="btn btn-primary" onclick="date_range_change(0)"><</button>
                                <button type="button  form-control" id="search" class="btn btn-primary" onclick="date_range_change(1)">></button> 
                            </div>
                        </div>
                        <div class="table-responsive" id="ajax_table_data">
                            <table class="table table-hover dataTable no-footer" id="table" width="100%">
                                <thead>
                                <tr>
                                    <th>Start Date</th>
                                    <th>Start Time</th>
                                    <th>End Date</th>
                                    <th>End Time</th>
                                    <th class="noExport">Action</th>
                                </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>


                        <div id='full_calendar_events' style="display:none"></div>

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
        'scrollX': 'true',
        dom: 'Rltipr',
        "bLengthChange": false,
    });
}

$('input[name="datefilter"]').daterangepicker({
        ranges: {
            'Today': [moment(), moment()],
            'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            'Last 7 Days': [moment().subtract(6, 'days'), moment()],
            'This Week': [moment().startOf('isoWeek'), moment()],
            'Last 30 Days': [moment().subtract(29, 'days'), moment()],
            'This Month': [moment().startOf('month'), moment().endOf('month')],
            'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        "startDate": moment(),
        "endDate": moment().add(6, 'days')
    }, function (start, end, label) {   
});

function load_table_data() {
    var datefilter = $('#daterange').val();
    var date = datefilter.split(" - ");
    $("#pageloader").fadeIn();
    $('#ajax_table_data').html('');
    $.ajax({
        url:  '{{ route('admin.rota.ajax.table_employee') }}',
        dataType: 'html',
        data: {
            "_token": "{{ csrf_token() }}",
            "startDate": date[0],
            "endDate": date[1]
        },
        success: function(response) {
            $('#ajax_table_data').html(response);
            datatables();
            $("#pageloader").hide();
        },
        error: function (data){
                console.log(data);
        }
    });
}
load_table_data();
function date_range_change(i) {
    var datefilter = $('#daterange').val();
    var date = datefilter.split(" - ");
    var start_date = date[0];
    var end_date = date[1];

    if(i==1){
        var new_date_range = end_date +' - '+ moment(end_date).add(6,'days').format("MM/DD/YYYY"); 
        $('#daterange').val(new_date_range);
    }else{
        var new_date_range = moment(start_date).add(-6,'days').format("MM/DD/YYYY") +' - '+ start_date; 
        $('#daterange').val(new_date_range);
    }

    load_table_data();
}

$(document).ready(function () {
    $(document).on('submit','#popup-form-rota',function(e){
        e.preventDefault();
        var url = $(this).attr('action');
        $('input[type="submit"]').attr('disabled','disabled');
        $("#pageloader").fadeIn();
        $.ajax({
            method: "POST",
            url: url,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: $(this).serialize(),
            success: function(message){
                if(typeof(message.success) != "undefined" && message.success !== null) {
                    $("#popup-modal").modal('hide');
                    var messageHtml = '<div class="alert alert-success alert-dismissible fade show" role="alert"><strong>Success: </strong> '+ message.success +' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    $('#message').html(messageHtml);
                    $("#navbar_current_Status").load(location.href+" #navbar_current_Status>*","");// after new attendance create. reload navbar_current_Status div
                    setTimeout(function() {   //calls click event after a certain time
                        load_table_data();
                        getLocationNavbar(); // after new attendance create. reload navbar_current_Status div
                        $('input[type="submit"]').removeAttr('disabled');
                        $("#pageloader").hide();
                    }, 1000);
                } else if(typeof(message.error) != "undefined" && message.error !== null){
                    var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+message.error+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                    $('#error_message').html(messageHtml);
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        $("#pageloader").hide();
                    }, 1000);
                }
            },
            error: function(message){
                if(typeof(message.responseJSON.errors) != "undefined" && message.responseJSON.errors !== null){
                    var errors = message.responseJSON.errors;
                    $.each(errors, function (key, val) {
                        var messageHtml = '<div class="alert alert-danger alert-dismissible fade show" role="alert"><strong>Error: </strong> '+val[0]+' <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                        $('#error_message').append(messageHtml);
                    });
                    
                    setTimeout(function() {   //calls click event after a certain time
                        datatables();
                        $("#pageloader").hide();
                    }, 1000);
                }
            },
        });
    }); 

    
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var calendar = $('#full_calendar_events').fullCalendar({
        events: "{{ url('admin/rota/ajax/calendarRota') }}",
        displayEventTime: true,
        eventRender: function (event, element, view) {
            if (event.allDay === 'true') {
                event.allDay = true;
            } else {
                event.allDay = false;
            }

            if(event.break_start_time!=''){
                var break_time = moment.utc(event.break_start_time,'hh:mm').format('hh:mm')+' to '+moment.utc(event.break_start_time,'hh:mm').add(event.break_time,'minutes').format('hh:mm');
            }else{
                var break_time = event.break_time+' minutes';
            }

            if(event.remotely_work=='No'){
                var branch = event.branch.name +' - '+ event.branch.address +', '+ event.branch.city +', '+ event.branch.state +', '+ event.branch.postcode +', '+ event.branch.country;
            }else{
                var branch = 'Remotely Work';
            }

            return $('<div class="user-add-shedule-list"><h2><div class="anchertag" style="border:2px dashed #1eb53a;width: 120px;margin: 0 0 0 10px;""><span class="username-info">'+ moment( event.start, true).format("YYYY-MM-DD") +' '+ event.start_time +' To</span><span class="username-info m-b-10"></span><span class="username-info m-b-10">' + event.end_date +' '+ event.end_time +'</span><br><span class="username-info m-b-10">Brake Time : '+ break_time +'</span><br><span class="username-info m-b-10">'+branch+'</span> </div></h2></div>');
        },
    });
});

function changeview(view) {
    if(view==0){
        $('#search').hide();
        $('#ajax_table_data').hide();
        $('#full_calendar_events').show();
        $("#viewlist").addClass("active");
        $("#viewgrid").removeClass("active")
    }else{
        $('#search').show();
        $('#ajax_table_data').show();
        $('#full_calendar_events').hide();
        $("#viewgrid").addClass("active");
        $("#viewlist").removeClass("active")
    }
}
</script>
@endsection


