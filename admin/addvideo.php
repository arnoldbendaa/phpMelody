<?php
// +------------------------------------------------------------------------+
// | PHP Melody ( www.phpsugar.com )
// +------------------------------------------------------------------------+
// | PHP Melody IS NOT FREE SOFTWARE
// | If you have downloaded this software from a website other
// | than www.phpsugar.com or if you have received
// | this software from someone who is not a representative of
// | PHPSUGAR, you are involved in an illegal activity.
// | ---
// | In such case, please contact: support@phpsugar.com.
// +------------------------------------------------------------------------+
// | Developed by: PHPSUGAR (www.phpsugar.com) / support@phpsugar.com
// | Copyright: (c) 2004-2013 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '2';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
if(isset($_GET['step'])) {
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_uniform = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
}
$_page_title = 'Add video';
include('header.php');

define('PHPMELODY', true);

$message = '';
$allowed_ext = array('.flv', '.mp4', '.mov', '.wmv', '.divx', '.avi', '.mkv', '.asf', '.wma', '.mp3', '.m4v', '.m4a', '.3gp', '.3g2');

$step = (int) $_GET['step'];
if($step == '')
	$step = 1;


if($step == 2 && isset($_POST['Submit']))
{
	if(trim($_POST['url']) == '')
	{
		$step = 1;
		$message = pm_alert_error('Please provide a valid URL.');	
	}
}


