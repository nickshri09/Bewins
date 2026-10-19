@php
    $deposit_requests = request_count('deposit_requests', true);
    $withdraw_requests = request_count('withdraw_requests', true);
    $member_requests = request_count('member_requests', true);
    $pending_loans = request_count('pending_loans', true);
    $user = auth()->user();
    $role = $user->role;
    $sidebarPerms = $user->getSidebarPermissions();
    $isAdmin = in_array('*', $sidebarPerms);
@endphp

{{-- Dashboard --}}
<li>
    <a href="{{ route('dashboard.index') }}"><i class="ti-dashboard"></i> <span>{{ _lang('Dashboard') }}</span></a>
</li>

<li>
    <a href="{{ route('my_activity.index') }}"><i class="fas fa-history"></i> <span>{{ _lang('My Activity') }}</span></a>
</li>

{{-- User Management --}}
@if ($isAdmin || in_array('user_management', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i class="ti-user"></i><span>{{ _lang('User Management') }}</span><span
                class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('user_management.users', $sidebarPerms))
                <li class="nav-item"><a class="nav-link" href="{{ route('users.index') }}">{{ _lang('All Users') }}</a>
                </li>
            @endif
          
          {{--
            @if ($isAdmin || in_array('user_management.roles', $sidebarPerms))
                <li class="nav-item"><a class="nav-link" href="{{ route('roles.index') }}">{{ _lang('User Roles') }}</a>
                </li>
            @endif
            @if ($isAdmin || in_array('user_management.access_control', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('permission.index') }}">{{ _lang('Access Control') }}</a></li>
            @endif
            --}}
            @if ($isAdmin || in_array('user_management.wallets', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('admin.wallets.index') }}">{{ _lang('Wallets') }}</a>
                </li>
            @endif
          {{--
            @if ($isAdmin || in_array('user_management.sidebar_permissions', $sidebarPerms))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('sidebar.permissions.index') }}">
                        <i class="fas fa-bars me-1" style="font-size:11px;"></i> {{ _lang('Sidebar Permissions') }}
                    </a>
                </li>
            @endif
            --}}
        </ul>
    </li>
@endif

{{-- Taxation --}}
@include('layouts.menus.static-menu')

{{-- Clients --}}
@if ($isAdmin || in_array('clients', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i class="fas fa-user-friends"></i><span>{{ _lang('Client') }}
                {!! xss_clean($member_requests) !!}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('clients.view', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('members.index') }}">{{ _lang('View Client') }}</a>
                </li>
            @endif

            @if ($isAdmin || in_array('clients.add', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('members.create') }}">{{ _lang('Add Client') }}</a>
                </li>
            @endif

            @if ($isAdmin || in_array('clients.import', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('members.import') }}">{{ _lang('Bulk Import') }}</a></li>
            @endif
            <li class="nav-item"><a class="nav-link" href="{{ route('commissions.mine') }}">{{ _lang('Referral & Commission') }}</a></li>
        </ul>
    </li>
@endif

{{-- Loans --}}
@if ($isAdmin || in_array('loans', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i class="fas fa-hand-holding-usd"></i><span>{{ _lang('Loans') }}
                {!! xss_clean($pending_loans) !!}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('loans.all', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('loans.index') }}">{{ _lang('All Loans') }}</a></li>
            @endif

            @if ($isAdmin || in_array('loans.pending', $sidebarPerms))
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('loans.filter', 'pending') }}">
                        {{ _lang('Pending Loans') }}
                        {!! xss_clean($pending_loans) !!}
                    </a>
                </li>
            @endif

            @if ($isAdmin || in_array('loans.active', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('loans.filter', 'active') }}">{{ _lang('Active Loans') }}</a></li>
            @endif

            <li class="nav-item">
                <a class="nav-link" href="{{ route('loans.overdue') }}">
                    <!-- <i class="fas fa-exclamation-triangle text-danger" style="font-size:11px;"></i> -->
                    {{ _lang('Overdue Loans') }}
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link" href="{{ route('loans.disbursement_queue') }}">
                    <!-- <i class="fas fa-money-check-alt"></i> -->
                    {{ _lang('Disbursement Queue') }}
                </a>
            </li>


            @if ($isAdmin || in_array('loans.calculator', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('loans.admin_calculator') }}">{{ _lang('Loan Calculator') }}</a></li>
            @endif

            @if ($isAdmin || in_array('loans.custom_fields', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('custom_fields.index', 'loans') }}">{{ _lang('Custom Fields') }}</a></li>
            @endif
        </ul>
    </li>
@endif

{{-- Fixed Deposit --}}
{{-- @if ($isAdmin || in_array('fixed_deposit', $sidebarPerms))
    <li>
        <a href="{{ route('fd.index') }}">
            <i class="fas fa-hand-holding-usd"></i><span>{{ _lang('Fixed Deposit') }}</span>
        </a>
    </li> 
@endif --}}

{{-- Investment --}}
@if ($isAdmin || in_array('investment', $sidebarPerms))
    <li>
        <a href="{{ route('investments.index') }}">
            <i class="fas fa-hand-holding-usd"></i><span>Bewin Collateral Security</span>
        </a>
    </li>
@endif


