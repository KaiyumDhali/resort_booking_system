<x-default-layout>

    {{-- Alerts --}}
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

                    <form action="{{ route('additional-services.store') }}" method="POST">
                        @csrf

                        {{-- Title --}}
                        <div class="row">
                        <div class="mb-5 col-md-6">
                            <label class="form-label required">Service Title</label>
                            <input type="text" name="title" class="form-control form-control-solid"
                                value="{{ old('title') }}" placeholder="e.g. BBQ, Photography">
                        </div>
                      <div class="col-md-3">
                                <label class="form-label">Select Zone</label>
                                <select name="spot_ids[]" multiple class="form-select form-control form-control-solid" data-control="select2">
                                    <option value="global" >
                                    Global
                                    </option>
                            @foreach($spots as $spt)
                                    <option value="{{ $spt->id }}" 
                                        {{ (collect(request('spot_id'))->contains($spt->id)) ? 'selected' : '' }}>
                                        {{ $spt->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                          <div class="mb-5 col-md-3">
                            <label class="form-label required">Price (৳)</label>
                            <input type="number" step="0.01" name="price" class="form-control form-control-solid"
                                value="{{ old('price', '0.00') }}">
                        </div>
                        {{-- Description --}}
                        <div class="mb-5 col-md-9">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="4" class="form-control form-control-solid"
                                placeholder="Optional service details">{{ old('description') }}</textarea>
                        </div>
                          <div class="mb-5 col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select form-select-solid">
                                <option value="1" selected>Active</option>
                                <option value="0">Disabled</option>
                            </select>
                        </div>
                        <div class="col-12 col-md-3">
                                        <div class="card-body py-3">
                                            <div class="product fv-row fv-plugins-icon-container">
                                                <label class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="is_frontend" checked/>
                                                    <span class="form-check-label text-muted fs-6">is Frontend Content </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-12 col-md-3">
                                        <div class="card-body py-3">
                                            <div class="product fv-row fv-plugins-icon-container">
                                                <label class="form-check form-switch form-check-custom form-check-solid">
                                                    <input class="form-check-input w-30px h-20px" type="checkbox" value="1" name="is_backend" checked/>
                                                    <span class="form-check-label text-muted fs-6">is Backend Content </span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                   
                        </div>
                        {{-- Price --}}
                      

                        {{-- Status --}}
              
   


    <!-- Zones Grid -->
    <!-- <div class="zone-grid" id="zoneGrid">
        @foreach($spots as $spt)
            <label class="zone-card">
                <input type="checkbox" name="spot_ids[]" value="{{ $spt->id }}">

                <div class="zone-content">
                    <div class="zone-title">{{ $spt->title }}</div>
                </div>

                <div class="zone-check">
                    <i class="fa fa-check"></i>
                </div>
            </label>
        @endforeach
    </div>
</div> -->

                        
             

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-3 mt-6">
                            <a href="{{ route('additional-services.index') }}" class="btn btn-sm btn-light-primary">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-sm btn-success">
                                <span class="indicator-label">Save Service</span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</x-default-layout>
