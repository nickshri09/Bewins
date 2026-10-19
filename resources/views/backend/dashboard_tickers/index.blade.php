@extends('layouts.app')

@section('content')
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ _lang('Scrolling Text') }}</h4>
            <a href="{{ route('admin.tickers.create') }}" class="btn btn-primary">{{ _lang('Add Scrolling Text') }}</a>
        </div>
        <div class="card-body">
            @if (session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
            <p class="text-muted">{{ _lang('All active messages appear in the continuously scrolling dashboard ticker.') }}</p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead><tr><th>{{ _lang('Message') }}</th><th>{{ _lang('Status') }}</th><th>{{ _lang('Actions') }}</th></tr></thead>
                    <tbody>
                        @forelse ($tickers as $ticker)
                            <tr>
                                <td>{{ $ticker->message }}</td>
                                <td>{!! $ticker->is_active ? '<span class="badge badge-success">' . e(_lang('Active')) . '</span>' : '<span class="badge badge-secondary">' . e(_lang('Inactive')) . '</span>' !!}</td>
                                <td class="text-nowrap">
                                    <a href="{{ route('admin.tickers.edit', $ticker) }}" class="btn btn-sm btn-info">{{ _lang('Edit') }}</a>
                                    <form action="{{ route('admin.tickers.destroy', $ticker) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ _lang('Delete this scrolling text?') }}')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-danger">{{ _lang('Delete') }}</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center">{{ _lang('No scrolling messages yet') }}</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            {{ $tickers->links() }}
        </div>
    </div>
@endsection
