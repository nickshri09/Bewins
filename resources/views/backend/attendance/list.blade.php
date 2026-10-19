@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card no-export">
                <div class="card-header d-flex align-items-center">
                    <span class="panel-title">{{ _lang('Employee Attendance Summary') }}</span>
                    <a class="btn btn-primary btn-xs ml-auto ajax-modal" data-title="{{ _lang('Add Attendance') }}"
                        href="{{ route('attendances.create') }}"><i
                            class="ti-plus"></i>&nbsp;{{ _lang('Add Attendance') }}</a>
                </div>
                <div class="card-body">
                    <table id="attendances_table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ _lang('Employee') }}</th>
                                <th>{{ _lang('Designation') }}</th>
                                <th>{{ _lang('Present This Month') }}</th>
                                <th class="text-center">{{ _lang('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($employees as $employee)
                                <tr>
                                    <td>{{ $employee->name }}</td>
                                    <td>{{ $employee->designation }}</td>
                                    <td>
                                        <span class="badge badge-success">{{ $employee->current_month_count }}
                                            {{ _lang('Days') }}</span>
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('attendances.user_attendance', $employee->id) }}"
                                            class="btn btn-info btn-xs">
                                            <i class="ti-eye"></i> {{ _lang('View Details') }}
                                        </a>
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
