var pm_uploaded_video_id = 0;
var pm_ajax_msg_div = $('#manage-video-ajax-message');
var pm_accepted_video_file_types = /(\.|\/)(flv|mp4|mov|wmv|divx|avi|mkv|asf|wma|mp3|m4v|m4a|3gp|3g2)$/i;

/**
 * Submit multipart/form-data via ajax.
 * usage: $("#form").serializefiles();
 */
(function($) {
	$.fn.serializefiles = function() {
		var obj = $(this);
		/* ADD FILE TO PARAM AJAX */
		var formData = new FormData();
		$.each($(obj).find("input[type='file']"), function(i, tag) {
			$.each($(tag)[0].files, function(i, file) {
				formData.append(tag.name, file);
			});
		});
		var params = $(obj).serializeArray();
		$.each(params, function (i, val) {
			formData.append(val.name, val.value);
		});
		return formData;
	};
})(jQuery);

/**
 * Display message in the upload/edit video form
 *   
 * @param string message
 * @param string alert_type bootstrap 'alert alert-{type}' class
 */
function pm_manage_video_form_display_message(message, alert_type) {
	
	if (alert_type === undefined) {
		alert_type = 'warning';
	}
	
	pm_ajax_msg_div.removeAttr('class').attr('class', 'alert alert-'+ alert_type).html(message).fadeIn('fast');
}

/**
 * Clear message displayed by pm_manage_video_form_display_message()
 */
function pm_manage_video_form_clear_message() {
	pm_ajax_msg_div.html('').fadeOut('fast');
}


/**
 * Validate required fields for 'Upload video' form
 * 
 * @param {Object} fileupload_files
 */
function validate_upload_video_form(fileupload_files) {
	
	if (fileupload_files !== null) {
		if (fileupload_files.length == 0) {
			pm_manage_video_form_display_message(pm_lang.validate_select_file);
			
			return false;
		}
	}
	
	if ($('input[name="video_title"]').val() == "") {
		pm_manage_video_form_display_message(pm_lang.validate_video_title);
		$('input[name="video_title"]').trigger('focus');
		
		return false
	}
	
	if ($('select[name="category"]').val() == "-1") {
		pm_manage_video_form_display_message(pm_lang.choose_category);
		$('select[name="category"]').trigger('focus');
		
		return false
	}
	
	return true;
}

function submit_upload_video_form() {
	
	if ( ! validate_upload_video_form(null)) {
		return false;
	}
	
	if (pm_uploaded_video_id > 0) {
		$('input[name="do"]').val('submit-upload-video-form');
		
		$.ajax({
			url: MELODYURL2 + '/ajax.php',
			data: $("#upload-video-form").serializefiles(),
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(jqXHR, settings) {
				//$.notify({message: pm_lang.swfupload_status_uploading}, {type: 'info'});
				//pm_manage_video_form_clear_message();
				pm_manage_video_form_display_message(pm_lang.swfupload_status_uploading, 'info');
			}
		})
		.done(function(data) {
			//$.notifyClose(); 
			
			if (data.success == false) {
				//$.notify({message: data.msg}, {type: data.alert_type});
				pm_manage_video_form_display_message(data.msg, data.alert_type);
			} else {
				//$.notify({message: data.msg}, {type: data.alert_type});
				pm_manage_video_form_display_message(data.msg, data.alert_type);
				
				// reset form
				$('#upload-video-dropzone').fadeIn();
				$('input[name="video_title"]').val('');
				$('input[name="duration"]').val('');
				$('textarea[name="description"]').val('');
				$('select[name="category"]').val('');
				$('#tags_upload').val('');
				$('#temp_id').val('');

				// hide the HTML form
				$('#upload-video-dropzone').hide();
				$('#upload-video-form').hide();
				
				var img_input = $('input[name="capture"]');
				img_input.wrap('<form>').parent('form').trigger('reset');
				img_input.unwrap().val('');
				
				$('li#selected-file-0').remove();
			}
			
			$('#upload-video-form-nonce').val(data._pmnonce_t);
			$('input[name="do"]').val('upload-media-file');
			
			return false;
		});

	} else { 
		pm_manage_video_form_display_message(pm_lang.validate_select_file);
	} 
	return false;
}

function pm_encode_html(string) {
	return string.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/"/g, '&quot;');
}

