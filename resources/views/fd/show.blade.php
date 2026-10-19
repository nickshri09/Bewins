@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-10 offset-lg-1">
        <div class="card">
            <div class="card-header d-flex align-items-center justify-content-between">
                <span class="panel-title">{{ _lang('FD Details') }}</span>
                <a href="{{ route('fd.index') }}" class="btn btn-secondary btn-sm">{{ _lang('Back') }}</a>
            </div>
            <div class="card-body">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <p><strong>{{ _lang('Customer Name:') }}</strong> {{ $fd->user->name ?? '-' }}</p>
                        <p><strong>{{ _lang('FD Amount:') }}</strong> {{ number_format($fd->fD_Amount, 2) }}</p>
                        <p><strong>{{ _lang('Duration:') }}</strong> {{ $fd->durations }} Months</p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>{{ _lang('FD Type:') }}</strong> {{ ucfirst($fd->fd_types) }}</p>
                        <p><strong>{{ _lang('Start Date:') }}</strong> {{ date('d M Y', strtotime($fd->start_date)) }}</p>
                        <p><strong>{{ _lang('End Date:') }}</strong> {{ date('d M Y', strtotime($fd->end_date)) }}</p>
                        <p><strong>{{ _lang('percentage:') }}</strong> {{($fd->percentage)) }}</p>
                        
                    </div>
                </div>

                <hr>

                @if($fd->fd_types == 'monthly' && !empty($schedule))
                    <h5 class="mt-4 mb-3">{{ _lang('Payment Schedule') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ _lang('Month') }}</th>
                                    <th>{{ _lang('Payment Date') }}</th>
                                    <th>{{ _lang('Principal') }}</th>
                                    <th>{{ _lang('Interest') }}</th>
                                    <th>{{ _lang('Total Payout') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($schedule as $row)
                                    <tr>
                                        <td>{{ $row['month'] }}</td>
                                        <td>{{ $row['payment_date'] }}</td>
                                        <td>{{ $row['principal'] }}</td>
                                        <td>{{ $row['interest'] }}</td>
                                        <td>{{ $row['total'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-muted mt-3">{{ _lang('No monthly schedule available for this FD type.') }}</p>
                @endif

            </div>
        </div>
    </div>
</div>
@endsection
