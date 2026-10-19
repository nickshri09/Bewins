@extends('layouts.app')

@section('content')
<form method="post" class="validate" autocomplete="off" action="{{ route('members.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<div class="row">
		<div class="col-lg-8">
			<div class="card">
				<div class="card-header">
					<h4 class="header-title">{{ _lang('Update Client Information') }}</h4>
				</div>
				<div class="card-body">
					<!--<input name="_method" type="hidden" value="PATCH">-->
					<input name="_method" type="hidden" value="PUT">
					<div class="row">
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('First Name') }}</label>
								<input type="text" class="form-control" name="first_name" value="{{ $member->first_name }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Last Name') }}</label>
								<input type="text" class="form-control" name="last_name" value="{{ $member->last_name }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Client No') }}</label>
								<input type="text" class="form-control" name="member_no" value="{{ $member->member_no }}" required>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Referred By Code') }}</label>
								<input type="text" class="form-control text-uppercase" name="referrer_code" value="{{ old('referrer_code', $member->referrer?->referral_code ?: $member->referredByUser?->referral_code) }}" placeholder="RB0001 or RBU0001">
								<small class="form-text text-muted">{{ _lang('Leave blank if this client has no referrer.') }}</small>
								@error('referrer_code')<small class="text-danger">{{ $message }}</small>@enderror
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Business Name') }}</label>
								<input type="text" class="form-control" name="business_name" value="{{ $member->business_name }}">
							</div>
						</div>

						@if(auth()->user()->user_type == 'admin')
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Branch') }}</label>
								<select class="form-control select2" name="branch_id">
									<option value="">{{ get_option('default_branch_name', 'Main Branch') }}</option>
									{{ create_option('branches', 'id', 'name', $member->branch_id) }}
                                </select>
							</div>
						</div>
						@else
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Branch') }}</label>
								<select class="form-control" name="branch_id" disabled>
									<option value="">{{ get_option('default_branch_name', 'Main Branch') }}</option>
									{{ create_option('branches', 'id', 'name', $member->branch_id) }}
                                </select>
							</div>
						</div>
						@endif

						@if(auth()->user()->user_type == 'admin')
						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Assigned User Profile') }}</label>
								<select class="form-control select2" name="assigned_user_id">
									<option value="">{{ _lang('Unassigned') }}</option>
									@foreach($assignees as $assignee)
									<option value="{{ $assignee->id }}" {{ (string) old('assigned_user_id', $member->assigned_user_id) === (string) $assignee->id ? 'selected' : '' }}>
											{{ $assignee->name }} ({{ $assignee->email }})
										</option>
									@endforeach
								</select>
								<small class="text-muted">{{ _lang('Reassigning changes which user profile owns this client. The client login remains unchanged.') }}</small>
							</div>
						</div>
						@endif

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Email') }}</label>
								<input type="text" class="form-control" name="email" value="{{ $member->email }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Country Code') }}</label>
								<select class="form-control select2 auto-select" name="country_code" data-selected="{{ $member->country_code }}">
									<option value="">{{ _lang('Country Code') }}</option>
									@foreach(get_country_codes() as $key => $value)
									<option value="{{ $value['dial_code'] }}">{{ $value['country'].' (+'.$value['dial_code'].')' }}</option>
									@endforeach
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Mobile') }}</label>
								<input type="text" class="form-control" name="mobile" value="{{ old('mobile',$member->mobile) }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Gender') }}</label>
								<select class="form-control auto-select" data-selected="{{ $member->gender }}" name="gender">
									<option value="">{{ _lang('Select One') }}</option>
									<option value="male">{{ _lang('Male') }}</option>
									<option value="female">{{ _lang('Female') }}</option>
								</select>
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('City') }}</label>
								<input type="text" class="form-control" name="city" value="{{ $member->city }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('State') }}</label>
								<input type="text" class="form-control" name="state" value="{{ $member->state }}">
							</div>
						</div>

						<div class="col-md-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Zip') }}</label>
								<input type="text" class="form-control" name="zip" value="{{ $member->zip }}">
							</div>
						</div>

						<!--Custom Fields-->
						@if(! $customFields->isEmpty())
							@php $customFieldsData = json_decode($member->custom_fields, true); @endphp
							@foreach($customFields as $customField)
							<div class="{{ $customField->field_width }}">
								<div class="form-group">
									<label class="control-label">{{ $customField->field_name }}</label>	
									{!! xss_clean(generate_input_field($customField, $customFieldsData[$customField->field_name]['field_value'] ?? null)) !!}
								</div>
							</div>
							@endforeach
                        @endif

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Credit Source') }}</label>
								<input type="text" class="form-control" name="credit_source" value="{{ $member->credit_source }}">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Address') }}</label>
								<textarea class="form-control" name="address">{{ $member->address }}</textarea>
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Photo') }}</label>
								<input type="file" class="form-control dropify" name="photo" data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG" data-default-file="{{ profile_picture($member->photo) }}">
							</div>
						</div>
						<!-- PAN & Aadhaar Section -->
