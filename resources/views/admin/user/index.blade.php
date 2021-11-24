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
                    <div class="table-responsive list-table-wrapper">
                        <table class="table table-hover" id="datatableUserRole">
                            <thead>
                                <tr>
                                    <th class="col-4">Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th class="col-3">Company - Branch</th>
                                    <th>Department</th>
                                    <th class="col-2">Date</th>
                                    @canany(['edit user', 'delete user'])
                                    <th>Actions</th>
                                    @endcanany
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                <tr>
                                    <td class="col-4"><img src="{{ $user->getImageUrlAttribute($user->id) }}" alt="Admin" class="profile-user-img-small img-circle"> {{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ $user->getRoleNames()->first() }}</td>
                                    <td class="col-3">@foreach($user->branches as $branch)<span class="selection_choice">{{$branch->company->name}} - {{$branch->name}}</span>@endforeach</td>
                                    <td>@foreach($user->departments as $department)<span class="selection_choice">{{ $department->name }}</span>@endforeach</td>
                                    <td class="col-2">{{ $user->date }}</td>
                                    <td  class="col-2">
                                        @can('edit user')
                                        @if($user->isDisabled()!='disabled')
                                            <a href="{{ route('admin.user.edit', ['user' => $user->id]) }}" 
                                                class="btn btn-success btn-sm float-left mr-3"  id="popup-modal-buttonUserRole">
                                                <span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span>
                                            </a>
                                        @else
                                            <button type="submit" class="btn btn-success btn-sm float-left mr-3" disabled><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></button>
                                        @endif
                                        @endcan 
                                        @can('delete user')
                                        @if(!$user->hasRole('superadmin'))
                                        <form method="post" class="float-left delete-formUserRole"
                                            action="{{ route('admin.user.destroy', ['user' => $user->id ]) }}">
                                            @csrf
                                            @method('delete')
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <span tooltip="Delete" flow="up"><i class="fas fa-trash-alt"></i></span>
                                            </button>
                                        </form>
                                        @else
                                            <button type="submit" class="btn btn-danger btn-sm" disabled><span tooltip="Delete" flow="up"><i class="fas fa-trash-alt"></i></span></button>
                                        @endif
                                        @endcan
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">There is no user.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
    </div>
</div>

@endsection