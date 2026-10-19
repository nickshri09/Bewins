<!-- Register New Client Modal -->
<div class="modal fade" id="addClientModal" tabindex="-1" role="dialog" aria-labelledby="addClientModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addClientModalLabel">Register New Client</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="newClientForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>First Name <span class="text-danger">*</span></strong></label>
                                <input type="text" name="first_name" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Last Name <span class="text-danger">*</span></strong></label>
                                <input type="text" name="last_name" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Email <span class="text-danger">*</span></strong></label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Mobile <span class="text-danger">*</span></strong></label>
                                <input type="text" name="mobile" class="form-control" required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>PAN Number <span class="text-danger">*</span></strong></label>
                                <div class="input-group">
                                    <input type="text" name="pan_number" class="form-control" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-primary" type="button" id="verifyPan">Verify PAN</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Address <span class="text-danger">*</span></strong></label>
                                <textarea name="address" class="form-control" required></textarea>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="saveClient">Save Client</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const saveClientBtn = document.getElementById('saveClient');
    const newClientForm = document.getElementById('newClientForm');
    const memberSelect = document.querySelector('select[name="member_id"]');

    saveClientBtn.addEventListener('click', function() {
        const formData = new FormData(newClientForm);
        
        fetch('/api/members', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Add new option to select
                const option = new Option(
                    `${data.member.first_name} ${data.member.last_name} | ${data.member.email} | ${data.member.mobile}`,
                    data.member.id
                );
                memberSelect.add(option);
                memberSelect.value = data.member.id;
                
                // Close modal
                $('#addClientModal').modal('hide');
                
                // Show success message
                alert('Client added successfully!');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding client. Please try again.');
        });
    });
});
</script>
@endpush 