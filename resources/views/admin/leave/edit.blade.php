@extends('admin.layouts.popup')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            @include('message.alert')
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="card-title">Edit leave</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.leave.update', ['leave' => $leave->id]) }}" method="put"  id="popup-form" >
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Company - Branch</label>
                            <select class="form-control select2" id="branch_id" name="branch_id" required autocomplete="branch_id">
                                <option></option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if($leave->branch_id==$branch->id) selected @endif>{{ $branch->company->name .' - '. $branch->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Employee</label>
                            <select class="form-control select2" id="employee_id" name="employee_id" required autocomplete="employee_id">
                                <option></option>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if($leave->employee_id==$user->id) selected @endif>{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Approved By</label>
                            <select class="form-control select2" id="approved_by" name="approved_by" required autocomplete="approved_by">
                                <option></option>
                                @foreach ($approvers as $approver)
                                    <option value="{{ $approver->id }}" @if($leave->approved_by==$approver->id) selected @endif>{{ $approver->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Leave Type</label>
                            <select class="form-control select2" id="leave_type" name="leave_type" required autocomplete="leave_type">
                                <option></option>
                                @foreach ($leave_types as $leave_type)
                                    <option value="{{ $leave_type }}" @if($leave->leave_type==$leave_type) selected @endif>{{ $leave_type }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input class="form-control" type="text" name="leave_date" id="leave_date" value="{{$leave->start_at.' - '.$leave->end_at}}" required />  
                        </div>
                        <div class="form-group">
                            <label>Days</label>
                            <input type='text' class="form-control" id='days' name="days" value="{{$leave->leave_days}}" required readonly />
                        </div>
                        <div class="form-group"  @if($leave->leave_days!=1) style="display:none;" @endif  id="half_day_hide_show">
                            <label>Full Day/Half Day</label>
                            <select class="form-control select2" id="half_day" name="half_day" required autocomplete="half_day">
                                <option></option>
                                @foreach ($half_day as $half_day)
                                    <option value="{{ $half_day }}" @if($leave->half_day==$half_day) selected @endif>{{ $half_day }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Reason</label>
                            <input class="form-control" type="text" name="reason" id="reason" value="{{$leave->reason}}" />  
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea id="description" name="description" class="form-control" required maxlength="5" autofocus>{{$leave->description}}</textarea>  
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select class="form-control select2" id="status" name="status" required autocomplete="status">
                                <option></option>
                                @foreach ($status as $status)
                                    <option value="{{ $status }}" @if($leave->status==$status) selected @endif>{{ $status }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update</button>
                        <a href="" class="btn btn-secondary"  data-dismiss="modal">Close</a>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    // jQuery Validation
    $(function(){
        $('#popup-form').validate(
        {
            rules:{
              
            }
        }); //valdate end
    }); //function end

    $("#branch_id").select2({
      placeholder: "select a company - branch",
      allowClear: false
    });

    $("#ajax_user_id").select2({
      placeholder: "select a user",
      allowClear: false
    });

    
    $('#branch_id').change(function(){
        $("#pageloader").fadeIn();
        $("#employee_id option").remove();
        
        var id = $(this).val();
        
        $.ajax({
          url : '{{ route('admin.user.ajax.users') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "id": id
            },
          type: 'post',
          dataType: 'json',
          success: function( result )
          {
            $('#employee_id').append($('<option>', {value:'', text:''}));
            $.each( result, function(k, v) {
                $('#employee_id').append($('<option>', {value:k, text:v}));
                $("#pageloader").hide();
            });
          }
        });
    });

    $("#user_id").select2({
      placeholder: "select a approver",
      allowClear: false
    });

    

    var date = new Date();
    var currentMonth = date.getMonth();
    var currentDate = date.getDate();
    var currentYear = date.getFullYear();
    var start_at = "{{$leave->start_at}}";
    var end_at = "{{$leave->end_at}}";
    $('input[name="leave_date"]').daterangepicker({
        minDate: new Date(currentYear, currentMonth, currentDate)
        , startDate: moment(start_at)
        , endDate: moment(end_at)
        ,"autoApply": true
    }).on('apply.daterangepicker', function(ev, picker) {debugger
                var start = moment(picker.startDate.format('YYYY-MM-DD'));
                var end   = moment(picker.endDate.format('YYYY-MM-DD'));
                var diff = end.diff(start, 'days'); // returns correct number
                diff= parseInt(diff) + 1;
                $('#days').val(diff);
                if(diff==1){
                    $('#half_day_hide_show').show();
                }else{
                    $('#half_day_hide_show').hide();
                }

    });


    //CKEDITOR for description
    CKEDITOR.replace( 'description' );</script>
@endsection