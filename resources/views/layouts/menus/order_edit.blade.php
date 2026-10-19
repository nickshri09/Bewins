@extends('layouts.app')

@section('content')
<div class="container-fluid mt-3">
    <h5 class="mb-3">Lead #{{ $order->id }}</h5>
    <div class="row">
        <!-- LEFT COLUMN -->
        <div class="col-md-6">

            <!-- Manage Card -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <span>Manage</span>
                    <span class="badge bg-warning text-dark">{{ $order->lead_status ?? 'In Process' }}</span>
                </div>
                <div class="card-body row text-center">
                    <div class="col-md-6">
                        <p><strong>RM(ID):</strong> {{ $order->user->id ?? 'N/A' }}</p>
                        <p><i class="fas fa-user text-primary"></i> {{ $order->user->name ?? 'N/A' }}</p>
                        <p><i class="fas fa-phone-alt text-primary"></i> {{ $order->user->mobile_number ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6 d-flex flex-column align-items-center justify-content-center">
                        <div style="width: 100px; height: 100px; border-radius: 50%; border: 10px solid green; display: flex; align-items: center; justify-content: center;">
                            <h3 class="text-success mb-0">0</h3>
                        </div>
                        <small class="text-success mt-2">632 day(s) Remaining Out of <strong>7 day(s)</strong></small>
                    </div>
                </div>
            </div>

            <!-- Client Info -->
            <div class="card mb-3">
                <div class="card-header bg-info text-white text-center">
                    <strong>Client Info</strong>
                </div>
                <div class="card-body row text-center">
                    <div class="col-md-4">
                        <img src="{{ asset('public/ano.jfif') }}" class="rounded-circle mb-2" alt="Profile" width="80">
                    </div>
                    <div class="col-md-8 text-start">
                        <p><strong>Name:</strong> {{ $order->member->name ?? 'N/A' }}</p>
                        <p><strong>Mobile:</strong> {{ $order->member->mobile ?? 'N/A' }}</p>
                        <p><strong>Email:</strong> {{ $order->member->email ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>
            
            
                 <!-- Document Upload Section -->
        <!--<div class="card mb-3">-->
        <!--    <div class="card-header bg-primary text-white">Documents</div>-->
        <!--    <div class="card-body text-center">-->

        <!--        @foreach($order->documents as $document)-->
        <!--            <div class="mb-4 d-inline-block">-->
        <!--                <div class="border p-3" style="width: 260px;">-->
        <!--                    <img src="{{ asset('public/documents/' . basename($document->file_path)) }}" alt="Document" class="img-fluid mb-2" style="max-height: 180px; object-fit: cover;">-->
        <!--                    <br>-->
        <!--                    <a href="{{ asset('public/documents/' . basename($document->file_path)) }}" target="_blank">View Document</a>-->
        <!--                    <p class="mt-2 text-muted">{{ $document->created_at->format('d-m-Y h:i A') }}</p>-->
        <!--                </div>-->
        <!--            </div>-->
        <!--        @endforeach-->
        <!--    </div>-->
        <!--</div>-->
        <!--</div>-->
        <!-- Document Upload Section -->
<!-- Document Upload Section -->
<div class="card mb-3">
    <div class="card-header bg-primary text-white">Documents</div>

    <div class="card-body text-center">
        @if(auth()->user()->user_type === 'admin')
            <form action="{{ route('orders.document.status.update') }}" method="POST">
                @csrf

                <div class="d-flex flex-wrap justify-content-center">
                    @foreach($order->documents as $document)
                        @php
                            $statusColor = match(strtolower($document->status)) {
                                'approved' => 'bg-success text-white',
                                'rejected' => 'bg-danger text-white',
                                default => 'bg-warning text-dark',
                            };
                        @endphp

                        <div class="position-relative border p-3 m-2 text-center shadow-sm" style="width: 260px; border-radius: 10px; background-color: #fff;">

                            <!-- Checkbox (Top-Left Corner) -->
                            <div class="position-absolute" style="top: 10px; left: 30px;">
                                <input type="checkbox" name="document_ids[]" value="{{ $document->id }}" class="form-check-input">
                            </div>

                            <!-- Status Badge (Top-Right Corner) -->
                            <span class="badge {{ $statusColor }} position-absolute" style="top: 10px; right: 10px;">
                                {{-- {{ ucfirst($document->status) }} --}}
                                {{ ucfirst($document->status ?? 'Pending') }}
                            </span>

                            <img src="{{ asset('public/documents/' . basename($document->file_path)) }}"
                                 alt="Document"
                                 class="img-fluid mb-2 mt-3"
                                 style="max-height: 180px; object-fit: cover; border-radius: 6px;">

                            <div>
                                <a href="{{ asset('public/documents/' . basename($document->file_path)) }}" target="_blank" class="text-primary fw-semibold">
                                    View Document
                                </a>
                                <p class="mt-2 text-muted small mb-0">{{ $document->created_at->format('d-m-Y h:i A') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Status Dropdown + Update Button -->
                <div class="mt-3 text-end">
                    <select name="status" class="form-select d-inline-block w-auto me-2" required>
                        <option value="">Select Status</option>
                        <option value="Approved">Approved</option>
                        <option value="Rejected">Rejected</option>
                        <option value="Pending">Pending</option>
                    </select>
                    <button type="submit" class="btn btn-success btn-sm px-3 py-1" style="font-size: 0.8rem;">
                        Update
                    </button>
                </div>
            </form>
        @else
            <!-- For normal users (no checkboxes) -->
            <div class="d-flex flex-wrap justify-content-center">
                @foreach($order->documents as $document)
                    @php
                        $statusColor = match(strtolower($document->status)) {
                            'approved' => 'bg-success text-white',
                            'rejected' => 'bg-danger text-white',
                            default => 'bg-warning text-dark',
                        };
                    @endphp

                    <div class="position-relative border p-3 m-2 text-center shadow-sm" style="width: 260px; border-radius: 10px; background-color: #fff;">
                        <span class="badge {{ $statusColor }} position-absolute" style="top: 10px; right: 10px;">
                            {{ ucfirst($document->status) }}
                        </span>

                        <img src="{{ asset('public/documents/' . basename($document->file_path)) }}"
                             alt="Document"
                             class="img-fluid mb-2 mt-2"
                             style="max-height: 180px; object-fit: cover; border-radius: 6px;">

                        <a href="{{ asset('public/documents/' . basename($document->file_path)) }}" target="_blank" class="text-primary fw-semibold">
                            View Document
                        </a>
                        <p class="mt-2 text-muted small mb-0">{{ $document->created_at->format('d-m-Y h:i A') }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>





        </div>

        <!-- RIGHT COLUMN -->
        <div class="col-md-6">
            <div class="card mb-3">
                <div class="card-header bg-secondary text-white">Update Lead / Order / Review</div>
                <div class="card-body">
                    <form action="{{ url('/update-order-status/' . $order->id) }}" method="POST">
                        @csrf

                        <!-- Lead Status -->
                        <div class="mb-3">
                            <label class="form-label">Lead Status</label>
                            <select name="lead_status" class="form-control" >
                                <option value="">Select</option>
                                @php
                                    $leadStatuses = [
                                        'In Process', 'Completed', 'Refunded', 'Cancelled',
                                        'Document Pending', 'Document Requested', 'Document Received'
                                    ];
                                @endphp
                                @foreach ($leadStatuses as $status)
                                    <option value="{{ $status }}" {{ old('lead_status', $order->lead_status) == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Order Status -->
                        <div class="mb-3">
                            <label class="form-label">Order Status</label>
                            <select name="order_status" class="form-control" >
                                <option value="">Select</option>
                                @php
                                    $orderStatuses = [
                                        'New case', 'Process', 'Completed', 'Refund initiated',
                                        '(POG) Pending From Government side', 'Contact to Connector/RM',
                                        'Audit and Certification', 'Evp till 06 Aug 2024',
                                        'Not Linked or Wrong Order or Tax payable',
                                        'Chalan/DSC Payment Pendency', 'Approval (Pending)', 'Approval (Approved)',
                                        'Approval (Rejected)', 'Refund Approved', 'Refund Rejected', 'Partially Refunded'
                                    ];
                                @endphp
                                @foreach ($orderStatuses as $status)
                                    <option value="{{ $status }}" {{ old('order_status', $order->order_status) == $status ? 'selected' : '' }}>
                                        {{ $status }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Review -->
                        <div class="mb-3">
                            <label class="form-label">Review</label>
                            <textarea name="review" rows="3" class="form-control" placeholder="Add SKU or comment here...">{{ old('review', $order->review) }}</textarea>
                        </div>

                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                        <button type="submit" class="btn btn-success w-100">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div> <!-- /.row -->
</div>
@endsection
