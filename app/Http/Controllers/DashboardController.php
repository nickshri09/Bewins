<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\FD;
use App\Models\Investment;
use App\Models\Loan;
use App\Models\LiveBanner;
use App\Models\DashboardTicker;
use App\Models\LoanRepayment;
use App\Models\Member;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Models\WalletTransaction;  // <-- ADDED: Import WalletTransaction model
use Illuminate\Database\Eloquent\Builder;
use \App\Models\Service;
use \App\Models\wallet;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        date_default_timezone_set(get_option('timezone', 'Asia/Dhaka'));
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
    // {
    //     $user = auth()->user();
    //     $user_type = $user->user_type;
    //     $date = date('Y-m-d');
    //     $data = array();
    //     // dd($user);

    //     if ($user_type == 'user') {
    //         $data['due_repayments'] = collect();
    //         $data['total_leads'] = Order::where('user_id', $user->id)->count();
    //         $data['totalServices'] = Service::count();
    //         $data['walletAmount'] = $user->wallet_amount;
    //     } else {
    //         $data['total_leads'] = Order::count();
    //     }

    //     // if ($user_type == 'user') {
    //     //     $data['totalServices'] = Service::where('user_id', $user->id)->count();
    //     // } else {
    //     //     $data['totalServices'] = Service::count();
    //     // }
    //     $data['walletAmount'] = $user->wallet_amount;

    //     if ($user_type == 'user') {
    //         $data['recent_transactions'] = Transaction::where('member_id', $user->member->id)
    //             ->limit('10')
    //             ->orderBy('trans_date', 'desc')
    //             ->get();

    //         // Load customer's own wallet transactions
    //         $data['recent_wallet_transactions'] = WalletTransaction::with(['user', 'createdBy'])
    //             ->where('user_id', $user->id)
    //             ->latest('created_at')
    //             ->limit(10)
    //             ->get();

    //         $data['loans'] = Loan::where('status', 1)->where('borrower_id', $user->member->id)->get();
    //         $data['my_total_members'] = Member::where('user_id', $user->id)->count();
    //         $data['my_total_orders'] = Order::where('user_id', $user->id)->count();
    //     } else {
    //         $branch_id = session('branch_id');
    //         $branch_id = ($branch_id == 'default') ? null : $branch_id;

    //         // Recent Transactions
    //         $transactionQuery = Transaction::limit('10')->orderBy('trans_date', 'desc');
    //         if ($branch_id) {
    //             $transactionQuery->whereHas('member', function ($q) use ($branch_id) {
    //                 $q->where('branch_id', $branch_id);
    //             });
    //         }
    //         $data['recent_transactions'] = $transactionQuery->get();

    //         // Load latest 10 wallet transactions (for admin/staff view)
    //         $walletQuery = WalletTransaction::with(['user', 'createdBy'])->latest('created_at')->limit(10);
    //         if ($branch_id) {
    //             $walletQuery->whereHas('user', function ($q) use ($branch_id) {
    //                 $q->where('branch_id', $branch_id);
    //             });
    //         }
    //         $data['recent_wallet_transactions'] = $walletQuery->get();

    //         $data['due_repayments'] = LoanRepayment::selectRaw('loan_repayments.*, MAX(repayment_date) as repayment_date, COUNT(id) as total_due_repayment, SUM(amount_to_pay) as total_due')
    //             ->with('loan')
    //             ->whereHas('loan')
    //             ->whereRaw("repayment_date < '$date'")
    //             ->where('status', 0)
    //             ->groupBy('loan_id')
    //             ->get();

    //         $data['total_customer'] = Member::count();

    //         $userQuery = User::whereNotIn('email', ['admin@gmail.com', 'bellway@gmail.com']);
    //         if ($branch_id) {
    //             $userQuery->where('branch_id', $branch_id);
    //         }
    //         $data['total_users'] = $userQuery->count();

    //         $data['total_members'] = Member::count();

    //         $orderQuery = Order::query();
    //         if ($branch_id) {
    //             $orderQuery->whereHas('user', function ($q) use ($branch_id) {
    //                 $q->where('branch_id', $branch_id);
    //             });
    //         }
    //         $data['total_leads'] = $orderQuery->count();
    //         $data['total_orders'] = $orderQuery->count();

    //         $todayOrderQuery = Order::whereDate('created_at', $date);
    //         if ($branch_id) {
    //             $todayOrderQuery->whereHas('user', function ($q) use ($branch_id) {
    //                 $q->where('branch_id', $branch_id);
    //             });
    //         }
    //         $data['todays_orders'] = $todayOrderQuery->count();

    //         $data['totalServices'] = Service::count();
    //         $data['walletAmount'] = $user->wallet_amount;
    //     }

    //     if ($user_type == 'admin') {
    //         $branch_id = session('branch_id');
    //         $branch_id = ($branch_id == 'default') ? null : $branch_id;

    //         $fdQuery = FD::query();
    //         $invQuery = Investment::query();
    //         $loanQuery = Loan::query();

    //         if ($branch_id) {
    //             $fdQuery->whereHas('member', function ($q) use ($branch_id) {
    //                 $q->where('branch_id', $branch_id);
    //             });
    //             $invQuery->whereHas('member', function ($q) use ($branch_id) {
    //                 $q->where('branch_id', $branch_id);
    //             });
    //             $loanQuery->whereHas('borrower', function ($q) use ($branch_id) {
    //                 $q->where('branch_id', $branch_id);
    //             });
    //         }

    //         $data['totalFds'] = $fdQuery->count();
    //         $data['totalInvestments'] = $invQuery->count();
    //         $data['totalLoans'] = $loanQuery->count();

    //         // Amount totals
    //         $data['totalFdAmount'] = (float) $fdQuery->sum('fd_amount');
    //         $data['totalInvestmentAmount'] = (float) $invQuery->sum('investment_amount');
    //         $data['totalLoanAmount'] = (float) $loanQuery->sum('applied_amount');
    //     } else {
    //         $data['totalFds'] = FD::where('created_by', $user->id)->count();
    //         $data['totalInvestments'] = Investment::where('created_user_id', $user->id)->count();
    //         $data['totalLoans'] = Loan::where('created_user_id', $user->id)->count();

    //         // Amount totals (user scope)
    //         $data['totalFdAmount'] = (float) FD::where('created_by', $user->id)->sum('fd_amount');
    //         $data['totalInvestmentAmount'] = (float) Investment::where('created_user_id', $user->id)->sum('investment_amount');
    //         $data['totalLoanAmount'] = (float) Loan::where('created_user_id', $user->id)->sum('applied_amount');
    //     }

    //     return view("backend.dashboard-$user_type", $data);
     
    // }
    public function index()
{
    $user = auth()->user();
    $user_type = $user->user_type;
    $role_id = $user->role_id;
    $date = date('Y-m-d');
    $data = array();
    $data['activeBanners'] = LiveBanner::where('is_active', true)->latest('updated_at')->get();
    $data['activeTickers'] = DashboardTicker::where('is_active', true)->orderBy('id')->get();
    
    // Check if user should see admin dashboard (user_type = user but role_id = 36)
    $showAdminDashboard = ($user_type == 'user' && $role_id == 36) ? true : false;
    
    // For dashboard view selection
    $dashboardView = $user_type;
    if ($showAdminDashboard) {
        $dashboardView = 'admin'; // Show admin dashboard for role_id 36 users
    }
    
    if (in_array($user_type, ['user', 'customer'], true) && !$showAdminDashboard) {
        // Original user dashboard logic (for regular users)
        $data['due_repayments'] = collect();
        $data['total_leads'] = Order::where('user_id', $user->id)->count();
        $data['totalServices'] = Service::count();
        $data['walletAmount'] = $user->wallet_amount;
    } else {
        // Admin or special user (role_id 36) - show admin level data
        $data['total_leads'] = Order::count();
    }

    $data['walletAmount'] = $user->wallet_amount;

    if (in_array($user_type, ['user', 'customer'], true) && !$showAdminDashboard) {
        // Regular user data
        $memberId = $user->member?->id;
        $data['recent_transactions'] = $memberId
            ? Transaction::where('member_id', $memberId)
                ->limit(10)
                ->orderBy('trans_date', 'desc')
                ->get()
            : collect();

        $data['recent_wallet_transactions'] = WalletTransaction::with(['user', 'createdBy'])
            ->where('user_id', $user->id)
            ->latest('created_at')
            ->limit(10)
            ->get();

        $data['loans'] = $memberId
            ? Loan::where('status', 1)->where('borrower_id', $memberId)->get()
            : collect();
        $data['my_total_members'] = Member::withoutGlobalScope('branch_id')
            ->where('assigned_user_id', $user->id)
            ->count();
        $data['my_total_orders'] = Order::where('user_id', $user->id)->count();
    } else {
        // Admin level data (for both regular admin AND role_id=36 users)
        $branch_id = session('branch_id');
        $branch_id = ($branch_id == 'default') ? null : $branch_id;

        // Recent Transactions
        $transactionQuery = Transaction::limit('10')->orderBy('trans_date', 'desc');
        if ($branch_id) {
            $transactionQuery->whereHas('member', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            });
        }
        $data['recent_transactions'] = $transactionQuery->get();

        // Load latest 10 wallet transactions
        $walletQuery = WalletTransaction::with(['user', 'createdBy'])->latest('created_at')->limit(10);
        if ($branch_id) {
            $walletQuery->whereHas('user', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            });
        }
        $data['recent_wallet_transactions'] = $walletQuery->get();

        $data['due_repayments'] = LoanRepayment::selectRaw('loan_repayments.*, MAX(repayment_date) as repayment_date, COUNT(id) as total_due_repayment, SUM(amount_to_pay) as total_due')
            ->with('loan')
            ->whereHas('loan')
            ->whereRaw("repayment_date < '$date'")
            ->where('status', 0)
            ->groupBy('loan_id')
            ->get();

        $data['total_customer'] = $user_type === 'user'
            ? Member::withoutGlobalScope('branch_id')->where('assigned_user_id', $user->id)->count()
            : Member::count();

        $userQuery = User::whereNotIn('email', ['admin@gmail.com', 'bellway@gmail.com']);
        if ($branch_id) {
            $userQuery->where('branch_id', $branch_id);
        }
        $data['total_users'] = $userQuery->count();

        $data['total_members'] = Member::count();

        $orderQuery = Order::query();
        if ($branch_id) {
            $orderQuery->whereHas('user', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            });
        }
        $data['total_leads'] = $orderQuery->count();
        $data['total_orders'] = $orderQuery->count();

        $todayOrderQuery = Order::whereDate('created_at', $date);
        if ($branch_id) {
            $todayOrderQuery->whereHas('user', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            });
        }
        $data['todays_orders'] = $todayOrderQuery->count();

        $data['totalServices'] = Service::count();
        $data['walletAmount'] = $user->wallet_amount;
    }

    // FD, Investment, Loan data for admin-level users
    if ($user_type == 'admin' || $showAdminDashboard) {
        $branch_id = session('branch_id');
        $branch_id = ($branch_id == 'default') ? null : $branch_id;

        $fdQuery = FD::query();
        // The dashboard BCS total is the cumulative principal of approved
        // investments. Each contribution remains its own investment row.
        $invQuery = Investment::query()->where('status', 1);
        $loanQuery = Loan::query();

        if ($branch_id) {
            $fdQuery->whereHas('member', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            });
            $invQuery->whereHas('member', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            });
            $loanQuery->whereHas('borrower', function ($q) use ($branch_id) {
                $q->where('branch_id', $branch_id);
            });
        }

        $data['totalFds'] = $fdQuery->count();
        $data['totalInvestments'] = $invQuery->count();
        $data['totalLoans'] = $loanQuery->count();

        // Amount totals
        $data['totalFdAmount'] = (float) $fdQuery->sum('fd_amount');
        $data['totalInvestmentAmount'] = (float) $invQuery->sum('investment_amount');
        $data['totalLoanAmount'] = (float) $loanQuery->sum('applied_amount');
    } elseif ($user_type === 'customer') {
        // Client dashboard: show the approved principal belonging to this member.
        $memberId = $user->member?->id;
        $customerInvestments = Investment::where('customer_id', $memberId)->where('status', 1);
        $data['totalInvestments'] = (clone $customerInvestments)->count();
        $data['totalInvestmentAmount'] = (float) $customerInvestments->sum('investment_amount');

        // Keep the existing customer dashboard totals for other products.
        $data['totalFds'] = FD::where('created_by', $user->id)->count();
        $data['totalLoans'] = Loan::where('created_user_id', $user->id)->count();
        $data['totalFdAmount'] = (float) FD::where('created_by', $user->id)->sum('fd_amount');
        $data['totalLoanAmount'] = (float) Loan::where('created_user_id', $user->id)->sum('applied_amount');
    } else {
        // Regular user data (only their own approved investment records)
        $data['totalFds'] = FD::where('created_by', $user->id)->count();
        $userInvestments = Investment::where('created_user_id', $user->id)->where('status', 1);
        $data['totalInvestments'] = (clone $userInvestments)->count();
        $data['totalLoans'] = Loan::where('created_user_id', $user->id)->count();

        $data['totalFdAmount'] = (float) FD::where('created_by', $user->id)->sum('fd_amount');
        $data['totalInvestmentAmount'] = (float) $userInvestments->sum('investment_amount');
        $data['totalLoanAmount'] = (float) Loan::where('created_user_id', $user->id)->sum('applied_amount');
    }

    // Return appropriate dashboard view
    return view("backend.dashboard-$dashboardView", $data);
}

    public function indexnew()
    {
        // dd('hello');
    }

    public function total_customer_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function deposit_requests_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function withdraw_requests_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function loan_requests_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function expense_overview_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function deposit_withdraw_analytics()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function recent_transaction_widget()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function due_loan_list()
    {
        // Use for Permission Only
        return redirect()->route('dashboard.index');
    }

    public function json_expense_by_category()
    {
        $transactions = Expense::selectRaw('expense_category_id, IFNULL(SUM(amount), 0) as amount')
            ->with('expense_category')
            ->whereRaw('MONTH(expense_date) = ?', date('m'))
            ->whereRaw('YEAR(expense_date) = ?', date('Y'))
            ->groupBy('expense_category_id')
            ->get();
        $category = array();
        $colors = array();
        $amounts = array();
        $data = array();

        foreach ($transactions as $transaction) {
            array_push($category, $transaction->expense_category->name);
            array_push($colors, $transaction->expense_category->color);
            array_push($amounts, (float) $transaction->amount);
        }

        echo json_encode(array('amounts' => $amounts, 'category' => $category, 'colors' => $colors));
    }

    public function json_deposit_withdraw_analytics($currency_id)
    {
        $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $transactions = Transaction::whereHas('account.savings_type', function (Builder $query) use ($currency_id) {
            $query->where('currency_id', $currency_id);
        })
            ->selectRaw('MONTH(trans_date) as td, type, IFNULL(SUM(amount), 0) as amount')
            ->whereRaw("(type = 'Deposit' OR type = 'Withdraw') AND status = 2")
            ->whereRaw('YEAR(trans_date) = ?', date('Y'))
            ->groupBy('td', 'type')
            ->get();

        $deposit = array();
        $withdraw = array();

        foreach ($transactions as $transaction) {
            if ($transaction->type == 'Deposit') {
                $deposit[$transaction->td] = $transaction->amount;
            } else if ($transaction->type == 'Withdraw') {
                $withdraw[$transaction->td] = $transaction->amount;
            }
        }

        echo json_encode(array('month' => $months, 'deposit' => $deposit, 'withdraw' => $withdraw));
    }
}
