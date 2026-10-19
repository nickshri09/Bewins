@extends('layouts.app')

@section('content')
<div class="">
    <!-- Page Heading -->
    <h4 class="mb-3 font-weight-bold">New Lead</h4>

    <!-- Breadcrumb + Add Button -->
    <div class="bg-light p-3 rounded mb-4">
        <div class="d-flex justify-content-between align-items-center">
            <strong>
                {{ $serviceCategories[$category]['name'] }}
                @if($subCategory)
                    > {{ $serviceCategories[$category]['sub_categories'][$subCategory] }}
                @endif
            </strong>
           
          
            <!--<div class="d-flex align-items-center gap-2">-->
            <!--    <a href="{{ route('services.create', ['category' => $category, 'subCategory' => $subCategory]) }}" class="btn btn-success">-->
            <!--        <i class="fas fa-plus-circle"></i> Add-->
            <!--    </a>-->
            <!--</div>-->
            
            @if(auth()->user()->user_type != 'user')
                <div class="d-flex align-items-center gap-2">
                    <a href="{{ route('services.create', ['category' => $category, 'subCategory' => $subCategory]) }}" class="btn btn-success">
                        <i class="fas fa-plus-circle"></i> Add
                    </a>
                </div>
            @endif

          
        </div>
    </div>

    <!-- Custom Services List -->
    <div class="row">
        @forelse($customServices as $customService)
            <div class="col-md-4 mb-3">
                <a href="{{ route('services.show', ['category' => $category, 'subCategory' => $subCategory, 'id' => $customService->id]) }}" class="btn btn-outline-primary w-100">
                    {{ $customService->custom_service_name ?? 'N/A' }}
                </a>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center">
                    No custom services found.
                </div>
            </div>
        @endforelse
    </div>
    
     <style>
            .btn-outline-primary {
                white-space: normal;

                word-break: break-word;
                
            }
        </style>

</div>
<div style="padding-top: 100px; margin-left:-40px; margin-right:-40px; margin-bottom:-50px;">
<style>
    .footer-modern{background:linear-gradient(135deg,#3a3636eb 0%,#000000 100%);box-shadow:0 -4px 20px rgba(13,110,253,.06)}
    .footer-title{color:#fd590d}
    .footer-link{color:#ffffff}
     .footer-link:hover{color:#12ed0a;text-decoration:none}
    .footer-sep{border-color:rgba(13,110,253,.25)!important}
    .footer-note{color:#ffffff}
    .footer-muted{color:#6c757d}
</style>

<!-- Modern Light Footer -->
<footer class="mt-5 footer-modern text-dark border-top footer-sep" >
    <div class="container-fluid px-4 px-md-5 py-5">
        <div class="row gy-4">
            <div class="col-6 col-md-3">
                <h6 class="fw-bold footer-title mb-3">Bewins</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="{{ route('login-page') }}" class="footer-link">Franchisee Login</a></li>
                    <li><a href="{{ route('login-page') }}" class="footer-link">BCP Login</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h6 class="fw-bold footer-title mb-3">Bewins Money</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="{{ route('login-page') }}" class="footer-link">Agent Login</a></li>
                    <li><a href="{{ route('login-page') }}" class="footer-link">Distributor Login</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h6 class="fw-bold footer-title mb-3">Loan Lenders</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="{{ route('login-page') }}" class="footer-link">Connector Login</a></li>
                    <li class="mb-2"><a href="{{ route('login-page') }}" class="footer-link">BCP Login</a></li>
                    <li class="mb-2"><a href="#" class="footer-link">Banners</a></li>
                    <li class="mb-2"><a href="{{ route('certificate.view') }}" class="footer-link">Certificate Download</a></li>
                    <li><a href=" {{ route('loan_agreement.view') }} " class="footer-link">Authorisation Letter Download</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-3">
                <h6 class="fw-bold footer-title mb-3">Quick Links</h6>
                <ul class="list-unstyled small mb-0">
                    <li class="mb-2"><a href="{{ route('knowledge.index') }}" class="footer-link">Knowledge Base</a></li>
                    <li class="mb-2"><a href="{{ route('bewins.vedio') }}" class="footer-link">Video Tutorials</a></li>
                    <li class="mb-2"><a href="#" class="footer-link">Media</a></li>
                    <li><a href="https://anydesk.com/en/downloads/thank-you?dv=win_exe" class="footer-link">Download AnyDesk</a></li>
                </ul>
            </div>
        </div>

        <hr class="my-4 footer-sep">

        <div class="small footer-note mb-3">
            <strong>Attention:</strong> Bewins OR LOAN LENDERS never asks any details related to debit cards and credit cards and Net Banking like CVV, OTP, SMS or any other highly confidential information. If an BEWINS OR LOAN LENDERS customer receives any such mail or phone call, then they should understand that this is an online trick to steal their money. For any report, kindly email us at hello@bewins.in
        </div>

        <div class="d-flex align-items-start gap-2 small mb-3 footer-note">
            <i class="fas fa-map-marker-alt text-primary mt-1"> </i>
            <span><strong style="margin-left:1em">Delhi</strong> Head Office: Textile Colony Dumraon, Dist- Buxar - 802119</span>
        </div>

        <div class="small footer-muted">
            Bewins is present in more than 25 states of India- ANDHRA PRADESH | ASSAM | BIHAR | CHANDIGARH | CHHATTISGARH | DELHI | GUJARAT | HIMACHAL PRADESH | HARYANA | JHARKHAND | JAMMU AND KASHMIR | KARNATAKA | MAHARASHTRA | MADHYA PRADESH | ORISSA | PUNJAB | RAJASTHAN | SIKKIM | TAMIL NADU | TELANGANA | UTTARAKHAND | UTTAR PRADESH | WEST BENGAL. With lots of <i class="fas fa-heart text-danger"></i> from Bewins Software Private Limited.
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start small footer-muted pt-4 mt-4 border-top footer-sep">
            <div>
                Copyright © 2019-2025 <a href="#" class="text-decoration-none" style="color:#0d6efd">Bewins</a>. All rights reserved.
            </div>
            <div class="mt-2 mt-md-0">Version 1.0.1</div>
        </div>
    </div>
</footer>
</div>
@endsection
