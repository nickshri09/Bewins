<table class="table table-bordered">
    <tr>
        <td colspan="2" class="text-center">
            <img class="thumb-image-sm img-thumbnail"
                 src="{{ profile_picture($user->profile_picture) }}"
                 alt="Profile Picture">
        </td>
    </tr>


<tr><td>{{ _lang('Profile ID') }}</td><td>{{ $user->user_id }}</td></tr>
<tr><td>{{ _lang('Referral Code') }}</td><td>{{ $user->referral_code ?? '-' }}</td></tr>
<tr><td>{{ _lang('Name') }}</td><td>{{ $user->name }}</td></tr>
<tr><td>{{ _lang('Email') }}</td><td>{{ $user->email }}</td></tr>
<tr><td>{{ _lang('Mobile Number') }}</td><td>{{ $user->mobile_number }}</td></tr>
<tr><td>{{ _lang('User Type') }}</td><td>{{ $user->user_type }}</td></tr>
<tr><td>{{ _lang('User Role') }}</td><td>{{ strtolower((string) $user->user_type) === 'admin' ? _lang('Admin') : $user->role->name }}</td></tr>

@if($user->role && strtolower(trim($user->role->name)) == 'agent')
    <tr>
        <td>{{ _lang('Assigned Manager') }}</td>
        <td>
            @if($user->assigned_manager_id)
                @if($user->assignedManager)
                    {{ $user->assignedManager->name }}
                @else
                    <span class="text-muted">{{ _lang('Manager Not Found') }}</span>
                @endif
            @else
                <span class="text-muted">{{ _lang('Not Assigned') }}</span>
            @endif
        </td>
    </tr>
@endif

<tr><td>{{ _lang('Status') }}</td><td>{!! xss_clean(user_status($user->status)) !!}</td></tr>
<tr><td>{{ _lang('Created At') }}</td><td>{{ $user->created_at }}</td></tr>
<tr><td>{{ _lang('Wallet Amount') }}</td><td>{{ $user->wallet_amount ? $user->wallet_amount : 0 }}</td></tr>

<!-- Aadhaar Front -->
<tr>
    <td>{{ _lang('Aadhaar Front') }}</td>
    <td>
        @if(!empty($user->addhaar_front))
            <div class="d-flex align-items-center gap-2">
                <img src="{{ asset('public/uploads/documents/'.$user->addhaar_front) }}"
                     alt="Aadhaar Front"
                     class="img-thumbnail"
                     style="max-width:150px;">

                 <div class="mt-2 d-flex flex-wrap doc-buttons">
                <a href="{{ asset('public/uploads/documents/'.$user->addhaar_front) }}" target="_blank"
                   class="btn btn-outline-info btn-sm px-2 py-1 d-flex align-items-center gap-1 mb-1" style="margin-left:20px;">
                    <i class="ti-eye small" ></i> <span>{{ _lang('View') }}</span>
                </a>
                <a href="{{ asset('public/uploads/documents/'.$user->addhaar_front) }}" download
                   class="btn btn-outline-primary btn-sm px-2 py-1 d-flex align-items-center gap-1 mb-1">
                    <i class="ti-download small"></i> <span>{{ _lang('Download') }}</span>
                </a>
            </div>
            </div>
        @else
            <span class="text-muted">Not Uploaded</span>
        @endif
    </td>
</tr>

<!-- Aadhaar Back -->
<tr>
    <td>{{ _lang('Aadhaar Back') }}</td>
    <td>
        @if(!empty($user->addhaar_back))
            <div class="d-flex align-items-center">
                <img src="{{ asset('public/uploads/documents/'.$user->addhaar_back) }}"
                     alt="Aadhaar Back"
                     class="img-thumbnail"
                     style="max-width:150px;">
                <!--<a href="{{ asset('public/uploads/documents/'.$user->addhaar_back) }}"-->
                <!--   target="_blank"-->
                <!--   class="btn btn-sm btn-outline-primary"-->
                <!--    style="margin-left:10px;">-->
                <!--    Preview-->
                <!--</a>-->

                 <div class="mt-2 d-flex flex-wrap doc-buttons">
                <a href="{{ asset('public/uploads/documents/'.$user->addhaar_back) }}" target="_blank"
                   class="btn btn-outline-info btn-sm px-2 py-1 d-flex align-items-center gap-1 mb-1" style="margin-left:20px;">
                    <i class="ti-eye small" ></i> <span>{{ _lang('View') }}</span>
                </a>
                <a href="{{ asset('public/uploads/documents/'.$user->addhaar_back) }}" download
                   class="btn btn-outline-primary btn-sm px-2 py-1 d-flex align-items-center gap-1 mb-1">
                    <i class="ti-download small"></i> <span>{{ _lang('Download') }}</span>
                </a>
            </div>
        @else
            <span class="text-muted">Not Uploaded</span>
        @endif
    </td>
