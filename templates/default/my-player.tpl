<div class="container" id="myVideoPlayer">
    <div class="page-layout_fixed-responsed">
        <div class="wrap-embed animated" style="height:auto;top:50%;">
            <div class="embed-title">
                <div>{$video_data.video_title}</div>
            </div>
            <div class="embed">
                <div class="video-iframe">
                    <div id="Playerholder">Loading Player...</div>
                    {literal}
                    <script type="text/javascript" src="{/literal}{$smarty.const._URL2}{literal}/players/jwplayer6/jwplayer.js"></script>
                    <script type="text/javascript">jwplayer.key="{/literal}{$jwplayerkey}{literal}";</script>
                    <script type="text/javascript">
                        var flashvars = {
                            {/literal}
                            {if $video_data.source_id == 0}
                            file: '{$video_data.jw_flashvars.file}',
                            streamer: '{$video_data.jw_flashvars.streamer}',
                            {literal}rtmp: {{/literal}
                                {if $video_data.jw_flashvars.provider != ''} provider: '{$video_data.jw_flashvars.provider}',{/if}
                                {if $video_data.jw_flashvars.startparam != ''} startparam: '{$video_data.jw_flashvars.startparam}',{/if}
                                {if $video_data.jw_flashvars.loadbalance != ''} loadbalance: {$video_data.jw_flashvars.loadbalance},{/if}
                                {if $video_data.jw_flashvars.subscribe != ''} subscribe: {$video_data.jw_flashvars.subscribe},{/if}
                                {if $video_data.jw_flashvars.securetoken != ''} securetoken: "{$video_data.jw_flashvars.securetoken}",{/if}
                            },
                            {elseif $video_data.source_id == $_sources.localhost.source_id}
                            {literal}
                            file: '{/literal}{$video_data.url_flv}{literal}',
                            //image: '{/literal}{$video_data.preview_image}{literal}',
                            {/literal}
                            {elseif $video_data.source_id == $_sources.youtube.source_id}
                            {literal}
                            file: '{/literal}{$video_data.direct}{literal}',
                            //image: '//img.youtube.com/vi/{/literal}{$video_data.yt_id}{literal}/hqdefault.jpg',
                            {/literal}
                            {elseif $video_data.source_id == $_sources.mp3.source_id}
                            {literal}
                            file: '{/literal}{$video_data.url_flv}{literal}',
                            type: 'mp3',
                            //image: '{/literal}{$video_data.preview_image}',
                            {else}
                            {literal}
                            file: '{/literal}{$video_data.url_flv}{literal}',
                            //image: '{/literal}{$video_data.preview_image}',
                            {/if}
                            {literal}
                            flashplayer: "{/literal}{$smarty.const._URL2}{literal}/players/jwplayer6/jwplayer.flash.swf",
                            primary: "html5",
                            width: "100%",
                            {/literal}{if $playlist}{literal}
                            autostart: true,
                            {/literal}{else}{literal}
                            height: "100%",
                            aspectratio: "16:9",
                            autostart: "{/literal}{$video_data.video_player_autoplay}{literal}",
                            {/literal}{/if}{literal}
                            image: '{/literal}{$video_data.preview_image}{literal}',
                            stretching: "fill",
                            events: {
                                onComplete: function() {
                                    {/literal}{if $playlist}{literal}
                                    window.location = "{/literal}{$playlist_next_url}{literal}";
                                    {/literal}{else}{literal}
                                    if (pm_video_data.autoplay_next && pm_video_data.autoplay_next_url != "") {
                                        window.location = pm_video_data.autoplay_next_url;
                                    }
                                    {/literal}{/if}{literal}
                                },
                                onError: function(object) {
                                    ajax_request("video", "do=report&vid={/literal}{$video_data.uniq_id}{literal}&error-message="+ object.message, "", "", false);
                                    {/literal}{if $playlist}{literal}
                                    window.location = "{/literal}{$playlist_next_url}{literal}";
                                    {/literal}{/if}{literal}
                                }
                            },
                            logo: {
                                file: '{/literal}{$smarty.const._WATERMARKURL}{literal}',
                                link: '{/literal}{$smarty.const._WATERMARKLINK}{literal}',
                            },
                            "tracks": [
                                {/literal}{foreach from=$video_subtitles key=k item=video_subtitles}{literal}
                                { file: "{/literal}{$video_subtitles.filename}{literal}", label: "{/literal}{$video_subtitles.language}{literal}", kind: "subtitles" },
                                {/literal}{/foreach}{literal}
                            ]
                        };
                        {/literal}{$jw_flashvars_override}{literal}
                        jwplayer("Playerholder").setup(flashvars);
                    </script>
                    {/literal}
                </div>
            </div>
        </div>
    </div>
    <div class="page-layout_fixed-bottom">
        <div class="tools-panel clearfix">
            <div class="wrap-rate">
                <div class="rate">
                    <div class="likes">
                        <a id="flag_like_this_video" href="" class="like">
                            <svg class="svg-icon" width="456.8px" height="438.5px">
                                <use xlink:href="#rating-like"></use>
                            </svg>
                        </a>
                    </div>
                    <div class="progress">
                        <div class="progress-bar">
                            <div style="width: 60%;" class="indicator"></div>
                        </div>
                        <div class="progress-text">85% (45 VOTES)</div>
                    </div>
                    <div class="likes">
                        <a id="flag_dislike_this_video" href="" class="dislike">
                            <svg class="svg-icon" width="456.8px" height="438.5px">
                                <use xlink:href="#rating-dislike"></use>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <div class="tools">
                <div class="tools-item">
                    <a href="#myFancyBox" id="inline"  class="tools-btn wrap-icon wrap-tooltip">
                        <div class="tooltip">Info</div>
                        <svg class="svg-icon" width="24.75px" height="25px">
                            <use xlink:href="#info"></use>
                        </svg>
                    </a>
                </div>
                <div class="tools-item">
                    <a href="#" data-href="{$smarty.const._URL}/videoComment.{$smarty.const._FEXT}?vid={$video_data.uniq_id}" data-width="500" data-max-width="740" class="tools-btn wrap-icon js__btn-popup wrap-tooltip">
                        <div class="tooltip">Comments</div>
                        <svg class="svg-icon" width="24.75px" height="25px">
                            <use xlink:href="#comments"></use>
                        </svg>
                        <span class="comment-count">{$video_data.comment_count}</span>
                    </a>
                </div>
                <div class="tools-item">
                    <a href="#" data-href="helpers/screenshots.html" data-max-width="740" class="tools-btn wrap-icon js__btn-popup wrap-tooltip">
                        <div class="tooltip">Screenshots</div>
                        <svg class="svg-icon" width="24.72px" height="23.156px">
                            <use xlink:href="#windows"></use>
                        </svg>
                    </a>
                </div>
                <div class="tools-item">
                    <a href="#" data-toggle="dropdown" class="tools-btn wrap-icon wrap-tooltip">
                        <div class="tooltip">Add to Lists</div>
                        <svg class="svg-icon" width="24.96px" height="22.281px">
                            <use xlink:href="#heart-empty"></use>
                        </svg>
                    </a>
                    <div class="tools-drop well well-small" id="playlist-container">
                        <ul class="pm-playlist-items tools-drop-list">



                            {foreach from=$my_playlists key=index  item=playlist_data name=my_playlists_foreach}
                            <li data-playlist-id="{$playlist_data.list_id}" class="tools-drop-item {if $playlist_data.has_current_video} pm-playlist-item-selected{/if}">
                                <a href="" class="tools-drop-link" onclick="{if $playlist_data.has_current_video}playlist_remove_item({$playlist_data.list_id}, {$video_data.id});{else}playlist_add_item({$playlist_data.list_id}, {$video_data.id});{/if} return false;">
                                    {smarty_fewchars s=$playlist_data.title length=40}
                                    {if $playlist_data.has_current_video}
                                        <i class="icon-ok"></i>
                                    {/if}
                                </a>
                            </li>
                            {/foreach}
                            <li class="tools-drop-item">
                                <a href="" class="tools-drop-link">Add to New Playlist</a>
                            </li>


                        </ul>
                    </div>
                </div>
                <div class="tools-item">
                    <a href="#" data-toggle="dropdown" class="tools-btn wrap-icon wrap-tooltip">
                        <div class="tooltip">More</div>
                        <svg class="svg-icon" width="4.59px" height="23.125px">
                            <use xlink:href="#more-points"></use>
                        </svg>
                    </a>
                    <div class="tools-drop">
                        <ul class="tools-drop-list">
                            <li class="tools-drop-item"><a href="#" data-href="helpers/report.html" data-max-width="740" class="tools-drop-link js__btn-popup">Report</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="tools-item">
                    <a href="#" data-href="helpers/share.html" data-max-width="740" class="tools-btn wrap-icon js__btn-popup wrap-tooltip">
                        <div class="tooltip">Share</div>
                        <svg class="svg-icon" width="21.09px" height="22px">
                            <use xlink:href="#share"></use>
                        </svg>
                    </a>
                </div>
            </div>
            <div class="wrap-tools-views">
                <div class="tools-views">
                    <svg class="svg-icon" width="22px" height="16px">
                        <use xlink:href="#eye2"></use>
                    </svg>

                    <div class="wrap-overflow"><span class="text-truncate">{$video_data.site_views}</span>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-info">
            <div class="box-info-channel">
                <a href="#" class="wrap-tooltip">
                    <div class="tooltip big">Brazzers Network</div>
                    <img src="static/img/assets/box-info/channel.jpg" title="Brazzers Network">
                    <div class="name text-truncate">Brazzers Network Brazzers Network</div>
                </a>
            </div>
            <div class="wrap-overflow">
                <div class="price text-center">
                    <div class="special-offer">
                        <div class="offer-title">Today’s Special Price</div>
                        <div class="wrap-offer-cost">
                            <div class="offer-cost"> <span class="currency">$ </span><span class="price">29<span class="coins">.99</span></span><span class="period">/Month</span>
                            </div>
                            <div class="offer-cost"> <span class="currency">$ </span><span class="price">99<span class="coins">.99</span></span><span class="period">/Year</span>
                            </div>
                        </div>
                    </div><a href="#" class="price-btn text-truncate">Join Romance Energy now! Join Romance Energy now!</a>
                </div>
            </div>
            <div class="clear"></div>
        </div>
    </div>
