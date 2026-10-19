@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title">{{ _lang('My Agents') }}</h4>
            </div>

            <div class="card-body">
                <table id="agents_table" class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th class="text-center">#</th>
                            <th>{{ _lang('Name') }}</th>
                            <th>{{ _lang('Email') }}</th>
                            <th>{{ _lang('Mobile Number') }}</th>
                            <th>{{ _lang('Branch') }}</th>
                            <th>{{ _lang('Status') }}</th>
                            <th class="text-center">{{ _lang('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($agents as $agent)
                        <tr data-id="row_{{ $agent->id }}">
                            <td class='profile_picture text-center'>
                                <img src="{{ profile_picture($agent->profile_picture) }}" class="thumb-sm img-thumbnail">
                            </td>
                            <td class='name'>{{ $agent->name }}</td>
                            <td class='email'>{{ $agent->email }}</td>
                            <td class='mobile_number'>{{ $agent->mobile_number }}</td>
                            <td class='branch'>{{ $agent->branch->name }}</td>
                            <td class='status'>{!! xss_clean(user_status($agent->status)) !!}</td>
                            <td class="text-center">
                                <span class="dropdown">
                                    <button class="btn btn-primary dropdown-toggle btn-xs" type="button"
                                        id="dropdownMenuButton" data-toggle="dropdown">
                                        {{ _lang('Action') }}
                                    </button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a href="{{ route('users.show', $agent->id) }}"
                                            data-title="{{ _lang('Agent Details') }}"
                                            class="dropdown-item ajax-modal"><i class="ti-eye"></i>
                                            {{ _lang('View') }}</a>
                                    </div>
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    if (!$.fn.DataTable.isDataTable('#agents_table')) {
        $('#agents_table').DataTable({
            responsive: true,
            language: {
                url: '{{ asset("backend/plugins/datatables/lang.json") }}'
            }
        });
    }
});
</script>
@endpush 