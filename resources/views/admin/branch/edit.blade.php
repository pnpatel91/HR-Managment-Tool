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
                    <h3 class="card-title">Edit Branch</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.branch.update', ['branch' => $branch->id]) }}" method="put"  id="popup-form" >
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ $branch->name }}" class="form-control" required autocomplete="name" autofocus maxlength="60">
                        </div>
                        <div class="form-group">
                            <label>Address</label>
                            <input type="text" name="address" value="{{ $branch->address }}" class="form-control" required autocomplete="address" maxlength="60">
                        </div>
                        <div class="form-group">
                            <label>State</label>
                            <input type="text" name="state" value="{{ $branch->state }}" class="form-control" required autocomplete="state" maxlength="60">
                        </div>
                        <div class="form-group">
                            <label>City</label>
                            <input type="text" name="city" value="{{ $branch->city }}" class="form-control" required autocomplete="city" maxlength="60">
                        </div>
                        <div class="form-group">
                            <label>Postcode</label>
                            <input type="text" name="postcode" value="{{ $branch->postcode }}" class="form-control" required autocomplete="postcode" maxlength="60">
                        </div>
                        <div class="form-group">
                            <label>Country</label>
                            <input type="text" name="country" value="{{ $branch->country }}" class="form-control" required autocomplete="country" maxlength="60">
                        </div>
                        <div class="form-group">
                            <label>Latitude <span class="tooltipfontsize" tooltip="Reload: your current location latitude" flow="right" onclick="getLocation(0)"><i class="fas fa-sync"></i></span></label></label>
                            <input type="text" name="latitude" id="latitude" value="{{ $branch->latitude }}" class="form-control" required autocomplete="latitude" maxlength="60" pattern="(\+|-)?(?:90(?:(?:\.0{1,6})?)|(?:[0-9]|[1-8][0-9])(?:(?:\.[0-9]{1,6})?))">
                        </div>
                        <div class="form-group">
                            <label>Longitude <span class="tooltipfontsize" tooltip="Reload: your current location longitude" flow="right" onclick="getLocation(1)"><i class="fas fa-sync"></i></span></label>
                            <input type="text" name="longitude" id="longitude" value="{{ $branch->longitude }}" class="form-control" required autocomplete="longitude" maxlength="60" pattern="^(\+|-)?(?:180(?:(?:\.0{1,6})?)|(?:[0-9]|[1-9][0-9]|1[0-7][0-9])(?:(?:\.[0-9]{1,6})?))$">
                        </div>
                        <div class="form-group">
                            <label>Radius <span class="tooltipfontsize" tooltip="Radius in metres" flow="right"><i class="fas fa-info-circle"></i></span></label>
                            <input type="number" value="{{ $branch->radius }}" step="any" name="radius" class="form-control" required autocomplete="radius">
                        </div>
                        <div class="form-group">
                            <label>Company</label>
                            <select class="form-control select2" id="company_id" name="company_id" required autocomplete="company_id">
                                @foreach ($company as $value)
                                    <option value="{{ $value->id }}" @if($branch->company_id == $value->id) selected @endif>{{ $value->name }}</option>
                                @endforeach
                            </select>
                            <label id="select2-error" class="error" for="select2"></label>
                        </div>
                        <div class="form-group">
                            <label>Users</label>
                            <select class="form-control select2" id="user_id" name="user_id[]" required autocomplete="user_id" multiple>
                                @foreach ($users as $user)
                                    <option value="{{ $user->id }}" @if(in_array($user->id, $branchUsers)) selected="selected" @endif >{{ $user->name }}</option>
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

    $("#company_id").select2({
      placeholder: "Select a company",
      allowClear: true
    });

    $("#user_id").select2({
      placeholder: "Select users",
      allowClear: true
    });

    // Get Latitude & Longitude 
    function getLocation(i) {
        if (navigator.geolocation) {
            if(i==0){
                navigator.geolocation.getCurrentPosition(
                // Success function
                showPositionLatitude, 
                // Error function
                null, 
                // Options. See MDN for details.
                {
                   enableHighAccuracy: true,
                   timeout: 5000,
                   maximumAge: 0
                });
            }else{
                navigator.geolocation.getCurrentPosition(
                // Success function
                showPositionLongitude, 
                // Error function
                null, 
                // Options. See MDN for details.
                {
                   enableHighAccuracy: true,
                   timeout: 5000,
                   maximumAge: 0
                });
            }
            
        } else { 
            x.innerHTML = "Geolocation is not supported by this browser.";
        }
    }

    function showPositionLatitude(position) {
        if (position.coords.latitude === undefined || position.coords.latitude === null) {
            document.getElementById("latitude").value= {{App\Http\Controllers\Admin\AttendanceController::get_location()->latitude}};
        }else{
            document.getElementById("latitude").value= position.coords.latitude;
        }

        //document.getElementById("latitude").value= {{App\Http\Controllers\Admin\AttendanceController::get_location()->latitude}};
    }

    function showPositionLongitude(position) {
        if (position.coords.longitude === undefined || position.coords.longitude === null) {
            document.getElementById("longitude").value= {{App\Http\Controllers\Admin\AttendanceController::get_location()->longitude}};
        }else{
            document.getElementById("longitude").value= position.coords.longitude;
        } 

        //document.getElementById("longitude").value= {{App\Http\Controllers\Admin\AttendanceController::get_location()->longitude}};
    }

</script>
@endsection