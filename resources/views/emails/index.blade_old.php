@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8 offset-lg-2">
        <div class="card">
            <div class="card-header text-center">
                <span class="panel-title">{{ _lang('Send Email Notification') }}</span>
            </div>
            <div class="card-body">
                <form action="{{ route('email_notifications.send') }}" method="POST" enctype="multipart/form-data" class="validate" autocomplete="off">
                    @csrf
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('To') }}</label>
                                <select name="users[]" class="form-control select2" multiple data-placeholder="{{ _lang('Select Users') }}" required>
                                    @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">{{ _lang('Use the search to select multiple users.') }}</small>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-group">
                                {{-- <label class="control-label">{{ _lang('Title') }}</label> --}}
                                <label class="control-label">{{ _lang('Subject') }}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{ _lang('subject') }}" required>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Message') }}</label>
                                <textarea name="message" class="form-control" rows="5" placeholder="{{ _lang('Write your message...') }}" required></textarea>
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="form-group">
                                <label class="control-label">{{ _lang('Attachment') }}</label>
                                <input type="file" name="attachment" class="form-control dropify">
                            </div>
                             <small class="text-muted d-block">{{ _lang('Attach only one file') }}</small>
                        </div>
                        

                    <!-- Custom CSS -->
                    <style>
                      
                        /* Dropify box ki height aur width override */
                    .dropify-wrapper {
                        min-height: 40px !important;   /* kam height */
                        height: 80px !important;       /* fix height */
                        max-width: 320px !important;   /* width */
                        padding: 5px !important;
                    }
                    
                    /* Inside message adjust */
                    .dropify-wrapper .dropify-message {
                        transform: scale(0.8);   /* text/image chhota ho jaye */
                        margin: 0 !important;
                        padding: 0 !important;
                    }
                    
                    /* Icon aur text chhota karna */
                    .dropify-wrapper .dropify-message span.file-icon {
                        font-size: 20px !important;  /* default 50px hota hai */
                    }
                    .dropify-wrapper .dropify-message p {
                        font-size: 12px !important;
                        line-height: 1.2;
                        margin: 0 !important;
                    }
                    
                    </style>

                        <div class="col-12">
                            <div class="form-group">
                                {{-- <button type="submit" class="bg-[#1F305E] text-white"><i class="ti-email"></i>&nbsp;{{ _lang('Send Now') }}</button> --}}
                                <button type="submit" class="btn text-white" style="background-color: #1E2E57;">
                                    <i class="ti-email"></i>&nbsp;{{ _lang('Send ') }}
                                </button>

                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
