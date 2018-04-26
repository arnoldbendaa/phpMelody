<script type="text/javascript">

	$('.alert-success, .alert-warning, .alert-error').addClass('animated flash');

	// bind top #loading div for all ajax requests
	$(document).ajaxStart(function() {
		phpmelody.doing_ajax = true;
		$('#loading').show();
	});
	$(document).ajaxStop(function() {
		phpmelody.doing_ajax = false;
		$('#loading').hide();
	});
	$(document).ajaxSend(function(event, jqxhr, settings) {
	});
	$(document).ajaxError(function(event, jqxhr, settings, thrownError) {
		if (thrownError != "" && thrownError != "abort" && thrownError != "canceled" && jqxhr.status != 0) { 
			$.notify({message: "Action could not be completed.<br />Please reload the page and try again.<br /><br /><code>AJAX error: " + jqxhr.status + " "+ jqxhr.responseText +"<br />" + thrownError +"</code>"}, {type: 'error'});
		}
	});
	
	// Loading ...
	document.getElementById("loading").style.display="block";
	
	function addLoadEvent(func) {
	  var oldonload = window.onload;
	  if (typeof window.onload != 'function') {
		window.onload = func;
	  } else {
		window.onload = function() {
		  if (oldonload) {
			oldonload();
		  }
		  func();
		}
	  }
	}
	
	addLoadEvent(function() {
	  document.getElementById("loading").style.display="none";
	});

	var adminPrimary = $('.content').height()+100;
	$('#adminSecondary').css({'min-height': ''+ adminPrimary +'px' });

	//$('#adminSecondary').css('position','fixed');
	$(window).on('resize', function(){
		var win = $(this); //this = window
		if (win.height() <= 860) {
		$('#adminSecondary').css('position','absolute');
		}
		if(win.height() >= 860) {
			$('#adminSecondary').css('position','fixed');
		}
	});	
	
	//===== Color picker =====//
<?php if($load_colorpicker == 1): ?>

	$("#bg_bar").colorpicker().on("changeColor", function(ev){
		hex = ev.color.toHex();
		$("#bg_bar").val(hex);
	});
	$("#play_timer").colorpicker().on("changeColor", function(ev){
		hex = ev.color.toHex();
		$("#play_timer").val(hex);
	});
<?php endif; ?>

<?php /*if($load_uniform == 1): ?>
	$("input:file").uniform();
<?php endif;*/ ?>

	$('#test-email').click(function(event){
		event.preventDefault();
		$('#loader').show();
		$.ajax({
			url: 'admin-ajax.php',
			data: {
				"p": 'settings',
				"do": 'testmail',
				"mail_server"	: $('input[name=mail_server]').val(),
				"mail_port"		: $('input[name=mail_port]').val(),
				"mail_user"		: $('input[name=mail_user]').val(),
				"mail_pass"		: $('input[name=mail_pass]').val(),
				"mail_smtp"		: $('input[name=issmtp]:checked').val(),
				"contact_email"	: $('input[name=contact_mail]').val()
			},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$.notify({message: data.msg}, {type: data.alert_type});
				$('#loader').hide();
			}
		});
		return false;
	});
	
	$('#test-fb-app').click(function(event){
		event.preventDefault();
		$('#fb-loader').show();
		$.ajax({
			url: 'admin-ajax.php',
			data: {
				"p": 'settings',
				"do": 'test-fb-app',
				"oauth_fb_app_id": $('input[name=oauth_fb_app_id]').val(),
				"oauth_fb_app_secret": $('input[name=oauth_fb_app_secret]').val()
			},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$.notify({message: data.msg}, {type: data.alert_type});
				$('#fb-loader').hide();
			}
		});
		return false;
	});

	$(".editadzone").click(function(){ 
		$("#adzoneid").val($(this).data('id'));
	});

<?php if($load_scrolltofixed == 1): ?>
$(document).ready(function() {
	$('#import-search-videos-form, #import-user-search-form').scrollToFixed({ 
		marginTop: $('.wide-header').outerHeight(true),
		limit: $('#stack-controls'),
		preFixed: function() { $(this).css({'background-color' : '#FFF', 'box-shadow' : '0 2px 4px rgb(200, 200, 200)', 'padding' : '22px 15px', 'border' : '1px solid #ddd', 'border-top' : 'none' }); },
		postFixed: function() { $(this).css({'background-color': '', 'box-shadow' : 'none', 'padding' : '10px 0px', 'border' : 'none', 'background-image' : 'none'}); }
	});
	 
	$('#stack-controls').scrollToFixed({  
		bottom: 0,
		limit: $('#stack-controls').offset().top,
		preFixed: function() { $(this).css({'background-color' : '#FFF', 'box-shadow' : '0 -2px 2px #eee', 'padding' : '5px 15px', 'border' : '1px solid #ddd', 'border-bottom' : 'none', 'background-image' : '-moz-linear-gradient(top, #fff, #f4f4f4)' }); },
		postFixed: function() { $(this).css({'background-color': '', 'box-shadow' : 'none', 'padding' : '10px 0px', 'border' : 'none', 'background-image' : 'none'}); }
	});

	
});
/*
	$('#import-nav').scrollToFixed({ 
		marginTop: $('header').outerHeight(),
        preFixed: function() { $(this).css({'background-color' : '#FFF', 'box-shadow' : '0 2px 2px #ddd', 'padding-right' : '10px' }); $(this).find('h2').css('visibility', 'hidden'); },
        postFixed: function() { $(this).css({'background-color': '', 'box-shadow' : 'none', 'padding-right' : '0px'}); $(this).find('h2').css('visibility', 'visible'); }
	});
*/
	/*$('#sideNav').scrollToFixed({ bottom: 0, limit: $('#wrapper').offset().top });*/

<?php endif; ?>
<?php if($load_tagsinput == 1): ?>
	var tidyTags = function(e){
		var tags = ($(e.target).val() + ',' + e.tags).split(',');
		var target = $(e.target);
		target.importTags('');
		for (var i = 0, z = tags.length; i<z; i++) {
			var tag = $.trim(tags[i]);
			if (!target.tagExist(tag)) {
				target.addTag(tag);
			}
		}
	}
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
<?php endif; ?>

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


