<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CommissionRecord;
use App\Models\Member;
use Illuminate\Http\Request;

class ClientReferralController extends Controller
{
    public function summary(Request $request)
    {
        $authenticatedMember = $request->user();

        if (!$authenticatedMember instanceof Member) {
            return response()->json([
                'status' => 'error',
                'message' => 'Authenticated client account is required.',
            ], 403);
        }

        // Referral counts follow the same three-generation client network shown
        // in the web referral page. Track visited IDs to avoid referral cycles.
        $levelCounts = [1 => 0, 2 => 0, 3 => 0];
        $visitedIds = [$authenticatedMember->id];
        $frontierIds = [$authenticatedMember->id];

        for ($level = 1; $level <= 3 && $frontierIds; $level++) {
            $members = Member::withoutGlobalScopes()
                ->whereIn('referred_by_member_id', $frontierIds)
                ->whereNotIn('id', $visitedIds)
                ->orderBy('id')
                ->get(['id']);

            $levelCounts[$level] = $members->count();
            $frontierIds = $members->pluck('id')->all();
            $visitedIds = array_merge($visitedIds, $frontierIds);
        }

        $commissionQuery = CommissionRecord::query()
            ->where('recipient_type', 'member')
            ->where('recipient_id', $authenticatedMember->id)
            ->whereIn('level', [1, 2, 3]);

        $commissionTotals = (clone $commissionQuery)
            ->selectRaw('level, COALESCE(SUM(amount), 0) as total_amount')
            ->groupBy('level')
            ->pluck('total_amount', 'level');

        $commissionByLevel = [];
        foreach ([1, 2, 3] as $level) {
            $commissionByLevel['level_' . $level] = [
                'rate_percent' => (float) get_option('commission_level_' . $level . '_rate', [1 => '0.50', 2 => '0.30', 3 => '0.20'][$level]),
                'earned' => (float) ($commissionTotals[$level] ?? 0),
            ];
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Referral summary fetched successfully.',
            'data' => [
                'member_id' => $authenticatedMember->id,
                'member_no' => $authenticatedMember->member_no,
                'referral_code' => $authenticatedMember->referral_code,
                'direct_referrals' => $levelCounts[1],
                'network_by_level' => $levelCounts,
                'total_referral_commission_earned' => (float) (clone $commissionQuery)->sum('amount'),
                'commission_by_level' => $commissionByLevel,
            ],
        ]);
    }
}
