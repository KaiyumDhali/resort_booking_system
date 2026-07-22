@extends('layouts.admin')

@section('content')
        <main class="page-content">
            <div class="container">
                <div class="page-header">
                    <h1 class="page-header__title">Sub Category</h1>
                </div>
                
                <div class="toolbox">
                    <div class="toolbox__row row gutter-bottom-xs">
                        <div class="toolbox__left col-12 col-lg">
                            <div class="toolbox__left-row row row--xs gutter-bottom-xs">
                                <div class="form-group form-group--inline col-12 col-sm-auto">
                                    <label class="form-label">Show</label>
                                    <div class="input-group input-group--white input-group--append">
                                        <input class="input input--select" type="text" value="10" size="1" data-toggle="dropdown" readonly><span class="input-group__arrow">
                                            <svg class="icon-icon-keyboard-down">
                                            <use xlink:href="#icon-keyboard-down"></use>
                                            </svg></span>
                                        <div class="dropdown-menu dropdown-menu--right dropdown-menu--fluid js-dropdown-select"><a class="dropdown-menu__item active" href="#" tabindex="0" data-value="10">10</a><a class="dropdown-menu__item" href="#" tabindex="0" data-value="15">15</a><a class="dropdown-menu__item" href="#" tabindex="0" data-value="20">20</a>
                                            <a
                                                class="dropdown-menu__item" href="#" tabindex="0" data-value="25">25</a><a class="dropdown-menu__item" href="#" tabindex="0" data-value="50">50</a>
                                        </div>
                                    </div>
                                </div>
                                
                                
                            </div>
                        </div>
                        <div class="toolbox__right col-12 col-lg-auto">
                            <div class="toolbox__right-row row row--xs flex-nowrap">
                                <div class="col col-lg-auto">
                                    <form class="toolbox__search" method="GET">
                                        <div class="input-group input-group--white input-group--prepend">
                                            <div class="input-group__prepend">
                                                <svg class="icon-icon-search">
                                                    <use xlink:href="#icon-search"></use>
                                                </svg>
                                            </div>
                                            <input class="input" type="text" placeholder="Search product">
                                        </div>
                                    </form>
                                </div>
                                <div class="col-auto">
                                    <a class="button-add button-add--blue" href="{{ route('sub_categorys.create') }}"><span class="button-add__icon">
                                            <svg class="icon-icon-plus">
                                            <use xlink:href="#icon-plus"></use>
                                            </svg>
                                        </span>
                                        <span class="button-add__text"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="table-wrapper">
                    <div class="table-wrapper__content table-products table-collapse scrollbar-thin scrollbar-visible" data-simplebar>
                        <table class="table table--lines">
                            <thead class="table__header">
                                <tr class="table__header-row">
                                    <th class="table__th-sort"><span class="align-middle">Category Name</span><span class="sort sort--down"></span>
                                    </th>
                                    <th class="table__th-sort"><span class="align-middle">Sub Category Name</span><span class="sort sort--down"></span>
                                    </th>
                                    <th class="table__th-sort"><span class="align-middle">Sub Category Code</span><span class="sort sort--down"></span>
                                    </th>
                                    <th class="table__th-sort"><span class="align-middle">Status</span><span class="sort sort--down"></span>
                                    </th>
                                    <th class="table__th-sort"><span class="align-middle">Action</span><span class="sort sort--down"></span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subCategorys as $subCategory)
                                <tr class="table__row">
                                    <td class="table__td">{{$subCategory->category->category_name}}</td>
                                    <td class="table__td">{{$subCategory->sub_category_name}}</td>
                                    <td class="table__td">{{$subCategory->sub_category_code}}</td>
                                    <td class="table__td"><span>{{ $subCategory->status==1?'Active':'Disabled' }}</span>
                                    </td>
                                    <td class="table__td"><span><a href="{{ route('sub_categorys.edit', $subCategory->id) }}"><span class="dropdown-items__link-icon">
                                                    <svg class="icon-icon-task-notes">
                                                    <use xlink:href="#icon-task-notes"></use>
                                                    </svg></span></a></span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="table-wrapper__footer">
                        <div class="row">
                            <div class="table-wrapper__show-result col text-grey"><span class="d-none d-sm-inline-block">Showing</span> 1 to 10 <span class="d-none d-sm-inline-block">of 50 items</span>
                            </div>
                            <div class="table-wrapper__pagination col-auto">
                                <ol class="pagination">
                                    <li class="pagination__item">
                                        <a class="pagination__arrow pagination__arrow--prev" href="#">
                                            <svg class="icon-icon-keyboard-left">
                                                <use xlink:href="#icon-keyboard-left"></use>
                                            </svg>
                                        </a>
                                    </li>
                                    <li class="pagination__item active"><a class="pagination__link" href="#">1</a>
                                    </li>
                                    <li class="pagination__item"><a class="pagination__link" href="#">2</a>
                                    </li>
                                    <li class="pagination__item"><a class="pagination__link" href="#">3</a>
                                    </li>
                                    <li class="pagination__item pagination__item--dots">...</li>
                                    <li class="pagination__item"><a class="pagination__link" href="#">10</a>
                                    </li>
                                    <li class="pagination__item">
                                        <a class="pagination__arrow pagination__arrow--next" href="#">
                                            <svg class="icon-icon-keyboard-right">
                                                <use xlink:href="#icon-keyboard-right"></use>
                                            </svg>
                                        </a>
                                    </li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
@endsection