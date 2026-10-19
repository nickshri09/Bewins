@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ _lang('Edit Pricing Plan') }}: {{ $plan->name }}</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.pricing-plans.update', $plan) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Plan Name') }} *</label>
                                    <input type="text" name="name" class="form-control" value="{{ old('name', $plan->name) }}" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Price') }} *</label>
                                    <input type="number" name="price" class="form-control" value="{{ old('price', $plan->price) }}" step="0.01" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Validity') }} *</label>
                                    <input type="text" name="validity" class="form-control" value="{{ old('validity', $plan->validity) }}" placeholder="e.g., 1 Year, Life Time" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Icon Class') }} *</label>
                                    <input type="text" name="icon_class" class="form-control" value="{{ old('icon_class', $plan->icon_class) }}" placeholder="e.g., fas fa-user" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Icon Color') }} *</label>
                                    <select name="icon_color" class="form-control" required>
                                        <option value="">Select Color</option>
                                        <option value="silver" {{ old('icon_color', $plan->icon_color) == 'silver' ? 'selected' : '' }}>Silver</option>
                                        <option value="dsp-in" {{ old('icon_color', $plan->icon_color) == 'dsp-in' ? 'selected' : '' }}>DSP-IN</option>
                                        <option value="dsp-cb" {{ old('icon_color', $plan->icon_color) == 'dsp-cb' ? 'selected' : '' }}>DSP-CB</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>{{ _lang('Cashback Description') }} *</label>
                                    <textarea name="cashback_description" class="form-control" rows="2" required>{{ old('cashback_description', $plan->cashback_description) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>{{ _lang('Features') }} *</label>
                            <div id="features-container">
                                @foreach($plan->features as $index => $feature)
                                    <div class="feature-row row mb-2">
                                        <div class="col-md-6">
                                            <input type="text" name="features[{{ $index }}][feature_name]" class="form-control" placeholder="Feature Name" value="{{ $feature->feature_name }}" required>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="features[{{ $index }}][feature_status]" class="form-control" required>
                                                <option value="">Select Status</option>
                                                <option value="paid" {{ $feature->feature_status == 'paid' ? 'selected' : '' }}>Paid</option>
                                                <option value="yes" {{ $feature->feature_status == 'yes' ? 'selected' : '' }}>Yes</option>
                                                <option value="no" {{ $feature->feature_status == 'no' ? 'selected' : '' }}>No</option>
                                                <option value="email" {{ $feature->feature_status == 'email' ? 'selected' : '' }}>On Email</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <button type="button" class="btn btn-danger btn-sm remove-feature">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button type="button" class="btn btn-success btn-sm" id="add-feature">
                                <i class="fas fa-plus"></i> Add Feature
                            </button>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">{{ _lang('Update Plan') }}</button>
                            <a href="{{ route('admin.pricing-plans.index') }}" class="btn btn-secondary">{{ _lang('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let featureIndex = {{ $plan->features->count() }};

document.getElementById('add-feature').addEventListener('click', function() {
    const container = document.getElementById('features-container');
    const newRow = document.createElement('div');
    newRow.className = 'feature-row row mb-2';
    newRow.innerHTML = `
        <div class="col-md-6">
            <input type="text" name="features[${featureIndex}][feature_name]" class="form-control" placeholder="Feature Name" required>
        </div>
        <div class="col-md-4">
            <select name="features[${featureIndex}][feature_status]" class="form-control" required>
                <option value="">Select Status</option>
                <option value="paid">Paid</option>
                <option value="yes">Yes</option>
                <option value="no">No</option>
                <option value="email">On Email</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-danger btn-sm remove-feature">Remove</button>
        </div>
    `;
    container.appendChild(newRow);
    featureIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('remove-feature')) {
        e.target.closest('.feature-row').remove();
    }
});
</script>
@endsection
