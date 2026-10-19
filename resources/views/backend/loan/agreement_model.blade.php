<form method="post" class="ajax-submit" autocomplete="off"
      action="{{ route('loan.agreement.store', $loanId) }}" enctype="multipart/form-data">

	{{ csrf_field() }}

	<div class="row px-2">

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Agreement Name') }}</label>
				<input type="text" class="form-control" name="name"
					   value="{{ old('name') }}" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Upload Documents') }}</label>
				<input type="file" class="form-control" name="documents[]"
					   multiple required>
				<small class="text-muted">You can upload multiple documents (PDF/JPG/PNG/DOCX)</small>
			</div>
		</div>

		<div class="col-md-12">
		    <div class="form-group">
			    <button type="submit" class="btn btn-primary">
			        <i class="ti-check-box"></i>&nbsp;{{ _lang('Submit') }}
			    </button>
		    </div>
		</div>

	</div>
</form>
