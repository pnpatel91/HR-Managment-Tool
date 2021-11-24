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
                    <h3 class="card-title">Edit Attendance</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.attendance.update', ['attendance' => $attendance->id]) }}" method="put"  id="popup-form" >
                        @csrf
                        @method('PUT')

                        <div class="form-group mt-4">
                            <!-- <label>Employee</label> -->
                            <div class="user-add-shedule-list">
                                <h2 class="table-avatar">
                                    <a href="" class="avatar" tooltip="{{$attendance->creator->name}}" flow="right"><img alt="" src="{{$attendance->creator->getImageUrlAttribute($attendance->creator->id)}}"></a>
                                    <a href="">{{$attendance->creator->name}} <span>{{$attendance->creator->departments[0]->name}}</span></a>
                                    <input type="hidden" name="user_id" id="user_id" value="{{$attendance->creator->id}}" >
                                </h2>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Company - Branch :</label> {{$attendance->branch->company->name .' - '. $attendance->branch->name}}
                            <input type="hidden" name="branch_id" id="branch_id" value="{{$attendance->branch_id}}" >
                        </div>

                        <div class="form-group" style="display:none;">
                            <label>Attendance Status</label>
                            <input type='text' class="form-control" id='ajax_status' name="status" value="{{str_replace('_', ' ', $attendance->status)}}" required readonly />
                        </div>
                        <div class="form-group">
                            <label>Punch In - Date & Time</label>
                            <input type='datetime' class="form-control" id='punch_in' name="punch_in" value="{{$attendance->attendance_at}}" required />
                        </div>

                        <div class="form-group">
                            <label>Punch Out - Date & Time</label>
                            <input type='datetime' class="form-control" id='punch_out' name="punch_out" value="@if($attendance->punch_out!=null) {{$attendance->punch_out->attendance_at}} @endif" required />
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

    $("#punch_in").datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
        step: 1,
    }).attr('readonly', 'readonly');

    $("#punch_out").datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
        step: 1,
    }).attr('readonly', 'readonly');
</script>
@endsection