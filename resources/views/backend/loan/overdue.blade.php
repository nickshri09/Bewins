@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
			<div class="card-header d-flex align-items-center">
				<span class="panel-title">
					<i class="fas fa-exclamation-triangle text-danger mr-1"></i>
					{{ _lang('Overdue Loans') }}
				</span>
			</div>

			<div class="card-body">
				<table id="overdue_loans_table" class="table table-bordered">
					<thead>
						<tr>
							<th>{{ _lang('Loan ID') }}</th>
							<th>{{ _lang('Loan Product') }}</th>
							<th>{{ _lang('Borrower') }}</th>
							<th>{{ _lang('Member No') }}</th>
							<th>{{ _lang('Release Date') }}</th>
							<th>{{ _lang('Applied Amount') }}</th>
							<th>{{ _lang('Overdue Amount') }}</th>
							<th>{{ _lang('Overdue Since') }}</th>
							<th>{{ _lang('Status') }}</th>
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
<script src="{{ asset('backend/assets/js/datatables/overdue_loans.js?v=1.0') }}"></script>
@endsection