</tr>

<!-- PAN Photo -->
<tr>
    <td>{{ _lang('PAN Photo') }}</td>
    <td>
        @if(!empty($user->pan_photo))
            <div class="d-flex align-items-center">
                <img src="{{ asset('public/uploads/documents/'.$user->pan_photo) }}"
                     alt="PAN Photo"
                     class="img-thumbnail"
                     style="max-width:150px;">
                <!--<a href="{{ asset('public/uploads/documents/'.$user->pan_photo) }}"-->
                <!--   target="_blank"-->
                <!--   class="btn btn-sm btn-outline-primary"-->
                <!--   style="margin-left:10px;">-->
                <!--    Preview-->
                <!--</a>-->

                 <div class="mt-2 d-flex flex-wrap doc-buttons">
                <a href="{{ asset('public/uploads/documents/'.$user->pan_photo) }}" target="_blank"
                   class="btn btn-outline-info btn-sm px-2 py-1 d-flex align-items-center gap-1 mb-1" style="margin-left:20px;">
                    <i class="ti-eye small" ></i> <span>{{ _lang('View') }}</span>
                </a>
                <a href="{{ asset('public/uploads/documents/'.$user->pan_photo) }}" download
                   class="btn btn-outline-primary btn-sm px-2 py-1 d-flex align-items-center gap-1 mb-1">
                    <i class="ti-download small"></i> <span>{{ _lang('Download') }}</span>
                </a>
            </div>
        @else
            <span class="text-muted">Not Uploaded</span>
        @endif
    </td>
</tr>


<tr>
    <td>{{ _lang('Other Documents') }}</td>
    <td>
        @if(isset($user) && $user->documents)
            @php
                $existingDocs = is_array($user->documents)
                    ? $user->documents
                    : (json_decode($user->documents, true) ?? []);
            @endphp

            @if(count($existingDocs) > 0)
                <div class="d-flex flex-wrap gap-3">
                    @foreach($existingDocs as $file)
                        @php
                            $ext = pathinfo($file, PATHINFO_EXTENSION);
                        @endphp
                        <div class="d-flex flex-column align-items-center">
                            @if(in_array(strtolower($ext), ['jpg','jpeg','png','gif','webp']))
                                <img src="{{ asset('public/uploads/documents/'.$file) }}"
                                     alt="{{ $file }}"
                                     class="img-thumbnail"
                                     style="max-width:120px;">
                            @else
                                <div class="preview-file-icon d-flex align-items-center justify-content-center bg-light"
                                     style="width:120px;height:120px;border-radius:6px;">
                                    <i class="fa fa-file fa-2x"></i>
                                </div>
                            @endif

                            <div class="mt-2 d-flex flex-wrap doc-buttons">
                                <a href="{{ asset('public/uploads/documents/'.$file) }}" target="_blank"
                                   class="btn btn-outline-info btn-sm px-2 py-1 d-flex align-items-center gap-1 mb-1">
                                    <i class="ti-eye small"></i> <span>{{ _lang('View') }}</span>
                                </a>
                                <a href="{{ asset('public/uploads/documents/'.$file) }}" download
                                   class="btn btn-outline-primary btn-sm px-2 py-1 d-flex align-items-center gap-1 mb-1">
                                    <i class="ti-download small"></i> <span>{{ _lang('Download') }}</span>
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <span class="text-muted">{{ _lang('No Documents Uploaded') }}</span>
            @endif
        @else
            <span class="text-muted">{{ _lang('No Documents Uploaded') }}</span>
        @endif
    </td>
</tr>


<style>
    .doc-buttons a {
        margin-right: 8px; /* Adjust gap as needed */
    }
</style>

</table>
