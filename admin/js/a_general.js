function delete_ad(adTitle, location)
{
	var confirmmessage = "Are you sure you want to delete '"+ adTitle +"'?";
	
	if (confirm(confirmmessage)) {
		window.location = location;
	}
}

function del_comment_id(ID, PAGE, FILTER) {
	this.id=ID
	this.page=PAGE
	this.filter=FILTER
	var confirmmessage = "Are you sure you want to delete comment #"+this.id+" ?";
	var pmnonce = $('#_pmnonce_admin_comments').val();
	var pmnonce_t = $('#_pmnonce_t_admin_comments').val();
	var goifokay = "comments.php?cid="+this.id+"&a=1&page="+this.page+"&filter="+this.filter + "&_pmnonce="+ pmnonce + "&_pmnonce_t=" + pmnonce_t;
	
	if (confirm(confirmmessage)) {
		window.location = goifokay;
	}
}

function del_member_id(ID, PAGE) {
	this.id=ID
	this.page=PAGE
	var confirmmessage = "Are you sure you want to delete member ID #"+this.id+" ?";
	var pmnonce = $('#_pmnonce_admin_members').val();
	var pmnonce_t = $('#_pmnonce_t_admin_members').val();
	var filter = $('#listing-filter').val();
	var filter_value = $('#listing-filter_value').val();
	
	var goifokay = "members.php?uid="+ this.id +"&a=1&page="+ this.page +"&filter="+ filter +"&fv="+ filter_value +"&_pmnonce="+ pmnonce + "&_pmnonce_t=" + pmnonce_t;
	
	if (confirm(confirmmessage)) {
		window.location = goifokay;
	}
}

function del_video_id(ID, PAGE, FILTER) {
	this.id=ID
	this.page=PAGE
	this.filter=FILTER
	var confirmmessage = "Are you sure you want to delete video ID #"+this.id+" ?";
	var pmnonce = $('#_pmnonce_admin_videos_listcontrols').val();
	var pmnonce_t = $('#_pmnonce_t_admin_videos_listcontrols').val();
	var filter = $('#listing-filter').val();
	var filter_value = $('#listing-filter_value').val();
	
	var goifokay = "modify.php?vid="+ this.id +"&a=1&page="+ this.page +"&filter="+ filter +"&fv="+ filter_value +"&_pmnonce="+ pmnonce + "&_pmnonce_t=" + pmnonce_t;
	
	if (confirm(confirmmessage)) {
		window.location = goifokay;
	}
}

function del_video_comments(ID, PAGE, FILTER) {
	this.id=ID
	this.page=PAGE
	this.filter=FILTER
	var confirmmessage = "You will remove all comments for video ID #"+this.id+" !";
	var pmnonce = $('#_pmnonce_admin_videos_listcontrols').val();
	var pmnonce_t = $('#_pmnonce_t_admin_videos_listcontrols').val();
	
	if ( ! pmnonce) {
		var pmnonce = $('#_pmnonce_admin_articles').val();
		var pmnonce_t = $('#_pmnonce_t_admin_articles').val();
	}
	
	var goifokay = "modify.php?vid="+this.id+"&a=2&page="+this.page+"&filter="+this.filter + "&_pmnonce="+ pmnonce + "&_pmnonce_t=" + pmnonce_t;
	
	if (confirm(confirmmessage)) {
		window.location = goifokay;
	}
}

function del_report(ID, PAGE) {
	this.id=ID
	this.page=PAGE
	var confirmmessage = "Click OK to delete this entry.";
	var goifokay = "reports.php?rid="+this.id+"&a=1&page="+this.page;
	
	if (confirm(confirmmessage)) {
		window.location = goifokay;
	}
}

function del_allreports() {
	var confirmmessage = "Click OK to delete all the reports.";
	var goifokay = "reports.php?a=2";
	if (confirm(confirmmessage)) {
		window.location = goifokay;
	}
}

