@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <span class="panel-title">{{ _lang('View Loan Details') }}</span>
                </div>
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#loan_details">{{ _lang('Loan Details') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#guarantor">{{ _lang('Guarantor') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#collateral">{{ _lang('Collateral') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#schedule">{{ _lang('Repayments Schedule') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#repayments">{{ _lang('Repayments') }}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab"
                                href="#agreement_docs">{{ _lang('Agreement Documents') }}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('loans.edit', $loan['id']) }}">{{ _lang('Edit') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('members.show', $loan->borrower_id) }}">
                                <i class="ti-user"></i> Member Details
                            </a>
                        </li>




                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content">
                        <div class="tab-pane active" id="loan_details">
                            @if ($loan->status == 0)
                                <div class="alert alert-warning mt-4">
                                    <span>
                                        {{ _lang('Add Loan ID, Release Date and First Payment Date before approving loan request') }}
                                    </span>
                                </div>
                            @endif
                            <table class="table table-bordered mt-4">
                                <tr>
                                    <td>{{ _lang('Loan ID') }}</td>
                                    <td>{{ $loan->loan_id }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Loan Type') }}</td>
                                    <td>{{ $loan->loan_product->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Borrower') }}</td>
                                    <td>{{ $loan->borrower->first_name . ' ' . $loan->borrower->last_name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Member No') }}</td>
                                    <td>{{ $loan->borrower->member_no }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Status') }}</td>
                                    <td>
                                        @if ($loan->status == 0)
                                            {!! xss_clean(show_status(_lang('Pending'), 'warning')) !!}
                                        @elseif($loan->status == 1)
                                            {!! xss_clean(show_status(_lang('Approved'), 'success')) !!}
                                        @elseif($loan->status == 2)
                                            {!! xss_clean(show_status(_lang('Completed'), 'info')) !!}
                                        @elseif($loan->status == 3)
                                            {!! xss_clean(show_status(_lang('Cancelled'), 'danger')) !!}
                                        @endif
                                        @if ($loan->status == 0)
                                            <a class="btn btn-outline-primary btn-xs"
                                                href="{{ route('loans.approve', $loan['id']) }}"><i
                                                    class="ti-check-box"></i>&nbsp;{{ _lang('Click to Approve') }}</a>
                                            <a class="btn btn-outline-danger btn-xs float-right"
                                                href="{{ route('loans.reject', $loan['id']) }}"><i
                                                    class="ti-close"></i>&nbsp;{{ _lang('Click to Reject') }}</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('First Payment Date') }}</td>
                                    <td>{{ $loan->first_payment_date }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Release Date') }}</td>
                                    <td>
                                        {{ $loan->release_date != '' ? $loan->release_date : '' }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Applied Amount') }}</td>
                                    <td>
                                        {{ decimalPlace($loan->applied_amount, currency($loan->currency->name)) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Total Principal Paid') }}</td>
                                    <td class="text-success">
                                        {{ decimalPlace($loan->total_paid, currency($loan->currency->name)) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Total Interest Paid') }}</td>
                                    <td class="text-success">
                                        {{ decimalPlace($loan->payments->sum('interest'), currency($loan->currency->name)) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Total Penalties Paid') }}</td>
                                    <td class="text-success">
                                        {{ decimalPlace($loan->payments->sum('late_penalties'), currency($loan->currency->name)) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Due Amount') }}</td>
                                    <td class="text-danger">
                                        {{ decimalPlace($loan->applied_amount - $loan->total_paid, currency($loan->currency->name)) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Late Payment Penalties') }}</td>
                                    <td>{{ $loan->late_payment_penalties }} %</td>
                                </tr>
                                <!--Custom Fields-->
                                @if (!$customFields->isEmpty())
                                    @php $customFieldsData = json_decode($loan->custom_fields, true); @endphp
                                    @foreach ($customFields as $customField)
                                        <tr>
                                            <td>{{ $customField->field_name }}</td>
                                            <td>
                                                @if ($customField->field_type == 'file')
                                                    @php $file = $customFieldsData[$customField->field_name]['field_value'] ?? null; @endphp
                                                    {!! $file != null
                                                        ? '<a href="' .
                                                            asset('public/uploads/media/' . $file) .
                                                            '" target="_blank" class="btn btn-xs btn-outline-primary"><i class="far fa-eye mr-2"></i>' .
                                                            _lang('Preview') .
                                                            '</a>'
                                                        : '' !!}
                                                @else
                                                    {{ $customFieldsData[$customField->field_name]['field_value'] ?? null }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                <tr>
                                    <td>{{ _lang('Attachment') }}</td>
                                    <td>
                                        {!! $loan->attachment == ''
                                            ? ''
                                            : '<a href="' .
                                                asset('public/uploads/media/' . $loan->attachment) .
                                                '" target="_blank">' .
                                                _lang('Download') .
                                                '</a>' !!}
                                    </td>
                                </tr>
                                @if ($loan->status == 1)
                                    <tr>
                                        <td>{{ _lang('Approved Date') }}</td>
                                        <td>{{ $loan->approved_date }}</td>
                                    </tr>
                                    <tr>
                                        <td>{{ _lang('Approved By') }}</td>
                                        <td>{{ $loan->approved_by->name }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>{{ _lang('Created By') }}</td>
                                    <td>{{ $loan->created_by->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Address') }}</td>
                                    <td>{{ $loan->address }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Description') }}</td>
                                    <td>{{ $loan->description }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Remarks') }}</td>
                                    <td>{{ $loan->remarks }}</td>
                                </tr>

                                <tr>
                                    <td>{{ _lang('Creator') }}</td>
                                    <td>{{ $loan->created_user_id }}</td>
                                </tr>

                            </table>
                        </div>
                        <div class="tab-pane fade" id="guarantor">
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <span>{{ _lang('Guarantors') }}</span>
                                    <a class="btn btn-primary btn-xs ml-auto ajax-modal"
                                        href="{{ route('guarantors.create') }}"
                                        data-title="{{ _lang('Add Guarantor') }}"><i class="ti-plus"></i>
                                        {{ _lang('Add New') }}</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table id="guarantors_table" class="table table-bordered mt-2">
                                            <thead>
                                                <tr>
                                                    <th>{{ _lang('Loan ID') }}</th>
                                                    <th>{{ _lang('Guarantor') }}</th>
                                                    <th>{{ _lang('Amount') }}</th>
                                                    <th class="text-center">{{ _lang('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($guarantors as $guarantor)
                                                    <tr data-id="row_{{ $guarantor->id }}">
                                                        <td class='loan_id'>{{ $guarantor->loan->loan_id }}</td>
                                                        <td class='member_id'>{{ $guarantor->member->name }}</td>
                                                        <td class='amount'>
                                                            {{ decimalPlace($guarantor->amount, currency($loan->currency->name)) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <span class="dropdown">
                                                                <button class="btn btn-primary dropdown-toggle btn-xs"
                                                                    type="button" id="dropdownMenuButton"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    {{ _lang('Action') }}
                                                                </button>
                                                                <form
                                                                    action="{{ route('guarantors.destroy', $guarantor['id']) }}"
                                                                    method="post">
                                                                    {{ csrf_field() }}
                                                                    <input name="_method" type="hidden" value="DELETE">
                                                                    <div class="dropdown-menu"
                                                                        aria-labelledby="dropdownMenuButton">
                                                                        <a href="{{ route('guarantors.edit', $guarantor['id']) }}"
                                                                            data-title="{{ _lang('Update Guarantor') }}"
                                                                            class="dropdown-item dropdown-edit ajax-modal"><i
                                                                                class="ti-pencil-alt"></i>&nbsp;{{ _lang('Edit') }}</a>
                                                                        <button class="btn-remove dropdown-item"
                                                                            type="submit"><i
                                                                                class="ti-trash"></i>&nbsp;{{ _lang('Delete') }}</button>
                                                                    </div>
                                                                </form>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <td colspan="2">{{ _lang('Grand Total') }}</td>
                                                    <td colspan="2">
                                                        <b>{{ decimalPlace($guarantors->sum('amount'), currency($loan->currency->name)) }}</b>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane fade" id="collateral">
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <span>{{ _lang('All Collaterals') }}</span>
                                    <a class="btn btn-primary btn-xs ml-auto"
                                        href="{{ route('loan_collaterals.create', ['loan_id' => $loan->id]) }}"><i
                                            class="ti-plus"></i>
                                        {{ _lang('New Collateral') }}</a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mt-2">
                                            <thead>
                                                <tr>
                                                    <th>{{ _lang('Name') }}</th>
                                                    <th>{{ _lang('Collateral Type') }}</th>
                                                    <th>{{ _lang('Serial Number') }}</th>
                                                    <th>{{ _lang('Estimated Price') }}</th>
                                                    <th class="text-center">{{ _lang('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($loancollaterals as $loancollateral)
                                                    <tr data-id="row_{{ $loancollateral->id }}">
                                                        <td class="name">{{ $loancollateral->name }}</td>
                                                        <td class="collateral_type">
                                                            {{ $loancollateral->collateral_type }}
                                                        </td>
                                                        <td class="serial_number">
                                                            {{ $loancollateral->serial_number }}
                                                        </td>
                                                        <td class="estimated_price">
                                                            {{ decimalPlace($loancollateral->estimated_price, currency($loan->currency->name)) }}
                                                        </td>
                                                        <td class="text-center">
                                                            <div class="dropdown">
                                                                <button class="btn btn-primary dropdown-toggle btn-xs"
                                                                    type="button" id="dropdownMenuButton"
                                                                    data-toggle="dropdown" aria-haspopup="true"
                                                                    aria-expanded="false">
                                                                    {{ _lang('Action') }}
                                                                </button>
                                                                <form
                                                                    action="{{ route('loan_collaterals.destroy', $loancollateral['id']) }}"
                                                                    method="post">
                                                                    {{ csrf_field() }}
                                                                    <input name="_method" type="hidden"
                                                                        value="DELETE" />
                                                                    <div class="dropdown-menu"
                                                                        aria-labelledby="dropdownMenuButton">
                                                                        <a href="{{ route('loan_collaterals.edit', $loancollateral['id']) }}"
                                                                            class="dropdown-item dropdown-edit dropdown-edit"><i
                                                                                class="ti-pencil-alt"></i>{{ _lang('Edit') }}</a>
                                                                        <a href="{{ route('loan_collaterals.show', $loancollateral['id']) }}"
                                                                            class="dropdown-item dropdown-view dropdown-view"><i
                                                                                class="ti-eye"></i>{{ _lang('View') }}</a>
                                                                        <button class="btn-remove dropdown-item"
                                                                            type="submit">
                                                                            <i class="ti-trash"></i>
                                                                            {{ _lang('Delete') }}
                                                                        </button>
                                                                    </div>
                                                                </form>
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
                        <div class="tab-pane fade mt-4 border px-3" id="schedule">
                            <div class="report-header">
                                <h4>{{ get_option('company_name') }}</h4>
                                <h5>{{ _lang('Repayments Schedule') }}</h5>
                                <p>{{ $loan->borrower->name }}, {{ _lang('Loan ID') . ': ' . $loan->loan_id }}</p>
                            </div>
                            <table class="table table-bordered report-table">
                                <thead>
                                    <tr>
                                        <th>{{ _lang('Date') }}</th>
                                        <th class="text-right">{{ _lang('Amount to Pay') }}</th>
                                        <th class="text-right">{{ _lang('Principal Amount') }}</th>
                                        <th class="text-right">{{ _lang('Interest') }}</th>
                                        {{-- <th class="text-right">{{ _lang("Late Penalty") }}</th> --}}
                                        <th class="text-right">{{ _lang('Balance') }}</th>
                                        <th class="text-center">{{ _lang('Status') }}</th>
                                        <th class="text-center">{{ _lang('Action') }}</th>
                                        td
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($repayments as $repayment)
                                        <tr>
                                            <td>{{ $repayment->repayment_date }}</td>
                                            <td class="text-right">
                                                {{ decimalPlace($repayment['amount_to_pay'], currency($loan->currency->name)) }}
                                            </td>
                                            <td class="text-right">
                                                {{ decimalPlace($repayment['principal_amount'], currency($loan->currency->name)) }}
                                            </td>
                                            <td class="text-right">
                                                {{ decimalPlace($repayment['interest'], currency($loan->currency->name)) }}
                                            </td>
                                            {{-- <td class="text-right">
                              {{ decimalPlace($repayment['penalty'], currency($loan->currency->name)) }}
                           </td> --}}
                                            <td class="text-right">
                                                {{ decimalPlace($repayment['balance'], currency($loan->currency->name)) }}
                                            </td>
                                            {{-- <td class="text-center">
                              @if ($repayment['status'] == 0 && date('Y-m-d') > $repayment->getRawOriginal('repayment_date'))
                              {!! xss_clean(show_status(_lang('Due'),'danger')) !!}
                              @elseif($repayment['status'] == 0 && date('Y-m-d') < $repayment->getRawOriginal('repayment_date'))
                              {!! xss_clean(show_status(_lang('Unpaid'),'warning')) !!}
                              @else
                              {!! xss_clean(show_status(_lang('Paid'),'success')) !!}
                              @endif
                           </td> --}}

                                            {{-- <td class="text-center">
                                @if ($repayment['status'] == 0 && date('Y-m-d') > $repayment->getRawOriginal('repayment_date'))
                                {!! xss_clean(show_status(_lang('Due'),'danger')) !!}
                                @elseif($repayment['status'] == 0 && date('Y-m-d') < $repayment->getRawOriginal('repayment_date'))
                                {!! xss_clean(show_status(_lang('Unpaid'),'warning')) !!}
                                @else
                                {!! xss_clean(show_status(_lang('Paid'),'success')) !!}
                                @endif
                            </td> --}}

                                            @php
                                                $status = $repayment->status; // Use object property
                                                $today = date('Y-m-d');
                                                // dd($today);
                                                $dueDate = $repayment->getRawOriginal('repayment_date');
                                                // dd($dueDate);
                                            @endphp

                                            <td class="text-center">

                                                {{-- PAID --}}
                                                @if ($status == 1)
                                                    {!! show_status('Paid', 'success') !!}

                                                    {{-- DUE --}}
                                                @elseif($status == 0 && $today > $dueDate)
                                                    {!! show_status('Due', 'danger') !!}

                                                    {{-- UNPAID --}}
                                                @elseif($status == 0 && $today <= $dueDate)
                                                    {!! show_status('Unpaid', 'warning') !!}
                                                @endif

                                            </td>


                                            <td class="text-center">
                                                @if ($repayment->status == 0 && $today <= $dueDate)
                                                    <button class="btn btn-success btn-sm payEmiBtn"
                                                        data-id="{{ $repayment->id }}"
                                                        data-date="{{ $repayment->repayment_date }}">Pay</button>
                                                @endif
                                            </td>






                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            {{-- pay model --}}
                            <div class="modal fade" id="emiPayModal" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Pay EMI</h5>
                                            {{-- <button class="btn-close" data-bs-dismiss="modal"></button> --}}
                                        </div>

                                        <div class="modal-body">

                                            <input type="hidden" id="emi_id">

                                            <div class="form-group">
                                                <label>Amount to Pay</label>
                                                <input type="text" id="amount_to_pay" class="form-control" readonly>
                                            </div>

                                            <div class="form-group mt-2">
                                                <label>Principal</label>
                                                <input type="text" id="principal" class="form-control" readonly>
                                            </div>

                                            <div class="form-group mt-2">
                                                <label>Interest</label>
                                                <input type="text" id="interest" class="form-control" readonly>
                                            </div>

                                            {{-- <div class="form-group mt-2">
                                        <label>Penalty Perday</label>
                                        <input type="text" id="penalty_perday" class="form-control" readonly>
                                    </div>


                                    <div class="form-group mt-2">
                                        <label>Late Penalty</label>
                                        <input type="text" id="late_penalty" class="form-control">
                                        <small>Admin can edit manually</small>
                                    </div> --}}

                                            <div class="form-group mt-2">
                                                <label>Late Penalty of <span id="days_late"></span> days</label>
                                                <div class="input-group">
                                                    <input type="text" id="late_penalty" class="form-control">
                                                    <div class="input-group-append">
                                                        <span class="input-group-text" id="penalty_perday">0%</span>
                                                    </div>
                                                </div>
                                                <small>Admin can edit manually. (Penalty Per Day)</small>
                                            </div>



                                        </div>

                                        <div class="modal-footer">
                                             <button class="btn btn-success" id="confirmEmiPayBtn">Cash</button> 
                                             <button type="button" class="btn btn-primary" id="razorpayBtn">Pay with Razorpay</button>
                                        </div>



                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="tab-pane fade mt-4" id="repayments">
                            <div class="report-header">
                                <h4>{{ get_option('company_name') }}</h4>
                                <h5>{{ _lang('Loan Payments') }}</h5>
                                <p>{{ $loan->borrower->name }}, {{ _lang('Loan ID') . ': ' . $loan->loan_id }}</p>
                            </div>
                            <table class="table table-bordered report-table" id="repayments-table">
                                <thead>
                                    <tr>
                                        <th>{{ _lang('Date') }}</th>
                                        <th class="text-right">{{ _lang('Principal Amount') }}</th>
                                        <th class="text-right">{{ _lang('Interest') }}</th>
                                        <th class="text-right">{{ _lang('Late Penalty') }}</th>
                                        <th class="text-right">{{ _lang('Total Amount') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($payments as $payment)
                                        <tr>
                                            <td>{{ $payment->paid_at }}</td>
                                            <td class="text-right">
                                                {{ decimalPlace($payment['repayment_amount'] - $payment['interest'], currency($loan->currency->name)) }}
                                            </td>
                                            <td class="text-right">
                                                {{ decimalPlace($payment['interest'], currency($loan->currency->name)) }}
                                            </td>
                                            <td class="text-right">
                                                {{ decimalPlace($payment['late_penalties'], currency($loan->currency->name)) }}
                                            </td>
                                            <td class="text-right">
                                                {{ decimalPlace($payment['total_amount'], currency($loan->currency->name)) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Agreement upload --}}

                        <div class="tab-pane fade" id="agreement_docs">
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <span>{{ _lang('Agreement Documents') }}</span>
                                    <a class="btn btn-primary btn-xs ml-auto ajax-modal"
                                        href="{{ route('loan.agreement.create', $loan->id) }}"
                                        data-title="{{ _lang('Upload Agreement Document') }}">
                                        <i class="ti-plus"></i> {{ _lang('Upload Document') }}
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-bordered mt-2">
                                            <thead>
                                                <tr>
                                                    <th>{{ _lang('Name') }}</th>
                                                    <th>{{ _lang('Uploaded At') }}</th>
                                                    <th class="text-center">{{ _lang('Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($loan->agreement_documents as $doc)
                                                    <tr>
                                                        <td>{{ $doc->name }}</td>
                                                        <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                                                        <td class="text-center">
                                                            {{-- <a href="{{ asset('public/uploads/agreements/'.$doc->file) }}" class="btn btn-xs btn-outline-primary" target="_blank">
                                                <i class="ti-eye"></i> {{ _lang("View") }}
                                            </a> --}}

                                                            <a href="javascript:void(0)"
                                                                onclick="openDocModal('{{ asset('uploads/agreements/' . $doc->file) }}')"
                                                                class="btn btn-xs btn-outline-primary">

                                                                <i class="ti-eye"></i> View
                                                            </a>

                                                            <a href="{{ asset('uploads/agreements/' . $doc->file) }}"
                                                                download class="btn btn-xs btn-outline-success">
                                                                <i class="ti-download"></i> {{ _lang('Download') }}
                                                            </a>
                                                            {{-- <form action="{{ route('loan.agreement.delete', $doc->id) }}" method="POST" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-xs btn-outline-danger"><i class="ti-trash"></i> {{ _lang("Delete") }}</button>
                                            </form> --}}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                @if ($loan->agreement_documents->isEmpty())
                                                    <tr>
                                                        <td colspan="3" class="text-center">
                                                            {{ _lang('No Documents Uploaded') }}</td>
                                                    </tr>
                                                @endif
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>





                            {{--
                <div class="modal fade" id="docPreviewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-md">
                        <div class="modal-content">

                            <div class="modal-header">
                                <h5 class="modal-title">Document Preview</h5>
                                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span>&times;</span>
                                </button>
                            </div>

                            <div class="modal-body" style="height: 85vh;">
                                <iframe id="docFrame" src="" width="100%" height="100%" style="border: none;"></iframe>
                            </div>

                        </div>
                    </div>
                </div> --}}

                            <div class="modal fade" id="docPreviewModal" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">

                                        <div class="modal-header">
                                            <h5 class="modal-title">Document Preview</h5>
                                            {{-- <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                                    <span>&times;</span>
                                </button> --}}
                                        </div>

                                        <div class="modal-body" style="height: 75vh;"> <!-- 85vh → 90vh -->
                                            <iframe id="docFrame" src="" width="100%" height="100%"
                                                style="border: none;"></iframe>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('js-script')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
   (function($) {
       "use strict";

   	$('.nav-tabs a').on('shown.bs.tab', function(event){
   		var tab = $(event.target).attr("href");
   		var url = "{{ route('loans.show',$loan->id) }}";
   	    history.pushState({}, null, url + "?tab=" + tab.substring(1));
   	});

   	@if(isset($_GET['tab']))
   	   $('.nav-tabs a[href="#{{ $_GET['tab'] }}"]').tab('show');
   	@endif

   })(jQuery);
</script>

<script>
    function openDocModal(url) {
        document.getElementById("docFrame").src = url;
        var myModal = new bootstrap.Modal(document.getElementById('docPreviewModal'));
        myModal.show();
    }
</script>

<script>
    let razorAmount = 0;

    $('.payEmiBtn').on('click', function () {
        let id = $(this).data('id');

        $.get("{{ url('/loan/repayment/details') }}/" + id, function (res) {
            $('#emi_id').val(res.id);
            $('#principal').val(res.principal_amount);
            $('#interest').val(res.interest);
            $('#late_penalty').val(res.penalty);
            $('#penalty_perday').text(res.penalty_perday + "%");
            $('#days_late').text(res.days_late);
            $('#amount_to_pay').val(res.total);

            razorAmount = res.total; // Set amount for Razorpay

            $('#late_penalty').off().on('keyup change', function () {
                let p = parseFloat($('#principal').val());
                let i = parseFloat($('#interest').val());
                let lp = parseFloat($(this).val()) || 0;
                $('#amount_to_pay').val(p + i + lp);
            });

            $('#emiPayModal').modal('show');
        });
    });

    $('#confirmEmiPayBtn').on('click', function () {
        let data = {
            emi_id: $('#emi_id').val(),
            late_penalty: $('#late_penalty').val(),
            _token: "{{ csrf_token() }}"
        };

        $.post("{{ url('/loan/repayment/pay') }}", data, function (res) {
            if(res.status){
                location.reload();
            }
        });
    });

    // Razorpay Button Click
    $('#razorpayBtn').on('click', function() {
        if (!razorAmount || razorAmount == 0) {
            alert('Amount not found');
            return;
        }

        let options = {
            "key": "{{ env('RAZORPAY_KEY') }}",
            "amount": razorAmount * 100, // in paisa
            "currency": "INR",
            "name": "Loan Payment",
            "description": "Loan EMI Payment",
            "image": "{{ asset('images/logo.png') }}",
            "handler": function(response) {
                let data = {
                    emi_id: $('#emi_id').val(),
                    razorpay_payment_id: response.razorpay_payment_id,
                    _token: "{{ csrf_token() }}"
                };

                $.post("{{ url('/loan/repayment/pay') }}", data, function(res) {
                    if (res.status) {
                        location.reload();
                    } else {
                        alert('Payment successful but DB update failed');
                    }
                });
            },
            "theme": {
                "color": "#0d6efd"
            }
        };

        var rzp1 = new Razorpay(options);
        rzp1.open();
    });
</script>

@endsection



{{-- @section('js-script')
    <script>
        (function($) {
            "use strict";

            $('.nav-tabs a').on('shown.bs.tab', function(event) {
                var tab = $(event.target).attr("href");
                var url = "{{ route('loans.show', $loan->id) }}";
                history.pushState({}, null, url + "?tab=" + tab.substring(1));
            });

            @if (isset($_GET['tab']))
                $('.nav-tabs a[href="#{{ $_GET['tab'] }}"]').tab('show');
            @endif

        })(jQuery);
    </script>
    <script>
        function openDocModal(url) {
            document.getElementById("docFrame").src = url;

            var myModal = new bootstrap.Modal(document.getElementById('docPreviewModal'));
            myModal.show();
        }
    </script>
    <script>
        $('.payEmiBtn').on('click', function() {

            let id = $(this).data('id');

            $.get("{{ url('/loan/repayment/details') }}/" + id, function(res) {

                // backend से आने वाले values set करें
                $('#emi_id').val(res.id);
                $('#principal').val(res.principal_amount);
                $('#interest').val(res.interest);
                $('#late_penalty').val(res.penalty); // auto calculated penalty
                console.log(res.penalty_perday);
                // console.log(res.days_late);
                console.log(res.principal_amount);

                // $('#penalty_perday').val(res.penalty_perday);  // perday penalty
                // $('#penalty_perday').val(res.penalty_perday || 0);
                $('#penalty_perday').text(res.penalty_perday + "%");
                $('#days_late').text(res.days_late);

                $('#amount_to_pay').val(res.total);
                console.log(res.total); // total = principal + interest + penalty

                // अगर admin कुछ edit करे penalty को, total फिर से auto update होना चाहिए
                $('#late_penalty').off().on('keyup change', function() {

                    let p = parseFloat($('#principal').val());
                    let i = parseFloat($('#interest').val());
                    let lp = parseFloat($(this).val()) || 0;

                    $('#amount_to_pay').val(p + i + lp);
                });

                $('#emiPayModal').modal('show');
            });

        });

        $('#confirmEmiPayBtn').on('click', function() {

            let data = {
                emi_id: $('#emi_id').val(),
                late_penalty: $('#late_penalty').val(),
                _token: "{{ csrf_token() }}"
            };

            $.post("{{ url('/loan/repayment/pay') }}", data, function(res) {
                if (res.status) {
                    location.reload();
                }
            });

        });
    </script>
@endsection --}}


