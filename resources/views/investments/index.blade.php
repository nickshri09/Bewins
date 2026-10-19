@extends('layouts.app')

@section('content')
<style>
    .bcs-investment-page { width: 100%; }
    .bcs-investment-page .bcs-page-header {
        display: flex; align-items: center; justify-content: space-between; gap: 16px;
        margin: 0 0 24px; padding: 20px 24px; border-radius: 6px;
        background: linear-gradient(110deg, #273c75, #1e50d9); color: #fff;
    }
    .bcs-investment-page .bcs-page-header h2 { margin: 0; color: #fff; font-size: 24px; font-weight: 600; }
    .bcs-investment-page .bcs-header-actions { display: flex; align-items: center; justify-content: flex-end; gap: 12px; flex-wrap: wrap; }
    .bcs-investment-page .bcs-header-actions a { display: inline-flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 5px; font-size: 14px; font-weight: 500; text-decoration: none; }
    .bcs-investment-page .bcs-header-actions svg, .bcs-investment-page .bcs-row-actions svg { width: 16px; height: 16px; flex: 0 0 16px; }
    .bcs-investment-page .bcs-cash-link { background: rgba(255,255,255,.2); color: #fff; }
    .bcs-investment-page .bcs-add-link { background: #fff; color: #2563eb; }
    .bcs-investment-page .bcs-table-card { overflow: hidden; border: 1px solid #e2e5e9; border-radius: 7px; background: #fff; box-shadow: 0 2px 6px rgba(0,0,0,.08); }
    .bcs-investment-page .bcs-table-wrap { width: 100%; overflow-x: auto; }
    .bcs-investment-page table.bcs-investment-table { width: 100%; height: auto !important; min-width: 1680px; margin: 0; border-collapse: collapse; table-layout: fixed; }
    .bcs-investment-page .bcs-investment-table th:nth-child(1), .bcs-investment-page .bcs-investment-table td:nth-child(1) { width: 145px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(2), .bcs-investment-page .bcs-investment-table td:nth-child(2) { width: 125px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(3), .bcs-investment-page .bcs-investment-table td:nth-child(3) { width: 110px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(4), .bcs-investment-page .bcs-investment-table td:nth-child(4) { width: 100px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(5), .bcs-investment-page .bcs-investment-table td:nth-child(5) { width: 125px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(6), .bcs-investment-page .bcs-investment-table td:nth-child(6) { width: 85px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(7), .bcs-investment-page .bcs-investment-table td:nth-child(7) { width: 105px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(8), .bcs-investment-page .bcs-investment-table td:nth-child(8) { width: 160px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(9), .bcs-investment-page .bcs-investment-table td:nth-child(9) { width: 110px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(10), .bcs-investment-page .bcs-investment-table td:nth-child(10) { width: 100px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(11), .bcs-investment-page .bcs-investment-table td:nth-child(11) { width: 130px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(12), .bcs-investment-page .bcs-investment-table td:nth-child(12) { width: 180px; }
    .bcs-investment-page .bcs-investment-table th:nth-child(13), .bcs-investment-page .bcs-investment-table td:nth-child(13) { width: 205px; }
    .bcs-investment-page .bcs-investment-table th { padding: 13px 12px; border: 1px solid #e2e5e9; background: #f8f9fb; color: #687386; font-size: 12px; font-weight: 600; line-height: 1.35; text-align: left; text-transform: uppercase; vertical-align: middle; white-space: nowrap; }
    .bcs-investment-page .bcs-investment-table td { height: auto !important; padding: 13px 12px; border: 1px solid #e2e5e9; color: #555f70; font-size: 14px; line-height: 1.45; vertical-align: middle; }
    .bcs-investment-page .bcs-investment-table tbody, .bcs-investment-page .bcs-investment-table tbody tr { height: auto !important; }
    .bcs-investment-page .bcs-investment-table tbody tr:hover { background: #f8fafc; }
    .bcs-investment-page .bcs-investment-table .bcs-nowrap { white-space: nowrap; }
    .bcs-investment-page .bcs-customer-cell { overflow-wrap: anywhere; }
    .bcs-investment-page .bcs-status-badge { display: inline-block; padding: 5px 10px; border-radius: 14px; font-size: 12px; font-weight: 600; line-height: 1.2; white-space: nowrap; }
    .bcs-investment-page .bcs-status-closed { background: #4b5563; color: #fff; }
    .bcs-investment-page .bcs-status-pending { background: #fef3c7; color: #92400e; }
    .bcs-investment-page .bcs-status-approved { background: #dcfce7; color: #166534; }
    .bcs-investment-page .bcs-status-rejected { background: #fee2e2; color: #991b1b; }
    .bcs-investment-page .bcs-status-completed { background: #dbeafe; color: #1e40af; }
    .bcs-investment-page .bcs-status-withdrawn { background: #f3f4f6; color: #374151; }
    .bcs-investment-page .bcs-interest { color: #16a34a !important; font-weight: 600; white-space: nowrap; }
    .bcs-investment-page .bcs-tds { color: #dc2626 !important; font-weight: 600; white-space: nowrap; }
    .bcs-investment-page .bcs-total { color: #1d4ed8 !important; font-weight: 700; white-space: nowrap; }
    .bcs-investment-page .bcs-closure-content { display: flex; flex-direction: column; align-items: flex-start; gap: 5px; }
    .bcs-investment-page .bcs-closure-content a, .bcs-investment-page .bcs-row-actions a { color: #2563eb; font-size: 13px; }
    .bcs-investment-page .bcs-email-sent { color: #166534; background: #dcfce7; border-radius: 12px; padding: 3px 8px; font-size: 11px; font-weight: 600; }
    .bcs-investment-page .bcs-email-pending { color: #92400e; background: #fef3c7; border-radius: 12px; padding: 3px 8px; font-size: 11px; font-weight: 600; }
    .bcs-investment-page .bcs-email-action { padding: 0; border: 0; background: none; color: #b45309; font-size: 12px; cursor: pointer; }
    .bcs-investment-page .bcs-row-actions { display: flex; align-items: center; justify-content: center; gap: 9px; white-space: nowrap; }
    .bcs-investment-page .bcs-row-actions a, .bcs-investment-page .bcs-row-actions button { color: #2563eb; }
    .bcs-investment-page .bcs-row-actions a:nth-of-type(2) { color: #d97706; }
    .bcs-investment-page .bcs-row-actions button { color: #dc2626; border: 0; background: none; padding: 0; }
    .bcs-investment-page .bcs-row-actions form { margin: 0; }
    @media (max-width: 767px) {
        .bcs-investment-page .bcs-page-header { align-items: flex-start; flex-direction: column; padding: 16px; }
        .bcs-investment-page .bcs-page-header h2 { font-size: 20px; }
        .bcs-investment-page .bcs-header-actions { justify-content: flex-start; }
    }
</style>
<div class="bcs-investment-page">
 <!-- Header -->
        <div class="bcs-page-header">
            <h2 class="text-xl font-semibold text-white">{{ _lang('Bewin Collateral Security (BCS) List') }}</h2>
            <div class="bcs-header-actions">
                @if(Auth::user()->user_type == 'admin')
                    <a href="{{ route('investments.cash_collection') }}"
                       class="bcs-cash-link">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ _lang('Cash Collection Tracker') }}
                    </a>
                @endif
                <a href="{{ route('investments.create') }}"
                   class="bcs-add-link">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    {{ _lang('Add New') }}
                </a>
            </div>
        </div>

<div class="container-fluid py-4">


    <div class="bcs-table-card">


        <!-- Table -->
        <div class="bcs-table-wrap">
            <table class="bcs-investment-table">
                <thead>
                    <tr>
                        <th class="w-[145px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Investment Serial') }}</th>
                        <th class="w-[125px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Customer') }}</th>
                        <th class="w-[110px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('(BCS) Type') }}</th>
                        <th class="w-[100px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Amount') }}</th>
                        <th class="w-[125px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Net Interest') }}</th>
                        <th class="w-[85px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('TDS') }}</th>
                        <th class="w-[105px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Total') }}</th>
                        <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ _lang('currency') }}</th> -->
                        <th class="w-[160px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">Contribution Type</th>
                        <th class="w-[110px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Start Date') }}</th>
                        <!-- <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ _lang('Maturity Date') }}</th> -->
                        <th class="w-[100px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Status') }}</th>

                        @if(Auth::user()->user_type == 'admin')
                            <th class="w-[130px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Close Status') }}</th>
                            <th class="w-[180px] border-r border-gray-200 px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Closure Document') }}</th>
                        @endif
                        <th class="w-[205px] px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider whitespace-nowrap">{{ _lang('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($investments as $investment)
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="bcs-nowrap">{{ $investment->investment_code }}</td>
                            <td class="bcs-customer-cell">{{ $investment->customer->name ?? '-' }}</td>
                            <td>{{ ucfirst($investment->investment_type) }}</td>
                            <td class="bcs-nowrap">{{ decimalPlace($investment->investment_amount) }}</td>
                            <td class="bcs-interest">+ {{ decimalPlace($investment->total_net_interest ?? 0) }}</td>
                            @php
                                $totalNetInterest = (float) ($investment->total_net_interest ?? 0);
                                $totalTds = round($totalNetInterest * 0.10, 2);
                            @endphp
                            <td class="bcs-tds">- {{ decimalPlace($totalTds) }}</td>
                            <td class="bcs-total">{{ decimalPlace($investment->investment_amount + $totalNetInterest - $totalTds) }}</td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $investment->currency->name }}</td> -->
                            <td>{{ $investment->contribution_type }}</td>
                            <td class="bcs-nowrap">{{ \Carbon\Carbon::parse($investment->start_date)->format('Y-m-d') }}</td>
                            <!-- <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">{{ $investment->maturity_date ?? '-' }}</td> -->
                                <td class="status-cell" data-id="{{ $investment->id }}">
                                @if($investment->is_locked)
                                    <span class="status-badge bcs-status-badge bcs-status-closed">Closed</span>
                                @elseif ($investment->status == 0)
                                    <span class="status-badge bcs-status-badge bcs-status-pending">Pending</span>
                                @elseif($investment->status == 1)
                                    <span class="status-badge bcs-status-badge bcs-status-approved">Approved</span>
                                @elseif($investment->status == 2)
                                    <span class="status-badge bcs-status-badge bcs-status-rejected">Rejected</span>
                                @elseif($investment->status == 3)
                                    <span class="status-badge bcs-status-badge bcs-status-completed">Completed</span>
                                @elseif($investment->status == 4)
                                    <span class="status-badge bcs-status-badge bcs-status-withdrawn">Withdrawn</span>
                                @endif
                            </td>
                            @if(Auth::user()->user_type == 'admin')
                                <td class="px-4 py-3">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" class="custom-control-input lock-toggle" id="lock_{{ $investment->id }}" data-id="{{ $investment->id }}" data-status="{{ $investment->status }}" {{ $investment->is_locked ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="lock_{{ $investment->id }}"></label>
                                    </div>
                                </td>
                                <td class="closure-document-cell" data-id="{{ $investment->id }}"
                                    data-download-url="{{ route('investments.closure_certificate.download', $investment->id) }}"
                                    data-email-url="{{ route('investments.closure_certificate.resend', $investment->id) }}">
                                    @if($investment->is_locked)
                                        <div class="bcs-closure-content">
                                            <a href="{{ route('investments.closure_certificate.download', $investment->id) }}">Download Certificate (PDF)</a>
                                            @if($investment->closure_certificate_sent_at)
                                                <span class="bcs-email-sent">Email Sent</span>
                                                <span>{{ $investment->closure_certificate_sent_at->format('d M Y, h:i A') }}</span>
                                            @else
                                                <span class="bcs-email-pending">Email not sent</span>
                                            @endif
                                            <form action="{{ route('investments.closure_certificate.resend', $investment->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="bcs-email-action">
                                                    {{ $investment->closure_certificate_sent_at ? 'Resend Email' : 'Send Email' }}
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                            @endif
                                <td class="bcs-nowrap text-center">
                                    <div class="bcs-row-actions">
                                        <a href="{{ route('investments.show', $investment->id) }}"
                                           class="text-blue-600 hover:text-blue-900 inline-flex items-center">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            {{ _lang('View') }}
                                        </a>

                                        @if(Auth::user()->user_type == 'admin' || $investment->is_locked == 0)
                                            <a href="{{ route('investments.edit', $investment->id) }}"
                                               class="text-amber-600 hover:text-amber-900 inline-flex items-center border-l border-gray-200 pl-2">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                                {{ _lang('Edit') }}
                                            </a>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-500 border border-gray-200 cursor-not-allowed ml-2">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                                {{ _lang('Locked') }}
                                            </span>
                                        @endif

                                        @if(Auth::user()->user_type == 'admin')
                                            <form action="{{ route('investments.destroy', $investment->id) }}" method="POST" 
                                                  class="inline-flex items-center border-l border-gray-200 pl-2"
                                                  onsubmit="return confirm('Are you sure you want to delete this investment?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900 flex items-center">
                                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    {{ _lang('Delete') }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>
@endsection

@section('js-script')
    <script>
        $(document).ready(function() {
            // Filter by status
            $('select[name="status"]').on('change', function() {
                const val = $(this).val();
                window.location.href = "{{ route('investments.index') }}?status=" + val;
            });

            // Toggle Lock
            $('.lock-toggle').on('change', function() {
                const toggle = $(this);
                const id = toggle.data('id');
                const isChecked = toggle.is(':checked');
                const previousState = !isChecked;
                const statusCell = $('.status-cell[data-id="' + id + '"] .status-badge');
                const documentCell = $('.closure-document-cell[data-id="' + id + '"]');
                const previousDocumentHtml = documentCell.html();
                const statusMap = {
                    0: ['Pending', 'bcs-status-pending'],
                    1: ['Approved', 'bcs-status-approved'],
                    2: ['Rejected', 'bcs-status-rejected'],
                    3: ['Completed', 'bcs-status-completed'],
                    4: ['Withdrawn', 'bcs-status-withdrawn']
                };
                const updateStatusBadge = function(locked) {
                    const status = statusMap[toggle.data('status')] || statusMap[0];
                    statusCell.text(locked ? 'Closed' : status[0]);
                    statusCell.attr('class', 'status-badge bcs-status-badge ' +
                        (locked ? 'bcs-status-closed' : status[1]));
                };
                const updateClosureDocument = function(locked, emailSent, certificateAvailable) {
                    if (!locked) {
                        documentCell.html('<span class="text-muted">—</span>');
                        return;
                    }

                    const content = $('<div class="bcs-closure-content"></div>');
                    if (certificateAvailable) {
                        $('<a></a>')
                            .attr('href', documentCell.data('download-url'))
                            .addClass('bcs-certificate-link')
                            .text('Download Certificate (PDF)')
                            .appendTo(content);
                    }

                    $('<span></span>')
                        .addClass(emailSent ? 'bcs-email-sent' : 'bcs-email-pending')
                        .text(emailSent ? 'Email Sent' : 'Email not sent')
                        .appendTo(content);

                    const form = $('<form method="POST"></form>').attr('action', documentCell.data('email-url'));
                    $('<input type="hidden" name="_token">').val("{{ csrf_token() }}").appendTo(form);
                    $('<button type="submit" class="bcs-email-action"></button>')
                        .text(emailSent ? 'Resend Email' : 'Send Email')
                        .appendTo(form);
                    form.appendTo(content);
                    documentCell.empty().append(content);
                };

                // Update the status at once; the server response below confirms it.
                updateStatusBadge(isChecked);
                if (!isChecked) {
                    updateClosureDocument(false, false, false);
                }

                $.ajax({
                    url: "{{ url('admin/investments/toggle-lock') }}/" + id,
                    method: 'POST',
                    data: {
                        _token: "{{ csrf_token() }}",
                    },
                    success: function(response) {
                        if(response.status) {
                            updateClosureDocument(response.is_locked, response.email_sent === true, response.certificate_available === true);
                            if (response.is_locked && response.email_sent === false) {
                                Command: toastr["warning"](response.msg);
                            } else {
                                Command: toastr["success"](response.msg);
                            }
                        } else {
                            toggle.prop('checked', previousState);
                            updateStatusBadge(previousState);
                            documentCell.html(previousDocumentHtml);
                            alert(response.msg || 'Could not update the BCS status.');
                        }
                    },
                    error: function(xhr) {
                        toggle.prop('checked', previousState);
                        updateStatusBadge(previousState);
                        documentCell.html(previousDocumentHtml);
                        alert('Something went wrong');
                    }
                });
            });
        });
    </script>
@endsection
