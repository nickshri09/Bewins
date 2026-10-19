@extends('layouts.app')

@section('content')
    @include('backend.live_banners.partials.dashboard-banner')
    @include('backend.dashboard_tickers.partials.ticker')
    <div class="row">
        <!-- Card 1 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card primary-card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Total Users') }}</h5>
                            <h4 class="pt-1 mb-0"><b>{{ $total_users ?? 0 }}</b></h4>
                        </div>
                        @php
                            $filterBranch =
                                request('branch_id') ?: (session('branch_id') == 'default' ? '' : session('branch_id'));
                            $branchParam = $filterBranch ? '?branch_id=' . $filterBranch : '';
                        @endphp
                        <a href="{{ route('users.index') . $branchParam }}">
                            <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card success-card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Total Client') }}</h5>
                            <h4 class="pt-1 mb-0"><b>{{ $total_customer }}</b></h4>
                        </div>
                        <a href="{{ route('members.index') }}">
                            <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card warning-card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Withdraw Requests') }}</h5>
                            <h4 class="pt-1 mb-0"><b>{{ request_count('withdraw_requests') }}</b></h4>
                        </div>
                        <a href="{{ route('withdraw_requests.index') }}">
                            <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card dashboard-card" style="background-color: #962086;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Pending Loans') }}</h5>
                            <h4 class="pt-1 mb-0"><b>{{ request_count('pending_loans') }}</b></h4>
                        </div>
                        <a href="{{ route('loans.index') }}">
                            <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 5 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="leads-light-card" style="background-color:#add8ec; padding:14px; height:100px; border-radius:10px;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="leads-title">{{ _lang('Total Leads') }}</span>
                    <a href="{{ route('orders.index') . $branchParam }}" class="text-dark text-decoration-none">
                        <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                    </a>
                </div>
                <h2 class="leads-count">{{ $total_orders ?? 0 }}</h2>
            </div>
        </div>

        <!-- Card 6 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="leads-light-card"
                style="background-color:#8659f05d; padding:14px; height:100px; border-radius:10px;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="leads-title">{{ _lang('Todays Total Order') }}</span>
                    <a href="{{ route('orders.index') . $branchParam }}" class="text-dark text-decoration-none">
                        <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                    </a>
                </div>
                <h2 class="leads-count">{{ $todays_orders ?? 0 }}</h2>
            </div>
        </div>

        <!-- Card 7 -->
        <!--<div class="col-xl-4 col-md-6 mb-4">-->
        <!--    <div class="card success-card dashboard-card">-->
        <!--        <div class="card-body">-->
        <!--            <div class="d-flex justify-content-between align-items-center">-->
        <!--                <div>-->
        <!--                    <h5>{{ _lang('Total FDs') }}</h5>-->
        <!--                    <h4 class="pt-1 mb-0"><b>{{ $totalFds ?? 0 }}</b></h4>-->
        <!--                    <div class="mt-1 small">-->
        <!--                        {{ _lang('Total Amount') }}:-->
        <!--                        <b>₹{{ number_format($totalFdAmount ?? 0, 2) }}</b>-->
        <!--                    </div>-->
        <!--                </div>-->
        <!--                <a href="{{ route('fd.index') }}">-->
        <!--                    <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}-->
        <!--                </a>-->
        <!--            </div>-->
        <!--        </div>-->
        <!--    </div>-->
        <!--</div>-->

        <!-- Card 8 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card primary-card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Cumulative BCS Investment') }}</h5>
                            <h4 class="pt-1 mb-0"><b>{{ $totalInvestments ?? 0 }}</b></h4>
                            <div class="mt-1 small">
                                {{ _lang('Total Amount') }}:
                                <b>₹{{ number_format($totalInvestmentAmount ?? 0, 2) }}</b>
                            </div>
                        </div>
                        <a href="{{ route('investments.index') }}">
                            <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 9 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card danger-card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Total Loans') }}</h5>
                            <h4 class="pt-1 mb-0"><b>{{ $totalLoans ?? 0 }}</b></h4>
                            <div class="mt-1 small">
                                {{ _lang('Total Amount') }}:
                                <b>₹{{ number_format($totalLoanAmount ?? 0, 2) }}</b>
                            </div>
                        </div>
                        <a href="{{ route('loans.index') }}">
                            <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

