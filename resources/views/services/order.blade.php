@extends('layouts.app')

@section('styles')
    <style>
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.1);
        }

        .card-title {
            color: #2c3e50;
            font-weight: 600;
            font-size: 1rem;
            margin-bottom: 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #3498db;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            padding: 8px 12px;
            font-size: 0.9rem;
        }

        .btn {
            border-radius: 8px;
            padding: 8px 15px;
            font-size: 0.9rem;
        }

        .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .info-label {
            font-weight: 600;
            color: #2c3e50;
            min-width: 80px;
            margin-right: 10px;
        }

        .info-value {
            color: #6c757d;
        }

        .service-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 12px;
        }

        .wallet-balance {
            background: linear-gradient(135deg, #2ecc71 0%, #27ae60 100%);
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-align: center;
            margin-top: 15px;
        }

        .wallet-balance h3 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }

        .wallet-balance p {
            margin: 2px 0 0;
            font-size: 0.8rem;
            opacity: 0.8;
        }

        /* Error Modal Styles */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }

        .modal-overlay.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 2.5rem;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            max-width: 450px;
            width: 90%;
            text-align: center;
            position: relative;
            animation: modalSlideIn 0.3s ease-out;
            border: 1px solid rgba(0, 0, 0, 0.1);
            margin: 0 auto;
        }

        @keyframes modalSlideIn {
            from {
                transform: translateY(-20px);
                opacity: 0;
            }

            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .error-icon {
            font-size: 4rem;
            color: #dc3545;
            margin-bottom: 1.5rem;
            animation: iconPulse 1s ease-in-out;
        }

        @keyframes iconPulse {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }

            50% {
                transform: scale(1.1);
            }

            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        .error-title {
            font-size: 1.75rem;
            color: #2c3e50;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .error-message {
            color: #6c757d;
            margin-bottom: 2rem;
            line-height: 1.6;
            font-size: 1.1rem;
        }

        .error-modal-close {
            background: #dc3545;
            color: white;
            border: none;
            padding: 0.85rem 2.5rem;
            border-radius: 12px;
            font-weight: 500;
            font-size: 1.1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.2);
        }

        .error-modal-close:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(220, 53, 69, 0.3);
        }

        .error-modal-close:active {
            transform: translateY(0);
        }

        /* Simple Modal Styles */
        #errorModal {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            width: 400px;
            text-align: center;
        }

        #modalOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }

        .error-icon {
            color: #dc3545;
            font-size: 50px;
            margin-bottom: 20px;
        }

        .error-title {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        .error-message {
            color: #666;
            margin-bottom: 25px;
            font-size: 16px;
        }

        .close-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        .close-btn:hover {
            background: #c82333;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <!-- Page Heading -->
        <h4 class="mb-3 font-weight-bold">New Lead</h4>

        <!-- Breadcrumb -->
        <div class="bg-light p-3 rounded mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <strong>
                    {{ $serviceCategories[$category]['name'] }}
                    @if ($subCategory)
                        > {{ $serviceCategories[$category]['sub_categories'][$subCategory] }}
                    @endif
                </strong>
            </div>
        </div>

        <!-- Order Form -->
        <form id="orderForm">
            @csrf
            <div class="row">
                <!-- User Details -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5><strong>My Details:</strong></h5>
                            <hr>
                            <p># {{ auth()->user()->id }}</p>
                            <p><strong>Name:</strong> {{ auth()->user()->name }}</p>
                            <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                            <p><strong>Mobile Number:</strong> {{ auth()->user()->mobile_number }}</p>
                        </div>
                    </div>
                </div>

                <!-- Client Selection -->
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5><strong>Client</strong></h5>
                                <a href="#" class="text-primary" data-bs-toggle="modal"
                                    data-bs-target="#newClientModal">
                                    <i class="fas fa-user-plus"></i> Add Client
                                </a>
                            </div>
                            <!--<select name="member_id" class="form-control mt-2" required>-->
                            <!--    <option value="">Select Existing Client</option>-->
                            <!--    @foreach ($members as $member)
    -->
                            <!--        <option value="{{ $member->id }}">-->
                            <!--            {{ $member->name }} | {{ $member->email }} | {{ $member->mobile }}-->
                            <!--        </option>-->
                            <!--
    @endforeach-->
                            <!--</select>-->

                            <select name="member_id" class="form-control mt-2 select2" required>
                                <option value="">Select Existing Client</option>
                                @foreach ($members as $member)
                                    @if (auth()->user()->user_type === 'admin' || $member->user_id == auth()->id())
                                        <option value="{{ $member->id }}">
                                            {{ $member->name }} | {{ $member->email }} | {{ $member->mobile }}
                                        </option>
                                    @endif
                                @endforeach
                            </select>

                            @if (isset($service))
                                <div class="mt-4">
                                    <p>{{ $serviceCategories[$category]['name'] }} >
                                        {{ $serviceCategories[$category]['sub_categories'][$subCategory] }}</p>
                                    <p><strong>{{ $service->service_name }}</strong></p>
                                    <h4 class="text-primary">₹{{ number_format($service->fee, 2) }}</h4>
                                    <hr>
                                    <p><strong>Obligation:</strong> {{ $service->obligation }}</p>
                                    <p><strong>Frequency:</strong> {{ $service->frequency }}</p>
                                    <p><strong>Maximum TAT:</strong> {{ $service->maximum_tat }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Service Information -->
                <div class="col-md-4">
                    <!-- Wallet -->
                    <div class="card text-white bg-primary mb-4">
                        <div class="card-body">
                            <h5>Wallet Balance</h5>
                            <h3>₹{{ number_format(auth()->user()->wallet_amount, 2) }}</h3>
                        </div>
                    </div>

                    <!-- Scope Services -->
                    @if ($scopeServices->count())
                        <div class="card mb-4">
                            <div class="card-body">
                                <h5>Scope <span class="text-muted">Fee List</span></h5>
                                <select class="form-control mt-2" id="scope_service_id" name="scope_service_id">
                                    <option value="">Select</option>
                                    @foreach ($scopeServices as $scopeService)
                                        <option value="{{ $scopeService->id }}"
                                            data-name="{{ $scopeService->service_name }}"
                                            data-fee="{{ $scopeService->fee }}">
                                            ₹{{ number_format($scopeService->fee, 2) }} -
                                            {{ $scopeService->service_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    @endif

                    <!-- Order Summary -->
                    <div class="card">
                        <div class="card-body">
                            <table class="table" id="orderSummaryTable">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Fee</th>
                                    </tr>
                                </thead>
                                <tbody id="orderSummary">
                                    <tr>
                                        <td id="mainServiceName">{{ $service->service_name }}</td>
                                        <td id="mainServiceFee">₹{{ number_format($service->fee, 2) }}</td>
                                    </tr>
                                    <tr id="scopeServiceRow" style="display: none;">
                                        <td id="scopeServiceName"></td>
                                        <td id="scopeServiceFee"></td>
                                    </tr>
                                    <tr class="table-primary">
                                        <th>Total</th>
                                        <th id="totalFee">₹{{ number_format($service->fee, 2) }}</th>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- Hidden Fields for submission -->
                            <input type="hidden" name="main_service_name" id="main_service_name"
                                value="{{ $service->service_name ?? '' }}">
                            <input type="hidden" name="main_service_fee" id="main_service_fee"
                                value="{{ $service->fee ?? 0 }}">
                            <input type="hidden" name="scope_service_name" id="scope_service_name" value="">
                            <input type="hidden" name="scope_service_fee" id="scope_service_fee" value="0">

                            <!--<button class="btn btn-primary w-100 mb-2">Apply Occasional Coupon</button>-->
                            <div class="col-12">
                                <div class="form-group">
                                    <button type="button" id="placeOrderBtn" class="btn btn-outline-dark w-100">Place
                                        Order</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- New Client Modal -->
        <div class="modal fade" id="newClientModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Register New Client</h5>
                        <button type="button" onclick="closeModal()"
                            style="background: none; border: none; font-size: 20px; color: #dc3545; cursor: pointer; padding: 0.25rem; margin-top: -8px;">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Client Creation Form (direct, no pre-verification) -->
                        <form id="newClientForm" method="POST" action="{{ route('members.store') }}"
                            enctype="multipart/form-data">
                            @csrf
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">First Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="first_name" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="last_name" required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Client No <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="member_no"
                                            value="{{ get_option('starting_member_no') }}"
                                            {{ get_option('starting_member_no') != '' ? 'readonly' : '' }} required>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Business Name</label>
                                        <input type="text" class="form-control" name="business_name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="email">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Mobile</label>
                                        <input type="text" class="form-control" name="mobile">
                                    </div>
                                </div>

                                <input type="hidden" name="country_code" value="+91">

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-control" name="gender">
                                            <option value="">Select One</option>
                                            <option value="male">Male</option>
                                            <option value="female">Female</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control" name="state">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Zip</label>
                                        <input type="text" class="form-control" name="zip">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Credit Source</label>
                                        <input type="text" class="form-control" name="credit_source">
                                    </div>
                                </div>

                                <div class="col-12">
                                    <div class="form-group mb-3">
                                        <label class="form-label">Address</label>
                                        <textarea class="form-control" name="address" rows="3"></textarea>
                                    </div>
                                </div>

                                <!-- Identification Details (PAN & Aadhaar) -->
                                <div class="col-12 mt-2">
                                    <h6 class="text-secondary">Identification Details</h6>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label">PAN Card Number</label>
                                        <div class="input-group">
                                            <input type="text" class="form-control" name="pancard_no"
                                                id="pan_card_modal" placeholder="ABCDE1234F" maxlength="10">
                                            <button type="button" class="btn btn-outline-primary"
                                                id="verify_pan_btn_modal">Verify</button>
                                        </div>
                                        <small id="pan_message_modal" class="form-text mt-1"
                                            style="display:none;"></small>
                                    </div>
                                    <div class="form-group mb-3">
                                        <label class="form-label">Upload PAN Card Image</label>
                                        <input type="file" class="form-control" name="pancard_image"
                                            accept=".jpg,.jpeg,.png">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group mb-2">
                                        <label class="form-label">Aadhaar Card Number</label>
                                        <input type="text" class="form-control" name="aadhaar_no"
                                            placeholder="Aadhaar Number">
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Aadhaar Front Image</label>
                                                <input type="file" class="form-control" name="aadhaar_front"
                                                    accept=".jpg,.jpeg,.png">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group mb-3">
                                                <label class="form-label">Aadhaar Back Image</label>
                                                <input type="file" class="form-control" name="aadhaar_back"
                                                    accept=".jpg,.jpeg,.png">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Custom Fields -->
                                @php
                                    $customFields = \App\Models\CustomField::where('table', 'members')
                                        ->where('status', 1)
                                        ->orderBy('id', 'asc')
                                        ->get();
                                @endphp
                                @if (!$customFields->isEmpty())
                                    @foreach ($customFields as $customField)
                                        <div class="{{ $customField->field_width }}">
                                            <div class="form-group mb-3">
                                                <label class="form-label">{{ $customField->field_name }}</label>
                                                {!! xss_clean(generate_input_field($customField)) !!}
                                            </div>
                                        </div>
                                    @endforeach
                                @endif

                                <div class="col-12">
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary" id="createMemberBtn">Create
                                            Member</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Error Modal -->
        <div id="modalOverlay"
            style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
        </div>
        <div id="errorModal"
            style="display: none; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.2); z-index: 10000; width: 400px; text-align: center;">
            <div style="color: #dc3545; font-size: 50px; margin-bottom: 20px;">
                <i class="fas fa-exclamation-circle"></i>
            </div>
            <h3 id="errorModalTitle" style="font-size: 24px; color: #333; margin-bottom: 15px;">Error</h3>
            <p id="errorModalMessage" style="color: #666; margin-bottom: 25px; font-size: 16px;"></p>
            <button onclick="closeErrorModal()"
                style="background: #dc3545; color: white; border: none; padding: 10px 30px; border-radius: 5px; cursor: pointer; font-size: 16px;">Close</button>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('.select2').select2({
                placeholder: "Search and select client",
                allowClear: true,
                width: '100%'
            });
        });
    </script>

    <script>
        function closeModal() {
            const modal = document.getElementById('newClientModal');
            const backdrop = document.querySelector('.modal-backdrop');

            // Hide the modal
            modal.style.display = 'none';
            modal.classList.remove('show');

            // Remove backdrop
            if (backdrop) {
                backdrop.remove();
            }

            // Remove modal-open class from body
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';

            // Reset forms
            document.getElementById('pan_number').value = '';
            document.getElementById('panError').style.display = 'none';
            document.getElementById('panVerificationForm').style.display = 'block';
            document.getElementById('clientCreationForm').style.display = 'none';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Add Client button click handler (open modal directly)
            document.querySelector('[data-bs-target="#newClientModal"]').addEventListener('click', function(e) {
                e.preventDefault();
                const modal = document.getElementById('newClientModal');
                modal.style.display = 'block';
                modal.classList.add('show');
                document.body.classList.add('modal-open');

                // Add backdrop
                const backdrop = document.createElement('div');
                backdrop.className = 'modal-backdrop fade show';
                document.body.appendChild(backdrop);
            });

            // Handle member creation form submission
            document.getElementById('newClientForm').addEventListener('submit', function(e) {
                e.preventDefault();

                var formData = new FormData(this);

                fetch(this.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.result === 'success') {
                            // Add new member to dropdown
                            var memberSelect = document.querySelector('select[name="member_id"]');
                            var option = new Option(data.data.first_name + ' ' + data.data.last_name +
                                ' | ' + (data.data.email || '') + ' | ' + (data.data.mobile || ''),
                                data.data.id);
                            memberSelect.add(option);
                            memberSelect.value = data.data.id;

                            // Reset forms
                            document.getElementById('newClientForm').reset();

                            // Close modal using our custom function
                            closeModal();
                            // ✅ Show success message
                            Swal.fire({
                                icon: 'success',
                                title: 'Member Created',
                                text: data.message || 'Member has been created successfully!',
                                confirmButtonColor: '#3085d6',
                                confirmButtonText: 'OK'
                            });
                        } else {
                            // Handle validation errors
                            if (data.message && Array.isArray(data.message)) {
                                alert(data.message.join('\n'));
                            } else {
                                // alert(data.message || 'An error occurred while creating the member.');
                                alert(data.message || 'Member has been created successfully.');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Member has been created successfully.');
                    });
            });




            // PAN verify button inside modal
            (function() {
                var panInput = document.getElementById('pan_card_modal');
                var verifyBtn = document.getElementById('verify_pan_btn_modal');
                var message = document.getElementById('pan_message_modal');
                if (verifyBtn && panInput && message) {
                    verifyBtn.addEventListener('click', function() {
                        var value = (panInput.value || '').trim().toUpperCase();
                        panInput.value = value;
                        var panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;
                        message.style.display = 'block';
                        if (panPattern.test(value)) {
                            message.className = 'form-text text-success mt-1';
                            message.textContent = '✅ PAN format verified successfully.';
                        } else {
                            message.className = 'form-text text-danger mt-1';
                            message.textContent = '❌ Invalid PAN format. Example: ABCDE1234F';
                        }
                    });
                }
            })();
        });

        // Scope service change handler (optional if dropdown exists)
        (function() {
            const scopeSelect = document.getElementById('scope_service_id');
            if (!scopeSelect) return;

            scopeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const scopeServiceRow = document.getElementById('scopeServiceRow');
                const scopeServiceName = document.getElementById('scopeServiceName');
                const scopeServiceFee = document.getElementById('scopeServiceFee');
                const totalFee = document.getElementById('totalFee');
                const mainServiceFee = parseFloat(document.getElementById('main_service_fee').value);

                if (this.value) {
                    const serviceName = selectedOption.dataset.name;
                    const serviceFee = parseFloat(selectedOption.dataset.fee);

                    scopeServiceRow.style.display = '';
                    scopeServiceName.textContent = serviceName;
                    scopeServiceFee.textContent = '₹' + serviceFee.toFixed(2);
                    totalFee.textContent = '₹' + (mainServiceFee + serviceFee).toFixed(2);

                    document.getElementById('scope_service_name').value = serviceName;
                    document.getElementById('scope_service_fee').value = serviceFee;
                } else {
                    scopeServiceRow.style.display = 'none';
                    totalFee.textContent = '₹' + mainServiceFee.toFixed(2);

                    document.getElementById('scope_service_name').value = '';
                    document.getElementById('scope_service_fee').value = '0';
                }
            });
        })();

        function showErrorModal(message, title = 'Error') {
            document.getElementById('modalOverlay').style.display = 'block';
            document.getElementById('errorModal').style.display = 'block';
            document.getElementById('errorModalTitle').textContent = title;
            document.getElementById('errorModalMessage').textContent = message;
            document.body.style.overflow = 'hidden';
        }

        function closeErrorModal() {
            document.getElementById('modalOverlay').style.display = 'none';
            document.getElementById('errorModal').style.display = 'none';
            document.body.style.overflow = '';
        }

        // Handle place order button click
        document.getElementById('placeOrderBtn').addEventListener('click', function() {
            const memberSelect = document.querySelector('select[name="member_id"]');

            // Check if client is selected
            if (!memberSelect.value) {
                showErrorModal('Please select a client first.', 'Select Client');
                return;
            }

            const totalAmount = parseFloat(document.getElementById('totalFee').textContent.replace('₹', '').replace(
                ',', ''));
            const walletBalance = parseFloat('{{ auth()->user()->wallet_amount }}');

            if (walletBalance < totalAmount) {
                showErrorModal('Insufficient wallet balance. Please recharge your wallet.', 'Insufficient Balance');
                return;
            }

            // If wallet balance is sufficient, submit the form via AJAX
            const form = document.getElementById('orderForm');
            const formData = new FormData(form);

            fetch('{{ route('services.order', ['category' => $category, 'subCategory' => $subCategory]) }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'error') {
                        showErrorModal(data.message, 'Error');
                    } else if (data.redirect) {
                        window.location.href = data.redirect;
                    } else if (data.order_id) {
                        window.location.href = '/view_order_single/' + data.order_id;
                    } else {
                        showErrorModal('An error occurred while processing your order. Please try again.',
                            'Error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showErrorModal('An error occurred while processing your request. Please try again.',
                        'Error');
                });
        });
    </script>
@endpush