$(document).ready(function() {
	/*
	 * Upload Video Page
	 */
	$('#upload-video-form').submit(function(e) {
		e.preventDefault();
		
		return submit_upload_video_form();
	});
	
	$('#duration').mask('99:99');
	$('input[name="video"]').change(function() {
		pm_manage_video_form_clear_message();
		//$('#upload-video-extra').fadeIn()
	});
	
	// disable submit button on page load
	$('#upload-video-submit-btn').prop('disabled', true).addClass('disabled');
	$('#upload-video-submit-btn').click(function(e) {
		$('#upload-video-form').submit();
		
		return false;
	});
	
	
	/*
	 * Edit Video Page
	 */
	$('#edit-video-form').submit(function(e) {
		e.preventDefault();
		
		$('#edit-video-submit-btn').prop('disabled', true).addClass('disabled');
		$('#edit-video-delete-btn').prop('disabled', true).addClass('disabled');
		
		$.ajax({
			url: MELODYURL2 + '/ajax.php',
			data: $("#edit-video-form").serializefiles(),
			cache: false,
			contentType: false,
			processData: false,
			type: 'POST',
			dataType: 'json',
			beforeSend: function(jqXHR, settings) {
				//$.notify({message: pm_lang.please_wait}, {type: 'info'});
				//pm_manage_video_form_clear_message();
				pm_manage_video_form_display_message(pm_lang.please_wait, 'info'); 
			}
		})
		.done(function(data) {
			//$.notifyClose(); 
			
			if (data.success == false) {
				//$.notify({message: data.msg}, {type: data.alert_type});
				pm_manage_video_form_display_message(data.msg, data.alert_type);
			} else {
				pm_manage_video_form_display_message(data.msg, data.alert_type);
				
				$('#edit-video-form').fadeOut();
			}
			
			$('#edit-video-form-nonce').val(data._pmnonce_t);
			
			$('#edit-video-submit-btn').prop('disabled', false).removeClass('disabled');
			$('#edit-video-delete-btn').prop('disabled', false).removeClass('disabled');
			
			return false;
		});
		
		return false;
	});
	
	$('#edit-video-submit-btn').click(function() {
		$('input[name="btn-pressed"]').val('submit');
		
		if ($('input[name="video_title"]').val() == "") {
			pm_manage_video_form_display_message(pm_lang.validate_video_title);
			$('input[name="video_title"]').trigger('focus');
			return false
		}
		if ($('select[name="category"]').val() == "-1") {
			pm_manage_video_form_display_message(pm_lang.choose_category);
			$('select[name="category"]').trigger('focus');
			return false
		}
	});
	
	$('#edit-video-delete-btn').click(function() {
		if (confirm(pm_lang.delete_video_confirmation)) {
			$('input[name="btn-pressed"]').val('delete');
			$('#edit-video-form').submit();
		}
		
		return false;
	});
});

/*
 * Upload Media File
 */
