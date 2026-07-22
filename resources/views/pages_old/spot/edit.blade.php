<x-default-layout>
    <div class="col-xl-12 px-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!--begin::Toolbar-->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
        <div id="kt_app_toolbar_container" class="app-container d-flex flex-stack">
            <div class="page-title d-flex flex-column justify-content-center flex-wrap me-3">
                <h3>Edit Spots</h3>
            </div>
            {{-- <div class="d-flex align-items-center gap-2 gap-lg-3">
                <a href="{{ route('spots.index') }}" class="btn btn-sm btn-light-primary">
                    <i class="ki-duotone ki-arrow-left fs-2"></i>Go Back
                </a>
            </div> --}}
        </div>
    </div>
    <!--end::Toolbar-->

    <!--begin::Content-->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container">
            <div class="card card-flush">
                {{-- <div class="card-header">
                    <h3 class="card-title">Update About Us</h3>
                </div> --}}
                <div class="card-body py-5">
                    <form action="{{ route('spots.update', $spots->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="mb-5">
                            <label for="title" class="form-label required">Title</label>
                            <input type="text" class="form-control form-control-solid" id="title" name="title"
                                value="{{ old('title', $spots->title) }}" />
                            {{-- <textarea class="form-control form-control-solid" id="title" name="title" rows="2">{{ old('title', $spots->title) }}</textarea> --}}
                        </div>



                        <script src="https://cdn.ckeditor.com/4.22.1/full/ckeditor.js"></script>

                        <div class="mb-5">
                            <label for="description" class="form-label required">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="5">{{ old('description', $spots->description) }}</textarea>
                        </div>

                        <script>
                            CKEDITOR.replace('description', {
                                allowedContent: true // Allow all HTML
                            });
                        </script>

                        <div class="mb-5">
                            <label for="title" class="form-label required">Price</label>
                            <input type="text" class="form-control form-control-solid" id="price" name="price"
                                value="{{ old('price', $spots->price) }}" />
                        </div>

                        <div class="mb-5">
                            <label for="image" class="form-label">Spot Thumbnail Image (1920px * 1080px)</label>
                            <input type="file" class="form-control form-control-solid p-2" id="image"
                                name="image" />
                            @if ($spots->image)
                                <div class="mt-2">
                                    <img src="{{ asset(Storage::url($spots->image)) }}" height="75" width="75"
                                        alt="Spot Image" />

                                    @if ($spots->image != null)
                                        <a class="btn btn-sm btn-danger"
                                            onclick="return confirm('Are you sure you want to delete ?')"
                                            href="{{ route('spot.image_destroy', $spots->id) }}"><i
                                                class="fa fa-trash"></i></a>
                                    @endif

                                </div>
                            @endif
                        </div>


                        <div class="mb-5">
                            <div class="product fv-row fv-plugins-icon-container">
                                <label class="form-label">Spot Gallery Image (1920px * 1080px)</label>
                                <input type="file" class="form-control form-control-sm mb-2" id="image"
                                    name="spot_gallery_image[]" multiple="multiple" accept="image/png, image/jpeg, image/jpg"
                                    value="{{ old('spot_gallery_image') }}" />

                                <!-- Display existing images if editing -->
                                @if (isset($spots) && $spots->spot_detail)
                                    <div class="mt-2 d-flex flex-wrap gap-2">
                                        @foreach ($spots->spot_detail as $image)
                                            <div class="existing-image d-flex align-items-center gap-2">
                                                <img src="{{ asset(Storage::url($image->image_path)) }}" alt="Image"
                                                    style="width: 100px; height: auto;">

                                                <a class="btn btn-sm btn-danger"
                                                    onclick="return confirm('Are you sure you want to delete ?')"
                                                    href="{{ route('spot_gallery_image_destroy', $image->id) }}">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>



                        <div class="mb-5">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select form-select-solid" id="status" name="status">
                                <option disabled>Select Status</option>
                                <option value="1" {{ $spots->status == 1 ? 'selected' : '' }}>Active</option>
                                <option value="0" {{ $spots->status == 0 ? 'selected' : '' }}>Disable</option>
                            </select>
                        </div>

                        <div class="d-flex justify-content-end my-5">
                            <!--begin::Button-->
                            <a href="{{ route('spots.index') }}" id="kt_ecommerce_add_product_cancel"
                                class="btn btn-sm btn-primary me-5">Cancel</a>
                            <!--end::Button-->
                            <!--begin::Button-->
                            <button type="submit" id="kt_ecommerce_add_category_submit" class="btn btn-sm btn-success">
                                <span class="indicator-label">Save</span>
                                <span class="indicator-progress">Please wait...
                                    <span class="spinner-border spinner-border-sm align-middle ms-2"></span></span>
                            </button>
                            <!--end::Button-->
                        </div>

                        {{-- <div class="text-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ki-duotone ki-check-circle fs-2"></i> Save
                            </button>
                        </div> --}}


                    </form>
                </div>
            </div>
        </div>
    </div>
    <!--end::Content-->
</x-default-layout>
