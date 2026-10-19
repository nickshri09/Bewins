<?php

namespace App\Http\Controllers;

use App\Models\Investment;
use App\Models\MemberWallet;
use App\Models\MemberWalletTransaction;
use App\Models\WithdrawalRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class WithdrawalRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = WithdrawalRequest::with(['member', 'investment'])
            ->orderByRaw("FIELD(status, 'pending', 'approved', 'rejected')")
            ->orderByDesc('id');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        $requests = $query->paginate(20);

        return view('backend.withdrawal_requests.index', compact('requests'));
    }

    public function approve(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $wr = WithdrawalRequest::whereKey($id)->lockForUpdate()->firstOrFail();
                if ($wr->status !== 'pending') {
                    throw new \DomainException('Request is already processed.');
                }

                if ($wr->type === 'wallet') {
                    $wallet = MemberWallet::where('member_id', $wr->member_id)->lockForUpdate()->first();
                    if (!$wallet || (float) $wallet->balance < (float) $wr->amount) {
                        throw new \DomainException('Member does not have sufficient wallet balance.');
                    }

                    $wallet->balance = (float) $wallet->balance - (float) $wr->amount;
                    $wallet->save();

                    MemberWalletTransaction::create([
                        'member_wallet_id' => $wallet->id,
                        'member_id'        => $wr->member_id,
                        'type'             => 'debit',
                        'amount'           => $wr->amount,
                        'gross_amount'     => 0,
                        'note'             => 'Wallet Withdrawal Approved (Request #'.$wr->id.')',
                        'created_by'       => Auth::id(),
                    ]);
                } elseif ($wr->type === 'investment') {
                    $investment = Investment::whereKey($wr->investment_id)->lockForUpdate()->firstOrFail();
                    if ((int) $investment->customer_id !== (int) $wr->member_id || (float) $investment->investment_amount < (float) $wr->amount) {
                        throw new \DomainException('Investment does not have sufficient available balance.');
                    }

                    $investment->investment_amount = (float) $investment->investment_amount - (float) $wr->amount;
                    $investment->updateReturns();
                    $investment->save();
                } else {
                    throw new \DomainException('Unsupported withdrawal type.');
                }

                $wr->update([
                    'status'       => 'approved',
                    'admin_note'   => $request->input('admin_note'),
                    'processed_at' => now(),
                ]);
            });
            return back()->with('success', 'Withdrawal request approved and amount deducted successfully.');

        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Error approving request: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_note' => 'required|string',
        ], [
            'admin_note.required' => 'Rejection reason is required.'
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $wr = WithdrawalRequest::whereKey($id)->lockForUpdate()->firstOrFail();
                if ($wr->status !== 'pending') {
                    throw new \DomainException('Request is already processed.');
                }
                $wr->update([
                    'status' => 'rejected',
                    'admin_note' => $request->admin_note,
                    'processed_at' => now(),
                ]);
            });
        } catch (\DomainException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', 'Withdrawal request rejected successfully.');
    }
}
