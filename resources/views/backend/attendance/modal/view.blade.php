<table class="table table-bordered">
    <tr>
        <td>{{ _lang('Employee') }}</td>
        <td>{{ $attendance->user->name }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Date') }}</td>
        <td>{{ $attendance->date }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Login Time') }}</td>
        <td>{{ $attendance->login_time ? date('h:i A', strtotime($attendance->login_time)) : '-' }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Logout Time') }}</td>
        <td>{{ $attendance->logout_time ? date('h:i A', strtotime($attendance->logout_time)) : '-' }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Status') }}</td>
        <td>{{ $attendance->status }}</td>
    </tr>
    <tr>
        <td>{{ _lang('Note') }}</td>
        <td>{{ $attendance->note }}</td>
    </tr>
</table>
