<form method="post" class="ajax-submit" autocomplete="off" action="{{ route('attendances.update', $attendance->id) }}">
    {{ csrf_field() }}
    <input name="_method" type="hidden" value="PATCH">
    <div class="row px-2">
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Employee') }}</label>
                <select class="form-control select2" name="user_id" required>
                    <option value="">{{ _lang('Select Employee') }}</option>
                    @foreach ($users as $user)
                        <option value="{{ $user->id }}" {{ $attendance->user_id == $user->id ? 'selected' : '' }}>
                            {{ $user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Date') }}</label>
                <input type="text" class="form-control datepicker" name="date" value="{{ $attendance->date }}"
                    required>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Status') }}</label>
                <select class="form-control auto-select" name="status" data-selected="{{ $attendance->status }}"
                    required>
                    <option value="Present">{{ _lang('Present') }}</option>
                    <option value="Absent">{{ _lang('Absent') }}</option>
                    <option value="Late">{{ _lang('Late') }}</option>
                    <option value="Leave">{{ _lang('Leave') }}</option>
                </select>
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Login Time') }}</label>
                <input type="time" class="form-control" name="login_time" value="{{ $attendance->login_time }}">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label">{{ _lang('Logout Time') }}</label>
                <input type="time" class="form-control" name="logout_time" value="{{ $attendance->logout_time }}">
            </div>
        </div>

        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label">{{ _lang('Note') }}</label>
                <textarea class="form-control" name="note">{{ $attendance->note }}</textarea>
            </div>
        </div>

        <div class="col-md-12 text-right">
            <div class="form-group">
                <button type="submit" class="btn btn-primary"><i
                        class="ti-check-box"></i>&nbsp;{{ _lang('Update') }}</button>
            </div>
        </div>
    </div>
</form>
