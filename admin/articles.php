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
$load_scrolltofixed = 1;
$load_chzn_drop = 1;
$_page_title = 'Manage articles';
include('header.php');
?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">Filtering</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>This module can be enabled or disabled depending on your needs (see settings page to disable it) . You can use the articles module as a blog or an article database depending on your needs. Using the built-in user ranks, you can assign Editors which can administer your articles/blog.</p>
			<p>Note: Posting unique and relevant content regularly will help your SEO efforts.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
            <p>Listing pages such as this one contain a filtering area which comes in handy when dealing with a large number of entries. The filtering options is always represented by a gear icon positioned on the top right area of the listings table. Clicking this icon usually reveals a search form and one or more drop-down filters.</p>
            </div>
          </div>
        </div> <!-- /tabbable -->
        </div><!-- .span12 -->
    </div><!-- /help-assist -->
    <div class="content">
	<a href="#" id="show-help-assist">Help</a>
    <h2>Articles <a class="label opac5" onClick="parent.location='article_manager.php?do=new'">+ add new</a></h2>

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
?>

<?php

$action	= (int) $_GET['action'];
$page	= (int) $_GET['page'];

if($page == 0)
	$page = 1;


$total_articles = 0;
//	articles per page
$limit = (isset($_COOKIE['aa_articles_per_page'])) ? $_COOKIE['aa_articles_per_page'] : 25;
$from = $page * $limit - ($limit);

$filter = '';
$filters = array('public', 'private', 'mostviewed', 'category', 'sticky', 'restricted');
$filter_value = '';

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
	
	if ($filter == 'category' && $filter_value == '')
	{
		$filter = '';
	}
}


//	Batch delete
if ($_POST['submit'] == "Delete" && ! csrfguard_check_referer('_admin_articles'))
{
	echo pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
}
else if ('' != $_POST['submit'] && $_POST['submit'] == "Delete")
{
	$total_checkboxes = count($_POST['checkboxes']);
	if($total_checkboxes > 0 && (is_admin() || (is_moderator() && mod_can('manage_articles'))))
	{
		$article_ids = array();
		foreach ($_POST['checkboxes'] as $k => $id)
		{
			$article_ids[] = (int) $id;
		}
		
		$result = mass_delete_articles($article_ids);
		
		if ($result['type'] == 'error')
		{
			echo pm_alert_error($result['msg']);
		}
		else
		{
			echo pm_alert_success($result['msg']);
		}
	}
	else if ($total_checkboxes > 0 && (is_editor() || (is_moderator() && mod_cannot('manage_articles'))))
	{
		echo pm_alert_error('You are not allowed delete articles.');
	}
	else
	{
		echo pm_alert_warning('Please select something first.');
	}
}

if ('' != $_POST['submit'] && $_POST['submit'] == 'Search')
{
	$articles = list_articles($_POST['keywords'], $_POST['search_type'], $from , $limit); 
	$total_articles = count($articles);
}
else
{
	switch ($filter)
	{
		default:
		case 'mostviewed':
		
			$total_articles = $config['total_articles'];
		
		break;
		
		case 'private':
		
			$total_articles = count_entries('art_articles', 'status', '0');
		
		break;

		case 'public':
		
			$total_articles = count_entries('art_articles', 'status', '1');
		
		break;

		case 'sticky':
		
			$total_articles = count_entries('art_articles', 'featured', '1');
		
		break;

		case 'restricted':
		
			$total_articles = count_entries('art_articles', 'restricted', '1');
		
		break;
		
		case 'category':
		
			$filter_value = (int) $filter_value;
			if ($filter_value > 0)
			{
				$sql = "SELECT COUNT(*) as total_found 
						FROM art_articles  
						WHERE category LIKE '". $filter_value ."' 
						   OR category LIKE '". $filter_value .",%' 
						   OR category LIKE '%,". $filter_value ."' 
						   OR category LIKE '%,". $filter_value .",%'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);
				
				$total_articles = $row['total_found'];
				unset($row, $result, $sql);
			}
			else if ($_GET['fv'] == '0')
			{
				$total_articles = count_entries('art_articles', 'category', '0');
			}
			else
			{
				$total_articles = 0;
			}
		
		break;
	}
	$articles = list_articles('', '', $from , $limit, $filter, $filter_value); 
}

