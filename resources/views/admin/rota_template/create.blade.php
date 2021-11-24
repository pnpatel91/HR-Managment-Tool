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
                    <h3 class="card-title">Create Rota Template</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.rota_template.store') }}" method="post" id="popup-form" >
                        @csrf
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control" required autocomplete="name" autofocus maxlength="60">
                        </div>
                        <div class="form-group">
                            <label>Start At</label>
                            <input type='time' class="form-control" id='start_at' name="start_at" value="09:00" required />
                        </div>

                        <div class="form-group">
                            <label>Max Start At</label>
                            <input type='time' class="form-control" id='max_start_at' name="max_start_at" value="09:10" required />
                        </div>

                        <div class="form-group">
                            <label>End At</label>
                            <input type='time' class="form-control" id='end_at' name="end_at" value="18:00" required />
                        </div>

                        <div class="form-group">
                            <label>Break Time <span class="tooltipfontsize" tooltip="break time in minutes" flow="right"><i class="fas fa-info-circle"></i></span></label>
                            <input type='number' class="form-control" id='break_time' name="break_time" value="30" min="0" max="120" oninput="this.value = !!this.value && Math.abs(this.value) >= 0 ? Math.abs(this.value) : null" required />
                        </div>
                        <div class="form-group">
                            <label>Break Start At</label>
                            <input type='time' class="form-control" id='break_start_at' name="break_start_at" value="02:00" required />
                        </div>


                        <div class="form-group">
                            <label>Types</label>
                            <select class="form-control select2" id="types" name="types" required autocomplete="types">
                                <option></option>
                                @foreach ($types as $types)
                                    <option value="{{ $types }}">{{ $types }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group" id="day_list_hide_show">
                            <label>Day List</label>
                            <select class="form-control select2" id="day_list" name="day_list[]" required autocomplete="day_list" multiple>
                                <option></option>
                                @foreach ($day_list as $day_list)
                                    <option value="{{ $day_list }}" @if($day_list!='Sunday') selected @endif >{{ $day_list }}</option>
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

</script>

@endsection