function add_video_form($video_details = array())
{
	global $modframework;
	$categories_dropdown_options = array(
									'attr_name' => 'category[]',
									'attr_id' => 'main_select_category',
									'select_all_option' => false,
									'spacer' => '&mdash;',
									'selected' => 0,
									'other_attr' => 'multiple="multiple"'
									);

	if ($video_details['url_flv'] == '') 
	{
		$video_lookup = pm_alert_warning('<strong>Sorry, no video was found at this location.</strong> Please try again or use another URL.');
	}
	
	if ($video_details['video_title'] != '')
	{
		$video_details['video_slug'] = sanitize_title($video_details['video_title']);
	}
	
// Generate a video title from the file name
if(isset($_GET['filename']) && $_GET['filename'] != '')
{
	$_GET['filename'] = urldecode($_GET['filename']);
	
	$uploaded_file = pathinfo($_GET['filename']);
	$uploaded_file_name =  basename($_GET['filename'],'.'.$uploaded_file['extension']);
	$unwanted_chars = array("-", "_", ",","'",".","(",")","[","]","*","{","}","  ","   ");
	$video_details['video_title'] = ucwords(str_replace($unwanted_chars, " ", $uploaded_file_name));
}
?>
<form method="post" enctype="multipart/form-data" action="addvideo.php?step=3" name="addvideo_form_step2" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted) or make sure the URL you entered at STEP 1 is valid.')">
<div class="container row-fluid" id="post-page">
    <div class="span9">
	<?php echo $video_lookup; ?>
    <div class="widget border-radius4 shadow-div">
    <h4>Title &amp; Description</h4>
    <div class="control-group">
    <input name="video_title" type="text" id="must" value="<?php echo str_replace('"', '&quot;', $video_details['video_title']); ?>" style="width: 99%;" />
    <div class="permalink-field">

	<?php if (_SEOMOD) : ?>
		<strong>Permalink:</strong> <?php echo _URL .'/';?><input class="permalink-input" type="text" name="video_slug" placeholder="<?php echo urldecode($video_details['video_slug']);?>" value="<?php echo urldecode($video_details['video_slug']);?>" /><?php echo '_UniqueID.html';?>
	<?php endif; ?>

    </div>
    </div>
    
    <div class="control-group">
	<div class="pull-right" style="position: absolute; top: 4px; right: 4px;">
	<span class="btn fileinput-button">
		<span>Upload images</span>
		<input type="file" name="file" id="upload-file-wysiwyg-btn" multiple />
	</span>
    </div>
    </div>
    
    <div class="controls" id="textarea-dropzone">
	<textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce" style="width:100%"><?php echo nl2br($video_details['description']); ?></textarea>
	<span class="upload-file-dropzone"></span>
	<span class="autosave-message"></span>
    </div>
    </div>
	
	<div class="widget border-radius4 shadow-div" id="custom-fields">
	<h4>Custom Fields <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank"><i class="icon-question-sign"></i></a></h4>
    	<div class="control-group">
		<div class="row-fluid">
			<div class="span3"><strong>Name</strong></div>
			<div class="span9"><strong>Value</strong></div>
		</div>
		<?php if (count($_POST['meta']) > 0) :
				foreach ($_POST['meta'] as $meta_id => $meta) : 
					$meta['meta_key'] = $meta['key'];
					$meta['meta_value'] = $meta['value'];
					
					echo admin_custom_fields_row($meta_id, $meta);
				endforeach;
			endif; ?>
		<?php echo admin_custom_fields_add_form(0, IS_VIDEO); ?>
		
		</div>
	</div>

    </div><!-- .span8 -->
    <div class="span3">
		<div class="widget border-radius4 shadow-div upload-file-dropzone" id="video-thumb-dropzone">
        <div class="pull-right">
			<span class="btn fileinput-button">
				<span>Change</span>
				<input type="file" name="file" id="upload-video-image-btn" />
			</span>
		</div>
		<h4>Thumbnail</h4>
            <div class="control-group container-fluid">
            <div class="controls row-fluid">
            <div id="video-thumb-container">
			<?php			
			if (($video_details['source_id'] == 0 || $video_details['source_id'] == 1 || $video_details['source_id'] == 2) && strpos($video_details['yt_thumb'], 'http') === false && $video_details['yt_thumb'] != '') 
			{
                $video_details['yt_thumb'] = _THUMBS_DIR . $video_details['yt_thumb'];
			}
			if (empty($video_details['yt_thumb']) && empty($video_details['yt_thumb_local'])) : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="Click here to specify a custom thumbnail URL" class="pm-sprite no-thumbnail"></a>
            <?php else : ?>
            <a href="#" id="show-thumb" rel="tooltip" title="Click here to specify a custom thumbnail URL"><img src="<?php echo make_url_https($video_details['yt_thumb']); ?>" id="must" style="display:block;min-width:120px;width:100%;min-height:80px; no-repeat center center;" /></a>
            <?php endif; ?>
            <div class="">
                <div id="show-opt-thumb">
                <br />
                <input type="text" name="yt_thumb" value="<?php echo $video_details['yt_thumb']; ?>" class="bigger span10" placeholder="http://" /> <i class="icon-info-sign" rel="tooltip" data-position="top" title="The thumbnail will refresh after you hit the 'Submit' button."></i>
                </div>
            </div><!-- .span8 -->
            </div>
			<div class="">
            </div><!-- .span4 -->
			
            </div><!-- .controls .row-fluid -->
            </div>
        </div><!-- .widget -->
        
		<div class="widget border-radius4 shadow-div">
		<h4>Category</h4>
            <div class="control-group">
            <div class="controls">
            <input type="hidden" name="categories_old" value="<?php echo $video_details['category'];?>"  />
            <?php 
			$categories_dropdown_options = array(
											'attr_name' => 'category[]',
											'attr_id' => 'main_select_category must',
											'attr_class' => 'category_dropdown span12',
											'select_all_option' => false,
											'spacer' => '&mdash;',
											'selected' => explode(',', $video_details['category']),
											'other_attr' => 'multiple="multiple"'
											);
			echo categories_dropdown($categories_dropdown_options);
            ?>
            </div>
			<a href="#" id="inline_add_new_category" />+ Add New Category</a>
			<div id="inline_add_new_category_form" class="hide">
				<input name="add_category_name" type="text" placeholder="Category name" id="add_category_name" />
				<input name="add_category_slug" type="text" placeholder="Slug" /> <a href="#" rel="tooltip" title="Slugs are used in the URL and can only contain numbers, letters, dashes and underscores."><i class="icon-info-sign" rel="tooltip" title="Slugs are used in the URL and can only contain numbers, letters, dashes and underscores."></i></a>
				<label>Create in (<em>optional</em>)</label>
				<?php 
					$categories_dropdown_options = array(
											'first_option_text' => '&ndash; Parent Category &ndash;', 
											'first_option_value' => '-1',
											'attr_name' => 'add_category_parent_id',
											'attr_id' => '',
											'attr_class' => '',
											'select_all_option' => true,
											'spacer' => '&mdash;'
											);
					echo categories_dropdown($categories_dropdown_options); 
				?>
				<br />
				<button name="add_category_submit_btn" value="Add category" class="btn btn-mini btn-normal" />Add New Category</button>
				<span id="add_category_response"></span>
			</div>
            </div>
		</div><!-- .widget -->

		<div class="widget border-radius4 shadow-div upload-file-dropzone" id="subtitle-dropzone">
			<h4>Subtitles  <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="Subtitles" data-content="Select the language you intend to assign a subtitle file for and then click the 'Upload' button. You can also replace or delete existing subtitles in the same manner. If you don't see the 'Delete' link for a subtitle, simply refresh this page."></i></h4>
			<div>
				<select name="language" id="language">
					<option value="">- Choose language -</option>
					<?php
					$languages = a_get_languages();
					foreach($languages as $tag => $label)
					{
						echo '<option value="'. $tag .'">'. $label .'</option>';
					}
					?>
				</select>
				<div class="pull-right">
					<span class="btn fileinput-button">
						<span>Select &amp; Upload</span> 
						<input type="file" name="file" id="upload-subtitle-btn" />
					</span>
				</div>
			</div>

			<div class="control-group container-fluid">
				<div class="controls row-fluid">
					<ul class="unstyled" id="showSubtitle"></ul>
				</div>
			</div>
		</div><!-- .widget -->

        <div class="widget border-radius4 shadow-div">
        <h4>Publish</h4>
			<?php
            if($video_details['yt_length'] > 0) {	
                $yt_minutes = intval($video_details['yt_length'] / 60); 
                $yt_seconds = intval($video_details['yt_length'] % 60); 
            } else {
                $yt_minutes = 0;
                $yt_seconds = 0;
            }
            ?>
            <div class="control-group">
            <label class="control-label" for="">Duration: <span id="value-yt_length"><strong><?php echo sec2min($video_details['yt_length']);?></strong></span> <a href="#" id="show-duration">Edit</a></label>
            <div class="controls" id="show-opt-duration">
            <input type="text" name="yt_min" id="yt_length" value="<?php echo $yt_minutes; ?>" size="4" class="smaller-select" /> <small>min.</small>
            <input type="text" name="yt_sec" id="yt_length" value="<?php echo $yt_seconds; ?>" size="3" class="smaller-select" /> <small>sec.</small>
            <input type="hidden" name="yt_length" id="yt_length" value="<?php echo trim(($yt_minutes * 60) + $yt_seconds); ?>" />
            </div>
            </div>

			<div class="control-group">
            <label class="control-label" for="">Comments: <span id="value-comments"><strong><?php echo ($video_details['allow_comments'] == 1) ? 'allowed' : 'closed';?></strong></span> <a href="#" id="show-comments">Edit</a></label>
            <div class="controls" id="show-opt-comments">
                <label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($video_details['allow_comments'] == 1) echo 'checked="checked"';?> /> Allow comments on this video</label>
				<?php if ($config['comment_system'] == 'off') : ?>
				<div class="alert">
				Comments are disabled site-wide. 
				<br />
				To enable comments, visit the <a href="settings.php?view=comment" title="Settings page" target="_blank">Settings</a> page.
				</div>
				<?php endif;?>
            </div>
            </div>
			
			<div class="control-group">
            <label class="control-label" for="">Embedding: <span id="value-embedding"><strong><?php echo ($video_details['allow_embedding'] == 1) ? 'allowed' : 'disallowed';?></strong></span> <a href="#" id="show-embedding">Edit</a></label>
            <div class="controls" id="show-opt-embedding">
                <label><input name="allow_embedding" id="allow_embedding" type="checkbox" value="1" <?php if ($video_details['allow_embedding'] == 1) echo 'checked="checked"';?> /> Allow embedding for this video</label>
				<?php if ($config['allow_embedding'] == '0') : ?>
				<div class="alert">
				Embedding is disabled site-wide. 
				<br />
				To enable embedding, visit the <a href="settings.php?view=video" title="Settings page" target="_blank">Settings</a> page.
				</div>
				<?php endif;?>
				
            </div>
            </div>
			
            <div class="control-group">
            <label>Featured: <span id="value-featured"><strong><?php echo ($video_details['featured'] == 1) ? 'yes' : 'no';?></strong></span> <a href="#" id="show-featured">Edit</a></label>
            <div class="controls" id="show-opt-featured">
                <label><input type="checkbox" name="featured" id="featured" value="1" <?php if($video_details['featured'] == 1) echo 'checked="checked"';?> /> Yes, mark as featured</label>
            </div>
            </div>
            <div class="control-group">
            <label class="control-label reqreg" for="">Requires registration: <span id="value-register"><strong><?php echo ($video_details['restricted'] == 1) ? 'yes' : 'no';?></strong></span> <a href="#" id="show-visibility">Edit</a></label>
            <div class="controls" id="show-opt-visibility">
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php echo ($video_details['restricted'] == 1) ? 'checked="checked"' : '';?> /> Yes</label> 
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php echo ($video_details['restricted'] != 1) ? 'checked="checked"' : '';?> /> No</label>
            </div>
            </div>
			
            <div class="control-group">
            <label class="control-label" for="">Publish: <span id="value-publish"><strong>immediately</strong></span> <a href="#" id="show-publish">Edit</a></label>
            <div class="controls" id="show-opt-publish">
            <?php echo ($_POST['date_month'] != '') ? show_form_item_date( pm_mktime($_POST) ) : show_form_item_date();	?>
            </div>
            </div>
            <?php 
            $modframework->trigger_hook('admin_addvideo_publishoptions');
            ?>
        </div><!-- .widget -->
		
		
		<div class="widget border-radius4 shadow-div">
		<h4>Video Source</h4>
		
		<?php

		if ($video_details['source_id'] == 0 && is_array($video_details['jw_flashvars'])) :
			$pieces = explode(';', $video_details['url_flv'], 2);
	    ?>
	    <div class="control-group">
	    <label>File <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="" data-content="Internal URL of video or audio file you want to stream.<br />This is the equivalent of JW Player's <code><i>file</i></code> flashvar. "></i> <a href="#" id="show-vs1">Edit</a></label>
	    <div class="controls" id="show-opt-vs1">
	    <input name="jw_file" type="text" id="must" value="<?php echo $pieces[0]; ?>" class="bigger span12" />
	    </div>
	    </div>
	    <?php else: ?>
	    <?php if ($video_details['source_id'] != 1 && $video_details['source_id'] != 2) : ?>
	    <div class="control-group">
	    <label>Original Video URL <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="Warning" data-content="Changing this URL will re-import the video. All other data (title, tags, description, etc.) will remain the same."></i> <a href="#" id="show-vs1">Edit</a></label>
	    <div class="controls" id="show-opt-vs1">
	    <input type="text" name="direct" class="bigger span12" value="<?php echo $video_details['direct']; ?>" />
	    <input type="hidden" name="direct-original" value="<?php echo $video_details['direct']; ?>" placeholder="http://"  />
	    </div>
	    </div>
	    <?php endif; ?>
	    <div class="control-group">
	    <label>File Location <i class="icon-info-sign" rel="popover" data-trigger="hover" data-animation="true" title="Warning" data-content="Changing the FLV/MOV/WMV/MP4 location of this video may cause it to stop working!"></i> <a href="#" id="show-vs2">Edit</a></label>
	    <div class="controls" id="show-opt-vs2">
	    <input type="text" name="url_flv" value="<?php echo $video_details['url_flv']; ?>" class="bigger span12" />	
	    <input type="hidden" name="url_flv-original" value="<?php echo $video_details['url_flv']; ?>" placeholder="http://" />
	    </div>
	    </div>
	    <?php endif; ?>
        </div><!-- .widget -->

		<div class="widget border-radius4 shadow-div">
		<h4>Tags</h4>
            <div class="control-group">
            <div class="controls">
                <div class="tagsinput" style="width: 100%;">
                <input type="text" name="tags" value="<?php echo $video_details['tags']; ?>" id="tags_addvideo_1" />
                </div>
            </div>
            </div>
        </div><!-- .widget -->
        <?php 
		$modframework->trigger_hook('admin_addvideo_input');
		?>
    </div>
    
