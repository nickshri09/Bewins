@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="header-title">{{ _lang('Wallet Transaction History') }}</h4>
                    <div class="d-flex gap-2">
                        <button class="btn btn-primary" id="export_pdf">
                            <i class="fa fa-file-pdf-o mr-1"></i> {{ _lang('Export PDF') }}
                        </button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                {{-- Filters Section --}}
                <div class="row mb-4">
                    <div class="col-md-12">
                        <form id="filter_form" class="row g-3">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ _lang('Date Range') }}</label>
                                    <input type="text" class="form-control daterange" name="daterange" value="{{ request('daterange') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>{{ _lang('User') }}</label>
                                    <input type="text" class="form-control" name="user" placeholder="{{ _lang('Search User') }}" value="{{ request('user') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ _lang('Type') }}</label>
                                    <select class="form-control" name="type">
                                        <option value="">{{ _lang('All') }}</option>
                                        <option value="credit" {{ request('type') == 'credit' ? 'selected' : '' }}>{{ _lang('Credit') }}</option>
                                        <option value="debit" {{ request('type') == 'debit' ? 'selected' : '' }}>{{ _lang('Debit') }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>{{ _lang('Amount Range') }}</label>
                                    <input type="number" class="form-control" name="min_amount" placeholder="{{ _lang('Min Amount') }}" value="{{ request('min_amount') }}">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <input type="number" class="form-control" name="max_amount" placeholder="{{ _lang('Max Amount') }}" value="{{ request('max_amount') }}">
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search mr-1"></i> {{ _lang('Search') }}
                                </button>
                                <a href="{{ route('wallets.history') }}" class="btn btn-secondary">
                                    <i class="fa fa-refresh mr-1"></i> {{ _lang('Reset') }}
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered" id="transactions_table">
                        <thead>
                            <tr>
                                <th>{{ _lang('Date') }}</th>
                                <th>{{ _lang('User') }}</th>
                                <th>{{ _lang('Amount') }}</th>
                                <th>{{ _lang('Type') }}</th>
                                <th>{{ _lang('Note') }}</th>
                                <th>{{ _lang('Added By') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                            <tr>
                                <td>
                                 
                                       {{ \Carbon\Carbon::parse($transaction->created_at)->setTimezone('Asia/Kolkata')->format('d M, Y H:i') }}
                                        
                                </td>
                                <td>{{ $transaction->user->name ?? 'Deleted User' }}</td>
                                <td>{{ number_format($transaction->amount, 2) }}</td>
                                <td>
                                    @if($transaction->type == 'credit')
                                        <span class="badge badge-success">{{ _lang('Credit') }}</span>
                                    @else
                                        <span class="badge badge-danger">{{ _lang('Debit') }}</span>
                                    @endif
                                </td>
                                <td>{{ $transaction->note ?? '—' }}</td>
                                <td>{{ $transaction->createdBy->name ?? 'Unknown User' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ _lang('No transactions found.') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $transactions->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script')
<script>
$(document).ready(function() {
    // Initialize DateRangePicker
    $('.daterange').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });

    $('.daterange').on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('.daterange').on('cancel.daterangepicker', function(ev, picker) {
        $(this).val('');
    });

    // Export PDF
    $('#export_pdf').click(function() {
        var filters = $('#filter_form').serialize();
        window.location.href = "{{ route('wallets.history') }}?export=pdf&" + filters;
    });

    // Initialize DataTable (optional: disable if using pagination)
    $('#transactions_table').DataTable({
        "paging": false,
        "ordering": false, // Disable since we order in backend
        "info": false,
        "searching": false,
        "language": {
            "emptyTable": "{{ _lang('No Data Found') }}"
        }
    });
});
</script>
@endsection