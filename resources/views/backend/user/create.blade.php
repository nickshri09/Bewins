@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="header-title">{{ _lang('Create User') }}</h4>
                </div>
                <div class="card-body">
                    <form method="post" class="validate" autocomplete="off" action="{{ route('users.store') }}"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <div class="row">
                            <div class="col-md-8 col-sm-12">

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Profile ID') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control"
                                            value="{{ _lang('Generated automatically after saving') }}" readonly>
                                        <small class="text-muted">{{ _lang('The ID prefix is selected from the account role.') }}</small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Percent (%)') }}</label>
                                    <div class="col-xl-9">
                                        <input type="number" step="0.01" class="form-control" name="percent"
                                            placeholder="Enter Percent">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('First Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="name"
                                            value="{{ old('name') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Middle Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="middle_name"
                                            value="{{ old('middle_name') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Last Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="last_name"
                                            value="{{ old('last_name') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row position-relative">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Password') }}</label>
                                    <div class="col-xl-9">
                                        <input type="password" class="form-control" name="password" id="password" required>
                                        <span class="toggle-password" onclick="togglePassword()"
                                            style="position:absolute; top:50%; right:28px; transform:translateY(-50%); cursor:pointer;">
                                            <i id="eyeIcon" class="fa fa-eye"></i>
                                        </span>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Email') }}</label>
                                    <div class="col-xl-9">
                                        <input type="email" class="form-control" name="email"
                                            value="{{ old('email') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Mobile Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="mobile_number"
                                            value="{{ old('mobile_number') }}" required>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">WhatsApp Number</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="whatsapp_number"
                                            value="{{ old('whatsapp_number') }}" pattern="\d{10}"
                                            title="Enter a valid 10-digit number">
                                    </div>
                                </div>

                                <!-- Aadhaar Images -->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">Aadhaar Front Image</label>
                                    <div class="col-xl-9">
                                        <input type="file" class="form-control dropify" name="aadhaar_front"
                                            data-height="120" data-max-file-size="2M"
                                            data-allowed-file-extensions="jpg jpeg png">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">Aadhaar Back Image</label>
                                    <div class="col-xl-9">
                                        <input type="file" class="form-control dropify" name="aadhaar_back"
                                            data-height="120" data-max-file-size="2M"
                                            data-allowed-file-extensions="jpg jpeg png">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Aadhaar Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="aadhar_number"
                                            value="{{ old('aadhar_number') }}" required>
                                    </div>
                                </div>

                                <!-- PAN -->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">PAN Photo</label>
                                    <div class="col-xl-9">
                                        <input type="file" class="form-control dropify" name="pan_photo"
                                            data-allowed-file-extensions="jpg jpeg png">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('PAN Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="pan_number"
                                            value="{{ old('pan_number') }}" required>
                                    </div>
                                </div>

                                <!-- GST -->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('GST Type') }}</label>
                                    <div class="col-xl-9">
                                        <select name="gst_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Regular" {{ old('gst_type') == 'Regular' ? 'selected' : '' }}>
                                                Regular</option>
                                            <option value="Composition"
                                                {{ old('gst_type') == 'Composition' ? 'selected' : '' }}>Composition
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('GST Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="gst_number"
                                            value="{{ old('gst_number') }}">
                                    </div>
                                </div>

                                <!-- Addresses -->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Residence Address') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="residence_address"
                                            value="{{ old('residence_address') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Office Address') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="office_address"
                                            value="{{ old('office_address') }}">
                                    </div>
                                </div>

                                <!-- Bank Details -->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Account Type') }}</label>
                                    <div class="col-xl-9">
                                        <select name="account_type" class="form-control">
                                            <option value="">Select</option>
                                            <option value="Savings"
                                                {{ old('account_type') == 'Savings' ? 'selected' : '' }}>Savings</option>
                                            <option value="Current"
                                                {{ old('account_type') == 'Current' ? 'selected' : '' }}>Current</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Account Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="account_name"
                                            value="{{ old('account_name') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Bank Name') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="bank_name"
                                            value="{{ old('bank_name') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('IFSC Code') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="ifsc_code"
                                            value="{{ old('ifsc_code') }}">
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Account Number') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="account_number"
                                            value="{{ old('account_number') }}">
                                    </div>
                                </div>

                                <!-- User Type & Role -->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('User Type') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control auto-select" data-selected="{{ old('user_type') }}"
                                            name="user_type" id="user_type" required>
                                            <option value="">{{ _lang('Select One') }}</option>
                                            <option value="admin">{{ _lang('Admin') }}</option>
                                            <option value="user">{{ _lang('User') }}</option>
                                        </select>
                                        <small class="text-primary"><i class="ti-info-alt"></i>
                                            <i>{{ _lang('Admin will get full access and user will get role based access only.') }}</i></small>
                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('User Role') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control select2-ajax" data-href="{{ route('roles.create') }}"
                                            data-title="{{ _lang('Add New Role') }}" data-value="id" data-display="name"
                                            data-table="roles" name="role_id" id="role_id">
                                        </select>
                                    </div>
                                </div>
                                
                                
                                <div class="form-group row" id="employee_designation_div" style="display:none;">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Designation') }}</label>
                                    <div class="col-xl-9">
                                        <input type="text" class="form-control" name="designation" value="{{ old('designation') }}">
                                    </div>
                                </div>

                                <div class="form-group row" id="employee_salary_div" style="display:none;">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Monthly Salary') }}</label>
                                    <div class="col-xl-9">
                                        <input type="number" step="0.01" class="form-control" name="salary" value="{{ old('salary') }}">
                                    </div>
                                </div>


                                <div class="form-group row" id="assigned_manager_div" style="display:none;">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Assigned To') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control select2" name="assigned_manager_id"
                                            id="assigned_manager_id">
                                            <option value="">{{ _lang('Select Manager') }}</option>
                                            @foreach ($managers as $manager)
                                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>


                                @php
                                    $defaultBranchId = 50; // DUMRAON MAIN BRANCH
                                @endphp
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Branch') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control select2-ajax"
                                            data-href="{{ route('branches.create') }}"
                                            data-title="{{ _lang('Add New Branch') }}" data-value="id"
                                            data-display="name" data-table="branches" name="branch_id"
                                            id="user_branch_id" disabled>
                                            @if (old('user_type') == 'admin')
                                                disabled
                                            @endif>
                                            <option value="{{ $defaultBranchId }}" selected>
                                                {{ _lang('DUMRAON MAIN BRANCH') }}</option>
                                        </select>
                                        <small class="text-primary"><i class="ti-info-alt"></i>
                                            <i>{{ _lang('If not assign any branch then user will get default branch access.') }}</i></small>
                                    </div>
                                </div>

                                <!-- Status -->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Status') }}</label>
                                    <div class="col-xl-9">
                                        <select class="form-control auto-select" data-selected="{{ old('status', 1) }}"
                                            name="status" required>
                                            <option value="">{{ _lang('Select One') }}</option>
                                            <option value="1">{{ _lang('Active') }}</option>
                                            <option value="0">{{ _lang('In Active') }}</option>
                                        </select>
                                    </div>
                                </div>

                                <!-- Profile Picture -->
                                <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('Profile Picture') }}</label>
                                    <div class="col-xl-9">
                                        <input type="file" class="form-control dropify" name="profile_picture">
                                    </div>
                                </div>

                                <!-- KYC Documents -->
                                {{-- <div class="form-group row">
                                    <label class="col-xl-3 col-form-label">{{ _lang('KYC and other Documents') }}</label>
                                    <div class="col-xl-9">
                                        <input type="file" class="form-control dropify" id="documents" name="documents[]"
                                        data-height="120"  multiple>
                                        <small class="form-text text-muted mt-1">
                                            {{ _lang('Upload PAN card, Aadhaar card, and other related documents.') }}
                                        </small>
                                    </div>
                                </div> --}}

                                {{-- <div class="form-group row mb-3">
                                    <label class="col-xl-3 col-form-label">
                                        KYC and other Documents
                                    </label>

                                    <div class="col-xl-9">
                                        <input type="file"
                                               name="documents[]"
                                               id="documents"
                                               class="form-control"
                                               multiple>

                                        <small class="form-text text-muted mt-2">
                                            Upload PAN card, Aadhaar card, and other related documents.
                                        </small>

                                        <!-- Preview container -->
                                        <div id="preview" class="mt-3 d-flex flex-wrap gap-3"></div>
                                    </div>
                                </div> --}}


                                <div class="form-group row mb-3">
                                    <label class="col-xl-3 col-form-label">
                                        KYC and Other Documents
                                    </label>

                                    <div class="col-xl-9">
                                        {{-- <input type="file" id="documents" class="form-control" multiple> --}}
                                        <input type="file" id="documents" class="form-control" name="documents[]" multiple>

                                        <small class="form-text text-muted mt-2">
                                            Upload PAN card, Aadhaar card, and other related documents.
                                        </small>

                                        <div id="preview" class="mt-3 d-flex flex-wrap gap-3"></div>
                                    </div>
                                </div>







                                <div class="form-group row">
                                    <div class="col-xl-9 offset-xl-3">
                                        <button type="submit"
                                            class="btn btn-primary">{{ _lang('Create User') }}</button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        ```

    </div>

<style>
 .file-preview-container {
    padding: 15px;
    background-color: #f8f9fa;
    border-radius: 10px;
    min-height: 200px;
    gap: 10px;
    /* background-color: #1ed4da; */
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
    align-content: center;
    align-items: center;
    align-items: center;
}

.file-preview-item {
    width: 180px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    background-color: #fff;
    border: 1px solid #e9ecef;
    /* gap: 50px; */
    /* background-color: #1e7cda; */
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
    background-color: #dc3545;
    border: 3px solid white;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    box-shadow: 0 2px 8px rgba(220, 53, 69, 0.4);
    transition: all 0.2s ease;
    z-index: 10;
}

.delete-btn:hover {
    background-color: #c82333;
    transform: scale(1.1);
    box-shadow: 0 3px 10px rgba(220, 53, 69, 0.6);
}

.delete-btn i {
    font-size: 14px;
    margin: 0;
}
</style>

    @push('scripts')

    <!-- FontAwesome -->
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script>
        function togglePassword() {
            const passwordField = document.getElementById("password");
            const eyeIcon = document.getElementById("eyeIcon");

            if (passwordField.type === "password") {
                passwordField.type = "text";
                eyeIcon.classList.replace("fa-eye", "fa-eye-slash");
            } else {
                passwordField.type = "password";
                eyeIcon.classList.replace("fa-eye-slash", "fa-eye");
            }
        }

        $(document).ready(function() {

            function toggleRoleSelect() {
                setTimeout(() => {
                    const isAdmin = $("#user_type").val() === "admin";
                    if (isAdmin) {
                        $("#role_id").val("").prop("disabled", true);
                        $("#assigned_manager_div").hide();
                        $("#assigned_manager_id").prop("required", false);
                    } else {
                        $("#role_id").prop("disabled", false);
                    }
                }, 100);
            }

            toggleRoleSelect();

            $(document).on("change", "#user_type", toggleRoleSelect);

            $(document).on("change", "#role_id", function() {
                let roleId = $(this).val();
                if (!roleId) return;

                $.post("{{ route('roles.get_role_name') }}", {
                    role_id: roleId,
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    if (response.role_name.toLowerCase() === "dsa") {
                        $("#assigned_manager_div").show();
                        $("#assigned_manager_id").prop("required", true);
                    } else {
                        $("#assigned_manager_div").hide();
                        $("#assigned_manager_id").prop("required", false);
                    }
                    
                     if (response.role_name.toLowerCase() === "employee") {
                        $("#employee_designation_div").show();
                        $("#employee_salary_div").show();
                    } else {
                        $("#employee_designation_div").hide();
                        $("#employee_salary_div").hide();
                    }
                });
            });
        });
    </script>


<script>
    let finalFiles = []; // Final file list

    document.getElementById('documents').addEventListener('change', function(event) {
        let newFiles = Array.from(event.target.files);

        // Add new files to finalFiles list
        finalFiles = finalFiles.concat(newFiles);

        // Reset file input so same file can be selected again
        event.target.value = "";

        renderPreviews();
    });

    // function renderPreviews() {
    //     let preview = document.getElementById('preview');
    //     preview.innerHTML = "";

    //     finalFiles.forEach((file, index) => {
    //         let reader = new FileReader();

    //         reader.onload = function(e) {
    //             let div = document.createElement('div');
    //             div.style.width = "120px";
    //             div.style.position = "relative";
    //             div.style.textAlign = "center";

    //             let content = "";

    //             if (file.type.includes("image")) {
    //                 content = `<img src="${e.target.result}" class="border rounded" width="120">`;
    //             } else {
    //                 content = `
    //                     <div class="border p-3 rounded bg-light">
    //                         <i class="fa fa-file-pdf text-danger fa-2x"></i>
    //                     </div>
    //                 `;
    //             }

    //             div.innerHTML = `
    //                 ${content}
    //                 <small class="d-block mt-1 text-truncate">${file.name}</small>

    //                 <button type="button"
    //                         class="btn btn-danger btn-sm"
    //                         style="position:absolute; top:-8px; right:-8px; border-radius:50%"
    //                         onclick="removeFile(${index})">
    //                     ×
    //                 </button>
    //             `;

    //             preview.appendChild(div);
    //         };

    //         reader.readAsDataURL(file);
    //     });
    // }

    function renderPreviews() {
    let preview = document.getElementById('preview');
    preview.innerHTML = "";

    // Add a container class for styling
    preview.className = "file-preview-container d-flex flex-wrap gap-3 mt-3";

    finalFiles.forEach((file, index) => {
        let reader = new FileReader();

        reader.onload = function(e) {
            let div = document.createElement('div');
            div.className = "file-preview-item position-relative";

            let content = "";

            if (file.type.includes("image")) {
                content = `<img src="${e.target.result}" class="preview-image" alt="${file.name}">`;
            } else {
                // Use appropriate icons based on file type
                let iconClass = "fa-file";
                if (file.type.includes("pdf")) iconClass = "fa-file-pdf text-danger";
                else if (file.type.includes("word")) iconClass = "fa-file-word text-primary";
                else if (file.type.includes("excel")) iconClass = "fa-file-excel text-success";
                else if (file.type.includes("powerpoint")) iconClass = "fa-file-powerpoint text-warning";

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

                <button type="button"
                        class="delete-btn"
                        onclick="removeFile(${index})"
                        aria-label="Remove file">
                    <i class="fas fa-times"></i>
                </button>
            `;

            preview.appendChild(div);
        };

        reader.readAsDataURL(file);
    });
}

// Helper function to format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

    function removeFile(index) {
        finalFiles.splice(index, 1); // Remove file
        renderPreviews();           // Re-render preview
    }



    document.querySelector("form").addEventListener("submit", function(e) {
    let fileInput = document.getElementById("documents");

    // Create DataTransfer to store final files
    let dataTransfer = new DataTransfer();

    finalFiles.forEach(file => {
        dataTransfer.items.add(file);
    });

    // Replace input files with finalFiles
    fileInput.files = dataTransfer.files;
});

    </script>



    @endpush


@endsection
