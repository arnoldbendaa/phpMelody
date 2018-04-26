function fileQueued(file) {
	showStatus("Pending...");
}

function fileQueueError(file, errorCode, message) {
	
	if (errorCode === SWFUpload.QUEUE_ERROR.QUEUE_LIMIT_EXCEEDED) {
		alert("You have attempted to queue too many files.\n" + (message === 0 ? "You have reached the upload limit." : "You may upload " + (message > 1 ? "up to " + message + " files." : "one file at a time.")));
		return;
	}

	switch (errorCode) {
	case SWFUpload.QUEUE_ERROR.FILE_EXCEEDS_SIZE_LIMIT:
			showError(swfu_msg.file_exceeds_size_limit);
		break;
	case SWFUpload.QUEUE_ERROR.ZERO_BYTE_FILE:
			showError(swfu_msg.zero_byte_file);
		break;
	case SWFUpload.QUEUE_ERROR.INVALID_FILETYPE:
			showError(swfu_msg.invalid_filetype);
		break;
	default:
		if (file !== null) {
			showError(swfu_msg.default_error);
		}
		break;
	}
}

function fileDialogComplete(numFilesSelected, numFilesQueued) {
	try {
		this.startUpload();
	} catch (ex)  {
        this.debug(ex);
	}
}

function uploadStart(file) {
	try {
		showStatus('Uploading...');
	}
	catch (ex) {}
	
	return true;
}

function uploadProgress(file, bytesLoaded, bytesTotal) {
	try {
		var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
		
		showStatus("Uploading... " + percent + "%");
	} catch (ex) {
		this.debug(ex);
	}
}

function uploadSuccess(file, serverData) {
		
		if ( serverData.match('_error_') ) {
			showError(serverData);
		}
		else
		{
			if (jQuery("#wysiwygtextarea-WYSIWYG").length > 0)
			{
				$("#wysiwygtextarea-WYSIWYG").contents().find("body").append(serverData);
			}
			else if (jQuery("#textarea-WYSIWYG").length > 0)
			{
				var textarea = $("#textarea-WYSIWYG").val();
				
				$("#textarea-WYSIWYG").val(textarea + serverData);
			}
			
			showStatus('<div class="info_msg_ok">Upload complete.</div>');
			
			setTimeout( function() {
			jQuery("#" + swfu.customSettings.progressTarget).fadeOut('slow');
			}, 5000);
		}
}

function uploadError(file, errorCode, message) {
		
		switch (errorCode) {
		case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
			showError(swfu_msg.http_error + message);
			
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
			showError(swfu_msg.upload_failed);
			
			break;
		case SWFUpload.UPLOAD_ERROR.IO_ERROR:
			showError(swfu_msg.io_error);
			
			break;
		case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
			showError(swfu_msg.security_error);
			
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
			showError(swfu_msg.upload_limit_exceeded);
			
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
			showError(swfu_msg.file_validation_failed);
			
			break;
		case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
			showError(swfu_msg.file_cancelled);
			break;
		case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
			showError(swfu_msg.upload_stopped);
			break;
		default:
			showError("Unhandled Error: " + errorCode);
			break;
		}
}

function uploadComplete(file) {
	if (this.getStats().files_queued === 0) {
	}
}

function showError(error_message) {
	jQuery("#" + swfu.customSettings.progressTarget).fadeIn('normal').html(error_message);
}

function showStatus(message) {
	jQuery("#" + swfu.customSettings.progressTarget).fadeIn('normal').html(message);
}