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
$category_type = (strtolower($_GET['type']) == 'article') ? 'article' : 'video';
$_page_title = ($category_type == 'article') ? 'Article categories' : 'Video categories';

include('header.php');

$sql_table = ($category_type == 'article') ? 'art_categories' : 'pm_categories';

$categories_dropdown_options = array('db_table' => $sql_table,
									 'first_option_text' => '- Root -', 
									 'selected' => $_POST['parent_id'], 
									 'attr_name' => 'parent_id',
									 'attr_class' => 'inline',
									 'spacer' => '&mdash;',
									 );

$action = $_GET['do'];
if ( ! in_array($action, array('update', 'new', 'delete', 'move', 'organize')) )
{
	$action = 'new';	//	default action
}

if ('' != $_POST['update'] || '' != $_POST['submit']) 
{
	if ($action == 'new')
	{
		$result = insert_category($_POST, $category_type);
	}
	else if ($action == 'update')
	{
		$result = update_category((int) $_POST['id'], $_POST, $category_type);
	}
	
	if ($result['type'] == 'error')
	{
		$info_msg = pm_alert_error($result['msg']);
	}
	else
	{
		$_POST = array();
		$info_msg = pm_alert_success($result['msg']);
	}
}

if ($_GET['move'] != '' && $_GET['id'] != '')
{
	$id = (int) $_GET['id'];
	
	if ($id > 0)
	{
		$categories = load_categories(array('db_table' => $sql_table));
		
		$limit = 0;
		$is_parent = false;
		$is_child = false;

		if ($categories[$id]['parent_id'] == 0)
		{
			foreach ($categories as $c_id => $c_arr)
			{
				if ($c_arr['parent_id'] == 0)
				{
					$is_parent = true;
					$limit++;
				}
			}
		}
		else
		{
			foreach ($categories as $c_id => $c_arr)
			{
				if ($c_arr['parent_id'] == $categories[$id]['parent_id'])
				{
					$is_child = true;
					$limit++;
				}
			}
		}
		
		$current_position = $categories[$id]['position'];
		$prev_cat_id = $next_cat_id = 0;
		
		// find neighbours 
		foreach ($categories as $c_id => $c_arr)
		{
			if ($c_arr['position'] == ($current_position - 1) && $c_arr['parent_id'] == $categories[$id]['parent_id'])
			{
				$prev_cat_id = $c_id;
			}
			
			if ($c_arr['position'] == ($current_position + 1) && $c_arr['parent_id'] == $categories[$id]['parent_id'])
			{
				$next_cat_id = $c_id;
			}
		}
		
		switch ($_GET['move'])
		{
			case 'up':
				
				if ($current_position > 1 && $current_position <= $limit && $prev_cat_id)
				{
					$sql_1 = "UPDATE $sql_table
							   SET position = '". ($categories[$prev_cat_id]['position'] + 1) ."' 
							 WHERE id = '". $prev_cat_id ."'";
					$sql_2 = "UPDATE $sql_table
							   SET position = '". ($categories[$id]['position'] - 1) ."' 
							 WHERE id = '". $id ."'";
					
					$categories[$prev_cat_id]['position']++;
					$categories[$id]['position']--;
				}
				
			break;
	
			case 'down':
				
				if ($current_position >= 1 && $current_position < $limit && $next_cat_id)
				{
					$sql_1 = "UPDATE $sql_table
							   SET position = '". ($categories[$id]['position'] + 1) ."' 
							 WHERE id = '". $id ."'";
					
					$sql_2 = "UPDATE $sql_table
							   SET position = '". ($categories[$next_cat_id]['position'] - 1) ."' 
							 WHERE id = '". $next_cat_id ."'";
					
					$categories[$id]['position']++;
					$categories[$next_cat_id]['position']--;
				}
				
			break;
		}

		if ($sql_1 != '' && $sql_2 != '')
		{
			if ( ! ($result = mysql_query($sql_1)))
			{
				$info_msg = pm_alert_error('A problem was encountered while updating your database!<br />MySQL returned: '. mysql_error());
			}
			else
			{
				if ( ! ($result = mysql_query($sql_2)))
				{
					$info_msg = pm_alert_error('A problem was encountered while updating your database!<br />MySQL returned: '. mysql_error());
				}
			}
			
			load_categories(array('db_table' => $sql_table, 'reload' => true));
		}
	}
	
	if ($info_msg == '')
	{
		echo '<meta http-equiv="refresh" content="0;URL=categories.php?type='. $category_type .'&id='. $id .'&moved='. $_GET['move'] .'" />';
		exit();
	}
	
}

$categories = load_categories(array('db_table' => $sql_table));
$total_categories = count($categories);

