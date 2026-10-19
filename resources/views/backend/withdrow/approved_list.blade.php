@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <span class="panel-title">{{ _lang('Approved Withdraw Requests') }}</span>
            </div>

            <div class="card-body">
                <table id="withdraw_table" class="table table-bordered data-table">
                    <thead>
                        <tr>
                            <th>{{ _lang('User ID') }}</th>
                            <th>{{ _lang('Account Number') }}</th>
                            <th>{{ _lang('IFSC Code') }}</th>
                            <th>{{ _lang('Account Type') }}</th>
                            <th>{{ _lang('Holder Name') }}</th>
                            <th>{{ _lang('Bank Name') }}</th>
                            <th>{{ _lang('Coin Transfer') }}</th>
                            <th>{{ _lang('Status') }}</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($list as $item)
                        <tr data-id="row_{{ $item->id }}">
                            <td>{{ $item->user_id }}</td>
                            <td>{{ $item->account_number }}</td>
                            <td>{{ $item->ifsc_code }}</td>
                            <td>{{ ucfirst($item->account_type) }}</td>
                            <td>{{ ucfirst($item->holder_name) }}</td>
                            <td>{{ ucfirst($item->bank_name) }}</td>
                            <td>{{ $item->coin_transfer }}</td>
                            <td>
                                <span class="badge badge-success">Approved</span>
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
