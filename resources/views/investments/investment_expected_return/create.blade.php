@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-6 col-md-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h4 class="mb-0 text-secondary fw-normal">Add New {{ ucfirst($type) }} Expected Return</h4>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('investment.expected-return.store') }}">
                            @csrf
                            <input type="hidden" name="type" value="{{ $type }}">

                            <div class="mb-4">
                                <label for="expected_return" class="form-label">{{ ucfirst($type) }} Expected Return (%)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="expected_return" class="form-control" id="expected_return" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="form-text">Enter the expected return percentage with up to 2 decimal places</div>
                            </div>

                            <div class="mb-4">
                                <label for="status" class="form-label mr-2 p-2">Status</label>
                                <select name="status" class="form-select p-1" id="status">
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('investment.expected-return.index', ['type' => $type]) }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-arrow-left mr-2"></i>Back
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save mr-2"></i>Save
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <style>
        .card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            border-radius: 0.375rem;
            margin-top: 2rem;
        }

        .card-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }

        .form-control, .form-select {
            padding: 0.625rem 0.875rem;
            font-size: 0.9rem;
            border: 1px solid #ced4da;
            border-radius: 0.25rem;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
        }

        .form-control:focus, .form-select:focus {
            border-color: #86b7fe;
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .form-label {
            font-weight: 500;
            margin-bottom: 0.5rem;
            color: #495057;
        }

        .form-text {
            font-size: 0.8rem;
            color: #6c757d;
            margin-top: 0.25rem;
        }

        .btn-primary {
            background-color: #0d6efd;
            border-color: #0d6efd;
            padding: 0.5rem 1.5rem;
        }

        .btn-outline-secondary {
            padding: 0.5rem 1.5rem;
        }

        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #ced4da;
        }
    </style>
@endpush