function del_temp_video_id(ID, PAGE) {
	this.id=ID
	this.page=PAGE
	var confirmmessage = "Are you sure you want to delete video ID #"+this.id+" ?";
	var pmnonce = $('#_pmnonce_admin_approve').val();
	var pmnonce_t = $('#_pmnonce_t_admin_approve').val();
	
	var goifokay = "approve.php?vid="+this.id+"&a=delvid&page="+this.page  + "&_pmnonce="+ pmnonce + "&_pmnonce_t=" + pmnonce_t;
	
	if (confirm(confirmmessage)) {
		window.location = goifokay;
	}
}

function del_alltemp() {
	var confirmmessage = "Click OK to delete all videos pending approval.";
	var pmnonce = $('#_pmnonce_admin_approve').val();
	var pmnonce_t = $('#_pmnonce_t_admin_approve').val();
	var goifokay = "approve.php?a=delall&_pmnonce="+ pmnonce + "&_pmnonce_t=" + pmnonce_t;
	
	if (confirm(confirmmessage)) {
		window.location = goifokay;
	} 
}
	
var horizontal_offset = "9px"; //horizontal offset of hint box from anchor link
var vertical_offset = "0";	 //horizontal offset of hint box from anchor link. No need to change.
var ie = document.all;
var ns6 = document.getElementById&&!document.all;

function getposOffset(what, offsettype){
	var totaloffset = (offsettype=="left") ? what.offsetLeft : what.offsetTop;
	var parentEl = what.offsetParent;
	while (parentEl != null) {
		totaloffset = (offsettype == "left") ? totaloffset+parentEl.offsetLeft : totaloffset+parentEl.offsetTop;
		parentEl = parentEl.offsetParent;
	}
	return totaloffset;
}

function iecompattest(){
	return (document.compatMode && document.compatMode!="BackCompat")? document.documentElement : document.body
}

function clearbrowseredge(obj, whichedge){
	var edgeoffset = (whichedge=="rightedge")? parseInt(horizontal_offset)*-1 : parseInt(vertical_offset)*-1
	if (whichedge == "rightedge"){
		var windowedge = ie && !window.opera ? iecompattest().scrollLeft+iecompattest().clientWidth-30 : window.pageXOffset+window.innerWidth-40;
		dropmenuobj.contentmeasure = dropmenuobj.offsetWidth
		if (windowedge-dropmenuobj.x < dropmenuobj.contentmeasure)
		edgeoffset = dropmenuobj.contentmeasure + obj.offsetWidth + parseInt(horizontal_offset);
	}
	else{
		var windowedge=ie && !window.opera? iecompattest().scrollTop + iecompattest().clientHeight-15 : window.pageYOffset+window.innerHeight-18;
		dropmenuobj.contentmeasure = dropmenuobj.offsetHeight;
		if (windowedge-dropmenuobj.y < dropmenuobj.contentmeasure)
			edgeoffset=dropmenuobj.contentmeasure-obj.offsetHeight
	}
	return edgeoffset;
}


function hidetip(e){
	dropmenuobj.style.visibility="hidden";
	dropmenuobj.style.left="-500px";
}

function clearText(field){
    if (field.defaultValue == field.value) 
		field.value = '';
    else if (field.value == '') 
		field.value = field.defaultValue;
}

function createhintbox(){
	var divblock=document.createElement("div");
	divblock.setAttribute("id", "hintbox");
	document.body.appendChild(divblock);
}

if (window.addEventListener)
	window.addEventListener("load", createhintbox, false);
else if (window.attachEvent)
	window.attachEvent("onload", createhintbox);
else if (document.getElementById)
	window.onload=createhintbox;

