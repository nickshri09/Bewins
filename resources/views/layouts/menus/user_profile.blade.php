@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <!-- Personal Information -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
            <h5 class="mb-0">Personal Information</h5>
        </div>
        <div class="card-body p-3">
            <div class="row mb-2">
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Profile ID</div>
                    <div>{{ $user->user_id ?? 'N/A' }}</div>
                </div>
                @if($user->user_type === 'user')
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Referral Code</div>
                    <div>{{ $user->referral_code ?? 'N/A' }}</div>
                </div>
                @endif
            </div>
            <div class="row mb-2">
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">First Name</div>
                    <div>{{ $user->name }}</div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Middle Name</div>
                    <div>{{ $user->middle_name }}</div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Last Name*</div>
                    <div>{{ $user->last_name }}</div>
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Email Id*</div>
                    <div>{{ $user->email }}</div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Mobile Number*</div>
                    <div>{{ $user->mobile_number }}</div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Whatsapp Number</div>
                    <div>{{ $user->whatsapp_number }}</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Permanent Account Number (PAN)*</div>
                    <div>{{ $user->pan_number }}</div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Aadhar Number*</div>
                    <div>{{ $user->aadhar_number }}</div>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="small text-muted">Role*</div>
                    <div>{{ strtolower((string) $user->user_type) === 'admin' ? 'Admin' : ($user->role->name ?: ucfirst($user->user_type)) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- GST Details -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
            <h5 class="mb-0">Goods and Service Tax Details</h5>
        </div>
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="small text-muted">GST Type*</div>
                    <div>{{ $user->gst_type }}</div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="small text-muted">GST Number*</div>
                    <div>{{ $user->gst_number }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Address Details -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white py-2">
                    <h5 class="mb-0">Residence Address</h5>
                </div>
                <div class="card-body p-3">
                    <div class="small text-muted">Address</div>
                    <div>{!! nl2br(e($user->residence_address)) !!}</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-primary text-white py-2">
                    <h5 class="mb-0">Office Address</h5>
                </div>
                <div class="card-body p-3">
                    <div class="small text-muted">Address</div>
                    <div>{!! nl2br(e($user->office_address)) !!}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bank Details -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
            <h5 class="mb-0">Bank Details</h5>
        </div>
        <div class="card-body p-3">
            <div class="row mb-2">
                 <div class="col-md-3 mb-2">
                    <div class="small text-muted">Account Holder Name*</div>
                    <div>{{ $user->account_name }}</div>
                </div>

                <div class="col-md-3 mb-2">
                    <div class="small text-muted" >Bank Name*</div>
                    <div>{{ $user->bank_name }}</div>
                </div>

                <div class="col-md-3 mb-2">
                    <div class="small text-muted">Branch Name*</div>
                    <div>{{ $user->branch ? $user->branch->name : '-' }}</div>
                </div>

                <div class="col-md-3 mb-2" >
                    <div class="small text-muted" >Account Type</div>
                    <div>{{ $user->account_type }}</div>
                </div>

            </div>

            <div class="row">

                <div class="col-md-3 mb-2">
                    <div class="small text-muted">Account Number*</div>
                    <div>{{ $user->account_number }}</div>
                </div>

                <div class="col-md-3 mb-2">
                    <div class="small text-muted" >IFSC Code*</div>
                    <div>{{ $user->ifsc_code }}</div>
                </div>

                <div class="col-md-3 mb-2">
                    <div class="small text-muted">Wallet Amount*</div>
                    <div>{{ $user->wallet_amount ?? '0.00' }}</div>
                </div>

                <div class="col-md-3 mb-2">
                    <div class="small text-muted">Percent*</div>
                    <div>{{ $user->percent ?? '0.00' }}</div>
                </div>

            </div>
        </div>
    </div>

    @if(in_array($user->user_type, ['user', 'admin']) || ($user->user_type === 'customer' && $profileMember))
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
            <h5 class="mb-0">{{ $user->user_type === 'customer' ? 'Referral Commission' : 'Direct Agent Commission' }}</h5>
        </div>
        <div class="card-body p-3">
            @if(in_array($user->user_type, ['user', 'admin']))
            <div class="mb-3">
                <span class="text-muted">Total Direct Agent Commission:</span>
                <strong>₹{{ number_format($agentCommissionTotal ?? 0, 2) }}</strong>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered mb-0">
                    <thead><tr><th>Investment</th><th>Client</th><th>Rate</th><th>Commission</th><th>Date</th></tr></thead>
                    <tbody>
                    @forelse($agentCommissions as $commission)
                        <tr>
                            <td>{{ $commission->investment?->investment_code ?? '#'.$commission->investment_id }}</td>
                            <td>{{ $commission->sourceMember?->first_name }} {{ $commission->sourceMember?->last_name }}</td>
                            <td>{{ number_format($commission->rate_percent, 2) }}%</td>
                            <td>₹{{ number_format($commission->amount, 2) }}</td>
                            <td>{{ $commission->created_at->format('d M Y') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted">No commission earned yet.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            @endif
            @if($user->user_type === 'customer' && $profileMember)
                <hr>
                <div class="mb-3">
                    <span class="text-muted">Total Referral Commission:</span>
                    <strong>₹{{ number_format($referralCommissionTotal ?? 0, 2) }}</strong>
                    <span class="text-muted ml-2">Referral code: {{ $profileMember->referral_code ?: '—' }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <thead><tr><th>Level</th><th>Investment</th><th>Rate</th><th>Commission</th><th>Date</th></tr></thead>
                        <tbody>
                        @forelse($referralCommissions as $commission)
                            <tr>
                                <td>Level {{ $commission->level }}</td>
                                <td>{{ $commission->investment?->investment_code ?? '#'.$commission->investment_id }}</td>
                                <td>{{ number_format($commission->rate_percent, 2) }}%</td>
                                <td>₹{{ number_format($commission->amount, 2) }}</td>
                                <td>{{ $commission->created_at->format('d M Y') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-center text-muted">No referral commission earned yet.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
    @endif


    <!-- RM Assignment -->
    {{-- <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
            <h5 class="mb-0">Assigned RM By Department</h5>
        </div>
        <div class="card-body p-3">
            <div class="row">
                <div class="col-md-3 col-6 mb-2">
                    <div class="small text-muted">Personal Loan*</div>

                    @foreach($user->loans as $loan)
                    <div class="mb-2">
                        Loan Product: {{ $loan->loan_product->name ?? '-' }}
                    </div>
                @endforeach
                </div>

                <div class="col-md-3 col-6 mb-2">
                    <div class="small text-muted">Home Loan*</div>
                    <div>{{ $user->home_loan  ?? "N/A"}}</div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="small text-muted">Business Loan*</div>
                    <div>{{ $user->business_loan  ?? "N/A"}}</div>
                </div>
                <div class="col-md-3 col-6 mb-2">
                    <div class="small text-muted">Car Loan*</div>
                    <div>{{ $user->car_loan  ?? "N/A"}}</div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-2">
                    <div class="small text-muted">Used Car Loan*</div>
                    <div>{{ $user->used_car_loan  ?? "N/A"}}</div>
                </div>
                <div class="col-md-6 mb-2">
                    <div class="small text-muted">Loan Against Property*</div>
                    <div>{{ $user->loan_against_property  ?? "N/A"}}</div>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
            {{-- <h5 class="mb-0">Assigned RM By Department</h5> --}}
            <h5 class="mb-0">Loan Deatils</h5>
        </div>
        <div class="card-body p-3">
            <div class="row">
                {{-- Personal Loan --}}
                <div class="col-md-3 col-6 mb-2">
                    <div class="small text-muted">Personal Loan*</div>
                    @php
                        $personalLoans = $user->loans->filter(fn($loan) => $loan->loan_product_id == 1);
                    @endphp
                    @if($personalLoans->isNotEmpty())
                        @foreach($personalLoans as $loan)
                            <div class="mb-2">{{ $loan->loan_product->name ?? '-' }}</div>
                        @endforeach
                    @else
                        <div>N/A</div>
                    @endif
                </div>

                {{-- Home Loan --}}
                <div class="col-md-3 col-6 mb-2">
                    <div class="small text-muted">Home Loan*</div>
                    @php
                        $homeLoans = $user->loans->filter(fn($loan) => $loan->loan_product_id == 2);
                    @endphp
                    @if($homeLoans->isNotEmpty())
                        @foreach($homeLoans as $loan)
                            <div class="mb-2">{{ $loan->loan_product->name ?? '-' }}</div>
                        @endforeach
                    @else
                        <div>N/A</div>
                    @endif
                </div>

                {{-- Business Loan --}}
                <div class="col-md-3 col-6 mb-2">
                    <div class="small text-muted">Business Loan*</div>
                    @php
                        $businessLoans = $user->loans->filter(fn($loan) => $loan->loan_product_id == 3);
                    @endphp
                    @if($businessLoans->isNotEmpty())
                        @foreach($businessLoans as $loan)
                            <div class="mb-2">{{ $loan->loan_product->name ?? '-' }}</div>
                        @endforeach
                    @else
                        <div>N/A</div>
                    @endif
                </div>

                {{-- Car Loan --}}
                <div class="col-md-3 col-6 mb-2">
                    <div class="small text-muted">Car Loan*</div>
                    @php
                        $carLoans = $user->loans->filter(fn($loan) => $loan->loan_product_id == 4);
                    @endphp
                    @if($carLoans->isNotEmpty())
                        @foreach($carLoans as $loan)
                            <div class="mb-2">{{ $loan->loan_product->name ?? '-' }}</div>
                        @endforeach
                    @else
                        <div>N/A</div>
                    @endif
                </div>
            </div>

            {{-- <div class="row">

                <div class="col-md-6 mb-2">
                    <div class="small text-muted">Used Car Loan*</div>
                    @php
                        $usedCarLoans = $user->loans->filter(fn($loan) => $loan->loan_product_id == 5);
                    @endphp
                    @if($usedCarLoans->isNotEmpty())
                        @foreach($usedCarLoans as $loan)
                            <div class="mb-2">{{ $loan->loan_product->name ?? '-' }}</div>
                        @endforeach
                    @else
                        <div>N/A</div>
                    @endif
                </div>


                <div class="col-md-6 mb-2">
                    <div class="small text-muted">Loan Against Property*</div>
                    @php
                        $lapLoans = $user->loans->filter(fn($loan) => $loan->loan_product_id == 6);
                    @endphp
                    @if($lapLoans->isNotEmpty())
                        @foreach($lapLoans as $loan)
                            <div class="mb-2">{{ $loan->loan_product->name ?? '-' }}</div>
                        @endforeach
                    @else
                        <div>N/A</div>
                    @endif
                </div>
            </div> --}}
        </div>
    </div>


    <!-- Documents -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
            <h5 class="mb-0">Profile and KYC Documents</h5>
        </div>
        <div class="card-body p-3">
            <div class="row">
                {{-- Self Photo --}}
                @if($user->profile_picture)
                    <div class="col-md-3 col-6 mb-3">
                        <div class="document-card">
                            <img class="img-fluid document-image" src="{{ profile_picture($user->profile_picture) }}" alt="Profile Photo" style="height: 180px; object-fit: contain; width: 100%; cursor: pointer;">
                            <div class="mt-1 text-center small">Profile Photo</div>
                        </div>
                    </div>
                @endif

                {{-- PAN Photo --}}
                @if($user->pan_photo)
                    @php
                        $panName = explode('_', $user->pan_photo, 2)[1] ?? $user->pan_photo;
                    @endphp
                    <div class="col-md-3 col-6 mb-3">
                        <div class="document-card">
                            <img class="img-fluid document-image" src="{{ asset('public/uploads/documents/' . $user->pan_photo) }}" alt="PAN Card" style="height: 180px; object-fit: contain; width: 100%; cursor: pointer;">
                            <!--<div class="mt-1 text-center small">{{ $panName }}</div>-->
                            <div class="mt-1 text-center small">Pencard</div>
                        </div>
                    </div>
                @endif

                {{-- Aadhar Front Photo --}}
                @if($user->addhaar_front)
                    @php
                        $aadhaarFrontName = explode('_', $user->addhaar_front, 2)[1] ?? $user->addhaar_front;
                    @endphp
                    <div class="col-md-3 col-6 mb-3">
                        <div class="document-card">
                            <img class="img-fluid document-image" src="{{ asset('public/uploads/documents/' . $user->addhaar_front) }}" alt="Aadhar Front" style="height: 180px; object-fit: contain; width: 100%; cursor: pointer;">
                            <!--<div class="mt-1 text-center small">{{ $aadhaarFrontName }}</div>-->
                            <div class="mt-1 text-center small">Addhaar Front</div>
                        </div>
                    </div>
                @endif

                
                {{-- Aadhar Back Photo --}}
                @if($user->addhaar_back)
                    @php
                        $aadhaarBackName = explode('_', $user->addhaar_back, 2)[1] ?? $user->addhaar_back;
                    @endphp
                    <div class="col-md-3 col-6 mb-3">
                        <div class="document-card">
                            <img class="img-fluid document-image" src="{{ asset('public/uploads/documents/' . $user->addhaar_back) }}" alt="Aadhar Back" style="height: 180px; object-fit: contain; width: 100%; cursor: pointer;">
                            <!--<div class="mt-1 text-center small">{{ $aadhaarBackName }}</div>-->
                            <div class="mt-1 text-center small">Addhaar Rear</div>
                        </div>
                    </div>
                @endif

                {{-- Other Documents --}}
                @if($user->documents)
                    @foreach(json_decode($user->documents, true) as $doc)
                        @php
                            $ext = strtolower(pathinfo($doc, PATHINFO_EXTENSION));
                            $fileName = preg_replace('/^\d+_/', '', $doc);
                        @endphp
                        <div class="col-md-3 col-6 mb-3">
                            <div class="document-card">
                                @if(in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif']))
                                    <img class="img-fluid document-image" src="{{ asset('public/uploads/documents/' . $doc) }}" alt="{{ $fileName }}" style="height: 180px; object-fit: contain; width: 100%; cursor: pointer;">
                                @elseif(in_array($ext, ['pdf', 'doc', 'docx']))
                                    <div class="file-preview" style="height: 180px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; border: 1px solid #dee2e6;">
                                        @if($ext == 'pdf')
                                            <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                        @else
                                            <i class="fas fa-file-word fa-3x text-primary"></i>
                                        @endif
                                    </div>
                                    <div class="text-center mt-1">
                                        <a href="{{ asset('public/uploads/documents/' . $doc) }}" target="_blank" class="btn btn-sm btn-primary">
                                            View
                                        </a>
                                    </div>
                                @else
                                    <div class="file-preview" style="height: 180px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; border: 1px solid #dee2e6;">
                                        <i class="fas fa-file fa-3x text-muted"></i>
                                    </div>
                                @endif
                                <div class="mt-1 text-center small">{{ $fileName }}</div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Document Preview</h5>
                {{-- <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button> --}}
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Document" class="img-fluid">
            </div>
            <div class="modal-footer">
                {{-- <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button> --}}
                {{-- <a id="downloadBtn" href="" class="btn btn-primary" download>Download</a> --}}
                <a id="downloadBtn" href="" ></a>
            </div>
        </div>
    </div>
</div>

<style>
    .document-card {
        border: 1px solid #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        transition: transform 0.2s;
    }

    .document-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }

    .card {
        border-radius: 6px;
    }

    .card-header {
        border-radius: 6px 6px 0 0 !important;
    }

    .document-image:hover {
        opacity: 0.9;
    }
</style>

<!-- Font Awesome for icons -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Get all document images
        const documentImages = document.querySelectorAll('.document-image');

        // Get modal elements
        const imageModal = new bootstrap.Modal(document.getElementById('imageModal'));
        const modalImage = document.getElementById('modalImage');
        const downloadBtn = document.getElementById('downloadBtn');

        // Add click event to each document image
        documentImages.forEach(function(image) {
            image.addEventListener('click', function() {
                // Set modal image source
                modalImage.src = this.src;

                // Set download button href
                downloadBtn.href = this.src;

                // Extract filename from the image alt attribute or src
                const fileName = this.alt || this.src.split('/').pop();
                downloadBtn.setAttribute('download', fileName);

                // Show the modal
                imageModal.show();
            });
        });
    });
</script>

@endsection
