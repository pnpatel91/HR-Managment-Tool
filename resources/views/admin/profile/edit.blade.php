@extends('admin.layouts.master')

@section('title', auth()->user()->name)

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    
                    <form method="post" id="file-upload-form" action="{{ route('admin.profile.updateProfileImage') }}" enctype="multipart/form-data">
                        @csrf
                        @method('put')
                        <div class="text-center mb-5">
                            <img class="profile-user-img img-fluid img-circle" src="{{ auth()->user()->getImageUrlAttribute() }}"
                                alt="User profile picture">

                            <input type="hidden" name="oldImage" value="{{ auth()->user()->image()->filename }}">
                            <input id="file-upload" type="file" class="form-control @error('image') is-invalid @enderror" name="image" value="{{ old('image') }}" required autocomplete="image" accept="image/gif, image/jpeg, image/jpg , image/png">
                            <label class="dropzone" for="file-upload" id="file-drag">
                                Select a file to upload
                                <br />OR
                                <br />Drag a file into this box
                                
                                <br /><br /><span id="file-upload-btn" class="button">Add a file</span>
                            </label>
                            <progress id="file-progress" value="0">
                                <span>0</span>%
                            </progress>
                            
                            <output for="file-upload" id="messages"></output>
                        </div>
                    </form>

                    <form method="post" id="profile_edit" action="{{ route('admin.profile.update') }}">
                        @csrf
                        @method('put')
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2"> <b>Name</b> </div>
                                <div class="col">
                                    <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ auth()->user()->name }}" required autocomplete="name" autofocus maxlength="200">
                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2"> <b>Email</b> </div>
                                <div class="col">
                                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ auth()->user()->email }}" required autocomplete="email">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2"> <b>Job Title</b> </div>
                                <div class="col">
                                    <input id="position" type="text" class="form-control @error('position') is-invalid @enderror" name="position" value="{{ auth()->user()->position }}" required autocomplete="position" autofocus maxlength="200">

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2"> <b>Date of birth</b> </div>
                                <div class="col">
                                    <input id="dateOfBirth" type="date" class="form-control @error('dateOfBirth') is-invalid @enderror" name="dateOfBirth" value="{{ auth()->user()->dateOfBirth }}" required>

                                    @error('dateOfBirth')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <div class="col-2"> <b>Biography</b> </div>
                                <div class="col">
                                    <textarea id="biography" name="biography" class="form-control @error('biography') is-invalid @enderror" required maxlength="5" autofocus>{{ auth()->user()->biography }}</textarea>

                                    @error('biography')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                        </li>
                    </ul>
                    <button type="submit" class="btn btn-primary"><b>Update</b></button>
                    <a href="{{ route('admin.profile.index') }}" class="btn btn-secondary"><b>Back</b></a>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>
<link rel="stylesheet" href="{{ asset('public/css/admin/profile.css') }}">
<script src="{{ asset('public/js/admin/profile.js') }}"></script>
<script type="text/javascript">
    // jQuery Validation
    $(function(){
        $('#file-upload-form').validate(
        {
            rules:{
              
            }
        }); //valdate end

        $('#profile_edit').validate(
        {
            rules:{
              
            }
        }); //valdate end
    }); //function end
</script>
@endsection