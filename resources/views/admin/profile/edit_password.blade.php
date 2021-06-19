@extends('admin.layouts.master')

@section('title', auth()->user()->name)

@section('content')

<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @include('message.alert')
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <div class="text-center mb-5">
                        <img class="profile-user-img img-fluid img-circle" src="{{ auth()->user()->getImageUrlAttribute() }}"
                                alt="User profile picture">
                    </div>

                    <form method="post" action="{{ route('admin.profile.update.password') }}">
                        @csrf
                        <ul class="list-group list-group-unbordered mb-3">
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col"> <b>Old Password</b> </div>
                                    <div class="col">
                                        <input type="password" name="old_password" value="{{ old('old_password') }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </li>
                            <li class="list-group-item">
                                <div class="row">
                                    <div class="col"> <b>New Password</b> </div>
                                    <div class="col">
                                        <input type="password" name="new_password" value="{{ old('new_password') }}"
                                            class="form-control">
                                    </div>
                                </div>
                            </li>
                        </ul>
                        <button type="submit" class="btn btn-primary"><b>Change</b></button>
                        <a href="{{ route('admin.profile.index') }}" class="btn btn-secondary"><b>Back</b></a>
                    </form>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>

@endsection