<?php if($load_ibutton == 1): ?>
	$(document).ready(function() {
		$('.on_off :checkbox').iButton({
			duration: 80,
			labelOn: "",
			labelOff: "",
			enableDrag: false 
		});

	    $("#checkall").click(function () {
			$('.on_off :checkbox').iButton("repaint");
			if($('.on_off :checkbox').is(":checked")) {
			  $('.video-stack').addClass("stack-selected");
			} else {
			  $('.video-stack').removeClass("stack-selected");
			}
	    });
	});
<?php endif; ?>

	$('.on_off :checkbox').change(function () {
		if ($(this).attr("checked")) {
			$(this).closest('.video-stack').addClass("stack-selected");
		} 
		else {
		$(this).closest('.video-stack').removeClass("stack-selected");
		}
	});

	$(document).ready(function () {
		 $("input[id^='featured'][type=checkbox]").change(function () { $('#value-featured').text('updated').addClass('label label-success'); });
		 $("input[id^='visibility'][type=radio]").change(function () { $('#value-visibility').text('updated').addClass('label label-success'); });
		 $("input[id^='restricted'][type=radio]").change(function () { $('#value-register').text('updated').addClass('label label-success'); });
		 $("input[class^='pubDate']").change(function () { $('#value-publish').text('updated').addClass('label label-success'); });
		 $("select[class^='pubDate']").change(function () { $('#value-publish').text('updated').addClass('label label-success'); });
		 $("input[id^='site_views_input']").change(function () { $('#value-views').text('updated').addClass('label label-success'); });
		 $("input[id^='submitted']").change(function () { $('#value-submitted').text('updated').addClass('label label-success'); });
		 $("input[id^='allow_comments']").change(function () { $('#value-comments').text('updated').addClass('label label-success'); });
		 $("input[id^='yt_length']").change(function () { $('#value-yt_length').text('updated').addClass('label label-success'); });
		 $("input[id^='show_in_menu'][type=radio]").change(function () { $('#value-showinmenu').text('updated').addClass('label label-success'); });
		 $("input[id^='allow_embedding']").change(function () { $('#value-embedding').text('updated').addClass('label label-success'); });
		 $("input[name^='channel_verified']").change(function () { $('#value-channel-verified').text('updated').addClass('label label-success'); });
		 $("input[name^='channel_featured']").change(function () { $('#value-channel-featured').text('updated').addClass('label label-success'); });
		 $("input[name^='channel_slug']").change(function () { $('#value-channel-permalink').text('updated').addClass('label label-success'); });

		var cc = $.cookie('list_grid');
		if (cc == 'g') {
			$('#vs-grid').addClass('vs-grid');
		} else {
			$('#vs-grid').removeClass('vs-grid');
		}
/*
		var list_filter = $.cookie('list_filter');
		if (list_filter == null) {
			$('#showfilter-content').show();
		} else {
			$('#showfilter-content').hide();
		}	
*/

<?php if ($load_import_js) :?>


	$('select[name="data_source"]').change(function(){
		
		var categories_youtube = '<?php echo $select_category_youtube_inner_html; ?>';
		var categories_dailymotion = '<?php echo $select_category_dailymotion_inner_html; ?>';
		
		if ($('select[name="data_source"] option:selected').val() == 'youtube') {
			$('select[name="search_category"]').html(categories_youtube).removeAttr('disabled');
			$('select[name="search_duration"]').removeAttr('disabled');
			$('select[name="search_time"]').removeAttr('disabled');
			$('select[name="search_language"]').removeAttr('disabled');
			$('select[name="search_license"]').removeAttr('disabled');
			$('input[name="search_hd"]').removeAttr('disabled');
			$('input[name="search_3d"]').removeAttr('disabled');
		}
		else if ($('select[name="data_source"] option:selected').val() == 'dailymotion') {
			$('select[name="search_category"]').html(categories_dailymotion).removeAttr('disabled');
			$('select[name="search_duration"]').removeAttr('disabled');
			$('select[name="search_time"]').removeAttr('disabled');
			$('select[name="search_language"]').removeAttr('disabled');
			$('select[name="search_license"]').attr('disabled', 'disabled');
			$('input[name="search_hd"]').removeAttr('disabled');
			$('input[name="search_3d"]').removeAttr('disabled');
		}
		else if ($('select[name="data_source"] option:selected').val() == 'vimeo') {
			$('select[name="search_category"]').html(categories_youtube).attr('disabled', 'disabled');
			$('select[name="search_duration"]').attr('disabled', 'disabled');
			$('select[name="search_time"]').attr('disabled', 'disabled');
			$('select[name="search_language"]').attr('disabled', 'disabled');
			$('select[name="search_license"]').attr('disabled', 'disabled');
			$('input[name="search_hd"]').attr('disabled', 'disabled');
			$('input[name="search_3d"]').attr('disabled', 'disabled');
		}
	});

<?php endif; ?>

	}); // end $(document).ready()

	$('#stacks').click(function() {
		$('#vs-grid').fadeOut(200, function() {
			$(this).addClass('vs-grid').fadeIn(200);
			$.cookie('list_grid', 'g');
		});
		return false;
	});
	
	$('#list').click(function() {
		$('#vs-grid').fadeOut(200, function() {
			$(this).removeClass('vs-grid').fadeIn(200);
			$.cookie('list_grid', null);
		});
		return false;
	});


	$("[rel=tooltip]").tooltip();
	$("[rel=popover]").popover();

	$('#myModal').modal({
	  keyboard: true,
	  show: false
	});

	$('#searchVideos').click(function() {
		$(".searchLoader").css({"display" : "inline"});
	});

	$('#addvideo_direct_submit').click(function() {
		$(".addLoader").css({"display" : "inline"});
	});	

	$('#submitFind').click(function() {
		$('#loading-large').show().find('.loading-msg').replaceWith('Searching');
		$(".pm-tables").css({"opacity" : "0.5"});
		$(".findIcon").css({"display" : "none"});
		$(".findLoader").css({"display" : "inline"});
	});

	$('.pagination > ul > li > a').click(function() {
		$('#loading-large').show();
		$(".pm-tables td").css({"opacity" : "0.5"});
		$(".tableFooter").css({"opacity" : "1.0"});
		$("#vs-grid").css({"opacity" : "0.5"});
	});

	
<?php if($load_chzn_drop == 1): ?>
	$(document).ready(function() {
		$('.category_dropdown').addClass("chzn-select");
		$(".chzn-select").chosen({width: "100%"});
		$(".chzn-select-deselect").chosen({allow_single_deselect:true});
	});
