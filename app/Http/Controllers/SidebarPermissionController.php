<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\SidebarPermission;
use Illuminate\Http\Request;

class SidebarPermissionController extends Controller
{
    /**
     * All sidebar menu keys with their labels.
     * Grouped by section for the admin UI.
     */
    public static function getSidebarMenuMap(): array
    {
        return [
            // ── USER MANAGEMENT ───────────────────────────────────────
            [
                'section' => 'User Management Permissions',
                'section_key' => 'user_management',
                'items' => [
                    ['key' => 'user_management', 'label' => 'Show User Management Menu (Main Link)'],
                    ['key' => 'user_management.users', 'label' => 'Show All Users (sub)'],
                    ['key' => 'user_management.roles', 'label' => 'Show User Roles (sub)'],
                    ['key' => 'user_management.access_control', 'label' => 'Show Access Control (sub)'],
                    ['key' => 'user_management.wallets', 'label' => 'Show Wallets (sub)'],
                    ['key' => 'user_management.sidebar_permissions', 'label' => 'Show Sidebar Permissions (sub)'],
                ],
            ],
            // ── TAXATION ──────────────────────────────────────────────
            [
                'section' => 'Taxation Permissions',
                'section_key' => 'taxation',
                'items' => [
                    ['key' => 'taxation', 'label' => 'Show Taxation Menu (Main Link)'],
                    ['key' => 'taxation.orders', 'label' => 'Show Orders Menu'],
                    ['key' => 'taxation.service_list', 'label' => 'Show Service List Menu'],
                    ['key' => 'taxation.action_required', 'label' => 'Show Action Required Menu'],
                ],
            ],
            // ── FINANCE ───────────────────────────────────────────────
            [
                'section' => 'Finance Permissions',
                'section_key' => 'finance',
                'items' => [
                    ['key' => 'fixed_deposit', 'label' => 'Show Fixed Deposit Menu'],
                    ['key' => 'investment', 'label' => 'Show Investment Menu'],
                    ['key' => 'expected_returns', 'label' => 'Show Expected Returns Menu (Main Link)'],
                    ['key' => 'expected_returns.investment', 'label' => 'Show Investment Return (sub)'],
                    ['key' => 'expected_returns.fd', 'label' => 'Show FD Return (sub)'],
                    ['key' => 'expected_returns.loan', 'label' => 'Show Loan Return (sub)'],
                ],
            ],
            // ── LOANS ─────────────────────────────────────────────────
            [
                'section' => 'Loans Permissions',
                'section_key' => 'loans',
                'items' => [
                    ['key' => 'loans', 'label' => 'Show Loans Menu (Main Link)'],
                    ['key' => 'loans.all', 'label' => 'Show All Loans (sub)'],
                    ['key' => 'loans.pending', 'label' => 'Show Pending Loans (sub)'],
                    ['key' => 'loans.active', 'label' => 'Show Active Loans (sub)'],
                    ['key' => 'loans.calculator', 'label' => 'Show Loan Calculator (sub)'],
                    ['key' => 'loans.products', 'label' => 'Show Loan Products (sub)'],
                    ['key' => 'loans.custom_fields', 'label' => 'Show Loan Custom Fields (sub)'],
                ],
            ],
            // ── CLIENTS & ACCOUNTS ────────────────────────────────────
            [
                'section' => 'Clients & Accounts Permissions',
                'section_key' => 'clients_accounts',
                'items' => [
                    ['key' => 'branches', 'label' => 'Show Branches Menu'],
                    ['key' => 'clients', 'label' => 'Show Clients Menu (Main Link)'],
                    ['key' => 'clients.view', 'label' => 'Show View Clients (sub)'],
                    ['key' => 'clients.add', 'label' => 'Show Add Client (sub)'],
                    ['key' => 'clients.import', 'label' => 'Show Bulk Import (sub)'],
                    ['key' => 'accounts', 'label' => 'Show Accounts Menu (Main Link)'],
                    ['key' => 'accounts.all', 'label' => 'Show All Accounts (sub)'],
                    ['key' => 'accounts.interest', 'label' => 'Show Interest Calculation (sub)'],
                    ['key' => 'accounts.types', 'label' => 'Show Account Types (sub)'],
                ],
            ],
            // ── TRANSACTIONS ──────────────────────────────────────────
            [
                'section' => 'Transactions Permissions',
                'section_key' => 'transactions',
                'items' => [
                    ['key' => 'withdraw', 'label' => 'Show Withdraw Menu (Main Link)'],
                    ['key' => 'withdraw.approved', 'label' => 'Show Withdraw Approved List (sub)'],
                    ['key' => 'withdraw.requests', 'label' => 'Show Withdraw Requests (sub)'],
                    ['key' => 'expense', 'label' => 'Show Expense Menu (Main Link)'],
                    ['key' => 'expense.all', 'label' => 'Show All Expenses (sub)'],
                    ['key' => 'expense.categories', 'label' => 'Show Expense Categories (sub)'],
                ],
            ],
            // ── HR & REPORTS ──────────────────────────────────────────
            [
                'section' => 'HR & Reports Permissions',
                'section_key' => 'hr_reports',
                'items' => [
                    ['key' => 'employee_management', 'label' => 'Show Employee Management Menu (Main Link)'],
                    ['key' => 'employee_management.salaries', 'label' => 'Show All Salaries (sub)'],
                    ['key' => 'employee_management.attendance', 'label' => 'Show Attendance (sub)'],
                    ['key' => 'reports', 'label' => 'Show Reports Base Menu (Main Link)'],
                    ['key' => 'reports.account_statement', 'label' => 'Show Account Statement (sub)'],
                    ['key' => 'reports.account_balance', 'label' => 'Show Account Balance (sub)'],
                    ['key' => 'reports.loan_report', 'label' => 'Show Loan Report (sub)'],
                    ['key' => 'reports.loan_due', 'label' => 'Show Loan Due Report (sub)'],
                    ['key' => 'reports.transactions', 'label' => 'Show Transaction Report (sub)'],
                    ['key' => 'reports.expense', 'label' => 'Show Expense Report (sub)'],
                    ['key' => 'reports.revenue', 'label' => 'Show Revenue Report (sub)'],
                ],
            ],
            // ── CONTENT & SETTINGS ────────────────────────────────────
            [
                'section' => 'Content & Settings Permissions',
                'section_key' => 'content_settings',
                'items' => [
                    ['key' => 'knowledge_base', 'label' => 'Show Knowledge Base Menu (Main Link)'],
                    ['key' => 'knowledge_base.all', 'label' => 'Show All Knowledge Base (sub)'],
                    ['key' => 'knowledge_base.view', 'label' => 'Show Knowledge Base (sub)'],
                    ['key' => 'pricing_plans', 'label' => 'Show Pricing Plans Menu (Main Link)'],
                    ['key' => 'pricing_plans.all', 'label' => 'Show All Plans (sub)'],
                    ['key' => 'pricing_plans.add', 'label' => 'Show Add New Plan (sub)'],
                    ['key' => 'notifications', 'label' => 'Show Notifications Menu'],
                    ['key' => 'system_settings', 'label' => 'Show System Settings Menu (Main Link)'],
                    ['key' => 'system_settings.general', 'label' => 'Show General Settings (sub)'],
                    ['key' => 'system_settings.currency', 'label' => 'Show Supported Currency (sub)'],
                    ['key' => 'system_settings.notifications', 'label' => 'Show Notification Templates (sub)'],
                    ['key' => 'system_settings.backup', 'label' => 'Show Database Backup (sub)'],
                ],
            ],
        ];
    }

