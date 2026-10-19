@extends('layouts.app')

@section('content')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <span class="panel-title">{{ _lang('Employee Cash Collection Tracker') }}</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered data-table">
                        <thead>
                            <tr>
                                <th>{{ _lang('Collection Date') }}</th>
                                <th>{{ _lang('Employee (Collector)') }}</th>
                                <th>{{ _lang('Investor') }}</th>
                                <th class="text-right">{{ _lang('Amount') }}</th>
                                <th>{{ _lang('Admin Status') }}</th>
                                <th class="text-center">{{ _lang('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                            @forelse($collections as $row)
                            <tr>
                                <td>{{ \Carbon\Carbon::parse($row->paid_date)->format('d M Y, h:i A') }}</td>
                                <td>
                                    <strong>{{ $row->employee_name }}</strong><br>
                                    <small class="text-muted">{{ $row->employee_mobile }}</small>
                                </td>
                                <td>
                                    <strong>{{ $row->investor_name }}</strong><br>
                                    <small class="text-muted">{{ $row->investor_mobile }}</small>
                                </td>
                                <td class="text-right">
                                    {{ number_format($row->total_amount, 2) }}
                                </td>
                                <td>
                                    @if($row->admin_received_status == 1)
                                        <span class="badge bg-success text-white">{{ _lang('Received by Admin') }}</span>
                                        <br><small class="text-muted">{{ \Carbon\Carbon::parse($row->admin_received_at)->format('d M Y') }}</small>
                                    @else
                                        <span class="badge bg-warning text-dark">{{ _lang('Pending with Employee') }}</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($row->admin_received_status == 0)
                                        <button class="btn btn-primary btn-xs verify-cash-btn" data-id="{{ $row->id }}">
                                            <i class="ti-check"></i> {{ _lang('Confirm Receipt') }}
                                        </button>
                                    @else
                                        <button class="btn btn-outline-success btn-xs" disabled title="{{ _lang('Verified') }}">
                                            <i class="ti-check"></i>
                                        </button>
                                        <a href="{{ route('investments.download_receipt', $row->id) }}" class="btn btn-warning btn-xs" title="{{ _lang('Download Receipt') }}">
                                            <i class="ti-download"></i>
                                        </a>
                                    @endif
                                    <a href="{{ route('investments.show', $row->investment_id) }}" class="btn btn-info btn-xs" title="View Investment">
                                        <i class="ti-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">{{ _lang('No records found') }}</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js-script')
<script>
    $(document).on('click', '.verify-cash-btn', function() {
        let btn = $(this);
        let id = btn.data('id');

        if (confirm('{{ _lang("Are you sure you have received this cash from the employee?") }}')) {
            btn.prop('disabled', true).html('<i class="ti-timer"></i> Processing...');

            $.post("{{ url('admin/investments/verify-cash') }}/" + id, {
                _token: "{{ csrf_token() }}"
            }, function(res) {
                if (res.status) {
                    location.reload();
                } else {
                    alert(res.msg);
                    btn.prop('disabled', false).html('<i class="ti-check"></i> {{ _lang("Confirm Receipt") }}');
                }
            }).fail(function() {
                alert('Something went wrong!');
                btn.prop('disabled', false).html('<i class="ti-check"></i> {{ _lang("Confirm Receipt") }}');
            });
        }
    });
</script>
@endsection