// generate smart pagination
$filename = 'articles.php';
$pagination = '';

if(!isset($_POST['submit'])) 
	$pagination = a_generate_smart_pagination($page, $total_articles, $limit, 5, $filename, '&filter='. $filter .'&fv='. $filter_value);


if ($_GET['action'] == "deleted") 
{
	echo pm_alert_success('Comments were deleted.');
}

if ($_GET['action'] == "badtoken") 
{
	echo pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
}

?>
<div class="clearfix"></div>
<div class="entry-count">
    <ul class="pageControls">
        <li>
            <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_articles); ?></strong><span>article(s)</span></div>
            <div class="blueImg"><img src="img/ico-articles-new.png" width="18" height="18" alt="" /></div>
        </li>
    </ul><!-- .pageControls -->
</div>
<div id="display_result" style="display:none;"></div>
<div class="row-fluid">
	<div class="span8">
	    <div class="pull-left">
		<?php if ( ! empty($_POST['keywords'])) : ?>
		<div class="pull-left">
			<h4>SEARCH RESULTS FOR "<em><?php echo $_POST['keywords']; ?></em>" <a href="#" onClick="parent.location='articles.php'" class="opac5"><i class="icon-remove-sign"></i></a></h4>
		</div>
		<div class="clearfix"></div>
		<?php endif; ?>
	    </div>
	</div><!-- .span8 -->
	<div class="span4">
	    <div class="pull-right">
	    <form name="articles_per_page" action="articles.php" method="get" class="form-inline pull-right">
	    <label><small>Articles/page</small></label>
	    <select name="results" class="smaller-select" onChange="this.form.submit()" >
	    <option value="25" <?php if($limit == 25) echo 'selected="selected"'; ?>>25</option>
	    <option value="50" <?php if($limit == 50) echo 'selected="selected"'; ?>>50</option>
	    <option value="75" <?php if($limit == 75) echo 'selected="selected"'; ?>>75</option>
	    <option value="100" <?php if($limit == 100) echo 'selected="selected"'; ?>>100</option>
	    <option value="125" <?php if($limit == 125) echo 'selected="selected"'; ?>>125</option>
	    </select>
	    <?php
	    // filter persistency
	    if (strlen($_SERVER['QUERY_STRING']) > 0)
	    {
	        $pieces = explode('&', $_SERVER['QUERY_STRING']);
	        foreach ($pieces as $k => $val)
	        {
	            $p = explode('=', $val);
	            if ($p[0] != 'page' && $p[0] != 'results') :	
	            ?>
	            <input type="hidden" name="<?php echo $p[0];?>" value="<?php echo $p[1];?>" />
	            <?php 
	            endif;
	        }
	    }
	    ?>
	    </form>    
	    </div>
	</div>
