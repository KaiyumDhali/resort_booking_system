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
@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif
    <!-- Toolbar -->
   <div id="kt_app_toolbar" class="app-toolbar py-3 py-lg-6">
    <div class="app-container w-100 d-flex align-items-center justify-content-between">

        <!-- LEFT -->
        <div class="page-title d-flex flex-column justify-content-center">
            <h3 class="mb-0">Create Additional Service Price Rules</h3>
            <span class="text-muted fs-7">Make Pricing Rules for Every Services</span>
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

                    <form action="{{ route('additional-services.price.rule.store') }}" method="POST">
                        @csrf

                        {{-- Title --}}
                        <div class="row">
                    
                      <div class="col-md-3">
                                <label class="form-label">Select Services</label>
                                <select name="additional_service_id[]" multiple class="form-select form-control form-control-solid" data-control="select2">
                            @foreach($services as $service)
                                    <option value="{{ $service->id }}" 
                                        {{ (collect(request('service_id'))->contains($service->id)) ? 'selected' : '' }}>
                                        {{ $service->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                  
                          <div class="mb-5 col-md-2">
                            <label class="form-label required">Minimum Person</label>
                            <input type="number" name="min_person" class="form-control form-control-solid"
                                value="{{ old('min_person') }}">
                        </div>
                          <div class="mb-5 col-md-2">
                            <label class="form-label required">Maximum Person</label>
                            <input type="number"  name="max_person" class="form-control form-control-solid"
                                value="{{ old('max_person') }}">
                        </div>
                          <div class="mb-5 col-md-2">
                            <label class="form-label required">Price (৳)</label>
                            <input type="number" step="0.01" name="price" class="form-control form-control-solid"
                                value="{{ old('price', '0.00') }}">
                        </div>
                       
                          <div class="mb-5 col-md-3">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select form-select-solid">
                                <option value="1" selected>Active</option>
                                <option value="0">Disabled</option>
                            </select>
                        </div>
                        </div>
                        {{-- Price --}}
                      

                        {{-- Status --}}
              
   




                        
             

                        {{-- Actions --}}
                        <div class="d-flex justify-content-end gap-3 mt-6">
                            <a href="{{ route('additional-services.price.index') }}" class="btn btn-sm btn-light-primary">
                                Cancel
                            </a>

                            <button type="submit" class="btn btn-sm btn-success">
                                <span class="indicator-label">Submit</span>
                            </button>
                        </div>

                    </form>

                </div>
            </div>

        </div>
    </div>

</x-default-layout>
