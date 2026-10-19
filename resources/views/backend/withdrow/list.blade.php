@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="panel-title">{{ _lang('Withdraw Requests') }}</span>
{{-- add --}}
                {{-- <a class="btn btn-primary btn-xs float-right" href="{{route('request_withdraw')}}">
                    <i class="ti-plus"></i>&nbsp;{{ _lang('Add New') }}
                </a> --}}
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
                            <th class="text-center">{{ _lang('Action') }}</th>
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
                                @if($item->status == 1)
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($item->status == 2)
                                    <span class="badge badge-success">Approved</span>
                                @elseif($item->status == 3)
                                    <span class="badge badge-danger">Rejected</span>
                                @endif
                            </td>

                            <td class="text-center">
                                <div class="dropdown">
                                    <button class="btn btn-primary btn-xs dropdown-toggle"
                                            type="button" id="dropdownMenuButton"
                                            data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ _lang('Action') }}
                                    </button>

                                    <div class="dropdown-menu">

                                        <!-- Approve -->
                                        @if(Auth::user()->user_type == 'admin')

                                        <a href="javascript:void(0)"
                                        class="dropdown-item"
                                        onclick="openStatusModal({{ $item->id }}, '2')">
                                            <i class="ti-check"></i>&nbsp;Approve
                                        </a>

                                        <a href="javascript:void(0)"
                                        class="dropdown-item"
                                        onclick="openStatusModal({{ $item->id }}, '3')">
                                            <i class="ti-close"></i>&nbsp;Reject
                                        </a>

                                    @endif



                                    <a href="javascript:void(0)"
                                    class="dropdown-item viewWithdraw"
                                    data-user_id="{{ $item->user_id }}"
                                    data-coin_transfer="{{ $item->coin_transfer }}"
                                    data-holder_name="{{ $item->holder_name }}"
                                    data-bank_name="{{ $item->bank_name }}"
                                    data-account_number="{{ $item->account_number }}"
                                    data-ifsc_code="{{ $item->ifsc_code }}"
                                    data-account_type="{{ $item->account_type }}"
                                    data-status="{{ $item->status }}"
                                    data-remark="{{ $item->remark ?? '' }}">
                                        <i class="ti-eye"></i>&nbsp;View
                                    </a>



                                        <!-- Edit -->
                                        @if(Auth::user()->user_type == 'user' && $item->status == 1)
                                        <a href="javascript:void(0)"
                                           class="dropdown-item editWithdraw"
                                           data-id="{{ $item->id }}"
                                           data-coin_transfer="{{ $item->coin_transfer }}"
                                           data-holder_name="{{ $item->holder_name }}"
                                           data-bank_name="{{ $item->bank_name }}"
                                           data-account_number="{{ $item->account_number }}"
                                           data-ifsc_code="{{ $item->ifsc_code }}"
                                           data-account_type="{{ $item->account_type }}">
                                            <i class="ti-pencil-alt"></i>&nbsp;Edit
                                        </a>
                                    @endif



                                    </div>
                                </div>
                            </td>


                        </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

         {{-- status model --}}
              <div class="modal fade" id="statusModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form id="statusForm" method="POST" action="{{ route('withdraw.changeStatus') }}">
                        @csrf

                        <input type="hidden" name="withdraw_id" id="modal_withdraw_id">
                        <input type="hidden" name="status" id="modal_status">

                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="modalTitle">Update Status</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>

                            <div class="modal-body">
                                <label>Remark</label>
                                <textarea name="remark" class="form-control" required></textarea>
                            </div>

                            <div class="modal-footer">
                                <button class="btn btn-primary">Submit</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

