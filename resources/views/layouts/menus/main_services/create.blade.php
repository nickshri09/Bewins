@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Create Main Service</h3>
    <form action="{{ route('main-services.store') }}" method="POST" class="mt-3">
        @csrf
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Service Code</label>
                <input name="service_code" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input name="name" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Amount</label>
                <input name="amount" type="number" step="0.01" class="form-control" required>
            </div>
        </div>
        <a href="{{ route('main-services.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary">Save</button>
    </form>
</div>
@endsection


