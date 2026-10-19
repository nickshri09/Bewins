<form method="post" class="wallet-ajax-submit" autocomplete="off" action="{{ route('members.store_wallet_money', $member->id) }}">
	{{ csrf_field() }}
	<div class="row p-2">
		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Amount') }}</label>						
				<input type="text" class="form-control float-field" name="amount" value="" required>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Payment Method') }}</label>						
				<select class="form-control select2" name="method" id="payment_method" required>
					<option value="manual">{{ _lang('Add Manual') }}</option>
					<option value="razorpay">{{ _lang('Add by Razorpay') }}</option>
				</select>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<label class="control-label">{{ _lang('Note') }}</label>						
				<textarea class="form-control" name="note"></textarea>
			</div>
		</div>

		<div class="col-md-12">
			<div class="form-group">
				<button type="submit" class="btn btn-primary btn-block"><i class="ti-check-box"></i>&nbsp;{{ _lang('Add Money') }}</button>
			</div>
		</div>
	</div>
</form>

<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
$(document).off('submit', '.wallet-ajax-submit').on('submit', '.wallet-ajax-submit', function(e){
    e.preventDefault();
    var form = $(this);
    var url = form.attr('action');
    var formData = new FormData(this);

    $.ajax({
        url: url,
        method: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        beforeSend: function() {
            $("#preloader").css("display", "block");
            form.find('button[type="submit"]').prop('disabled', true);
        },
        success: function(data) {
            $("#preloader").css("display", "none");
            form.find('button[type="submit"]').prop('disabled', false);
            
            // Check if it's a string (sometimes global handlers might have parsed it or not)
            var json = (typeof data === 'string') ? JSON.parse(data) : data;

            if(json.result == 'success'){
                if(json.action == 'razorpay'){
                    var options = {
                        "key": json.razorpay_key,
                        "amount": json.amount * 100,
                        "currency": "INR",
                        "name": "Bewins",
                        "description": json.note || 'Wallet Deposit',
                        "handler": function (response){
                            $.ajax({
                                url: '{{ route('members.store_wallet_money', $member->id) }}',
                                method: 'POST',
                                data: {
                                    _token: '{{ csrf_token() }}',
                                    amount: json.amount,
                                    method: 'manual', 
                                    note: 'Razorpay Payment ID: ' + response.razorpay_payment_id + '. ' + (json.note || '')
                                },
                                beforeSend: function() {
                                    $("#preloader").css("display", "block");
                                },
                                success: function(res){
                                    $("#preloader").css("display", "none");
                                    var finalJson = (typeof res === 'string') ? JSON.parse(res) : res;
                                    if(finalJson.result == 'success'){
                                        $('#main_modal').modal('hide');
                                        if($('#wallet_transactions_table').length){
                                            $('#wallet_transactions_table').DataTable().ajax.reload();
                                        }else{
                                            window.location.reload();
                                        }
                                        $.toast({
                                            text: finalJson.message,
                                            showHideTransition: "slide",
                                            icon: "success",
                                            position: "top-right",
                                        });
                                    }
                                }
                            });
                        },
                        "prefill": {
                            "name": "{{ $member->first_name }} {{ $member->last_name }}",
                            "email": "{{ $member->email }}",
                            "contact": "{{ $member->mobile }}"
                        },
                        "theme": {
                            "color": "#3399cc"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.open();
                } else {
                    // Manual success
                    $('#main_modal').modal('hide');
                    if($('#wallet_transactions_table').length){
                        $('#wallet_transactions_table').DataTable().ajax.reload();
                    }else{
                        window.location.reload();
                    }
                    $.toast({
                        text: json.message,
                        showHideTransition: "slide",
                        icon: "success",
                        position: "top-right",
                    });
                }
            } else {
                // Error
                var errorMsg = Array.isArray(json.message) ? json.message.join('<br>') : json.message;
                $.toast({
                    text: errorMsg,
                    showHideTransition: "slide",
                    icon: "error",
                    position: "top-right",
                });
            }
        },
        error: function(xhr) {
            $("#preloader").css("display", "none");
            form.find('button[type="submit"]').prop('disabled', false);
            $.toast({
                text: 'Something went wrong!',
                showHideTransition: "slide",
                icon: "error",
                position: "top-right",
            });
        }
    });
});

$('.select2').select2({
    dropdownParent: $('#main_modal')
});

$(".float-field").on("keypress", function (event) {
    if ((event.which != 46 || $(this).val().indexOf(".") != -1) && (event.which < 48 || event.which > 57)) {
        event.preventDefault();
    }
});
</script>