<?php endif; ?>

	$('#adminSecondary').css({'height': (($('#wrapper').height()))+'px'});
	$(window).resize(function () {
		$('#adminSecondary').css({'height': (($('#wrapper').height()))+'px'});
	});

   /* $('.content').css({'height': (($('#adminSecondary').height()))+'px'});	*/


	$(document).ready(function() {	
		$('#sideNav li.has-subcats').hover(function(){
			if ( ! $(this).hasClass('active')) {
			$('ul.pm-sub-menu', this).stop().doTimeout( 'hover', 00, 'addClass', 'pm-sub-menu-side' );
			}
		}, function() {
			if ( ! $(this).hasClass('active')) {
			$('ul.pm-sub-menu', this).stop().doTimeout( 'hover', 0, 'removeClass', 'pm-sub-menu-side' );
			}
		});
	});
	
	/*
	$(document).ready(function() {	
		$('li.has-subcats').hover(function(){
			if ( ! $(this).hasClass('active')) {
				//$('ul', this).stop().doTimeout( 'hover', 500, 'slideDown', 250 );
				$('ul', this).stop().doTimeout( 'hover', 250, 'slideDown', 250 ); //@since v2.1 
			}
		}, function(){
			if ( ! $(this).hasClass('active')) {
				//$('ul', this).stop().doTimeout( 'hover', 0, 'slideUp', 300 );
				$('ul', this).stop().doTimeout( 'hover', 200, 'slideUp', 300 ); //@since v2.1
			}
		});
	});
*/
	//$('#showfilter').click(function() { $('#showfilter-content').slideToggle(100, function() { $.cookie('list_filter', 'open'); }); }); 
	/*
	$('#showfilter').click(function() {
		$('#showfilter-content').slideToggle(100, function() {
			if ($.cookie('list_filter') == null) {
				$.cookie('list_filter','close');
			} else {
				$.cookie('list_filter', null);
			}
		});
		return false;
	});
	*/
	$('#import-options').click(function() { 
		$('#import-opt-content').toggle(); 
		$(this).toggleClass('active'); 

		if ($(this).is('.active')) {
			$('.video-stack').addClass('stack-gray');
		} else {
			$('.video-stack').removeClass('stack-gray');
		}
		
	});
	//$('#import-options').click(function() { $('#import-opt-content').slideToggle('fast'); });

	$('#show-comments').click(function() { $('#show-opt-comments').slideToggle('fast'); return false; });
	$('#show-restriction').click(function() { $('#show-opt-restriction').slideToggle('fast'); return false; });
	$('#show-visibility').click(function() { $('#show-opt-visibility').slideToggle('fast'); return false; });
	$('#show-publish').click(function() { $('#show-opt-publish').slideToggle('fast'); return false; });
	$('#show-thumb').click(function() { $('#show-opt-thumb').slideToggle(50); return false; });
	$('#show-featured').click(function() { $('#show-opt-featured').slideToggle('fast'); return false; });
	$('#show-user').click(function() { $('#show-opt-user').slideToggle('fast'); return false; });
	$('#show-views').click(function() { $('#show-opt-views').slideToggle('fast'); return false; });
	$('#show-vs1').click(function() { $('#show-opt-vs1').slideToggle('fast'); return false; });
	$('#show-vs2').click(function() { $('#show-opt-vs2').slideToggle('fast'); return false; });
	$('#show-vs3').click(function() { $('#show-opt-vs3').slideToggle('fast'); return false; });
	$('#show-duration').click(function() { $('#show-opt-duration').slideToggle('fast'); return false; });
	$('#show-showinmenu').click(function() { $('#show-opt-showinmenu').slideToggle('fast'); return false; });
	$('#show-help-assist').click(function() { $('#help-assist').slideToggle('fast'); $('#show-help-assist').toggleClass('opac5'); return false; });
	$('#show-help-link-assist').click(function() { $('#help-assist').slideToggle('fast'); $('#show-help-link-assist').toggleClass('opac5'); return false; });	
	$('#show-embedding').click(function() { $('#show-opt-embedding').slideToggle('fast'); return false; });

	$('#show-channel-featured').click(function() { $('#show-opt-featured').slideToggle('fast'); return false; });
	$('#show-channel-verified').click(function() { $('#show-opt-verified').slideToggle('fast'); return false; });
	$('#show-channel-permalink').click(function() { $('#show-opt-permalink').slideToggle('fast'); return false; });


<?php if($load_prettypop == 1): ?>
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
		callback: function(){}
	});
<?php endif; ?>

	$('a[id^="show-more-"]').click(function(){
		var id = $(this).attr('id').match(/\d+$/);
		$(this).hide();
		$('#excerpt-'+id).hide();
		$('#full-text-'+id).show();
		$('#show-less-'+id).show();
		return false;
	});
	$('a[id^="show-less-"]').click(function(){
		var id = $(this).attr('id').match(/\d+$/);
		$(this).hide();
		$('#full-text-'+id).hide();
		$('#show-more-'+id).show();
		$('#excerpt-'+id).show();
		return false;
	});
	
	$(document).ready(function() {
		$('[placeholder]').focus(function() {
		  var input = $(this);
		  if (input.val() == input.attr('placeholder')) {
			input.val('');
			input.removeClass('placeholder');
		  }
		}).blur(function() {
		  var input = $(this);
		  if (input.val() == '' || input.val() == input.attr('placeholder')) {
			input.addClass('placeholder');
			input.val(input.attr('placeholder'));
		  }
		}).blur();
		$('[placeholder]').parents('form').submit(function() {
		  $(this).find('[placeholder]').each(function() {
			var input = $(this);
			if (input.val() == input.attr('placeholder')) {
			  input.val('');
			}
		  })
		});
	});

	function validateFormOnSubmit(theForm, say_reason) {

		var counter = 0;

		if ( ! say_reason) {
			say_reason = 'Please fill in the required fields (highlighted)';
		}

		$("input,textarea").each(function () {
			if ($(this).attr('id') == "must") {
				if ($(this).attr("value").length == 0) {
					$(this).css("background", "#FFD2D2");
					counter++;
				}
				else {
					$(this).css("background", "#FFFFFF");
				}
			}
		});

		if ($('select[name="category[]"]').length > 0 && $('select[name="category[]"]').val() == null) {
			if (counter == 0) {
				$.notify({message: 'Please select at least one category'}, {type: 'error'}); 
				$('.chosen-container').addClass('chosen-container-active form-required-field');
				$('.chosen-drop').show();
				return false;
			} else {
				counter++;
			}
		}

		if (counter > 0) {
			$.notify({message: say_reason}, {type: 'error'});
			return false;
		}
		return true;
	}


	function validateSearch(b_on_submit){
		if(document.forms['search'].keywords.value == '' || document.forms['search'].keywords.value == 'search'){
			alert('You did not enter a search term. Please try again.');
			if(b_on_submit == 'true')
				return false;
		}
		else{
			document.forms['search'].submit();
		}
	}
	function confirm_delete_all() {
		var confirm_msg = "You are about to delete all these selected items. Click 'Cancel' to stop or 'OK' to continue";	 // refers to articles, videos and users
		var response = false;
		if (confirm(confirm_msg)) {
		} else {
			return false;
		}
	}
