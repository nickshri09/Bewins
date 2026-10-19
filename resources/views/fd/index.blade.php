@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <span class="panel-title">{{ _lang('FD List') }}</span>
                    <div class="ml-auto d-flex align-items-center">
                        {{-- <select name="status" class="select-filter filter-select auto-select mr-1" data-selected="{{ $status ?? '' }}">
						<option value="">{{ _lang('All') }}</option>
						<option value="pending">{{ _lang('Pending') }}</option>
						<option value="active">{{ _lang('Active') }}</option>
						<option value="matured">{{ _lang('Matured') }}</option>
						<option value="withdrawn">{{ _lang('Withdrawn') }}</option>
					</select> --}}
                        <a class="btn btn-primary btn-xs" href="{{ route('fd.create') }}"><i
                                class="ti-plus"></i>&nbsp;{{ _lang('Add New') }}</a>
                    </div>
                </div>

                <div class="card-body">
                    <table id="fd_table" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ _lang('FD ID') }}</th>
                                <th>{{ _lang('Customer') }}</th>
                                <th>{{ _lang('Currency') }}</th>
                                <th>{{ _lang('Amount') }}</th>
                                <th>{{ _lang('FD Type') }}</th>
                                <th>{{ _lang('Start Date') }}</th>
                                <th>{{ _lang('End Date') }}</th>
                                <th>{{ _lang('Total Paid') }}</th>
                                <!--<th>{{ _lang('Due Amount') }}</th>-->
                                <th>{{ _lang('Interest_Rate') }}</th>
                                <th>{{ _lang('Status') }}</th>
                                <th class="text-center">{{ _lang('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($fds as $fd)
                                <tr>
                                    <td>{{ $fd->id }}</td>
                                    {{-- <td>{{ $fd->member->first_name}}</td> --}}
                                    <td>{{ $fd->member?->name ?? '-' }}</td>
                                    <td>{{ $fd->currency->name }}</td>
                                    <td>{{ number_format($fd->fd_amount, 2) }}</td>
                                    <td>{{ ucfirst($fd->fd_types) }}</td>
                                    <td>{{ $fd->start_date->format('d-m-Y') }}</td>
                                    <td>{{ $fd->end_date->format('d-m-Y') }}</td>
                                    <td>{{ number_format($fd->total_paid, 2) }}</td>
                                    <!--<td>{{ number_format($fd->due_Amount, 2) }}</td>-->
                                    <td>{{ number_format($fd->percentage, 2) }}</td>
                                    <td>
                                        @if ($fd->status == 0)
                                            <span class="badge bg-warning text-white">Pending</span>
                                        @elseif($fd->status == 1)
                                            <span class="badge bg-success text-white">Approved</span>
                                        @elseif($fd->status == 2)
                                            <span class="badge bg-danger text-white">Rejected</span>
                                        @elseif($fd->status == 3)
                                            <span class="badge bg-info text-white">Withdrawn</span>
                                        @endif
                                    </td>

                                    {{-- <td>{{ ucfirst($fd->status ?? 'pending') }}</td> --}}
                                    <td class="text-center">

                                        <a href="{{ route('fd.show', $fd->id) }}" class="btn btn-primary btn-xs ml-auto">
                                            <i class="ti-eye"></i> {{ _lang('View') }}
                                        </a>
                                        <a href="{{ route('fd.edit', $fd->id) }}" class="btn btn-yellow btn-xs ml-auto">
                                            <i class="ti-pencil-alt"></i> {{ _lang('Edit') }}
                                        </a>
                                        @if ($fd->status == 'pending')
                                            <form action="{{ route('fd.approve', $fd->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm"><i
                                                        class="ti-check"></i></button>
                                            </form>
                                        @endif
                                        @if ($fd->status == 'matured')
                                            <form action="{{ route('fd.withdraw', $fd->id) }}" method="POST"
                                                style="display:inline-block;">
                                                @csrf
                                                <button type="submit" class="btn btn-warning btn-sm"><i
                                                        class="ti-wallet"></i></button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- <h1>vdsvf</h1> --}}
@endsection

<style>
    .btn-yellow {
        background-color: #eeb73f !important;
        border-color: #eeb73f !important;
        color: #ffffff !important;
        /* text color white */
    }

    .btn-yellow:hover {
        background-color: #BF8100 !important;
        /* hover thoda dark */
        border-color: #BF8100 !important;
    }
</style>


@section('js-script')
    <script src="{{ asset('public/backend/assets/js/datatables/fd.js?v=1.0') }}"></script>
@endsection
