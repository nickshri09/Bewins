@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Page Heading -->
    <h4 class="mb-3 font-weight-bold">Add New Service</h4>

    <!-- Breadcrumb -->
    <div class="bg-light p-3 rounded mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <strong>
                {{ $serviceCategories[$category]['name'] }}
                @if($subCategory)
                    > {{ $serviceCategories[$category]['sub_categories'][$subCategory] }}
                @endif
            </strong>
        </div>
    </div>

    <!-- Add Service Form -->
    <div class="card">
        <div class="card-body">
            <form action="{{ route('services.store', ['category' => $category, 'subCategory' => $subCategory]) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label for="custom_service_name" class="form-label">Service Name</label>
                    <input type="text" class="form-control @error('custom_service_name') is-invalid @enderror"
                           id="custom_service_name" name="custom_service_name" required>
                    @error('custom_service_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="obligation" class="form-label">Obligation</label>
                        <input type="text" class="form-control @error('obligation') is-invalid @enderror" id="obligation" name="obligation">
                        @error('obligation')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="frequency" class="form-label">Frequency</label>
                        <input type="text" class="form-control @error('frequency') is-invalid @enderror" id="frequency" name="frequency">
                        @error('frequency')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="maximum_tat" class="form-label">Max TAT</label>
                        <input type="text" class="form-control @error('maximum_tat') is-invalid @enderror" id="maximum_tat" name="maximum_tat">
                        @error('maximum_tat')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label for="main_service_id" class="form-label">Select Main Service</label>
                    <select class="form-control select2 @error('main_service_id') is-invalid @enderror"
                            id="main_service_id" name="main_service_id" required>
                        <option value="">Select One</option>
                        @foreach($mainServices as $service)
                            <option value="{{ $service->id }}">{{ $service->service_code ? $service->service_code.' - ' : '' }}{{ $service->name }} - ₹{{ number_format($service->amount, 2) }}</option>
                        @endforeach
                    </select>
                    @error('main_service_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <div class="d-flex justify-content-end gap-2">
                    <a href="{{ route('services.index', ['category' => $category, 'subCategory' => $subCategory]) }}"
                       class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Service</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.jQuery && jQuery.fn && typeof jQuery.fn.select2 === 'function') {
        jQuery('#main_service_id').select2({
            placeholder: 'Select One',
            allowClear: true,
            width: '100%'
        });
    }
});
</script>
@endsection