function checkUncheckAll(theElement) {
    var theForm = theElement.form, z = 0;
	var part_id = '';
	
	for (z = 0; z < theForm.length; z++) {
		part_id = theForm[z].id.substring(0,5);
        if (theForm[z].type == 'checkbox' && theForm[z].name != 'checkall' && theForm[z].id != 'check_ignore' && part_id != 'ddcl-') {
            theForm[z].checked = theElement.checked;
        }
    }
}
/*
 * resizehandle.js (c) Fil 2007, plugin pour jQuery ecrit
 * a partir du fichier resize.js du projet DotClear
 * (c) 2005 Nicolas Martin & Olivier Meunier and contributors
 */
jQuery.fn.resizehandle = function() {
  return this.each(function() {
    var me = jQuery(this);
	var meId = me[0].id;
	if(meId != "textarea")	//	avoid interference with WYSIWYG Editor
	{
		me.after(
		  jQuery('<div class="resizehandle"></div>')
		  .bind('mousedown', function(e) {
			var h = me.height();
			var y = e.clientY;
			var moveHandler = function(e) {
			  me
			  .height(Math.max(20, e.clientY + h - y));
			};
			var upHandler = function(e) {
			  jQuery('html')
			  .unbind('mousemove',moveHandler)
			  .unbind('mouseup',upHandler);
			};
			jQuery('html')
			.bind('mousemove', moveHandler)
			.bind('mouseup', upHandler);
		  })
	    );
	}
  });
}

// Shows PM notification for errors, news and such
function show_pm_note(title, description, url_image, bgcolor) {

var unique_id = $.gritter.add({
		// (string | mandatory) the heading of the notification
		title: title,
		// (string | mandatory) the text inside the notification
		text: description,
		// (string | optional) the image to display on the left
		image: url_image,
		// (bool | optional) if you want it to fade out on its own or just sit there
		bgcolor: bgcolor,
		sticky: true, 
		// (int | optional) the time you want it to be alive for before fading out
		time: ''
	});
}

