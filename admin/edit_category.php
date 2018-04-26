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
$showm = '3';
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$load_tagsinput = 1;
$load_tinymce = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;
$_page_title = 'Edit category';
include('header.php');

$mode = ($_GET['mode'] != '') ? $_GET['mode'] : 'add';
$category_type = $_GET['type'];
$category_type = ($category_type == '') ? 'video' : $category_type;
$category_id = (int) $_GET['id'];


$form_data = $errors = array();
$success_add = $success_edit = $show_footer_early = false;

$categories_dropdown_options = array('first_option_text' => '- Root -', 
									 'attr_class' => 'category_dropdown span12',
									 'spacer' => '&mdash;',
									 'selected' => 0,
									 'db_table' => ($category_type == 'article') ? 'art_categories' : 'pm_categories'
									);

$all_categories = load_categories(array('db_table' => ($category_type == 'article') ? 'art_categories' : 'pm_categories', 'with_image' => true));
$category_data = $all_categories[$category_id];

if ($mode == 'edit' && empty($category_id))
{
	$errors[] = 'Invalid category ID.';
}
else if ($mode == 'edit')
{
	$form_data = $category_data;
	$categories_dropdown_options['selected'] = $category_data['parent_id'];
}

if ($_POST['save'] != '' && count($errors) == 0)
{
	foreach ($_POST as $k => $v)
	{
		$_POST[$k] = stripslashes( trim($v) );
	}

	switch ($mode)
	{
		case 'add':
			
			$_POST['name'] = str_replace('&amp;', '"', $_POST['name']);
			$_POST['tag'] = sanitize_title(trim($_POST['tag']));
			
			$result = insert_category($_POST, $category_type);
			
			if ($result['type'] == 'error')
			{
				$errors[] = $result['msg'];
			}
			else
			{
				$success_add = true;
				$show_footer_early = true;
			}

		break;
		
		case 'edit':
			
			$_POST['name'] = str_replace('&amp;', '"', $_POST['name']);
			$_POST['tag'] = sanitize_title(trim($_POST['tag']));
			$_POST['old_tag'] = $category_data['tag'];
 
			$result = update_category($category_id, $_POST, $category_type);
			
			if ($result['type'] == 'error')
			{
				$errors[] = $result['msg'];
			}
			else
			{
				$success_edit = true;
				$show_footer_early = false;
			}

		break;
	}
	
	$form_data = $_POST;
	$categories_dropdown_options['selected'] = $form_data['category'];
}

?>
<div id="adminPrimary"> 
	<div class="content">
	<?php if ($mode == 'add') : ?>
	<h2>Add New <?php echo ($category_type == 'article') ? 'Article' : 'Video'; ?> Category</h2> 
	<?php else : ?>
	<h2>Edit <?php echo ($category_type == 'article') ? 'Article' : 'Video'; ?> Category: <?php echo $form_data['name'];?></h2>
	<?php endif; ?>
	
	<?php 
	if (($errors_count = count($errors)) > 0)
	{
		echo ($errors_count > 1) ? pm_alert_error($errors) : pm_alert_error($errors[0]);
	} 
	?>
	
	<?php if ($success_add) : ?>
	<?php echo pm_alert_success('Category <strong>'. $name .'</strong> was added successfully.'); ?>
	<hr />
	<?php if ($category_type == 'video') : ?>
	<a href="categories.php" class="btn">&larr; Video Categories</a>
	<a href="edit_category.php?mode=add&type=video" class="btn btn-success">Add another video category &rarr;</a>
	<?php else : ?>
	<a href="categories.php?type=article" class="btn">&larr; Article Categories</a>
	<a href="edit_category.php?mode=add&type=article" class="btn btn-success">Add another article category &rarr;</a>
	<?php endif; ?>
	
	<?php if ($show_footer_early) : ?>
		</div><!-- .content -->
	</div><!-- .primary -->
	<?php
	include('footer.php');
	exit();
	endif; // show_footer_early
	?>
	<?php endif; //if ($success_add) : ?>
	
	<?php if ($success_edit) : ?>
	<?php echo pm_alert_success('Category <strong>'. $name .'</strong> was updated.'); ?>
	<hr />
	<?php endif; ?>
	<?php if ($show_footer_early) : ?>
		</div><!-- .content -->
	</div><!-- .primary -->
	<?php
	include('footer.php');
	exit();
	endif; // show_footer_early
	?>
	
	<form name="edit-category" method="POST" action="edit_category.php?mode=<?php echo $mode; ?>&type=<?php echo $category_type; echo ($mode == 'edit') ? '&id='. $category_id : '';?>" class="form-horizontal">



