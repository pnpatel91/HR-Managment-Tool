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
                    <h3 class="card-title">Edit User</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.user.update', ['user' => $user->id]) }}" method="put"  id="popup-formUserRole" >
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ $user->name }}" class="form-control" required autocomplete="name" autofocus maxlength="200">
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ $user->email }}" class="form-control" required autocomplete="email">
                        </div>
                        <div class="form-group">
                            <label>Password: <i class="text-info">(Default: password)</i></label>
                            <input type="password" name="password" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select class="form-control" name="role">
                                @foreach ($roles as $id => $name)
                                <option value="{{ $id }}" 
                                {{ $name === $userRole ? 'selected' : null }}
                                >{{ $name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Job Title</label>
                            <input type="text" name="position" class="form-control" value="{{ $user->position }}" required autocomplete="position" autofocus maxlength="200">
                        </div>
                        <div class="form-group">
                            <label>Company-Branch</label>
                            <select class="form-control select2" id="select2" name="branch_id[]" required autocomplete="branch_id" multiple>
                                @foreach ($branches as $branch)
                                    <option value="{{ $branch->id }}" @if(in_array($branch->id, $userBranches)) selected="selected" @endif >{{ $branch->company->name .' - '. $branch->name}}</option>
                                @endforeach
                            </select>
                            <label id="select2-error" class="error" for="select2"></label>
                        </div>
                        <div class="form-group">
                            <label>Department</label>
                            <select class="form-control select2" id="department_id" name="department_id[]" required autocomplete="department_id" multiple>
                                @foreach ($departments as $department)
                                    <option value="{{ $department->id }}" @if(in_array($department->id, $userDepartments)) selected="selected" @endif >{{$department->name}}</option>
                                @endforeach
                            </select>
                            <label id="select2-error" class="error" for="select2"></label>
                        </div>
                        @if($userRole!='superadmin')
                        <div class="form-group">
                            <label>Parent User</label>
                            <select class="form-control select2" id="parent_id" name="parent_id" required autocomplete="parent_id">
                                @foreach ($parents as $parent)
                                    <option value="{{ $parent->id }}" @if($parent->id==$user->parent_id) selected="selected" @endif >{{$parent->name}}</option>
                                @endforeach
                            </select>
                            <label id="select2-error" class="error" for="select2"></label>
                        </div>
                        @else
                            <input type="hidden" name="parent_id" class="form-control" value="" required autocomplete="parent_id" autofocus maxlength="200">
                        @endif

                         <div class="form-group">
                            <label>Remote Employee</label>
                            <select class="form-control select2" id="remote_employee" name="remote_employee" required autocomplete="remote_employee">
                                <option value="Yes" @if($user->remote_employee=='Yes') selected="selected" @endif >Yes</option>
                                <option value="No" @if($user->remote_employee=='No') selected="selected" @endif >No</option>
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
        $('#popup-formUserRole').validate(
        {
            rules:{
              
            }
        }); //valdate end
    }); //function end

    $("#select2").select2({
      placeholder: "Select a company - branch",
      allowClear: true
    });

    $("#department_id").select2({
      placeholder: "Select a department",
      allowClear: true
    });

    $("#parent_id").select2({
      placeholder: "Select a parent user",
      allowClear: true
    });

    $("#remote_employee").select2({
      placeholder: "Select a remote employee",
      allowClear: true
    });
</script>
@endsection