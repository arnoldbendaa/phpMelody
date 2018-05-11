{include file='header.tpl' p="general" tpl_name="video-categories-page"}
<div class="content clearfix" id="wrapper">
    <div class="container">
        <div class="main">
            <div class="wrap-heading clearfix">
                <div class="switch-blocks">
                    <a  class="active">{$gv_casino_name}</a>
                </div>
                <div class="sortings-navbar magic-line">
                    <div class="sortings-navbar-inner">
                        <div class="sorting {if $order=='date'||$order==''}active{/if}">
                            <a href="{$smarty.const._URL}/casino.php?sortby=date" aria-expanded="false"  class="sorting-choice">Newest</a>
                        </div>
                        <div class="sorting {if $order=='rating'}active{/if}">
                            <a href="{$smarty.const._URL}/casino.php?sortby=rating"  class="sorting-choice" aria-expanded="false">Top Rated</a>
                        </div>
                        <div class="sorting {if $order=='views'}active{/if}">
                            <a href="{$smarty.const._URL}/casino.php?sortby=views"  class="sorting-choice" aria-expanded="false">Most Popular</a>
                        </div>
                        <div class="sorting {if $order=='title'}active{/if}">
                            <a href="{$smarty.const._URL}/casino.php?sortby=title" class="sorting-choice">Title</a>
                        </div>
                    </div>
                    <div class="magic-line-sorting" style="width: 74px; left: 0px;"></div></div>
            </div>
            <div class="wrap-thumbs">
                <div class="thumbs-lists">
                    {foreach from=$results key=k item=video_data}
                        <div class="thumbs-item">
                            <div class="thumb">
                                <a href="{$video_data.video_href}">
								<span class="thumb-preview video">
									<img src="{$video_data.thumb_img_url}" alt="{$video_data.attr_alt}">
									<span class="name-over text-truncate">
										<span>{smarty_fewchars s=$video_data.video_title length=32}</span>
									</span>
								</span>
                                    <span class="thumb-info"><span class="ctg-info">
										<span class="rating wrap-icon">
										<svg class="svg-icon" width="35.97px" height="40.031px">
											<use xlink:href="#like"></use>
										</svg>
        								<span>{$video_data.likes_compact}</span>
										</span>
										<span class="text text-truncate">{$video_data.views_compact}</span>
									</span>
								</span>
                                </a>
                            </div>
                        </div>
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="sidebar">
            <div class="sidebar-block">
                <div class="sidebar-title"><span class="highlight">Top </span>Categories</div>
                <ul class="sidebar-lists">
                    <li class="sidebar-lists-item">
                        <a  href="{$casinos_data[1].url}" class="sidebar-lists-link clearfix"><span class="name wrap-icon">
							<svg class="svg-icon" width="40px" height="37px">
								<use xlink:href="#list"></use>
							</svg>
							<span class="text-truncate">{smarty_fewchars s=$casinos_data[1].name length=32}</span></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div class="sidebar-block">
                <div class="sidebar-title"><span class="highlight">Top </span>Casinos</div>
                <ul class="sidebar-lists">
                    <li class="sidebar-lists-item"><a href="#" class="sidebar-lists-link clearfix"><span class="name wrap-icon">
            <svg class="svg-icon" width="11px" height="10.969px">
                <use xlink:href="#model"></use>
            </svg>
        <span class="text-truncate">AZ Gals</span></span></a>
                    </li>
                </ul>
            </div>
            <div class="sidebar-block">
                <div class="sidebar-title"><span class="highlight">Top </span>Providers</div>
                <ul class="sidebar-lists">
                    <li class="sidebar-lists-item"><a href="#" class="sidebar-lists-link clearfix"><span class="name wrap-icon">
            <svg class="svg-icon" width="11px" height="10.969px">
                <use xlink:href="#link"></use>
            </svg>
        <span class="text-truncate">AZ Gals</span></span></a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

{include file="footer.tpl" tpl_name="video-categories-page"}