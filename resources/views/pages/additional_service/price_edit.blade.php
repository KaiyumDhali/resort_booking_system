<x-default-layout>

<div class="app-container">

    <h3 class="mb-4">Edit Price Rule</h3>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('additional-services.price.update', $rule->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row">

            {{-- Service --}}
            <div class="col-md-3">
                <label class="form-label">Select Services</label>
                <select name="additional_service_id[]" multiple
                        class="form-select" data-control="select2">

                    @foreach($services as $service)
                        <option value="{{ $service->id }}"
                            {{ $rule->additional_service_id == $service->id ? 'selected' : '' }}>
                            {{ $service->title }}
                        </option>
                    @endforeach

                </select>
            </div>

            {{-- Min --}}
            <div class="col-md-2">
                <label class="form-label">Min Person</label>
                <input type="number"
                       name="min_person"
                       value="{{ old('min_person', $rule->min_person) }}"
                       class="form-control">
            </div>

            {{-- Max --}}
            <div class="col-md-2">
                <label class="form-label">Max Person</label>
                <input type="number"
                       name="max_person"
                       value="{{ old('max_person', $rule->max_person) }}"
                       class="form-control">
            </div>

            {{-- Price --}}
            <div class="col-md-2">
                <label class="form-label">Price</label>
                <input type="number"
                       step="0.01"
                       name="price"
                       value="{{ old('price', $rule->price) }}"
                       class="form-control">
            </div>

            {{-- Status --}}
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="1" {{ $rule->status == 1 ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ $rule->status == 0 ? 'selected' : '' }}>Disabled</option>
                </select>
            </div>

        </div>

        <div class="mt-5 text-end">
            <a href="{{ route('additional-services.price.index') }}" class="btn btn-light">
                Cancel
            </a>
            <button type="submit" class="btn btn-success">
                Update Rule
            </button>
        </div>

    </form>

</div>

</x-default-layout>