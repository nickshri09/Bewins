@extends('layouts.app')

@section('content')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <span class="panel-title">{{ _lang('Update Loan Information') }}</span>
                </div>
                <div class="card-body">
                    @if ($loan->status == 1)
                        <div class="alert alert-warning">
                            <strong>{{ _lang('Loan has already approved. You can change only description and remarks') }}</strong>
                        </div>
                    @endif
                    <form method="post" class="validate" autocomplete="off" action="{{ route('loans.update', $id) }}"
                        enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <input name="_method" type="hidden" value="PATCH">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Loan ID') }}</label>
                                    <input type="text" class="form-control" name="loan_id" value="{{ $loan->loan_id }}"
                                        {{ $loan->status == 1 ? 'disabled' : 'required' }}>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Loan Product') }}</label>
                                    <select class="form-control auto-select select2"
                                        data-selected="{{ $loan->loan_product_id }}" id="loan_product_id"
                                        name="loan_product_id" {{ $loan->status == 1 ? 'disabled' : 'required' }}>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @foreach (\App\Models\LoanProduct::active()->get() as $loanProduct)
                                            <option value="{{ $loanProduct->id }}"
                                                data-penalties="{{ $loanProduct->late_payment_penalties }}">
                                                {{ $loanProduct->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Borrower') }}</label>
                                    <select class="form-control auto-select select2"
                                        data-selected="{{ $loan->borrower_id }}" name="borrower_id" id="borrower_id"
                                        {{ $loan->status == 1 ? 'disabled' : 'required' }}>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @if (auth()->user()->user_type === 'admin')
                                            @foreach (\App\Models\Member::all() as $member)
                                                <option value="{{ $member->id }}">
                                                    {{ $member->first_name . ' ' . $member->last_name . ' (' . $member->member_no . ')' }}
                                                </option>
                                            @endforeach
                                        @else
                                            @foreach (\App\Models\Member::where('created_by', auth()->id())->get() as $member)
                                                <option value="{{ $member->id }}">
                                                    {{ $member->first_name . ' ' . $member->last_name . ' (' . $member->member_no . ')' }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Currency') }}</label>
                                    <select class="form-control auto-select" data-selected="{{ $loan->currency_id }}"
                                        name="currency_id" {{ $loan->status == 1 ? 'disabled' : 'required' }}>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        {{ create_option('currency', 'id', 'name', '', ['status=' => 1]) }}
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('First Payment Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="first_payment_date"
                                        value="{{ $loan->getRawOriginal('first_payment_date') }}"
                                        {{ $loan->status == 1 ? 'disabled' : 'required' }}>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Release Date') }}</label>
                                    <input type="text" class="form-control datepicker" name="release_date"
                                        value="{{ $loan->getRawOriginal('release_date') }}"
                                        {{ $loan->status == 1 ? 'disabled' : 'required' }}>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Applied Amount') }}</label>
                                    <input type="text" class="form-control float-field" name="applied_amount"
                                        value="{{ $loan->applied_amount }}"
                                        {{ $loan->status == 1 ? 'disabled' : 'required' }}>
                                </div>
                            </div>

                            <!-- ब्याज दर का फ़ील्ड जोड़ें -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Interest Rate (%)') }}</label>
                                    <select class="form-control auto-select" name="interest_rate"
                                        data-selected="{{ $loan->interest_rate }}"
                                        {{ $loan->status == 1 ? 'disabled' : 'required' }}>
                                        <option value="">{{ _lang('Select One') }}</option>
                                        @if (isset($expectedReturns))
                                            @foreach ($expectedReturns as $expectedReturn)
                                                <option value="{{ $expectedReturn->expected_return }}"
                                                    {{ $loan->interest_rate == $expectedReturn->expected_return ? 'selected' : '' }}>
                                                    {{ $expectedReturn->expected_return }}%
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Late Payment Penalties') }}</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control float-field" name="late_payment_penalties"
                                            value="{{ $loan->late_payment_penalties }}" id="late_payment_penalties"
                                            {{ $loan->status == 1 ? 'disabled' : 'required' }}>
                                        <div class="input-group-append">
                                            <span class="input-group-text">%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!--Custom Fields-->
                            @if (!$customFields->isEmpty())
                                @php $customFieldsData = json_decode($loan->custom_fields, true); @endphp
                                @foreach ($customFields as $customField)
                                    <div class="{{ $customField->field_width }}">
                                        <div class="form-group">
                                            <label class="control-label">{{ $customField->field_name }}</label>
                                            {!! xss_clean(
                                                generate_input_field($customField, $customFieldsData[$customField->field_name]['field_value'] ?? null),
                                            ) !!}
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Attachment') }}</label>
                                    <input type="file" class="dropify" name="attachment"
                                        data-default-file="{{ $loan->attachment != null ? asset('public/uploads/media/' . $loan->attachment) : '' }}"
                                        {{ $loan->status == 1 ? 'disabled' : '' }}>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Address') }}</label>
                                    <textarea class="form-control" name="address">{{ $loan->address }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Description') }}</label>
                                    <textarea class="form-control" name="description">{{ $loan->description }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label">{{ _lang('Remarks') }}</label>
                                    <textarea class="form-control" name="remarks">{{ $loan->remarks }}</textarea>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">{{ _lang('Update Changes') }}</button>
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
