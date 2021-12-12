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
                    <h3 class="card-title">Edit Role</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <form action="{{ route('admin.role.update', ['role' => $role->id]) }}" method="POST"  id="popup-formUserRole" >
                        @csrf
                        @method('PUT')
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" value="{{ $role->name }}"  required autocomplete="name" autofocus maxlength="200" {{ $role->isDisabled() }}
                                class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Permission: </label> &nbsp; &nbsp;<input type="checkbox" id="checkbox_permission" > &nbsp;Select All
                        </div>
                        <div class="row">
                            @foreach ($permissions as $permission)
                            <div class="col-md-3">
                                <div class="form-group form-check">
                                    <input type="checkbox" name="permission[]" value="{{ $permission->id }}"
                                        id="{{ Str::slug($permission->name) }}"
                                        {{ $role->isDisabled() }}
                                        {{ $role->isChecked($permission) }}
                                        class="form-check-input">
                                    <label class="form-check-label {{ $permission->isDeleteLabel() }}"
                                        for="{{ Str::slug($permission->name) }}">
                                        {{ Str::title($permission->display_name) }}
                                    </label>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @unless ($role->isSuperAdmin())
                        <button type="submit" class="btn btn-primary">Update</button>
                        @endunless
                        <a href="" class="btn btn-secondary" data-dismiss="modal">Close</a>
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

    $("#checkbox_permission").click(function(){
        if($("#checkbox_permission").is(':checked') ){
            $('input:checkbox').prop('checked',true);
        }else{
            $('input:checkbox').prop('checked',false);
        }
    });
</script>
@endsection