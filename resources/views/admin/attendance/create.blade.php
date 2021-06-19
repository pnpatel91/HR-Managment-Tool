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
                    <h3 class="card-title">Create Attendance</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.attendance.store_admin') }}" method="post" id="popup-form" >
                        @csrf
                        <div class="form-group">
                            <label>Company - Branch</label>
                            <select class="form-control select2" id="branch_id" name="branch_id" required autocomplete="branch_id">
                                <option></option>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}">{{ $branch->company->name .' - '. $branch->name}}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Users</label>
                            <select class="form-control select2" id="ajax_user_id" name="user_id" required autocomplete="user_id">

                            </select>
                        </div>
                        <div class="form-group">
                            <label>Attendance Status</label>
                            <input type='text' class="form-control" id='ajax_status' name="status" value="" required readonly />
                        </div>
                        <div class="form-group">
                            <label>Date & Time</label>
                            <input type='datetime' class="form-control" id='datetimepicker4' name="attendance_at" value="" required />
                        </div>

                        <button type="submit" class="btn btn-primary">Create</button>
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
        $("#ajax_user_id option").remove();
        
        var id = $(this).val();
        
        $.ajax({
          url : '{{ route('admin.attendance.ajax.user') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "id": id
            },
          type: 'post',
          dataType: 'json',
          success: function( result )
          {
            $('#ajax_user_id').append($('<option>', {value:'', text:''}));
            $.each( result, function(k, v) {
                $('#ajax_user_id').append($('<option>', {value:k, text:v}));
                $("#pageloader").hide();
            });
          }
        });
    });

    $('#ajax_user_id').change(function(){
        $("#pageloader").fadeIn();
        $('#ajax_status').val('');
        
        $.ajax({
          url : '{{ route('admin.attendance.ajax.status') }}',
          data: {
            "_token": "{{ csrf_token() }}",
            "id": $(this).val()
            },
          type: 'post',
          dataType: 'json',
          success: function( result )
          {
               $('#ajax_status').val(result.status);
               $("#pageloader").hide();
          },
        });
    });

    $("#datetimepicker4").datetimepicker({
        dateFormat: 'yy-mm-dd',
        timeFormat: 'HH:mm:ss',
    }).attr('readonly', 'readonly');
</script>
@endsection