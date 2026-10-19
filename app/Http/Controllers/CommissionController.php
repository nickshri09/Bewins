<?php

namespace App\Http\Controllers;

use App\Models\CommissionRecord;
use App\Models\Member;
use Illuminate\Http\Request;

class CommissionController extends Controller
{
    public function myReferrals(Request $request)
    {
        $user = auth()->user();
        abort_unless($user, 401);

        // Three-level referral commissions belong to client/customer accounts only.
        $member = $user->user_type === 'customer'
            ? Member::withoutGlobalScopes()->where('user_id', $user->id)->first()
            : null;
        $search = trim((string) $request->query('q', ''));
        $referralHierarchy = [1 => collect(), 2 => collect(), 3 => collect()];
        $referralCounts = [1 => 0, 2 => 0, 3 => 0];
        $visitedMemberIds = $member ? [$member->id] : [];
        $frontierIds = $member ? [$member->id] : [];

        // Load the member's downline through three generations only.
        for ($level = 1; $level <= 3 && $frontierIds; $level++) {
            $query = Member::withoutGlobalScopes()
                ->whereIn('referred_by_member_id', $frontierIds)
                ->orderBy('id');
            if ($visitedMemberIds) {
                $query->whereNotIn('id', $visitedMemberIds);
            }

            $levelMembers = $query->get();
            $referralCounts[$level] = $levelMembers->count();
            $referralHierarchy[$level] = $search === ''
                ? $levelMembers
                : $levelMembers->filter(function (Member $referral) use ($search) {
                    $searchable = implode(' ', [
                        $referral->member_no,
                        $referral->first_name,
                        $referral->last_name,
                        $referral->referral_code,
                    ]);
                    return stripos($searchable, $search) !== false;
                })->values();

            $visitedMemberIds = array_merge($visitedMemberIds, $levelMembers->pluck('id')->all());
            $frontierIds = $levelMembers->pluck('id')->all();
        }

        $directReferrals = $referralHierarchy[1];
        $memberCommissions = $member
            ? CommissionRecord::with(['investment', 'sourceMember'])
                ->where('recipient_type', 'member')
                ->where('recipient_id', $member->id)
                ->latest()->paginate(20, ['*'], 'member_commissions_page')
            : collect();
        $agentCommissions = CommissionRecord::with(['investment', 'sourceMember'])
            ->where('recipient_type', 'user')
            ->where('recipient_id', $user->id)
            ->where('level', 0)
            ->latest()->paginate(20, ['*'], 'agent_commissions_page');
        $directReferralCount = $member
            ? Member::withoutGlobalScopes()->where('referred_by_member_id', $member->id)->count()
            : 0;
        $managedClients = Member::withoutGlobalScopes()
            ->when($member, fn ($query) => $query->where('id', '!=', $member->id))
            ->where(function ($query) use ($user) {
                $query->where('referred_by_user_id', $user->id)
                    ->orWhere('assigned_user_id', $user->id)
                    ->orWhere('created_by', $user->id);
            })
            ->orderBy('id')
            ->limit(100)
            ->get();
        $memberCommissionTotal = $member
            ? (float) CommissionRecord::where('recipient_type', 'member')->where('recipient_id', $member->id)->sum('amount')
            : 0;
        $agentCommissionTotal = (float) CommissionRecord::where('recipient_type', 'user')
            ->where('recipient_id', $user->id)->where('level', 0)->sum('amount');
        $referralRates = [
            1 => (float) get_option('commission_level_1_rate', '0.50'),
            2 => (float) get_option('commission_level_2_rate', '0.30'),
            3 => (float) get_option('commission_level_3_rate', '0.20'),
        ];
        $directAgentRate = (float) get_option('commission_direct_agent_rate', '2.00');

        return view('backend.commissions.my_referrals', compact(
            'user', 'member', 'directReferrals', 'referralHierarchy', 'referralCounts', 'search', 'referralRates', 'directAgentRate',
            'memberCommissions', 'agentCommissions', 'directReferralCount', 'managedClients',
            'memberCommissionTotal', 'agentCommissionTotal'
        ));
    }

    private function ensureAdmin(): void
    {
        abort_unless(auth()->check() && auth()->user()->user_type === 'admin', 403);
    }

    public function network(Request $request)
    {
        $this->ensureAdmin();
        $search = trim((string) $request->query('q', ''));
        $memberQuery = Member::withoutGlobalScopes();
        if ($search !== '') {
            $memberQuery->where(function ($query) use ($search) {
                $query->where('member_no', 'like', '%' . $search . '%')
                    ->orWhere('referral_code', 'like', '%' . $search . '%')
                    ->orWhere('first_name', 'like', '%' . $search . '%')
                    ->orWhere('last_name', 'like', '%' . $search . '%')
                    ->orWhereHas('referrer', function ($referrerQuery) use ($search) {
                        $referrerQuery->where('member_no', 'like', '%' . $search . '%')
                            ->orWhere('referral_code', 'like', '%' . $search . '%')
                            ->orWhere('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%');
                    });
            });
        }

        $members = $memberQuery
            ->with(['referrer' => fn ($query) => $query->withoutGlobalScopes()])
            ->with('referredByUser')
            ->withCount('referrals')
            ->orderBy('id')
            ->paginate(30);

        return view('backend.commissions.network', compact('members', 'search'));
    }

    public function settings()
    {
        $this->ensureAdmin();
        return view('backend.commissions.settings', [
            'rates' => [
                'direct_agent' => get_option('commission_direct_agent_rate', '2.00'),
                'level_1' => get_option('commission_level_1_rate', '0.50'),
                'level_2' => get_option('commission_level_2_rate', '0.30'),
                'level_3' => get_option('commission_level_3_rate', '0.20'),
            ],
        ]);
    }

    public function updateSettings(Request $request)
    {
        $this->ensureAdmin();
        $values = $request->validate([
            'direct_agent' => 'required|numeric|min:0|max:100',
            'level_1' => 'required|numeric|min:0|max:100',
            'level_2' => 'required|numeric|min:0|max:100',
            'level_3' => 'required|numeric|min:0|max:100',
        ]);

        foreach ($values as $key => $value) {
            update_option('commission_' . $key . '_rate', number_format((float) $value, 4, '.', ''));
        }

        return redirect()->route('commissions.settings')->with('success', _lang('Commission rates updated successfully'));
    }

    public function history()
    {
        $this->ensureAdmin();
        $records = CommissionRecord::with(['investment', 'sourceMember'])
            ->orderByDesc('id')
            ->paginate(30);
        $members = Member::withoutGlobalScopes()->get()->keyBy('id');
        $users = \App\Models\User::query()->get()->keyBy('id');

        return view('backend.commissions.history', compact('records', 'members', 'users'));
    }
}