$(document).ready(function() {
	$("textarea[id!=textarea-WYSIWYG]")
  	.resizehandle();
  
	$("#video_check_message").hide();

	// setting the tabs in the sidebar hide and show, setting the current tab
	$('a[data-toggle="tab"]').on('shown', function (e) {
		var target = String(e.target);
		var tabname = target.split('#');
		var index = target.indexOf("settings.php");
		var index_2 = target.indexOf("settings_theme.php");

		if (index > 0 || index_2 > 0) {
			$('input[name="settings_selected_tab"]').val(tabname[1]);
		}
    })
	$('div.tabbed_set div').hide();
	
	if (typeof(php_post_arr) !== 'undefined') {
		var selected_tab = php_post_arr["settings_selected_tab"];
		$('div.'+selected_tab).show();
		$('div.tabbed_set ul.tabs li.'+selected_tab+' a').addClass('tab-current');
	} else {
		$('div.t1').show();
		$('div.tabbed_set ul.tabs li.t1 a').addClass('tab-current');
	}
	
	// SIDEBAR TABS
	$('div.tabbed_set ul li a').click(function(){
		var thisClass = this.className.slice(0,2);
		$('div.tabbed_set div').hide();
		$('div.' + thisClass).show();
		$('div.tabbed_set ul.tabs li a').removeClass('tab-current');
		$(this).addClass('tab-current');
		$('input[name="settings_selected_tab"]').val(thisClass);
	});

	// close edit comment form; the "Cancel" button
	$("a[id^='comment_update_']").click(function(){
		var row_id = $(this).attr('id').replace( /^\D+/g, '');
		$('#comment_update_form_' + row_id).hide();
		$('#comment_span_' + row_id).show();
		return false;
	});
	
	// show edit comment form
    $("div[id^='comment_update_']").click(function(){
		var row_id = $(this).attr('id').replace( /^\D+/g, '');
		
		$('#comment_span_' + row_id).hide();
		$('#comment_update_form_' + row_id).show();
    });
	
	$("a[id^='comment_update_pencil_']").click(function(){
		var row_id = $(this).attr('id').replace( /^\D+/g, '');

		$('#comment_span_' + row_id).hide();
		$('#comment_update_form_' + row_id).show();
		
		return false;
    });
	
	// save comment (ajax)
	$('input[id^="comment_update_btn_"]').click(function(){
		var row_id = $(this).attr('id').replace( /^\D+/g, '');
		var comment_txt = $('#commenttxt_'+ row_id).val();
		
		$('#loading').show();
		
		$.ajax({
	            type: 'POST',
	            url: "./admin-ajax.php",
	            data: {
					'p': 'manage-comments',
					'do': 'edit-comment',
	                'comment_id': $('#commentid_'+ row_id).val(),
					'comment_txt': $('#commenttxt_'+ row_id).val(),
					'update': 'update'
	            },
	            dataType: "json",
	            success: function(c){
					
					$('#loading').hide();

					if (c.success) {
						$('div[id="comment_update_form_'+ row_id + '"]').hide();
						$('#comment_span_' + row_id).html( c.html ).show();
					} else {
						alert(c.msg);
					}
			}
		});

		return false;
	});
	
    $("div.comment_update").hover(function () {
      $(this).addClass("comment_update_hover");
    }, function () {
      $(this).removeClass("comment_update_hover");
    });

	$('input[name="page_title"]').typeWatch({
		callback: function(){
			var title = $('input[name="page_title"]').val();
			var slug = $('#item-slug').val();
			
			if (slug == '' && title != '') {
				$('#loading').show();
				
				$.ajax({
					type: 'POST',
					url: "./admin-ajax.php",
					data: {
						"p": "utilities",
						"do": "sanitize-title",
						"text": title
					},
					dataType: "html",
					success: function(data){
						$('#item-slug').val(data);
						$('#preview_complete_url').html(data);
						$('#loading').hide();
					}
				});
			}
		},
		wait: 1000,
    	highlight: true,
    	captureLength: 2
	});
	
	$('#item-slug').focus(function(){
		$('#preview_url').show();
	});
	// Update URL 
	//$("#item-slug").keyup(function(){
	$("#item-slug").typeWatch( {
		
    	callback: function () { 
			//$('#loading').show();
			
			$.ajax({
	            type: 'POST',
	            url: "./admin-ajax.php",
	            data: {
					"p": "utilities",
					"do": "sanitize-title",
					"text": $("#item-slug").val()
	            },
	            dataType: "html",
	            success: function(r){
					$('#preview_complete_url').html(r);
					//$('#loading').hide();
				}
			}); 
		},
    	wait: 500,
    	highlight: true,
    	captureLength: 2
	});
		
	//});
	
	// Update Ad Zones
	$('div.adzone_update_form').hide();

    $("a[class^='adzone_update_']").click(function(){
		$(this).closest('tr').next('tr').find('span').toggle();
		$(this).closest('tr').next('tr').find('div.adzone_update_form').toggle();
		return false;
    });

	$("a[id^='adzone_update_']").click(function(){
		var row_id = $(this).attr('id').replace( /^\D+/g, '');
		$(this).closest('.adzone_update_form').hide();
		return false;
	});

	$('input[name="video_title"]').typeWatch({
		callback: function(){
			var title = $('input[name="video_title"]').val();
			var slug = $('input[name="video_slug"]').val();
			
			if (slug == '' && title != '') {
				$('#loading').show();
				$.ajax({
					type: 'POST',
					url: "./admin-ajax.php",
					data: {
						"p": "addvideo",
						"do": "generate-video-slug",
						"video-title": title
					},
					dataType: "html",
					success: function(data){
						$('input[name="video_slug"]').val(data);
						$('#loading').hide();
					}
				});
			}
		},
		wait: 1000,
    	highlight: true,
    	captureLength: 2
	});
	$('input[name="video_slug"]').change(function(){
		if ($(this).val() != '') {
			$('#loading').show();
			
			$.ajax({
					type: 'POST',
					url: "./admin-ajax.php",
					data: {
						"p": "addvideo",
						"do": "generate-video-slug",
						"video-title": $(this).val()
					},
					dataType: "html",
					success: function(data){
						$('input[name="video_slug"]').val(data);
						$('#loading').hide();
					}
				});
		}
	});
});

