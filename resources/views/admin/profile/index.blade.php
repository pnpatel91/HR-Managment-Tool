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

                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col"> <b>Name</b> </div>
                                <div class="col"> {{  auth()->user()->name  }} </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col"> <b>Email</b> </div>
                                <div class="col"> {{  auth()->user()->email  }} </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col"> <b>Date of birth</b> </div>
                                <div class="col"> {{  auth()->user()->dateOfBirth  }} </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="row">
                                <div class="col"> <b>Biography</b> </div>
                                <div class="col"> {!!  auth()->user()->biography  !!} </div>
                            </div>
                        </li>

                        <li class="list-group-item">
                            <div class="row">
                                <div class="col"> <b>Company Name - Branch Name</b> </div>
                                <div class="col"> 
                                    <ul>
                                        @foreach(auth()->user()->branches as $branch)
                                        <li>{{ $branch->company->name .' - '. $branch->name}}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </div>
                        </li>

                    </ul>

                    <a href="{{ route('admin.profile.edit') }}" class="btn btn-primary"><b>Edit</b></a>
                    <a href="{{ route('admin.profile.edit.password') }}" class="btn btn-warning"><b>Change Password</b></a>
                </div>

                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>

@endsection