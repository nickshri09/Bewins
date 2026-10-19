@extends('layouts.app')
 
@section('content')
<div class="container mt-4">
 
    <!-- Page Heading -->
    <h4 class="mb-4 font-weight-bold">New Lead</h4>
 
    <!-- Breadcrumb Section -->
    <div class="bg-light p-3 rounded mb-4">
        <div class="d-flex justify-content-between align-items-center">
           
            <div class="d-flex align-items-center gap-2">
              <a href="{{ route('service.select') }}" class="btn btn-success">
    <i class="fas fa-plus-circle"></i> Add
</a>
                 
            </div>
        </div>
    </div>
 
    <!-- Custom Services List -->
    <div class="row">
        @forelse($customServices as $customService)
            <div class="col-md-4 mb-3">
                <a href="{{ route('basic.licence', ['id' => $customService->id]) }}" class="btn btn-outline-primary w-100">
                    {{ $customService->custom_service_name ?? 'N/A' }}
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    No custom services found.
                </div>
            </div>
        @endforelse
    </div>
 
</div>
@endsection
 
 