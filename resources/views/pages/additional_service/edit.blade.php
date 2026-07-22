<x-default-layout>

    {{-- Validation Errors --}}
    <div class="col-xl-12 px-5">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>

    <!-- Toolbar -->
    <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div class="app-container w-100 d-flex align-items-center justify-content-between">

        <!-- LEFT -->
        <div class="page-title d-flex flex-column justify-content-center">
            <h3 class="mb-0">Create Additional Service</h3>
            <span class="text-muted fs-7">Add extra service for spot booking</span>
        </div>

        <!-- RIGHT -->
        <div class="ms-auto">
            <a href="{{ route('additional-services.index') }}"
               class="btn btn-sm btn-light-primary">
                <i class="fa fa-arrow-left me-1"></i>
                Back
            </a>
        </div>

    </div>
</div>

    <!-- Content -->
    <div id="kt_app_content" class="app-content flex-column-fluid">
        <div id="kt_app_content_container" class="app-container">

            <div class="card card-flush">
                <div class="card-body py-5">

                    <form action="{{ route('additional-services.update', $additionalService->id) }}"
                          method="POST">
                        @csrf
                        @method('PUT')

                        {{-- Title --}}
                        <div class="row">
                        <div class="mb-5 col-md-5">
                            <label class="form-label required">Service Title</label>
                            <input type="text"
                                   name="title"
                                   class="form-control form-control-solid"
                                   value="{{ old('title', $additionalService->title) }}"
                                   placeholder="Enter service title">
                        </div>
                         <div class="mb-5 col-md-3">
                            <label class="form-label required">Price (৳)</label>
                            <input type="number"
                                   step="0.01"
                                   name="price"
                                   class="form-control form-control-solid"
                                   value="{{ old('price', $additionalService->price) }}">
                        </div>
                            <div class="col-md-4">
                                <label class="form-label">Select Zone</label>
                                <select name="spot_ids[]" multiple class="form-select form-control form-control-solid" data-control="select2">

                                    <option value="global" {{ $additionalService->is_global ? 'selected' : '' }}>
                                        Global
                                    </option>

                                    @foreach($spots as $spt)
                                        <option value="{{ $spt->id }}" 
                                            {{ in_array($spt->id, $selectedSpotIds) ? 'selected' : '' }}>
                                            {{ $spt->title }}
                                        </option>
                                    @endforeach

                                </select>
                            </div>
                        {{-- Description --}}
                        <div class="mb-5 col-md-10">
                            <label class="form-label">Description</label>
                            <textarea name="description"
                                      rows="4"
                                      class="form-control form-control-solid"
                                      placeholder="Service description">{{ old('description', $additionalService->description) }}</textarea>
                        </div>

                        {{-- Price --}}
                       

                        {{-- Status --}}
                        <div class="mb-5 col-md-2">
                            <label class="form-label required">Status</label>
                            <select name="status" class="form-select form-select-solid">
                                <option value="1" {{ $additionalService->status == 1 ? 'selected' : '' }}>
                                    Active
                                </option>
                                <option value="0" {{ $additionalService->status == 0 ? 'selected' : '' }}>
                                    Disable
                                </option>
                            </select>
                        </div>

                        <div class="col-12 col-md-3">
                                        <div class="card-body py-3">
                                            <div class="product fv-row fv-plugins-icon-container">
                                                <label class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="is_frontend" {{ $additionalService->is_frontend==1 ? 'checked' : '' }}/>
                                                    <span class="form-check-label text-muted fs-6">is Frontend Content </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <div class="card-body py-3">
                                            <div class="product fv-row fv-plugins-icon-container">
                                                <label class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="is_backend" {{ $additionalService->is_backend==1 ? 'checked' : '' }}/>
                                                    <span class="form-check-label text-muted fs-6">is Backend Content </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                        </div>
                        {{-- Buttons --}}
                        <div class="d-flex justify-content-end mt-6">
                            <a href="{{ route('additional-services.index') }}"
                               class="btn btn-light me-5">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-check-circle"></i> Update Service
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</x-default-layout>