// Delete page
function onpage_delete_page(page_id, output_sel, tr_selector) {
	
	var confirm_msg = "You are about to remove this page. Click 'Cancel' to stop, 'OK' to delete";
	var response = false;
	var ret = false;
	var pmnonce = $('#_pmnonce_admin_pages').val();
	var pmnonce_t = $('#_pmnonce_t_admin_pages').val();

	if (confirm(confirm_msg)) 
	{
		$('#loading').show();
		
		$.ajax({
		   type: "GET",
		   url: "./admin-ajax.php",
		   data: "p=page&do=delete&id=" + page_id + "&_pmnonce="+ pmnonce + "&_pmnonce_t=" + pmnonce_t,
		   dataType: 'html',
		   success: function(data) {
						if (output_sel.length > 0)
						{
							$(output_sel).html(data);
							$(output_sel).show();
							$(tr_selector).fadeOut('normal');
						}
						ret = true;
						$('#loading').hide();
					}
		   });
	}
	return ret;
}

function del_activity_id(ID, PAGE) {
	this.id=ID
	this.page=PAGE
	var confirmmessage = "Are you sure you want to delete activity #"+this.id+"?";
	var pmnonce = $('#_pmnonce_admin_members_activity').val();
	var pmnonce_t = $('#_pmnonce_t_admin_members_activity').val();
	
	if ( ! pmnonce) {
		var pmnonce = $('#_pmnonce_admin_members_activity').val();
		var pmnonce_t = $('#_pmnonce_t_admin_members_activity').val();
	}
	
	var filter = $('#listing-filter').val();
	var filter_value = $('#listing-filter_value').val();
	
	var goifokay = "activity-stream.php?aid="+ this.id +"&a=delete&page="+ this.page +"&filter="+ filter +"&fv="+ filter_value +"&_pmnonce="+ pmnonce + "&_pmnonce_t=" + pmnonce_t;
	
	if (confirm(confirmmessage)) {
		window.location = goifokay;
	}
}

function onpage_delete_category(category_id, category_type, result_selector, tr_selector) {

	var confirm_msg = "You are about to delete a category.\nAny content associated with it will not be deleted.\n\nClick 'Cancel' to stop, 'OK' to delete";
	var response = false;

	if (confirm(confirm_msg)) 
	{
		$(result_selector).html('').hide();
		
		$.ajax({
			type: "POST",
			url: "./admin-ajax.php",
			data: {
				"p": "manage-categories",
				"do": "delete",
				"id": category_id,
				"type": category_type,
				"_pmnonce": '_admin_catmanager',
				"_pmnonce_t": $('#_pmnonce_t_admin_catmanager').val()
			},
			type: 'POST',
			dataType: 'json',
			success: function(data) {
				if (data.success == false) {
					$(result_selector).html(data.html).show();
				} else {
					$(result_selector).html(data.html).show();
					$(tr_selector).fadeOut('normal');
				}
				
				if (data._pmnonce != '') {
					$('#_pmnonce_t_admin_catmanager').val(data._pmnonce_t);
				}
				$('#loading').hide();
			}
		});
	}
	return false;
}

function delete_subtitle(sub_id) {
    $.ajax({
        type: "POST",
        url: "./admin-ajax.php",
        data: {
            "p": "addvideo",
            "do": "delete-subtitle",
            "sub-id": sub_id
        },
        type: 'POST',
        dataType: 'json',
        success: function(data) {
            if (data.success == false) {
                $('#showSubtitle').append(data);
            } else {
                $('#subtitle-'+ sub_id).fadeOut('normal');
            }
        }
    });

    return false;
}