<?php if ($load_settings_theme_resources) : ?>
 $(document).ready(function(){
	$('#btn-remove-logo').click(function(){
		 if (confirm('Are you sure you want to delete the current logo?')) {
			$.ajax({
				url: '<?php echo _URL .'/'. _ADMIN_FOLDER .'/admin-ajax.php'?>',
				data: {
					p: "layout-settings",
					"do": "delete-logo"
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					$('#btn-remove-logo').hide();
					$('#settings-logo-container').html(" ");
				}
			});
		}
		return false;
	});
});
<?php endif;?>

$(document).ready(function() {
 $('.tablesorter tr')
  .filter(':has(:checkbox:checked)')
	.addClass('selected')
  .end()
 .click(function(event) {
	var disallow = { "SMALL":1, "A":1, "IMG":1, "INPUT":1, "I":1, "TH":1, "TEXTAREA":1, "SPAN":1 }; 
  if(!disallow[event.target.tagName]) {
   $(':checkbox', this).trigger('click');
  }
 })
  .find(':checkbox')
  .click(function(event) {
   $(this).parents('tr:first').toggleClass('selected');
  });

  $("#selectall").click(function () {
	if($('.tablesorter tr').filter(':has(:checkbox:checked)').removeClass('selected').end()) {
		$('.tablesorter tr').not(".tablePagination").toggleClass("selected");
	}
  });
  
  // inline add new category
  $('#inline_add_new_category').click(function(){
  	$('#inline_add_new_category_form').slideToggle(0);
	$('#add_category_name').focus();
	return false;
  });
  
  $('button[name="add_category_submit_btn"]').click(function(event){
  	event.preventDefault();
	
	$('#add_category_response').html();
	var current_page = "<?php $tmp_parts = explode('/', $_SERVER['SCRIPT_NAME']); $tmp_script = array_pop($tmp_parts); echo $tmp_script; ?>";
	var category_name = $('input[name="add_category_name"]').val();
	var category_slug = $('input[name="add_category_slug"]').val();
	var parent_id = $('input[name="add_category_parent_id"]').val();
	var chzn_is_on = false;
	<?php if ($load_chzn_drop) : ?>
	chzn_is_on = true;
	<?php endif;?>

	// check if required fields are set
	if (category_name === "" || category_name == $('input[name="add_category_name"]').attr('placeholder')) {
		$('input[name="add_category_name"]').trigger('focus');
	} else if (category_slug === "" || category_slug == "Slug") {
		$('input[name="add_category_slug"]').trigger('focus');
	} else {
		if (current_page == "article_manager.php") {
			var ajax_do = "add-article-category"; 
			var parent_select_name = 'categories[]';
		} else {
			var ajax_do = "add-video-category";
			var parent_select_name = 'category[]';
		}

		$.ajax({
			url: "admin-ajax.php",
			data: {
			    p: "manage-categories",
			    name: $('input[name=add_category_name]').val(),
			    tag: $('input[name=add_category_slug]').val(),
			    category: $('select[name=add_category_parent_id]').val(),
				current_selection: $('select[name="'+ parent_select_name +'"]').val(),
				"do": ajax_do
			},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				if (data.success == false) {
					$('#add_category_response').html(data.html);
				} else {
					// remove current Chosen instance html (no destroy method provided)
					if (chzn_is_on) {
						$(".chzn-select").removeAttr("style", "").removeClass("chzn-done").data("chosen", null).next().remove();
					}
					
					// update parent category dropdown   
					$('select.category_dropdown').replaceWith(data.html);

					$('#add_category_response').html('');
					
					// update Create-in category dropdown
					$('select[name=add_category_parent_id]').replaceWith(data.create_category_select_html);
					
					// clear Create-new-category form data
					$('input[name=add_category_name]').val("");
					$('input[name=add_category_slug]').val("");
					
					// create new Chosen instance for updated parent category dropdown
					if (chzn_is_on) {
						$('.category_dropdown').addClass("chzn-select");
						$(".chzn-select").chosen({width: "100%"});
						$(".chzn-select-deselect").chosen({allow_single_deselect:true});
					}
				}
			}
		});
	}
	return false;
  });
  
  $('.category_mark_featured').click(function(event){
  	event.preventDefault();
	
	var parent = $(this);
	
	$.ajax({
		url: phpmelody.admin_ajax_url,
		data: {
			"p": "manage-categories",
			"do": "mark-featured",
			"id": $(this).attr('data-category-id')
		},
		type: 'POST',
		dataType: 'json',
		beforeSend: function() {
			$.notifyClose();
		}
	}).done(function(data) { 
		if (data.success == false) {
			$.notify({message: data.msg}, {type: data.alert_type, delay: 2000});
			return false;
		}
		
		if (parent.hasClass('is_featured_category')) {
			parent.removeClass('is_featured_category')
		} else {
			parent.addClass('is_featured_category');
			$.notify({message: data.msg}, {type: data.alert_type, delay: 2000});
		}
		return false;
	});
  });
  
  $('#delete-category-image').click(function(event){
  	event.preventDefault();
	
	$.ajax({
		url: phpmelody.admin_ajax_url,
		data: {
			"p": "manage-categories",
			"do": "delete-image",
			"id": $(this).attr('data-category-id'),
			"_pmnonce": '_admin_catmanager',
			"_pmnonce_t": $('#_pmnonce_t_admin_catmanager').val()
		},
		type: 'POST',
		dataType: 'json',
		beforeSend: function() {
			$.notifyClose();
		}
	}).done(function(data) { 
		if (data.success == false) {
			$.notify({message: data.msg}, {type: data.alert_type, delay: 2000});
			return false;
		}
		
		$('#category-image-container').html('<div id="show-cat-cover"><img src="img/no-category-thumbnail.png"></div>');
		$('input[name="image"]').val('');
		
		if (data._pmnonce != '') {
			$('#_pmnonce_t_admin_catmanager').val(data._pmnonce_t);
		}
		
		return false;
	});
  });
});

