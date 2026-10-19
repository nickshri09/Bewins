<!-- KYC Documents Section -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <span class="header-title">{{ _lang('KYC Documents') }}</span>
            </div>
            <div class="card-body">
                @if($user->documents && count($user->documents) > 0)
                    <div class="row">
                        @foreach($user->documents as $document)
                            <div class="col-md-3 mb-3">
                                <div class="document-card">
                                    <a href="{{ asset('uploads/documents/' . $document) }}" target="_blank">
                                        <div class="document-preview">
                                            @if(in_array(pathinfo($document, PATHINFO_EXTENSION), ['jpg', 'jpeg', 'png', 'gif']))
                                                <img src="{{ asset('uploads/documents/' . $document) }}" class="img-fluid" alt="Document">
                                            @else
                                                <div class="file-icon">
                                                    <i class="fas fa-file-pdf fa-3x text-danger"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="document-name text-center mt-2">{{ $document }}</div>
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p>{{ _lang('No KYC documents uploaded') }}</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Aadhaar and PAN Documents -->
<div class="row mt-4">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <span class="header-title">{{ _lang('Identity Documents') }}</span>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Aadhaar Front -->
                    @if($user->addhaar_front)
                        <div class="col-md-3 mb-3">
                            <div class="document-card">
                                <a href="{{ asset('uploads/documents/' . $user->addhaar_front) }}" target="_blank">
                                    <div class="document-preview">
                                        <img src="{{ asset('uploads/documents/' . $user->addhaar_front) }}" class="img-fluid" alt="Aadhaar Front">
                                    </div>
                                    <div class="document-name text-center mt-2">Aadhaar Front</div>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- Aadhaar Back -->
                    @if($user->addhaar_back)
                        <div class="col-md-3 mb-3">
                            <div class="document-card">
                                <a href="{{ asset('uploads/documents/' . $user->addhaar_back) }}" target="_blank">
                                    <div class="document-preview">
                                        <img src="{{ asset('uploads/documents/' . $user->addhaar_back) }}" class="img-fluid" alt="Aadhaar Back">
                                    </div>
                                    <div class="document-name text-center mt-2">Aadhaar Back</div>
                                </a>
                            </div>
                        </div>
                    @endif

                    <!-- PAN Card -->
                    @if($user->pan_photo)
                        <div class="col-md-3 mb-3">
                            <div class="document-card">
                                <a href="{{ asset('uploads/documents/' . $user->pan_photo) }}" target="_blank">
                                    <div class="document-preview">
                                        <img src="{{ asset('uploads/documents/' . $user->pan_photo) }}" class="img-fluid" alt="PAN Card">
                                    </div>
                                    <div class="document-name text-center mt-2">PAN Card</div>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.document-card {
    border: 1px solid #ddd;
    border-radius: 5px;
    padding: 10px;
    height: 100%;
}

.document-preview {
    height: 200px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
}

.document-preview img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.file-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
}

.document-name {
    font-size: 14px;
    word-break: break-all;
}
</style>