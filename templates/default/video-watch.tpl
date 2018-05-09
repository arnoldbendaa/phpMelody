{include file="header.tpl" p="detail" tpl_name="video-watch"}
<div id="wrapper">
    <div class="page-layout-wrapper clearfix">
        <div class="page-layout_fixed">
            {include file="my-player.tpl"}
        </div>
        <div class="page-layout_content">
            <div class="page-layout_content-fixed">
                <div class="container">
                    <div class="wrap-heading clearfix">
                        <div class="sortings-navbar fullsize clearfix magic-line">
                            <div class="sortings-navbar-inner" >
                                <div class="sorting w33 active" id="relatedTab">
                                    <a href="#" class="sorting-choice text-center text-truncate" onclick="getRelatedVideos('{$video_data.video_title}',{$video_data.id},{$video_data.category})">Related Videos</a>
                                </div>
                                <div class="sorting w33" id="userTab">
                                    <a href="#" class="sorting-choice text-center text-truncate" onclick="getUserVideos({$user_id},{$video_data.id});">More from {$username}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="page-layout_content-fixed-replacement"></div>
            <div class="container">
                <div class="wrap-thumbs">
                    <div class="thumbs-lists side-list">
                        {foreach from=$related_video_list key=k item=related_video_data}
                        <div class="thumbs-item">
                            <div class="thumb">
                                <span class="thumb-preview video">
                                    <a href="{$related_video_data.video_href}"><img src="{$related_video_data.thumb_img_url}" alt="{$related_video_data.attr_alt}">
                                        {if $video_data.mark_new}<span class="label new">{$lang._new}</span>{/if}
                                        <span class="add-favorite wrap-icon">
                                    <svg class="svg-icon" width="17px" height="16px">
                                        <use xlink:href="#heart"></use>
                                    </svg>
                                </span><span class="add-watch-later wrap-icon">
                                    <svg class="svg-icon" width="12px" height="12px">
                                        <use xlink:href="#time"></use>
                                    </svg>
                                </span><span class="preview-info-group-br"><span class="time">{$related_video_data.duration}</span><span class="hd">
                                    <svg class="svg-icon" width="24px" height="20px">
                                        <use xlink:href="#hd"></use>
                                    </svg>
                                </span></span><span class="rating rat-video wrap-icon">
                                    <svg class="svg-icon" width="35.97px" height="40.031px">
                                        <use xlink:href="#like"></use>
                                    </svg>
                                </span>
                                    </a>
                                        </span><span class="thumb-info"><span class="name text-truncate"><span>{$related_video_data.video_title}</span></span>
                                    <span class="thumb-sub-info right wrap-icon"><span>{$related_video_data.views_compact}</span>
                                        <svg class="svg-icon" width="22px" height="16px">
                                            <use xlink:href="#eye2"></use>
                                        </svg>
                                    </span>
                                </span>
                            </div>
                        </div>
                            {foreachelse}
                            {$lang.top_videos_msg2}
                        {/foreach}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{literal}
<script type="text/javascript">

    function getRelatedVideos(videoTitle,videoId,videoCategory){
        $("#relatedTab").addClass('active');
        $("#userTab").removeClass('active');
        $.ajax({
            url:"{/literal}{$smarty.const._URL2}{literal}/ajax.php?p=video&do=getRelated&videoId="+videoId+"&category="+videoCategory+"&title="+videoTitle,
            type:"get",
            success:function(response){
                displayrelatedVideos(response);
            }
        })
    }
    function getUserVideos(userId,videoId) {
        console.log("test");
        $("#relatedTab").removeClass('active');
        $("#userTab").addClass('active');
        $.ajax({
            url:"{/literal}{$smarty.const._URL2}{literal}/ajax.php?p=video&do=getUserVideo&userId="+userId+"&videoId="+videoId,
            type:"get",
            success:function(response){
                displayrelatedVideos(response);
            }
        })
    }
    function displayrelatedVideos(response){
        lists = JSON.parse(response);
//        var length = lists.length;
//        if(lengh==undefined)
           var length = Object.keys(lists).length;
        var html = "";
        for(var i = 0 ; i < length; i++){
            html+=                        '<div class="thumbs-item">'+
                '<div class="thumb">'+
                '<span class="thumb-preview video">'+
                '<a href="'+lists[i].video_href+'"><img src="'+lists[i].thumb_img_url+'" alt="'+lists[i].thumb_img_url+'">';
            if (lists[i].mark_new)
                   html+='<span class="label new">{/literal}{$lang._new}{literal}</span>';
            html+=
        '<span class="add-favorite wrap-icon">'+
                '<svg class="svg-icon" width="17px" height="16px">'+
                '<use xlink:href="#heart"></use>'+
                '</svg>'+
                '</span><span class="add-watch-later wrap-icon">'+
                '<svg class="svg-icon" width="12px" height="12px">'+
                '<use xlink:href="#time"></use>'+
                '</svg>'+
                '</span><span class="preview-info-group-br"><span class="time">'+lists[i].duration+'</span><span class="hd">'+
                '<svg class="svg-icon" width="24px" height="20px">'+
                '<use xlink:href="#hd"></use>'+
                '</svg>'+
                '</span></span><span class="rating rat-video wrap-icon">'+
                '<svg class="svg-icon" width="35.97px" height="40.031px">'+
                '<use xlink:href="#like"></use>'+
                '</svg>'+
                '</span>'+
                '</a>';
                html+='</span><span class="thumb-info"><span class="name text-truncate"><span>'+ lists[i].video_title + '</span></span>'+
            '<span class="thumb-sub-info right wrap-icon"><span>'+lists[i].views_compact+'</span>'+
            '<svg class="svg-icon" width="22px" height="16px">'+
                '<use xlink:href="#eye2"></use>'+
                '</svg></span></span></div></div>';
        }
        $(".thumbs-lists").html(html);
    }
</script>
{/literal}
{include file="footer.tpl" p="detail" tpl_name="video-watch"}