/*
 * Fileupload - START
 */
$(function () {
	'use strict';
	/*
	 * Modal video uploader
	 */
	$('#upload-video-modal-btn').fileupload(
		$.extend({}, pm_fileupload_single_options, {
			dropZone: $('#upload-video-modal-dropzone'),
			acceptFileTypes: pm_media_file_types
		})
	)
	.bind('fileuploadadd', function (e, data) {
		
		$('input[name="upload-type"]').val('');
		$('input[name="do"]').val('upload-file');
		
		pm_fileupload_add(e, data);
		data.formData = $('#upload-video-modal-form').serializeArray();
		
		if (data.files.length > 0) {
			data.submit();
		}
	})
	.bind('fileuploadsubmit', function (e, data) {
		pm_fileupload_submit(e, data);
	})
	.bind('fileuploadstart', function (e, data) {
		pm_fileupload_start(e, data);
	})
	.bind('fileuploadprogress', function (e, data) {
		pm_fileupload_progress(e, data);
	})
	.bind('fileuploadprogressall', function (e, data) {
		pm_fileupload_progress_all(e, data);
	})
	.bind('fileuploaddone', function (e, data) { 
		pm_fileupload_done(e, data);
		
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);
			
			if (server_response.success == true) {
				pm_fileupload_notifications[file.name].update('message', 'Uploaded! Redirecting...');
				window.location.href = phpmelody.admin_url + '/addvideo.php?step=2&filename='+ server_response.filename;
			}
		});
	})
	.bind('fileuploadfail', function (e, data) {
		pm_fileupload_fail(e, data);
	});
	
	/*
	 * WYSIWYG image uploader
	 */
	$('#upload-file-wysiwyg-btn').fileupload(
		$.extend({}, pm_fileupload_multi_options, {
			dropZone: $('#textarea-dropzone')
		})
	)
	.bind('fileuploadadd', function (e, data) {
		
		$('input[name="upload-type"]').val('');
		$('input[name="do"]').val('upload-image');
		
		pm_fileupload_add(e, data);
		
		if (data.files.length > 0) {
			// wait for $.notify's animate.enter to finish before uploading
			setTimeout(function(){
				data.submit();
			}, 850);
		}
	})
	.bind('fileuploadsubmit', function (e, data) {
		pm_fileupload_submit(e, data);
	})
	.bind('fileuploadstart', function (e, data) {
		pm_fileupload_start(e, data);
	})
	.bind('fileuploadprogress', function (e, data) {
		pm_fileupload_progress(e, data);
	})
	.bind('fileuploadprogressall', function (e, data) {
		pm_fileupload_progress_all(e, data);
	})
	.bind('fileuploaddone', function (e, data) {
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);
			
			if (server_response.success == true) {
				if ($("#wysiwygtextarea-WYSIWYG").length > 0) {
					$("#wysiwygtextarea-WYSIWYG").contents().find("body").append(server_response.html);
				} else if ($("#textarea-WYSIWYG").length > 0) {
					var textarea = $("#textarea-WYSIWYG").val();
					$("#textarea-WYSIWYG").val(textarea + server_response.html);
				}
			}
		});
		
		pm_fileupload_done(e, data);
	})
	.bind('fileuploadfail', function (e, data) {
		pm_fileupload_fail(e, data);
	});
	
	/*
	 * Video/Category/etc. thumbnail uploader
	 */
	$('#upload-video-image-btn').fileupload(
		$.extend({}, pm_fileupload_single_options, {
			dropZone: $('#video-thumb-dropzone')
		})
	)
	.bind('fileuploadadd', function (e, data) {
		
		$('input[name="upload-type"]').val('video-thumb');
		$('input[name="do"]').val('upload-image');
		
		pm_fileupload_add(e, data);
		
		if (data.files.length > 0) {
			data.submit();
		}
	})
	.bind('fileuploadsubmit', function (e, data) {
		pm_fileupload_submit(e, data);
	})
	.bind('fileuploadstart', function (e, data) {
		pm_fileupload_start(e, data);
	})
	.bind('fileuploadprogress', function (e, data) {
		pm_fileupload_progress(e, data);
	})
	.bind('fileuploadprogressall', function (e, data) {
		pm_fileupload_progress_all(e, data);
	})
	.bind('fileuploaddone', function (e, data) { 
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);			
			
			if (server_response.success == true) {
				$('#video-thumb-container').html(server_response.html);
			}
		});
		
		pm_fileupload_done(e, data);
	})
	.bind('fileuploadfail', function (e, data) {
		pm_fileupload_fail(e, data);
	});
	
	/*
	 * Video source file uploader
	 */
	$('#upload-video-source-btn').fileupload(
		$.extend({}, pm_fileupload_single_options, {
			dropZone: $('#video-file-dropzone'),
			acceptFileTypes: pm_media_file_types
		})
	)
	.bind('fileuploadadd', function (e, data) {
		
		$('input[name="upload-type"]').val('video');
		$('input[name="do"]').val('upload-file');
		
		pm_fileupload_add(e, data);
		
		if (data.files.length > 0) {
			data.submit();
		}
	})
	.bind('fileuploadsubmit', function (e, data) {
		pm_fileupload_submit(e, data);
	})
	.bind('fileuploadstart', function (e, data) {
		pm_fileupload_start(e, data);
	})
	.bind('fileuploadprogress', function (e, data) {
		pm_fileupload_progress(e, data);
	})
	.bind('fileuploadprogressall', function (e, data) {
		pm_fileupload_progress_all(e, data);
	})
	.bind('fileuploaddone', function (e, data) { 
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);
			
			if (server_response.success == true) {
				$('#showFlv').html(server_response.html);
			}
		});
		
		pm_fileupload_done(e, data);
	})
	.bind('fileuploadfail', function (e, data) {
		pm_fileupload_fail(e, data);
	});

	/*
	 * Video subtitles uploader
	 */
	$('#upload-subtitle-btn').fileupload(
		$.extend({}, pm_fileupload_single_options, {
			dropZone: $('#subtitle-dropzone'),
			acceptFileTypes: /(\.|\/)(vtt|srt)$/i
		})
	)
	.bind('fileuploadadd', function (e, data) {
		// validate language select
		var language = $("#language").val();
		if (language == '') {
			$.notify({message: 'Select a LANGUAGE before uploading a subtitle file.'}, {type: 'error'});
			$('input[name="language"]').trigger('focus');
			
			return false;
		}
		
		$('input[name="upload-type"]').val('subtitle');
		$('input[name="do"]').val('upload-file');
		$('input[name="language"]').val(language);
		
		pm_fileupload_add(e, data);
		
		if (data.files.length > 0) {
			data.submit();
		}
	})
	.bind('fileuploadsubmit', function (e, data) {
		pm_fileupload_submit(e, data);
	})
	.bind('fileuploadstart', function (e, data) {
		pm_fileupload_start(e, data);
	})
	.bind('fileuploadprogress', function (e, data) {
		pm_fileupload_progress(e, data);
	})
	.bind('fileuploadprogressall', function (e, data) {
		pm_fileupload_progress_all(e, data);
	})
	.bind('fileuploaddone', function (e, data) { 
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);
			
			if (server_response.success == true) {
				$('#showSubtitle').html(server_response.html);
			}
		});
		
		pm_fileupload_done(e, data);
	})
	.bind('fileuploadfail', function (e, data) {
		pm_fileupload_fail(e, data);
	});
	
	/*
	 * Category Thumbnail
	 */
	<?php if ($showm == '3') : ?>
	$('#upload-category-image-btn').fileupload(
		$.extend({}, pm_fileupload_single_options, {
			dropZone: $('#category-image-dropzone')
		})
	)
	.bind('fileuploadadd', function (e, data) {
		$('input[name="upload-type"]').val('category-image');
		$('input[name="do"]').val('upload-image');
		
		pm_fileupload_add(e, data);
		
		if (data.files.length > 0) {
			data.submit();
		}
	})
	.bind('fileuploadsubmit', function (e, data) {
		//data.formData = $('form[name="edit-category"]').serializeArray();
		pm_fileupload_submit(e, data);
	})
	.bind('fileuploadstart', function (e, data) {
		pm_fileupload_start(e, data);
	})
	.bind('fileuploadprogress', function (e, data) {
		pm_fileupload_progress(e, data);
	})
	.bind('fileuploadprogressall', function (e, data) {
		pm_fileupload_progress_all(e, data);
	})
	.bind('fileuploaddone', function (e, data) { 
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);
			
			if (server_response.success == true) {
				$('#category-image-container').html(server_response.html);
			}
		});
		
		pm_fileupload_done(e, data);
	})
	.bind('fileuploadfail', function (e, data) {
		pm_fileupload_fail(e, data);
	});
	<?php endif; ?>

	/*
	 * Site logo uploader
	 */ 
	<?php if ($showm == '8'): ?>
	$('#upload-logo-btn').fileupload(
		pm_fileupload_single_options
	)
	.bind('fileuploadadd', function (e, data) {
		$('input[name="upload-type"]').val('logo');
		$('input[name="do"]').val('upload-image');
		
		pm_fileupload_add(e, data);
		
		if (data.files.length > 0) {
			data.submit();
		}
	})
	.bind('fileuploadsubmit', function (e, data) {
		pm_fileupload_submit(e, data);
	})
	.bind('fileuploadstart', function (e, data) {
		pm_fileupload_start(e, data);
	})
	.bind('fileuploadprogress', function (e, data) {
		pm_fileupload_progress(e, data);
	})
	.bind('fileuploadprogressall', function (e, data) {
		pm_fileupload_progress_all(e, data);
	})
	.bind('fileuploaddone', function (e, data) { 
		$.each(data.files, function (index, file) {
			var server_response = $.parseJSON(data.jqXHR.responseText);
			
			if (server_response.success == true) {
				$('#settings-logo-container').html(server_response.html);
			}
		});
		
		pm_fileupload_done(e, data);
	})
	.bind('fileuploadfail', function (e, data) {
		pm_fileupload_fail(e, data);
	});
	<?php endif; ?>
});

