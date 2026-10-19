@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">

            <h4 class="mb-4">Withdraw Coins</h4>

            <div class="card">
                {{-- Header with rounded top and centered title --}}
                <div class="card-header bg-primary text-white rounded-top px-4 py-3">
                    <h5 class="mb-0">Request withdraw coins from earned coins</h5>
                </div>
{{-- action="{{ route('fd.store') }}" enctype="multipart/form-data" --}}
                {{-- Transferable balance --}}
                <div class="text-center my-3">
                    <div class="fw-semibold">Transferable Balance</div>
                    <span class="badge bg-success fs-4 px-3 py-2">27.00</span>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{route('sendrequest.store')}}">
                        @csrf
                        {{-- Coin Transfer --}}
                        <div class="mb-3">
                            <label for="coin_transfer" class="form-label">Coin Transfer <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="coin_transfer" id="coin_transfer" class="form-control" placeholder="Enter Amount" required>
                        </div>

                        {{-- TDS & Net Amount --}}
                        <div class="mb-3">
                            <strong>TDS Amount:</strong> ₹0.00<br>
                            <strong>Net Amount to Wallet:</strong> ₹0.00
                        </div>

                        {{-- Account Holder Name --}}
                        <div class="mb-3">
                            <label for="holder_name" class="form-label">Account Holder Name <span class="text-danger">*</span></label>
                            <input type="text" name="holder_name" id="holder_name" class="form-control" placeholder="Enter Account Holder Name" required>
                        </div>

                        {{-- Bank Name --}}
                        <div class="mb-3">
                            <label for="bank_name" class="form-label">Bank Name <span class="text-danger">*</span></label>
                            <input type="text" name="bank_name" id="bank_name" class="form-control" placeholder="Enter Bank Name" required>
                        </div>

                        {{-- Account Number --}}
                        <div class="mb-3">
                            <label for="account_number" class="form-label">Account Number <span class="text-danger">*</span></label>
                            <input type="text" name="account_number" id="account_number" class="form-control" placeholder="Enter Account Number" required>
                        </div>

                        {{-- IFSC + Account Type --}}
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="ifsc_code" class="form-label">IFSC Code <span class="text-danger">*</span></label>
                                <input type="text" name="ifsc_code" id="ifsc_code" class="form-control" placeholder="Enter IFSC Code" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="account_type" class="form-label">Account Type <span class="text-danger">*</span></label>
                                <select name="account_type" id="account_type" class="form-select" required>
                                    <option value="">Select Account Type</option>
                                    <option value="savings">Savings</option>
                                    <option value="current">Current</option>
                                </select>
                            </div>
                        </div>

                        {{-- OTP --}}
                        {{-- <div class="row align-items-end">
                            <div class="col-md-6 mb-3">
                                <label for="otp" class="form-label">Enter 6 digit OTP received on Email <span class="text-danger">*</span></label>
                                <input type="text" name="otp" id="otp" class="form-control" placeholder="Enter 6 digit OTP" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <button type="button" class="btn btn-primary w-100">
                                    Click to Generate and Send OTP on Email
                                </button>
                                <small class="text-muted d-block mt-1">Please check Inbox, Spam and other tabs also for OTP</small>
                            </div>
                        </div> --}}


                        {{-- Submit --}}
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
