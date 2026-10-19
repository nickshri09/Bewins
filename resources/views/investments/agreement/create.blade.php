<form method="post" class="ajax-submit" autocomplete="off"
      action="{{ route('investment.agreement.store', $investmentId) }}" enctype="multipart/form-data">

    @csrf

    <div class="form-group">
        <label>Agreement Name</label>
        <input type="text" class="form-control" name="name" required>
    </div>

    <div class="form-group">
        <label>Upload Documents</label>
        <input type="file" class="form-control" name="documents[]" multiple required>
        <small>PDF/JPG/PNG/DOCX allowed</small>
    </div>

    <button type="submit" class="btn btn-primary">
        Upload
    </button>
</form>

