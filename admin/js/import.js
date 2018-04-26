/**
 * Handler for import.php and import-user.php
 * 
 * @since v2.5
 */

var pm_import_videos_on_page = 0;
var pm_import_next_page = '';
var pm_import_search_count = 0; // counts only successful searches; resets when new search is performed
var pm_import_user_search_action = 'search'; // = search || playlists || favorites  for 'Load more' functionality
var pm_import_user_playlist_id = '';
var pm_import_user_playlist_title = '';
var pm_import_continue_counter = 0; // counts the number of times import_search() has been called recursively
var pm_import_videos_counter = 0; // counts the number of times import_videos() has been called recursively
var pm_import_doing_ajax = false; // does not apply to "Import" method
var pm_import_xhr = null; // keeps the most recent xhr object
var subscriptions_ajax_manager = null;

function update_stack_controls() {
	
	if (pm_import_next_page) {
		$('#import-load-more-div').show();
	} else {
		$('#import-load-more-div').hide();
	}
	
	if (pm_import_videos_on_page > 0 && pm_import_user_search_action != 'list-playlists') {
		$('#stack-controls').show();
	} else {
		$('#stack-controls').hide();
	}
}

function updateCount(){
	var count = $("input[id^='import-'][type=checkbox]:checked").size();
	
	if( count > 0 ) {
		$("#count").text(count);
	}
	$("#status").toggle(count > 0);
};

function import_subscribe_search_success(data) {
	
	if (data.success == false) {
		if (data.html != '') {
			$('.modal-response-placeholder').html(data.html).show();
		} else if (data.msg != '') {
			alert(data.msg);
		}
	} else {
		$('#modal_subscribe').modal('hide');

		$('#btn-subscribe').hide();
		$('#btn-unsubscribe').attr('data-subscription-id', data.sub_id).show();
		$('div.pagination > ul > li > a').each( function() {
			$(this).attr('href', $(this).attr('href') + '&sub_id=' + data.sub_id);
		});
	}
	return;
}
function import_subscribe_user_success(data) {
	
	if (data.success == false) {
		if (data.html != '') {
			$('#modal_subscribe').modal('show');
			$('.modal-response-placeholder').html(data.html).show();
		} else if (data.msg != '') {
			alert(data.msg);
		}
	} else {
		$('#modal_subscribe').modal('hide');
		$('#btn-subscribe').hide();
		$('#btn-unsubscribe').attr('data-subscription-id', data.sub_id).show();
		
		$('div.pagination > ul > li > a').each( function() {
			$(this).attr('href', $(this).attr('href') + '&sub_id=' + data.sub_id);
		});
	}
	return;
}

function import_subscribe() {

	$('.modal-response-placeholder').html('').hide();
	
	var sub_type = $('input[name="sub-type"]').val();
 
	$.ajax({
		url: phpmelody.admin_ajax_url,
		data: {
			"p": "import-subscriptions",
			"do": "subscribe",
			"name": $('input[name="sub-name"]').val(),
			"type": $('input[name="sub-type"]').val(),
			"params": $('input[name="sub-params"]').val(),
			"keyword": $('input[name="keyword"]').val(),
			"_pmnonce": '_admin_import_subscriptions',
			"_pmnonce_t": $('#_pmnonce_t_admin_import_subscriptions').val()
		},
		type: 'POST',
		dataType: 'json',
		success: function(data){
			if (sub_type == 'user' || sub_type == 'user-favorites' || sub_type == 'user-playlist') {
				import_subscribe_user_success(data);
			} else {
				import_subscribe_search_success(data);
			}
			
			if (data._pmnonce != '') {
				$('#_pmnonce_t_admin_import_subscriptions').val(data._pmnonce_t);
			}
		}
	});
	return false;
}

function import_unsubscribe(unsubscribe_btn) {
 
	var sub_id = unsubscribe_btn.attr('data-subscription-id');

	if (sub_id) {
		if (confirm('Are you sure you want to unsubscribe?')) {

			$.ajax({
				url: phpmelody.admin_ajax_url,
				data: {
					"p": "import-subscriptions",
					"do": "unsubscribe",
					"sub-id": sub_id,
					"_pmnonce": '_admin_import_subscriptions',
					"_pmnonce_t": $('#_pmnonce_t_admin_import_subscriptions').val()
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if (data.success == false) {
						$.notify({message: data.msg}, {type: data.alert_type});
					} else {
						$('#btn-subscribe').show();
						$('#btn-unsubscribe').hide();
						$('#row-subscription-'+ sub_id).fadeOut('normal');
						
						$('div.pagination > ul > li > a').each( function(index) {
							$(this).attr('href', $(this).attr('href').replace('&sub_id='+ sub_id, ''));
						});
					}
					
					if (data._pmnonce != '') {
						$('#_pmnonce_t_admin_import_subscriptions').val(data._pmnonce_t);
					}
				}
			});
		}
	} else {
		$.notify({message: 'Missing subscription ID. Please reload the page and try again.'}, {type: 'error'});
	}

	return false;
}