</div><!-- .row-fluid-->
<div class="tablename">
<div class="row-fluid">
    <div class="span8">
        <div class="qsFilter pull-left">
            <div class="btn-group input-prepend">
                <div class="form-filter-inline">
                <form name="category_filter" action="articles.php" method="get" class="form-inline">
                <?php if ( ! empty($_GET['filter'])) : ?>
                <button type="button" id="appendedInputButtons" class="btn btn-danger btn-strong" onClick="parent.location='articles.php'">Remove filter</button>
                <?php else : ?>
                <button type="button" id="appendedInputButtons" class="btn">Filter</button>
                <?php endif; ?>
                
                <select name="fv" onchange=submit()>
                <option value="articles.php">by category...</option>
                <?php
                $categories = art_get_categories();
                
                foreach ($categories as $id => $cat)
                {
                    $option = '<option value="'. $id .'" ';
                    if ($filter_value == $id && $filter == 'category')
                    {
                        $option .= ' selected="selected" ';
                    }
                    $option .= '>'. $cat['name'] .'</option>';
                    echo $option;
                }
                ?>
                <option value="0" <?php echo ($_GET['fv'] == '0') ? 'selected="selected"' : '';?>>Uncategorized</option>
                </select>
                <select name="URL" class="inline last-filter" onChange="window.parent.location=this.form.URL.options[this.form.URL.selectedIndex].value">
                <option value="articles.php">by status ...</option>
                <option value="articles.php?page=1&filter=mostviewed" <?php if ($_GET['filter'] == 'mostviewed') echo 'selected="selected"'; ?>>Most viewed</option>
                <option value="articles.php?page=1&filter=public" <?php if ($_GET['filter'] == 'public') echo 'selected="selected"'; ?>>Published</option>
                <option value="articles.php?page=1&filter=private" <?php if ($_GET['filter'] == 'private') echo 'selected="selected"'; ?>>Drafts</option>
                <option value="articles.php?page=1&filter=sticky" <?php if ($_GET['filter'] == 'sticky') echo 'selected="selected"'; ?>>Sticky</option>
                <option value="articles.php?page=1&filter=restricted" <?php if ($_GET['filter'] == 'restricted') echo 'selected="selected"'; ?>>Restricted</option>
                </select>
                <input type="hidden" name="filter" value="category" />
                </form>
                </div><!-- .form-filter-inline -->
            </div><!-- .btn-group -->
        </div><!-- .qsFilter -->
    </div>
    <div class="span4">
        <div class="pull-right">
            <form name="search" action="articles.php" method="post" class="form-search-listing form-inline">
            <div class="input-append">
            <input name="keywords" type="text" value="<?php echo $_POST['keywords']; ?>" size="30" class="search-query search-quez input-medium" placeholder="Enter keyword" id="form-search-input" />
            <select name="search_type" tabindex="1" class="input-small">
            <option value="title" <?php echo ($_POST['search_type'] == 'title') ? 'selected="selected"' : '';?>>Title</option>
            <option value="content" <?php echo ($_POST['search_type'] == 'content') ? 'selected="selected"' : '';?>>Description</option>
            </select>
            <button type="submit" name="submit" class="btn" value="Search" id="submitFind"><i class="icon-search findIcon"></i><span class="findLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span></button>
            </div>
            </form>
        </div>
    </div>
