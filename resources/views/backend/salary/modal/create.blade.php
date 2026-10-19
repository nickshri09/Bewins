<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('salaries.store') }}">
    {{ csrf_field() }}
    <div class="row px-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Employee') }}</label>
                <select class="form-control select2" name="user_id" required>
                    <option value="">{{ _lang('Select Employee') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" data-salary="{{ $user->salary }}">{{ $user->name }}
                            ({{ $user->designation }})
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Month') }}</label>
                <select class="form-control auto-select" name="month" required>
                    <option value="">{{ _lang('Select Month') }}</option>
                    @foreach (['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'] as $month)
                        <option value="{{ $month }}" {{ date('F') == $month ? 'selected' : '' }}>
                            {{ $month }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Year') }}</label>
                <input type="number" class="form-control" name="year" value="{{ date('Y') }}" required>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Attendance Summary') }}</label>
                <div class="alert alert-info py-2">
                    <span class="mr-3 text-dark"><b>{{ _lang('Working Days') }}:</b> <span
                            id="total_working_days">0</span></span>
                    <span class="mr-3 text-success"><b>{{ _lang('Presents') }}:</b> <span
                            id="total_presents">0</span></span>
                    <span class="mr-3 text-danger"><b>{{ _lang('Absents') }}:</b> <span
                            id="total_absents">0</span></span>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Monthly Salary') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ currency(get_base_currency()) }}</span>
                    </div>
                    <input type="text" class="form-control" id="monthly_salary" disabled>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Calculated Salary') }}</label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text">{{ currency(get_base_currency()) }}</span>
                    </div>
                    <input type="text" class="form-control float-field" name="amount" id="salary_amount" required>
                </div>
                <small
                    class="text-muted">{{ _lang('Calculated based on working days and presents. Editable.') }}</small>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Status') }}</label>
                <select class="form-control auto-select" name="status" required>
                    <option value="Paid">{{ _lang('Paid') }}</option>
                    <option value="Pending">{{ _lang('Pending') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Payment Date') }}</label>
                <input type="text" class="form-control datepicker" name="payment_date" value="{{ date('Y-m-d') }}">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Note') }}</label>
                <textarea class="form-control" name="note">{{ old('note') }}</textarea>
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i
                        class="ti-check-box"></i>&nbsp;{{ _lang('Submit') }}</button>
            </div>
        </div>
    </div>
</form>

<script>
    $(document).on('change', 'select[name="user_id"], select[name="month"], input[name="year"]', function() {
        var user_id = $('select[name="user_id"]').val();
        var month = $('select[name="month"]').val();
        var year = $('input[name="year"]').val();

        var base_salary = parseInt($('select[name="user_id"]').find(':selected').data('salary')) || 0;
        $('#monthly_salary').val(base_salary);

        if (user_id != '' && month != '' && year != '') {
            $.ajax({
                url: "{{ route('salaries.get_attendance') }}",
                data: {
                    user_id: user_id,
                    month: month,
                    year: year
                },
                success: function(data) {
                    $('#total_working_days').text(data.total_working_days);
                    $('#total_presents').text(data.presents);
                    $('#total_absents').text(data.absents);

                    // Calculation: (Total Salary / Total Working Days) * Presents
                    var calculated_salary = 0;
                    if (data.total_working_days > 0) {
                        calculated_salary = (base_salary / data.total_working_days) * data.presents;
                    }
                    $('#salary_amount').val(calculated_salary.toFixed(2));
                }
            });
        }
    });
</script>
