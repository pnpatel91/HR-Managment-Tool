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
                    <h3 class="card-title">Edit Holiday</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.holiday.update', ['holiday' => $holiday->id]) }}" method="put"  id="popup-form" >
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ $holiday->name }}" class="form-control" required autocomplete="name" autofocus maxlength="60">
                        </div>
                        <div class="form-group">
                            <label>Date</label>
                            <input type="date" name="holiday_date" value="{{ $holiday->holiday_date }}" class="form-control" required autocomplete="holiday_date">
                        </div>
                        <div class="form-group">
                            <label>Company-Branch</label>
                            <select class="form-control select2" id="branch_id" name="branch_id[]" required autocomplete="branch_id" multiple>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if(in_array($branch->id, $holidayBranches)) selected="selected" @endif >{{ $branch->company->name .' - '. $branch->name}}</option>
                                @endforeach
                            </select>
                            <label id="select2-error" class="error" for="select2"></label>
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
      placeholder: "Select a company - branch",
      allowClear: true
    });
</script>
@endsection