{include file='header.tpl' p="general" tpl_name="video-categories-page"}

<div class="content clearfix" id="wrapper">
	<div class="container">
		<div class="main">
			<div class="wrap-heading clearfix">
				<div class="switch-blocks">
					<a class="active">Categories</a>
				</div>
				<div class="sortings-navbar magic-line">
					<div class="sortings-navbar-inner">
						<div class="sorting {if $order=='date'||$order==''}active{/if}">
							<a href="{$smarty.const._URL}/category.php?sortby=date" aria-expanded="false"  class="sorting-choice">Newest</a>
						</div>
						<div class="sorting {if $order=='rating'}active{/if}">
							<a href="{$smarty.const._URL}/category.php?sortby=rating"  class="sorting-choice" aria-expanded="false">Top Rated</a>
						</div>
						<div class="sorting {if $order=='views'}active{/if}">
							<a href="{$smarty.const._URL}/category.php?sortby=views"  class="sorting-choice" aria-expanded="false">Most Popular</a>
						</div>
						<div class="sorting {if $order=='title'}active{/if}">
							<a href="{$smarty.const._URL}/category.php?sortby=title" class="sorting-choice">Title</a>
						</div>
					</div>
					<div class="magic-line-sorting" style="width: 74px; left: 0px;"></div></div>
			</div>
			<div class="wrap-thumbs">
				<div class="thumbs-lists">
                    {foreach from=$categories_data key=k item=category_data}
						{if $category_data.parent_id == 0}
					<div class="thumbs-item">
						<div class="thumb">
							<a href="{$category_data.url}">
								<span class="thumb-preview video">
									<img src="{$category_data.image_url}" alt="{$category_data.attr_alt}">
									<span class="name-over text-truncate">
										<span>{smarty_fewchars s=$category_data.name length=32}</span>
									</span>
								</span>
								<span class="thumb-info"><span class="ctg-info">
										<span class="text text-truncate">{$category_data.total_videos} videos</span>
									</span>
								</span>
							</a>
						</div>
					</div>
						{/if}
                    {/foreach}

				</div>
			</div>
		</div>
        {include file="sidebar.tpl" }
	</div>
</div>

{include file="footer.tpl" tpl_name="video-categories-page"}