{{-- <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-5 mb-4">

    <!-- Card 1 - Total Users -->
    <div class="group bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
        <div class="p-4 h-28 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 bg-purple-200 rounded-lg flex items-center justify-center">
                            <i class="ri-user-line text-purple-700 text-sm"></i>
                        </div>
                        <p class="text-purple-700 text-xs font-semibold uppercase tracking-wide">{{ _lang('Total Users') }}</p>
                    </div>
                    <h3 class="text-purple-900 text-2xl font-bold">{{ $total_users ?? 0 }}</h3>
                </div>
                <div class="bg-purple-200/50 rounded-lg px-2 py-1">
                    <span class="text-purple-700 text-xs font-medium">+12%</span>
                </div>
            </div>
            @php
                $filterBranch = request('branch_id') ?: (session('branch_id') == 'default' ? '' : session('branch_id'));
                $branchParam = $filterBranch ? '?branch_id=' . $filterBranch : '';
            @endphp
            <a href="{{ route('users.index') . $branchParam }}" class="inline-flex items-center gap-1 text-purple-600 hover:text-purple-800 text-xs font-semibold transition-all group-hover:gap-2">
                {{ _lang('View Details') }}
                <i class="ri-arrow-right-line text-sm"></i>
            </a>
        </div>
    </div>

    <!-- Card 2 - Total Client -->
    <div class="group bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
        <div class="p-4 h-28 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 bg-emerald-200 rounded-lg flex items-center justify-center">
                            <i class="ri-group-line text-emerald-700 text-sm"></i>
                        </div>
                        <p class="text-emerald-700 text-xs font-semibold uppercase tracking-wide">{{ _lang('Total Client') }}</p>
                    </div>
                    <h3 class="text-emerald-900 text-2xl font-bold">{{ $total_customer }}</h3>
                </div>
                <div class="bg-emerald-200/50 rounded-lg px-2 py-1">
                    <span class="text-emerald-700 text-xs font-medium">{{ _lang('Active') }}</span>
                </div>
            </div>
            <a href="{{ route('members.index') }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-xs font-semibold transition-all group-hover:gap-2">
                {{ _lang('View Details') }}
                <i class="ri-arrow-right-line text-sm"></i>
            </a>
        </div>
    </div>

    <!-- Card 3 - Withdraw Requests -->
    <div class="group bg-gradient-to-br from-amber-50 to-amber-100 border border-amber-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
        <div class="p-4 h-28 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 bg-amber-200 rounded-lg flex items-center justify-center">
                            <i class="ri-wallet-line text-amber-700 text-sm"></i>
                        </div>
                        <p class="text-amber-700 text-xs font-semibold uppercase tracking-wide">{{ _lang('Withdraw Requests') }}</p>
                    </div>
                    <h3 class="text-amber-900 text-2xl font-bold">{{ request_count('withdraw_requests') }}</h3>
                </div>
                <div class="bg-amber-200/50 rounded-lg px-2 py-1">
                    <span class="text-amber-700 text-xs font-medium">{{ _lang('Pending') }}</span>
                </div>
            </div>
            <a href="{{ route('withdraw_requests.index') }}" class="inline-flex items-center gap-1 text-amber-600 hover:text-amber-800 text-xs font-semibold transition-all group-hover:gap-2">
                {{ _lang('View Details') }}
                <i class="ri-arrow-right-line text-sm"></i>
            </a>
        </div>
    </div>

    <!-- Card 4 - Pending Loans -->
    <div class="group bg-gradient-to-br from-indigo-50 to-indigo-100 border border-indigo-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
        <div class="p-4 h-28 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 bg-indigo-200 rounded-lg flex items-center justify-center">
                            <i class="ri-bank-line text-indigo-700 text-sm"></i>
                        </div>
                        <p class="text-indigo-700 text-xs font-semibold uppercase tracking-wide">{{ _lang('Pending Loans') }}</p>
                    </div>
                    <h3 class="text-indigo-900 text-2xl font-bold">{{ request_count('pending_loans') }}</h3>
                </div>
                <div class="bg-indigo-200/50 rounded-lg px-2 py-1">
                    <span class="text-indigo-700 text-xs font-medium">{{ _lang('Review') }}</span>
                </div>
            </div>
            <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1 text-indigo-600 hover:text-indigo-800 text-xs font-semibold transition-all group-hover:gap-2">
                {{ _lang('View Details') }}
                <i class="ri-arrow-right-line text-sm"></i>
            </a>
        </div>
    </div>

    <!-- Card 5 - Total Leads -->
    <div class="group bg-gradient-to-br from-sky-50 to-sky-100 border border-sky-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
        <div class="p-4 h-28 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 bg-sky-200 rounded-lg flex items-center justify-center">
                            <i class="ri-line-chart-line text-sky-700 text-sm"></i>
                        </div>
                        <p class="text-sky-700 text-xs font-semibold uppercase tracking-wide">{{ _lang('Total Leads') }}</p>
                    </div>
                    <h3 class="text-sky-900 text-2xl font-bold">{{ $total_orders ?? 0 }}</h3>
                </div>
                <div class="bg-sky-200/50 rounded-lg px-2 py-1">
                    <span class="text-sky-700 text-xs font-medium">{{ _lang('New') }}</span>
                </div>
            </div>
            <a href="{{ route('orders.index') . $branchParam }}" class="inline-flex items-center gap-1 text-sky-600 hover:text-sky-800 text-xs font-semibold transition-all group-hover:gap-2">
                {{ _lang('View Details') }}
                <i class="ri-arrow-right-line text-sm"></i>
            </a>
        </div>
    </div>

    <!-- Card 6 - Today's Total Order -->
    <div class="group bg-gradient-to-br from-teal-50 to-teal-100 border border-teal-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
        <div class="p-4 h-28 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 bg-teal-200 rounded-lg flex items-center justify-center">
                            <i class="ri-calendar-todo-line text-teal-700 text-sm"></i>
                        </div>
                        <p class="text-teal-700 text-xs font-semibold uppercase tracking-wide">{{ _lang("Today's Total Order") }}</p>
                    </div>
                    <h3 class="text-teal-900 text-2xl font-bold">{{ $todays_orders ?? 0 }}</h3>
                </div>
                <div class="bg-teal-200/50 rounded-lg px-2 py-1">
                    <span class="text-teal-700 text-xs font-medium">{{ _lang('Today') }}</span>
                </div>
            </div>
            <a href="{{ route('orders.index') . $branchParam }}" class="inline-flex items-center gap-1 text-teal-600 hover:text-teal-800 text-xs font-semibold transition-all group-hover:gap-2">
                {{ _lang('View Details') }}
                <i class="ri-arrow-right-line text-sm"></i>
            </a>
        </div>
    </div>

    <!-- Card 7 - Total FDs -->
    <!--<div class="group bg-gradient-to-br from-emerald-50 to-emerald-100 border border-emerald-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">-->
    <!--    <div class="p-4 h-28 flex flex-col justify-between">-->
    <!--        <div class="flex justify-between items-start">-->
    <!--            <div class="flex-1">-->
    <!--                <div class="flex items-center gap-2 mb-1">-->
    <!--                    <div class="w-6 h-6 bg-emerald-200 rounded-lg flex items-center justify-center">-->
    <!--                        <i class="ri-pie-chart-2-line text-emerald-700 text-sm"></i>-->
    <!--                    </div>-->
    <!--                    <p class="text-emerald-700 text-xs font-semibold uppercase tracking-wide">{{ _lang('Total FDs') }}</p>-->
    <!--                </div>-->
    <!--                <div class="flex items-baseline gap-2">-->
    <!--                    <h3 class="text-emerald-900 text-2xl font-bold">{{ $totalFds ?? 0 }}</h3>-->
    <!--                    <span class="text-emerald-600 text-xs">FDs</span>-->
    <!--                </div>-->
    <!--                <p class="text-emerald-700 text-xs mt-0.5">{{ _lang('Total Amount') }}: <span class="font-semibold">₹{{ number_format($totalFdAmount ?? 0, 2) }}</span></p>-->
    <!--            </div>-->
    <!--            <div class="bg-emerald-200/50 rounded-lg px-2 py-1">-->
    <!--                <span class="text-emerald-700 text-xs font-medium">{{ _lang('Active') }}</span>-->
    <!--            </div>-->
    <!--        </div>-->
    <!--        <a href="{{ route('fd.index') }}" class="inline-flex items-center gap-1 text-emerald-600 hover:text-emerald-800 text-xs font-semibold transition-all group-hover:gap-2">-->
    <!--            {{ _lang('View Details') }}-->
    <!--            <i class="ri-arrow-right-line text-sm"></i>-->
    <!--        </a>-->
    <!--    </div>-->
    <!--</div>-->

    <!-- Card 8 - Total Investments -->
    <div class="group bg-gradient-to-br from-violet-50 to-violet-100 border border-violet-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
        <div class="p-4 h-28 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 bg-violet-200 rounded-lg flex items-center justify-center">
                            <i class="ri-funds-line text-violet-700 text-sm"></i>
                        </div>
                        <p class="text-violet-700 text-xs font-semibold uppercase tracking-wide">{{ _lang('Total Investments') }}</p>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-violet-900 text-2xl font-bold">{{ $totalInvestments ?? 0 }}</h3>
                        <span class="text-violet-600 text-xs">Investments</span>
                    </div>
                    <p class="text-violet-700 text-xs mt-0.5">{{ _lang('Total Amount') }}: <span class="font-semibold">₹{{ number_format($totalInvestmentAmount ?? 0, 2) }}</span></p>
                </div>
                <div class="bg-violet-200/50 rounded-lg px-2 py-1">
                    <span class="text-violet-700 text-xs font-medium">{{ _lang('Portfolio') }}</span>
                </div>
            </div>
            <a href="{{ route('investments.index') }}" class="inline-flex items-center gap-1 text-violet-600 hover:text-violet-800 text-xs font-semibold transition-all group-hover:gap-2">
                {{ _lang('View Details') }}
                <i class="ri-arrow-right-line text-sm"></i>
            </a>
        </div>
    </div>

    <!-- Card 9 - Total Loans -->
    <div class="group bg-gradient-to-br from-rose-50 to-rose-100 border border-rose-200 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 overflow-hidden">
        <div class="p-4 h-28 flex flex-col justify-between">
            <div class="flex justify-between items-start">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <div class="w-6 h-6 bg-rose-200 rounded-lg flex items-center justify-center">
                            <i class="ri-hand-coin-line text-rose-700 text-sm"></i>
                        </div>
                        <p class="text-rose-700 text-xs font-semibold uppercase tracking-wide">{{ _lang('Total Loans') }}</p>
                    </div>
                    <div class="flex items-baseline gap-2">
                        <h3 class="text-rose-900 text-2xl font-bold">{{ $totalLoans ?? 0 }}</h3>
                        <span class="text-rose-600 text-xs">Loans</span>
                    </div>
                    <p class="text-rose-700 text-xs mt-0.5">{{ _lang('Total Amount') }}: <span class="font-semibold">₹{{ number_format($totalLoanAmount ?? 0, 2) }}</span></p>
                </div>
                <div class="bg-rose-200/50 rounded-lg px-2 py-1">
                    <span class="text-rose-700 text-xs font-medium">{{ _lang('Active') }}</span>
                </div>
            </div>
            <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1 text-rose-600 hover:text-rose-800 text-xs font-semibold transition-all group-hover:gap-2">
                {{ _lang('View Details') }}
                <i class="ri-arrow-right-line text-sm"></i>
            </a>
        </div>
    </div>

</div> --}}

    <div class="row">
        <!-- Portfolio Overview Chart -->
        <div class="col-lg-6 col-md-12">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ _lang('Portfolio Overview') }}</h6>
                </div>
                <div class="card-body">
                    {{-- Portfolio Chart Hidden --}}
                    {{-- <div style="height: 300px;">
                        <canvas id="portfolioChart"></canvas>
                    </div> --}}
                    <div class="mt-3 small">
                        <div class="d-flex flex-wrap justify-content-center" style="gap: 12px;">
                            <!--<div class="text-nowrap">-->
                            <!--    <span-->
                            <!--        style="display:inline-block;width:10px;height:10px;background:#FFC107;border-radius:2px;margin-right:6px;"></span>-->
                            <!--    {{ _lang('Total FD') }}:-->
                            <!--    <b>₹{{ number_format($totalFdAmount ?? 0, 2) }}</b>-->
                            <!--    <span class="text-muted">({{ $totalFds ?? 0 }})</span>-->
                            <!--</div>-->
                            <div class="text-nowrap">
                                <span
                                    style="display:inline-block;width:10px;height:10px;background:#17a2b8;border-radius:2px;margin-right:6px;"></span>
                                {{ _lang('Bewin Collateral Security') }}:
                                <b>₹{{ number_format($totalInvestmentAmount ?? 0, 2) }}</b>
                                <span class="text-muted">({{ $totalInvestments ?? 0 }})</span>
                            </div>
                            <div class="text-nowrap">
                                <span
                                    style="display:inline-block;width:10px;height:10px;background:#dc3545;border-radius:2px;margin-right:6px;"></span>
                                {{ _lang('Total Loan') }}:
                                <b>₹{{ number_format($totalLoanAmount ?? 0, 2) }}</b>
                                <span class="text-muted">({{ $totalLoans ?? 0 }})</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Business Stats Chart -->
        <div class="col-lg-6 col-md-12">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-success">{{ _lang('Business Growth') }}</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="businessStatsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>


    <div class="row">
        <div class="col-md-12 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    {{ _lang('Due Loan Payments') }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th class="text-nowrap">{{ _lang('Loan ID') }}</th>
                                    <th class="text-nowrap">{{ _lang('Member No') }}</th>
                                    <th class="text-nowrap">{{ _lang('Member') }}</th>
                                    <th class="text-nowrap">{{ _lang('Last Payment Date') }}</th>
                                    <th class="text-nowrap">{{ _lang('Due Repayments') }}</th>
                                    <th class="text-nowrap text-right">{{ _lang('Total Due') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (count($due_repayments) == 0)
                                    <tr>
                                        <td colspan="5">
                                            <h6 class="text-center">{{ _lang('No Active Loan Available') }}</h6>
                                        </td>
                                    </tr>
                                @endif

                                @foreach ($due_repayments as $repayment)
                                    <tr>
                                        <td>{{ $repayment->loan->loan_id }}</td>
                                        <td>{{ $repayment->loan->borrower->member_no }}</td>
                                        <td>{{ $repayment->loan->borrower->name }}</td>
                                        <td class="text-nowrap">{{ $repayment->repayment_date }}</td>
                                        <td class="text-nowrap">{{ $repayment->total_due_repayment }}</td>
                                        <td class="text-nowrap text-right">
                                            {{ decimalPlace($repayment->total_due, currency($repayment->loan->currency->name)) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>



    {{-- <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-header">
                    {{ _lang('Recent Wallet Transactions') }}
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>{{ _lang('Date') }}</th>
                                    <th>{{ _lang('User') }}</th>
                                    <th>{{ _lang('Amount') }}</th>
                                    <th class="text-nowrap">{{ _lang('Type') }}</th>
                                    <th>{{ _lang('Note') }}</th>
                                    <th>{{ _lang('Added By') }}</th>
                                    <th class="text-center">{{ _lang('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recent_wallet_transactions as $transaction)
                                    @php
                                        $isCredit = $transaction->type === 'credit';
                                        $symbol = $isCredit ? '+' : '-';
                                        $class = $isCredit ? 'text-success' : 'text-danger';
                                    @endphp
                                    <tr>
                                        <td class="text-nowrap">
                                            <!--{{ \Carbon\Carbon::parse($transaction->created_at) }}-->
                                            {{ \Carbon\Carbon::parse($transaction->created_at)->format('d M, Y H:i') }}


                                        </td>

                                        <td>
                                            {{ $transaction->user->name ?? 'Deleted User' }}
                                        </td>
                                        <td>
                                            <span class="text-nowrap {{ $class }}">
                                                {{ $symbol . ' ' . number_format($transaction->amount, 2) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($isCredit)
                                                <span class="badge badge-success">{{ _lang('Credit') }}</span>
                                            @else
                                                <span class="badge badge-danger">{{ _lang('Debit') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ Str::limit($transaction->note, 30, '...') }}
                                        </td>
                                        <td>
                                            {{ $transaction->createdBy->name ?? 'Unknown' }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('wallets.history') }}?user={{ $transaction->user->name ?? '' }}"
                                                class="btn btn-outline-primary btn-xs">
                                                <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            {{ _lang('No recent wallet transactions found.') }}</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection

@section('js-script')
    <script src="{{ asset('public/backend/plugins/chartJs/chart.min.js') }}"></script>
    <script src="{{ asset('public/backend/assets/js/dashboard.js?v=1.1') }}"></script>
    <script>
        (function($) {
            "use strict";

            // Portfolio Overview Chart (FD, Investment, Loan) - Amounts
            // Portfolio Overview Chart removed as per request


            // Business Growth Chart (Users, Clients, Services)
            if (document.getElementById('businessStatsChart')) {
                const ctxB = document.getElementById('businessStatsChart').getContext('2d');
                new Chart(ctxB, {
                    type: 'bar',
                    data: {
                        labels: ['{{ _lang('Total Users') }}', '{{ _lang('Total Clients') }}',
                            '{{ _lang('Services') }}'
                        ],
                        datasets: [{
                            label: '{{ _lang('Count') }}',
                            data: [{{ $total_users ?? 0 }}, {{ $total_customer ?? 0 }},
                                {{ $totalServices ?? 0 }}
                            ],
                            backgroundColor: [
                                'rgba(78, 115, 223, 0.7)', // Primary
                                'rgba(28, 200, 138, 0.7)', // Success
                                'rgba(54, 185, 204, 0.7)' // Info
                            ],
                            borderColor: [
                                'rgba(78, 115, 223, 1)',
                                'rgba(28, 200, 138, 1)',
                                'rgba(54, 185, 204, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    borderDash: [2, 2]
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
            }
        })(jQuery);
    </script>
@endsection
