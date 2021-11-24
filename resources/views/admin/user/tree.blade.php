@extends('admin.layouts.master')

@section('content')

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12" id="message">
            @include('message.alert')
        </div>

        <div class="row col-sm-12 page-titles">
            <div class="col-lg-5 p-b-9 align-self-center text-left  " id="list-page-actions-container">
                <div id="list-page-actions">
                    <!--ADD NEW ITEM-->
                    @can('create user')
                    <a href="{{ route('admin.user.create') }}" class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="popup-modal-buttonUserRole">
                        <span tooltip="Create new team member." flow="right"><i class="fas fa-plus"></i></span>
                    </a>
                    @endcan
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Team Members</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Team Members</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Team Members List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body ">
                    <div class="verticals twelve">
                        <section class="management-tree">
                            <div class="mgt-container">
                                <div class="mgt-wrapper">
                                    
                                    <div class="mgt-item">
                                        @foreach($users as $user)
                                        <div @if(count($user->allChildren)>0) class="mgt-item-parent" @else class="mgt-item-lastparent" @endif>
                                            <div class="person">
                                                <img src="{{$user->getImageUrlAttribute($user->id)}}" alt="{{$user->name}}">
                                                <p class="name">{{$user->name}} / {{$user->position}}</p>
                                            </div>
                                        </div>
                                        @if(count($user->allChildren)>0)
                                        <div class="mgt-item-children">
                                            @foreach($user->allChildren as $child_user)
                                                @include('admin.user.child_user', ['child_user' => $child_user])
                                            @endforeach
                                        </div>
                                        @endif
                                        @endforeach
                                    </div>
                                    
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>

@endsection