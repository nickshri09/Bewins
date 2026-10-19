@extends('layouts.app')

@section('content')
<div class="container">
    <h3>Edit Main Service</h3>
    <form action="{{ route('main-services.update', $item->id) }}" method="POST" class="mt-3">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Service Code</label>
                <input name="service_code" class="form-control" value="{{ $item->service_code }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Name</label>
                <input name="name" class="form-control" value="{{ $item->name }}" required>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Amount</label>
                <input name="amount" type="number" step="0.01" class="form-control" value="{{ $item->amount }}" required>
            </div>
        </div>
        <a href="{{ route('main-services.index') }}" class="btn btn-secondary">Cancel</a>
        <button class="btn btn-primary">Update</button>
    </form>
</div>
@endsection


