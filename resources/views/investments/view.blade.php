@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <span class="panel-title">{{ _lang('Investment Details') }}</span>
                </div>

                <div class="card-body">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab"
                                href="#investment_details">{{ _lang('Investment Details') }}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#schedule">{{ _lang('Payout Schedule') }}</a>
                        </li>
                        
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#interest_earnings">{{ _lang('Interest Earnings') }}</a>
                        </li>
                         @if(!$investment->is_locked)
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('investments.edit', $investment->id) }}">
                                <i class="ti-pencil-alt"></i> {{ _lang('Edit') }}
                            </a>
                        </li>
                        @endif

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('members.show', $investment->customer_id) }}">
                                <i class="ti-user"></i> Member Details
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab"
                                href="#agreement_docs">{{ _lang('Agreement Documents') }}</a>
                        </li>
                    </ul>

                    <!-- Tab Panes -->
                    <div class="tab-content">
                        <!-- INVESTMENT DETAILS TAB -->
                        <div class="tab-pane active mt-4" id="investment_details">
                            <table class="table table-bordered">
                                <tr>
                                    <td>{{ _lang('Investment ID') }}</td>
                                    <td>{{ $investment->investment_code }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Investor') }}</td>
                                    <td>{{ $investment->customer->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Currency') }}</td>
                                    <td>{{ $investment->currency->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Investment Type') }}</td>
                                    <td>{{ ucfirst($investment->investment_type) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Contribution Type') }}</td>
                                    <td>{{ ucfirst($investment->contribution_type) }}</td>
                                </tr>

                                <tr>
                                    <td>{{ _lang('Status') }}</td>
                                    <td>
                                        @if ($investment->status == 0)
                                            {!! xss_clean(show_status(_lang('Pending'), 'warning')) !!}
                                        @elseif($investment->status == 1)
                                            {!! xss_clean(show_status(_lang('Approved'), 'success')) !!}
                                        @elseif($investment->status == 2)
                                            {!! xss_clean(show_status(_lang('Rejected'), 'danger')) !!}
                                            {{-- @elseif($investment->status == 3)
                                            {!! xss_clean(show_status(_lang('Cancelled'), 'danger')) !!} --}}
                                        @endif
                                        @if ($investment->status == 0)
                                            <a class="btn btn-outline-primary btn-xs"
                                                href="{{ route('investment.approve', $investment['id']) }}"><i
                                                    class="ti-check-box"></i>&nbsp;{{ _lang('Click to Approve') }}</a>
                                            <a class="btn btn-outline-danger btn-xs float-right"
                                                href="{{ route('investment.reject', $investment['id']) }}"><i
                                                    class="ti-close"></i>&nbsp;{{ _lang('Click to Reject') }}</a>
                                        @endif
                                    </td>
                                </tr>

                                <tr>
                                    <td>{{ _lang('Risk Level') }}</td>
                                    <td>{{ ucfirst($investment->risk_level) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Investment Amount') }}</td>
                                    <td>{{ decimalPlace($investment->investment_amount, currency($investment->currency->name ?? '')) }}
                                        {{--
                                        @if ($investment->disbused)
                                            <span class="text-success">({{ $investment->disbused }})</span>
                                        @endif --}}

                                        {{-- <span class="text-success">({{ $investment->disbused ?? 'Not Disbused' }})</span> --}}
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Maturity Amount') }}</td>
                                    <td class="text-success">
                                        {{ decimalPlace($investment->maturity_amount, currency($investment->currency->name ?? '')) }}

                                        @if ($investment->disbused)
                                            <span class="text-success">({{ $investment->disbused }})</span>
                                        @endif
                                    </td>

                                </tr>
                                <tr>
                                    <td>{{ _lang('Start Date') }}</td>
                                    <!--<td>{{ $investment->start_date }}</td>-->
                                    <td>{{ \Carbon\Carbon::parse($investment->start_date)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Maturity Date') }}</td>
                                    <!--<td>{{ $investment->maturity_date }}</td>-->
                                    <td>{{ \Carbon\Carbon::parse($investment->maturity_date)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Description') }}</td>
                                    <td>{{ $investment->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Remarks') }}</td>
                                    <td>{{ $investment->remarks ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <td>{{ _lang('Created By') }}</td>
                                    <td>{{ $investment->created_by->name }}</td>
                                </tr>

                                <tr>
                                    <td>{{ _lang('Creator ID') }}</td>
                                    <td>{{ $investment->created_user_id }}</td>
                                </tr>

                            </table>
                        </div>

                        @if ($investment->status == 1)
                            <!-- PAYMENT SCHEDULE TAB -->
                            <div class="tab-pane fade mt-4 border px-3" id="schedule">
                                <div class="report-header text-center mb-4">
                                    <h4>{{ get_option('company_name') }}</h4>
                                    <h5>{{ _lang('Investment Payout Schedule') }}</h5>
                                    <p>{{ $investment->customer->name ?? '' }},
                                        {{ _lang('Investment ID') . ': ' . $investment->id }}</p>
                                </div>

                                <table class="table table-bordered report-table" id="investment_schedule_table">
                                    <thead>
                                        <tr>
                                            <th>{{ _lang('Month') }}</th>
                                            <th>{{ _lang('Payout Date') }}</th>
                                            <th class="text-right">{{ _lang('Principal') }}</th>
                                            <th class="text-right">{{ _lang('Expected Returns (%)') }}</th>
                                            <th class="text-right">{{ _lang('Monthly Interest') }}</th>
                                            <th class="text-right">{{ _lang('Total Payout') }}</th>
                                            <th class="text-right">{{ _lang('Status') }}</th>
                                            <th class="text-right">{{ _lang('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($schedule as $row)
                                            <tr>
                                                <td>{{ $row->month_no }}</td>
                                                <td>{{ \Carbon\Carbon::parse($row->payment_date)->format('d M Y') }}</td>
                                                <td class="text-right">{{ number_format($row->principal, 2) }}</td>
                                                <td class="text-right">{{ number_format($investment->expected_return, 2) }}
                                                </td>
                                                <td class="text-right">{{ number_format($row->profit, 2) }}</td>
                                                <td class="text-right">{{ number_format($row->total_amount, 2) }}</td>
                                                <td class="text-right">
                                                    @if ($row->status == 'paid')
                                                        <div class="mb-1">
                                                            <span class="badge bg-success">Paid</span>
                                                            <span class="text-muted small">({{ ucfirst($row->payment_mode) }})</span>
                                                        </div>
                                                        @if ($row->payment_mode == 'cash')
                                                            <div class="small">
                                                                <strong>Collector:</strong> {{ $row->collector_name }}<br>
                                                                @if ($row->admin_received_status == 1)
                                                                    <span class="text-success"><i class="ti-check"></i> Received by Admin</span>
                                                                @else
                                                                    <span class="text-warning"><i class="ti-timer"></i> Pending Admin Receipt</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @elseif($row->status == 'due')
                                                        <span class="badge bg-warning">Due</span>
                                                    @else
                                                        <span class="badge bg-danger text-white p-1">Upcoming</span>
                                                    @endif
                                                </td>
                                                <td class="text-right">
                                                    @if ($row->status != 'paid')
                                                        <button class="btn btn-success btn-sm payInvestmentBtn"
                                                            data-repayment="{{ $row->id }}">Pay Now</button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="8" class="text-center text-muted">
                                                    {{ _lang('No schedule available for this Investment') }}</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <div class="tab-pane fade mt-4" id="interest_earnings">
                            <table class="table table-bordered data-table">
                                <thead>
                                    <tr>
                                        <th>{{ _lang('Date') }}</th>
                                        <th class="text-right">{{ _lang('Amount') }}</th>
                                        <th>{{ _lang('Description') }}</th>
                                        <th>{{ _lang('Period') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wallet_transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->created_at->format('d M, Y') }}</td>
                                        <td class="text-right text-success">+ {{ decimalPlace($transaction->amount) }}</td>
                                        <td>{{ $transaction->note }}</td>
                                        <td>{{ $transaction->posted_from }} to {{ $transaction->posted_to }}</td>
                                    </tr>
                                    @endforeach
                                    @if($wallet_transactions->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center">{{ _lang('No interest earnings found') }}</td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>


                        {{-- Agreement upload --}}

                        <div class="tab-pane fade" id="agreement_docs">
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <span>{{ _lang('Agreement Documents') }}</span>
                                    <a class="btn btn-primary btn-xs ml-auto ajax-modal"
                                        href="{{ route('investment.agreement.create', $investment->id) }}"
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
                                                @foreach ($investment->agreement_documents as $doc)
                                                    <tr>
                                                        <td>{{ $doc->name }}</td>
                                                        <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0)"
                                                                onclick="openDocModal('{{ asset('uploads/investments/' . $doc->file) }}')"
                                                                class="btn btn-xs btn-outline-primary">
                                                                <i class="ti-eye"></i> View
                                                            </a>

                                                            <a href="{{ asset('uploads/investments/' . $doc->file) }}"
                                                                download class="btn btn-xs btn-outline-success">
                                                                <i class="ti-download"></i> Download
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if ($investment->agreement_documents->isEmpty())
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

                        {{-- model --}}

                        <div class="modal fade" id="investmentPayModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Pay Principal Amount</h5>
                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden" id="investment_id">

                                        <div class="form-group">
                                            <label>Principal Amount</label>
                                            <input type="text" id="principal" readonly class="form-control">
                                        </div>

                                        <div class="form-group mt-2">
                                            <label>Monthly Interest (View Only)</label>
                                            <input type="text" id="interest" readonly class="form-control">
                                        </div>

                                        <div class="form-group mt-2">
                                            <label>Total Payable (Principal Only)</label>
                                            <input type="text" id="total_payable" readonly class="form-control">
                                        </div>

                                        <!--<div class="form-group mt-2">-->
                                        <!--    <label>Late Penalty</label>-->
                                        <!--    <input type="number" id="late_penalty" class="form-control" value="0">-->
                                        <!--</div>-->

                                    </div>

                                    {{-- <div class="modal-footer">
                                        <button class="btn btn-success" id="confirmInvestmentPayBtn">Pay Now</button>
                                    </div> --}}

                                    <div class="modal-footer d-flex justify-content-between">

                                        <!-- COD / Manual Pay -->
                                        <button class="btn btn-success rounded-pill px-4" id="confirmInvestmentPayBtn">
                                            Pay (Cash / COD)
                                        </button>

                                        <!-- Razorpay Pay -->
                                        <button class="btn btn-primary rounded-pill px-4" id="razorpayBtn">
                                            Pay with Razorpay
                                        </button>

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
    <script>
        (function($) {
            "use strict";


            $('.nav-tabs a').on('shown.bs.tab', function(event) {
                var tab = $(event.target).attr("href");
                var url = "{{ route('investments.show', $investment->id) }}";
                history.pushState({}, null, url + "?tab=" + tab.substring(1));
            });

            @if (isset($_GET['tab']))
                $('.nav-tabs a[href="#{{ $_GET['tab'] }}"]').tab('show');
            @endif

            // Initialize DataTable
            if (!$.fn.DataTable.isDataTable('#investment_schedule_table')) {
                $('#investment_schedule_table').DataTable({
                    retrieve: true
                });
            }

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
        $(document).on('click', '.payInvestmentBtn', function() {

            let id = $(this).data('repayment');

            // $.get('/investment/repayment/details/' + id, function(res) {
            $.get("{{ url('investment/repayment/details') }}/" + id, function(res) {

                console.log(res);
                if (res.status === false) {
                    alert(res.msg);
                    return;
                }

                $('#investment_id').val(res.id);
                $('#principal').val(res.principal_amount);
                // console.log('#principal');
                $('#interest').val(res.monthly_interest);
                $('#total_payable').val(res.principal_amount); // ✅ principal only
                $('#late_penalty').val(res.late_penalty ?? 0);

                var myModal = new bootstrap.Modal(document.getElementById('investmentPayModal'));
                myModal.show();
            });

        });


        $('#confirmInvestmentPayBtn').on('click', function() {

            let data = {
                emi_id: $('#investment_id').val(),
                late_penalty: $('#late_penalty').val(),
                _token: "{{ csrf_token() }}"
            };

            // $.post("{{ url('/investment/repayment/pay') }}", data, function(res) {
            $.post("{{ url('investment/repayment/pay') }}", data, function(res) {

                if (res.status) {
                    location.reload();
                } else {
                    alert(res.msg);
                }

            });

        });



        let razorAmount = 0;


        $(document).on('click', '.payInvestmentBtn', function() {
            setTimeout(function() {
                razorAmount = $('#principal').val();
                console.log(razorAmount);
            }, 300);
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
                "name": "Investment Payment",
                "description": "Monthly Investment Installment",
                "image": "{{ asset('images/logo.png') }}",
                "handler": function(response) {

                    // After successful payment — mark paid in DB
                    let data = {
                        emi_id: $('#investment_id').val(),
                        razorpay_payment_id: response.razorpay_payment_id,
                        _token: "{{ csrf_token() }}"
                    };

                    $.post("{{ url('investment/repayment/pay') }}", data, function(res) {

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

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
@endsection
