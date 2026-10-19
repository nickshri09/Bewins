@extends('layouts.app')

@section('content')
    @include('backend.live_banners.partials.dashboard-banner')
    @include('backend.dashboard_tickers.partials.ticker')
    @php $permissions = permission_list(); @endphp
    {{-- Compact Financial Overview Cards --}}

    <style>
        /* Compact Card Styles */
        .compact-card {
            transition: all 0.2s ease;
            border-radius: 8px;
            border: 1px solid #e9ecef !important;
        }

        .compact-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
            border-color: #dee2e6 !important;
        }

        /* Icon Wrapper */
        .icon-wrapper {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            transition: all 0.2s ease;
        }

        .compact-card:hover .icon-wrapper {
            transform: scale(1.05);
        }

        /* Icon Colors */
        .icon-primary {
            background-color: rgba(78, 115, 223, 0.1);
            color: #4e73df;
        }

        .icon-info {
            background-color: rgba(23, 162, 184, 0.1);
            color: #17a2b8;
        }

        .icon-danger {
            background-color: rgba(220, 53, 69, 0.1);
            color: #dc3545;
        }

        .icon-warning {
            background-color: rgba(255, 193, 7, 0.1);
            color: #ffc107;
        }

        .icon-success {
            background-color: rgba(28, 200, 138, 0.1);
            color: #1cc88a;
        }

        .icon-purple {
            background-color: rgba(111, 66, 193, 0.1);
            color: #6f42c1;
        }

        /* Table Styles */
        .table {
            font-size: 0.875rem;
        }

        .table th {
            font-weight: 500;
            border-bottom: 1px solid #f1f3f5 !important;
        }

        .table td {
            vertical-align: middle;
            border-bottom: 1px solid #f8f9fa;
        }

        /* Card Header */
        .card-header {
            background-color: #fafbfc;
        }

        /* Remove last border */
        .table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Link hover effects */
        .text-decoration-none:hover {
            text-decoration: underline !important;
        }

        /* Grid spacing - increased gap */
        .row.g-4>* {
            padding-right: calc(var(--bs-gutter-x) * 0.5);
            padding-left: calc(var(--bs-gutter-x) * 0.5);
            padding-top: var(--bs-gutter-y);
            padding-bottom: var(--bs-gutter-y);
        }

        /* Custom gap for better spacing */
        .row.g-4 {
            --bs-gutter-x: 1.5rem;
            --bs-gutter-y: 1.5rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .row.g-4 {
                --bs-gutter-x: 1rem;
                --bs-gutter-y: 1rem;
            }
        }
    </style>
    <div class="row">
        <!-- Card 1 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card success-card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Total Clients') }}</h5>
                            <h4 class="pt-1 mb-0"><b>{{ $my_total_members ?? 0 }}</b></h4>
                        </div>
                        <a href="{{ route('members.index') }}">
                            <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card primary-card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Total Orders') }}</h5>
                            <h4 class="pt-1 mb-0"><b>{{ $my_total_orders ?? 0 }}</b></h4>
                        </div>
                        <a href="{{ route('orders.index') }}">
                            <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

         <!-- Card 6 -->
         <div class="col-xl-4 col-md-6 mb-4">
            <div class="card dashboard-card" style="background-color: chocolate;">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Wallet') }}</h5>
                            <h4 class="pt-1 mb-0"><b>₹{{ number_format($walletAmount ?? 0, 0) }}</b></h4>
                        </div>
                        <a href="{{ route('walle.showwww') }}">
                            <i class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card danger-card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Total Loans') }}<b class="ml-2">{{ $totalLoans ?? 0 }}</b></h5>
                            <!-- <h4 class="pt-1 mb-0"><b>{{ $totalLoans ?? 0 }}</b></h4> -->
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

        <!-- Card 4 -->
        <!--<div class="col-xl-4 col-md-6 mb-4">-->
        <!--    <div class="card  dashboard-card" style="background-color:rgb(127, 147, 27);">-->
        <!--        <div class="card-body">-->
        <!--            <div class="d-flex justify-content-between align-items-center">-->
        <!--                <div>-->
        <!--                    <h5>{{ _lang('Total FDs') }} <b class="ml-2">{{ $totalFds ?? 0 }}</b></h5>-->
                            <!-- <h4 class="pt-1 mb-0"><b class="ml-2">{{ $totalFds ?? 0 }}</b></h4> -->
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

        <!-- Card 5 -->
        <div class="col-xl-4 col-md-6 mb-4">
            <div class="card primary-card dashboard-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5>{{ _lang('Cumulative BCS Investment') }} <b class="ml-2">{{ $totalInvestments ?? 0 }}</b></h5>
                            <!-- <h4 class="pt-1 mb-0"><b>{{ $totalInvestments ?? 0 }}</b></h4> -->
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


    </div>


    <div class="row">
        <!-- Portfolio Overview Chart -->
        <div class="col-lg-6 col-md-12 ">
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ _lang('Portfolio Overview') }}</h6>
                </div>
                <div class="card-body">
                    <div style="height: 300px;">
                        <canvas id="portfolioChart"></canvas>
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

    @if (in_array('dashboard.due_loan_list', $permissions))
        <div class="row">
            <div class="col-lg-12">
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
    @endif

    @if (in_array('dashboard.recent_transaction_widget', $permissions))
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-4">
                    <div class="card-header">
                        {{ _lang('Recent Transactions') }}
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>{{ _lang('Date') }}</th>
                                        <th>{{ _lang('Member') }}</th>
                                        <th class="text-nowrap">{{ _lang('Account Number') }}</th>
                                        <th>{{ _lang('Amount') }}</th>
                                        <th class="text-nowrap">{{ _lang('Debit/Credit') }}</th>
                                        <th>{{ _lang('Type') }}</th>
                                        <th>{{ _lang('Status') }}</th>
                                        <th class="text-center">{{ _lang('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($recent_transactions as $transaction)
                                        @php
                                            $symbol = $transaction->dr_cr == 'dr' ? '-' : '+';
                                            $class = $transaction->dr_cr == 'dr' ? 'text-danger' : 'text-success';
                                        @endphp
                                        <tr>
                                            <td class="text-nowrap">{{ $transaction->trans_date }}</td>
                                            <td>{{ $transaction->member->name }}</td>
                                            <td>{{ $transaction->account->account_number }}</td>
                                            <td><span
                                                    class="text-nowrap {{ $class }}">{{ $symbol . ' ' . decimalPlace($transaction->amount, currency($transaction->account->savings_type->currency->name)) }}</span>
                                            </td>
                                            <td>{{ strtoupper($transaction->dr_cr) }}</td>
                                            <td>{{ str_replace('_', ' ', $transaction->type) }}</td>
                                            <td>{!! xss_clean(transaction_status($transaction->status)) !!}</td>
                                            <td class="text-center"><a
                                                    href="{{ route('transactions.show', $transaction->id) }}"
                                                    target="_blank" class="btn btn-outline-primary btn-xs"><i
                                                        class="ti-arrow-right"></i>&nbsp;{{ _lang('View') }}</a></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@section('js-script')
    <!-- <script src="{{ asset('public/backend/plugins/chartJs/chart.min.js') }}"></script> -->
     <script src="{{ asset('backend/plugins/chartJs/chart.min.js') }}"></script>
    <!-- <script src="{{ asset('public/backend/assets/js/dashboard.js?v=1.1') }}"></script> -->
    <script src="{{ asset('backend/assets/js/dashboard.js?v=1.1') }}"></script>
    <script>
        (function($) {
            "use strict";

            // Portfolio Overview Chart (FD, Investment, Loan)
            if (document.getElementById('portfolioChart')) {
                const ctxP = document.getElementById('portfolioChart').getContext('2d');

                const fdCount = {{ $totalFds ?? 0 }};
                const investmentCount = {{ $totalInvestments ?? 0 }};
                const loanCount = {{ $totalLoans ?? 0 }};

                new Chart(ctxP, {
                    type: 'doughnut',
                    data: {
                        labels: [
                            'FDs - ' + fdCount,
                            'Investments - ' + investmentCount,
                            'Loans - ' + loanCount
                        ],
                        datasets: [{
                            data: [fdCount, investmentCount, loanCount],
                            backgroundColor: ['#FFC107', '#17a2b8', '#dc3545'],
                            borderWidth: 2,
                            borderColor: '#fff'
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: {
                                position: 'bottom',
                                labels: {
                                    usePointStyle: true,
                                    padding: 20,
                                    font: {
                                        size: 13,
                                        weight: 'bold'
                                    }
                                }
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const label = context.label || '';
                                        const value = context.parsed || 0;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = total > 0 ? ((value / total) * 100).toFixed(1) :
                                            0;
                                        return label + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    },
                    plugins: [{
                        id: 'centerText',
                        beforeDraw: function(chart) {
                            const width = chart.width;
                            const height = chart.height;
                            const ctx = chart.ctx;
                            ctx.restore();

                            const total = fdCount + investmentCount + loanCount;
                            const fontSize = (height / 150).toFixed(2);
                            ctx.font = "bold " + fontSize + "em sans-serif";
                            ctx.textBaseline = "middle";
                            ctx.fillStyle = "#666";

                            const text = "Total: " + total;
                            const textX = Math.round((width - ctx.measureText(text).width) / 2);
                            const textY = height / 2;

                            ctx.fillText(text, textX, textY);
                            ctx.save();
                        }
                    }]
                });
            }

            // Business Growth Chart (Clients, Leads, Services)
            if (document.getElementById('businessStatsChart')) {
                const ctxB = document.getElementById('businessStatsChart').getContext('2d');
                new Chart(ctxB, {
                    type: 'bar', // Using Bar chart for better comparison
                    data: {
                        labels: ['{{ _lang('Total Clients') }}', '{{ _lang('Total Leads') }}',
                            '{{ _lang('Services') }}'
                        ],
                        datasets: [{
                            label: '{{ _lang('Count') }}',
                            data: [{{ $my_total_members ?? 0 }}, {{ $total_leads ?? 0 }},
                                {{ $totalServices ?? 0 }}
                            ],
                            backgroundColor: [
                                'rgba(78, 115, 223, 0.7)', // Primary-ish
                                'rgba(28, 200, 138, 0.7)', // Success-ish
                                'rgba(54, 185, 204, 0.7)' // Info-ish
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