// limit dropzone to container instead of the whole page
$(document).bind('drop dragover', function (e) {
	e.preventDefault();
});

// effects for multiple dropzones
$(document).bind('dragover', function(e){
 
	var dropZone = $('.upload-file-dropzone'), 
		foundDropzone, 
		timeout = window.dropZoneTimeout;
	
	if ( ! timeout) {
		dropZone.addClass('in');
	} else {
		clearTimeout(timeout);
	}
	var found = false, 
		node = e.target;
	do {
		if ($(node).hasClass('upload-file-dropzone')) {
			found = true;
			foundDropzone = $(node);
			break;
		}
		node = node.parentNode;
	}
	while (node != null);
	
	dropZone.removeClass('hover');
	
	if (found) {
		foundDropzone.addClass('hover');
	}
	
	window.dropZoneTimeout = setTimeout(function(){
		window.dropZoneTimeout = null;
		dropZone.removeClass('in hover');
	}, 100);
});
/*
 * Fileupload - END
 */

<?php if($load_tinymce == 1 && _SEOMOD): ?>

	// Permalink Adjuster
	
	//var inputJ = $('input[class="permalink-input"]').val().length;

	//$('input[class="permalink-input"]').attr('size', inputJ);

	var inputPermalink = $('input[class="permalink-input"]').val().length;
	if(inputPermalink > 0) {
		$('input[class="permalink-input"]').attr('size', inputPermalink).css('width', inputPermalink * 6.5 +'px');
	}
	// Permalink Adjuster
	/*

    var input = document.querySelectorAll('input[class="permalink-input"]');
	for(i=0; i<input.length; i++){
		input[i].setAttribute('size',input[i].getAttribute('placeholder').length);
	}


	function resizeInput() {
		if($(this).val().length > inputPermalink) {
			$(this).attr('size', $(this).val().length);
		}
	}
	$('input[class="permalink-input"]').keypress(resizeInput);
	*/


