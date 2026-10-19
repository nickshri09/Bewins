@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <span class="panel-title">{{ _lang('Attendance Details') }} - {{ $employee->name }}</span>
                    <a class="btn btn-secondary btn-xs ml-auto" href="{{ route('attendances.index') }}"><i
                            class="ti-arrow-left"></i>&nbsp;{{ _lang('Back') }}</a>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <form method="get" action="{{ route('attendances.user_attendance', $employee->id) }}"
                                class="form-inline">
                                <label class="mr-2">{{ _lang('Select Month & Year') }}: </label>
                                <select class="form-control mr-2" name="month">
                                    @foreach (range(1, 12) as $m)
                                        <option value="{{ sprintf('%02d', $m) }}"
                                            {{ $month == sprintf('%02d', $m) ? 'selected' : '' }}>
                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                        </option>
                                    @endforeach
                                </select>
                                <select class="form-control mr-2" name="year">
                                    @foreach (range(date('Y') - 5, date('Y') + 5) as $y)
                                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>
                                            {{ $y }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary btn-xs">{{ _lang('View') }}</button>
                            </form>
                        </div>
                    </div>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ _lang('Date') }}</th>
                                <th>{{ _lang('Login Time') }}</th>
                                <th>{{ _lang('Logout Time') }}</th>
                                <th>{{ _lang('Status') }}</th>
                                <th>{{ _lang('Note') }}</th>
                                <th class="text-center">{{ _lang('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($attendances->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center text-muted">
                                        {{ _lang('No records found for this month.') }}</td>
                                </tr>
                            @endif
                            @foreach ($attendances as $attendance)
                                <tr>
                                    <td>{{ date('d M, Y', strtotime($attendance->date)) }}</td>
                                    <td>{{ $attendance->login_time ? date('h:i A', strtotime($attendance->login_time)) : 'Not Logged In' }}
                                    </td>
                                    <td>{{ $attendance->logout_time ? date('h:i A', strtotime($attendance->logout_time)) : 'Not Logged Out' }}
                                    </td>
                                    <td>
                                        @if ($attendance->status == 'Present')
                                            <span class="badge badge-success">{{ _lang('Present') }}</span>
                                        @elseif($attendance->status == 'Absent')
                                            <span class="badge badge-danger">{{ _lang('Absent') }}</span>
                                        @elseif($attendance->status == 'Late')
                                            <span class="badge badge-warning">{{ _lang('Late') }}</span>
                                        @else
                                            <span class="badge badge-info">{{ $attendance->status }}</span>
                                        @endif
                                    </td>
                                    <td>{{ $attendance->note }}</td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-primary btn-xs dropdown-toggle" type="button"
                                                data-toggle="dropdown">
                                                {{ _lang('Action') }} &nbsp;
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item ajax-modal"
                                                    href="{{ route('attendances.edit', $attendance->id) }}"
                                                    data-title="{{ _lang('Update Attendance') }}">
                                                    <i class="ti-pencil-alt"></i> {{ _lang('Edit') }}
                                                </a>
                                                <form action="{{ route('attendances.destroy', $attendance->id) }}"
                                                    method="post" class="d-inline">
                                                    {{ csrf_field() }}
                                                    <input name="_method" type="hidden" value="DELETE">
                                                    <button class="dropdown-item btn-remove" type="submit">
                                                        <i class="ti-trash"></i> {{ _lang('Delete') }}
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
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