function import_show_loading(display_text) {
	
	display_text = display_text || 'Please wait';
	
	$('#loading-large .loading-msg').html(display_text).addClass('animated infinite fadeIn');
	$('#loading-large').addClass('animated infinite pulse').show();
	$('.import-user-playlist-item').css({"opacity" : "0.3"});
	$('.video-stack').addClass('stack-gray');
	$('#import-load-more-btn, #import-user-load-more-btn, #import-csv-load-more-btn, #import-submit-btn').html('Loading ...').toggleClass('disabled');
}

function import_hide_loading() {
	$('#loading-large').hide();
	$('.import-user-playlist-item').css({"opacity" : "1"});
	$('.video-stack').removeClass('stack-gray');
	$('#import-load-more-btn, #import-user-load-more-btn, #import-csv-load-more-btn').html('Load more').toggleClass('disabled');
	$('#import-submit-btn').html('Import <span id="status"><span id="count"></span></span> videos').toggleClass('disabled');
}

/**
 * Reapply/bind/etc. rules and functionality to new elements.
 * 
 * The import UI depends on a lot of scripts, plugins, style and functionality.
 * All of them need to be manually re-applied after loading new HTML 
 * elements via AJAX requests.
 */
function import_apply_scripts() {

	// $load_chzn_drop
	$('.category_dropdown').each(function(){
		if ( ! $(this).hasClass('chzn-select')) {
			$(this).addClass("chzn-select").chosen({width: "100%", allow_single_deselect: true});
		}
	});
	
	// $load_tagsinput
	$('input[id^="tags_addvideo_"]').tagsInput({
		onAddTag : function(tag){
		if(tag.indexOf(',') > 0){
			tidyTags({target: 'input[id^="tags_addvideo_"]', tags : tag});
			 }
		 },
		'removeWithBackspace' : true,
		'height':'auto',
		'width':'auto',
		'defaultText':'',
		'minChars' : 3,
		'maxChars' : 90
	});
	
	$("img[name='video_thumbnail']").click(function() {
		
		var img = $(this);
		var row_id = $(this).attr('rowid');
		var ul = img.parents('.thumbs_ul_import');
		var li = img.parent();
		var tr = img.parents('div');	
		var input = $('#thumb_url_'+ row_id);
	
		if ( ! li.hasClass('stack-thumb-selected'))
		{
			ul.children().removeClass('stack-thumb-selected').addClass('stack-thumb');
			li.addClass('stack-thumb-selected');
			input.val(img.attr('src'));
		}
	});
	
	// $load_ibutton
	$('.on_off :checkbox').iButton({
		duration: 80,
		labelOn: "",
		labelOff: "",
		enableDrag: false 
	});

	$('.on_off :checkbox').change(function(){
		if ($(this).attr("checked")) {
			$(this).closest('.video-stack').addClass("stack-selected");
		}
		else {
			$(this).closest('.video-stack').removeClass("stack-selected");
		}
		
		updateCount();
	});
	
	// $load_prettypop
	$("a[rel^='prettyPop']").prettyPhoto({
		animationSpeed: 'fast', /* fast/slow/normal */
		padding: 40, /* padding for each side of the picture */
		opacity: 0.70, /* Value betwee 0 and 1 */
		showTitle: false, /* true/false */
		allowresize: false, /* true/false */
		counter_separator_label: '/', /* The separator for the gallery counter 1 "of" 2 */
		theme: 'dark_rounded', /* light_rounded / dark_rounded / light_square / dark_square */
		width: 1024,
		height: 744,
		// flowplayer settings - start
		fp_bgcolor: pm_prettyPop_fp_bgcolor,
		fp_timecolor: pm_prettyPop_fp_timecolor,
		fp_swf_loc: pm_prettyPop_fp_swf_loc,
		// flowplayer settings - end 
		callback: function(){
		}
	});
	
	// $load_lazy_load
	pm_init_lazy_load();
}

/**
 * Playlists are loaded dynamically through the 'list-playlists' action
 * so we need to bind the click action after loading them.
 */
function import_user_bind_playlist_item() {
	
	$('.import-user-playlist-item').click(function(event){
		event.preventDefault();
		
		pm_import_user_playlist_id = $(this).attr('data-playlist-id');
		pm_import_user_playlist_title = $(this).attr('data-playlist-title');
		pm_import_search_count = 0;
		pm_import_next_page = '';
		pm_import_user_search_action = 'playlists';
		
		$('#import-user-nav-latest-uploads').parent('li').removeClass('active');
		$('#import-user-nav-playlists').parent('li').addClass('active');
		$('#import-user-nav-favorites').parent('li').removeClass('active');
		
		import_search("p=import&do=search-user&action="+ pm_import_user_search_action +"&playlistid="+ pm_import_user_playlist_id +"&title="+ pm_import_user_playlist_title +"&"+ $('#import-user-search-form').serialize() +"&checkall="+ $('#checkall').is(':checked'));
	});
}