    /**
     * Show the sidebar permission form for a given role.
     */
    public function index($role_id = null)
    {
        // Accept role_id from URL path (/sidebar-permissions/3)
        // OR from GET query string (?role_id=3) — form submits as query string
        if (!$role_id) {
            $role_id = request()->input('role_id');
        }

        $roles = Role::orderBy('name')->get();
        $menuMap = self::getSidebarMenuMap();

        $assignedKeys = [];
        if ($role_id) {
            $assignedKeys = SidebarPermission::getKeysForRole((int) $role_id);
        }

        return view('backend.sidebar-permissions.index', compact(
            'roles',
            'menuMap',
            'assignedKeys',
            'role_id'
        ));
    }

    /**
     * Save sidebar permissions for a role.
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'role_id' => 'required|integer',
        ]);

        $roleId = (int) $request->role_id;
        $selectedKeys = $request->input('menu_keys', []);  // array of checked keys

        // Delete old entries for this role
        SidebarPermission::where('role_id', $roleId)->delete();

        // Insert new ones
        foreach ($selectedKeys as $key) {
            SidebarPermission::create([
                'role_id' => $roleId,
                'menu_key' => $key,
            ]);
        }

        return redirect()
            ->route('sidebar.permissions.index', ['role_id' => $roleId])
            ->with('success', 'Sidebar permissions saved successfully!');
    }
}
