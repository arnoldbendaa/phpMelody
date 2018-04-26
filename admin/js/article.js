function article_ajax_request(page, extra_params, output_sel, type) {
	
	var ret = false;
	
	if (type.length == 0)
	{
		type = "html";	
	}

	if (output_sel.length > 0)
	{
		$(output_sel).html('<img src="./img/ico-loading.gif" alt="Loading" id="loading" />Loading...').fadeIn('normal');
	}
	$.ajax({
		   type: "GET",
		   //url: "./article_ajax.php", 
		   url: "./admin-ajax.php",
		   //cache: false, 
		   data: "p=" + page + "&" + extra_params,
		   dataType: type,
		   success: function(data) {
						if (output_sel.length > 0)
						{
							$(output_sel).html(data);
							$(output_sel).show();
						}
						ret = true;
					}
		   });
	return ret;
}

function onpage_delete_article(article_id, result_selector, tr_selector) {

	var confirm_msg = "You are about to remove this article and everything attached to it. Click 'Cancel' to stop, 'OK' to delete";
	var response = false;
	var pmnonce = $('#_pmnonce_admin_articles').val();
	var pmnonce_t = $('#_pmnonce_t_admin_articles').val();
	
	if (confirm(confirm_msg)) 
	{
		article_ajax_request("articles", "do=delete&id=" + article_id + "&_pmnonce="+ pmnonce + "&_pmnonce_t=" + pmnonce_t, result_selector, "html");
		$(tr_selector).fadeOut('normal');
	}
	return false;
}

$(document).ready(function(){
	$('input[name="title"]').typeWatch({
		callback: function(){
			var title = $('input[name="title"]').val();
			var slug = $('input[name="article_slug"]').val();
			
			if (slug == '' && title != '') {
				$('#loading').show();
				
				$.ajax({
					type: 'POST',
					url: "./admin-ajax.php",
					data: {
						"p": "articles",
						"do": "generate-article-slug",
						"title": title
					},
					dataType: "html",
					success: function(data){
						$('input[name="article_slug"]').val(data);
						$('#loading').hide();
					}
				});
			}
		},
		wait: 1000,
    	highlight: true,
    	captureLength: 2
	});
	
	$('input[name="article_slug"]').change(function(){
		if ($(this).val() != '') {
			$('#loading').show();
			
			$.ajax({
					type: 'POST',
					url: "./admin-ajax.php",
					data: {
						"p": "articles",
						"do": "generate-article-slug",
						"title": $(this).val()
					},
					dataType: "html",
					success: function(data){
						$('input[name="article_slug"]').val(data);
						$('#loading').hide();
					}
				});
		}
	});
});
