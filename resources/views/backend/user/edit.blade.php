@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ _lang('Update User') }}</h4>
                </div>
                <div class="card-body">
                    <form method="post" class="validate" autocomplete="off" action="{{ route('users.update', $id) }}"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input name="_method" type="hidden" value="PATCH">
                        <div class="row">
                            <div class="col-md-8 col-sm-12">
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Profile ID') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="user_id"
                                            value="{{ $user->user_id ?? '' }}" readonly>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Percent (%)') }}</label>
                                    <div class="col-xl-9">
                                        <input type="number" step="0.01" class="form-control" name="percent"
                                            value="{{ $user->percent ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('First Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="name"
                                            value="{{ $user->name }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Middle Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="middle_name"
                                            value="{{ $user->middle_name ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Last Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="last_name"
                                            value="{{ $user->last_name ?? '' }}" required>
                                    </div>
                                </div>

                                <div class="form-group row position-relative">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Password') }}</label>
                                    <div class="col-xl-9">
                                        <div class="input-group">
                                            <input type="password" class="form-control" name="password" id="password_edit"
                                                placeholder="Leave blank to keep unchanged">
                                            <div class="input-group-append">
                                                <span class="input-group-text" onclick="togglePasswordEdit()" style="cursor: pointer;">
                                                    <i id="eyeIconEdit" class="fa fa-eye"></i>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Email') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="email"
                                            value="{{ $user->email }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Mobile Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="mobile_number"
                                            value="{{ $user->mobile_number }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('WhatsApp Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="whatsapp_number"
                                            value="{{ $user->whatsapp_number ?? '' }}" pattern="\d{10}"
                                            title="Enter a valid 10-digit number">
                                    </div>
                                </div>

                                <!-- Aadhaar Images -->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Aadhaar Front Image') }}</label>
                                    <div class="col-xl-9">
                                        <input type="file" class="form-control dropify" name="addhaar_front"
                                            data-height="120" data-max-file-size="2M"
                                            data-allowed-file-extensions="jpg jpeg png"
                                            data-default-file="{{ $user->addhaar_front ? asset('public/uploads/documents/' . $user->addhaar_front) : '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Aadhaar Back Image') }}</label>
                                    <div class="col-xl-9">
                                        <input type="file" class="form-control dropify" name="addhaar_back"
                                            data-height="120" data-max-file-size="2M"
                                            data-allowed-file-extensions="jpg jpeg png"
                                            data-default-file="{{ $user->addhaar_back ? asset('public/uploads/documents/' . $user->addhaar_back) : '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Aadhaar Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" name="aadhar_number" class="form-control"
                                            value="{{ old('aadhar_number', $user->aadhar_number ?? '') }}"
                                            placeholder="1234 5678 9123" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('PAN Photo') }}</label>
                                    <div class="col-xl-9">
                                        <input type="file" class="form-control dropify" name="pan_photo"
                                            data-allowed-file-extensions="jpg jpeg png"
                                            data-default-file="{{ asset('public/uploads/documents/' . $user->pan_photo) }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('PAN Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" name="pan_number" class="form-control"
                                            value="{{ old('pan_number', $user->pan_number ?? '') }}"
                                            placeholder="ABCDE1234F" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('GST Type') }}</label>
                                    <div class="col-xl-9">
                                        <select name="gst_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Regular"
                                                {{ ($user->gst_type ?? '') == 'Regular' ? 'selected' : '' }}>Regular
                                            </option>
                                            <option value="Composition"
                                                {{ ($user->gst_type ?? '') == 'Composition' ? 'selected' : '' }}>
                                                Composition</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('GST Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="gst_number"
                                            value="{{ $user->gst_number ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Residence Address') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="residence_address"
                                            value="{{ $user->residence_address ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Office Address') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="office_address"
                                            value="{{ $user->office_address ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Account Type') }}</label>
                                    <div class="col-xl-9">
                                        <select name="account_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Savings"
                                                {{ ($user->account_type ?? '') == 'Savings' ? 'selected' : '' }}>Savings
                                            </option>
                                            <option value="Current"
                                                {{ ($user->account_type ?? '') == 'Current' ? 'selected' : '' }}>Current
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Account Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="account_name"
                                            value="{{ $user->account_name ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Bank Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="bank_name"
                                            value="{{ $user->bank_name ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('IFSC Code') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="ifsc_code"
                                            value="{{ $user->ifsc_code ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Account Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="account_number"
                                            value="{{ $user->account_number ?? '' }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('User Type') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control auto-select" data-selected="{{ $user->user_type }}"
                                            name="user_type" id="user_type" required>
                                            <option value="">{{ _lang('Select One') }}</option>
                                            <option value="admin">{{ _lang('Admin') }}</option>
                                            <option value="user">{{ _lang('User') }}</option>
                                        </select>
                                        <small class="text-info"><i class="ti-info-alt"></i>
                                            <i>{{ _lang('Admin will get full access and user will get role based access only.') }}</i></small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('User Role') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control select2" data-value="id" data-display="name"
                                            name="role_id" id="role_id">
                                            <option value="">{{ _lang('Select One') }}</option>
                                            {{ create_option('roles', 'id', 'name', $user->role_id) }}
                                        </select>
                                    </div>
                                </div>

                                <!-- Assigned Manager Section - Moved here and improved -->
                                <div class="form-group row" id="assigned_manager_div"
                                    style="{{ strtolower(optional($user->role)->name) == 'dsa' ? '' : 'display:none;' }}">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Assigned To') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control select2" name="assigned_manager_id"
                                            id="assigned_manager_id"
                                            {{ strtolower(optional($user->role)->name) == 'dsa' ? 'required' : '' }}>
                                            <option value="">{{ _lang('Select Manager') }}</option>
                                            @foreach ($managers as $manager)
                                                <option value="{{ $manager->id }}"
                                                    {{ $user->assigned_manager_id == $manager->id ? 'selected' : '' }}>
                                                    {{ $manager->name }}</option>
                                            @endforeach
                                        </select>
                                        <small class="text-muted">{{ _lang('Select a manager for this DSA user') }}</small>
                                    </div>
                                </div>

                                <div class="form-group row" id="employee_designation_div"
                                    style="{{ optional($user->role)->name == 'Employee' ? '' : 'display:none;' }}">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Designation') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="designation"
                                            value="{{ $user->designation }}">
                                    </div>
                                </div>

                                <div class="form-group row" id="employee_salary_div"
                                    style="{{ optional($user->role)->name == 'Employee' ? '' : 'display:none;' }}">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Monthly Salary') }}</label>
                                    <div class="col-xl-9">
                                        <input type="number" step="0.01" class="form-control" name="salary"
                                            value="{{ $user->salary }}">
                                    </div>
                                </div>

                                @php
                                    $defaultBranchId = 50;
                                @endphp

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Branch') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control select2" name="branch_id" id="user_branch_id"
                                            @if ($user->user_type == 'admin') disabled @endif>
                                            @if ($user->user_type == 'admin')
                                                <option value="{{ $defaultBranchId }}" selected>
                                                    {{ _lang('DUMRAON MAIN BRANCH') }}</option>
                                            @else
                                                {{ create_option('branches', 'id', 'name', $user->branch_id) }}
                                            @endif
                                        </select>
                                        <small class="text-info"><i class="ti-info-alt"></i>
                                            <i>{{ _lang('If not assign any branch then user will get default branch access.') }}</i></small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Status') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control auto-select" data-selected="{{ $user->status }}"
                                            name="status" required>
                                            <option value="">{{ _lang('Select One') }}</option>
                                            <option value="1">{{ _lang('Active') }}</option>
                                            <option value="0">{{ _lang('In Active') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Profile Picture') }}</label>
                                    <div class="col-xl-9">
                                        <input type="file" class="form-control dropify" name="profile_picture"
                                            data-allowed-file-extensions="png jpg jpeg PNG JPG JPEG"
                                            data-default-file="{{ profile_picture($user->profile_picture) }}">
                                    </div>
                                </div>

                                <div class="form-group row mb-3">
                                    <label class="col-xl-3 col-form-label">KYC and Other Documents</label>

                                    <div class="col-xl-9">
                                        <!-- NEW FILES INPUT -->
                                        <input type="file" id="documents" name="documents[]" class="form-control"
                                            multiple>

                                        <small class="form-text text-muted mt-2">
                                            Upload PAN card, Aadhaar card, and other related documents.
                                        </small>

                                        <!-- PREVIEW CONTAINER -->
                                        <div id="preview" class="mt-3 d-flex flex-wrap gap-3"></div>

                                        <!-- OLD FILES (already uploaded) -->
                                        @if (isset($user) && $user->documents)
                                            @php
                                                // ALWAYS convert to array
                                                $existingDocs = is_array($user->documents)
                                                    ? $user->documents
                                                    : json_decode($user->documents, true) ?? [];
                                            @endphp

                                            <div class="mt-3">
                                                <h6>Existing Documents:</h6>

                                                <div id="existing-documents" class="d-flex flex-wrap">

                                                    @foreach ($existingDocs as $file)
                                                        <div class="file-preview-item position-relative mr-3 mb-3">
                                                            @php
                                                                $ext = pathinfo($file, PATHINFO_EXTENSION);
                                                            @endphp

                                                            @if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp']))
                                                                <img src="{{ asset('public/uploads/documents/' . $file) }}"
                                                                    class="preview-image">
                                                            @else
                                                                <div class="preview-file-icon">
                                                                    <i class="fa fa-file fa-3x"></i>
                                                                </div>
                                                            @endif

                                                            <div class="preview-content">
                                                                <div class="file-info">
                                                                    <small class="file-name text-truncate d-block"
                                                                        title="{{ $file }}">{{ $file }}</small>
                                                                </div>
                                                            </div>

                                                            <button type="button" class="delete-btn"
                                                                onclick="removeOldFile('{{ $file }}')">
                                                                <i class="fas fa-times"></i>
                                                            </button>

                                                            <input type="hidden" name="old_files[]"
                                                                value="{{ $file }}">
                                                        </div>
                                                    @endforeach

                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <div class="col-xl-9 offset-xl-3">
                                        <button type="submit" class="btn btn-primary"><i
                                                class="ti-check-box"></i>&nbsp;{{ _lang('Update User') }}</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        .file-preview-container {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 10px;
            min-height: 200px;
            gap: 10px;
            display: flex;
            flex-wrap: wrap;
            justify-content: flex-start;
            align-items: flex-start;
        }

        .file-preview-item {
            width: 180px;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            background-color: #fff;
            border: 1px solid #e9ecef;
            position: relative;
        }

        .file-preview-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.12);
        }

        .preview-content {
            padding: 15px;
            text-align: center;
        }

        .preview-image {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
        }

        .preview-file-icon {
            height: 140px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        .file-info {
            margin-top: 12px;
        }

        .file-name {
            font-weight: 500;
            max-width: 100%;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            font-size: 0.9rem;
        }

        .file-size {
            font-size: 0.75rem;
            color: #6c757d;
        }

        .delete-btn {
            position: absolute;
            top: 0px;
            right: 0px;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background-color: #6c757d;
            border: 3px solid white;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(108, 117, 125, 0.4);
            transition: all 0.2s ease;
            z-index: 10;
        }

        .delete-btn:hover {
            background-color: #5a6268;
            transform: scale(1.1);
            box-shadow: 0 3px 10px rgba(108, 117, 125, 0.6);
        }

        .delete-btn i {
            font-size: 14px;
            margin: 0;
        }

        /* Improved form styling */
        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-control:focus {
            border-color: #6c757d;
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.25);
        }

        .input-group-text {
            background-color: #f8f9fa;
            border-color: #ced4da;
        }

        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card-header {
            background-color: #f8f9fa;
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
        }

        .btn-primary {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-primary:hover {
            background-color: #5a6268;
            border-color: #545b62;
        }

        /* Highlighted section for conditional fields */
        #assigned_manager_div, #employee_designation_div, #employee_salary_div {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.25rem;
            margin-left: 0.5rem;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }

        #assigned_manager_div:hover, #employee_designation_div:hover, #employee_salary_div:hover {
            background-color: #e9ecef;
        }
    </style>

    <script>
        // Role management
        $(document).ready(function() {
            function toggleRoleSelect() {
                setTimeout(function() {
                    var userType = $('#user_type').val();
                    if (userType === 'admin') {
                        $('#role_id').val('').prop('disabled', true);
                        $('#assigned_manager_div').hide();
                        $('#assigned_manager_id').prop('required', false);
                    } else {
                        $('#role_id').prop('disabled', false);
                    }
                }, 100);
            }

            toggleRoleSelect();

            $(document).on('change', '#user_type', function() {
                toggleRoleSelect();
            });

            $(document).on('change', '#role_id', function() {
                var roleId = $(this).val();
                if (roleId) {
                    $.ajax({
                        url: "{{ route('roles.get_role_name') }}",
                        type: "POST",
                        data: {
                            role_id: roleId,
                            _token: "{{ csrf_token() }}"
                        },
                        success: function(response) {
                            if (response.role_name.toLowerCase() === 'dsa') {
                                $('#assigned_manager_div').show();
                                $('#assigned_manager_id').prop('required', true);
                            } else {
                                $('#assigned_manager_div').hide();
                                $('#assigned_manager_id').prop('required', false);
                            }

                            if (response.role_name.toLowerCase() === 'employee') {
                                $('#employee_designation_div').show();
                                $('#employee_salary_div').show();
                            } else {
                                $('#employee_designation_div').hide();
                                $('#employee_salary_div').hide();
                            }
                        }
                    });
                }
            });
        });

        // Password toggle
        function togglePasswordEdit() {
            const passwordField = document.getElementById('password_edit');
            const eyeIcon = document.getElementById('eyeIconEdit');

            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                eyeIcon.classList.replace('fa-eye', 'fa-eye-slash');
            } else {
                passwordField.type = 'password';
                eyeIcon.classList.replace('fa-eye-slash', 'fa-eye');
            }
        }

        // File management
        let finalFiles = []; // List of new selected files

        document.getElementById('documents').addEventListener('change', function(event) {
            let newFiles = Array.from(event.target.files);

            // Add selected files
            finalFiles = finalFiles.concat(newFiles);

            // Allow selecting same file again
            event.target.value = "";

            renderPreviews();
        });

        // Render thumbnails
        function renderPreviews() {
            let preview = document.getElementById('preview');
            preview.innerHTML = "";
            preview.className = "file-preview-container d-flex flex-wrap gap-3 mt-3";

            finalFiles.forEach((file, index) => {
                let reader = new FileReader();

                reader.onload = function(e) {
                    let div = document.createElement('div');
                    div.className = "file-preview-item position-relative";

                    let content = "";

                    // Image previews
                    if (file.type.includes("image")) {
                        content = `<img src="${e.target.result}" class="preview-image" alt="${file.name}">`;
                    } else {
                        let iconClass = "fa-file";
                        if (file.type.includes("pdf")) iconClass = "fa-file-pdf text-danger";
                        else if (file.type.includes("word")) iconClass = "fa-file-word text-primary";
                        else if (file.type.includes("excel")) iconClass = "fa-file-excel text-success";
                        else if (file.type.includes("powerpoint")) iconClass =
                            "fa-file-powerpoint text-warning";

                        content = `
                            <div class="preview-file-icon">
                                <i class="fa ${iconClass} fa-3x"></i>
                            </div>
                        `;
                    }

                    div.innerHTML = `
                        <div class="preview-content">
                            ${content}
                            <div class="file-info">
                                <small class="file-name text-truncate d-block" title="${file.name}">${file.name}</small>
                                <small class="file-size text-muted">${formatFileSize(file.size)}</small>
                            </div>
                        </div>

                        <button type="button" class="delete-btn" onclick="removeFile(${index})">
                            <i class="fas fa-times"></i>
                        </button>
                    `;

                    preview.appendChild(div);
                };

                reader.readAsDataURL(file);
            });
        }

        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            let i = Math.floor(Math.log(bytes) / Math.log(k));
            return (bytes / Math.pow(k, i)).toFixed(2) + ' ' + sizes[i];
        }

        function removeFile(index) {
            finalFiles.splice(index, 1);
            renderPreviews();
        }

        // Before submit: attach finalFiles to input
        document.querySelector("form").addEventListener("submit", function(e) {
            let input = document.getElementById("documents");
            let dt = new DataTransfer();

            finalFiles.forEach(file => dt.items.add(file));
            input.files = dt.files;
        });

        // Remove old file
        function removeOldFile(filename) {
            // Hide the item
            const items = document.querySelectorAll(`input[value="${filename}"]`);

            items.forEach(input => {
                // parent file div hide
                input.closest('.file-preview-item').remove();
            });

            // Add hidden input to tell backend to delete this file
            let removedInput = document.createElement('input');
            removedInput.type = 'hidden';
            removedInput.name = 'remove_old_files[]';
            removedInput.value = filename;

            document.querySelector("form").appendChild(removedInput);
        }
    </script>
@endpush
