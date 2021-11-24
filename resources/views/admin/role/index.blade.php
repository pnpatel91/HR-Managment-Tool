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
                    @can('create role')
                    <a href="{{ route('admin.role.create') }}" class="btn btn-danger btn-add-circle edit-add-modal-button js-ajax-ux-request reset-target-modal-form" id="popup-modal-buttonUserRole">
                        <span tooltip="Create new role." flow="right"><i class="fas fa-plus"></i></span>
                    </a>
                    @endcan
                    <!--ADD NEW BUTTON (link)-->
                </div>
            </div>
            <div class="col-lg-7 align-self-center list-pages-crumbs text-right" id="breadcrumbs">
                <h3 class="text-themecolor">Roles</h3>
                <!--crumbs-->
                <ol class="breadcrumb float-right">
                    <li class="breadcrumb-item">App</li>    
                    <li class="breadcrumb-item  active active-bread-crumb ">Roles</li>
                </ol>
                <!--crumbs-->
            </div>
            
        </div>
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">All Roles List</h3>
                </div>
                <!-- /.card-header -->
                <div class="card-body">
                    <div class="table-responsive list-table-wrapper">
                        <table class="table table-hover" id="datatableUserRole">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Role</th>
                                    <th>Date</th>
                                    {{-- @canany(['edit roles', 'delete roles']) --}}
                                    <th>Actions</th>
                                    {{-- @endcanany --}}
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->date }}</td>
                                    <td>
                                        @if($role->isDisabled()!='disabled')
                                            @can('edit role')
                                            <a href="{{ route('admin.role.edit', ['role' => $role->id]) }}" class="btn btn-success btn-sm float-left mr-3" id="popup-modal-buttonUserRole">
                                                <span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span>
                                            </a>
                                            @endcan 
                                            @can('delete role')
                                            <form method="post" class="float-left delete-formUserRole"
                                                action="{{ route('admin.role.destroy', ['role' => $role->id ]) }}">
                                                @csrf
                                                @method('delete')
                                                <button type="submit" class="btn btn-danger btn-sm" 
                                                {{ $role->isDisabled() }}
                                                >
                                                    <span tooltip="Delete" flow="right"><i class="fas fa-trash-alt"></i></span>
                                                </button>
                                            </form>
                                            @endcan
                                        @else
                                            <button type="submit" class="btn btn-success btn-sm float-left mr-3" 
                                                {{ $role->isDisabled() }}
                                                ><span tooltip="Edit" flow="left"><i class="fas fa-edit"></i></span></button>

                                            <button type="submit" class="btn btn-danger btn-sm" 
                                                {{ $role->isDisabled() }}
                                                ><span tooltip="Delete" flow="right"><i class="fas fa-trash-alt"></i></span></button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5">There is no role.</td>
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