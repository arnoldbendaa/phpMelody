/**
 * Cron 'Automated Jobs' 
 * 
 * @since 2.6
 * @version 1.0
 */

$(document).ready(function(){
	
	var cron_notify_delay = 5000;
	var cron_table_form_nonce_name = "_admin_cron_jobs_form_automated-jobs";
	
	// hide buttons on page load
	$('.cron-add-btn').each(function(){
		if ($(this).attr('data-job-is-scheduled') == 'true') {
			$(this).hide();
		}
	});
	$('.cron-delete-btn').each(function(){
		if ($(this).attr('data-job-is-scheduled') == 'false') {
			$(this).hide();
		}
	});
	
	// start/stop job
	$('.cron-start-stop-btn').click(function(){

		var job_id = $(this).attr('data-job-id');
		var current_btn = $(this).attr('data-btn-type');
		
		$.ajax({
			url: phpmelody.admin_ajax_url,
			data: {
				"p": "cron",
				"do": "start-job",
				"job-id": job_id,
				"_pmnonce": cron_table_form_nonce_name,
				"_pmnonce_t":  $('#_pmnonce_t'+ cron_table_form_nonce_name).val()
			},
			type: "POST",
			dataType: "json"
		})
		.done( function(data) {
			
			if (data._pmnonce != '') {
				$('#_pmnonce_t'+ cron_table_form_nonce_name).val(data._pmnonce_t);
			}
			
			if (data.success == false) {
				$.notify({message: data.msg}, {type: data.alert_type});
				return false;
			}
			
			if (current_btn == 'start') {
				$('#cron-start-btn-'+ job_id).hide();
				$('#cron-stop-btn-'+ job_id).show();
			} else {
				$('#cron-start-btn-'+ job_id).show();
				$('#cron-stop-btn-'+ job_id).hide();
			}
			
			$('#cron-state-container-'+ job_id).replaceWith(data.state_html);
		});
		
		return false;
	});
	
	// show add job modal and load form
	$('.cron-add-btn').click(function(){
		
		var sub_id = $(this).attr('data-sub-id');
		
		$.ajax({
			url: phpmelody.admin_ajax_url,
			data: {
				"p": "cron",
				"do": "edit-form",
				"job-id": "",
				"rel-object-id": sub_id,
				"job-type": "import"
			},
			type: "GET",
			dataType: "json",
			beforeSend: function(jqXHR, settings) {
				$('#cron-add-modal-loading').show();
				$('#cron-add-modal-content').html('');
			},
		})
		.done( function(data) {
			$('#cron-add-modal-loading').hide();
			$('#cron-add-submit-btn').attr('data-sub-id', sub_id);
			$('#cron-add-modal-content').html(data.html);
			
			$('#add-cron-job-modal').modal('toggle');
		});

		return false;
	});
	
	// submit add job form
	$('#cron-add-submit-btn').click(function(event) {
		event.preventDefault();
		$(this).toggleClass('disabled'); // prevent multiple clicks when the UI is waiting for a 'videos this week' ajax to finish 
		
		var sub_id = $(this).attr('data-sub-id');
		
		$.ajax({
			url: phpmelody.admin_ajax_url,
			data: "p=cron&do=add-job&"+ $('#add-cron-job-form').serialize(),
			type: "POST",
			dataType: "json",
		})
		.done( function(data) {
			
			$('#cron-add-submit-btn').toggleClass('disabled');
			
			if (data.success == false) {
				$.notify({message: data.msg}, {type: data.alert_type});
				
				return false;
			}
			
			$.notify({message: data.msg}, {type: data.alert_type, delay: cron_notify_delay});
			
			$('#cron-add-btn-'+ sub_id).hide();
			$('#cron-delete-btn-'+ sub_id).css('visibility', 'visible').show().attr('data-job-id', data.job_id);
			
			$('#add-cron-job-modal').modal('toggle');
		});
		
		return false;
	});
	
	// edit job
	var current_edit_form_job_id = 0;
	
	// show edit modal and load form
	$('.cron-edit-btn').click(function(){
		
		var job_id = $(this).attr('data-job-id');
		
		if (current_edit_form_job_id != job_id) {
			$.ajax({
				url: phpmelody.admin_ajax_url,
				data: {
					"p": "cron",
					"do": "edit-form",
					"job-id": job_id
				},
				type: "GET",
				dataType: "json",
				beforeSend: function(jqXHR, settings) {
					$('#cron-edit-modal-loading').show();
					$('#cron-edit-modal-content').html('');
				},
			})
			.done( function(data) {
				$('#cron-edit-modal-loading').hide();
				$('#cron-edit-submit-btn').attr('data-job-id', job_id);
				$('#cron-edit-modal-content').html(data.html);
				
				current_edit_form_job_id = job_id;
			});
		}
		
		$('#edit-cron-job-modal').modal('toggle');
		
		return false;
	});
	
	// view log
	$('.cron-view-log-btn').click(function(){
		
		var job_id = $(this).attr('data-job-id');

		$.ajax({
			url: phpmelody.admin_ajax_url,
			data: {
				"p": "cron",
				"do": "view-log",
				"job-id": job_id,
				"_pmnonce": cron_table_form_nonce_name,
				"_pmnonce_t":  $('#_pmnonce_t'+ cron_table_form_nonce_name).val()
			},
			type: "POST",
			dataType: "json",
			beforeSend: function(jqXHR, settings) {
				$('#view-cron-log-modal-loading').show();
				$('#view-cron-log-modal-content').html('');
			},
		})
		.done( function(data) {
			$('#view-cron-log-modal-loading').hide();
			
			if (data._pmnonce != '') {
				$('#_pmnonce_t'+ cron_table_form_nonce_name).val(data._pmnonce_t);
			}
			
			if (data.success == false) {
				$.notify({message: data.msg}, {type: data.alert_type});
				return false;
			}

			$('#cron-clear-log-btn').attr('data-job-id', job_id);
			$('#view-cron-log-modal-content').html(data.html);
			
			$('#view-cron-log-modal').modal('toggle');
		});
		
		return false;
	});
	
	// clear log
	$('#cron-clear-log-btn').click(function(){
		
		var job_id = $(this).attr('data-job-id');
		
		$.ajax({
			url: phpmelody.admin_ajax_url, 
			data: {
				"p": "cron",
				"do": "clear-log",
				"job-id": job_id,
				"_pmnonce": cron_table_form_nonce_name,
				"_pmnonce_t":  $('#_pmnonce_t'+ cron_table_form_nonce_name).val()
			},
			type: "POST",
			dataType: "json",
		})
		.done( function(data) {
			
			if (data._pmnonce != '') {
				$('#_pmnonce_t'+ cron_table_form_nonce_name).val(data._pmnonce_t);
			}
			
			if (data.success == false) {
				$.notify({message: data.msg}, {type: data.alert_type});
				return false;
			}
			
			$.notify({message: data.msg}, {type: data.alert_type, delay: cron_notify_delay});
			$('#view-cron-log-modal').modal('toggle');
		});
		
		return false;
	});
	
	// delete job
	$('.cron-delete-btn').click(function(){
		
		if (confirm("Are you sure you want to delete this job?")) { 
			
			var job_id = $(this).attr('data-job-id');
			var cron_ui = $(this).attr('data-cron-ui');
			var sub_id = $(this).attr('data-sub-id');
			var nonce_t = null;
			
			if (cron_ui == 'import' || cron_ui == 'import-user') {
				nonce_name = '_admin_cron_jobs_form_' + cron_ui;
				nonce_token = $(this).attr('data-pmnonce-t');
			} else {
				nonce_name = cron_table_form_nonce_name;
				nonce_token = $('#_pmnonce_t'+ cron_table_form_nonce_name).val();
			}

			$.ajax({
				url: phpmelody.admin_ajax_url,
				data: {
					"p": "cron",
					"do": "delete-job",
					"job-id": job_id,
					"_pmnonce": nonce_name,
					"_pmnonce_t": nonce_token
				},
				type: "POST",
				dataType: "json",
			})
			.done( function(data) {
				
				if (data._pmnonce != '') {
					if (cron_ui == 'import' || cron_ui == 'import-user') {
						// update all links
						$('.cron-delete-btn').each(function(){
							$(this).attr('data-pmnonce-t', data._pmnonce_t);
						});
					} else {
						$('#_pmnonce_t'+ cron_table_form_nonce_name).val(data._pmnonce_t);
					}
				}
				
				if (data.success == false) {
					$.notify({message: data.msg}, {type: data.alert_type});
					
					return false;
				}
				
				$.notify({message: data.msg}, {type: data.alert_type, delay: cron_notify_delay});
				
				if (cron_ui == 'import' || cron_ui == 'import-user') {
					$('#cron-delete-btn-'+ sub_id).hide();
					$('#cron-add-btn-'+ sub_id).css('visibility', 'visible').show();
				} else {
					$('#tr-job-id-'+ job_id).fadeOut('normal');
				}
			})
		}
		
		return false;
	});
	
	// delete selected jobs 
	$('#cron-delete-selected-btn').click(function(){
		if (confirm("You are about to delete all these selected items. Click 'Cancel' to stop or 'OK' to continue.")) { 
			// continue form submit
		} else {
			return false;
		}
	});
});