</div>
<div style="display:none">
    <div id="myFancyBox">
        <div class="wrap-popup bordered">
            <div class="popup popup-wide">
                <div class="popup-content">
                    <div class="popup-container-outside">
                        <div class="popup-container-inside">
                            <div class="popup-subtitle">{$video_data.video_title}</div>
                            <div class="popup-text">
                                <p>
                                    {$video_data.description}
                                </p>
                            </div>
                        </div>
                        <div class="popup-separate"></div>
                        <div class="popup-container-inside">
                            <div class="wrap-overflow">
                                <div class="box-info-data-row">
                                    <div class="box-info-data">
                                        <div class="box-info-data-item wrap-icon"><span class="key">Views:</span><span class="value">{$video_data.site_views}</span>
                                        </div>
                                        <div class="box-info-data-item wrap-icon"><span class="key">Duration:</span><span class="value">{$video_data.duration}</span>
                                        </div>
                                        <div class="box-info-data-item wrap-icon"><span class="key">Submitted:</span><span class="value">{$video_data.added_timestamp|date_format:"%Y-%m-%d "}</span>
                                        </div>
                                        <div class="box-info-data-item wrap-icon"><span class="key">By :</span><span class="value">
                                                {$video_data.submitted}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="popup-separate"></div>
                                <div class="box-info-data-row">
                                    <div class="specific-list info">
                                        <div class="specific-item">
                                            <div class="specific-label">Categories:</div>
                                            <div class="specific-value">
                                                <div class="list-links">
                                                    <span>{$category_name}</span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="specific-item">
                                            <div class="specific-label">{$lang.tags}</div>
                                            {if !empty($tags)}
                                                <div class="video-tags">
                                                    {$tags}
                                                </div>
                                            {/if}
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="wrap-overflow"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