<div class="col-md-12 mt-4">
    <h5 class="pb-2 text-secondary d-flex align-items-center">
        <i class="ti-id-badge mr-2"></i> Identification Details
    </h5>
</div>

<!-- PAN Card Details -->
<div class="col-md-6">
    <div class="form-group">
        <label class="control-label">{{ _lang('PAN Card Number') }}</label>
        <div class="input-group">
            <input type="text" class="form-control" name="pan_card" id="pan_card"
                   value="{{ $member->pancard_no ?? '' }}"
                   maxlength="10" placeholder="Enter PAN (e.g., ABCDE1234F)">
            <button type="button" class="btn btn-outline-primary" id="verify_pan_btn">Verify</button>
        </div>
        <small id="pan_message" class="form-text mt-1" style="display:none;"></small>
    </div>

    <div class="form-group mb-3">
        <label class="control-label">{{ _lang('Upload PAN Card Image') }}</label>
        <input type="file" class="form-control dropify"
               name="pancard_image"
               data-height="120"
               data-max-file-size="2M"
               data-allowed-file-extensions="jpg jpeg png"
               data-default-file="{{ isset($member->pancard_image) ? asset('uploads/pancards/'.$member->pancard_image) : '' }}">
    </div>
</div>

<!-- Aadhaar Details -->
<div class="col-md-6">
    <div class="form-group">
        <label class="control-label">{{ _lang('Aadhaar Card Number') }}</label>
        <input type="text" class="form-control" name="aadhaar_no" id="aadhaar_no"
               value="{{ $member->aadhaar_no ?? '' }}" placeholder="Enter Aadhaar No">
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="control-label">{{ _lang('Aadhaar Front Image') }}</label>
                <input type="file" class="form-control dropify"
                       name="aadhaar_front"
                       data-height="120"
                       data-max-file-size="2M"
                       data-allowed-file-extensions="jpg jpeg png"
                       data-default-file="{{ isset($member->aadhaar_front) ? asset('uploads/aadhaar/'.$member->aadhaar_front) : '' }}">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group mb-3">
                <label class="control-label">{{ _lang('Aadhaar Back Image') }}</label>
                <input type="file" class="form-control dropify"
                       name="aadhaar_back"
                       data-height="120"
                       data-max-file-size="2M"
                       data-allowed-file-extensions="jpg jpeg png"
                       data-default-file="{{ isset($member->aadhaar_back) ? asset('uploads/aadhaar/'.$member->aadhaar_back) : '' }}">
            </div>
        </div>
    </div>
