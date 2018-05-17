{foreach from=$results key=k item=video_data}
    {if $k%9==0 }{$ad_13}{/if}
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
                                        {assign var=voteCount value=$video_data.up_vote_count+$video_data.down_vote_count}
                                        {$voteCount}**********{$video_data.up_vote_count}&&&{$video_data.down_vote_count}
                                        {if empty($voteCount)}
                                            <span>100%</span>
										{else}
											<span>{math equation="height * width / division"
                                            height=$video_data.up_vote_count
                                            width=100
                                            division=$voteCount}%</span>
                                        {/if}

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