<div class="container row-fluid" id="post-page">
	<div class="span9">
	<div class="widget border-radius4 shadow-div">
	<h4>Category Name &amp; Description</h4>
	<div class="control-group">
	<input type="text" name="name" id="must" value="<?php echo str_replace('"', '&quot;', $form_data['name']); ?>" style="width: 99%;" />
	<div class="controls">
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
			<textarea name="description" cols="100" id="textarea-WYSIWYG" class="tinymce" style="width:100%"><?php echo $form_data['description']; ?></textarea>
			<span class="upload-file-dropzone"></span>
			<span class="autosave-message"></span>
		</div>
	</div>
	</div>
	
	</div><!-- .span8 -->

	<div class="span3">
	<?php if ($category_type == 'video'): ?>
	<div class="widget border-radius4 shadow-div upload-file-dropzone" id="category-image-dropzone">
	<div class="pull-right">
		<span class="btn fileinput-button">
			<span>Change</span>
			<input type="file" name="file" id="upload-category-image-btn" />
		</span>
	</div>
	<h4>Category Thumbnail</h4>
		<div class="control-group container-fluid">
			<div class="controls row-fluid">
			<div id="category-image-container">
				<?php if ($form_data['image'] == '') : ?>
				<div id="show-cat-cover"><img src="img/no-category-thumbnail.png"></div>
				<?php else : ?>
				<div id="show-cat-cover"><img src="<?php echo _THUMBS_DIR . $form_data['image']; ?>?cache_buster=<?php echo $time_now;?>" style="display:block;min-width:120px;width:100%;min-height:80px; no-repeat center center;" /></div>
				<button id="delete-category-image" class="btn btn-link" data-category-id="<?php echo $category_id; ?>"><i class="icon icon-trash icon-white"></i></button>
				<?php endif; ?>
				<input type="hidden" name="image" value="<?php echo $form_data['image']; ?>" />
			</div>
			</div><!-- .controls .row-fluid -->
		</div>
	</div><!-- .widget -->
	<?php endif; ?>
	
	<div class="widget border-radius4 shadow-div">
	<h4>Slug <i class="icon-info-sign" rel="tooltip" title="Define how the URL will look in your address bar. No need to include an extension (.html)."></i></h4>
		<div class="control-group">
		<div class="controls">
			<input name="tag" id="item-slug" type="text" class="default span12" value="<?php echo $form_data['tag']; ?>" size="50" style="width:95%" />
			<small>Updating this field will have an impact on SEO for pages already indexed</small>

			<div id="preview_url" class="small-ok">
			<?php 
				if(_SEOMOD == 1) 
				{
			?>
				 <small>Live preview: <?php echo _URL."/browse-"; ?><span id="preview_complete_url"><?php echo ($form_data['tag'] != '') ? $form_data['tag'] : '';?></span>-1-date.html</small>
			<?php
				} else {
			?>
				 <small>Live preview: <?php echo _URL."/category.php?cat="; ?><span id="preview_complete_url"></span></small>
			<?php			
				}
			?>
			</div>
		</div>
		</div>
	</div><!-- .widget -->
			
	<div class="widget border-radius4 shadow-div">
	<h4>Parent Category</h4>
		<div class="control-group">
		<div class="controls">
		<?php echo categories_dropdown($categories_dropdown_options);?>
		</div>
		</div>
	</div><!-- .widget -->

	<div class="widget border-radius4 shadow-div">
	<h4>HTML Meta</h4>
		<div class="control-group">
			<div class="controls">
				<label>Meta Title</label>
				<input type="text" name="meta_title" class="default span12" value="<?php echo str_replace('"', '&quot;', $form_data['meta_title']);?>" />
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label>Meta Description</label>
				<textarea name="meta_description" rows="1" style="width:95%" /><?php echo str_replace('"', '&quot;', $form_data['meta_description']);?></textarea>
			</div>
		</div>
		<div class="control-group">
			<div class="controls">
				<label>Meta Keywords</label>
				<div class="tagsinput" style="width: 100%;">
				<input type="text" name="meta_keywords" value="<?php echo str_replace('"', '&quot;', $form_data['meta_keywords']);?>" id="tags_addvideo_1" size="50" />
				</div>
			</div>
		</div>
	</div><!-- .widget -->	
	</div>
</div>
<div class="clearfix"></div>

	  
	<div id="stack-controls" class="list-controls">
	<div class="btn-toolbar">
		<div class="btn-group">
			<button type="submit" name="save" value="<?php echo ($mode == 'add') ? 'Submit' : 'Save';?>" class="btn btn-small btn-success btn-strong"><?php echo ($mode == 'add') ? 'Submit' : 'Save';?></button>
		</div>
	</div>
	</div><!-- #list-controls -->
	
	<input type="hidden" name="cat_id" value="<?php echo $category_id; ?>" />
	<input type="hidden" name="upload-type" value="category-image" />
	<input type="hidden" name="p" value="upload" /> 
	<input type="hidden" name="do" value="upload-image" />
	<!-- .permalink-input filed added to prevent errors in JS -->
	<input type="hidden" name="permalink-input-hack" class="permalink-input" value="" />
	</form>

	<?php echo csrfguard_form('_admin_catmanager'); ?>
	</div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>