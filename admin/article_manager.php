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

$showm = 'mod_article';
/*
$load_uniform = 0;
$load_ibutton = 0;
$load_tinymce = 0;
$load_swfupload = 0;
$load_colorpicker = 0;
$load_prettypop = 0;
*/
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$_page_title = 'Add new article';

$action = $_GET['do'];
if ( ! in_array($action, array('edit', 'new', 'delete')) )
{
	$action = 'new';	//	default action
}

if ($action == 'edit')
{
	$_page_title = 'Edit article';
}
include('header.php');

?>
<script type="text/javascript">
$(document).ready(function(){
	$("img[name='article_thumbnail']").click(function() {
		var img = $(this);
		var ul = img.parents('.thumbs_ul');
		var li = img.parent();
		var input = $("input[name='post_thumb_show']");
		
		if ( ! li.hasClass('art-thumb-selected'))
		{
			ul.children().removeClass('art-thumb-selected').addClass('art-thumb-default');
			li.addClass('art-thumb-selected');
			input.val(img.attr('src'));
		}
	});
});
</script>

<div id="adminPrimary">
    <div class="content">
		<?php if ($action == 'edit') : ?>
		<h2>Edit Article</h2>
		<?php else : ?>
		<h2>Post New Article</h2>
		<?php endif; ?>
		<div id="display_result" style="display:none;"></div>

<?php
if ( ! $config['mod_article'])
{
	echo pm_alert_info('The Article Module is currently disabled. Please enable it from \'<a href="settings.php?view=t6">Settings / Available Modules</a>\'.');
  ?>
  </div>
  <?php
  include('footer.php');
  exit();
}

$inputs = array();

if ('' != $_POST['submit'])
{
	$_POST['title'] = after_post_filter($_POST['title']);
	$_POST['tags'] = after_post_filter($_POST['tags']);
	
	if ($action == 'new')
	{
        $modframework->trigger_hook('admin_article_insert_before');
		$result = insert_new_article($_POST);
        $modframework->trigger_hook('admin_article_insert_after');
	}
	else if ($action == 'edit')
	{
        $modframework->trigger_hook('admin_article_update_before');
		$result = update_article($_POST['id'], $_POST);
        $modframework->trigger_hook('admin_article_update_after');
	}
	
	if ($result['type'] == 'error')
	{
		echo pm_alert_error($result['msg']);
	}
	else
	{
		if ($action == 'new')
		{
			echo pm_alert_success('<strong>'. $result['msg'] .'.</strong> <a href="'. _URL .'/article_read.php?a='.$result['article_id'].'&mode=preview" target="_blank">See how it looks</a>.');

			echo '<input name="continue" type="button" value="&larr; Manage articles" onClick="location.href=\'articles.php\'" class="btn" /> ';
			echo ' <input name="add_new" type="button" value="Post a new article &rarr;" onClick="location.href=\'article_manager.php?do=new\'" class="btn btn-success" />';
			echo '</div></div>';
			
			include('footer.php');
			exit();
		}
		else
		{
			echo pm_alert_success('<strong>'. $result['msg'] .'</strong> <a href="'. _URL .'/article_read.php?a='. $_POST['id'] .'&mode=preview" target="_blank">See how it looks</a>.');
		}
	}	
}

if ($action == 'edit')
{
	$id = (int) $_GET['id'];
	if ($id == 0)
	{
		$action = 'new';
		$inputs = array();
		$inputs['allow_comments'] = 1;
		$inputs['status'] = 1;
		$inputs['author'] = $userdata['id'];
		$inputs['category_as_arr'] = array();
	}
	else
	{
		$inputs = get_article($id);
	}
	$meta_data = get_all_meta_data($inputs['id'], IS_ARTICLE);
	
	if ($inputs['article_slug'] == '')
	{
		$inputs['article_slug'] = 'read-'. sanitize_title($inputs['title']);
		$inputs['article_slug'] = preg_replace('/-video$/', '_video', $inputs['article_slug']);
	}
	
}
else if ($action == 'new')
{
	if ('' != $_POST['submit'])
	{
		$inputs = $_POST;
	}
	else
	{
		$inputs['allow_comments'] = 1;
		$inputs['status'] = 1;
		$inputs['author'] = $userdata['id'];
	}
	if ( ! is_array($inputs['category_as_arr']))
	{
		$inputs['category_as_arr'] = array();
	}
}