/**
 * A wrapper for the common ajax request
 * 
 * @param {String} ajax_data the 'data' ajax setting 
 */
function import_search(ajax_data) {

	// stop the ajax manager if it's currently working
	if (subscriptions_ajax_manager != null) {
		try {
			$.manageAjax.destroy('subscriptions');
			$('.row-subscription-loading-gif').hide();
			subscriptions_ajax_manager = null;
		} catch(e) {
			console.log(e);
		}
	}
	
	// abort current ajax requests in favor of user action
	if (pm_import_doing_ajax) {
		try {
			pm_import_xhr.abort();
			pm_import_doing_ajax = false;
			pm_import_continue_counter = 0;
		} catch (e) {
			console.log(e);
		}
	}
	
	pm_import_xhr = $.ajax({
		url: phpmelody.admin_ajax_url,
		data: ajax_data, 
		type: "POST",
		dataType: "json",
		beforeSend: function(jqXHR, settings) {
			// clean error message container
			$.notifyClose();
			import_show_loading('Sending API request');
			
			// set doing ajax flag
			pm_import_doing_ajax = true;
			
		},
	})
	.always(function(data) {
		import_hide_loading();
		
		// set doing ajax flag
		pm_import_doing_ajax = false; 
	})
	.done(function(data) {
			 
		if (data.success == false) {
			$.notify({message: data.msg}, {type: data.alert_type});
			
			return false;
		}
		
		if (data.total_search_results == 0) {
			$.notify({message: data.msg}, {type: data.alert_type});
			
			return false;
		}
		
		// clear content area
		if (pm_import_search_count == 0) {
			$('#import-content-placeholder').empty();
			pm_import_videos_on_page = 0;
		}
		
		pm_import_next_page = data.next_page;
		pm_import_search_count++;
		
		if (data.duplicates == data.total_results && data.total_results > 0) {				
			// load next page(s) recursively but prevent infinite loops
			if (data.next_page != null && pm_import_continue_counter < 5) {
				
				pm_import_continue_counter++;
				
				return import_search(ajax_data + "&page="+ data.next_page);
			} else {
				// stop recursion and tell the user to manually load more.
				$.notify({message: data.msg}, {type: data.alert_type});
				
				pm_import_continue_counter = 0;
				
				update_stack_controls();
			}
		}
		
		// append content to page
		if (data.duplicates < data.total_results) {
			$('#import-content-placeholder').append(data.html);
			updateCount();
		}
		
		pm_import_videos_on_page += data.total_results - data.duplicates;
		
		// show the top-right layout and subscription buttons
		if ($('#import-ui-control').is(':hidden')) {
			$('#import-ui-control').show();
		}
		
		if (data.sub_id > 0) {
			$('#btn-subscribe').hide();
			$('#btn-unsubscribe').show().attr('data-subscription-id', data.sub_id);
		}
		
		if (data.sub != undefined) {
			$('input[name="sub-name"]').val(data.sub.name);
			$('input[name="sub-params"]').val(data.sub.params);
			$('input[name="sub-type"]').val(data.sub.type);
			
			if (data.sub._pmnonce_t != '') {
				$('#_pmnonce_t_admin_import_subscriptions').val(data.sub._pmnonce_t);
			}
		}
		
		if (data.next_page == null || data.next_page == '') {
			$('#import-load-more-btn').prop('disabled', true);
			$('#import-user-load-more-btn').prop('disabled', true);
			$('#import-csv-load-more-btn').prop('disabled', true);
		} else {
			$('#import-load-more-btn').prop('disabled', false);
			$('#import-user-load-more-btn').prop('disabled', false);
			$('#import-csv-load-more-btn').prop('disabled', false);
		}
		
		update_stack_controls();
		
		if (pm_import_user_search_action != 'list-playlists' && data.total_results > 0) {
			// apply rules on new content
			import_apply_scripts();
			
			var selected_per_page = parseInt($('input[name="results"]').val());
			
			// fill in the page with more content when duplicate videos => 40%
			if (data.next_page != null && pm_import_videos_on_page > 0 && pm_import_videos_on_page <= (selected_per_page * 0.6)) {
				
				return import_search(ajax_data + "&page="+ data.next_page);
			}
		}
	})
	.fail( function(jqXHR, textStatus, errorThrown) {
		if (textStatus != "" && textStatus != "abort" && textStatus != "canceled" && ! (textStatus == "error" && errorThrown == "") && jqxhr.status != 0) {
			$.notify({message: "Action could not be completed.<br />Please reload the page and try again.<br /><br /><code>AJAX error: " + textStatus +"<br />"+ errorThrown +"</code>"}, {type: 'error'});
		}
	});
	
	return pm_import_xhr;
}

