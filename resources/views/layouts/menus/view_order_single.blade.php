@extends('layouts.app')

@section('content')
    <div class="container-fluid mt-3">
        <h5 class="mb-3">Lead #{{ $order->id }}</h5>
        <div class="row">

            <!-- Left Section -->
            <div class="col-lg-8">

                <!-- Manage Card -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Manage</span>
                        <span class="badge bg-warning text-dark">In Process</span>
                    </div>
                    <div class="card-body row text-center">
                        <div class="col-md-4">


 
                             @if ($order->user && $order->user->assignedManagerDetails)
                                <strong>RM(ID): </strong> {{ $order->user->assignedManagerDetails->user_id ?? 'N/A' }}<br>
                                <strong> Name:</strong> {{ $order->user->assignedManagerDetails->name ?? 'N/A' }}<br>
                                <strong>Mobile:</strong>
                                {{ $order->user->assignedManagerDetails->mobile_number ? '+91' . $order->user->assignedManagerDetails->mobile_number : 'N/A' }}
                            @else
                                N/A
                            @endif






                        </div>
                        <div class="col-md-4 offset-md-4 d-flex flex-column align-items-center justify-content-center">
                            <div
                                style="width: 100px; height: 100px; border-radius: 50%; border: 10px solid green; display: flex; align-items: center; justify-content: center;">
                                <h3 class="text-success mb-0">0</h3>
                            </div>
                            <small class="text-success mt-2">632 day(s) Remaining Out of <strong>7 day(s)</strong></small>
                        </div>
                    </div>
                </div>

                <!-- Client Info -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="card text-center border-primary">
                            <div class="card-body">
                                <img src="{{ asset('public/ano.jfif') }}" class="rounded-circle mb-2" alt="Profile"
                                    width="80">
                                <h5><strong>{{ $order->member->name ?? 'N/A' }}</strong></h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-primary">
                            <div class="card-body">
                                <p><i class="fas fa-mobile-alt"></i> <strong>Mobile:</strong>
                                    {{ $order->member->mobile ?? 'N/A' }}</p>
                                <hr>
                                <p><i class="fas fa-envelope"></i> <strong>Email:</strong>
                                    {{ $order->member->email ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>
                </div>



                <!-- Document Upload Section -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Documents</span>
                        <button type="button" class="btn btn-light btn-sm text-primary fw-bold" data-toggle="modal"
                            data-target="#addDocumentModal">
                            <i class="fas fa-plus"></i> Add Document
                        </button>
                    </div>
                    <div class="card-body text-center">

                        {{-- ✅ Show Uploaded Documents --}}
                        <div class="d-flex flex-wrap justify-content-center">
                            @forelse($order->documents as $document)
                                @php
                                    $statusColor = match (strtolower($document->status)) {
                                        'approved' => 'bg-success text-white',
                                        'rejected' => 'bg-danger text-white',
                                        default => 'bg-warning text-dark',
                                    };
                                @endphp

                                <div class="position-relative border p-3 m-2 text-center shadow-sm"
                                    style="width: 260px; border-radius: 10px; background-color: #fff;">

                                    <!-- Status Badge -->
                                    <span class="badge {{ $statusColor }} position-absolute"
                                        style="top: 10px; right: 10px;">
                                        {{ ucfirst($document->status ?? 'Pending') }}
                                    </span>

                                    <!-- Document Name -->
                                     <h6 class="mt-4 mb-2 fw-bold text-dark">{{ $document->name ?? 'Document' }}</h6>

                                    <!-- Document Image -->
                                    <img src="{{ asset('public/documents/' . basename($document->file_path)) }}"
                                        alt="Document" class="img-fluid mb-2"
                                        style="max-height: 140px; object-fit: cover; border-radius: 6px;">

                                    <!-- View Link -->
                                    <a href="{{ asset('public/documents/' . basename($document->file_path)) }}"
                                        target="_blank" class="text-primary fw-semibold d-block">
                                        View Document
                                    </a>

                                    <!-- Uploaded Date -->
                                    <p class="mt-2 text-muted small mb-0">
                                        {{ $document->created_at->format('d-m-Y h:i A') }}
                                    </p>
                                </div>
                            @empty
                                <p class="text-muted">No documents uploaded yet.</p>
                            @endforelse
                        </div>

                    </div>
                </div>

                <!-- Add Document Modal -->
                <div class="modal fade" id="addDocumentModal" tabindex="-1" aria-labelledby="addDocumentModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-primary text-white">
                                <h5 class="modal-title" id="addDocumentModalLabel">Add Document</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <form action="{{ route('order.document.store', $order->id) }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="modal-body">
                                    <div class="mb-3 text-start">
                                        <label for="documentName" class="form-label fw-semibold">Document Name</label>
                                        <input type="text" class="form-control" id="documentName" name="name"
                                            placeholder="Enter document name" required>
                                    </div>
                                    <div class="mb-3 text-start">
                                        <label for="file" class="form-label fw-semibold">Upload File</label>
                                        <input type="file" name="file" id="file" class="form-control" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                        <span>Main Wallet</span>
                        <span>Balance: <strong>{{ auth()->user()->wallet_amount ?? '0.00' }}</strong></span>
                    </div>
                </div>
                {{-- deatils --}}
                <div class="card shadow-sm mb-3" style="border-radius: 10px;">
                    @php
                        $status = strtolower($order->order_status ?? 'in process');
                        //dd($status);
                        $statusColor = match ($status) {
                            'completed' => 'bg-success',
                            // 'in process', 'in process' => 'bg-warning text-dark',
                            'process' => 'bg-warning text-dark',
                            'approval (rejected)' => 'bg-danger',
                            default => 'bg-secondary',
                        };
                    @endphp
                    <div class="card-header d-flex justify-content-between align-items-center"
                        style="background-color: #6c757d; color: white;">
                        <div>
                            <strong>Order ID:</strong> ({{ $order->id }})<br>
                            <small>Order Date: {{ \Carbon\Carbon::parse($order->created_at)->format('d-m-Y') }}</small>
                        </div>
                        {{-- <span class="badge bg-success px-3 py-2" style="font-size: 0.9rem;">{{ $order->order_status ?? 'In Process' }}</span> --}}

                        <span class="badge {{ $statusColor }} px-3 py-2"
                            style="font-size: 0.9rem; text-transform: capitalize;">
                            {{ $order->order_status ?? 'In Process' }}
                        </span>
                    </div>

                    <div class="card-body">
                        <p>
                            <strong>{{ $order->main_service_name ?? 'N/A' }}</strong>
                            {{ $order->scope_service_name ? ' - ' . $order->scope_service_name : '' }}
                            <br>
                            {{-- <small class="text-muted">
                            ({{ $order->id ?? 'N/A' }})
                        </small> --}}
                        </p>

                        <hr>

                        <p><strong>Order Progress</strong></p>

                        <p>
                            <strong>Assign to:</strong>
                            ({{ $order->service->category_code ?? 'ROC' }})
                            {{ $order->user->name ?? 'Pinki' }}
                        </p>

                        {{-- <p>
                        <strong>TL:</strong> {{ $order->user->id ?? 'N/A' }} -
                        {{ $order->user->name ?? 'N/A' }}
                        <a href="tel:{{ $order->user->mobile_number ?? '#' }}" class="text-decoration-none text-primary">
                            📞 {{ $order->user->mobile_number ?? '' }}
                        </a>
                    </p> --}}

                        <p><strong>Instructions:</strong> {{ $order->instructions ?? 'DIRECTOR KYC' }}</p>

                        {{-- <p>
                        <strong>Download:</strong>
                        <a href="{{ route('invoice', $order->id) }}" target="_blank" class="text-primary">Invoice PDF</a>
                    </p> --}}

                        @if (!empty($order->reference_order_id))
                            <p>
                                <strong>Reference Order:</strong>
                                <a href="{{ url('/orders/' . $order->reference_order_id) }}" class="text-primary">
                                    ({{ $order->reference_order_id }})
                                </a>
                            </p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right Chat Section -->
            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">Chat</div>
                    <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                        @forelse($chats as $chat)
                            <div class="mb-4">
                                <div class="d-flex align-items-start">
                                    <div class="me-2">
                                        <i class="fas fa-comment-dots" style="font-size: 1.4rem; color: purple;"></i>
                                    </div>
                                    <div class="card px-3 py-2 shadow-sm w-100">
                                        <strong class="text-primary">{{ $chat->name }}</strong>
                                        <div class="text-dark">{{ $chat->message }}</div>
                                    </div>
                                </div>
                                <div class="mt-1">
                                    <span
                                        class="badge bg-warning text-dark">{{ $chat->created_at->format('d/m/Y h:i A') }}</span>
                                </div>
                            </div>
                        @empty
                            <p>No chats yet.</p>
                        @endforelse
                    </div>

                    <!-- Chat Input -->
                    <div class="card-footer">
                        <form action="{{ route('order.chat.store', $order->id) }}" method="POST">
                            @csrf
                            <div class="mb-2">
                                <textarea name="message" class="form-control" rows="3" placeholder="Enter..." required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Add Comment</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <div class="container-fluid mb-4">
        <div class="row">
            <div class="col-12 text-end">
                <a href="{{ route('orders.invoice', $order->id) }}" class="btn btn-outline-primary" target="_blank">
                    <i class="fas fa-file-invoice"></i> View Invoice
                </a>
            </div>
        </div>
    </div>
@endsection
