@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="{{ $alert_col ?? 'col-lg-12' }}">
            <div class="card">
                <div class="card-header text-center">
                    <span class="panel-title">{{ _lang('Edit Investment') }}</span>
                </div>
                <div class="card-body">
                    <form method="post" class="validate" autocomplete="off"
                        action="{{ route('investments.update', $investment->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">

                            {{-- Customer --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Customer') }}</label>
                                    <select class="form-control select2" name="customer_id" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}"
                                                {{ $investment->customer_id == $customer->id ? 'selected' : '' }}>
                                                {{ $customer->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Currency --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Currency') }}</label>
                                    <select class="form-control auto-select" name="currency_id"
                                        data-selected="{{ $investment->currency_id }}" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        {{ create_option('currency', 'id', 'name', $investment->currency_id, ['status=' => 1]) }}
                                    </select>
                                </div>
                            </div>

                            {{-- Investment Amount --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Investment Amount') }}</label>
                                    <input type="number" class="form-control float-field" name="investment_amount"
                                        value="{{ $investment->investment_amount }}" {{ $payments_count > 0 ? 'readonly' : '' }} required>
                                    @if($payments_count > 0)
                                        <small class="text-danger">{{ _lang('Investment amount cannot be changed because some payments have already been made.') }}</small>
                                    @endif
                                </div>
                            </div>

                            {{-- Investment Type --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Investment Type') }}</label>
                                    <select class="form-control auto-select" name="investment_type"
                                        data-selected="{{ $investment->investment_type }}" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="mutual_fund"
                                            {{ $investment->investment_type == 'mutual_fund' ? 'selected' : '' }}>
                                            {{ _lang('Mutual Fund') }}</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Contribution Type --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Contribution Type') }}</label>
                                    <select class="form-control" name="contribution_type" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="onetime"
                                            {{ $investment->contribution_type == 'onetime' ? 'selected' : '' }}>
                                            {{ _lang('One Time (Lump Sum)') }}
                                        </option>
                                        <option value="monthly"
                                            {{ $investment->contribution_type == 'monthly' ? 'selected' : '' }}>
                                            {{ _lang('Monthly (SIP)') }}
                                        </option>
                                    </select>
                                </div>
                            </div>

                            {{-- Risk Level --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Risk Level') }}</label>
                                    <select class="form-control auto-select" name="risk_level"
                                        data-selected="{{ $investment->risk_level }}" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="low" {{ $investment->risk_level == 'low' ? 'selected' : '' }}>
                                            {{ _lang('Low') }}</option>
                                        <option value="medium" {{ $investment->risk_level == 'medium' ? 'selected' : '' }}>
                                            {{ _lang('Medium') }}</option>
                                        <option value="high" {{ $investment->risk_level == 'high' ? 'selected' : '' }}>
                                            {{ _lang('High') }}</option>
                                    </select>
                                </div>
                            </div>

                            {{-- Expected Return --}}
                            {{-- <div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Expected Return (%)') }}</label>
								<input type="text" class="form-control float-field" name="expected_return" value="{{ $investment->expected_return }}">
							</div>
						</div> --}}
                            {{-- <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Expected Return (%)') }}</label>
                                    <select class="form-control auto-select"
                                        data-selected="{{ $investment->expected_return }}" name="expected_return" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach ($expectedReturns as $expectedReturn)
                                            <option value="{{ $expectedReturn->expected_return }}">
                                                {{ $expectedReturn->expected_return }}%
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div> --}}


                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Interest Rate (%)') }}</label>

                                    <select class="form-control" name="percentage" required>
                                        {{-- Show current value as first option (NOT selected) --}}
                                        @if (!empty($investment->expected_return))
                                            <option value="{{ old('percentage', $investment->expected_return) }}">
                                                Current Rate : {{ $investment->expected_return }}%
                                            </option>
                                        @endif

                                        <option value="">{{ _lang('Select New Rate') }}
                                        </option>

                                        @foreach ($expectedReturns as $expectedReturn)
                                            <option value="{{ $expectedReturn->expected_return }}">
                                                {{ $expectedReturn->expected_return }}%
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- Start Date --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Start Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="start_date"
                                        value="{{ $investment->start_date }}" required>
                                </div>
                            </div>

                            {{-- Maturity Date --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Maturity Date / Target Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="maturity_date"
                                        value="{{ $investment->maturity_date }}">
                                </div>
                            </div>

                            {{-- Description --}}
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Description') }}</label>
                                    <textarea class="form-control" name="description">{{ $investment->description }}</textarea>
                                </div>
                            </div>

                            {{-- Remarks --}}
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Remarks') }}</label>
                                    <textarea class="form-control" name="remarks">{{ $investment->remarks }}</textarea>
                                </div>
                            </div>

                            {{-- Submit --}}
                            <div class="col-lg-12">
                                <div class="form-group text-center">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="ti-check-box"></i>&nbsp;{{ _lang('Update') }}
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

@section('js-script')
    <script>
        (function($) {
            "use strict";
            // Any future JS logic for edit page
        })(jQuery);
    </script>
@endsection