function import_videos(selected_items) {
	
	if (selected_items === undefined) {
		selected_items = $("input[id^='import-'][type=checkbox]:checked").size()
	}
	
	$.ajax({
		url: phpmelody.admin_ajax_url,
		data: "p=import&do=import&data_source="+ $('select[name="data_source"]').val() +"&"+ $('#import-search-results-form').serialize(),
		type: "POST",
		dataType: "json",
		beforeSend: function(jqXHR, settings) {
			// clean error message container
			$.notifyClose();
			import_show_loading('Importing...');
			//pm_doing_ajax = true;
		},
	})
	.always(function(data) {
		import_hide_loading();
		//pm_doing_ajax = false;
	})
	.done( function(data) {

		if (data.success == false) {
			$.notify({message: data.msg}, {type: data.alert_type});
			return false;
		}
		
		if (data.imported_total) {
			pm_import_videos_on_page -= data.imported_total;
		}
		
		if (data.msg != '') {
			$.notify({message: data.msg}, {type: data.alert_type});
		}
		
		// remove/display errors for each individual video  
		if (data.item_status.length > 0) { 
			for (var i = 0; i < data.item_status.length; i++) {
				
				var item = data.item_status[i];
				
				// need to uncheck first 
				$('#import-'+ item.yt_id).attr('checked', false);
				
				if (item.success == true) {
					if (selected_items > 50) {
						$(item.stack_id).remove();
					} else {
						$(item.stack_id).fadeOut('normal', function() { $(this).remove(); });
					}
				} else {
					$(item.stack_id).append(item.html);//.removeClass("stack-selected");
					$(item.stack_id + ' .on_off :checkbox').iButton("repaint");
				}
			}
		}
		
		pm_import_videos_counter++;
		
		// limited by php's max_input_vars? continue importing the rest of the videos recursively
		if (selected_items > 0 && ((data.imported_total + data.import_total_errors) < selected_items) 
			&& data.import_total_errors == 0 && pm_import_videos_counter < 10) {

			return import_videos();
		}
		
		update_stack_controls();
	})
	.fail( function(jqXHR, textStatus, errorThrown) {
		if (textStatus != "" && textStatus != "abort" && textStatus != "canceled" && ! (textStatus == "error" && errorThrown == "") && jqxhr.status != 0) {
			$.notify({message: "Action could not be completed.<br />Please reload the page and try again.<br /><br /><code>AJAX error: " + textStatus +"<br />"+ errorThrown +"</code>"}, {type: 'error'});
		}
	});

}

function import_csv_process_queue(start, params, html_output_sel) {
	$('.import-ajax-loading-animation').css({'display' : 'inline'})
	
	$.ajax({
		url: phpmelody.admin_ajax_url,
		data: "p=import-csv&do=process-queue" +
			  "&file_id="+ params.file_id +
			  "&start="+ start +  
			  "&progress=" + params.progress +
			  "&items_processed=" + params.items_processed + 
			  "&total_items="+ params.total_items +
			  "&eta=" + params.eta,
		type: "POST",
		dataType: "json",
		beforeSend: function(jqXHR, settings) {
			// clean error message container
			$.notifyClose();
		},
	})
	.done( function(data) {
		$('.bar').css({'width': data['progress'] + "%"});
		//$('.bar').html(data['progress'] + "%"); // Removed for styling purposes
		
		switch (data['state'])
		{
			case 'processing':
				$("#progressbar").fadeIn();
				//$("#progressbar").progressbar({value: data['progress']});
				params.progress = data['progress'];
				params.items_processed = data['items_processed'];
				params.total_items = data['total_items'];
				params.file_id = data['file_id'];
				params.eta = data['eta'];
				
				if (data['eta'] > 0 && data['progress'] < 70) {
					$('#import-csv-eta-value').html(data['eta_formatted']);
					$('#import-csv-eta').show();
				}

				import_csv_process_queue(data['start'], params, html_output_sel);
				
			break;
			
			case 'finished':
			case 'error':
				if (data['state'] == 'finished') {
					$("#progressbar").hide();
					$('#import-csv-ajax-response').html(data['message']);
					$('#import-csv-process-btn').hide();
				} else {
					//$( "#progressbar" ).progressbar({value: data['progress'] });
					$('#import-csv-ajax-response').html(data['message']);
					$('#import-csv-process-btn').attr('disabled', false);
				}
				
				$('.import-ajax-loading-animation').hide();
			break;
		}
	})
	.fail( function(jqXHR, textStatus, errorThrown) {
		if (textStatus != "" && textStatus != "abort" && textStatus != "canceled" && ! (textStatus == "error" && errorThrown == "") && jqxhr.status != 0) {
			$.notify({message: "Action could not be completed.<br />Please reload the page and try again.<br /><br /><code>AJAX error: " + textStatus +"<br />"+ errorThrown +"</code>"}, {type: 'error'});
		}
		$('#import-csv-process-btn').attr('disabled', false);
		$('.import-ajax-loading-animation').hide();
	});
}