$categories = art_get_categories();

//	Filter some fields before output
$inputs['title'] = pre_post_filter($inputs['title']);
$inputs['tags'] = pre_post_filter($inputs['tags']);


if($inputs['date'] > time()) 
{
	$message = 'This article is <strong>scheduled</strong> to appear on your site ';
	$days_until_release = count_days($inputs['date'], time());
	if ($days_until_release == 0)
	{
		$days_until_release = 'today at '. date('g:i A', $inputs['date']);
	}
	else
	{
		$message .= 'in';
		$days_until_release = ($days_until_release == 1) ? $days_until_release .' day' : $days_until_release .' days';
	}
	$message .= ' <strong>'. $days_until_release .'</strong>.<br> <small>Change the "Publish date" below to update its schedule date ('. date("M d, Y g:i A", $inputs['date']) .').</small>';
	
	echo pm_alert_warning($message);
}

?>


<form name="write_article" method="post" action="article_manager.php?do=<?php echo $action; ?>&id=<?php echo $_GET['id'];?>" onsubmit="return validateFormOnSubmit(this, 'Please fill in the required fields (highlighted).')">

<div class="container row-fluid" id="post-page">

    <div class="span9">
    <div class="widget border-radius4 shadow-div">
    <h4>Title &amp; Description</h4>
    <div class="control-group">
	<input name="title" type="text" id="must" value="<?php echo $inputs['title']; ?>" style="width: 99%;" />

    <div class="permalink-field">

	<?php if (_SEOMOD) : ?>
		<strong>Permalink:</strong> <?php echo _URL .'/articles/';?><input class="permalink-input" type="text" name="article_slug" value="<?php echo urldecode($inputs['article_slug']);?>" /><?php echo  '_'. (($inputs['id'] == '') ? 'ID' : $inputs['id']) .'.html';?>
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
	<div class="clear"></div>
    <div class="controls" id="textarea-dropzone">
    <textarea name="content" cols="100" id="textarea-WYSIWYG" class="tinymce" style="height: 350px;width:100%"><?php echo $inputs['content']; ?></textarea>
	<span class="upload-file-dropzone"></span>
	<span class="autosave-message"></span>
    </div>
    </div>
    </div>
	
	<div class="widget border-radius4 shadow-div" id="custom-fields">
	<h4>Custom Fields <a href="http://help.phpmelody.com/how-to-use-the-custom-fields/" target="_blank"><i class="icon-question-sign"></i></a></h4>
    	<div class="control-group">
		<div class="row-fluid">
			<div class="span3"><strong>Name</strong></div>
			<div class="span9"><strong>Value</strong></div>
		</div>		
		<?php 
		if ($action == 'new') :
			if (count($_POST['meta']) > 0) :
				foreach ($_POST['meta'] as $meta_id => $meta) : 
					$meta['meta_key'] = $meta['key'];
					$meta['meta_value'] = $meta['value'];
					
					echo admin_custom_fields_row($meta_id, $meta);
				endforeach;
			endif;
			
			echo admin_custom_fields_add_form(0, IS_ARTICLE);
		else :
			if (count($meta_data) > 0) :
			 	foreach ($meta_data as $meta_id => $meta) : 
					echo admin_custom_fields_row($meta_id, $meta);
				endforeach;
			endif;
			
			echo admin_custom_fields_add_form($inputs['id'], IS_ARTICLE);
		endif; ?>
		
		</div>
	</div>
    
    </div><!-- .span8 -->

    <div class="span3">


		<div class="widget border-radius4 shadow-div">
		<h4>Category</h4>
            <div class="control-group">
            <div class="controls">
            <input type="hidden" name="categories_old" value="<?php echo $inputs['category'];?>"  />
            <?php 
            //$checklist_options = array('selected' => explode(',', $inputs['category']), 'ul_wrapper' => false);
            //echo art_cats_checklist($categories, $checklist_options);

            $categories_dropdown_options = array(
                        'db_table' => 'art_categories',
                        'attr_name' => 'categories[]',
                        'attr_id' => 'main_select_category',
						'attr_class' => 'category_dropdown span12',
                        'select_all_option' => false,
                        'spacer' => '&mdash;',
                        'selected' => explode(',', $inputs['category']), 
                        'other_attr' => 'multiple="multiple" size="3"',
                        'option_attr_id' => 'check_ignore'
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
											'db_table' => 'art_categories',
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
            <?php
        $modframework->trigger_hook('admin_article_publishfields');
        ?>
		</div><!-- .widget -->
        
        <div class="widget border-radius4 shadow-div">
        <h4>Article Details</h4>
            <div class="control-group">
            <label>Comments: <span id="value-comments"><strong><?php if ($inputs['allow_comments'] == 1) { echo 'allowed'; } else { echo 'closed'; } ?></strong></span> <a href="#" id="show-comments">Edit</a></label>
            <div class="controls" id="show-opt-comments">
                <label><input name="allow_comments" id="allow_comments" type="checkbox" value="1" <?php if ($inputs['allow_comments'] == 1) echo 'checked="checked"';?> /> Allow comments on this article</label>
                <?php if ($config['comment_system'] == 'off') : ?>
                <div class="alert">
                Commenting is currently disabled site-wide.
                <br />
                To enable comments site-wide, go to <a href="settings.php?view=comment" title="Settings page" target="_blank">Settings > Comment Settings</a>.
                </div>
                <?php endif;?>
            </div>
            </div>
            
            <div class="control-group">
            <label class="control-label" for="">Visibility: <span id="value-visibility"><strong><?php if ($inputs['status'] == 0) { echo 'draft'; } else { echo 'public'; } ?></strong></span> <a href="#" id="show-visibility">Edit</a></label>
            <div class="controls" id="show-opt-visibility">
                <label class="checkbox inline"><input type="radio" name="status" id="visibility" value="0" <?php if ($inputs['status'] == 0) echo 'checked="checked"'; ?> /> Draft</label> 
                <label class="checkbox inline"><input type="radio" name="status" id="visibility" value="1" <?php if ($inputs['status'] == 1) echo 'checked="checked"'; ?> /> Public</label>
            </div>
            </div>

            <div class="control-group">
            <label>Sticky: <span id="value-featured"><strong><?php if($inputs['featured'] == 1) { echo 'yes'; } else { echo 'no'; } ?></strong></span> <a href="#" id="show-featured">Edit</a></label>
            <div class="controls" id="show-opt-featured">
                <label><input type="checkbox" name="featured" id="featured" value="1" <?php if($inputs['featured'] == 1) echo 'checked="checked"';?> /> Yes, stick to front page</label>
            </div>
            </div>

            <div class="control-group">
            <label class="control-label" for="">Requires registration: <span id="value-register"><strong><?php if($inputs['restricted'] == 1) { echo 'yes'; } else { echo 'no'; } ?></strong></span> <a href="#" id="show-restriction">Edit</a></label>
            <div class="controls" id="show-opt-restriction">
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="0" <?php if ($inputs['restricted'] == 0) echo 'checked="checked"'; ?> /> No</label> 
                <label class="checkbox inline"><input type="radio" name="restricted" id="restricted" value="1" <?php if ($inputs['restricted'] == 1) echo 'checked="checked"'; ?> /> Yes</label>
            </div>
            </div>
                        
            <div class="control-group">
            <label class="control-label" for="">Publish date: <span id="value-publish"><strong><?php if(empty($inputs['date'])) { echo 'immediately'; } else { echo date('M d, Y @ G:i',$inputs['date']); }?> </strong></span> <a href="#" id="show-publish">Edit</a></label>
            <div class="controls" id="show-opt-publish">
            <?php echo show_form_item_date($inputs['date']); ?>
            </div>
            </div>
        </div><!-- .widget -->

		<div class="widget border-radius4 shadow-div">
		<h4>Tags</h4>
            <div class="control-group">
            <div class="controls">
                <div class="tagsinput" style="width: 100%;">
                <input name="tags" type="text" value="<?php echo $inputs['tags']; ?>"  id="tags_addvideo_1" size="50" />
                </div>
            </div>
            </div>
        </div><!-- .widget -->


		<div class="widget border-radius4 shadow-div">
		<h4>Post thumbnail</h4>
            <div class="control-group">
            <div class="controls">
                <?php
            
                    $all_meta = $inputs['meta']['*'];
                    $total_thumbs = count($all_meta['_post_thumb']);
                          
                    if ($total_thumbs > 0)
                    { 
                        echo '<ul class="thumbs_ul">';
                        
                        // display current selected thumbnail
                        if ($inputs['meta']['_post_thumb_show'] != '')
                        {
                            echo '<li class="art-thumb-selected"><img src="img/bg-selected.gif" alt="" border="0" style="display:none" /><img src="'. _ARTICLE_ATTACH_DIR . $inputs['meta']['_post_thumb_show'] .'" width="'. THUMB_W_ARTICLE .'" height="'. THUMB_H_ARTICLE .'" alt="Thumb 1" name="article_thumbnail" /></li>';	
                        }
                        
                        // display next thumbnails available for this post.
                        $limit = 10;
                        for ($i = 0; $i < $limit; $i++)
                        {
                            if (strlen($all_meta['_post_thumb'][$i]) > 0)
                            {
                                if ($all_meta['_post_thumb'][$i] != $inputs['meta']['_post_thumb_show'])
                                {
                                    echo '<li class="art-thumb-default"><img src="img/bg-selected.gif" alt="" border="0" style="display:none" /><img src="'. _ARTICLE_ATTACH_DIR . $all_meta['_post_thumb'][$i] .'" width="'. THUMB_W_ARTICLE .'" height="'. THUMB_H_ARTICLE .'"  alt="Thumb '. ($i + 2) .'" name="article_thumbnail" /></li>';
                                }
                                else
                                {
                                    $limit++;
                                }
                                
                                if ($limit > 99)
                                {
                                    break;
                                }
                            }
                        }
                        echo '</ul>';
                    } 
                    else
                    {
                        echo '<em>There are no thumbnails associated with this article. To create a thumbnail for this article first upload images within this post, then "Save" it.</em>';
                    }
                ?>
                <div class="clearfix"></div>
                    <input type="hidden" name="post_thumb_show" value="<?php if ($inputs['meta']['_post_thumb_show'] != '') echo $inputs['meta']['_post_thumb_show'];?>" />
            </div>
            </div>
        </div><!-- .widget -->
        <?php
        $modframework->trigger_hook('admin_article_fields');
        ?>
    </div>
    
</div>
<div class="clearfix"></div>


<input type="hidden" name="author" value="<?php  echo $inputs['author'];?>" />
<input type="hidden" name="id" value="<?php echo $inputs['id'];?>" />
<input type="hidden" name="date_old" value="<?php echo $inputs['date'];?>" />
<input type="hidden" name="p" value="upload" /> 
<input type="hidden" name="do" value="upload-image" />

<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
    	<button name="cancel" type="button" value="Cancel" onClick="location.href='articles.php'" class="btn btn-small btn-normal btn-strong">Cancel</button>
    </div>
    <div class="btn-group">
    	<button name="submit" type="submit" <?php echo ($action == 'edit') ? 'value="Save"' : 'value="Publish"';?> class="btn btn-small btn-success btn-strong"><?php echo ($action == 'edit') ? 'Save' : 'Publish';?></button>
	</div>
</div>
</div><!-- #list-controls -->
    
</form>


    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>