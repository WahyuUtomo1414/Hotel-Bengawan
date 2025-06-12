var $ = jQuery.noConflict();
var RecordId = '';

$(function () {
	"use strict";
	
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});	
	
	$("#submit-form-midtrans").on("click", function () {
        $("#midtrans_formId").submit();
    });
	
});

function onListPanel() {
	$('.parsley-error-list').hide();
	
    $('#list-panel').show();
    $('.btn-list').hide();
    $('#form-panel-'+RecordId).hide();
}

function onEditPanel() {
    $('#list-panel').hide();
    $('.btn-list').show();	
    $('#form-panel-'+RecordId).show();	
}

function showPerslyError() {
    $('.parsley-error-list').show();
}

function onEdit(id) {
	RecordId = id;
	var msg = TEXT["Do you really want to edit this record"];
	onCustomModal(msg, "onEditData");	
}

function onEditData() {
	onEditPanel();
}

jQuery('#midtrans_formId').parsley({
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
            
        console.log(isFormValid);
        if (isFormValid) {
            onConfirmWhenAddEditForMidtrans();
            return false;
        }
        }
    }
});

function onConfirmWhenAddEditForMidtrans() {
    $.ajax({
		type : 'POST',
		url: base_url + '/backend/MidtransSettingsUpdate',
		data: $('#midtrans_formId').serialize(),
		success: function (response) {			
			var msgType = response.msgType;
			var msg = response.msg;

			if (msgType == "success") {
				onSuccessMsg(msg);
			} else {
				onErrorMsg(msg);
			}
		}
	});
}



