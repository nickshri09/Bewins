@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ _lang('Create New Knowledge') }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.knowledge-base.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Title') }} *</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Category') }} *</label>
                                    <select name="category" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <option value="home" {{ old('category') == 'home' ? 'selected' : '' }}>Home</option>
                                        <option value="service" {{ old('category') == 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="about" {{ old('category') == 'about' ? 'selected' : '' }}>About</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>{{ _lang('Description') }} *</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                        </div>

                        <!-- Multiple Images -->
                        <div class="form-group">
                            <label>{{ _lang('Upload Images') }} *</label>
                            <input type="file" name="images[]" class="form-control" multiple required>
                            <small class="text-muted">You can select multiple images</small>
                        </div>

                        <!-- Submit -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ _lang('Create Knowledge') }}</button>
                            <a href="{{ route('admin.knowledge-base.index') }}" class="btn btn-secondary">{{ _lang('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
