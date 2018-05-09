<div style="width:600px; padding:15px">
{if $allow_comments == '1'}

	<h2 class="upper-blue" >{$lang.post_comment}</h2>
	{if ($comment_system_native + $comment_system_facebook + $comment_system_disqus) > 1}
	<ul class="nav nav-tabs nav-comments">
		{if $comment_system_native}
			<li {if $comment_system_primary == 'native'}class="active"{/if}><a href="#comments-native" id="nav-link-comments-native" data-toggle="tab">{$lang.comments}</a></li>
		{/if}
		{if $comment_system_facebook}
			<li {if $comment_system_primary == 'facebook'}class="active"{/if}><a href="#comments-facebook" id="nav-link-comments-facebook" data-toggle="tab">Facebook</a></li>
		{/if}
		{if $comment_system_disqus}
			<li {if $comment_system_primary == 'disqus'}class="active"{/if}><a href="#comments-disqus" id="nav-link-comments-disqus" data-toggle="tab">Disqus</a></li>
		{/if}
	</ul>
	{/if}
	<div class="tab-content pm-comments-container">
	{if $comment_system_native}
		<div class="tab-pane fade {if $comment_system_primary == 'native'}in active{/if}" id="comments-native">
			{include file='comment-form.tpl'}
			{if ! $logged_in && ! $guests_can_comment}
				{$must_sign_in}
			{/if}
			
			<h2 class="upper-blue">{$lang.comments}</h2>
			
			<div class="pm-comments comment_box">
				{if $comment_count == 0}
				    <ul class="pm-ul-comments">
				    	<li id="preview_comment"></li>
				    </ul>
				    <div id="be_the_first">{$lang.be_the_first}</div>
				{else}
				    <span id="comment-list-container">
						{include file="comment-list.tpl"}
						<!-- comment pagination -->
						{if $comment_pagination_obj != ''}
							{include file="comment-pagination.tpl"}
						{/if}
					</span>
				{/if}
			</div>
		</div>
	{/if}
	
	{if $comment_system_facebook}
		<div class="tab-pane fade {if $comment_system_primary == 'facebook'}in active{/if} pm-comments comment_box" id="comments-facebook">
			{literal}
			<div class="fb-comments" data-href="{/literal}{if $tpl_name == 'article-read'}{$article.link}{else}{$video_data.video_href}{/if}{literal}" data-numposts="{/literal}{$fb_comment_numposts}{literal}" data-order-by="{/literal}{$fb_comment_sorting}{literal}" data-colorscheme="light" data-width="652"></div>
			{/literal}
		</div>
	{/if}
	
	{if $comment_system_disqus}
		<div class="tab-pane fade {if $comment_system_primary == 'disqus'}in active{/if} pm-comments comment_box" id="comments-disqus">
			<div id="disqus_thread"></div> 
			{literal}
			<script type="text/javascript">
			    var disqus_shortname = '{/literal}{$disqus_shortname}{literal}'; 
				var disqus_identifier = {/literal}{if $tpl_name == 'article-read'} 'article-{$article.id}' {else} 'video-{$video_data.uniq_id}' {/if}{literal};
			    /* * * DON'T EDIT BELOW THIS LINE * * */
			    (function() {
			        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
			        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
			        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
			    })();

			</script>
			{/literal}
		</div>
	{/if}
	</div>
{else}
	<div>{$lang.comments_disabled}</div>
{/if}
</div>
{literal}
	<script>
        $("#c_submit").click(function(){
            $("#mycommentspan").html('<img src="' + TemplateP + '/img/ajax-loading.gif" alt="Loading" id="loading" /> Posting...').show();
            var b = $("#c_user_id").val();
            var e = $("#c_vid").val();
            var d = $("#c_comment_txt").val();
            var f = $("#c_username").val();
            var c = $("#captcha").val();
            if (b == 0) {
                $.post(MELODYURL2 + "/comment.php", {
                    username: f,
                    captcha: c,
                    vid: e,
                    user_id: b,
                    comment_txt: d
                }, function(g){
                    if (g.cond == true) {
                        $("#pm-post-form").slideUp("normal", function(){
                            $("#mycommentspan").html(g.html).show();
                            if (g.preview == true) {
                                $("#be_the_first").hide();
                                $("#preview_comment").html(g.preview_html).fadeIn(700)
                            }
                        })
                    }
                    else {
                        if (g.cond == false) {
                            $("#c_submit").show();
                            $("#mycommentspan").html(g.html).show();
                        }
                    }
                }, "json")
            }
            else {
                if (b > 0) {
                    $.post(MELODYURL2 + "/comment.php", {
                        vid: e,
                        user_id: b,
                        comment_txt: d
                    }, function(g){
                        if (g.cond == true) {
                            $("#pm-post-form").slideUp("normal", function(){
                                $("#mycommentspan").html(g.html).show();
                                if (g.preview == true) {
                                    $("#be_the_first").hide();
                                    $("#preview_comment").html(g.preview_html).fadeIn(700)
                                }
                            })
                        }
                        else {
                            if (g.cond == false) {
                                $("#c_submit").show();
                                $("#mycommentspan").html(g.html).show();
                            }
                        }
                    }, "json")
                }
            }
            return false
        });

	</script>

{/literal}