{include file='header.tpl' p="index"}
<link rel="stylesheet" type="text/css" media="screen" href="{$smarty.const._URL}/templates/{$template_dir}/css/index.css">
<div id="wrapper">
    <div class="container-fluid">
		<div class="wrap-slider-thumbs">
			<div class="container">
				<div class="wrap-heading clearfix">
					<div class="block-heading clearfix">
						<div>Videos being Watched</div>
					</div>
				</div>
			</div>
		</div>
		<div class="slider-thumbs">
			<div class="swiper-container">
				<div class="swiper-wrapper">
                    {foreach from=$top_videos key=k item=video_data}
					<div class="swiper-slide">
						<div class="thumb">
							<span class="thumb-preview video">
								<a href="trailer.html">
									<img src="{$video_data.thumb_img_url}" alt="{$video_data.attr_alt}">
									<span class="new">new</span>
									<span class="add-favorite wrap-icon">
										<svg class="svg-icon" width="17px" height="16px">
											<use xlink:href="#heart"></use>
										</svg>
        							</span>
									<span class="add-watch-later wrap-icon">
										<svg class="svg-icon" width="12px" height="12px">
											<use xlink:href="#time"></use>
										</svg>
        							</span>
									<span class="preview-info-group-br">
										{if $video_data.yt_length != 0}<span class="time">{$video_data.duration}</span>{/if}
										<span class="hd">
											<svg class="svg-icon" width="24px" height="20px">
												<use xlink:href="#hd"></use>
											</svg>
        								</span>
									</span>
									<span class="rating rat-video wrap-icon">
										<svg class="svg-icon" width="35.97px" height="40.031px">
											<use xlink:href="#like"></use>
										</svg>
        								<span>92%</span>
									</span>
								</a>
							</span>
							<span class="thumb-info">
								<span class="name text-truncate">
									<span>{$video_data.video_title}</span>
								</span>
								<span class="thumb-sub-info right wrap-icon"><span>{$video_data.views_compact}</span>
									<svg class="svg-icon" width="22px" height="16px">
										<use xlink:href="#eye2"></use>
									</svg>
								</span>
								<a href="#" class="info-cs">
									<img src="{$smarty.const._URL}/templates/{$template_dir}/img/assets/thumbs/cs-info-mini.png" alt="">
								</a>
							</span>
						</div>
					</div>
					{/foreach}

				</div>
			</div>


			<div class="swiper-button-next">
				<svg class="svg-icon" width="15px" height="27px">
					<use xlink:href="#arr-right"></use>
				</svg>

			</div>
			<div class="swiper-button-prev">
				<svg class="svg-icon" width="15.03px" height="27px">
					<use xlink:href="#arr-left"></use>
				</svg>

			</div>
		</div>

		<div class="wrap-masonry">
			<div class="container">
				<div class="wrap-heading clearfix">
					<div class="switch-blocks"><a href="#" class="active">Videos</a><a href="#">Albums</a>
					</div>
					<div class="sortings-navbar magic-line">
						<div class="sortings-navbar-inner">

							<div class="sorting {if $order==''}active{/if}">
								<a href="{$smarty.const._URL}" class="sorting-choice">Newest</a>
							</div>
							<div class="sorting {if $order=='top_today' ||$order=='top_lastWeek' ||$order=='top_lastMonth' ||$order=='top_allTime'}active{/if}" >
								<div data-toggle="dropdown" class="sorting-choice ">Top Rated</div>
								<ul class="sorting-menu">
									<li class="{if $order=='top_today'}active{/if}">
										<a href="{$smarty.const._URL}?order=top_today" class="sorting-menu-link"> <span>Today</span>
										</a>
									</li>
									<li class="{if $order=='top_lastWeek'}active{/if}">
										<a href="{$smarty.const._URL}?order=top_lastWeek" class="sorting-menu-link"> <span>Last Week</span>
										</a>
									</li>
									<li class="{if $order=='top_lastMonth'}active{/if}">
										<a href="{$smarty.const._URL}?order=top_lastMonth" class="sorting-menu-link"> <span>Last Month</span>
										</a>
									</li>
									<li class="{if $order=='top_allTime'}active{/if}">
										<a href="{$smarty.const._URL}?order=top_allTime" class="sorting-menu-link"> <span>All Time</span>
										</a>
									</li>
								</ul>
							</div>
							<div class="sorting {if $order=='popular_today' || $order=='popular_lastWeek' || $order=='popular_lastMonth' || $order=='popular_allTime'}active{/if}">
								<div data-toggle="dropdown" class="sorting-choice">Most Popular</div>
								<ul class="sorting-menu">
									<li class="{if $order=='popular_today'}active{/if}">
										<a href="{$smarty.const._URL}?order=popular_today" class="sorting-menu-link"> <span>Today</span>
										</a>
									</li>
									<li class="{if $order=='popular_lastWeek'}active{/if}">
										<a href="{$smarty.const._URL}?order=popular_lastWeek" class="sorting-menu-link"> <span>Last Week</span>
										</a>
									</li>
									<li class="{if $order=='popular_lastMonth'}active{/if}">
										<a href="{$smarty.const._URL}?order=popular_lastMonth" class="sorting-menu-link"> <span>Last Month</span>
										</a>
									</li>
									<li class="{if $order=='popular_allTime'}active{/if}">
										<a href="{$smarty.const._URL}?order=popular_allTime" class="sorting-menu-link"> <span>All Time</span>
										</a>
									</li>
								</ul>
							</div>
							<div class="sorting {if $order=='longest'}active{/if}"><a href="{$smarty.const._URL}?order=longest" class="sorting-choice">Longest</a>
							</div>
						</div>
					</div>
				</div>
				<div class="masonry grid" style="position: relative; height: 3273.47px;">
					<div class="grid-sizer"></div>
					<div class="gutter-sizer"></div>
                    {foreach from=$results key=k item=video_data}

					<div class="grid-item" style="position: absolute; left: 0%; top: 0px;">
						<div class="thumb">
							<span class="thumb-preview video">
								<a href="{$video_data.video_href}"><img src="{$video_data.thumb_img_url}" alt="{$video_data.attr_alt}">
                                    {if $video_data.mark_new}<span class="new">{$lang._new}</span>{/if}
									<span class="add-favorite wrap-icon">
										<svg class="svg-icon" width="17px" height="16px">
											<use xlink:href="#heart"></use>
										</svg>
									</span>
									<span class="add-watch-later wrap-icon">
										<svg class="svg-icon" width="12px" height="12px">
											<use xlink:href="#time"></use>
										</svg>
									</span>
									<span class="preview-info-group-br">
											    {if $video_data.yt_length != 0}<span class="time">{$video_data.duration}</span>{/if}

										<span class="hd">
											<svg class="svg-icon" width="24px" height="20px">
												<use xlink:href="#hd"></use>
											</svg>
										</span>
									</span>
									<span class="rating rat-video wrap-icon">
										<svg class="svg-icon" width="35.97px" height="40.031px">
											<use xlink:href="#like"></use>
										</svg>
										<span>92%</span>
									</span>
								</a>
							</span>
							<span class="thumb-info">
								<span class="name"><span>{$video_data.video_title}</span></span>
								<span class="thumb-sub-info right wrap-icon"><span>365,452</span>
									<svg class="svg-icon" width="22px" height="16px">
										<use xlink:href="#eye2"></use>
									</svg>
								</span>
								<a href="#" class="info-cs">
									<img src="{$smarty.const._URL}/templates/{$template_dir}/img/assets/thumbs/cs-info-mini.png" alt="">
								</a>
							</span>
						</div>
					</div>


                        {foreachelse}
                        {$lang.top_videos_msg2}
                    {/foreach}

				</div>

			</div>
		</div>
	{if count($featured_categories_data) > 0}
	<div class="row-fluid">
	<div class="span12">
		<div class="element-videos">
			{foreach from=$featured_categories_data key=category_id item=video_data_array}
				{if $categories.$category_id.published_videos > 0}
				<div class="pm-section-head">
					<h2 class="upper-blue"><a href="{$categories.$category_id.url}">{$categories.$category_id.name}</a></h2>
					<div class="btn-group btn-group-sort pm-slide-control">
					<button class="btn btn-mini" id="pm-slide-prev_{$category_id}"><i class="pm-vc-sprite arr-l"></i></button>
					<button class="btn btn-mini" id="pm-slide-next_{$category_id}"><i class="pm-vc-sprite arr-r"></i></button>
					</div>
				</div>
				<div id="">
				<!-- Carousel items -->
				<ul class="pm-ul-carousel-videos list-inline" data-slider-id="{$category_id}" data-slides="6" id="pm-carousel_{$category_id}">
				{foreach from=$video_data_array key=k item=video_data}
				  <li>
					<div class="pm-li-video">
						<span class="pm-video-thumb pm-thumb-145 pm-thumb border-radius2">
						<span class="pm-video-li-thumb-info">
						{if $video_data.yt_length != 0}<span class="pm-label-duration border-radius3 opac7">{$video_data.duration}</span>{/if}
						{if $video_data.mark_new}<span class="label label-new">{$lang._new}</span>{/if}
						{if $video_data.mark_popular}<span class="label label-pop">{$lang._popular}</span>{/if}
						{if $logged_in}
						<span class="watch-later hide">
							<button class="btn btn-mini watch-later-add-btn-{$video_data.id}" onclick="watch_later_add({$video_data.id}); return false;" rel="tooltip" title="{$lang.add_to} {$lang.watch_later}"><i class="icon-time"></i></button>
							<button class="btn btn-mini btn-remove hide watch-later-remove-btn-{$video_data.id}" onclick="watch_later_remove({$video_data.id}); return false;" rel="tooltip" title="{$lang.playlist_remove_item}"><i class="icon-ok"></i></button>
						</span>
						{/if}
						</span>
						<a href="{$video_data.video_href}" class="pm-thumb-fix pm-thumb-145"><span class="pm-thumb-fix-clip"><img src="{$video_data.thumb_img_url}" alt="{$video_data.attr_alt}" width="145"><span class="vertical-align"></span></span></a>
						</span>
						<h3><a href="{$video_data.video_href}" class="pm-title-link" title="{$video_data.attr_alt}">{$video_data.video_title}</a></h3>
						<div class="pm-video-attr">
							<span class="pm-video-attr-author">{$lang.articles_by} <a href="{$video_data.author_profile_href}">{$video_data.author_name}</a></span>
							<span class="pm-video-attr-since"><small>{$lang.added} <time datetime="{$video_data.html5_datetime}" title="{$video_data.full_datetime}">{$video_data.time_since_added} {$lang.ago}</time></small></span>
							<span class="pm-video-attr-numbers"><small>{$video_data.views_compact} {$lang.views} / {$video_data.likes_compact} {$lang._likes}</small></span>
						</div>
						<p class="pm-video-attr-desc">{$video_data.excerpt}</p>
						{if $video_data.featured}
						<span class="pm-video-li-info">
							<span class="label label-featured">{$lang._feat}</span>
						</span>
						{/if}
					</div>
				  </li>
				{/foreach}
				</ul>
				</div><!-- #pm-slider -->
				<div class="clear-fix"></div>
				{/if}
			{/foreach}
		</div>
	</div>
	</div>
	{/if}

		<div class="gradient-block">
			<div class="wrap-top-categories">
				<div class="container">
					<div class="wrap-heading clearfix">
						<div class="block-heading clearfix">
							<div>Top Categories</div>
						</div>
					</div>
					<div class="wrap-thumbs">
						<div class="top-ctgs-lists">
							{foreach from=$categories_data key=k item=category_data}
								{if $category_data.parent_id == 0}
									<div class="top-ctgs-item">
										<div class="thumb">
											<a href="{$category_data.url}">
												<span class="thumb-preview ctg">
													<img src="{$category_data.image_url}" alt="{$category_data.attr_alt}" >
													<span class="name-over text-truncate">
														<span>{smarty_fewchars s=$category_data.name length=32}</span>
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
			</div>
		</div>
	</div><!-- .container-fluid -->
{include file="footer.tpl" tpl_name="video-categories-page"}