</div>
<div class="clearfix"></div>
<input type="hidden" name="language" value="1" />
<input type="hidden" name="yt_id" value="<?php echo $video_details['yt_id']; ?>" />
<input type="hidden" name="url_flv" value="<?php echo $video_details['url_flv']; ?>" />
<input type="hidden" name="source_id" value="<?php echo $video_details['source_id']; ?>" />
<input type="hidden" name="submitted_user_id" value="<?php echo $video_details['submitted_user_id']; ?>" />
<input type="hidden" name="submitted" value="<?php echo $video_details['submitted']; ?>" />
<input type="hidden" name="mp4" value="<?php echo $video_details['mp4']; ?>" />
<input type="hidden" name="direct" value="<?php echo $video_details['direct']; ?>" />
<input type="hidden" name="age_verification" value="0" />
<input type="hidden" name="uniq_id" value="<?php echo $video_details['uniq_id']; ?>" />
<input type="hidden" name="upload-type" value="" />
<input type="hidden" name="p" value="upload" /> 
<input type="hidden" name="do" value="upload-image" />

<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
	<button type="submit" name="submit" value="Submit" class="btn btn-small btn-success btn-strong">Add video</button>
	</div>	
</div>
</div><!-- #list-controls -->

<?php
if($video_details['yt_id'] == '') 
	$video_details['yt_id'] = generate_activation_key(9); 
