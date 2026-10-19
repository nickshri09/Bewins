@extends('layouts.app')

@section('content')
    @php($editing = $banner->exists)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $editing ? _lang('Edit Live Banner') : _lang('Create Live Banner') }}</h4>
            <a href="{{ route('admin.banners.index') }}" class="btn btn-light">{{ _lang('Manage Banners') }}</a>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <form method="POST" action="{{ $editing ? route('admin.banners.update', $banner) : route('admin.banners.store') }}" enctype="multipart/form-data">
                @csrf
                @if ($editing) @method('PUT') @endif

                <div class="form-group">
                    <label for="title">{{ _lang('Title') }} *</label>
                    <input id="title" name="title" class="form-control" maxlength="150" required value="{{ old('title', $banner->title) }}">
                </div>
                <div class="form-group">
                    <label for="subtitle">{{ _lang('Subtitle') }}</label>
                    <input id="subtitle" name="subtitle" class="form-control" maxlength="200" value="{{ old('subtitle', $banner->subtitle) }}">
                </div>
                <div class="form-group">
                    <label for="description">{{ _lang('Banner Message') }}</label>
                    <textarea id="description" name="description" class="form-control" rows="4" maxlength="2000">{{ old('description', $banner->description) }}</textarea>
                </div>
                <div class="form-group">
                    <label for="image">{{ _lang('Banner Image') }}</label>
                    <input id="image" type="file" name="image" class="form-control-file" accept="image/jpeg,image/png,image/webp">
                    <small class="form-text text-muted">{{ _lang('Optional. JPG, PNG, or WebP; maximum 5 MB.') }}</small>
                    @if ($editing && $banner->image)
                        <div class="mt-2"><img src="{{ asset('uploads/live-banners/' . $banner->image) }}" alt="{{ $banner->title }}" style="max-width: 360px; max-height: 160px"></div>
                    @endif
                </div>
                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="button_text">{{ _lang('Button Text') }}</label>
                        <input id="button_text" name="button_text" class="form-control" maxlength="50" value="{{ old('button_text', $banner->button_text) }}">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="button_url">{{ _lang('Button Link') }}</label>
                        <input id="button_url" name="button_url" type="url" class="form-control" placeholder="https://example.com" value="{{ old('button_url', $banner->button_url) }}">
                    </div>
                </div>
                <div class="custom-control custom-checkbox mb-3">
                    <input id="is_active" name="is_active" type="checkbox" value="1" class="custom-control-input" {{ old('is_active', $banner->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="custom-control-label">{{ _lang('Make this the active dashboard banner') }}</label>
                    <small class="form-text text-muted">{{ _lang('All active banners will rotate in the dashboard carousel.') }}</small>
                </div>
                <button type="submit" class="btn btn-primary">{{ $editing ? _lang('Update Banner') : _lang('Save Banner') }}</button>
                <a href="{{ route('admin.banners.index') }}" class="btn btn-light">{{ _lang('Cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