{{-- edit model --}}
<div class="modal fade" id="editWithdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="editWithdrawForm" method="POST" action="{{ route('withdraw.update') }}">
            @csrf
            <input type="hidden" name="withdraw_id" id="edit_withdraw_id">

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Withdraw Request</h5>
                    {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> --}}
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Row 1 -->
                        <div class="col-md-6">
                            <label>Coin Transfer</label>
                            <input type="number" class="form-control" name="coin_transfer" id="edit_coin_transfer" required>
                        </div>
                        <div class="col-md-6">
                            <label>Holder Name</label>
                            <input type="text" class="form-control" name="holder_name" id="edit_holder_name" required>
                        </div>

                        <!-- Row 2 -->
                        <div class="col-md-6">
                            <label>Bank Name</label>
                            <input type="text" class="form-control" name="bank_name" id="edit_bank_name" required>
                        </div>
                        <div class="col-md-6">
                            <label>Account Number</label>
                            <input type="text" class="form-control" name="account_number" id="edit_account_number" required>
                        </div>

                        <!-- Row 3 -->
                        <div class="col-md-6">
                            <label>IFSC Code</label>
                            <input type="text" class="form-control" name="ifsc_code" id="edit_ifsc_code" required>
                        </div>
                        <div class="col-md-6">
                            <label>Account Type</label>
                            <select name="account_type" id="edit_account_type" class="form-control" required>
                                <option value="savings">Savings</option>
                                <option value="current">Current</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-primary">Update</button>
                </div>
            </div>

        </form>
    </div>
</div>





<!-- View Withdraw Modal -->
<div class="modal fade" id="viewWithdrawModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Withdraw Request Details</h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal"></button> --}}
            </div>

            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label>User ID</label>
                        <input type="text" class="form-control" id="view_user_id" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Coin Transfer</label>
                        <input type="text" class="form-control" id="view_coin_transfer" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Holder Name</label>
                        <input type="text" class="form-control" id="view_holder_name" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>Bank Name</label>
                        <input type="text" class="form-control" id="view_bank_name" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Account Number</label>
                        <input type="text" class="form-control" id="view_account_number" readonly>
                    </div>
                    <div class="col-md-6">
                        <label>IFSC Code</label>
                        <input type="text" class="form-control" id="view_ifsc_code" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Account Type</label>
                        <input type="text" class="form-control" id="view_account_type" readonly>
                    </div>

                    <div class="col-md-6">
                        <label>Status</label>
                        <input type="text" class="form-control" id="view_status" readonly>
                    </div>

                    <div class="col-12">
                        <label>Remark</label>
                        <textarea class="form-control" id="view_remark" rows="2" readonly></textarea>
                    </div>
                </div>
            </div>
            {{-- <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div> --}}
        </div>
    </div>
</div>







        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function openStatusModal(id, status) {
    document.getElementById('modal_withdraw_id').value = id;
    document.getElementById('modal_status').value = status;

    document.getElementById('modalTitle').innerText =
        status.toUpperCase() + " WITHDRAW REQUEST";

    var modal = new bootstrap.Modal(document.getElementById('statusModal'));
    modal.show();
}




    $(document).on("click", ".editStatus", function () {
        let id = $(this).data("id");
        let status = $(this).data("status");
        let remark = $(this).data("remark");

        $("#modal_id").val(id);
        $("#modal_status").val(status);
        $("#modal_remark").val(remark);

        $("#statusModal").modal("show");
    });
    </script>


<script>
    // edit modal
        $(document).on('click', '.editWithdraw', function() {
        let id = $(this).data('id');
        $('#edit_withdraw_id').val(id);

        $('#edit_coin_transfer').val($(this).data('coin_transfer'));
        $('#edit_holder_name').val($(this).data('holder_name'));
        $('#edit_bank_name').val($(this).data('bank_name'));
        $('#edit_account_number').val($(this).data('account_number'));
        $('#edit_ifsc_code').val($(this).data('ifsc_code'));
        $('#edit_account_type').val($(this).data('account_type'));

        var modal = new bootstrap.Modal(document.getElementById('editWithdrawModal'));
        modal.show();
    });


    //view modal
    $(document).on('click', '.viewWithdraw', function() {
    $('#view_user_id').val($(this).data('user_id'));
    $('#view_coin_transfer').val($(this).data('coin_transfer'));
    $('#view_holder_name').val($(this).data('holder_name'));
    $('#view_bank_name').val($(this).data('bank_name'));
    $('#view_account_number').val($(this).data('account_number'));
    $('#view_ifsc_code').val($(this).data('ifsc_code'));
    $('#view_account_type').val($(this).data('account_type'));

    // Status display
    let status = $(this).data('status');
    let statusText = '';
    if (status == 1) statusText = 'Pending';
    else if (status == 2) statusText = 'Approved';
    else if (status == 3) statusText = 'Rejected';
    $('#view_status').val(statusText);

    $('#view_remark').val($(this).data('remark'));

    var modal = new bootstrap.Modal(document.getElementById('viewWithdrawModal'));
    modal.show();
});


    </script>

@endsection

