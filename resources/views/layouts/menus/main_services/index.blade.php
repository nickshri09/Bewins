@extends('layouts.app')

@section('content')
    <div class="">
        <div class="d-flex justify-content-between align-items-center mb-3 shadow border rounded p-2">
            <h3>Main Services</h3>
            <a href="{{ route('main-services.create') }}" class="btn btn-primary">Add Main Service</a>
        </div>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" action="{{ route('main-services.index') }}">
                    <div class="row">
                        <div class="col-md-4">
                            <input type="text" name="search" class="form-control" placeholder="Search by code or name"
                                value="{{ request('search') }}">
                        </div>

                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary">
                                Search
                            </button>

                            <a href="{{ route('main-services.index') }}" class="btn btn-secondary">
                                Reset
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th>Code</th>
                            <th>Name</th>
                            <th class="text-center">Amount</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr>
                                <td>{{ $item->service_code }}</td>
                                <td>{{ $item->name }}</td>
                                <td class="text-center">₹ {{ number_format($item->amount, 2) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('main-services.edit', $item->id) }}"
                                        class="btn btn-sm btn-secondary">Edit</a>

                                    @if (auth()->user()->user_type == 'admin')
                                        <form action="{{ route('main-services.destroy', $item->id) }}" method="POST"
                                            class="d-inline" onsubmit="return confirm('Delete this main service?');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-4">No main services yet</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{-- <div class="card-footer">{{ $items->links() }}</div> --}}
            <div class="card-footer">{{ $items->appends(request()->query())->links() }}</div>
        </div>
    </div>
@endsection
