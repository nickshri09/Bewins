@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <h2>New Taxation Order</h2>
    <form id="orderForm" action="{{ route('orderPlaced.store') }}" method="POST">
        @csrf
        <div class="row mt-3">

            <!-- My Details -->
            <div class="col-md-4 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5><strong>My Details:</strong></h5>
                        <hr>
                        <p># {{ auth()->user()->id }}</p>
                        <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                        <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                        <p><strong>Mobile Number:</strong> {{ auth()->user()->mobile_number ?? 'Not Available' }}</p>
                    </div>
                </div>
            </div>

            <!-- Client + Service Info -->
            <div class="col-md-4 mb-3">
                <!-- Client Section -->
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5><strong>Client</strong></h5>
                            <a href="#" class="text-primary" data-toggle="modal" data-target="#addClientModal">
                                <i class="fas fa-user-plus"></i> Add Client
                            </a>
                        </div>
                        <select name="member_id" class="form-control mt-2" required>
                            <option value="">Select Existing Client</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">
                                    {{ $member->first_name }} {{ $member->last_name }} |
                                    {{ $member->email }} | {{ $member->mobile }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Main Service Info -->
                @if($amendService)
                <div class="card">
                    <div class="card-body">
                        <p><strong>{{ $amendService->service_name }}</strong></p>
                        <h4 class="text-primary">₹{{ number_format($amendService->fee, 2) }}</h4>
                        <hr>
                        <p><strong>Obligation:</strong> {{ $amendService->obligation ?? 'N/A' }}</p>
                        <p><strong>Frequency:</strong> {{ $amendService->frequency ?? 'N/A' }}</p>
                        <p><strong>Maximum TAT:</strong> {{ $amendService->maximum_tat ?? 'N/A' }}</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Wallet + Scope + Summary -->
            <div class="col-md-4 mb-3">
                <!-- Wallet -->
                <div class="card text-white bg-primary mb-3">
                    <div class="card-body">
                        <h5>Wallet Balance</h5>
                        <h3>{{ auth()->user()->wallet_amount ?? '0.00'}}</h3>
                    </div>
                </div>

                <!-- Scope Services -->
                @if($scopeServices->count())
                <div class="card mb-3">
                    <div class="card-body">
                        <h5>Scope <span class="text-muted">Fee List</span></h5>
                        <select class="form-control mt-2" id="scopeSelect" required>
                            <option value="">Select</option>
                            @foreach($scopeServices as $scope)
                                <option 
                                    value="{{ $scope->batch_id }}"
                                    data-id="{{ $scope->id }}"
                                    data-name="{{ $scope->service_name }}"
                                    data-fee="{{ $scope->fee }}">
                                    ₹{{ number_format($scope->fee, 2) }} - {{ $scope->service_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                @endif

                <!-- Order Summary -->
                <table class="table" id="orderSummaryTable">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Fee</th>
                        </tr>
                    </thead>

                    <tbody id="scopeServiceBody">
                        <!-- JS adds scope services here -->
                    </tbody>

                    <tbody id="mainServiceBody">
                        @if($amendService)
                            <tr>
                                <td>{{ $amendService->service_name }}</td>
                                <td>₹{{ number_format($amendService->fee, 2) }}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>

                <!-- Hidden Fields -->
                @if($amendService)
                    <input type="hidden" name="main_service_name" value="{{ $amendService->service_name }}">
                    <input type="hidden" name="main_service_fee" value="{{ $amendService->fee }}">
                @endif
                <input type="hidden" id="scope_service_name" name="scope_service_name">
                <input type="hidden" id="scope_service_fee" name="scope_service_fee">
                <input type="hidden" id="scope_service_id" name="scope_service_id">

                <!-- Confirmation Trigger Button -->
                <button type="button" class="btn btn-outline-dark w-100" data-toggle="modal" data-target="#confirmOrderModal">
                    Place Order
                </button>

            </div>
        </div>
    </form>
</div>

<!-- Confirm Order Modal -->
<div class="modal fade" id="confirmOrderModal" tabindex="-1" role="dialog" aria-labelledby="confirmOrderModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-warning text-dark">
        <h5 class="modal-title" id="confirmOrderModalLabel">Confirm Your Order</h5>
        <button type="button" class="close text-dark" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body text-center">
        <i class="fas fa-exclamation-circle text-warning mb-3" style="font-size: 2rem;"></i>
        <p class="h5 mb-2"><strong>Are you sure you want to place this order?</strong></p>
        <p class="text-muted">Once submitted, your wallet will be debited, and the order will be placed.</p>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
        <button type="submit" form="orderForm" class="btn btn-success">Yes, Place Order</button>
      </div>
    </div>
  </div>
</div>

<!-- Add Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="addClientModalLabel">Register New Client</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <label><strong>PAN Number <span class="text-danger">*</span></strong></label>
        <div class="input-group mb-3">
          <span class="input-group-text"><i class="fas fa-id-card"></i></span>
          <input type="text" class="form-control" placeholder="PAN Number" required>
          <button class="btn btn-primary">Verify PAN</button>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const scopeSelect = document.getElementById('scopeSelect');
        const scopeBody = document.getElementById('scopeServiceBody');
        const scopeNameInput = document.getElementById('scope_service_name');
        const scopeFeeInput = document.getElementById('scope_service_fee');
        const scopeIdInput = document.getElementById('scope_service_id');

        scopeSelect.addEventListener('change', function () {
            const selectedOption = this.options[this.selectedIndex];
            const name = selectedOption.getAttribute('data-name');
            const fee = selectedOption.getAttribute('data-fee');
            const id = selectedOption.getAttribute('data-id');

            if (name && fee && id) {
                scopeNameInput.value = name;
                scopeFeeInput.value = fee;
                scopeIdInput.value = id;

                scopeBody.innerHTML = `
                    <tr>
                        <td>${name}</td>
                        <td>₹${parseFloat(fee).toFixed(2)}</td>
                    </tr>
                `;
            } else {
                scopeNameInput.value = '';
                scopeFeeInput.value = '';
                scopeIdInput.value = '';
                scopeBody.innerHTML = '<tr><td colspan="2">No matching services found</td></tr>';
            }
        });
    });
</script>
@endpush
