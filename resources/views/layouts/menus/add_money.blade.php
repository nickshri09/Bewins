@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-md-12">

                <h4 class="mb-3">Add Money</h4>

                <div class="card">
                    {{-- Blue Section Header --}}
                    <div class="card-header bg-primary text-white rounded-top">
                        <h5 class="mb-0">Add Money to Main Wallet</h5>
                    </div>

                    <div class="card-body">
                        {{-- Bootstrap Tabs Navigation --}}
                        <ul class="nav nav-tabs" id="paymentTab" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="razorpay-tab" data-bs-toggle="tab"
                                    data-bs-target="#razorpay-pane" type="button" role="tab"
                                    aria-controls="razorpay-pane" aria-selected="true">
                                    Pay with Razorpay
                                </button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="upi-tab" data-bs-toggle="tab" data-bs-target="#upi-pane"
                                    type="button" role="tab" aria-controls="upi-pane" aria-selected="false">
                                    Pay with UPI/QR
                                </button>
                            </li>
                        </ul>

                        {{-- Bootstrap Tabs Content --}}
                        <div class="tab-content p-3" id="paymentTabContent">

                            {{-- Razorpay Tab Pane --}}
                            <div class="tab-pane fade show active" id="razorpay-pane" role="tabpanel"
                                aria-labelledby="razorpay-tab" tabindex="0">
                                {{-- Amount Input Form --}}
                                <form id="addMoneyForm" class="mb-4">
                                    @csrf
                                    <div class="row justify-content-center">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="amount" class="form-label">Enter Amount</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">₹</span>
                                                    <input type="number" class="form-control" id="amount" name="amount"
                                                        min="1" step="0.01" required
                                                        placeholder="Enter amount to add">
                                                </div>
                                                <div class="form-text">Minimum amount: ₹1</div>
                                            </div>
                                            <button type="submit" class="btn btn-primary w-100" id="payBtn">
                                                Pay with Razorpay
                                            </button>
                                        </div>
                                    </div>
                                </form>

                                <hr>

                                {{-- Wallet Balance Display --}}
                                <div class="text-center">
                                    <h5>Current Wallet Balance</h5>
                                    <h3 class="text-success">₹{{ Auth::user()->wallet_amount ?? 0 }}</h3>
                                </div>
                            </div>

                            {{-- UPI/QR Tab Pane --}}
                            <div class="tab-pane fade text-center" id="upi-pane" role="tabpanel" aria-labelledby="upi-tab"
                                tabindex="0">

                                @if (!empty($user->vpa_address) || !empty($user->qr_code_url))
                                    {{-- Dynamic Smart Collect QR --}}
                                    <div class="row justify-content-center mb-3">
                                        <div class="col-md-6 d-flex flex-column align-items-center">
                                            @if (!empty($user->vpa_address))
                                                @php
                                                    $upiLink =
                                                        'upi://pay?pa=' . $user->vpa_address . '&pn=Bewins&cu=INR';
                                                @endphp
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($upiLink) }}"
                                                    class="img-fluid mb-2 border p-2 rounded shadow-sm" alt="UPI QR">
                                            @elseif(!empty($user->qr_code_url))
                                                {{-- Fallback to Link QR if VPA missing --}}
                                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data={{ urlencode($user->qr_code_url) }}"
                                                    class="img-fluid mb-2 border p-2 rounded shadow-sm" alt="Payment QR">
                                            @endif

                                            <h5 class="mt-2">Scan to Pay Instantly</h5>
                                            <p class="text-muted small">Use any UPI App (GPay, PhonePe, Paytm)</p>

                                            @if (!empty($user->vpa_address))
                                                <div class="mt-3 p-2 bg-light border rounded w-100"
                                                    style="max-width: 300px;">
                                                    <small class="text-muted d-block text-start px-2">UPI ID</small>
                                                    <div class="d-flex justify-content-between align-items-center px-2">
                                                        <strong class="text-primary text-truncate me-2"
                                                            id="vpaText">{{ $user->vpa_address }}</strong>
                                                        <button class="btn btn-sm btn-outline-secondary"
                                                            onclick="copyVPA()">
                                                            <i class="fa fa-copy"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="alert alert-success d-inline-block px-4 py-2 mt-2">
                                        <i class="fa fa-bolt me-2"></i> Amount will be added to wallet
                                        <strong>automatically</strong>.
                                    </div>
                                @else
                                    {{-- Manual Fallback (Old Static QR) --}}
                                    <div class="row justify-content-center mb-3">
                                        <div class="col-md-4 d-flex flex-column align-items-center">
                                            <img src="{{ asset('public/backend/images/qrnew.png') }}" class="img-fluid mb-2"
                                                alt="BHIM QR">
                                            <strong>BHIM UPI</strong>
                                        </div>
                                    </div>

                                    <p class="text-muted text-center">
                                        Scan this QR-Code, pay online, then take a screenshot and upload on the next step
                                    </p>

                                    <hr class="w-50 mx-auto">

                                    <p class="mb-1 fw-bold">UPI Transfer</p>
                                    <p>
                                        <a href="#" class="text-decoration-none text-primary fw-bold">
                                            9431058741m@pnb
                                        </a>
                                    </p>

                                    <hr class="w-50 mx-auto">

                                    <p class="text-center">
                                        <strong>Please Contact Your <span class="text-primary">RM</span></strong><br>
                                        Mobile Number: <br>
                                        <small class="text-muted">
                                            After payment using above payment methods
                                        </small>
                                    </p>
                                @endif

                            </div>

                            <script>
                                function copyVPA() {
                                    var copyText = document.getElementById("vpaText");
                                    navigator.clipboard.writeText(copyText.innerText).then(function() {
                                        alert("UPI ID Copied!");
                                    }, function(err) {
                                        console.error('Could not copy text: ', err);
                                    });
                                }

                                function testPayment() {
                                    if (!confirm('Simulate a ₹10 test payment?')) return;

                                    fetch('{{ route('razorpay.webhook') }}?amount=10', {
                                            method: 'GET',
                                            headers: {
                                                'Accept': 'application/json'
                                            }
                                        })
                                        .then(response => response.json())
                                        .then(data => {
                                            if (data.success) {
                                                alert('✅ ' + data.message + '\nNew Balance: ₹' + data.new_balance);
                                                location.reload();
                                            } else {
                                                alert('❌ ' + data.message);
                                            }
                                        })
                                        .catch(error => {
                                            alert('Error: ' + error.message);
                                        });
                                }
                            </script>


                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Razorpay Script --}}
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        // Bootstrap tab switching functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Handle tab switching
            const tabButtons = document.querySelectorAll('[data-bs-toggle="tab"]');
            tabButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();

                    // Remove active class from all tabs and panes
                    document.querySelectorAll('.nav-link').forEach(tab => tab.classList.remove(
                        'active'));
                    document.querySelectorAll('.tab-pane').forEach(pane => {
                        pane.classList.remove('show', 'active');
                    });

                    // Add active class to clicked tab and corresponding pane
                    this.classList.add('active');
                    const targetPane = document.querySelector(this.getAttribute('data-bs-target'));
                    if (targetPane) {
                        targetPane.classList.add('show', 'active');
                    }
                });
            });
        });

        // Only initialize Razorpay form listener if element exists
        if (document.getElementById('addMoneyForm')) {
            document.getElementById('addMoneyForm').addEventListener('submit', function(e) {
                e.preventDefault();

                const amount = document.getElementById('amount').value;
                const payBtn = document.getElementById('payBtn');

                if (!amount || amount <= 0) {
                    alert('Please enter a valid amount');
                    return;
                }

                payBtn.disabled = true;
                payBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Processing...';

                let options = {
                    "key": "{{ env('RAZORPAY_KEY') }}",
                    "amount": amount * 100, // in paisa
                    "currency": "INR",
                    "name": "Add Money to Wallet",
                    "description": "Adding ₹" + amount + " to wallet",
                    "image": "{{ asset('images/logo.png') }}",
                    "handler": function(response) {

                        // After successful payment — add money to wallet
                        let data = {
                            amount: amount,
                            razorpay_payment_id: response.razorpay_payment_id,
                            _token: "{{ csrf_token() }}"
                        };

                        $.post("{{ route('razorpay.payment.success') }}", data, function(res) {

                            if (res.success) {
                                alert('Payment successful! Money added to your wallet.');
                                location.reload();
                            } else {
                                alert('Payment successful but wallet update failed: ' + res
                                    .message);
                            }

                        }).fail(function() {
                            alert(
                                'Payment successful but wallet update failed. Please contact support.'
                            );
                        });

                    },
                    "modal": {
                        "ondismiss": function() {
                            payBtn.disabled = false;
                            payBtn.innerHTML = 'Pay with Razorpay';
                        }
                    },
                    "prefill": {
                        "name": "{{ Auth::user()->name }}",
                        "email": "{{ Auth::user()->email }}",
                    },
                    "theme": {
                        "color": "#0d6efd"
                    }
                };

                var rzp1 = new Razorpay(options);
                rzp1.open();
            });
        }
    </script>
@endpush
