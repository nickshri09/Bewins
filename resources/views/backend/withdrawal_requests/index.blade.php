@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="panel-title">{{ _lang('Withdrawal Requests') }}</span>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    {{ _lang('Withdrawals are processed manually. Complete the bank or payment-provider transfer outside this panel, then approve the request to record the deduction. Approval does not send money automatically.') }}
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ _lang('ID') }}</th>
                                <th>{{ _lang('Member') }}</th>
                                <th>{{ _lang('Type') }}</th>
                                <th>{{ _lang('Details') }}</th>
                                <th>{{ _lang('Amount') }}</th>
                                <th>{{ _lang('Status') }}</th>
                                <th>{{ _lang('Requested At') }}</th>
                                <th class="text-center">{{ _lang('Action') }}</th>
                            </tr>                       
                        </thead>
                        <tbody>
                            @forelse($requests as $request)
                            <tr>
                                <td>{{ $request->id }}</td>
                                <td>{{ $request->member->first_name }} {{ $request->member->last_name }}<br><small>{{ $request->member->member_no }}</small></td>
                                <td>
                                    @if($request->type == 'wallet')
                                        <span class="badge badge-info">{{ _lang('Wallet') }}</span>
                                    @else
                                        <span class="badge badge-warning">{{ _lang('Investment') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($request->type == 'investment' && $request->investment)
                                        <small>{{ _lang('Investment ID') }}: {{ $request->investment_id }}</small><br>
                                        <small>{{ _lang('Type') }}: {{ $request->investment->investment_type }}</small>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td><b>{{ decimalPlace($request->amount) }}₹</b></td>
                                <td>
                                    @if($request->status == 'pending')
                                        <span class="badge badge-warning">{{ _lang('Pending') }}</span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge badge-success">{{ _lang('Approved') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ _lang('Rejected') }}</span>
                                    @endif
                                    @if($request->admin_note)
                                        <i class="fas fa-info-circle" data-toggle="tooltip" title="{{ $request->admin_note }}"></i>
                                    @endif
                                </td>
                                <td>{{ $request->created_at->format('d M, Y H:i A') }}</td>
                                <td class="text-center">
                                    @if($request->status == 'pending')
                                        <form action="{{ route('withdrawal_requests.approve', $request->id) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ _lang('Are you sure you want to approve and deduct this amount?') }}');">
                                            @csrf
                                            <input type="hidden" name="admin_note" value="Approved by admin">
                                            <button type="submit" class="btn btn-success btn-sm"><i class="fas fa-check"></i> {{ _lang('Approve') }}</button>
                                        </form>

                                        <button class="btn btn-danger btn-sm" data-toggle="modal" data-target="#rejectModal{{ $request->id }}"><i class="fas fa-times"></i> {{ _lang('Reject') }}</button>

                                        <!-- Reject Modal -->
                                        <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered" role="document">
                                                <div class="modal-content">
                                                    <form action="{{ route('withdrawal_requests.reject', $request->id) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">{{ _lang('Reject Withdrawal Request') }}</h5>
                                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                                <span aria-hidden="true">&times;</span>
                                                            </button>
                                                        </div>
                                                        <div class="modal-body text-left">
                                                            <div class="form-group">
                                                                <label>{{ _lang('Reason for Rejection') }} <span class="text-danger">*</span></label>
                                                                <textarea class="form-control" name="admin_note" required rows="3"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ _lang('Cancel') }}</button>
                                                            <button type="submit" class="btn btn-danger">{{ _lang('Reject Request') }}</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">{{ _lang('No withdrawal requests found.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $requests->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