<?php endif; ?>
<?php if($config['keyboard_shortcuts'] == 1) : ?>
$(document).bind('keydown', 'shift+/', function() {
	$('#seeShortcuts').modal('show');
});
$(document).bind('keydown', 'c', function() {
	$('#addVideo').modal('show');
	$('#addVideo').on('shown', function () {
    	$('#yt_query').focus();
	});
});
$(document).bind('keydown', 'alt+s', function() {
	window.location = 'settings.php';
	return false;
});
$(document).bind('keydown', 'alt+l', function() {
	window.location = 'settings_theme.php';
	return false;
});
$(document).bind('keydown', 'alt+v', function() {
	window.location = 'videos.php';
	return false;
});
$(document).bind('keydown', 'alt+a', function() {
	window.location = 'articles.php';
	return false;
});
$(document).bind('keydown', 'alt+p', function() {
	window.location = 'pages.php';
	return false;
});
$(document).bind('keydown', 'alt+c', function() {
	window.location = 'comments.php';
	return false;
});
$(document).bind('keydown', 'shift+a', function() {
	$(".pm-tables").each(function(){
		if ( $('input:checkbox').attr('checked')) {
			$('input:checkbox').attr('checked', false);
		} else {
			$('input:checkbox').attr('checked', 'checked');
		}
	});
	if($('.tablesorter tr').filter(':has(:checkbox:checked)').removeClass('selected').end()) {
		$('.tablesorter tr').toggleClass("selected");
	}
	<?php if($load_ibutton == 1): ?>
	$("#import-opt-content").each(function(){
	
		if ( $('input[name^="video_ids"]:checkbox').attr('checked')) {
			$('input[name^="video_ids"]:checkbox').attr('checked', false);
		} else {
			$('input[name^="video_ids"]:checkbox').attr('checked', 'checked');
		}

		$('.on_off :checkbox').iButton("repaint");
		if($('.on_off :checkbox').is(":checked")) {
		  $('.video-stack').addClass("stack-selected");
		}else {
		  $('.video-stack').removeClass("stack-selected");
		}

	});
	<?php endif; ?>

});
$(document).bind('keydown', 'shift+s', function() {
	$('#form-search-input').focus();
	$('#form-search-input').css({"border":"1px solid #ddd"});	
	return false;
});
<?php endif; ?>


<?php if($load_dotdotdot): ?>
$(".item-comment").dotdotdot({
	height: 40,
});
<?php endif; ?>

<?php if($load_scrollpane): ?>
$(document).ready(function(){
	
	$(".widget-handle").click(function(){
		//$(this).next(".widget-inside").toggle();
		//$(this).toggleClass("widget-handle-active"); return false;
	});

	$(".widget-hide").click(function(){});

	$('.scroll-panel').each(
		function()
		{
			$(this).jScrollPane(
				{
					showArrows: false,
					horizontalGutter: 0,
				}
			);
			var api = $(this).data('jsp');
			var throttleTimeout;
			$(window).bind(
				'resize',
				function()
				{
					if (!throttleTimeout) {
						throttleTimeout = setTimeout(
							function()
							{
								api.reinitialise();
								throttleTimeout = null;
							},
							50
						);
					}
				}
			);
		}
	);
});
<?php endif; ?>


$(document).ready(function(){
	$('#meta_switch_select_input').click(function(event){
		event.preventDefault();
		
		$('#meta_key_select').hide();
		$(this).hide();
		$('input[name="meta_key"]').show();
		$('#meta_switch_input_select').show('50');
		
		return false;
	});
	
	$('#meta_switch_input_select').click(function(event){
		event.preventDefault();
		
		$('#meta_key_select').show();
		$(this).hide();
		$('input[name="meta_key"]').hide();
		$('#meta_switch_select_input').show('50');
		
		return false;
	});
	
	$('#add_meta_btn').click(function(event){
		event.preventDefault();
		
		$('#new-meta-error').html('');

		if (($('input[name="meta_key"]').val() != "" 
			 && $('input[name="meta_key"]').val() != $('input[name="meta_key"]').attr('placeholder')) 
			|| ($('select[name="meta_key_select"]').val() != "_nokey"))
		{
			var input_key = '';
			var input_value = '';
			
			if ($('input[name="meta_key"]').val() != "" 
				 && $('input[name="meta_key"]').val() != $('input[name="meta_key"]').attr('placeholder'))
			{
				input_key = $('input[name="meta_key"]').val();
			} else {
				input_key = $('select[name="meta_key_select"]').val();
			}
			
			if ($('input[name="meta_value"]').val() != $('input[name="meta_value"]').attr('placeholder')) {
				input_value = $('input[name="meta_value"]').val();
			}
			
			$.ajax({
				url: 'admin-ajax.php',
				data: {
					"p": "metadata",
					"do": "add-meta",
				    "meta_key": input_key,
				    "meta_value": input_value,
					"item_id": $('input[name="meta_item_id"]').val(),
				    "item_type": $('input[name="meta_item_type"]').val(),
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if (data['type'] == "error") {
						$('#new-meta-error').html(data['html']);
					} else {
					 
						$('#new-meta-placeholder').append(data['html']);
						
						// clear form
						$('input[name="meta_key"]').val("");
						$('input[name="meta_value"]').val("");
						
						bind_metadata_actions(data['meta_id']);
					}
				}
			});
		}
		else {
			$('input[name="meta_key"]').trigger('focus');
		}
		return false;
	});
	
	$('div[id^="meta-row-"]').click(function() {
			$(this).find('button.btn-normal').css('box-shadow','0 1px 3px #bee1be').removeClass("btn-normal").addClass("btn-success");
			$('input').change(function() {
				$(this).css('border', '1px solid #96ce96');
			});
	});
	
	bind_metadata_actions("");
});

