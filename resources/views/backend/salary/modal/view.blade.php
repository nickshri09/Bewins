<table class="table table-bordered">
    <tr>
        <td>{{ _lang('Employee') }}</td>
        <td>{{ $salary->user->name }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Designation') }}</td>
        <td>{{ $salary->user->designation }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Month') }}</td>
        <td>{{ $salary->month }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Year') }}</td>
        <td>{{ $salary->year }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Amount') }}</td>
        <td>{{ decimalPlace($salary->amount, currency(get_base_currency())) }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Status') }}</td>
        <td>{{ $salary->status }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Payment Date') }}</td>
        <td>{{ $salary->payment_date }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Note') }}</td>
        <td>{{ $salary->note }}</td>
    </tr>
</table>
