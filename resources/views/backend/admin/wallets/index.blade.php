@extends('layouts.app')

@section('content')
    <style>
        /* Modern card styles */
        .modern-card {
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            border: none;
            overflow: hidden;
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        }

        .modern-card .card-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            padding: 12px 20px;
        }

        .modern-card .card-body {
            padding: 20px;
        }

        /* Modern form elements */
        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Modern table */
        .modern-table {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        }

        .modern-table thead th {
            background-color: #f8f9fa;
            font-weight: 600;
            border-bottom: 2px solid #e9ecef;
            padding: 12px 8px;
            font-size: 0.85rem;
        }

        .modern-table tbody tr:hover {
            background-color: #f8f9fa;
        }

        .modern-table tbody td {
            padding: 8px;
            font-size: 0.85rem;
        }

        /* Modern buttons */
        .btn-modern {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }

        .btn-primary-modern {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
        }

        .btn-info-modern {
            background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
            border: none;
            color: white;
        }

        .btn-success-modern {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            color: white;
        }

        /* Keep payment buttons exactly as provided */
        .custom-btn {
            padding: 6px 12px;
            font-size: 13px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: inline-block;
            text-align: center;
            text-decoration: none;
            margin: 2px;
        }

        .custom-btn-cod {
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
        }

        .custom-btn-cod:hover {
            background: linear-gradient(135deg, #218838, #1ea085);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
        }

        .custom-btn-razorpay {
            background: linear-gradient(135deg, #007bff, #6f42c1);
            color: white;
        }

        .custom-btn-razorpay:hover {
            background: linear-gradient(135deg, #0069d9, #5a32a3);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        }

        .btn-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            justify-content: center;
            margin-top: 15px;
        }

        .btn-container .custom-btn {
            flex: 1;
            min-width: 120px;
            max-width: 200px;
        }

        @media (max-width: 576px) {
            .btn-container .custom-btn {
                flex: 1 1 100%;
                max-width: 100%;
            }
        }

        /* Summary box styling */
        .summary-box {
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .summary-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .summary-item:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }

        .summary-label {
            color: #6c757d;
            font-weight: 500;
        }

        .summary-value {
            font-weight: 600;
        }

        /* Payment details styling */
        .payment-info {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
        }

        .payment-item {
            flex: 1;
            min-width: 200px;
            background: #f8f9fa;
            padding: 12px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .payment-label {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 5px;
        }

        .payment-value {
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .modern-card .card-body {
                padding: 15px;
            }

            .form-control {
                padding: 8px 12px;
            }
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="modern-card">
                    <div class="card-header">
                        <h4 class="mb-0">{{ _lang('Wallet Management') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="modern-card">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ _lang('Add Wallet Money') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="{{ route('admin.wallets.add-money') }}" method="POST">
                                            @csrf
                                            <div class="form-group mb-3">
                                                <label
                                                    class="form-label font-weight-bold">{{ _lang('Select User') }}</label>
                                                <select class="form-control select2" name="user_id" required>
                                                    <option value="">{{ _lang('Select One') }}</option>
                                                    @foreach ($users as $user)
                                                        <option value="{{ $user->id }}">{{ $user->name }} -
                                                            {{ _lang('Current Balance') }}:
                                                            {{ $user->wallet_amount ? $user->wallet_amount : 0 }}</option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="form-label font-weight-bold">{{ _lang('Amount') }}</label>
                                                <input type="number" class="form-control" name="amount" step="0.01"
                                                    required>
                                            </div>

                                            <div class="form-group mb-3">
                                                <label class="form-label font-weight-bold">{{ _lang('Note') }}</label>
                                                <textarea class="form-control" name="note" rows="3"></textarea>
                                            </div>

                                            <div class="form-group">
                                                <button type="submit"
                                                    class="btn btn-modern btn-primary-modern">{{ _lang('Add Money') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="modern-card d-flex flex-column">
                                    <div class="card-header">
                                        <h5 class="mb-0">{{ _lang('Wallet History') }}</h5>
                                    </div>
                                    <div class="card-body d-flex align-items-center justify-content-center">
                                        <a href="{{ route('admin.wallets.history') }}"
                                            class="btn btn-modern btn-info-modern">
                                            <i class="fas fa-history me-2"></i> {{ _lang('View Transaction History') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- User Commission Details and Payment Details side by side -->
                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="modern-card" id="userDetailsCard" style="display:none;">
                                    <div class="card-header">
                                        <h5 class="mb-0">User Commission Details</h5>
                                    </div>
                                    <div class="card-body">
                                        <!-- FILTERS -->
                                        <div class="row mb-3">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label font-weight-bold">Date From</label>
                                                <input type="date" id="date_from" class="form-control">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label font-weight-bold">Date To</label>
                                                <input type="date" id="date_to" class="form-control">
                                            </div>
                                        </div>

                                        <div class="table-responsive mb-3">
                                            <table class="table modern-table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>ID</th>
                                                        <th>Type</th>
                                                        <th>Amount</th>
                                                        <th>Date</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="ordersTableBody">
                                                    <!-- Orders load here -->
                                                </tbody>
                                            </table>
                                        </div>

                                        <h5 class="font-weight-bold">User Summary</h5>
                                        <div id="userDetails" class="summary-box mt-2"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-6 col-md-12 mb-4">
                                <div class="modern-card" id="paymentDetailsCard" style="display:none;">
                                    <div class="card-header">
                                        <h5 class="mb-0">Payment Details</h5>
                                    </div>
                                    <div class="card-body" id="paymentDetailsBody">
                                        <!-- AJAX loaded user & account details will appear here -->
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const orderViewUrl = "{{ route('orders.index.simple') }}";
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <Script>
        function showToast(type, message) {
            Swal.fire({
                toast: true,
                position: 'top-end',
                icon: type, // success, error, warning, info
                title: message,
                showConfirmButton: false,
                timer: 2500
            });
        }
    </Script>


    <script>
        $(document).ready(function() {
            function loadUserData() {
                let userId = $('select[name="user_id"]').val();
                let orderStatus = $('#order_status').val();
                let dateFrom = $('#date_from').val();
                let dateTo = $('#date_to').val();

                if (userId === "") {
                    $('#userDetailsCard').hide();
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.wallet.userDetails', '') }}/" + userId,
                    type: "GET",
                    data: {
                        order_status: orderStatus,
                        date_from: dateFrom,
                        date_to: dateTo
                    },
                    success: function(data) {
                        $('#userDetailsCard').show();

                        let summaryHtml = `
                        <div class="summary-item">
                            <span class="summary-label">Name:</span>
                            <span class="summary-value">${data.name}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Total Business Amount:</span>
                            <span class="summary-value">${data.total_order_amount}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Percent:</span>
                            <span class="summary-value">${data.percent}%</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Commission:</span>
                            <span class="summary-value">${data.commission}</span>
                        </div>
                        <div class="summary-item">
                            <span class="summary-label">Final Total Amount:</span>
                            <span class="summary-value">${data.final_total}</span>
                        </div>
                        <div class="mt-3 text-center">
                            <button id="processPayBtn" class="btn btn-modern btn-success-modern">Proceed to Pay</button>
                        </div>
                    `;

                        $('#userDetails').html(summaryHtml);

                        // ====== TABLE DATA ======
                        let rows = "";

                        if (data.mixed_data && data.mixed_data.length > 0) {
                            data.mixed_data.forEach(function(item, index) {

                                let viewLink = item.type;
                                if (item.type === 'Order') {
                                    viewLink =
                                        `<a href="${orderViewUrl}?order_id=${item.id}" target="_blank" class="text-decoration-none">${item.type}</a>`;
                                }
                                // Add other links if needed e.g. for FD/Loan view

                                rows += `
                                <tr>
                                    <td>${item.id}</td>
                                    <td>${viewLink}</td>
                                    <td>${item.amount}</td>
                                    <td>${ new Date(item.date).toLocaleDateString('en-GB') }</td>
                                </tr>
                            `;
                            });
                        } else {
                            rows = `<tr><td colspan="4" class="text-center">No records found</td></tr>`;
                        }


                        $('#ordersTableBody').html(rows);
                    }
                });
            }

            $('select[name="user_id"], #order_status, #date_from, #date_to').on('change', loadUserData);

            $(document).on('click', '#processPayBtn', function() {
                let userId = $('select[name="user_id"]').val();
                let orderStatus = $('#order_status').val();
                let dateFrom = $('#date_from').val();
                let dateTo = $('#date_to').val();

                if (!userId) return;

                $.ajax({
                    url: "{{ route('admin.wallet.userAccountDetails', '') }}/" + userId,
                    type: "GET",
                    data: {
                        order_status: orderStatus,
                        date_from: dateFrom,
                        date_to: dateTo
                    },
                    success: function(data) {
                        let html = `
                        <div class="payment-info">
                            <div class="payment-item">
                                <div class="payment-label">Name</div>
                                <div class="payment-value">${data.name}</div>
                            </div>
                            <div class="payment-item">
                                <div class="payment-label">Email</div>
                                <div class="payment-value">${data.email}</div>
                            </div>
                            <div class="payment-item">
                                <div class="payment-label">Bank Name</div>
                                <div class="payment-value">${data.bank_name}</div>
                            </div>
                            <div class="payment-item">
                                <div class="payment-label">Account Number</div>
                                <div class="payment-value">${data.account_number}</div>
                            </div>
                            <div class="payment-item">
                                <div class="payment-label">IFSC</div>
                                <div class="payment-value">${data.ifsc_code}</div>
                            </div>
                           {{-- <div class="payment-item">
                                <div class="payment-label">Commission to Pay</div>
                                <div class="payment-value">${data.commission}</div>
                            </div> --}}

                            <div class="payment-item">
                                <div class="payment-label">Commission to Pay</div>
                                <div class="payment-value" id="commissionAmount">${data.commission}</div>
                            </div>

                        </div>
                        <div class="btn-container">
                            <button class="custom-btn custom-btn-cod" id="codPaymentBtn">Pay With Cash</button>
                            <button class="custom-btn custom-btn-razorpay" id="razorpayPaymentBtn">Pay With Razorpay</button>
                        </div>
                    `;
                        $('#paymentDetailsBody').html(html);
                        $('#paymentDetailsCard').show();
                    }
                });
            });






            //cod
            //     $(document).on('click', '#codPaymentBtn', function() {
            //     let userId = $('select[name="user_id"]').val();
            //     let commission = $('#paymentDetailsBody').find('strong:contains("Commission to Pay")').next().text();

            //     $.ajax({
            //         // url: '/admin/wallet/pay-cod/' + userId, // create this route
            //         url: "{{ route('admin.wallet.payCOD', '') }}/" + userId,
            //         type: 'POST',
            //         data: {
            //             _token: '{{ csrf_token() }}',
            //             amount: commission
            //         },
            //         success: function(res) {
            //             alert('COD Payment marked successfully!');
            //             $('#paymentDetailsCard').hide();
            //             // optionally reload user wallet summary
            //         },
            //         error: function(err) {
            //             alert('Something went wrong!');
            //         }
            //     });
            // });


            // $(document).on('click', '#codPaymentBtn', function() {
            //     let userId = $('select[name="user_id"]').val();
            //     let commission = $('#commissionAmount').text().trim();

            //     if (!userId) {
            //         alert("Please select a user first.");
            //         return;
            //     }

            //     // If commission = 0, stop request
            //     if (commission === 0) {
            //         alert("There is no commission to pay for this transaction.");
            //         return;
            //     }
            //     $.ajax({
            //         url: "{{ route('admin.wallet.payCOD', '') }}/" + userId,
            //         type: 'POST',
            //         data: {
            //             _token: '{{ csrf_token() }}',
            //             amount: commission
            //         },
            //         success: function(res) {
            //             alert('COD Payment marked successfully!');
            //             $('#paymentDetailsCard').hide();
            //         },
            //         error: function(err) {
            //             alert('Something went wrong!');
            //         }
            //     });
            // });



            $(document).on('click', '#codPaymentBtn', function() {

                let userId = $('select[name="user_id"]').val();
                let commission = parseFloat($('#commissionAmount').text().trim()) || 0;

                if (!userId) {
                    // alert("Please select a user first.");
                    showToast('warning', "Please select a user first.");
                    return;
                }

                // If commission = 0, stop request
                if (commission === 0) {
                    // alert("There is no commission to pay for this transaction.");
                    showToast('info', "There is no commission to pay for this transaction.");
                    return;
                }

                $.ajax({
                    url: "{{ route('admin.wallet.payCOD', '') }}/" + userId,
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        amount: commission
                    },
                    success: function(res) {
                        // alert(res.message); // backend ka correct message show karega
                        showToast('success', res.message ||
                            "COD Payment recorded successfully.");
                        $('#paymentDetailsCard').hide();
                    },
                    error: function(err) {
                        alert('Something went wrong!');
                    }
                });
            });



        });
    </script>
@endsection
