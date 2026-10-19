@extends('layouts.app')

@section('content')
<div class="row">
	<div class="col-lg-12">
		<div class="card">
		    <div class="card-header">
				<span class="header-title">{{ _lang('Branch Details') }}</span>
			</div>
			
			<div class="card-body">
			    <table class="table table-bordered">
					<tr>
						<td>{{ _lang('Branch Image') }}</td>
						<td>
							@if($branch->branch_image)
								<img src="{{ asset('public/uploads/media/' . $branch->branch_image) }}" class="thumb-md img-thumbnail" style="max-height: 120px;">
							@else
								<span class="text-muted">{{ _lang('No Branch Image') }}</span>
							@endif
						</td>
					</tr>
				    <tr><td>{{ _lang('Branch Name') }}</td><td>{{ $branch->name }}</td></tr>
				    <tr><td>{{ _lang('District') }}</td><td>{{ $branch->district }}</td></tr>
				    <tr><td>{{ _lang('State') }}</td><td>{{ $branch->state }}</td></tr>
					<tr><td>{{ _lang('Pincode') }}</td><td>{{ $branch->pincode }}</td></tr>
					<tr><td>{{ _lang('Contact Email') }}</td><td>{{ $branch->contact_email }}</td></tr>
					<tr><td>{{ _lang('Contact Phone') }}</td><td>{{ $branch->contact_phone }}</td></tr>
					<tr><td>{{ _lang('Address') }}</td><td>{{ $branch->address }}</td></tr>
					<tr><td>{{ _lang('Descriptions') }}</td><td>{{ $branch->descriptions }}</td></tr>
					<tr>
						<td>{{ _lang('Agreements') }}</td>
						<td>
							@if($branch->agreements)
								@php $files = json_decode($branch->agreements, true) ?? []; @endphp
								@if(!empty($files))
									<ul class="list-unstyled mb-0">
										@foreach($files as $file)
											<li>
												<a href="{{ asset('public/uploads/media/' . $file) }}" target="_blank" class="btn btn-xs btn-outline-primary my-1">
													<i class="ti-download"></i> {{ $file }}
												</a>
											</li>
										@endforeach
									</ul>
								@else
									<span class="text-muted">{{ _lang('No Agreement Uploaded') }}</span>
								@endif
							@else
								<span class="text-muted">{{ _lang('No Agreement Uploaded') }}</span>
							@endif
						</td>
					</tr>
			    </table>
			</div>
	    </div>
	</div>
</div>
@endsection
