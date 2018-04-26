function onpage_delete_favorite(video_id, selector) {
	var confirm_msg = pm_lang.onpage_delete_favorite_confirm;
	var response = false;
	
	if (confirm(confirm_msg)) {
		if (selector.length > 0) {
			ajax_request("favorites", "do=onpage_delete_favorite&vid=" + video_id, "", "", false);
			$(selector).fadeOut("slow");
		}
	}
}

function fav_send_request() {
	$.ajax({
			type: "POST",
			url: MELODYURL2 + "/ajax_favorite.php", 
			data: {
				    video_id : $('input[name="fav_video_id"]').val(), 
				    user_id: $('input[name="fav_user_id"]').val()
				   },
			dataType: 'json',
			success: function(data) {
				
				if (data.success == false){
					alert(data.msg);
					return false;
				}
				$('#fav_save_button').replaceWith(data.html);
				$('#fav_save_button').click(function() { return fav_send_request();});
				return false;
			}
		});
	return false;
}

$(document).ready(function(){
	/*
	 * Add to/Remove from favorites
	 */
	$('#addtofavorites').submit(function(){ 
		return false;
	});

	$('#fav_save_button').click(function() { return fav_send_request();});
	
	
	/*
	 * Share video
	 */
	$('#pm-vc-share').click(function() {
		$('#securimage-share').prop('src',  MELODYURL2 + '/include/securimage_show.php?sid=' + Math.random());
	});
	
	$('form[name="sharetofriend"]').submit(function(){

		$('#share-confirmation').hide();
		
		$.ajax({
		   type: "POST",
		   url: MELODYURL2 + "/ajax.php", 
		   //cache: false, 
		   data: $(this).serialize(),
		   dataType: 'json',
		   success: function(data) {
		   				$('#share-confirmation').html(data.msg).show();
						if (data.success == true) {
							$('form[name="sharetofriend"]').slideUp();
						}
		   			}
		});
		
		return false;
	});
	
	/*
	 * Report video
	 */
	$('#pm-vc-report').click(function() {
		$('#securimage-report').prop('src',  MELODYURL2 + '/include/securimage_show.php?sid=' + Math.random());
	});
	
	$('form[name="reportvideo"]').submit(function(){

		$('#report-confirmation').hide();
	
		$.ajax({
		   type: "POST",
		   url: MELODYURL2 + "/ajax.php", 
		   //cache: false, 
		   data: $(this).serialize(),
		   dataType: 'json',
		   success: function(data) {
		   				$('#report-confirmation').html(data.msg).show();
						if (data.success == true) {
							$('form[name="reportvideo"]').slideUp();
						}
		   			}
		});
		
		return false;
	});
});