function bind_metadata_actions(selector_meta_id)
{
	var update_btn_selector = '[id^="update_meta_btn_"]';
	var delete_btn_selector = '[id^="delete_meta_btn_"]';
	
	if (selector_meta_id != "")
	{
		update_btn_selector = '#update_meta_btn_' + selector_meta_id;
		delete_btn_selector = '#delete_meta_btn_' + selector_meta_id;
	}
	
	$(update_btn_selector).click(function(event){
		event.preventDefault();
		
		var meta_id = $(this).attr('id').replace( /^\D+/g, '');
		
		$('#update-response-'+ meta_id).html('');

		if ($('input[name="meta['+ meta_id +'][key]"]').val() === "") {
			$('input[name="meta['+ meta_id +'][key]"]').trigger('focus');
		} else {
			$.ajax({
				url: 'admin-ajax.php',
				data: {
					"p": "metadata",
					"do": "update-meta",
				    "meta_id": meta_id,
					meta_key: $('input[name="meta['+ meta_id +'][key]"]').val(),
				    meta_value: $('input[name="meta['+ meta_id +'][value]"]').val()
				},
				type: 'POST',
				dataType: 'json',
				success: function(data){
					if (data['type'] == 'success') {
						$('#update-response-'+ meta_id).html(data['html']).show().delay(2000).fadeOut("slow");
					} else { 
						$('#update-response-'+ meta_id).html(data['html']).show();
					}
				}
			});
		}
		return false;
	});
	
	$(delete_btn_selector).click(function(event){
		event.preventDefault();
		
		var meta_id = $(this).attr('id').replace( /^\D+/g, '');
		
		$.ajax({
			url: 'admin-ajax.php',
			data: {
				"p": "metadata",
				"do": "delete-meta",
			    "meta_id": meta_id
			},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				$('#meta-row-'+ meta_id).css('border-bottom', '5px solid #f4543c').slideUp();
			}
		});

		return false;
	});
}


<?php if ($load_sortable) : ?>
$(document).ready(function(){

    var changes_made = false;
		
    $('.sortable').nestedSortable({
        handle: 'div',
        items: 'li',
		itemsAttr: 'data-id',
        toleranceElement: '> div',
		placeholder: 'transport',
		forcePlaceholderSize: true,
		opacity: .6,
		update: function(){
			phpmelody.prevent_leaving_without_saving = true;
		}
    });
	
	$('#organize-category-save-btn').click(function(){
		var tree = $('.sortable').nestedSortable('toArray');

		$.ajax({
			url: 'admin-ajax.php',
			data: {
				"p": "manage-categories", 
				"do": "organize",
				"type": "<?php echo $category_type; ?>",
				"tree": tree
			},
			type: 'POST',
			dataType: 'json',
			success: function(data){
				if (data.success == true) {
					phpmelody.prevent_leaving_without_saving = false;
					window.location = 'categories.php?type=<?php echo $category_type;?>&organized=true';
				} else {
					$('#display_result').html(data.html).show();
				}
			}
		});
		return false;
	});
	
});
<?php endif; ?>


window.onbeforeunload = function (e) {
	if (phpmelody.prevent_leaving_without_saving == true) {
		var e = e || window.event;
		var msg = 'Are you sure you want to leave without saving?';
		// For IE and Firefox
		if (e) {
			e.returnValue = msg;
		}
		// For Safari / chrome
		return msg;
	}
}
</script>

<?php if($load_googlesuggests == 1) : ?>
<script src="js/jquery-ui.min.js" type="text/javascript"></script>
<script type="text/javascript">
/**@license
This file uses Google Suggest for jQuery plugin (licensed under GPLv3) by Haochi Chen ( http://ihaochi.com )
 */
(function ($) {
$.fn.googleSuggest = function(opts){
  opts = $.extend({service: 'web', secure: true}, opts);

  var services = {
    youtube: { client: 'youtube', ds: 'yt' },
    books: { client: 'books', ds: 'bo' },
    products: { client: 'products-cc', ds: 'sh' },
    news: { client: 'news-cc', ds: 'n' },
    images: { client: 'img', ds: 'i' },
    web: { client: 'hp', ds: '' },
    recipes: { client: 'hp', ds: 'r' }
  }, service = services[opts.service], span = $('<span>');


  opts.source = function(request, response){
    $.ajax({
      url: 'http'+(opts.secure?'s':'')+'://clients1.google.com/complete/search',
      dataType: 'jsonp',
      data: {
        q: request.term,
        nolabels: 't',
        client: service.client,
        ds: service.ds
      },
      success: function(data) {
        response($.map(data[1], function(item){
          return { value: span.html(item[0]).text() };
        }));
      }
    });  
  };
  
  return this.each(function(){
    $(this).autocomplete(opts);
  });
}
}(jQuery));

$(document).ready(function(){
	$(".gautocomplete").googleSuggest({service: "youtube"});
});
</script>
<?php endif; ?>
<?php if($load_lazy_load == 1) : ?>
<script src="js/echo.min.js"></script>
<script type="text/javascript">
function pm_init_lazy_load() {
	echo.init({
		offset: 50,
		throttle: 200,
		unload: false,
	});
}

$(document).ready(function() {
	pm_init_lazy_load()
});
</script>
<?php endif; ?>

<?php if($showm == '8') : // Settings page ?>
	<?php if ($config['total_videos'] > 50) : ?>
	<script type="text/javascript">
	$(document).ready(function() {
	    $('input[type=radio][name=thumb_from]').change(function() {
	        if (this.value == '2') {
				alert("Important Notice!\n\nExisting videos imported from YouTube, DailyMotion, etc. might not have corresponding thumbnails saved locally\n if they were imported while the 'Remote' option was active.\n\nChanging between the two options should be made with caution if videos were imported while the 'Remote' option was active.\n\nIdeally, this option should always be set to 'Local'. Switching back and forth after importing videos is not recommended.");
	        }
	    });
	});
	</script>
	<?php endif; ?>

<?php endif; ?>

<?php if(file_exists("db_update.php") && $hide_update_notification != 1) : ?>
<div id="modalDBUpdate" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="dbUpdateDialog" aria-hidden="true">
<div class="modal-header">
	<h3 id="dbUpdateDialog">PHP Melody Update</h3>
</div>
<div class="modal-body alert-help">
	<h4>Database Update Required</h4>
	<p>PHP Melody has to update the MySQL database.</p>
	<p>Click '<strong>Continue</strong>' finalize the update process.</p>
</div>
<div class="modal-footer">
	<a href="db_update.php" class="btn btn-success btn-strong">Continue</a>
</div>
</div>

<script type="text/javascript">
$('#modalDBUpdate').modal({
	show: true,
	backdrop: 'static',
	keyboard: false
});
</script>
<?php endif; ?>