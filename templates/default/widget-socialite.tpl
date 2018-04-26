<div id="pm-socialite">
	<ul class="social-buttons cf">
		<li>
			<a href="https://www.facebook.com/sharer.php?u={if $tpl_name == 'article-read'}{$article.link}{else}{$video_data.video_href}{/if}&t={if $tpl_name == 'article-read'}{$article.title}{else}{$video_data.video_title}{/if}" class="socialite facebook-like" data-href="{if $tpl_name == 'article-read'}{$article.link}{else}{$video_data.video_href}{/if}" data-send="false" data-layout="box_count" data-width="60" data-show-faces="false" rel="nofollow" target="_blank"><span class="vhidden">Share on Facebook</span></a>
		</li>
		<li>
			<a href="https://twitter.com/share" class="socialite twitter-share" data-text="{if $tpl_name == 'article-read'}{$article.title}{else}{$video_data.video_title}{/if}" data-url="{if $tpl_name == 'article-read'}{$article.link}{else}{$video_data.video_href}{/if}" data-count="vertical" rel="nofollow" target="_blank"><span class="vhidden">Share on Twitter</span></a>
		</li>
		<li>
			<a href="https://plus.google.com/share?url={if $tpl_name == 'article-read'}{$article.link}{else}{$video_data.video_href}{/if}" class="socialite googleplus-one g-plusone" data-size="tall" data-href="{if $tpl_name == 'article-read'}{$article.link}{else}{$video_data.video_href}{/if}" rel="nofollow" target="_blank"><span class="vhidden">Share on Google+</span></a>
		</li>
		<li>
			<a href="https://pinterest.com/pin/create/button/?url={if $tpl_name == 'article-read'}{$article.link}{else}{$video_data.video_href}{/if}/" class="socialite pinterest-pinit"><span class="vhidden">Pinterest</span></a>
		</li>
	</ul>
</div>