if ($category_type == 'video')
{
	$featured_categories = ($config['homepage_featured_categories'] != '') ? unserialize($config['homepage_featured_categories']) : array();
} 
?>
<div id="adminPrimary">
	<div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>Categories are separated into Video and Article categories. The Article Categories appear only if the Article Module is enabled.</p>
			<p>At the top of the page there's a form to quickly add new categories as needed. Below you'll find a list of your current category tree. Categories can be moved up or down depending on the desired hierarchy.<br />Editing existing categories can be made without leaving the page. Simply hover the category to edit.</p>
			<p>Adding a new category requires a &quot;slug&quot;, which is the URL-friendly version of the category name. Categories can be placed in the &quot;root&quot; or in an existing category making it a subcategory.</p>
			<hr />
			<p>Clicking on the <i class="icon icon-home"></i> icon next to each category will create a list of recent videos from that category on your homepage.</p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
    <a href="#" id="show-help-assist">Help</a>
    <div class="entry-count">
        <ul class="pageControls">
            <li>
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_categories); ?></strong><span>categories</span></div>
                <div class="blueImg"><img src="img/ico-cats-new.png" width="18" height="17" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2><?php echo ($category_type == 'article') ? 'Article Categories' : 'Video Categories'; ?> <a class="label opac5" onClick="parent.location='edit_category.php?mode=add&type=<?php echo $category_type; ?>'">+ add new</a></h2>

	<?php  if ( ! $config['mod_article'] && $category_type == 'article') : ?>
	<div class="alert alert-warning">
		The Article Module is currently disabled. Please enable it from the <a href="settings.php?view=t6" title="Settings Page">Settings</a> page.
	</div>
	
	<?php endif; ?>
	
	<div id="display_result" style="display:none;"></div>
	
	<?php 
	if ($_GET['moved'] != '')
	{
		echo pm_alert_success('Category <strong>'. $_video_categories[$_GET['id']]['name'] .'</strong> moved '. $_GET['moved'] .' a level.', false, true);
	}
	
	if ($_GET['organized'] != '')
	{
		echo pm_alert_success('The categories order was saved.', false, true);
	}
	?>

	<?php echo $info_msg; ?>
		
	<?php if ($action == 'organize') :
	$load_jquery_ui = true;
	$load_sortable = true;
	echo pm_alert_info('Simply drag and drop the categories to achieve the desired structure and click <strong>Save</strong> once you\'re done.');

	if ($total_categories > 0)
	{
		echo a_category_sortable_list($categories);
	}
	else
	{
		echo pm_alert_error('Please <a href="edit_category.php?do=add&type='. $category_type .'">create a category</a> first.');
	}
	?>
    <div class="clearfix"></div>
    <div id="stack-controls" class="list-controls">
    <div class="btn-toolbar pull-left"><div class="btn-group"><a href="categories.php?type=<?php echo $category_type;?>" class="btn btn-small btn-strong" title="Go back">&larr; <?php echo ($category_type == 'article') ? 'Article' : 'Video'; ?> categories</a></div></div>
	<div class="btn-toolbar">
        <div class="btn-group"><a href="categories.php?type=<?php echo $category_type;?>" class="btn btn-small btn-strong" title="Go back">Cancel</a></div>
		<div class="btn-group"><a href="#" class="btn btn-small btn-strong btn-success" id="organize-category-save-btn">Save</a></div>
	</div>
    </div><!-- #list-controls -->

	<?php else : ?>
	
	<div class="tablename">
	<div class="qsFilter">
	<?php if ($total_categories > 1) : ?>
	<div class="row-fluid">
	<div class="span4">
	<a href="categories.php?type=<?php echo $category_type; ?>&do=organize" class="btn btn-strong"><i class="icon-list opac7"></i> Rearrange</a>
	</div>
	<div class="span8">
	<a href="#modal_add_category" class="btn btn-success btn-strong pull-right" data-toggle="modal">Add new category</a>
	</div>
	</div>
	<?php endif; ?>
	</div>
	</div>
	<br />
	<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
	 <thead>
	  <tr>
		<?php if ($category_type == 'video') : ?>
		<th width="10"></th>
		<?php endif;?>
		<th width="3%">ID</th>
		<th width="25%">Category Name</th>
		<th width="35%">Slug</th>
		<th width="15%">Parent Category</th>
		<th width="5%"><?php echo ($category_type == 'article') ? 'Articles' : 'Videos'; ?></th>
		<th width="5%">Position</th>
		<th width="10%" align="center" style="width: 90px;">Action</th>
	  </tr>
	 </thead>
	 <tbody>
		<?php 
		$args = array('page' => 'categories.php?type='. $category_type,
					  'form_action' => 'categories.php?type='. $category_type .'&do=update',
					  'type' => $category_type
					);		
		echo a_category_table_body($categories, $args);	
		?>
	 </tbody>
	</table>
	<?php echo csrfguard_form('_admin_catmanager'); ?>
	<?php endif; ?>
    </div><!-- .content -->
</div><!-- .primary -->

<div id="stack-controls" class="hide"></div>
<!-- quick category add modal -->
<div class="modal hide" id="modal_add_category" tabindex="-1" role="dialog" aria-labelledby="modal_add_category" aria-hidden="true" class="form-horizontal">

	<div class="modal-header">
		<a class="close" data-dismiss="modal">&times;</a>
        <h3>Add New Cateory</h3>
    </div>
	
	<form name="new-category" method="post" action="">
        <div class="modal-body">
			<div class="modal-response-placeholder hide"></div>
			<div style="padding: 10px; margin: 10px;">
					<label>Category name</label>
					<input name="name" type="text" value="<?php if($_POST['name'] != '') { echo $_POST['name']; } ?>" placeholder="Category name" size="22" />
					<label>Slug <a href="#" rel="tooltip" title="Slugs are used in the URL and can only contain numbers, letters, dashes and underscores."><i class="icon-info-sign" rel="tooltip" title="Slugs are used in the URL and can only contain numbers, letters, dashes and underscores."></i></a></label>
					<input name="tag" type="text" value="<?php if($_POST['tag'] != '') { echo $_POST['tag']; } ?>" placeholder="Slug" size="10" class="span2" /> 
			
					<label>Create in</label>
					<?php echo categories_dropdown($categories_dropdown_options); ?>
			</div>
		</div>
        <div class="modal-footer">
	        <a class="btn btn-link btn-strong" data-dismiss="modal" aria-hidden="true">Cancel</a>
			<a class="btn btn-default btn-strong" href="edit_category.php?mode=add&type=<?php echo $category_type;?>">Use the advanced form</a>
	        <button name="submit" type="submit" value="Add category" class="btn btn-success btn-strong">Add Category</button>
	    </div>
    </form>

</div>
<?php
include('footer.php');