$(document).ready(function(){
	
	$("input[id^='import-'][type=checkbox]").change(function(){
		updateCount();
	});

	$('#checkall').change(function(){
		var checked = $(this).prop("checked");
		
		$("input[id^='import-']:checkbox").not(".check_ignore").each(function(){
			//if ($(this).attr('name') != 'checkall' && ! $(this).prop('disabled') && ! $(this).hasClass('check_ignore')) {
				$(this).prop('checked', checked).change();
			//}
		});
		updateCount();
	});
	
	$('#import-search-videos-form').submit(function(event) {
		event.preventDefault();
	});
	$('#import-search-results-form').submit(function(event) {
		event.preventDefault();
	});
	$('#import-user-search-form').submit(function(event) {
		event.preventDefault();
	});
	
	$('#import-submit-btn').click(function(event) {
		event.preventDefault();	
		pm_import_videos_counter = 0;
		
		import_videos();
	});
	
	// --------------------------------------------- 
	//					Import
	// --------------------------------------------- 
	
	$('#search-videos-btn').click(function(event) {
		event.preventDefault();
		
		pm_import_search_count = 0;
		pm_import_next_page = '';
			
		// reset the Subscribe button if state = subscribed
		$('#btn-subscribe').show();
		$('#btn-unsubscribe').hide();
		
		import_search("p=import&do=search&"+ $('#import-search-videos-form').serialize() +"&checkall="+ $('#checkall').is(':checked'));
	});
	
	$('#import-load-more-btn').click(function(event) {
		event.preventDefault();

		import_search("p=import&do=search&page="+ pm_import_next_page +"&"+ $('#import-search-videos-form').serialize() +"&checkall="+ $('#checkall').is(':checked'))
	});
	
	$('.row-subscription-link').click(function(event){
		event.preventDefault();

		var sub_id = $(this).attr('data-sub-id');
		
		$('#import-content-placeholder').empty();
		
		import_search("p=import&do=search&"+ $(this).attr('data-query') +"&checkall="+ $('#checkall').is(':checked'))
		.done( function(data) {
			// change subscription button state
			$('#btn-subscribe').hide();
			$('#btn-unsubscribe').attr('data-subscription-id', sub_id).css('visibility', 'visible').show();
		});
		
		// manually fill in the form with subscription data
		var sub_data = $.unserialize( $(this).attr('data-query') );
		
		$('input[name="keyword"]').val(sub_data.keyword);
		$('select[name="data_source"]').val(sub_data.data_source);
		if (sub_data.oc == "1" && sub_data.utc != "") {
			$('#main_select_category').val(sub_data.utc.split(",")).trigger("chosen:updated");
		}
		if (sub_data.autodata == "1") {
			$('input[name="autodata"]').prop('checked', true);
		} else {
			$('input[name="autodata"]').prop('checked', false);
		}
		
		$('select[name="search_category"]').val(sub_data.search_category);
		$('select[name="search_duration"]').val(sub_data.search_duration);
		$('select[name="search_time"]').val(sub_data.search_time);
		$('select[name="search_orderby"]').val(sub_data.search_orderby);
		$('select[name="search_language"]').val(sub_data.search_language);
		$('select[name="search_license"]').val(sub_data.search_license);
		if (sub_data.search_hd == 'true') {
			$('input[name="search_hd"]').prop('checked', true);
		} else {
			$('input[name="search_hd"]').prop('checked', false);
		}
		if (sub_data.search_3d == 'true') {
			$('input[name="search_3d"]').prop('checked', true);
		} else {
			$('input[name="search_3d"]').prop('checked', false);
		}
		if (sub_data.autofilling == "1") {
			$('input[name="autofilling"]').prop('checked', true);
		} else {
			$('input[name="autofilling"]').prop('checked', false);
		}
	});
	
	// --------------------------------------------- 
	//			Import from User/Channel
	// --------------------------------------------- 
	$('#search-user-btn').click(function(event) {
		event.preventDefault();

		pm_import_search_count = 0;
		pm_import_next_page = '';
		pm_import_user_search_action = 'search';
		
		// reset the Subscribe button if state = subscribed
		$('#btn-subscribe').show();
		$('#btn-unsubscribe').hide();
		
		import_search("p=import&do=search-user&action="+ pm_import_user_search_action +"&"+ $('#import-user-search-form').serialize() +"&checkall="+ $('#checkall').is(':checked'))
		.done( function(data) {
			if (data.success) {
				$('.import-user-nav').show();
			}
		});
		
		$('#import-user-nav-latest-uploads').parent('li').addClass('active');
		$('#import-user-nav-playlists').parent('li').removeClass('active');
		$('#import-user-nav-favorites').parent('li').removeClass('active');	
	});
	
	$('#import-user-load-more-btn').click(function(event) {
		event.preventDefault();
		
		if (pm_import_user_search_action == 'list-playlists') {
			import_search("p=import&do=search-user&action="+ pm_import_user_search_action +"&page="+ pm_import_next_page +"&"+ $('#import-user-search-form').serialize() +"&checkall="+ $('#checkall').is(':checked'))
			.done(function(){
				import_user_bind_playlist_item();
			});
		} else if (pm_import_user_search_action == 'playlists') {
			import_search("p=import&do=search-user&action="+ pm_import_user_search_action +"&page="+ pm_import_next_page +"&playlistid="+ pm_import_user_playlist_id +"&title="+ pm_import_user_playlist_title +"&"+ $('#import-user-search-form').serialize() +"&checkall="+ $('#checkall').is(':checked'));
		} else {
			import_search("p=import&do=search-user&action="+ pm_import_user_search_action +"&page="+ pm_import_next_page +"&"+ $('#import-user-search-form').serialize() +"&checkall="+ $('#checkall').is(':checked'));
		}
	});
	
	$('#import-user-nav-latest-uploads').click(function(event) {
		event.preventDefault();
		
		var previous_action = pm_import_user_search_action;
		
		pm_import_search_count = 0;
		pm_import_next_page = '';
		pm_import_user_search_action = 'search';
		
		// reset active tab
		$('#import-user-nav-latest-uploads').parent('li').removeClass('active');
		$('#import-user-nav-playlists').parent('li').removeClass('active');
		$('#import-user-nav-favorites').parent('li').removeClass('active');
		$(this).parent('li').addClass('active');
		
		import_search("p=import&do=search-user&action="+ pm_import_user_search_action +"&"+ $('#import-user-search-form').serialize() +"&checkall="+ $('#checkall').is(':checked'));
	});
	
	$('#import-user-nav-playlists').click(function(event) {
		event.preventDefault();
		
		pm_import_search_count = 0;
		pm_import_next_page = '';
		pm_import_user_search_action = 'list-playlists';
		
		// reset active tab
		$('#import-user-nav-latest-uploads').parent('li').removeClass('active');
		$('#import-user-nav-playlists').parent('li').removeClass('active');
		$('#import-user-nav-favorites').parent('li').removeClass('active');
		$(this).parent('li').addClass('active');
		
		import_search("p=import&do=search-user&action="+ pm_import_user_search_action +"&"+ $('#import-user-search-form').serialize() +"&checkall="+ $('#checkall').is(':checked'))
		.done(function(data) {
			import_user_bind_playlist_item();
		});
	});
	
	$('#import-user-nav-favorites').click(function(event) {
		event.preventDefault();
		
		pm_import_search_count = 0;
		pm_import_next_page = '';
		pm_import_user_search_action = 'favorites';
		
		// reset active tab
		$('#import-user-nav-latest-uploads').parent('li').removeClass('active');
		$('#import-user-nav-playlists').parent('li').removeClass('active');
		$('#import-user-nav-favorites').parent('li').removeClass('active');
		$(this).parent('li').addClass('active');
		
		import_search("p=import&do=search-user&action="+ pm_import_user_search_action +"&"+ $('#import-user-search-form').serialize() +"&checkall="+ $('#checkall').is(':checked'));
	});
	
	$('.row-user-subscription-link').click(function(event){
		event.preventDefault();

		var sub_id = $(this).attr('data-sub-id');
		pm_import_user_search_action = 'search';
		
		$('#import-content-placeholder').empty();
		
		import_search("p=import&do=search-user&action="+ pm_import_user_search_action +"&"+ $(this).attr('data-query') +"&checkall="+ $('#checkall').is(':checked'))
		.done(function(data) {
			// change subscription button state
			$('#btn-subscribe').hide();
			$('#btn-unsubscribe').attr('data-subscription-id', sub_id).css('visibility', 'visible').show();
			
			if (data.success) {
				$('.import-user-nav').show();
			}
		});
		
		// manually fill in the form with subscription data
		var sub_data = $.unserialize( $(this).attr('data-query') );
		
		$('input[name="username"]').val(sub_data.username);
		$('select[name="data_source"]').val(sub_data.data_source);
		//$('select[name="results"]').val(sub_data.results);
		if (sub_data.oc == "1" && sub_data.utc != "") {
			$('#main_select_category').val(sub_data.utc.split(",")).trigger("chosen:updated");
		}
		if (sub_data.autodata == "1") {
			$('input[name="autodata"]').prop('checked', true);
		} else {
			$('input[name="autodata"]').prop('checked', false);
		}
		if (sub_data.autofilling == "1") {
			$('input[name="autofilling"]').prop('checked', true);
		} else {
			$('input[name="autofilling"]').prop('checked', false);
		}
	});
	
	// --------------------------------------------- 
	// 		Subscribe/Unsubscribe buttons
	// ---------------------------------------------
	var sub_type = $('input[name="sub-type"]').val();
	$('#btn-unsubscribe').hide(); // this works better instead of using .hide; main div is hidden anyway

	$('#btn-subscribe-modal-save').click(function(){ 
		return import_subscribe();
	});
	if (sub_type == 'user' || sub_type == 'user-favorites' || sub_type == 'user-playlist') {
		$('#btn-subscribe').click(function(){
			return import_subscribe();
		})
	}

	$('#btn-unsubscribe').click(function(){
		return import_unsubscribe($(this));
	});
	
	$('.link-search-unsubscribe').click(function(){ 
		return import_unsubscribe($(this));
	});
	
	var subscriptions = $.makeArray($('.row-subscription-get-results'));
	
	if (subscriptions.length > 0) {
		var i = 0, sub_id = 0;
		var obj;
		subscriptions_ajax_manager = $.manageAjax.create('subscriptions', {queue: true, maxRequests: 1});
		
		$('#loading').show();
		
		for (i = 0; i < subscriptions.length; i++) {
			sub_id = ( $(subscriptions[i]).attr('data-subscription-id') );

			if (sub_id > 0) {

				$.manageAjax.add( 'subscriptions' , { 
					url: phpmelody.admin_ajax_url,
					data: {
						"p": "import-subscriptions",
						"do": "get-results",
						"sub-id": sub_id
					},
					type: 'GET',
					dataType: 'json',
					success: function(data) {
						if (data.success == false) {
							var html = '<a href="#" title="'+ data.msg.replace('"', '&quot;') +'" rel="tooltip">?</a>'; 
							$('#row-subscription-'+ data.sub_id).find('.row-subscription-get-results').html(html);
						} else {
							$('#row-subscription-'+ data.sub_id).find('.row-subscription-get-results').html(data.msg);
						}
					},
				});
			}
		}
	}
	
	// --------------------------------------------- 
	//			Import from CSV
	// --------------------------------------------- 
	$('#import-csv-show-videos-btn').click(function(event) {
		event.preventDefault();
		pm_import_search_count = 0;
		pm_import_next_page = '';
		
		import_search("p=import&do=csv-get-videos&"+ $('#import-csv-options-form').serialize() +"&checkall="+ $('#checkall').is(':checked'))
		.done(function(data) {
			import_hide_loading();
		});
		// overwrite the beforeSend call
		import_show_loading('Loading videos');
	});

	$('#import-csv-load-more-btn').click(function(event) {
		event.preventDefault();
	
		import_search("p=import&do=csv-get-videos&page="+ pm_import_next_page +"&"+ $('#import-csv-options-form').serialize() +"&checkall="+ $('#checkall').is(':checked'))
		.done(function(data) {
			import_hide_loading();
		});
		// overwrite the beforeSend call
		import_show_loading('Loading videos');
	});
	
	$('#import-csv-process-btn').click(function(){
		var params = new Array();
		
		$(this).attr('disabled', true);
		$("#progressbar").fadeIn();
		$('.bar').css({'width': '3%'});
		
		params['progress'] = 0;
		params['items_processed'] = 0;
		params['total_items'] = parseInt( $('input[name="items_detected"]').val() );
		params['file_id'] = $('input[name="file_id"]').val();
		params['eta'] = 0;
		
		import_csv_process_queue(0, params, '#import-csv-ajax-response');
		return false;
	});
	
	$('.import-csv-delete-file').click(function(event){
		event.preventDefault();
		
		var file_id = $(this).attr('data-file-id');
	
		if (confirm("Are you sure you want to delete this file and all related data? Imported videos will not be removed from your database.")) {
			$.ajax({
				url: phpmelody.admin_ajax_url,
				data: "p=import-csv&do=delete-file&file_id="+ file_id,
				type: "POST",
				dataType: "json",
				beforeSend: function(jqXHR, settings) {
					// clean error message container
					$.notifyClose();
				},
			})
			.done( function(data) {
				if (data.success == false) {
					$.notify({message: data.msg}, {type: data.alert_type});
					return false;
				}
				$('#import-csv-table-row-'+ file_id).fadeOut('normal').hide();
			})
			.fail( function(jqXHR, textStatus, errorThrown) {
				$.notify({message: "Action could not be completed.<br />Please reload the page and try again.<br /><br /><code>AJAX error: " + textStatus +"<br />"+ errorThrown +"</code>"}, {type: 'error'});
			});
		}
	});
	
});

