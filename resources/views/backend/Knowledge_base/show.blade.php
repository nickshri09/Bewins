@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ _lang('Knowledge Details') }}: {{ $knowledge->title }}</h4>
                </div>
                <div class="card-body">

                    <div class="row">
                        <!-- Title -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>{{ _lang('Title') }}:</strong></label>
                                <p class="form-control-plaintext">{{ $knowledge->title }}</p>
                            </div>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>{{ _lang('Category') }}:</strong></label>
                                <p class="form-control-plaintext">{{ ucfirst($knowledge->category) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group">
                        <label><strong>{{ _lang('Description') }}:</strong></label>
                        <div class="form-control-plaintext" style="white-space: pre-wrap;">
                            {{ $knowledge->description }}
                        </div>
                    </div>

                    <!-- Images -->
                    @if($knowledge->images && $knowledge->images->count() > 0)
                        <div class="form-group">
                            <label><strong>{{ _lang('Images') }}:</strong></label>
                            <div class="row mt-2">
                                @foreach($knowledge->images as $img)
                                    <div class="col-md-3 mb-3">
                                        <a href="{{ asset('storage/app/publlic' . $img->path) }}" target="_blank">
                                            <img src="{{ asset('storage/app/public/' . $img->path) }}" 
                                                 class="img-fluid img-thumbnail rounded"
                                                 alt="Knowledge Image">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <div class="form-group">
                            <label><strong>{{ _lang('Images') }}:</strong></label>
                            <p class="text-muted">{{ _lang('No images uploaded') }}</p>
                        </div>
                    @endif

                    <!-- Status -->
                    <div class="form-group">
                        <label><strong>{{ _lang('Status') }}:</strong></label>
                        <p class="form-control-plaintext">
                            @if($knowledge->is_active)
                                <span class="badge bg-success">{{ _lang('Active') }}</span>
                            @else
                                <span class="badge bg-danger">{{ _lang('Inactive') }}</span>
                            @endif
                        </p>
                    </div>

                    <!-- Actions -->
                    <div class="form-group mt-4">
                        <a href="{{ route('admin.knowledge-base.index') }}" class="btn btn-secondary">{{ _lang('Back to List') }}</a>
                        <a href="{{ route('admin.knowledge-base.edit', $knowledge) }}" class="btn btn-primary">{{ _lang('Edit') }}</a>
                      
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection