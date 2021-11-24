@extends('admin.layouts.popup')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="error_message">
            @include('message.alert')
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h3 class="card-title">Create Rota</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.rota.store_bulk') }}" method="put"  id="popup-form-rota" >
                        @csrf
                        @method('PUT')

                        <div class="form-group">
                            <label>Rota Template</label>
                            <select class="form-control select2" id="rota_template" name="rota_template" required autocomplete="rota_template">
                                <option></option>
                                @foreach ($rota_templates as $rota_template)
                                    <option value="{{ $rota_template->id }}">{{$rota_template->name}}</option>
                                @endforeach
                            </select>
                        </div>

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
                            <label>Employee</label>
                            <select class="form-control select2" id="employee_id" name="employee_id[]" required autocomplete="employee_id" multiple>
                                <option></option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Start Date</label>
                            <input type='text' class="form-control datepicker" id='start_date' name="start_date" value="{{date("m/d/Y", strtotime('tomorrow'))}}" onchange="end_date_value()" required min={{date('Y-m-d')}} readonly />
                        </div>

                        <div class="form-group">
                            <label>End Date</label>
                            <input type='text' class="form-control" id='end_date' name="end_date" value="" required min={{date('Y-m-d')}} readonly />
                        </div>

                        <div class="form-group">
                            <label>Start At</label>
                            <input type='time' class="form-control" id='start_at' name="start_at" value="" required />
                        </div>

                        <div class="form-group">
                            <label>Max Start At</label>
                            <input type='time' class="form-control" id='max_start_at' name="max_start_at" value="" required />
                        </div>

                        <div class="form-group">
                            <label>End At</label>
                            <input type='time' class="form-control" id='end_at' name="end_at" value="" required />
                        </div>

                        <div class="form-group">
                            <label>Break Time <span class="tooltipfontsize" tooltip="break time in minutes" flow="right"><i class="fas fa-info-circle"></i></span></label>
                            <input type='number' class="form-control" id='break_time' name="break_time" value="" min="0" max="120" oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" required />
                        </div>

                        <div class="form-group">
                            <label>Break Start At</label>
                            <input type='time' class="form-control" id='break_start_at' name="break_start_at" value=""  required />
                        </div>

                        <div class="form-group">
                            <label>Types</label>
                            <select class="form-control select2" id="types" name="types" required autocomplete="types" onchange="end_date_value()">
                                <option></option>
                                @foreach ($types as $types)
                                    <option value="{{ $types }}">{{ $types }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="day_list_hide_show">
                            <label>Day List</label>
                            <select class="form-control select2" id="day_list" name="day_list[]" autocomplete="day_list" multiple>
                                <option></option>
                                @foreach ($day_list as $day_list)
                                    <option value="{{ $day_list }}">{{ $day_list }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Remotely Work</label>
                            <select class="form-control select2" id="remotely_work" name="remotely_work" required autocomplete="remotely_work">
                                <option></option>
                                @foreach ($remotely_work as $remotely_work)
                                    <option value="{{ $remotely_work }}">{{ $remotely_work }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Over Time</label>
                            <select class="form-control select2" id="over_time" name="over_time" required autocomplete="over_time">
                                <option></option>
                                @foreach ($over_time as $over_time)
                                    <option value="{{ $over_time }}">{{ $over_time }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Notes</label>
                            <textarea id="notes" name="notes" class="form-control" required maxlength="5" autofocus></textarea>  
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
        $('#popup-form-rota').validate(
        {
            rules:{
              
            }
        }); //valdate end
    }); //function end

    $("#day_list").select2({
      placeholder: "Select days",
      allowClear: true
    });

    $('#types').change(function(){
        
        var types = $(this).val();
        if(types!='Day'){
            $('#day_list_hide_show').show();
            $('#day_list').prop('required',true);
        }else{
            $('#day_list_hide_show').hide();
            $('#day_list').prop('required',false);
        }
    });

    $('.datepicker').datepicker({
        format: 'mm/dd/yyyy',
        startDate: '0d'
    });

    $("#branch_id").select2({
      placeholder: "select a company - branch",
      allowClear: false
    });

    $("#employee_id").select2({
      placeholder: "select multiple employee",
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

    $('#rota_template').change(function(){
        $("#pageloader").fadeIn();
        var id = $(this).val();

        if(id==''){
            $("#pageloader").hide();
        }else{
            $.ajax({
              url : '{{ route('admin.rota_template.ajax.get_rota_template') }}',
              data: {
                "_token": "{{ csrf_token() }}",
                "id": id
                },
              type: 'get',
              dataType: 'json',
              success: function( result )
              {
                $('#start_at').val(result.start_at);
                $('#max_start_at').val(result.max_start_at);
                $('#end_at').val(result.end_at);
                $('#break_time').val(result.break_time);
                $('#break_start_at').val(result.break_start_at);
                $('#types').val(result.types);
                var day_list = result.day_list.replace(/\s+/g, '').replace(/[\[\]"]+/g,'').split(",");
                console.log(day_list);
                $('#day_list').val(day_list).change();
                $('#remotely_work').val(result.remotely_work);
                $('#over_time').val(result.over_time);
                end_date_value();
                $("#pageloader").hide();
              }
            });   
        }
        
    });

    function end_date_value() {
        var start_date = $('#start_date').val();
        var types = $('#types').val();
        if(types=='Month'){
            var d = new Date(start_date);
            d.setMonth(d.getMonth() + + 1); 
        }else if(types=='Week'){
            var d = new Date(start_date);
            d.setDate(d.getDate() + + 6); 
        }else{
            var d = new Date(start_date);
        }
        
        month = '' + (d.getMonth() + 1),
        day = '' + d.getDate(),
        year = d.getFullYear();

        if (month.length < 2) month = '0' + month;
        if (day.length < 2) day = '0' + day;

        $('#end_date').val([ month, day, year].join('/'));
    }

    end_date_value();

    //CKEDITOR for notes
    CKEDITOR.replace( 'notes' );

    
</script>
@endsection