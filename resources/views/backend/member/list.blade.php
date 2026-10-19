@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('Client List') }}</span>

				<div class="ml-auto">
					<a class="btn btn-dark btn-xs" href="{{ route('members.import') }}"><i class="ti-import mr-1"></i>{{ _lang('Bulk Import') }}</a>
					<a class="btn btn-primary btn-xs" href="{{ route('members.create') }}"><i class="ti-plus mr-1"></i>{{ _lang('Add New') }}</a>
				</div>
				
			</div>
			<div class="card-body">
				<table id="members_table" class="table table-bordered">
					<thead>
					    <tr>
							<th class="text-center">{{ _lang('Photo') }}</th>
							<th>{{ _lang('Client No') }}</th>
							<th>{{ _lang('Referral Code') }}</th>
						    <th>{{ _lang('First Name') }}</th>
							<th>{{ _lang('Last Name') }}</th>
							<th>{{ _lang('Email') }}</th>
							<th>{{ _lang('Branch') }}</th>
							<th class="text-right">{{ _lang('Wallet Balance') }}</th>
							<th class="text-center">{{ _lang('Allow Withdrawal') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@endsection

@section('js-script')
<script>
(function ($) {
	"use strict";

	var membersTable = $('#members_table').DataTable({
		processing: true,
		serverSide: true,
		ajax: '{{ url('admin/members/get_table_data') }}',
		"columns" : [
			{ data : 'photo', name : 'photo' },
			{ data : 'member_no', name : 'member_no' },
			{ data : 'referral_code', name : 'referral_code' },
			{ data : 'first_name', name : 'first_name' },
			{ data : 'last_name', name : 'last_name' },
			{ data : 'email', name : 'email' },
			{ data : 'branch.name', name : 'branch.name' },
			{ data : 'wallet_balance', name : 'member_wallets.balance', className: 'text-right' },
			{ data : 'allow_withdrawal', name : 'allow_withdrawal', className: 'text-center' },
			{ data : "action", name : "action" },
		],
		responsive: true,
		"bStateSave": true,
		"bAutoWidth":false,
		"ordering": false,
		"language": {
		   "decimal":        "",
		   "emptyTable":     "{{ _lang('No Data Found') }}",
		   "info":           "{{ _lang('Showing') }} _START_ {{ _lang('to') }} _END_ {{ _lang('of') }} _TOTAL_ {{ _lang('Entries') }}",
		   "infoEmpty":      "{{ _lang('Showing 0 To 0 Of 0 Entries') }}",
		   "infoFiltered":   "(filtered from _MAX_ total entries)",
		   "infoPostFix":    "",
		   "thousands":      ",",
		   "lengthMenu":     "{{ _lang('Show') }} _MENU_ {{ _lang('Entries') }}",
		   "loadingRecords": "{{ _lang('Loading...') }}",
		   "processing":     "{{ _lang('Processing...') }}",
		   "search":         "{{ _lang('Search') }}",
		   "zeroRecords":    "{{ _lang('No matching records found') }}",
		   "paginate": {
			  "first":      "{{ _lang('First') }}",
			  "last":       "{{ _lang('Last') }}",
			  "previous": "<i class='ti-angle-left'></i>",
        	  "next" : "<i class='ti-angle-right'></i>",
		  }
		},
		drawCallback: function () {
			$(".dataTables_paginate > .pagination").addClass("pagination-bordered");
		}
	});

	$(document).on('change', '.toggle-withdrawal', function() {
		var $toggle = $(this);
		var status = $toggle.prop('checked') ? 1 : 0;
		var previousStatus = status ? 0 : 1;
		var member_id = $toggle.data('id');
		$toggle.prop('disabled', true);

		$.ajax({
			type: "POST",
			url: "{{ url('admin/members/toggle_withdrawal') }}/" + member_id,
			dataType: 'json',
			headers: { 'Accept': 'application/json' },
			data: {
				_token: "{{ csrf_token() }}",
				status: status
			},
			success: function(response) {
				if (response.result == 'success') {
					var isEnabled = response.allow_withdrawal;
					$('.toggle-withdrawal[data-id="' + member_id + '"]').prop('checked', isEnabled).prop('disabled', false);
					// Reload the row data so Responsive child rows do not retain stale switch markup.
					membersTable.ajax.reload(null, false);

					$.toast({ text: response.message, icon: 'success', position: 'top-right' });
				} else {
					$('.toggle-withdrawal[data-id="' + member_id + '"]').prop('checked', previousStatus).prop('disabled', false);
					$.toast({ text: response.message || "{{ _lang('Unable to update withdrawal setting') }}", icon: 'error', position: 'top-right' });
				}
			},
			error: function(xhr) {
				$('.toggle-withdrawal[data-id="' + member_id + '"]').prop('checked', previousStatus).prop('disabled', false);
				var message = xhr.responseJSON && xhr.responseJSON.message
					? xhr.responseJSON.message
					: "{{ _lang('Error occurred while updating status') }}";
				$.toast({ text: message, icon: 'error', position: 'top-right' });
			}
		});
	});

})(jQuery);
</script>
@endsection
