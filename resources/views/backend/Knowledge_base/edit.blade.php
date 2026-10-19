@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ _lang('Edit Knowledge') }}: {{ $knowledge->title }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.knowledge-base.update', $knowledge) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Title -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Title') }} *</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $knowledge->title) }}" required>
                                </div>
                            </div>

                            <!-- Category -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Category') }} *</label>
                                    <select name="category" class="form-control" required>
                                        <option value="">Select Category</option>
                                        <option value="home" {{ old('category', $knowledge->category) == 'home' ? 'selected' : '' }}>Home</option>
                                        <option value="service" {{ old('category', $knowledge->category) == 'service' ? 'selected' : '' }}>Service</option>
                                        <option value="about" {{ old('category', $knowledge->category) == 'about' ? 'selected' : '' }}>About</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="form-group">
                            <label>{{ _lang('Description') }} *</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description', $knowledge->description) }}</textarea>
                        </div>

                        <!-- Multiple Images -->
                        <div class="form-group">
                            <label>{{ _lang('Upload Images') }}</label>
                            <input type="file" name="images[]" class="form-control" multiple>
                            <small class="text-muted">You can select multiple images. Leave empty if not changing.</small>
                        </div>

                        <!-- Existing Images Preview -->
                        @if($knowledge->images && count($knowledge->images) > 0)
                            <div class="form-group">
                                <label>{{ _lang('Current Images') }}</label>
                                <div class="row">
                                    @foreach($knowledge->images as $img)
                                        <div class="col-md-3 mb-2">
                                            <img src="{{ asset('storage/app/public/'.$img->path) }}" class="img-fluid img-thumbnail">
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Submit -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ _lang('Update Knowledge') }}</button>
                            <a href="{{ route('admin.knowledge-base.index') }}" class="btn btn-secondary">{{ _lang('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
