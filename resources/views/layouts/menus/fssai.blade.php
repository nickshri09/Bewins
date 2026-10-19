@extends('layouts.app')

@section('content')
<div class="container">
    <!-- Page Heading -->
    <h4 class="mb-3 font-weight-bold">New Lead</h4>

    <!-- Breadcrumb + Add Button -->
    <div class="bg-light p-3 rounded mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <strong>Amendment > FSSAI OR FOSCOS</strong>
            <div class="d-flex align-items-center gap-2">
              <a href="{{ route('service.select') }}" class="btn btn-success">
    <i class="fas fa-plus-circle"></i> Add
</a>
                 
            </div>
        </div>
    </div>

    <!-- Buttons -->
    <div class="card p-4">
        <div class="row text-center">
            <div class="col-md-4 mb-2">
                <a href="" class="btn btn-outline-primary w-100 {{ request()->routeIs('fssai.basic') ? 'bg-primary text-white' : '' }}">
                    Basic Licence (Amendment)
                </a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="" class="btn btn-outline-primary w-100 {{ request()->routeIs('fssai.state') ? 'bg-primary text-white' : '' }}">
                    State Licence (Amendment)
                </a>
            </div>
            <div class="col-md-4 mb-2">
                <a href="" class="btn btn-outline-primary w-100 {{ request()->routeIs('fssai.central') ? 'bg-primary text-white' : '' }}">
                    Central Licence (Amendment)
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
