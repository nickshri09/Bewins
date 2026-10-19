@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header"><span class="panel-title">{{ _lang('Referral & Commission') }}</span></div>
            <div class="card-body">
                <div class="alert alert-light border">
                    @if($user->user_type === 'customer')
                        <strong>{{ _lang('Current referral commission rates') }}:</strong>
                        {{ _lang('Level 1') }} {{ number_format($referralRates[1], 2) }}%
                        <span class="mx-2">|</span>
                        {{ _lang('Level 2') }} {{ number_format($referralRates[2], 2) }}%
                        <span class="mx-2">|</span>
                        {{ _lang('Level 3') }} {{ number_format($referralRates[3], 2) }}%
                        <div class="small mt-1">{{ _lang('Referral commission is calculated when an eligible investment is approved. Rates apply to new approvals.') }}</div>
                    @elseif(in_array($user->user_type, ['user', 'admin']))
                        <strong>{{ _lang('Direct Agent') }}:</strong> {{ number_format($directAgentRate, 2) }}%
                        <div class="small mt-1">{{ _lang('Direct Agent commission applies to eligible service value.') }}</div>
                    @endif
                </div>

                @if($member)
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted">{{ _lang('Your Referral Code') }}</div>
                                <div class="input-group mt-2">
                                    <input class="form-control" id="my-referral-code" value="{{ $member->referral_code }}" readonly>
                                    <div class="input-group-append"><button class="btn btn-primary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('my-referral-code').value)">{{ _lang('Copy') }}</button></div>
                                </div>
                                <small class="text-muted">{{ _lang('Share this code with people you refer.') }}</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted">{{ _lang('My Referral Network') }}</div>
                                <div class="small mt-2">{{ _lang('Level 1') }}: <strong>{{ $referralCounts[1] }}</strong></div>
                                <div class="small">{{ _lang('Level 2') }}: <strong>{{ $referralCounts[2] }}</strong></div>
                                <div class="small">{{ _lang('Level 3') }}: <strong>{{ $referralCounts[3] }}</strong></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="border rounded p-3 h-100">
                                <div class="text-muted">{{ _lang('Referral Commission Earned') }}</div>
                                <div class="h3 mb-0">₹{{ number_format($memberCommissionTotal, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="mb-4"><strong>{{ _lang('Referred By') }}:</strong>
                        {{ $member->referrer ? $member->referrer->first_name.' '.$member->referrer->last_name.' ('.$member->referrer->referral_code.')' : ($member->referredByUser ? $member->referredByUser->name.' ('.$member->referredByUser->referral_code.')' : _lang('No referrer linked')) }}
                    </div>

                    <h5>{{ _lang('My Referral Network (up to 3 levels)') }}</h5>
                    <form method="GET" action="{{ route('commissions.mine') }}" class="form-inline mb-3">
                        <input type="search" name="q" value="{{ $search }}" class="form-control mr-2" placeholder="{{ _lang('Search my referrals') }}" aria-label="{{ _lang('Search my referrals') }}">
                        <button class="btn btn-outline-primary" type="submit">{{ _lang('Search') }}</button>
                        @if($search !== '')
                            <a class="btn btn-link" href="{{ route('commissions.mine') }}">{{ _lang('Clear') }}</a>
                        @endif
                    </form>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered">
                            <thead><tr><th>{{ _lang('Level') }}</th><th>{{ _lang('Client No') }}</th><th>{{ _lang('Name') }}</th><th>{{ _lang('Referral Code') }}</th><th>{{ _lang('Referred By') }}</th><th>{{ _lang('Joined') }}</th></tr></thead>
                            <tbody>
                            @php($hasReferralRows = false)
                            @foreach($referralHierarchy as $level => $levelMembers)
                                @foreach($levelMembers as $referral)
                                    @php($hasReferralRows = true)
                                    <tr>
                                        <td>{{ _lang('Level') }} {{ $level }}</td>
                                        <td>{{ $referral->member_no }}</td>
                                        <td>{{ $referral->first_name }} {{ $referral->last_name }}</td>
                                        <td>{{ $referral->referral_code }}</td>
                                        <td>{{ $referral->referrer?->first_name }} {{ $referral->referrer?->last_name }}</td>
                                        <td>{{ $referral->created_at }}</td>
                                    </tr>
                                @endforeach
                            @endforeach
                            @unless($hasReferralRows)
                                <tr><td colspan="6" class="text-center text-muted">{{ $search !== '' ? _lang('No matching referrals found') : _lang('No referrals yet') }}</td></tr>
                            @endunless
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mt-4">{{ _lang('Referral Commission History') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th>{{ _lang('Date') }}</th><th>{{ _lang('Level') }}</th><th>{{ _lang('Investment') }}</th><th>{{ _lang('Rate') }}</th><th>{{ _lang('Commission') }}</th></tr></thead>
                            <tbody>
                            @forelse($memberCommissions as $commission)
                                <tr>
                                    <td>{{ $commission->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ _lang('Level') }} {{ $commission->level }}</td>
                                    <td>{{ $commission->investment?->investment_code ?? '#'.$commission->investment_id }}</td>
                                    <td>{{ number_format($commission->rate_percent, 2) }}%</td>
                                    <td>₹{{ number_format($commission->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted">{{ _lang('No referral commission earned yet') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $memberCommissions->links() }}
                @elseif($user->user_type === 'customer')
                    <div class="alert alert-info">{{ _lang('No client profile is linked to this login. Direct Agent commissions and your assigned clients are shown below.') }}</div>
                @endif

                @if(in_array($user->user_type, ['user', 'admin']))
                    @if($user->referral_code)
                        <div class="border rounded p-3 mb-4">
                            <div class="text-muted">{{ _lang('Your User Referral Code') }}</div>
                            <div class="input-group mt-2" style="max-width: 420px">
                                <input class="form-control" id="my-user-referral-code" value="{{ $user->referral_code }}" readonly>
                                <div class="input-group-append"><button class="btn btn-primary" type="button" onclick="navigator.clipboard.writeText(document.getElementById('my-user-referral-code').value)">{{ _lang('Copy') }}</button></div>
                            </div>
                            <small class="text-muted">{{ _lang('Clients who register with this code can earn you direct-agent commission when their eligible investment is approved.') }}</small>
                        </div>
                    @endif
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="border rounded p-3">
                                <div class="text-muted">{{ _lang('Direct Agent Commission Earned') }}</div>
                                <div class="h3 mb-0">₹{{ number_format($agentCommissionTotal, 2) }}</div>
                            </div>
                        </div>
                    </div>
                    <h5>{{ _lang('Direct Agent Commission History') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th>{{ _lang('Date') }}</th><th>{{ _lang('Client') }}</th><th>{{ _lang('Investment') }}</th><th>{{ _lang('User Referral Code') }}</th><th>{{ _lang('Rate') }}</th><th>{{ _lang('Commission') }}</th></tr></thead>
                            <tbody>
                            @forelse($agentCommissions as $commission)
                                <tr>
                                    <td>{{ $commission->created_at->format('d M Y H:i') }}</td>
                                    <td>{{ $commission->sourceMember?->first_name }} {{ $commission->sourceMember?->last_name }}</td>
                                    <td>{{ $commission->investment?->investment_code ?? '#'.$commission->investment_id }}</td>
                                    <td>{{ $user->referral_code ?? '—' }}</td>
                                    <td>{{ number_format($commission->rate_percent, 2) }}%</td>
                                    <td>₹{{ number_format($commission->amount, 2) }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted">{{ _lang('No direct agent commission earned yet') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                    {{ $agentCommissions->links() }}

                    <h5 class="mt-4">{{ _lang('Clients Managed by You') }}</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead><tr><th>{{ _lang('Client No') }}</th><th>{{ _lang('Name') }}</th><th>{{ _lang('Referral Code') }}</th></tr></thead>
                            <tbody>
                            @forelse($managedClients as $client)
                                <tr><td>{{ $client->member_no }}</td><td>{{ $client->first_name }} {{ $client->last_name }}</td><td>{{ $client->referral_code }}</td></tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted">{{ _lang('No clients assigned') }}</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