{{-- Expected Returns (Sub-menu shared keys) --}}
@if ($isAdmin || in_array('expected_returns', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i class="fas fa-percent"></i><span>{{ _lang('Expected Returns') }}</span><span
                class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('expected_returns.investment', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('investment.expected-return.index', ['type' => 'investment']) }}">{{ _lang('Bewin Institutional Collateral Security Return') }}</a>
                </li>
            @endif
            @if ($isAdmin || in_array('expected_returns.fd', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('investment.expected-return.index', ['type' => 'fd']) }}">{{ _lang('FD Return') }}</a>
                </li>
            @endif
            @if ($isAdmin || in_array('expected_returns.loan', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('investment.expected-return.index', ['type' => 'loan']) }}">{{ _lang('Loan Return') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif

{{-- Accounts --}}
@if ($isAdmin || in_array('accounts', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i class="fas fa-landmark"></i><span>{{ _lang('Accounts') }}</span><span
                class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('accounts.all', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('savings_accounts.index') }}">{{ _lang('All Accounts') }}</a></li>
            @endif

            @if ($isAdmin || in_array('accounts.interest', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('interest_calculation.calculator') }}">{{ _lang('Interest Calculation') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif

{{-- Withdraw --}}
@if ($isAdmin || in_array('withdraw', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i class="fas fa-money-check"></i><span>{{ _lang('Withdraw') }}
                {!! xss_clean($withdraw_requests) !!}</span><span class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('withdraw.requests', $sidebarPerms))
                <li class="nav-item"><a class="nav-link" href="{{ route('getrequest.index') }}">
                        {{ _lang('Withdraw Requests') }}
                        {!! xss_clean($withdraw_requests) !!}
                    </a></li>
            @endif
            @if ($isAdmin || in_array('withdraw.approved', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('withdraw.approvedlist') }}?type=withdraw">{{ _lang('Withdraw approved list') }}</a>
                </li>
            @endif
        </ul>
    </li>
@endif

{{-- Expense --}}
@if ($isAdmin || in_array('expense', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i class="fas fa-money-bill-wave"></i><span>{{ _lang('Expense') }}</span><span
                class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('expense.all', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('expenses.index') }}">{{ _lang('All Expense') }}</a>
                </li>
            @endif

            @if ($isAdmin || in_array('expense.categories', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('expense_categories.index') }}">{{ _lang('Expense Categories') }}</a></li>
            @endif
        </ul>
    </li>
@endif

{{-- Employee Management --}}
@if ($isAdmin || in_array('employee_management', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i
                class="fas fa-wallet"></i><span>{{ _lang('Employee Management') }}</span><span class="menu-arrow"><i
                    class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('employee_management.salaries', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('salaries.index') }}">{{ _lang('All Salaries') }}</a></li>
            @endif
            @if ($isAdmin || in_array('employee_management.attendance', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('attendances.index') }}">{{ _lang('Attendance') }}</a></li>
            @endif
        </ul>
    </li>
@endif

@if ($role && strtolower($role->name) === 'manager')
    <li>
        <a href="{{ route('users.my_agents') }}"><i class="ti-user"></i><span>{{ _lang('My Agents') }}</span></a>
    </li>
@endif

{{-- Reports --}}
@if ($isAdmin || in_array('reports', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i class="ti-bar-chart"></i><span>{{ _lang('Reports') }}</span><span
                class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('reports.account_statement', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('reports.account_statement') }}">{{ _lang('Account Statement') }}</a></li>
            @endif

            @if ($isAdmin || in_array('reports.account_balance', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('reports.account_balances') }}">{{ _lang('Account Balance') }}</a></li>
            @endif

            @if ($isAdmin || in_array('reports.loan_report', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('reports.loan_report') }}">{{ _lang('Loan Report') }}</a></li>
            @endif

            @if ($isAdmin || in_array('reports.loan_due', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('reports.loan_due_report') }}">{{ _lang('Loan Due Report') }}</a></li>
            @endif

            @if ($isAdmin || in_array('reports.transactions', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('reports.transactions_report') }}">{{ _lang('Transaction Report') }}</a></li>
            @endif

            @if ($isAdmin || in_array('reports.expense', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('reports.expense_report') }}">{{ _lang('Expense Report') }}</a></li>
            @endif

            @if ($isAdmin || in_array('reports.revenue', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('reports.revenue_report') }}">{{ _lang('Revenue Report') }}</a></li>
            @endif
        </ul>
    </li>
@endif

{{-- Knowledge Base --}}
@if ($isAdmin || in_array('knowledge_base', $sidebarPerms))
    <li>
        <a href="javascript: void(0);"><i class="fas fa-tags"></i><span>{{ _lang('knowledge Base') }}</span><span
                class="menu-arrow"><i class="mdi mdi-chevron-right"></i></span></a>
        <ul class="nav-second-level" aria-expanded="false">
            @if ($isAdmin || in_array('knowledge_base.all', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('admin.knowledge-base.index') }}">{{ _lang('All knowledge Base') }}</a></li>
            @endif
            @if ($isAdmin || in_array('knowledge_base.view', $sidebarPerms))
                <li class="nav-item"><a class="nav-link"
                        href="{{ route('knowledge.index') }}">{{ _lang('knowledge Base') }}</a></li>
            @endif
        </ul>
    </li>
@endif

{{-- Notifications --}}
@if ($isAdmin || in_array('notifications', $sidebarPerms))
    <li>
        <a href="{{ route('emails.index') }}">
            <i class="icon-bell"></i> Notifications
        </a>
    </li>
@endif
