@extends('layouts.app')

@section('content')
<style>
    /* Modern card styles */
    .modern-card {
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
        border: none;
        overflow: hidden;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
    }

    .modern-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 12px 20px;
    }

    .modern-card .card-body {
        padding: 20px;
    }

    /* Modern form elements */
    .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        padding: 10px 15px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Modern buttons */
    .btn-modern {
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .btn-primary-modern {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
    }

    .btn-info-modern {
        background: linear-gradient(135deg, #36d1dc 0%, #5b86e5 100%);
        border: none;
        color: white;
    }

    .btn-success-modern {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        border: none;
        color: white;
    }

    /* Custom email button */
    .btn-email {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        border-radius: 8px;
        padding: 10px 20px;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .btn-email:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* Dropify box styling */
    .dropify-wrapper {
        min-height: 80px !important;
        height: 100px !important;
        max-width: 320px !important;
        border-radius: 8px;
        border: 2px dashed #e0e0e0;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .dropify-wrapper:hover {
        border-color: #667eea;
        background-color: rgba(102, 126, 234, 0.05);
    }

    .dropify-wrapper .dropify-message {
        transform: scale(0.9);
        margin: 0 !important;
        padding: 0 !important;
    }

    .dropify-wrapper .dropify-message span.file-icon {
        font-size: 24px !important;
        color: #667eea;
    }

    .dropify-wrapper .dropify-message p {
        font-size: 13px !important;
        line-height: 1.2;
        margin: 0 !important;
        color: #6c757d;
    }

    /* Form labels */
    .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }

    /* Text muted */
    .text-muted {
        color: #6c757d !important;
        font-size: 0.85rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .modern-card .card-body {
            padding: 15px;
        }

        .form-control {
            padding: 8px 12px;
        }
    }
</style>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-8 offset-lg-2">
            <div class="modern-card">
                <div class="card-header">
                    <h4 class="mb-0">{{ _lang('Send Email Notification') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('email_notifications.send') }}" method="POST" enctype="multipart/form-data" class="validate" autocomplete="off">
                        @csrf
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ _lang('To') }}</label>
                                    <select name="users[]" class="form-control select2" multiple data-placeholder="{{ _lang('Select Users') }}" required>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted">{{ _lang('Use the search to select multiple users.') }}</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ _lang('Subject') }}</label>
                                    <input type="text" name="title" class="form-control" placeholder="{{ _lang('Enter email subject') }}" required>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ _lang('Message') }}</label>
                                    <textarea name="message" class="form-control" rows="5" placeholder="{{ _lang('Write your message...') }}" required></textarea>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group mb-3">
                                    <label class="form-label">{{ _lang('Attachment') }}</label>
                                    <input type="file" name="attachment" class="form-control dropify">
                                    <small class="text-muted d-block">{{ _lang('Attach only one file') }}</small>
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-email">
                                        <i class="ti-email"></i>&nbsp;{{ _lang('Send Email') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
