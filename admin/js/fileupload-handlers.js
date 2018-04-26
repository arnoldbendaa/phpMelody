/**
 * jQuery fileupload common callbacks in Admin Area 
 * 
 * @since v2.7
 */
var pm_image_file_types = /(\.|\/)(jpg|jpeg|png|gif)$/i;
var pm_media_file_types = /(\.|\/)(flv|mp4|mov|wmv|divx|avi|mkv|asf|wma|mp3|m4v|m4a|3gp|3g2)$/i;

// basic fileupload options with image file types by default 
var pm_fileupload_single_options = {
	dataType: 'json',
	autoUpload: false,
	url: phpmelody.admin_ajax_url,
	dropZone: null,
	singleFileUploads: true,
	limitMultiFileUploads: 1,
	maxNumberOfFiles: 1,
	limitConcurrentUploads: 1,
	acceptFileTypes: pm_image_file_types
};

var pm_fileupload_multi_options = {
	dataType: 'json', 
	autoUpload: false,
	url: phpmelody.admin_ajax_url,
	dropZone: null,
	acceptFileTypes: pm_image_file_types
};

var pm_fileupload_notifications = {}; // keeps each $.notify object
var pm_fileupload_notify_pos = 0;

function pm_fileupload_add(e, data) {
	
	$.each(data.files, function (index, file) {
		
		// set a unique ID for each file
		file.pm_file_id = pm_fileupload_notify_pos;
		
		var notify = $.notify({
			title: file.name,
			message: 'Pending'
		},{
			allow_dismiss: true,
			type: "info",
			delay: 0,
			showProgressbar: true,
//			onClose: function() {
//				var notify_selector_id = $(this).attr('id');
//				if ($('#'+ notify_selector_id +' .cancel:visible')) {
//					$('#'+ notify_selector_id +' .cancel').click();
//				}
//			},
			template: 
			'<div data-notify="container" class="growl alert alert-{0}" role="alert" id="selected-file-' + pm_fileupload_notify_pos + '">' +
				'<div >' +
					'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button>' +
					'<span data-notify="icon" class="growl-icon"></span> ' +
					'<div data-notify="title" class="growl-title">{1}</div> ' +
					'<span data-notify="message" class="growl-message">{2}</span>' +
					'<div class="growl-progress">' +
					'<div class="progressvalue"></div>' +
					'<div class="progress pm-notify-progressbar" data-notify="progressbar">' +
						'<div class="progress-bar progress-bar-{0} pm-notify-progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
					'</div>' +
					'</div>' + 
					'<span class="cancel btn btn-small btn-danger pull-right">Cancel</span>' +
				'</div>'+
			'</div>'
		});
		
		// hook cancel button action
		$('#selected-file-' + pm_fileupload_notify_pos + ' .cancel').on('click', { filename: file.name, files: data.files }, function(e) {
			e.preventDefault();
			data.abort();
			data.files.splice(parseInt($(this).attr('data-selected-file-id')) ,1);
			notify.close();
		});
		
		pm_fileupload_notifications[file.name] = notify;
		pm_fileupload_notify_pos++;
			
		if (file.size > phpmelody.max_file_size_bytes) {
			console.log('Filesize is too big');
			data.files.splice(index, 1);
			
			$('#selected-file-'+ file.pm_file_id +' .growl-progress').hide();
			$('#selected-file-'+ file.pm_file_id +' .cancel').hide();
			pm_fileupload_notifications[file.name].update('message', 'Size of the file is greater than the server\'s limit: '+ phpmelody.max_file_size_readable +'.')
			pm_fileupload_notifications[file.name].update('type', 'error');
		}
	});
}

function pm_fileupload_submit(e, data) {
	$.each(data.files, function (index, file) {
		pm_fileupload_notifications[file.name].update('message', 'Uploading...');
	});
}

function pm_fileupload_start(e, data) {}

function pm_fileupload_progress(e, data) {
	var percentage = parseFloat(data.loaded / data.total * 100);
	var file = data.files[0];
	
	if (percentage < 1) {
		percentage = percentage.toPrecision(1);
	} else {
		percentage = parseInt(percentage, 10);
	}
	
	// update progress bar
	//pm_fileupload_notifications[file.name].update('progress', percentage); // bug: this causes multiple notifies to bounce up and down on each update
	
	$('#selected-file-'+ file.pm_file_id +' .pm-notify-progress').css('width', percentage + '%');
	
	// update progress text 
	$('#selected-file-'+ file.pm_file_id +' .progressvalue').text(percentage + '%');
}

function pm_fileupload_progress_all(e, data) {	
	var percentage = parseInt(data.loaded / data.total * 100, 10);
}

function pm_fileupload_done(e, data) {
	$.each(data.files, function (index, file) {
		// replace previous 'cancel' event with notify.close() 
		$('#selected-file-'+ file.pm_file_id +' .cancel').off('click').on('click', function(){
			pm_fileupload_notifications[file.name].close();
		});
		
		// update progress to 100%
		pm_fileupload_notifications[file.name].update('progress', 100);
		$('#selected-file-'+ file.pm_file_id +' .progressvalue').text('100%');
		
		var server_response = $.parseJSON(data.jqXHR.responseText);
		
		if (server_response === null) {
			// note: keep this debug mode in production
			console.log(data.jqXHR);
			
			if (data.jqXHR.status == 200 && data.jqXHR.responseText == "") {
				console.log('Blank response is most likely coming from a misconfigured `post_max_size` and `upload_max_filesize` directives.');
			}
		}
		
		// hide cancel button
		$('#selected-file-'+ file.pm_file_id +' .cancel').hide().remove();
		
		if (server_response.success == true) {
			pm_fileupload_notifications[file.name].update('type', server_response.alert_type || 'success');
			pm_fileupload_notifications[file.name].update('message', 'Uploaded! "Save" this page to apply the changes.');
			
			// close notify when clicking on it
			$('#selected-file-' + file.pm_file_id).click(function(){
				pm_fileupload_notifications[file.name].close();
			});
			
			// auto-close notify
			setTimeout(function(){
				pm_fileupload_notifications[file.name].close();
			}, 5000);
		} else {
			$('#selected-file-'+ file.pm_file_id +' .growl-progress').hide();
			$('#selected-file-'+ file.pm_file_id +' .cancel').hide();
			pm_fileupload_notifications[file.name].update('type', server_response.alert_type || 'error');
			pm_fileupload_notifications[file.name].update('message', 'Error: '+ server_response.msg);
		}
		
	});
}

function pm_fileupload_fail(e, data) {
	if (data.errorThrown != 'abort') {
		$.each(data.files, function (index, file) {
			var error_message = file.error || data.errorThrown || 'Unknown Error';
			
			pm_fileupload_notifications[file.name].update('type', 'error');
			pm_fileupload_notifications[file.name].update('message', 'Error: '+ error_message);
		});
	}
}
