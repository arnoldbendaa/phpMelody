{include file='header.tpl' p="general" tpl_name="video-categories-page"}
<div id="wrapper">
	<div class="container-fluid">
		<div class="row-fluid">
			<div class="span12">
			<div id="primary">
				<h1>{$lang._categories}</h1>
				<ul class="pm-ul-browse-categories thumbnails">
				{foreach from=$categories_data key=k item=category_data}
					{if $category_data.parent_id == 0}
					<li>
						<div class="pm-li-category">
						<a href="{$category_data.url}">
							<span class="pm-video-thumb pm-thumb-234 pm-thumb">
								<div class="pm-thumb-fix pm-thumb-234"><span class="pm-thumb-fix-clip"><img src="{$category_data.image_url}" alt="{$category_data.attr_alt}" width="234"><span class="vertical-align"></span></span></div>
							</span>
							<h3>{smarty_fewchars s=$category_data.name length=32}</h3>
						</a>
						</div>
					</li>
					{/if}
				{/foreach}
				</ul>	
			</div><!-- #primary -->
			</div><!-- #content -->
		</div><!-- .row-fluid -->
	</div><!-- .container-fluid -->
{include file="footer.tpl" tpl_name="video-categories-page"} 