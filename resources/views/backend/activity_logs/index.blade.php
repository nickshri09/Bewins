@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <span class="panel-title">{{ _lang($pageTitle) }}</span>
            </div>

            <div class="card-body">
                <form method="GET" action="{{ route($routeName) }}" class="row mb-3">
                    <div class="col-md-3 mb-2">
                        <label>{{ _lang('Action') }}</label>
                        <input
                            type="text"
                            name="action"
                            class="form-control"
                            value="{{ request('action') }}"
                            placeholder="{{ _lang('Search action') }}"
                        >
                    </div>

                    <div class="col-md-3 mb-2">
                        <label>{{ _lang('From Date') }}</label>
                        <input
                            type="date"
                            name="from"
                            class="form-control"
                            value="{{ request('from') }}"
                        >
                    </div>

                    <div class="col-md-3 mb-2">
                        <label>{{ _lang('To Date') }}</label>
                        <input
                            type="date"
                            name="to"
                            class="form-control"
                            value="{{ request('to') }}"
                        >
                    </div>

                    <div class="col-md-3 mb-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary mr-2">
                            {{ _lang('Filter') }}
                        </button>
                        <a href="{{ route($routeName) }}" class="btn btn-secondary">
                            {{ _lang('Reset') }}
                        </a>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ _lang('Date & Time') }}</th>
                                <th>{{ _lang('User ID') }}</th>
                                <th>{{ _lang('Action') }}</th>
                                <th>{{ _lang('Record Type') }}</th>
                                <th>{{ _lang('Record ID') }}</th>
                                <th>{{ _lang('Description') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($activityLogs as $log)
                                <tr>
                                    <td>{{ $log->created_at }}</td>
                                    <td>{{ $log->user_id ?? '—' }}</td>
                                    <td>{{ $log->action }}</td>
                                    <td>{{ $log->subject_type ?? '—' }}</td>
                                    <td>{{ $log->subject_id ?? '—' }}</td>
                                    <td>{{ $log->description ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center">
                                        {{ _lang('No activity records found') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $activityLogs->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
