$(document).ready(function() {
  var msg_div = $("#video_check_message");
  msg_div.hide();
  var speed = "";
  if($.browser.msie) 
	speed = "";
  else
  	speed = "fast";
  $('#VideoChecker').click( function() { 
    $('#VideoChecker').attr("disabled", "true");
	if( $(':input:checkbox:checked').length > 0 && $(':input:checkbox:checked').length <= 102)
	{
		msg_div.text( "" );
		msg_div.hide( speed );
		var video_id = 0;
		var i = 0;
		var checkedItems = $.makeArray($(':input:checkbox:checked'));
		var buffItem;
		var ajaxManager = $.manageAjax.create('vscheck', {queue: true, maxRequests: 1}); 

		for (i = 0; i <= checkedItems.length; i++)
		{
			buffItem = checkedItems[i];
			
			if ($(buffItem).attr('ID') && $(buffItem).attr('ID') != 'selectall') 
			{
				video_id = parseInt($(buffItem).attr('ID'));
				
				//$("#status_" + video_id).attr("src", "img/ico-loading.gif");
				$("#status_" + video_id).addClass("ico-loading");
				
				$.manageAjax.add( 'vscheck' , { 
					  type: "POST",
					  url: "vscheck.php",
					  data: ({ vid_id: video_id, job_type: 1}),
					  success: 
						 function(data){
							if (data.message != "") 
							{
								msg_div.append('<span class="vscheck-highlight-row" data-video-id="'+ data.video_id +'">'+ data.message + '</span><br />').show(speed);
							}
							$("#status_" + data.video_id).removeClass().addClass('pm-sprite '+ data.status_img);
							
							// update tooltip information too
							if (data.attr_title != '') {
								$("#status_" + data.video_id).attr("title", data.attr_title);
								$("#status_" + data.video_id).attr("data-original-title", data.attr_title);
							}
						  },
					  dataType: "json" 
				});
			}
			else if ($(buffItem).attr('ID') != 'selectall')
			{
				if ($(buffItem).attr('VALUE')) 
				{
					msg_div.append("Sorry, Video ID " + $(buffItem).attr('VALUE') + " cannot be checked. <br />").show(speed);
				}
			}
		}
	}
	else
	{
		msg_div.text("Please use the checkboxes on the left to select the videos you want to check.").show( speed );
	}
	if( msg_div.text().length > 0)
	{
		msg_div.show( speed );
	}
	$('#VideoChecker').removeAttr("disabled");
  });
});