@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="{{ $alert_col }}">
            <div class="card">
                <div class="card-header text-center">
                    <span class="panel-title">{{ _lang('Add New Loan') }}</span>
                </div>
                <div class="card-body">
                    @if (($invalidLoanProducts ?? collect())->isNotEmpty())
                        <div class="alert alert-warning">
                            {{ _lang('These active loan products have invalid repayment terms and cannot be used:') }}
                            @foreach ($invalidLoanProducts as $invalidProduct)
                                <a href="{{ route('loan_products.edit', $invalidProduct->id) }}">{{ $invalidProduct->name }} — {{ _lang('Edit term') }}</a>{{ !$loop->last ? ', ' : '' }}
                            @endforeach
                            {{ _lang('Set the approved term between 1 and 600 periods.') }}
                        </div>
                    @endif
                    <form method="post" class="validate" autocomplete="off" action="{{ route('loans.store') }}"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="row">
                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Loan ID') }}</label>
                                    <input type="text" class="form-control" value="{{ _lang('Generated automatically when saved') }}" readonly>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Loan Product') }}</label>
                                    <select class="form-control auto-select select2"
                                        data-selected="{{ old('loan_product_id') }}" name="loan_product_id"
                                        id="loan_product_id" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach (\App\Models\LoanProduct::active()->get() as $loanProduct)
                                            <option value="{{ $loanProduct->id }}"
                                                data-penalties="{{ $loanProduct->late_payment_penalties }}"
                                                {{ $loanProduct->hasValidTerm() ? '' : 'disabled' }}>
                                                {{ $loanProduct->name }}
                                                {{ $loanProduct->hasValidTerm() ? '' : ' — ' . _lang('Invalid repayment term; edit this product') }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Borrower') }}</label>
                                    <select class="form-control auto-select select2"
                                        data-selected="{{ old('borrower_id') }}" name="borrower_id" id="borrower_id"
                                        required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach ($customers as $member)
                                            <option value="{{ $member->id }}">
                                                {{ $member->first_name . ' ' . $member->last_name . ' (' . $member->member_no . ')' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Currency') }}</label>
                                    <select class="form-control auto-select" data-selected="{{ old('currency_id') }}"
                                        name="currency_id" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        {{ create_option('currency', 'id', 'name', '', ['status=' => 1]) }}
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('First Payment Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="first_payment_date"
                                        value="{{ old('first_payment_date') }}" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Release Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="release_date"
                                        value="{{ old('release_date') }}" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Applied Amount') }}</label>
                                    <input type="text" class="form-control float-field" name="applied_amount"
                                        value="{{ old('applied_amount') }}" required>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Interest Rate (%)') }}</label>
                                    <select class="form-control auto-select" name="interest_rate"
                                        data-selected="{{ old('interest_rate') }}" required>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach ($expectedReturns as $expectedReturn)
                                            <option value="{{ $expectedReturn->expected_return }}">
                                                {{ $expectedReturn->expected_return }}%
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Late Payment Penalties') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control float-field" name="late_payment_penalties"
                                            value="{{ old('late_payment_penalties') }}" id="late_payment_penalties"
                                            required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--Custom Fields-->
                            @if (!$customFields->isEmpty())
                                @foreach ($customFields as $customField)
                                    <div class="{{ $customField->field_width }}">
                                        <div class="form-group">
                                            <label class="control-label">{{ $customField->field_name }}</label>
                                            {!! xss_clean(generate_input_field($customField)) !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Attachment') }}</label>
                                    <input type="file" class="dropify" name="attachment"
                                        value="{{ old('attachment') }}">
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Address') }}</label>
                                    <textarea class="form-control" name="address">{{ old('address') }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Description') }}</label>
                                    <textarea class="form-control" name="description">{{ old('description') }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Remarks') }}</label>
                                    <textarea class="form-control" name="remarks">{{ old('remarks') }}</textarea>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary"><i
                                            class="ti-check-box"></i>&nbsp;{{ _lang('Submit') }}</button>
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
            $(document).on('change', '#loan_product_id', function() {
                $("#late_payment_penalties").val($(this).find(':selected').data('penalties'));

            });


            $(document).on('change', '#applied_amount, #interest_rate', function() {
                calculateLoan();
            });

            function calculateLoan() {
                var principal = parseFloat($('#applied_amount').val()) || 0;
                var rate = parseFloat($('#interest_rate').val()) || 0;
                var term = parseInt($('#term').val()) || 0;
                var termPeriod = $('#term_period').val() || 'month';

                if (principal > 0 && rate > 0 && term > 0) {

                    var monthlyRate = rate / 100 / 12;
                    var payments = term;
                    if (termPeriod === 'year') {
                        payments = term * 12;
                    }

                    var x = Math.pow(1 + monthlyRate, payments);
                    var monthlyPayment = (principal * x * monthlyRate) / (x - 1);

                    if (!isNaN(monthlyPayment) && isFinite(monthlyPayment)) {
                        $('#monthly_payment_preview').html(monthlyPayment.toFixed(2));
                    }
                }
            }
        })(jQuery);
    </script>
@endsection
