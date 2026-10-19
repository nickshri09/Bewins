@extends('layouts.app')

@section('content')
    <form method="post" class="validate" autocomplete="off" action="{{ route('members.store') }}" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h4 class="header-title">{{ _lang('Add New Client') }}</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('First Name') }}</label>
                                    <input type="text" class="form-control" name="first_name"
                                        value="{{ old('first_name') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Last Name') }}</label>
                                    <input type="text" class="form-control" name="last_name"
                                        value="{{ old('last_name') }}" required>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Client No') }}</label>
                                    <input type="text" class="form-control" name="member_no"
                                        value="{{ $memberNo != '' ? $memberNo : old('member_no') }}" required
                                        {{ $memberNo != '' ? 'readonly' : '' }}>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Referred By Code') }}</label>
                                    <input type="text" class="form-control text-uppercase" name="referrer_code" value="{{ old('referrer_code') }}" placeholder="RB0001 or RBU0001">
                                    <small class="form-text text-muted">{{ _lang('Use a client RB code for 3-level referral, or an office user RBU code for direct-agent commission.') }}</small>
                                    @error('referrer_code')<small class="text-danger">{{ $message }}</small>@enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Business Name') }}</label>
                                    <input type="text" class="form-control" name="business_name"
                                        value="{{ old('business_name') }}">
                                </div>
                            </div>

                            @if (auth()->user()->user_type == 'admin')
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Branch') }}</label>
                                        <select class="form-control select2" name="branch_id">
                                            <option value="">{{ get_option('default_branch_name', 'Main Branch') }}
                                            </option>
                                            {{ create_option('branches', 'id', 'name', auth()->user()->branch_id) }}
                                        </select>
                                    </div>
                                </div>
                            @else
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="control-label">{{ _lang('Branch') }}</label>
                                        <select class="form-control" name="branch_id" disabled>
                                            <option value="">{{ get_option('default_branch_name', 'Main Branch') }}
                                            </option>
                                            {{ create_option('branches', 'id', 'name', auth()->user()->branch_id) }}
                                        </select>
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Email') }}</label>
                                    <input type="text" class="form-control" name="email" value="{{ old('email') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Country Code') }}</label>
                                    <select class="form-control select2" name="country_code">
                                        <option value="">{{ _lang('Country Code') }}</option>
                                        @foreach (get_country_codes() as $key => $value)
                                            <option value="{{ $value['dial_code'] }}"
                                                {{ old('country_code') == $value['dial_code'] ? 'selected' : '' }}>
                                                {{ $value['country'] . ' (+' . $value['dial_code'] . ')' }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Mobile') }}</label>
                                    <input type="text" class="form-control" name="mobile" value="{{ old('mobile') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Gender') }}</label>
                                    <select class="form-control auto-select" data-selected="{{ old('gender') }}"
                                        name="gender">
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="male">{{ _lang('Male') }}</option>
                                        <option value="male">{{ _lang('Female') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('City') }}</label>
                                    <input type="text" class="form-control" name="city" value="{{ old('city') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('State') }}</label>
                                    <input type="text" class="form-control" name="state" value="{{ old('state') }}">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Zip') }}</label>
                                    <input type="text" class="form-control" name="zip"
                                        value="{{ old('zip') }}">
                                </div>
                            </div>

                            <!--Custom Fields-->
                            @if (!$customFields->isEmpty())
                                @foreach ($customFields as $customField)
                                    <div class="{{ $customField->field_width }}">
                                        <div class="form-group">
                                            <label class="control-label">{{ $customField->field_name }}</label>
                                            {!! xss_clean(generate_input_field($customField)) !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Credit Source') }}</label>
                                    <input type="text" class="form-control" name="credit_source"
                                        value="{{ old('credit_source') }}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Address') }}</label>
                                    <textarea class="form-control" name="address">{{ old('address') }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Photo') }}</label>
                                    <input type="file" class="form-control dropify" name="photo">
                                </div>
                            </div>

                            <!-- PAN & Aadhaar Section -->
                            <div class="col-md-12 mt-4">
                                <h5 class="pb-2 text-secondary d-flex align-items-center">
                                    <i class="ti-id-badge mr-2"></i> Identification Details
                                </h5>
                            </div>

                            <!-- Keep same row structure as rest of form -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('PAN Card Number') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="pan_card" id="pan_card"
                                            maxlength="10" placeholder="Enter PAN (e.g., ABCDE1234F)" required>
                                        <button type="button" class="btn btn-outline-primary"
                                            id="verify_pan_btn">Verify</button>
                                    </div>
                                    <small id="pan_message" class="form-text mt-1" style="display:none;"></small>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="control-label">{{ _lang('Upload PAN Card Image') }}</label>
                                    <input type="file" class="form-control dropify" name="pancard_image"
                                        data-height="120" data-max-file-size="2M"
                                        data-allowed-file-extensions="jpg jpeg png" data-show-remove="true">
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Aadhaar Card Number') }}</label>
                                    <input type="text" class="form-control" name="aadhaar_no" id="aadhaar_no"
                                        value="{{ old('aadhaar_no') }}" placeholder="Enter Aadhaar No">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="control-label">{{ _lang('Aadhaar Front Image') }}</label>
                                            <input type="file" class="form-control dropify" name="aadhaar_front"
                                                data-height="120" data-max-file-size="2M"
                                                data-allowed-file-extensions="jpg jpeg png" data-show-remove="true">
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="control-label">{{ _lang('Aadhaar Back Image') }}</label>
                                            <input type="file" class="form-control dropify" name="aadhaar_back"
                                                data-height="120" data-max-file-size="2M"
                                                data-allowed-file-extensions="jpg jpeg png" data-show-remove="true">
                                        </div>
                                    </div>
                                </div>
                            </div>






                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="ti-check-box"></i>&nbsp;{{ _lang('Submit') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="togglebutton">
                            <h4 class="header-title d-flex align-items-center">{{ _lang('Login Details') }}&nbsp;&nbsp;
                                <input type="checkbox" id="client_login" value="1" name="client_login">
                            </h4>
                        </div>
                    </div>
                    <div class="card-body" id="client_login_card">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Name') }}</label>
                                    <input type="text" class="form-control" name="name"
                                        value="{{ old('name') }}">
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Email') }}</label>
                                    <input type="text" class="form-control" name="login_email"
                                        value="{{ old('login_email') }}">
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
                                    <select class="form-control select2 auto-select" data-selected="{{ old('status') }}"
                                        name="status">
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="1">{{ _lang('Active') }}</option>
                                        <option value="0">{{ _lang('In Active') }}</option>
                                    </select>
                                </div>
                            </div>
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
        });
    </script>

    <style>
        h5.text-secondary {
            font-weight: 600;
            border-bottom: 1px solid #ddd;
            padding-bottom: 6px;
            margin-bottom: 15px;
        }
    </style>
@endpush
