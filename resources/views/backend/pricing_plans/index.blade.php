@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">{{ _lang('Pricing Plans') }}</h4>
                    <a href="{{ route('admin.pricing-plans.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> {{ _lang('Add New Plan') }}
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>{{ _lang('Name') }}</th>
                                    <th>{{ _lang('Price') }}</th>
                                    <th>{{ _lang('Validity') }}</th>
                                    <th>{{ _lang('Features') }}</th>
                                    <th>{{ _lang('Status') }}</th>
                                    <th>{{ _lang('Actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="{{ $plan->icon_class }} mr-2" style="color: #16a085;"></i>
                                                {{ $plan->name }}
                                            </div>
                                        </td>
                                        <td>₹{{ number_format($plan->price) }}/-</td>
                                        <td>{{ $plan->validity }}</td>
                                        <td>
                                            <span class="badge badge-info">{{ $plan->features->count() }} features</span>
                                        </td>
                                        <td>
                                            <form action="{{ route('admin.pricing-plans.toggle-status', $plan) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-sm {{ $plan->is_active ? 'btn-success' : 'btn-secondary' }}">
                                                    {{ $plan->is_active ? 'Active' : 'Inactive' }}
                                                </button>
                                            </form>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.pricing-plans.edit', $plan) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.pricing-plans.destroy', $plan) }}" method="POST" class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">{{ _lang('No pricing plans found') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
