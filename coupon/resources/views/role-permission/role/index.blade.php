@extends('layouts.admin')

@section('content')
    <main class="page-content">
        <div class="container">
            <div class="page-header">
                <h1 class="page-header__title">Roles</h1>
            </div>

            <div class="toolbox">
                <div class="toolbox__row row gutter-bottom-xs">


                    <div class="toolbox__left col-12 col-lg">
                        <div class="toolbox__left-row row row--xs gutter-bottom-xs">

                            <div class="form-group form-group--inline col-12 col-sm-auto">
                                <!--<label class="form-label">Show</label>-->
                                <div class="input-group input-group--white input-group--append">
                                    <div class="container mt-5">
                                        @can('view user')
                                            <a href="{{ route('roles.index') }}" class="btn btn-primary mx-1">Roles</a>
                                        @endcan
                                        @can('view permission')
                                            <a href="{{ route('permissions.index') }}" class="btn btn-info mx-1">Permissions</a>
                                        @endcan
                                        @can('view role')
                                            <a href="{{ route('users.index') }}" class="btn btn-warning mx-1">Users</a>
                                        @endcan
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>

                    <div class="toolbox__right col-12 col-lg-auto">
                        <div class="toolbox__right-row row row--xs flex-nowrap">
                            <div class="col col-lg-auto">
                                <!--                                    <form class="toolbox__search" method="GET">
                                                                                        <div class="input-group input-group--white input-group--prepend">
                                                                                            <div class="input-group__prepend">
                                                                                                <svg class="icon-icon-search">
                                                                                                    <use xlink:href="#icon-search"></use>
                                                                                                </svg>
                                                                                            </div>
                                                                                            <input class="input" type="text" placeholder="Search product">
                                                                                        </div>
                                                                                    </form>-->
                            </div>


                            <div class="col-auto">
                                @can('create role')
                                    <a class="button-add button-add--blue" href="{{ route('roles.create') }}"><span
                                            class="button-add__icon">
                                            <svg class="icon-icon-plus">
                                                <use xlink:href="#icon-plus"></use>
                                            </svg>
                                        </span>
                                        <span class="button-add__text"></span>
                                    </a>
                                @endcan

                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="table-wrapper">
                <div class="table-wrapper__content table-products table-collapse scrollbar-thin scrollbar-visible"
                    data-simplebar>
                    <table class="table table--lines" id="unitTable">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Name</th>
                                <th width="40%">Action</th>
                            </tr>
                        </thead>
                        <tbody>


                            @foreach ($roles as $role)
                                <tr>
                                    <td>{{ $role->id }}</td>
                                    <td>{{ $role->name }}</td>
                                    <td>
                                        <!--<a href="{{-- url('roles/'.$role->id.'/give-permissions') --}}" class="btn btn-warning">Add / Edit Role Permission</a>-->


                                        @can('create role')
                                            <a href="{{ route('addPermissionToRole', $role->id) }}" class="btn btn-warning">Add
                                                /
                                                Edit Role Permission</a>
                                        @endcan

                                        @can('update role')
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-success">Edit</a>
                                        @endcan


                                        @can('delete role')
                                            <form onclick="return confirm('are you sure ? ')" class="d-inline"
                                                action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                                @csrf
                                                @method('delete')
                                                <button class="btn btn-danger mx-2" style="">
                                                    Delete
                                                </button>
                                            </form>
                                        @endcan
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </main>
@endsection
