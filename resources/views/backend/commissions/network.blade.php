@extends('layouts.app')

@section('content')
<div class="card">
    <div class="card-header"><span class="panel-title">{{ _lang('Referral Network') }}</span></div>
    <div class="card-body table-responsive">
        <form method="GET" action="{{ route('commissions.network') }}" class="form-inline mb-3">
            <input type="search" name="q" value="{{ $search }}" class="form-control mr-2" placeholder="{{ _lang('Search clients or referral codes') }}" aria-label="{{ _lang('Search clients or referral codes') }}">
            <button class="btn btn-outline-primary" type="submit">{{ _lang('Search') }}</button>
            @if($search !== '')
                <a class="btn btn-link" href="{{ route('commissions.network') }}">{{ _lang('Clear') }}</a>
            @endif
        </form>
        <table class="table table-bordered">
            <thead><tr><th>{{ _lang('Client No') }}</th><th>{{ _lang('Client') }}</th><th>{{ _lang('Referral Code') }}</th><th>{{ _lang('Referred By') }}</th><th>{{ _lang('Direct Referrals') }}</th><th>{{ _lang('Level 2 Upline') }}</th><th>{{ _lang('Level 3 Upline') }}</th></tr></thead>
            <tbody>
            @forelse($members as $member)
                @php($level2 = $member->referrer?->referrer)
                @php($level3 = $level2?->referrer)
                <tr>
                    <td>{{ $member->member_no }}</td>
                    <td>{{ $member->first_name }} {{ $member->last_name }}</td>
                    <td>{{ $member->referral_code ?: '—' }}</td>
                    <td>
                        @if($member->referrer)
                            {{ $member->referrer->first_name.' '.$member->referrer->last_name.' ('.$member->referrer->referral_code.')' }}
                        @elseif($member->referredByUser)
                            {{ $member->referredByUser->name }} ({{ $member->referredByUser->referral_code }})
                        @else
                            —
                        @endif
                    </td>
                    <td>{{ $member->referrals_count }}</td>
                    <td>{{ $level2 ? $level2->first_name.' '.$level2->last_name.' ('.$level2->referral_code.')' : '—' }}</td>
                    <td>{{ $level3 ? $level3->first_name.' '.$level3->last_name.' ('.$level3->referral_code.')' : '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" class="text-center">{{ _lang('No clients found') }}</td></tr>
            @endforelse
            </tbody>
        </table>
        {{ $members->appends(['q' => $search])->links() }}
    </div>
</div>
@endsection
