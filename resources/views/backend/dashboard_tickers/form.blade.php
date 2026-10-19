@extends('layouts.app')

@section('content')
    @php($editing = $ticker->exists)
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">{{ $editing ? _lang('Edit Scrolling Text') : _lang('Add Scrolling Text') }}</h4>
            <a href="{{ route('admin.tickers.index') }}" class="btn btn-light">{{ _lang('Manage Scrolling Text') }}</a>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif
            <form method="POST" action="{{ $editing ? route('admin.tickers.update', $ticker) : route('admin.tickers.store') }}">
                @csrf
                @if ($editing) @method('PUT') @endif
                <div class="form-group">
                    <label for="message">{{ _lang('Scrolling Message') }} *</label>
                    <textarea id="message" name="message" class="form-control" rows="3" maxlength="500" required>{{ old('message', $ticker->message) }}</textarea>
                    <small class="form-text text-muted">{{ _lang('Maximum 500 characters. Active messages will be combined with separators in the ticker.') }}</small>
                </div>
                <div class="custom-control custom-checkbox mb-3">
                    <input id="is_active" name="is_active" type="checkbox" value="1" class="custom-control-input" {{ old('is_active', $ticker->is_active) ? 'checked' : '' }}>
                    <label for="is_active" class="custom-control-label">{{ _lang('Show this message in the dashboard ticker') }}</label>
                </div>
                <button type="submit" class="btn btn-primary">{{ $editing ? _lang('Update Message') : _lang('Save Message') }}</button>
                <a href="{{ route('admin.tickers.index') }}" class="btn btn-light">{{ _lang('Cancel') }}</a>
            </form>
        </div>
    </div>
@endsection
