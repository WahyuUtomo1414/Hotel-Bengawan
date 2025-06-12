var $ = jQuery.noConflict();
var payment_method = 1;
var total_amount = 0;

$(function () {
	"use strict";

	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	
    $("#new_account").on("click", function () {
		if($(this).is(":checked")){
			$("#new_account_pass").removeClass("hideclass");
			$("#password").attr("required", "");
			$("#password_confirmation").attr("required", "");
		}else if($(this).is(":not(:checked)")){
			$("#new_account_pass").addClass("hideclass");
			$("#password").removeAttr("required");
			$("#password_confirmation").removeAttr("required");
		}
    });
	
	
    $("#payment_method_bank").on("click", function () {
		$("#pay_bank").removeClass("hideclass");
    });

	$("#checkout_submit_form").on("click", function () {
		payment_method = $('input[name="payment_method"]:checked').val();
        $("#checkout_formid").submit();
    });
	
	$("#checkin_date").datetimepicker({
		format: 'yyyy-mm-dd',
		startDate: new Date(),
		autoclose: true,
		todayBtn: false,
		minView: 2
	});

	$("#checkout_date").datetimepicker({
		format: 'yyyy-mm-dd',
		startDate: new Date(),
		autoclose: true,
		todayBtn: false,
		minView: 2
	});
	
	$("#checkin_date").on("change", function () {
		onCheckOutTotalPrice();
	});
	
	$("#checkout_date").on("change", function () {
		onCheckOutTotalPrice();
	});
	
	$("#room").on("blur", function () {
		onCheckOutTotalPrice();
	});

	$("#person").on("blur", function () {
		onCheckOutTotalPrice();
	});

	$("#extra_bed").on("blur", function () {
		onCheckOutTotalPrice();
	});

});

function showPerslyError() {
    $('.parsley-error-list').show();
}

jQuery('#checkout_formid').parsley({
    listeners: {
        onFieldValidate: function (elem) {
            if (!$(elem).is(':visible')) {
                return true;
            }
            else {
                showPerslyError();
                return false;
            }
        },
        onFormSubmit: function (isFormValid, event) {
            if (isFormValid) {
				onConfirmMakeOrder();				
                return false;
            }
        }
    }
});

function onConfirmMakeOrder() {

	var payment_method = $('input[name="payment_method"]:checked').val();

	
	var checkout_btn = $('.checkout_btn').html();
	
    $.ajax({
		type : 'POST',
		url: base_url + '/frontend/send_booking_request',
		data: $('#checkout_formid').serialize(),
		beforeSend: function() {
			$('.checkout_btn').html('<span class="spinner-border spinner-border-sm"></span> Please Wait...');
		},
		success: function (response) {		
			var msgType = response.msgType;
			var msg = response.msg;

			if (msgType == "success") {
				$("#checkout_formid").find('span.error-text').text('');

				//Stripe
				if(payment_method == 3){
					if(isenable_stripe == 1){
						if(response.intent != ''){
							onConfirmPayment(response.intent, msg);
						}
					}
				
				//Paypal
				}else if(payment_method == 4){
					if(response.intent != ''){
						window.location.href = response.intent;
					}
				
				//Mollie
				}else if(payment_method == 6){
					if(response.intent != ''){
						window.location.href = response.intent;
					}
				}else{
					//onSuccessMsg(msg);
					window.location.href = base_url + '/thank';
				}

			} else {
				$.each(msg, function(prefix, val){
					if(prefix == 'oneError'){
						onErrorMsg(val[0]);
					}else{
						$('span.'+prefix+'_error').text(val[0]);
					}
				});
			}
			
			$('.checkout_btn').html(checkout_btn);
		}
	});
}

function onCheckOutTotalPrice() {
	var checkin = $("#checkin_date").val();
	var checkout = $("#checkout_date").val();
	var room = $("#room").val();
	var roomtypeid = $("#roomtype_id").val();
	var person = $("#person").val();
	var extrabed = $("#extra_bed").val();
	
	if((checkin == '') || (checkout == '') || (room == '')) {
		return;
	}

	if(parseInt(room) > parseInt(maxRoom)){
		onErrorMsg('This value should be lower than or equal to '+ maxRoom);
		return;
	}
	
    $.ajax({
		type : 'POST',
		url: base_url + '/frontend/getCheckOutTotalPrice',
		data:{checkin_date:checkin, checkout_date:checkout, total_room:room, roomtype_id:roomtypeid, extra_bed: extrabed, person: person},
		success: function (response) {
			var data = response;
			$("#TotalPrice").html(data.total_table);
			total_amount = data.total_amount;
		}
	});
}