// --------------------------------------------- 
//		Upload CSV files
// --------------------------------------------- 
var pm_fileupload_notify_pos = 0;

$(function () {
	'use strict';
	$('#upload-csv-btn').fileupload({
		dataType: 'json',
		autoUpload: false,
		url: phpmelody.admin_ajax_url,
		// dropZone: $('#upload-csv-dropzone'),
		acceptFileTypes: /(\.|\/)(csv|txt)$/i
	})
	.bind('fileuploadadd', function (e, data) {
		
		$('input[name="upload-type"]').val('csv');
		
		$.each(data.files, function (index, file) {
			
			// validate file size first
			if (file.size > phpmelody.max_file_size_bytes) {
				console.log('Filesize is too big');
				data.files.splice(index, 1);
				$.notify({title: file.name, message: 'Size of the file is greater than the server\'s limit: ' + phpmelody.max_file_size_readable + '.'}, {type: 'error'});
			} else {
				// set a unique ID for each file
				file.pm_file_id = pm_fileupload_notify_pos;
				
				var listitem = '<li id="selected-file-' + pm_fileupload_notify_pos + '" class="pm-csv-data">' +
				//'File: <em>'+file.name+'</em> ('+Math.round(file.size/1024)+' KB) <span class="progressvalue" ></span>'+
				'<div class="pm-file-data-append-' +
				pm_fileupload_notify_pos +
				'"></div>' +
				'<div class="pm-file-data">' +
				'<div style="width: 100%;" id="progressbar uploadCSVUploadProgress" class="progress progress-success progress-striped active hide">' +
				'<div class="bar progressvalue" style="width: %;"></div>' +
				'</div>' +
				'<div class="progressbar" ><div class="progress progress-success progress-striped active"><div class="bar" style="" class="progressvalue"></div></div></div>' +
				//'<span class="cancel" >&nbsp;</span>' +
				'<div class="pm-file-icon">' +
				'<img src="' +
				phpmelody.admin_url +
				'/img/ico-file-csv.png" height="34" width="34" alt="" class="pull-right">' +
				'</div>' +
				'<div class="pm-file-attr">' +
				'<ul class="list-unstyled">' +
				'<li><strong>File name</strong>: ' +
				file.name +
				'</li>' +
				'<li><strong>File size</strong>: ' +
				Math.round(file.size / 1024) +
				' KB </li>' +
				'</ul>' +
				'</div>' +
				'</div>' +
				'<div class="clearfix"></div>' +
				'</li>';
				
				$('input[name="selected-csv-filename"]').val(file.name);
				
				$('#upload-csv-log').append(listitem);
				$('#selected-file-' + pm_fileupload_notify_pos + ' .cancel').bind('click', function(){
					$('#selected-file-' + pm_fileupload_notify_pos).slideUp('fast');
				});
				
				// hook cancel button action
				$('#selected-file-' + pm_fileupload_notify_pos + ' .cancel').on('click', {
					filename: file.name,
					files: data.files
				}, function(e){
					e.preventDefault();
					data.abort();
					data.files.splice(parseInt($(this).attr('data-selected-file-id')), 1);
					$('#selected-file-' + pm_fileupload_notify_pos).fadeOut('fast');
				});
				
				pm_fileupload_notify_pos++;
			}
		});
		
		if (data.files.length > 0) {
			data.submit();
		}
	})
	.bind('fileuploadsubmit', function (e, data) {
		$.each(data.files, function (index, file) {
			$('#import-csv-upload-file-form').find('.import-ajax-loading-animation').show();
		});
	})
	.bind('fileuploadstart', function (e, data) {
	})
	.bind('fileuploadprogress', function (e, data) {
		var percentage = parseInt(data.loaded / data.total * 100, 10);
		var file = data.files[0];
		
		$('#selected-file-'+ file.pm_file_id +' .bar').css('width', percentage + '%');
	})
	.bind('fileuploadprogressall', function (e, data) {
		//var percentage = parseInt(data.loaded / data.total * 100, 10);
	})
	.bind('fileuploaddone', function (e, data) {
		
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);
			var node = $('#selected-file-' + file.pm_file_id);
			node.find('div.progress').css('width', '100%');
			node.find('span.progressvalue').text('100%');
			
			if (server_response === null) {
				// note: keep this debug mode in production
				console.log(data.jqXHR);
				
				if (data.jqXHR.status == 200 && data.jqXHR.responseText == "") {
					console.log('Blank response is most likely coming from a misconfigured `post_max_size` and `upload_max_filesize` directives.');
				}
			}
			
			setTimeout(function(){
				$('#selected-file-' + file.pm_file_id +' .progressbar').fadeOut('slow');
			}, 700);
			$('#import-csv-upload-file-form').find('.import-ajax-loading-animation').hide();
			
			if (server_response.success == true) {
				node.find('.pm-file-data-append-' + file.pm_file_id).html(server_response.html);
				$('#import-csv-upload-file-form').find('.import-ajax-loading-animation').hide();
			} else {
				$.notify({title: file.name, message: server_response.msg}, {type: server_response.alert_type});
				
				$('#selected-file-' + file.pm_file_id).fadeOut('slow');
			}
		});
	})
	.bind('fileuploadfail', function (e, data) {
		if (data.errorThrown != 'abort') {
			$.each(data.files, function (index, file) {
				var error_message = file.error || data.errorThrown || 'Unknown Error';
				$.notify({message: file.name +': '+ error_message}, {type: 'error'});
			});
		}
	});
});
