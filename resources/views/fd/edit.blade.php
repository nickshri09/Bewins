@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="{{ $alert_col ?? 'col-lg-8 offset-lg-2' }}">
            <div class="card">
                <div class="card-header text-center">
                    <span class="panel-title">{{ _lang('Edit FD') }}</span>
                </div>
                <div class="card-body">
                    <form method="post" class="validate" autocomplete="off" action="{{ route('fd.update', $fd->id) }}"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        @method('PUT')
                        <div class="row">

                            {{-- <div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Customer') }}</label>
								<select class="form-control auto-select select2" name="user_id" data-selected="{{ old('user_id', $fd->user_id) }}" required>
									<option value="">{{ _lang('Select One') }}</option>
									@foreach ($users as $user)
										<option value="{{ $user->id }}">{{ $user->name }}</option>
									@endforeach
								</select>
							</div>
						</div> --}}

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Customer') }}</label>
                                    <select class="form-control auto-select select2" name="customer_id"
                                        data-selected="{{ old('customer_id', $fd->customer_id) }}" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Currency') }}</label>
                                    <select class="form-control auto-select select2" name="currency_id"
                                        data-selected="{{ old('currency_id', $fd->currency_id) }}" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach ($currencies as $currency)
                                            <option value="{{ $currency->id }}">{{ $currency->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('FD Amount') }}</label>
                                    <input type="text" class="form-control float-field" name="fd_amount"
                                        value="{{ old('fd_amount', $fd->fd_amount) }}" required>
                                </div>
                            </div>

                            {{-- <div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('	Interest Rate') }}</label>
								<input type="text" class="form-control float-field" name="percentage" value="{{ old('percentage',$fd->percentage) }}" required>
							</div>
						</div> --}}

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Interest Rate (%)') }}</label>

                                    <select class="form-control" name="percentage" required>
                                        {{-- Show current value as first option (NOT selected) --}}
                                        @if (!empty($fd->percentage))
                                            <option value="{{ old('percentage', $fd->percentage) }}">
                                                <span class="text-success" style="color: #85f307">Current Rate</span> :{{ $fd->percentage }}%
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




                            {{-- <div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('Duration (in Months)') }}</label>
								<input type="number" class="form-control" name="durations" value="{{ old('durations', $fd->durations) }}" required min="1">
							</div>
						</div> --}}

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Duration') }}</label>
                                    <select class="form-control auto-select" name="durations"
                                        data-selected="{{ old('durations', $fd->durations) }}" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="12">{{ _lang('1 Year') }}</option>
                                        <option value="24">{{ _lang('2 Years') }}</option>
                                        <option value="36">{{ _lang('3 Years') }}</option>
                                    </select>
                                </div>
                            </div>

                            {{-- <div class="col-lg-6">
							<div class="form-group">
								<label class="control-label">{{ _lang('FD Type') }}</label>
								<select class="form-control auto-select" name="fd_types" data-selected="{{ old('fd_types', $fd->fd_types) }}" required>
									<option value="">{{ _lang('Select One') }}</option>
									<option value="simple">{{ _lang('Simple Interest') }}</option>
									<option value="compound">{{ _lang('Compound Interest') }}</option>
								</select>
							</div>
						</div> --}}
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('FD Type') }}</label>
                                    <select class="form-control auto-select" name="fd_types"
                                        data-selected="{{ old('fd_types', $fd->fd_types) }}" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        <option value="monthly">{{ _lang('Monthly') }}</option>
                                        <option value="onetime">{{ _lang('One Time') }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Start Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="start_date"
                                        value="{{ old('start_date', $fd->start_date) }}" required>
                                </div>
                            </div>

                            {{-- Optional Fields --}}
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('End Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="end_date"
                                        value="{{ old('end_date', $fd->end_date) }}">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Description') }}</label>
                                    <textarea class="form-control" name="description">{{ old('description', $fd->description) }}</textarea>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Remarks') }}</label>
                                    <textarea class="form-control" name="remarks">{{ old('remarks', $fd->remarks) }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="ti-check-box"></i>&nbsp;{{ _lang('Update') }}</button>
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

            // agar future me FD type change pe interest calculation ya preview karna ho
        })(jQuery);
    </script>
@endsection
