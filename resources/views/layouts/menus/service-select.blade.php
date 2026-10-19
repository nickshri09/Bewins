@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h4>Select a Service</h4>
    <form method="POST" action="{{ route('custom.service.store') }}">
        @csrf

        <div class="form-group">
            <label for="custom_service_name">Service Name:</label>
            <input type="text" name="custom_service_name" class="form-control" placeholder="Enter your service name">
        </div>

        <div class="form-group mt-3">
            <label for="service_id">Choose Service:</label>
            <select name="service_id" class="form-control" required>
                <option value="">-- Select --</option>
           @foreach($services as $service)
    @if(!str_contains(strtolower($service->service_name), 'government') && !str_contains(strtolower($service->service_name), 'govt'))
        <option value="{{ $service->id }}">
            {{ $service->service_name }} - ₹{{ $service->fee }}
        </option>
    @endif
@endforeach

            </select>
        </div>

        <button class="btn btn-primary mt-3">Submit</button>
    </form>
</div>
@endsection
