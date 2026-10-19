@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><span class="panel-title">{{ _lang('Commission Settings') }}</span></div>
            <div class="card-body">
                <div class="alert alert-info">{{ _lang('Rates apply to new investments when an admin approves them. Existing commission records are not recalculated.') }}</div>
                <form method="POST" action="{{ route('commissions.settings.update') }}">
                    @csrf
                    @foreach(['direct_agent' => 'Direct Agent (%)', 'level_1' => 'Referral Level 1 (%)', 'level_2' => 'Referral Level 2 (%)', 'level_3' => 'Referral Level 3 (%)'] as $key => $label)
                        <div class="form-group">
                            <label>{{ _lang($label) }}</label>
                            <input class="form-control" type="number" name="{{ $key }}" min="0" max="100" step="0.0001" value="{{ old($key, $rates[$key]) }}" required>
                            @error($key)<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    @endforeach
                    <button class="btn btn-primary" type="submit">{{ _lang('Save Rates') }}</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