</div>


						<div class="col-md-12">
							<div class="form-group">
								<button type="submit" class="btn btn-primary"><i class="ti-check-box"></i>&nbsp;{{ _lang('Update') }}</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-lg-4">
			<div class="card">
				<div class="card-header">
					@if($member->user_id == NULL)
						<div class="togglebutton">
							<h4 class="header-title d-flex align-items-center">{{ _lang('Login Details') }}&nbsp;&nbsp;
								<input type="checkbox" id="client_login" value="1" name="client_login">
							</h4>
						</div>
					@else
						<h4 class="header-title">{{ _lang('Login Details') }}</h4>
						<input type="hidden" value="1" name="client_login">
					@endif
				</div>
				<div class="card-body" {{ $member->user_id == NULL ? 'id=client_login_card' : '' }}>
					<div class="row">
						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Name') }}</label>
								<input type="text" class="form-control" name="name" value="{{ $member->user->name }}">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Email') }}</label>
								<input type="text" class="form-control" name="login_email" value="{{ $member->user->email }}">
							</div>
						</div>


						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Password') }}</label>
								<input type="password" class="form-control" name="password">
							</div>
						</div>

						<div class="col-md-12">
							<div class="form-group">
								<label class="control-label">{{ _lang('Status') }}</label>
								<select class="form-control select2 auto-select" data-selected="{{ $member->user->status }}" name="status">
									<option value="">{{ _lang('Select One') }}</option>
									<option value="1">{{ _lang('Active') }}</option>
									<option value="0">{{ _lang('In Active') }}</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="card mt-3">
				<div class="card-header">
					<h4 class="header-title">{{ _lang('Referral Code') }}</h4>
				</div>
				<div class="card-body">
					<div class="form-group mb-0">
						<div class="input-group">
							<input type="text" class="form-control" id="referral_code" value="{{ $member->referral_code }}" readonly>
							<div class="input-group-append">
								<button type="button" class="btn btn-primary" id="copy_referral_code" aria-label="Copy referral code" title="Copy referral code">
									<i class="far fa-copy"></i> {{ _lang('Copy') }}
								</button>
							</div>
						</div>
						<small class="form-text text-muted">{{ _lang('Share this code to identify referrals for this client.') }}</small>
					</div>
				</div>
			</div>
			<div class="card mt-3">
				<div class="card-header">
					<h4 class="header-title">{{ _lang('Referral Commission') }}</h4>
				</div>
				<div class="card-body">
					<div class="mb-3">
						{{ _lang('Total Earned') }}:
						<strong>₹{{ number_format($memberCommissionTotal ?? 0, 2) }}</strong>
					</div>
					<div class="mb-3">
						{{ _lang('Referred By') }}:
						<strong>{{ $member->referrer ? $member->referrer->first_name.' '.$member->referrer->last_name.' ('.$member->referrer->referral_code.')' : ($member->referredByUser ? $member->referredByUser->name.' ('.$member->referredByUser->referral_code.')' : '—') }}</strong>
					</div>
					<div class="table-responsive">
						<table class="table table-sm table-bordered mb-0">
							<thead>
								<tr><th>{{ _lang('Level') }}</th><th>{{ _lang('Investment') }}</th><th>{{ _lang('Amount') }}</th></tr>
							</thead>
							<tbody>
								@forelse($memberCommissions as $commission)
								<tr>
									<td>{{ _lang('Level') }} {{ $commission->level }}</td>
									<td>{{ $commission->investment?->investment_code ?? '#'.$commission->investment_id }}</td>
									<td>₹{{ number_format($commission->amount, 2) }}</td>
								</tr>
								@empty
								<tr><td colspan="3" class="text-center text-muted">{{ _lang('No referral commission earned yet') }}</td></tr>
								@endforelse
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>

	</div>
</form>
@endsection


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const panInput = document.getElementById('pan_card');
    const verifyBtn = document.getElementById('verify_pan_btn');
    const message = document.getElementById('pan_message');
    const panPattern = /^[A-Z]{5}[0-9]{4}[A-Z]{1}$/;

    verifyBtn.addEventListener('click', function() {
        const value = panInput.value.trim().toUpperCase();
        panInput.value = value;

        if (panPattern.test(value)) {
            message.style.display = 'block';
            message.className = 'form-text text-success';
            message.textContent = '✅ PAN format verified successfully.';
        } else {
            message.style.display = 'block';
            message.className = 'form-text text-danger';
            message.textContent = '❌ Invalid PAN format. Example: ABCDE1234F';
        }
    });

    const referralCode = document.getElementById('referral_code');
    const copyReferralCode = document.getElementById('copy_referral_code');
    if (referralCode && copyReferralCode) {
        copyReferralCode.addEventListener('click', async function() {
            try {
                if (navigator.clipboard && window.isSecureContext) {
                    await navigator.clipboard.writeText(referralCode.value);
                } else {
                    referralCode.select();
                    document.execCommand('copy');
                    referralCode.setSelectionRange(0, 0);
                }
                const originalContent = copyReferralCode.innerHTML;
                copyReferralCode.innerHTML = '<i class="fas fa-check"></i> Copied';
                setTimeout(function() { copyReferralCode.innerHTML = originalContent; }, 1500);
            } catch (error) {
                referralCode.select();
            }
        });
    }
});
</script>
@endpush