?>
</form>
<?php
} // add_video_form()

$video_details = array(	'uniq_id' => '',
						'video_title' => '',
						'description' => '',
						'yt_id' => '',
						'yt_length' => '',
						'category' => '',
						'submitted' => '',
						'source_id' => '',
						'language' => '',
						'age_verification' => '',
						'url_flv' => '',
						'yt_thumb' => '',
						'yt_thumb_local' => '',
						'mp4' => '',
						'direct' => '',
						'tags' => '', 
						'featured' => 0,
						'added' => '',
						'restricted' => 0, 
						'allow_comments' => 1,
						'allow_embedding' => 1
						);

?>
<script language="javascript">
function checkFields(Form) {

	var msg;
	if(Form.elements.url.value == "")
		msg = "Please insert a valid link as instructed below.";
	
	if(msg)
	{
		document.forms["add"].elements.url.style.background = "#FFDDDE";
		alert(msg);
		return false;
	}
	else 
		return true;
}
</script>

<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">Adding self-hosted video</a></li>
            <li><a href="#help-bulk" data-toggle="tab">Adding a remote video</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>This page makes adding videos from remote or even local sources as easy as copy/pasting a URL.</p>
            <p>The allowed URLs are either your self-hosted videos (*.flv, *.mp4, *.wmv, *.mov, etc.) or videos hosted by remote services (Youtube, Vimeo, DailyMotion, etc.)</p>
            <p>You can also use the "ADD VIDEO" button located in the header to quickly paste a video URL.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
              <p>If you decide to self-host videos, you can use a 3rd party service such as AWS S3 or even your own hosting provider. The form below allows you to add self-hosted videos from any location. Just paste the URL to your *.flv, *.mp4, *.wmv, *.mov video below.</p>
            </div>
            <div class="tab-pane fade" id="help-bulk">
            <p>Remote videos are hosted by 3rd party video sites. Below is a list of supported sites:</p>
             <ul style="height:200px; overflow-y: scroll; margin:3px 0;padding: 3px; color:#666; border: 1px solid #e2d59c; box-shadow: inset 0 1px 2px #ccc;" class="border-radius3">
             <?php
                $sources = a_fetch_video_sources();
                $sources = array_reverse($sources);
				$sources = array_sort($sources, 'source_name', SORT_ASC);
                $counter = 1;
                
                foreach ($sources as $k => $src)
                {
                    if (is_int($k) && $k >= 2): 
                    ?>
                    <li><?php echo $counter.'. '. ucfirst($src['source_name']);?> <small>(e.g. <?php echo $src['url_example'];?>)</small></li>
                    <?php 
                    $counter++;
                    endif;
                }
             ?>
             </ul>
             <p></p>
            <p>After pasting the desired  URL below, PHP Melody will automatically retrieve as much data as possible from the remote location. This includes, thumbnails, video title, description and so on. On some occasions you will have to add such data manually.</p>
            <p>Please note that no video files will be downloaded to your domain in this process.</p>
            <p>Learn how to use the <strong>custom fields</strong>: <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank">http://help.phpmelody.com/how-to-use-the-custom-fields/</a></p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    
    <div class="content">
	<a href="#" id="show-help-assist">Help</a>

	<?php if((isset($_GET['filename']) && $_GET['filename'] != '') || $_POST['source_id'] == 1) : ?>
	<h2>Upload Video</h2>	
	<?php else : ?>
	<h2>Add Video from URL</h2>	
	<?php endif; ?>

	<?php 	
		echo $message; 

		switch($step)
		{

			case 1:		//	STEP 1
	?>

	<form name="add" action="addvideo.php?step=2" method="post" class="form-inline" onSubmit="return checkFields(this);">
	<input type="text" id="addvideo_direct_input" name="url" size="30" class="span5" placeholder="http://" /> 
	<button type="submit" id="addvideo_direct_submit" name="Submit" class="btn">Step 2 &raquo;</button>  <strong><small><a href="#" id="show-help-link-assist">Need help?</a></small></strong>
	</form>
	<hr />
	<?php
	break;
	
	case 2:		//	STEP 2

	$modframework->trigger_hook('admin_addvideo_step2_pre');
		if(isset($_POST['Submit']) || $_GET['url'] != '' || isset($_GET['filename']))
		{
			if ($_POST['uniq_id']) //@since v2.3
			{
				$uniq_id = $_POST['uniq_id'];
			}
			else
			{
				$uniq_id = generate_video_uniq_id();
			}

			$video_details['uniq_id'] = $uniq_id;

			if($_POST['url'] != '' || $_GET['url'] != '')
				$url = (isset($_POST['url'])) ? trim($_POST['url']) : trim($_GET['url']);
			
			if($_POST['submitted'] != '' || $_GET['submitted'] != '')
			{
				$submitted = (isset($_POST['submitted'])) ? $_POST['submitted'] : trim($_GET['submitted']);
				$submitted_user_id = username_to_id($submitted);// $userdata['id'];
			}
			else
			{
				$submitted = $userdata['username'];
				$submitted_user_id = $userdata['id'];
			}
			/*
				MODE
				1 = Outsource (e.g. youtube)
				2 = Direct URL to video file
				3 = Direct URL/Path/Filename to video hosted locally
			*/
			
			$mode = 0;
			$temp = '';
			
			$url = expand_common_short_urls($url);
			
			//	Is this a direct link to a video file?
			if (strpos($url, '?') !== false)
			{
				$temp = explode('?', $url);
				$url = $temp[0];
			}
			
			$ext = pm_get_file_extension($url, true);
			
			if (is_array($temp) && count($temp) > 0)
			{
				$url = '';
				$temp[0] = rtrim($temp[0], '?');
				$temp[0] = $temp[0] .'?';
				foreach ($temp as $k => $v)
				{
					$url .= $v;
				}
			}
			
			if(in_array($ext, $allowed_ext) && (preg_match('/photobucket\.com/', $url) == 0))
			{
				if(!is_url($url))
				{
					// maybe it's an IP address
					if (is_ip_url($url))
					{
						$mode = 2;
					}
					else
					{
						$mode = 3;
					}
				}
				else if(strpos($url, _URL) !== false)
				{
					$mode = 3;
				}
				else
				{
					// filenames that look like domains pass as URLs (e.g. some-file.info.mp4) 
					// so we need to check them again for http(s), "//" and www
					if ( ! preg_match('%^((http(s?)\://)|(//)|(www\.))%', $url)) 
					{
						$mode = 3;
					}
					else 
					{
						$mode = 2;
					}
				}
			}
			elseif(is_url($url))
			{
				$mode = 1;
			}
			else	//	default;
			{
				$mode = 2;
			}
			if(isset($_GET['filename']) && $_GET['filename'] != '')
				$mode = 3;
			
			//	Build the $video_details array;
			switch($mode)
			{
				case 1: 	//	 Outsource (e.g. youtube); 
					$sources = a_fetch_video_sources();
					$use_this_src = -1;

					if($sources === false || count($sources) == 0)
					{
						$message = "There are no sources available.";
						break;
					}
					
					foreach($sources as $src_id => $source)
					{
						if($use_this_src > -1)
						{
							break;
						}
						else
						{
							if(@preg_match($source['source_rule'], $url))
							{
								$use_this_src = $source['source_id'];
							}
						}
					}

					if($use_this_src > -1)
					{
						if(!file_exists( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php"))
						{
							$message = "File '/src/" . $sources[ $use_this_src ]['source_name'] . ".php'" . " not found.";
							break;
						}
						else
						{
							$temp = array();
							$do_main = $sources[ $use_this_src ]['php_namespace'] .'\do_main';
							if ( ! function_exists($do_main))
							{
								require_once( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php");
							}
							$do_main($temp, $url);
							
							$video_details = array_merge($video_details, $temp);
							
							unset($temp);
							
							$video_details['source_id'] = $use_this_src;
						}
					}
					else
					{
						$message = "<strong>The submitted video source might not be supported</strong>. For a full list of supported sites, open the 'Help' section (Top right of this page).";
					}
				break;
				
				case 2:		//	2 = direct link to .flv/.mp4 (outsource)
					if(!is_url($url) && ! is_ip_url($url))
					{
						$message = '<strong>'.$url.'</strong><br />This doesn\'t look like a valid link. Please <a href="addvideo.php?step=1">return</a> and try again.';
						break;
					}
					$video_details['source_id'] = 2;
					$video_details['url_flv'] = $url;
					$video_details['direct'] = $url;
				break;
				case 3:		//	flv hosted locally or just uploaded
				
					if(isset($_GET['filename']) && $_GET['filename'] != '')
					{
						$contents = get_config('last_video');
						update_config('last_video', '');
						
						//	try the backup file
						if($contents == '')
						{
							$fp = fopen('tmp.pm', 'r');
							$contents = fread($fp, 512);
							fclose($fp);
						}
						
						//	clear file contents anyway
						$fp = fopen('tmp.pm', 'w');
						fwrite($fp, '');
						fclose($fp);
						
						if ($contents == '')	
						{
							$message  = 'Could not retreive the name of the uploaded file. ';
							$message .= '<br />Check your <a href="'. _URL .'/'._ADMIN_FOLDER.'/readlog.php">System log</a> for any error messages.';
							
							if ( ! is_writable(ABSPATH .''._ADMIN_FOLDER.'/tmp.pm'))
							{
								$message .= '<br />Make sure the "<em>/'._ADMIN_FOLDER.'/tmp.pm</em>" file has the required permissions (0777) ';
								$message .= 'and then try uploading the video again.';
							}
						}
						else
						{
							//	get filename
							$content  = explode("/", $contents);
							$filename = $content[ count($content)-1 ];
							
							//	move the new file to the videos directory 
							$oldpath = $contents;
							$newpath = _VIDEOS_DIR_PATH . $filename;
							
							if ($oldpath != $newpath)
							{
								if(!rename($oldpath, $newpath))
								{
									$message  = 'Could not move uploaded file to the uploads directory. ';
									$message .= 'Make sure the uploads directory is writable (0777).';
									break;
								}
							}
							$video_details['url_flv'] = $filename;
							$video_details['direct'] = $filename;
							
						}				
					}
					else
					{
						//	this means $url is either the path or a direct link to the .flv file whick is hosted locally(!)
						//	we only need the filename
						$temp = explode("/", $url);
						$video_details['url_flv'] = $temp[ count($temp)-1 ];
						unset($temp);
					}
					$sources = a_fetch_video_sources();
					
					$use_this_src = -1;
					foreach($sources as $src_id => $source)
						if($source['source_name'] == 'localhost')
							$use_this_src = $source['source_id'];
						$video_details['source_id'] = ($use_this_src != -1) ? $use_this_src : 1; //	1 = Default for LOCALHOST
				break;
			}
			$modframework->trigger_hook('admin_addvideo_step2_mid');
			//	Prevent adding the same video twice
			if ($video_details['direct'] != '')
			{
				$sql = "SELECT * FROM pm_videos_urls WHERE direct = '". $video_details['direct'] ."'";
				
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$message .= 'This video is already in your database.';
					$message .= '</div><div><br />';
					$message .= '<input name="view_video" type="button" value="Watch this video" onClick="location.href=\''. _URL .'/watch.php?vid='. $row['uniq_id'] .'\'" class="btn" /> ';
					$message .= '<input name="edit_video" type="button" value="Edit video &raquo;" onClick="location.href=\'modify.php?vid='. $row['uniq_id'] .'\'" class="btn btn-info" />';
					$message .= '</strong>';
				}
				unset($row, $sql, $result);
			}
			if (strlen($message) == 0 && $video_details['url_flv'] != '')
			{
				$sql = "SELECT * FROM pm_videos WHERE url_flv = '". $video_details['url_flv'] ."'";
				
				$result = mysql_query($sql);
				if (mysql_num_rows($result) > 0)
				{
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$message .= 'This video is already in your database.';
					$message .= '</div><div><br />';
					$message .= '<input name="view_video" type="button" value="Watch this video" onClick="location.href=\''. _URL .'/watch.php?vid='. $row['uniq_id'] .'\'" class="btn" /> ';
					$message .= '<input name="edit_video" type="button" value="Edit video &raquo;" onClick="location.href=\'modify.php?vid='. $row['uniq_id'] .'\'" class="btn btn-info" />';
					$message .= '</strong>';

				}
				unset($row, $sql, $result);
			}
			$modframework->trigger_hook('admin_addvideo_step2_post');
			if($message != '')
			{
				echo pm_alert_info($message);
			}
			else	//	show form
			{
				$video_details['submitted_user_id'] = (int) $submitted_user_id;
				$video_details['submitted'] = $submitted;
				add_video_form($video_details);
			}
		}	//	endif isset(POST or GET)
		else
		{
			echo "<a href=\"addvideo.php?step=1\">&larr; Please go to Step 1</a>";
			if ( ! headers_sent())
			{
				header("Location: addvideo.php?step=1");
			}
			else 
			{
				echo '<meta http-equiv="refresh" content="0;URL=addvideo.php?step=1" />';
			}
			exit();
		}
	break;
	case 3:		//	STEP 3
	
		$modframework->trigger_hook('admin_addvideo_step3_pre');

		if(isset($_POST['submit']))
		{
			$required_fields = array('video_title' => 'The "Video Title" field cannot be empty',
									'url_flv' => 'A direct link to the video file is missing', 
									'category' => 'Please assign at least one category to this video'
									);
			$message = '';
			
			foreach($video_details as $field => $value)
			{
				if ($field == 'category' && is_array($_POST[$field]))
				{
					$_POST[$field] = implode(',', $_POST[$field]);
				}
				$video_details[$field] = trim($_POST[$field]);
				if(trim($_POST[$field]) == '' && array_key_exists($field, $required_fields))
					$message .= $required_fields[$field] . '<br />';
			}

			$video_details['yt_length'] = ($_POST['yt_min'] * 60) + $_POST['yt_sec'];
			$video_details['meta'] = $_POST['meta'];
			
			$added = validate_item_date($_POST);
			if ($added === false)
			{
				$message .= "Invalid date. Please correct it.<br />";
			}
			else
			{
				$video_details['added'] = pm_mktime($added);
			}
			
			if($message != '')
			{
				echo pm_alert_error($message);
				add_video_form($video_details);
				break;
			}
			else
			{
				$message = '';
				//	check if this video already exists
				if(count_entries('pm_videos', 'url_flv', $video_details['url_flv']) > 0)
				{
					$message .= "This video (".$video_details['url_flv'].") is already in your database. Please go back and make the right adjustments.<br />";
				}
				elseif( ($video_details['direct'] != "") && (count_entries('pm_videos_urls', 'direct', $video_details['direct']) > 0))
				{
					$message .= "This direct link <em>'".$video_details['direct']."'</em> already exists in your database. <br />Please go back and make the right adjustments.<br />";
				}
				else
				{
					if ($_POST['uniq_id']) //@since v2.3
					{
						$uniq_id = $_POST['uniq_id'];
					}
					else
					{
						$uniq_id = generate_video_uniq_id();
					}

					$video_details['uniq_id'] = $uniq_id;
					$modframework->trigger_hook('admin_addvideo_step3_mid');
					
					//	upload, download or rename thumbnail file
					if ($video_details['yt_thumb_local'] != '')
					{
						$tmp_parts = explode('/', $video_details['yt_thumb_local']);
						$thumb_filename = array_pop($tmp_parts);
						$tmp_parts = explode('.', $thumb_filename);
						$thumb_ext = array_pop($tmp_parts);
						$thumb_ext = strtolower($thumb_ext);
						$renamed = false;
						
						if (file_exists(_THUMBS_DIR_PATH . $thumb_filename))
						{
							if (rename(_THUMBS_DIR_PATH . $thumb_filename, _THUMBS_DIR_PATH . $uniq_id . '-1.'. $thumb_ext))
							{
								$video_details['yt_thumb'] = $uniq_id . '-1.'. $thumb_ext;
								$renamed = true;
							}
						}
			
						if ( ! $renamed)
						{
							$video_details['yt_thumb'] = $video_details['yt_thumb_local'];
						}

						generate_social_thumb(_THUMBS_DIR_PATH . $video_details['yt_thumb']);
					}
					else
					{
						//	download thumbnail
						$sources = a_fetch_video_sources();
						$use_this_src = -1;
						
						foreach($sources as $src_id => $source)
						{
							if($src_id == $video_details['source_id'])
							{
								$use_this_src = $source['source_id'];
								break;
							}
						}
						
						$download_thumb = $sources[ $use_this_src ]['php_namespace'] .'\download_thumb';
						if ( ! function_exists($download_thumb))
						{
							require_once( "./src/" . $sources[ $use_this_src ]['source_name'] . ".php");
						}
						
						if ('' != $video_details['yt_thumb'])
						{
							$img = $download_thumb($video_details['yt_thumb'], _THUMBS_DIR_PATH, $uniq_id);
							generate_social_thumb($img);
						}
						else 
						{
							$img = true;
						}
						
						//if($img === false)
						//	$message .= "An error occurred while downloading the thumbnail!<br />";
					}
				}
				
				if ($img === false)
				{
					echo pm_alert_error('An error occurred while downloading the thumbnail. Check that GD Library is installed and enabled on your server.');
				}
				
				if ($message != '')
				{
					echo pm_alert_info($message);
					echo '<br /><input name="add_new" type="button" value="&larr; Return" onClick="location.href=\'addvideo.php?step=1\'" class="btn" />';
				}
				else	//	Everything is good. Now we can add the new video to the database
				{
					if ($_POST['featured'] == '1')
					{
						$video_details['featured'] = 1;
					}
					else
					{
						$video_details['featured'] = 0;
					}
					$modframework->trigger_hook('admin_addvideo_step3_pre_video');
					
					$new_video = insert_new_video($video_details, $new_video_id);
					if($new_video !== true)
					{
						$message = "<em>A problem occurred! Couldn't add the new video in your database;</em><br /><strong>MySQL Reports:</strong> ".$new_video[0]."<br /><strong>Error Number:</strong> ".$new_video[1]."<br />";		
					}
					else
					{
						$modframework->trigger_hook('admin_addvideo_step3_post_video');
						//	tags?
						if(trim($_POST['tags']) != '')
						{
							$tags = explode(",", $_POST['tags']);
							foreach($tags as $k => $tag)
							{
								$tags[$k] = stripslashes(trim($tag));
							}
							//	remove duplicates and 'empty' tags
							$temp = array();
							for($i = 0; $i < count($tags); $i++)
							{
								if($tags[$i] != '')
									if($i <= (count($tags)-1))
									{
										$found = 0;
										for($j = $i + 1; $j < count($tags); $j++)
										{
											if(strcmp($tags[$i], $tags[$j]) == 0)
												$found++;
										}
										if($found == 0)
											$temp[] = $tags[$i];
									}
							}
							$tags = $temp;
							//	insert tags
							if(count($tags) > 0)
								insert_tags($uniq_id, $tags);
						}
						$message = "The video has been successfully submitted.";
					}
					$modframework->trigger_hook('admin_addvideo_step3_final');
					echo pm_alert_success($message);
					echo '<br />';
					
					if((isset($_GET['filename']) && $_GET['filename'] != '') || $video_details['source_id'] == 1)
					{
					
						echo '<div class="btn-group"><a href="#addVideo" data-toggle="modal" class="btn btn-small">&larr;  Upload another video</a>';

					} 
					else
					{

						echo '<div class="btn-group"><a href="addvideo.php?step=1" name="add_new" class="btn btn-small" />&larr; Add a new video</a>';

					}

						echo '<a href="import.php" name="import_new" class="btn btn-small" />Import Videos</a></div>';

					}
			}
		}	//	end if post['submit'];
		else
		{
			if(headers_sent())
			{
				echo '<meta http-equiv="refresh" content="0;URL=addvideo.php?step=1" />';
			}
			else
			{
				header("Location: addvideo.php?step=1");
			}
			exit();
		}
	break;
}
?>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>