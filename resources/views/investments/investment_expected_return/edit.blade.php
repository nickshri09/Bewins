@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <h4>Edit {{ ucfirst($record->type) }} Expected Return</h4>

        <form method="POST" action="{{ route('investment.expected-return.update', $record->id) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label>{{ ucfirst($record->type) }} Expected Return (%)</label>
                <input type="number" step="0.01" name="expected_return" value="{{ $record->expected_return }}"
                    class="form-control" required>
            </div>

            <div class="mb-3">
                <label>Status</label>
                <select name="status" class="form-control">
                    <option value="1" {{ $record->status ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ !$record->status ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <button class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
