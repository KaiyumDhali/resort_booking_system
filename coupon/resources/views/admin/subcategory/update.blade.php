@extends('layouts.admin')

@section('content')
<main class="page-content">
    <div class="container container--flex">
        <div class="page-header">
            <h1 class="page-header__title">Update Category</h1>
        </div>
        <div class="card add-product card--content-center">
            <div class="card__wrapper">
                <div class="">
                    <form class="add-product__form" action="{{ route('sub_categorys.update', $subCategory->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="">

                            <div class="col-12 form-group form-group--lg">
                                <label class="form-label">select Category</label>
                                <div class="input-group input-group--append">
                                    <select class="form-control" name="category_id" required data-placeholder="">
                                        <option selected disabled>Select Category</option>
                                                @foreach($allCategories as $key => $value)
                                        <option value="{{ $key }}" {{ $subCategory->category_id==$key ? 'selected' : ''}}>
                                                    {{ $value}}
                                        </option>
                                                @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="">
                                <div class="col-12 form-group form-group--lg">
                                    <label class="form-label">Sub Category Name</label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" id="sub_category_name" name="sub_category_name" placeholder="" value="{{$subCategory->sub_category_name}}" required>
                                    </div>
                                </div>
                            </div>

                            <div class="">
                                <div class="col-12 form-group form-group--lg">
                                    <label class="form-label">Sub Category Code</label>
                                    <div class="input-group">
                                        <input class="form-control" type="text" id="sub_category_code" name="sub_category_code" placeholder="" value="{{$subCategory->sub_category_code}}" required readonly="readonly" maxlength="2">
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 form-group form-group--lg">
                                <label class="form-label">Status</label>
                                <div class="input-group input-group--append">
                                    <select class="form-control" name="status" required>
                                        <option value="">--Select Status--</option>
                                        <option value="1" selected>Active</option>
                                        <option value="2">Disable</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12">

                                <div class="add-product__submit">
                                    <div class="modal__footer-button">
                                        <button class="button button--primary button--block" type="submit"><span class="button__text">Save</span>
                                        </button>
                                    </div>
                                    <div class="modal__footer-button"><a class="button button--secondary button--block" href="{{ route('sub_categorys.index') }}"><span class="button__text">Cancel</span></a>
                                    </div>
                                </div>
                            </div>
                        </div>

                </div>
                </form>
            </div>
        </div>
    </div>
</div>
</main>

<script>
    document.getElementById('sub_category_name').addEventListener('input', function() {
        var subCategoryName = this.value.trim();
        if (subCategoryName.length > 0) {
            var words = subCategoryName.split(' ');
            var code = words[0].charAt(0);
            if (words.length > 1) {
                code += words[1].charAt(0);
            }
            document.getElementById('sub_category_code').value = code.toUpperCase();
        } else {
            document.getElementById('sub_category_code').value = '';
        }
    });
</script>

@endsection
