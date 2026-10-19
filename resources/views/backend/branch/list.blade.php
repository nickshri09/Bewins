@extends('layouts.app')

@section('content')

<div class="row">
	<div class="col-lg-12">
		<div class="card no-export">
		    <div class="card-header d-flex align-items-center">
				<span class="panel-title">{{ _lang('All Branch') }}</span>

				<div class="d-flex align-items-center ml-auto mr-2">
					<label class="mr-2 mb-0" style="font-weight:600; white-space: nowrap;">{{ _lang('Sort By') }}:</label>
					<select id="branch_sort" class="form-control form-control-sm" style="width: 220px;"
						onchange="window.location.href='{{ route('branches.index') }}?sort=' + this.value;">
						<option value="default" {{ ($sort ?? 'default') == 'default' ? 'selected' : '' }}>{{ _lang('Default (Newest First)') }}</option>
						<option value="az" {{ ($sort ?? '') == 'az' ? 'selected' : '' }}>{{ _lang('Branch Name (A → Z)') }}</option>
						<option value="za" {{ ($sort ?? '') == 'za' ? 'selected' : '' }}>{{ _lang('Branch Name (Z → A)') }}</option>
					</select>
				</div>

				<a class="btn btn-primary btn-xs ajax-modal" data-title="{{ _lang('Add New Branch') }}" href="{{ route('branches.create') }}"><i class="ti-plus"></i>&nbsp;{{ _lang('Add New') }}</a>
			</div>
			<div class="card-body">

				<table id="branches_table" class="table table-bordered data-table">
					<thead>
					    <tr>
						    <th>{{ _lang('S.No') }}</th>
						    <th class="text-center">{{ _lang('Image') }}</th>
						    <th>{{ _lang('Branch Name') }}</th>
						    <th>{{ _lang('District') }}</th>
						    <th>{{ _lang('State') }}</th>
						    <th>{{ _lang('Pincode') }}</th>
							<th>{{ _lang('Contact Email') }}</th>
							<th>{{ _lang('Contact Phone') }}</th>
							<th class="text-center">{{ _lang('Action') }}</th>
					    </tr>
					</thead>
					<tbody>
					    @foreach($branchs as $branch)
					    <tr data-id="row_{{ $branch->id }}">
							<td>{{ $loop->iteration }}</td>
							<td class="text-center">
								@if($branch->branch_image)
									<img src="{{ asset('public/uploads/media/' . $branch->branch_image) }}" class="thumb-sm img-thumbnail" style="max-height: 40px; width: 40px; object-fit: cover; border-radius: 4px;">
								@else
									<img src="{{ asset('public/backend/images/avatar.png') }}" class="thumb-sm img-thumbnail" style="max-height: 40px; width: 40px; object-fit: cover; border-radius: 4px;">
								@endif
							</td>
							<td class='name'>{{ $branch->name }}</td>
							<td class='district'>{{ $branch->district }}</td>
							<td class='state'>{{ $branch->state }}</td>
							<td class='pincode'>{{ $branch->pincode }}</td>
							<td class='contact_email'>{{ $branch->contact_email }}</td>
							<td class='contact_phone'>{{ $branch->contact_phone }}</td>

							<td class="text-center">
								<span class="dropdown">
								  <button class="btn btn-primary dropdown-toggle btn-xs" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								  {{ _lang('Action') }}
								  </button>
								  <form action="{{ route('branches.destroy', $branch['id']) }}" method="post">
									{{ csrf_field() }}
									<input name="_method" type="hidden" value="DELETE">

									<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
										<a href="{{ route('branches.edit', $branch['id']) }}" data-title="{{ _lang('Update Branch') }}" class="dropdown-item dropdown-edit ajax-modal"><i class="ti-pencil-alt"></i>&nbsp;{{ _lang('Edit') }}</a>
										<a href="{{ route('branches.show', $branch['id']) }}" data-title="{{ _lang('Branch Details') }}" class="dropdown-item dropdown-view ajax-modal"><i class="ti-eye"></i>&nbsp;{{ _lang('View') }}</a>
										<button class="btn-remove dropdown-item" type="submit"><i class="ti-trash"></i>&nbsp;{{ _lang('Delete') }}</button>
									</div>
								  </form>
								</span>
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