$(function () {
	'use strict';
	
	$('#upload-video-file-btn').fileupload({
		dataType: 'json',
		url: MELODYURL2 + '/ajax.php',
		dropZone: $('#upload-video-dropzone'),
		autoUpload: false,
		singleFileUploads: true,
		limitMultiFileUploads: 1,
		maxNumberOfFiles: 1,
		limitConcurrentUploads: 1,
		acceptFileTypes: pm_accepted_video_file_types,
		maxFileSize: parseInt($('input[name="MAX_FILE_SIZE"]').val()),
	})
	.bind('fileuploadadd', function (e, data) {
		$.each(data.files, function (index, file) {
			var file_errors = [];
			var max_file_size = parseInt($('input[name="MAX_FILE_SIZE"]').val());
			var file_size_kb = Math.round(file.size / 1024);
			
			// validate file size
			if (file.size > max_file_size) {
				pm_manage_video_form_display_message(pm_lang.swfupload_error_oversize +" (" + pm_lang.swfupload_friendly_maxsize +").");
				data.files.splice(index, 1);
				file_errors.push('Filesize is too big');
			}
			
			// upload media file
			if (file_errors.length == 0) {
				
				$('#upload-video-dropzone').addClass('animated zoomOut').hide();
				$('#upload-video-form').css('height', '100%').addClass('animated zoomIn');

				var file_node = '<li id="selected-file-' + index + '" >' +
							pm_lang.swfupload_file + ': <em>' +
							file.name +
							'</em> (' +
							file_size_kb +
							' KB)' +
							' <span class="progressvalue"></span>' +
							' <span class="cancel" data-selected-file-id="'+ index +'">'+ pm_lang.swfupload_btn_cancel +'</span>' +
							'<div class="progressbar" style="  "><div class="progress" style="width:0%;"></div></div>' +
							'<p class="status"></p>' +
							'</li>';
				$('#upload-video-selected-files-container').html(file_node);
				
				// autocomplete title field with sanitized filename
				if ($('input[name="video_title"]').val() == '') {
					$('input[name="video_title"]').val(pm_encode_html(file.name.replace(/\.[^/.]+$/, "")));
				}
				
				if (file_size_kb >= 204800) { //200MB
					$("#duration").mask("9:99:99");
				} else {
					$("#duration").mask("99:99");
				}
				
				// hook cancel button action
				$('li#selected-file-' + index + ' .cancel').on('click', { filename: file.name, files: data.files }, function(e) {
					e.preventDefault();
					data.abort();
					data.files.splice(parseInt($(this).attr('data-selected-file-id')) ,1);
					$('li#selected-file-' + index).fadeOut('fast');
					
					// Show the upload form again
					$('#upload-video-dropzone').removeClass('zoomOut').addClass('animated zoomIn').show();
					$('#upload-video-form').css('height', '1px');
				});
				
				data.submit();
			} else {
				console.log('Fileupload errors:', file_errors); 
				return false;
			}
		});
	})
	.bind('fileuploadsubmit', function (e, data) {
		pm_manage_video_form_clear_message();
		data.formData = $('#upload-video-form').serializeArray();
	})
	.bind('fileuploadstart', function (e, data) {
		// disable submit button while the file is uploading
		$('#upload-video-submit-btn').prop('disabled', true).addClass('disabled');
	})
	.bind('fileuploadprogress', function (e, data) {
	})
	.bind('fileuploadprogressall', function (e, data) {
		var percentage = parseFloat(data.loaded / data.total * 100);
		
		if (percentage < 1) {
			percentage = percentage.toPrecision(1);
		} else {
			percentage = parseInt(percentage, 10);
		}
		
		$('#upload-video-selected-files-container').find('div.progress').css('width', percentage +'%');
		$('#upload-video-selected-files-container').find('span.progressvalue').text(percentage +'%');
	})
	.bind('fileuploaddone', function (e, data) { 
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);
			var node = $('#upload-video-selected-files-container li#selected-file-' + index);
			node.find('div.progress').css('width', '100%');
			node.find('span.progressvalue').text('100%');
			node.addClass('success').find('p.status').html(pm_lang.swfupload_status_uploaded);
			
			if (server_response === null) {
				// note: keep this debug mode in production
				console.log(data.jqXHR);
				
				if (data.jqXHR.status == 200 && data.jqXHR.responseText == "") {
					console.log('Blank response is most likely coming from a misconfigured `post_max_size` and `upload_max_filesize` directives.');
				}
			}
			
			if (server_response.success == true && server_response.video_id >= 0) {
				// set new entry id
				$('#temp_id').val(server_response.video_id);
				pm_uploaded_video_id = server_response.video_id;
				
				$('li#selected-file-' + index + ' .cancel').remove();
				
				$('li#selected-file-' + index).html('<span>' + file.name + '</span>');
				
				$('#upload-video-dropzone').fadeOut();
			} else { 				
				// display returned error
				pm_manage_video_form_display_message(pm_lang.swfupload_status_error +': '+ server_response.msg);
				$('li#selected-file-' + index).remove();
				
				// Show the upload form again
				$('#upload-video-dropzone').removeClass('zoomOut').addClass('animated zoomIn').show();
				$('#upload-video-form').css('height', '1px');
			}
			// update form nonce
			$('#upload-video-form-nonce').val(server_response._pmnonce_t);
			
			// re-enable submit button
			$('#upload-video-submit-btn').prop('disabled', false).removeClass('disabled');
		});
			
		return false;
	})
	.bind('fileuploadfail', function (e, data) {
		if (data.errorThrown != 'abort') {
			$.each(data.files, function (index, file) {
				var error_message = file.error || data.errorThrown || pm_lang.upload_error_unknown; 
				
				pm_manage_video_form_display_message(pm_lang.swfupload_status_error +': '+ error_message);
			});
		}
		$('#upload-video-submit-btn').prop('disabled', false).removeClass('disabled');

		$('#upload-video-dropzone').removeClass('zoomOut').addClass('animated zoomIn').show();
		$('#upload-video-form').css('height', '1px');
	});
});

// limit dropzone to container instead of the whole page
$(document).bind('drop dragover', function (e) {
	e.preventDefault();
});

// dropzone effects
$(document).bind('dragover', function (e) {
	var dropZone = $('#upload-video-dropzone'),
		timeout = window.dropZoneTimeout;
	if (!timeout) {
		dropZone.addClass('in');
	} else {
		clearTimeout(timeout);
	}
	var found = false,
		node = e.target;
	do {
		if (node === dropZone[0]) {
			found = true;
			break;
		}
		node = node.parentNode;
	} while (node != null);
	if (found) {
		dropZone.addClass('hover');
	} else {
		dropZone.removeClass('hover');
	}
	window.dropZoneTimeout = setTimeout(function () {
		window.dropZoneTimeout = null;
		dropZone.removeClass('in hover');
	}, 100);
});