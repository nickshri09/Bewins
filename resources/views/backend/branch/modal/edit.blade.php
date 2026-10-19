<form method="post" class="ajax-screen-submit" autocomplete="off" action="{{ route('branches.update', $id) }}" enctype="multipart/form-data">
	{{ csrf_field()}}
	<input name="_method" type="hidden" value="PATCH">

	<div class="row px-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Branch Name') }}</label>
				<input type="text" class="form-control" name="name" value="{{ $branch->name }}" required>
			</div>
		</div>

		

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('District') }}</label>
				<input type="text" class="form-control" name="district" value="{{ $branch->district }}">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('State') }}</label>
				<input type="text" class="form-control" name="state" value="{{ $branch->state }}">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Pincode') }}</label>
				<input type="text" class="form-control" name="pincode" value="{{ $branch->pincode }}">
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Contact Email') }}</label>
				<input type="text" class="form-control" name="contact_email" value="{{ $branch->contact_email }}">
			</div>
		</div>

		<div class="col-md-6">
			<div class="form-group">
				<label class="control-label">{{ _lang('Contact Phone') }}</label>
				<input type="text" class="form-control" name="contact_phone" value="{{ $branch->contact_phone }}">
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Branch Image') }}</label>
				<input type="file" class="form-control" name="branch_image" accept="image/*">
				@if($branch->branch_image)
					<div class="mt-2">
						<img src="{{ asset('public/uploads/media/' . $branch->branch_image) }}" alt="Branch Image" class="thumb-md img-thumbnail" style="max-height: 80px;">
					</div>
				@endif
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Upload Agreement (PDF / Multiple Images)') }}</label>
				<input type="file" class="form-control" name="agreements[]" multiple accept=".pdf,image/*">
				@if($branch->agreements)
					@php $files = json_decode($branch->agreements, true) ?? []; @endphp
					@if(!empty($files))
						<div class="mt-2">
							<label class="font-weight-bold">{{ _lang('Uploaded Agreements') }}:</label>
							<ul class="list-group">
								@foreach($files as $file)
									<li class="list-group-item d-flex justify-content-between align-items-center py-1">
										<a href="{{ asset('public/uploads/media/' . $file) }}" target="_blank">
											<i class="ti-file"></i> {{ $file }}
										</a>
									</li>
								@endforeach
							</ul>
						</div>
					@endif
				@endif
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Address') }}</label>
				<textarea class="form-control" name="address">{{ $branch->address }}</textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Descriptions') }}</label>
				<textarea class="form-control" name="descriptions">{{ $branch->descriptions }}</textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<button type="submit" class="btn btn-primary"><i class="ti-check-box"></i>&nbsp;{{ _lang('Update') }}</button>
			</div>
		</div>
	</div>
</form>