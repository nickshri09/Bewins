@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <span class="panel-title">{{ _lang('FD Details') }}</span>
                </div>

                <div class="card-body">
                    <!-- Nav Tabs -->
                    <ul class="nav nav-tabs">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#fd_details">{{ _lang('FD Details') }}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#schedule">{{ _lang('Payment Schedule') }}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('fd.edit', $fd->id) }}">
                                <i class="ti-pencil-alt"></i> {{ _lang('Edit') }}
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab"
                                href="#agreement_docs">{{ _lang('Agreement Documents') }}</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('members.show', $fd->customer_id) }}">
                                <i class="ti-user"></i> Member Details
                            </a>
                        </li>




                    </ul>

                    <!-- Tab Panes -->
                    <div class="tab-content">
                        <!-- FD DETAILS TAB -->
                        <div class="tab-pane active mt-4" id="fd_details">
                            <table class="table table-bordered">

                                <tr>
                                    <td>{{ _lang('FD ID') }}</td>
                                    <td>{{ $fd->id }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Customer') }}</td>
                                    <td>{{ $fd->member->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Currency') }}</td>
                                    <td>{{ $fd->currency->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('FD Type') }}</td>
                                    <td>{{ ucfirst($fd->fd_types) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Status') }}</td>
                                    <td>
                                        @if ($fd->status == 0)
                                            {!! xss_clean(show_status(_lang('Pending'), 'warning')) !!}
                                        @elseif($fd->status == 1)
                                            {!! xss_clean(show_status(_lang('Approved'), 'success')) !!}
                                        @elseif($fd->status == 2)
                                            {!! xss_clean(show_status(_lang('Rejected'), 'danger')) !!}
                                            {{-- @elseif($fd->status == 3)
                                            {!! xss_clean(show_status(_lang('Cancelled'), 'danger')) !!} --}}
                                        @endif
                                        @if ($fd->status == 0)
                                            <a class="btn btn-outline-primary btn-xs"
                                                href="{{ route('fd.approve', $fd['id']) }}"><i
                                                    class="ti-check-box"></i>&nbsp;{{ _lang('Click to Approve') }}</a>
                                            <a class="btn btn-outline-danger btn-xs float-right"
                                                href="{{ route('fd.reject', $fd['id']) }}"><i
                                                    class="ti-close"></i>&nbsp;{{ _lang('Click to Reject') }}</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Duration (Months)') }}</td>
                                    <td>{{ $fd->durations }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('FD Amount') }}</td>
                                    <td>{{ decimalPlace($fd->fd_amount, currency($fd->currency->name ?? '')) }}</td>
                                </tr>
                                <tr>
                                    {{-- <td>{{ _lang('Total Paid') }}</td> --}}
                                    <td>{{ _lang('Total Payable') }}</td>
                                    <td class="text-success">
                                        {{ decimalPlace($fd->total_paid, currency($fd->currency->name ?? '')) }}

                                        @if ($fd->disbused)
                                            <span class="text-success">({{ $fd->disbused }})</span>
                                        @endif
                                    </td>
                                </tr>
                                {{-- <tr>
                                    <td>{{ _lang('Due Amount') }}</td>
                                    <td class="text-danger">
                                        {{ decimalPlace($fd->due_Amount, currency($fd->currency->name ?? '')) }}</td>
                                </tr> --}}
                                <tr>
                                    <td>{{ _lang('Interest Rate') }}</td>
                                    <td>{{ $fd->percentage ?? '-' }}</td>
                                </tr>

                                <tr>
                                    <td>{{ _lang('Start Date') }}</td>
                                    <!--<td>{{ $fd->start_date }}</td>-->
                                    <td>{{ \Carbon\Carbon::parse($fd->start_date)->format('d M Y') }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('End Date') }}</td>
                                    <!--<td>{{ $fd->end_date }}</td>-->
                                    <td>{{ \Carbon\Carbon::parse($fd->end_date)->format('d M Y') }}</td>
                                </tr>


                                <tr>
                                    <td>{{ _lang('Description') }}</td>
                                    <td>{{ $fd->description ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td>{{ _lang('Remarks') }}</td>
                                    <td>{{ $fd->remarks ?? '-' }}</td>
                                </tr>
                            </table>
                        </div>

                        @if ($fd->status == 1)
                            <!-- PAYMENT SCHEDULE TAB -->
                            <div class="tab-pane fade mt-4 border px-3" id="schedule">
                                <div class="report-header">
                                    <h4>{{ get_option('company_name') }}</h4>
                                    <h5>{{ _lang('FD Payment Schedule') }}</h5>
                                    <p>{{ $fd->user->name ?? '' }}, {{ _lang('FD ID') . ': ' . $fd->id }}</p>
                                </div>

                                <table class="table table-bordered report-table">
                                    <thead>
                                        <tr>
                                            <th class="text-center">{{ _lang('Month') }}</th>
                                            <th class="text-center">{{ _lang('Payment Date') }}</th>
                                            <th class="text-center">{{ _lang('Principal') }}</th>
                                            <th class="text-center">{{ _lang('Interest') }}</th>
                                            <th class="text-center">{{ _lang('Total Payment') }}</th>
                                            <th class="text-center">{{ _lang('Status') }}</th>
                                            <th class="text-center">{{ _lang('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if (!empty($schedule))
                                            @foreach ($schedule as $row)
                                                <tr>
                                                    <td class="text-center">{{ $row->month_no }}</td>
                                                    <td class="text-center">{{ $row->payment_date }}</td>
                                                    <td class="text-center">{{ $row->principal }}</td>
                                                    <td class="text-center">{{ $row->interest }}</td>
                                                    <td class="text-center">{{ $row->total_amount }}</td>


                                                    <td class="text-center">
                                                        @if ($row->status == 'paid')
                                                         <span class="me-1 text-muted">
                                                        {{ ucfirst($row->payment_mode) }}
                                                    </span>
                                                            <span class="badge bg-success text-white">Paid</span>
                                                        @elseif($row->status == 'due')
                                                            <span class="badge bg-warning">Due</span>
                                                        @else
                                                            <span class="badge bg-danger text-white p-1">Upcoming</span>
                                                        @endif
                                                    </td>

                                                    <td class="text-center">
                                                        @if ($row->status != 'paid')
                                                            <button class="btn btn-success btn-sm payFDBtn"
                                                                data-repayment="{{ $row->id }}">Pay Now</button>
                                                        @endif
                                                    </td>

                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="5" class="text-center text-muted">
                                                    {{ _lang('No schedule available for this FD') }}</td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    {{ _lang('No schedule available for this FD') }}</td>
                            </tr>
                        @endif




                        {{-- Agreement upload --}}

                        <div class="tab-pane fade" id="agreement_docs">
                            <div class="card">
                                <div class="card-header d-flex align-items-center">
                                    <span>{{ _lang('Agreement Documents') }}</span>
                                    <a class="btn btn-primary btn-xs ml-auto ajax-modal"
                                        href="{{ route('fd.agreement.create', $fd->id) }}"
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
                                                @foreach ($fd->agreement_documents as $doc)
                                                    <tr>
                                                        <td>{{ $doc->name }}</td>
                                                        <td>{{ $doc->created_at->format('Y-m-d') }}</td>
                                                        <td class="text-center">
                                                            <a href="javascript:void(0)"
                                                                onclick="openDocModal('{{ asset('public/uploads/fd/' . $doc->file) }}')"
                                                                class="btn btn-xs btn-outline-primary">
                                                                <i class="ti-eye"></i> View
                                                            </a>

                                                            <a href="{{ asset('public/uploads/fd/' . $doc->file) }}"
                                                                download class="btn btn-xs btn-outline-success">
                                                                <i class="ti-download"></i> Download
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach

                                                @if ($fd->agreement_documents->isEmpty())
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

                        {{-- model --}}

                        <div class="modal fade" id="FDPayModal" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">

                                    <div class="modal-header">
                                        <h5 class="modal-title">Pay Principal Amount</h5>
                                    </div>

                                    <div class="modal-body">

                                        <input type="hidden" id="fd_id">

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

                                    <div class="modal-footer d-flex justify-content-end">

                                        <!-- COD / Manual Pay -->
                                       <button class="btn btn-success rounded-pill px-4" id="confirmFDPayBtn">
                                            Pay (COD)
                                        </button> 

                                        <!-- Razorpay Pay -->
                                        <button class="btn btn-primary rounded-pill px-4 shadow-sm" id="razorpayBtn">
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
            // Maintain tab state in URL
            $('.nav-tabs a').on('shown.bs.tab', function(event) {
                var tab = $(event.target).attr("href");
                var url = "{{ route('fd.show', $fd->id) }}";
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
        $(document).on('click', '.payFDBtn', function() {

            let id = $(this).data('repayment');

            // $.get('/investment/repayment/details/' + id, function(res) {
            $.get("{{ url('fd/repayment/details') }}/" + id, function(res) {

                console.log(res);
                if (res.status === false) {
                    alert(res.msg);
                    return;
                }

                $('#fd_id').val(res.id);
                $('#principal').val(res.principal_amount);
                // console.log('#principal');
                $('#interest').val(res.monthly_interest);
                $('#total_payable').val(res.principal_amount); //  principal only
                $('#late_penalty').val(res.late_penalty ?? 0);


                var myModal = new bootstrap.Modal(document.getElementById('FDPayModal'));
                myModal.show();
            });

        });


        $('#confirmFDPayBtn').on('click', function() {

            let data = {
                emi_id: $('#fd_id').val(),
                late_penalty: $('#late_penalty').val(),
                _token: "{{ csrf_token() }}"
            };


            $.post("{{ url('fd/repayment/pay') }}", data, function(res) {

                if (res.status) {
                    location.reload();
                } else {
                    alert(res.msg);
                }

            });

        });



        let razorAmount = 0;

        // Jab modal open ho — amount set karo
        $(document).on('click', '.payFDBtn', function() {
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
                        emi_id: $('#fd_id').val(),
                        razorpay_payment_id: response.razorpay_payment_id,
                        _token: "{{ csrf_token() }}"
                    };

                    $.post("{{ url('fd/repayment/pay') }}", data, function(res) {

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