</div>
</div>
<div class="clearfix"></div>
<form name="articles_checkboxes" id="articles_checkboxes" action="articles.php?page=<?php echo $page;?>" method="post">
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead>
   <tr>
	<th align="center" style="text-align:center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
	<th width="40%">Title</th>
	<th width="5%">Views</th>
    <th width="16%">Categories</th>
	<th width="10%">Author</th>	
    <th width="90">Added</th>
	<th width="150">Comments</th>
    <th width="" style="width:90px;">Action</th>
   </tr>
  </thead>
  <tbody>
  <?php if ($pagination != '') : ?>
  <tr class="tablePagination">
	<td colspan="9" class="tableFooter">
		<div class="pagination pull-right"><?php echo $pagination; ?></div>
	</td>
  </tr>
  <?php endif; ?>
  
  	<?php 
	
	/*
	 *  List articles
	 */ 
	if ( ! array_key_exists('type', $articles) && $total_articles > 0)
	{
		$alt = 1;
		
		foreach ($articles as $k => $article)
		{
			$col = ($alt % 2) ? 'table_row1' : 'table_row2';
			$alt++;
			
			$total_comments = count_entries('pm_comments', 'uniq_id', 'article-'.$article['id']);

			//	Table row
			$table_row = '';
			if ($article['status'] == 0)
			{
				$table_row .= '<tr class="scheduled '. $col .'" id="article-'. $article['id'] .'">';
			} elseif ($article['restricted'] == '1') {
				$table_row .= '<tr class="private '. $col .'" id="article-'. $article['id'] .'">';
			} else {
				$table_row .= '<tr class="'. $col .'" id="article-'. $article['id'] .'">';
			}
			echo $table_row;
			?>
			
			 <td align="center" style="text-align:center" width="3%">
			 	<input name="checkboxes[]" type="checkbox" value="<?php echo $article['id']; ?>" />
			 </td>
			 <td>
			 	<?php if ($article['featured'] == '1') : ?>
			 		<span class="label label-featured"><a href="articles.php?filter=sticky" rel="tooltip" title="Click to list only sticky articles" />STICKY</a></span> 
                <?php endif; ?>
				<?php if ($article['status'] == 0) : ?>
					<span class="label label-scheduled"><a href="articles.php?filter=private" rel="tooltip" title="Click to list only drafts. This is a private article (Draft). Only Administrators and Editors can see this article">DRAFT</a></span>
				<?php endif; ?>
			    <?php if ($article['restricted'] == '1') : ?>
					<span class="label label-private"><a href="articles.php?filter=restricted" rel="tooltip" title="Click to list only private articles. Only registered users can read this article.">PRIVATE</a></span>
				<?php endif; ?>
				<a href="<?php echo _URL.'/article_read.php?a='. $article['id']; if ($article['status'] == 0 || $article['date'] > time()) echo '&mode=preview'; ?>" target="_blank"><?php echo htmlspecialchars($article['title']); ?></a>
				<?php if ($article['date'] > time()): ?>
					&mdash; <small>Not published yet</small>
				<?php endif;?>
			 </td>
			 <td align="center" style="text-align:center"><?php echo pm_number_format($article['views']); ?></td>
			 <td>
			  <?php 
			 	$str = '';
				foreach ($article['category_as_arr'] as $id => $name)
				{
					if ($id != '' && $name != '')
					{
						$str .= '<a href="articles.php?filter=category&fv='. $id .'" title="List articles from '. $name .' only">'. $name .'</a>, ';
					}
					
					if ($id == 0)
					{
						$name = 'Uncategorized';
						$str .= '<a href="articles.php?filter=category&fv='. $id .'" title="List articles from '. $name .' only">'. $name .'</a>, ';
					}
				}
			 	echo substr($str, 0, -2);
			  ?>
			 </td>
			 <td align="center" style="text-align:center">
			  <?php 
			  	$author = fetch_user_advanced($article['author']);
				
				echo '<a href="edit_user_profile.php?uid='. $author['id'] .'" title="Edit">'. $author['username'] .'</a>';
			  ?>
			 </td>
			 <td align="center" style="text-align:center">
			 <span rel="tooltip" title="<?php echo date('l, F j, Y g:i A', $article['date']); ?>">
			 <?php echo date('M d, Y', $article['date']); ?>
			 </span>
			 </td>
			 <td align="center" style="text-align:center"> 
             		 <a href="comments.php?vid=<?php echo 'article-'.$article['id'];?>" title="View comments" class="b_view">View</a> 
			  <?php 
			  if (is_admin() || (is_moderator() && mod_can('manage_comments')))
			  {
			  	?>
			  	| <a href="#" title="Delete all comments" onClick='del_video_comments("article-<?php echo $article['id'];?>", "<?php echo $page;?>")'>Delete (<?php echo $total_comments; ?>)</a>
				<?php
			  }
			  ?>
			 </td>
			 <td align="center" class="table-col-action" style="text-align:center; width: 90px;">
			  <a href="article_manager.php?do=edit&id=<?php echo $article['id'];?>" class="btn btn-mini btn-link" rel="tooltip" title="Edit"><i class="icon-pencil"></i></a>
			  <a href="#" onclick="onpage_delete_article('<?php echo $article['id']; ?>', '#display_result', '#article-<?php echo $article['id'];?>')" class="btn btn-mini btn-link" rel="tooltip" title="Delete"><i class="icon-remove"></i></a>
			  </td>
		    </tr>
			
			<?php
		}
	}
	else	//	Error?
	{
		if (strlen($articles['msg']) > 0)
		{
			echo pm_alert_error($articles['msg']);
		}
		
		if ($total_articles == 0)
		{
			?>
			<tr>
			 <td colspan="9" align="center" style="text-align:center">
			 No articles found.
			 </td>
			</tr>
			<?php
		}
	}
	?>
	
	<?php if ($pagination != '') : ?>
  	<tr class="tablePagination">
		<td colspan="9" class="tableFooter">
			<div class="pagination pull-right"><?php echo $pagination; ?></div>
		</td>
	</tr>
  	<?php endif; ?>
  </tbody>
 </table>

<div class="clearfix"></div>
<div id="stack-controls" class="list-controls">
<div class="btn-toolbar">
    <div class="btn-group">
	<button type="submit" name="submit" value="Delete" class="btn btn-small btn-danger btn-strong" onClick="return confirm_delete_all();">Delete</button>
    </div>
</div>
</div><!-- #list-controls -->
<?php
echo csrfguard_form('_admin_articles');
?>
</form>
    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>