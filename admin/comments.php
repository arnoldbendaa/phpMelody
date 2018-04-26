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

$filter = '';
$filters = array('articles', 'videos', 'flagged', 'pending');

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
}

$showm = '5';
$load_scrolltofixed = 1;
$_page_title = 'Comments';
include('header.php');

$vid 		= trim($_GET['vid']);
$action 	= $_GET['a'];
$comment_id = (int) trim($_GET['cid']);
$page 		= $_GET['page'];

$filter = '';
$filters = array('articles', 'videos', 'flagged', 'pending');

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
}

if($page == 0)
	$page = 1;

//	comments per page
$limit = (isset($_COOKIE['aa_comments_per_page'])) ? $_COOKIE['aa_comments_per_page'] : 25;
$from 		= $page * $limit - ($limit);


//	Batch Delete/Approve Comments/Remove flag
if (($_POST['Submit'] == 'Delete' || $_POST['Submit'] == 'Approve' || $_POST['Submit'] == 'Remove flag') &&  ! csrfguard_check_referer('_admin_comments'))
{	
	$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
}
else if($_POST['Submit'] == 'Delete' || $_POST['Submit'] == 'Approve' || $_POST['Submit'] == 'Remove flag')
{
	$video_ids = array();
	$video_ids = $_POST['video_ids'];
	
	$total_ids = count($video_ids);
	if($total_ids > 0)
	{
		$in_arr = '';
		for($i = 0; $i < $total_ids; $i++)
		{
			$in_arr .= $video_ids[ $i ] . ", ";
		}
		$in_arr = substr($in_arr, 0, -2);
		if(strlen($in_arr) > 0)
		{
			if($_POST['Submit'] == 'Approve')
			{
				$sql = "UPDATE pm_comments SET approved = '1' WHERE id IN (" . $in_arr . ")";
				$result = @mysql_query($sql);
	
				if(!$result)
					$info_msg = pm_alert_error('An error occured while updating your database.<br />MySQL returned: '. mysql_error());
				else
					$info_msg = pm_alert_success('The selected comments have been approved.');
				
				if (_MOD_SOCIAL)
				{
					$sql = "SELECT id, uniq_id, user_id 
							FROM pm_comments WHERE id IN (" . $in_arr . ")";
					$result = mysql_query($sql);
					while ($row = mysql_fetch_assoc($result))
					{
						if (strpos($row['uniq_id'], 'article-') !== false)
						{
							$tmp_parts = explode('-', $row['uniq_id']);
							$id = array_pop($tmp_parts);
							$article = get_article($id);
							log_activity(array(
									'user_id' => $row['user_id'],
									'activity_type' => ACT_TYPE_COMMENT,
									'object_id' => $row['id'],
									'object_type' => ACT_OBJ_COMMENT,
									'object_data' => array(),
									'target_id' => $id,
									'target_type' => ACT_OBJ_ARTICLE,
									'target_data' => $article
									)
								);
						}
						else
						{
							$video = request_video($row['uniq_id']);
							log_activity(array(
									'user_id' => $row['user_id'],
									'activity_type' => ACT_TYPE_COMMENT,
									'object_id' => $row['id'],
									'object_type' => ACT_OBJ_COMMENT,
									'object_data' => array(),
									'target_id' => $video['id'],
									'target_type' => ACT_OBJ_VIDEO,
									'target_data' => $video
									)
								);
						}
					}
				}
			}
			else if ($_POST['Submit'] == 'Remove flag')
			{
				$sql = "UPDATE pm_comments SET report_count = '0' WHERE id IN (" . $in_arr . ")";
				$result = @mysql_query($sql);
				
				if ( ! $result)
				{
					$info_msg = pm_alert_error('An error occured while updating your database.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					@mysql_query("DELETE FROM pm_comments_reported WHERE comment_id IN (" . $in_arr . ")");
					$info_msg = pm_alert_success('The selected flags have been removed.');
				}
			}
			else
			{
				if (_MOD_SOCIAL)
				{
					$sql = "SELECT id, uniq_id, user_id 
							FROM pm_comments WHERE id IN (" . $in_arr . ")";
					if ($result = mysql_query($sql))
					{
						while (	$row = mysql_fetch_assoc($result))
						{
							$sql = "DELETE FROM pm_activity 
									WHERE user_id = '". $row['user_id'] ."' 
									  AND activity_type = '". ACT_TYPE_COMMENT ."'
									  AND object_id = '". $row['id'] ."' 
									  AND object_type = '". ACT_OBJ_COMMENT ."'";
							@mysql_query($sql);
						}
						mysql_free_result($result);
					}
				}
				
				$sql = "DELETE FROM pm_comments WHERE id IN (" . $in_arr . ")";
				$result = @mysql_query($sql);
				
				if(!$result)
				{
					$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
				}
				else
				{
					// remove reports
					$sql = "DELETE FROM pm_comments_reported WHERE comment_id IN (" . $in_arr . ")";
					$result = @mysql_query($sql);
					
					$in_arr = '';
					for($i = 0; $i < $total_ids; $i++)
					{
						if ($video_ids[ $i ] > 0)
						{
							$in_arr .= "'com-". $video_ids[ $i ] . "', ";
						}
					}
					$in_arr = substr($in_arr, 0, -2);
					
					// remove likes/dislikes
					$sql = "DELETE FROM pm_bin_rating_votes WHERE uniq_id IN (". $in_arr .")";
					$result = @mysql_query($sql);
					
					$info_msg = pm_alert_success('The selected comments have been deleted.');
				}
			}
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}

switch($action)
{
	case 1:
		if (csrfguard_check_referer('_admin_comments'))
		{
			if (_MOD_SOCIAL)
			{
				$sql = "SELECT id, uniq_id, user_id 
						FROM pm_comments WHERE id = '" . $comment_id . "'";
				if ($result = mysql_query($sql))
				{
					$row = mysql_fetch_assoc($result);
					$sql = "DELETE FROM pm_activity 
							WHERE user_id = '". $row['user_id'] ."' 
							  AND activity_type = '". ACT_TYPE_COMMENT ."'
							  AND object_id = '". $row['id'] ."' 
							  AND object_type = '". ACT_OBJ_COMMENT ."'";
					@mysql_query($sql);
					mysql_free_result($result);
				}
			}
			@mysql_query("DELETE FROM pm_comments WHERE id = '".$comment_id."'");
			@mysql_query("DELETE FROM pm_comments_reported WHERE comment_id = '".$comment_id."'");
			@mysql_query("DELETE FROM pm_bin_rating_votes WHERE uniq_id = 'com-".$comment_id."'");
			$info_msg = pm_alert_success('Comment(s) deleted.');
		}
		else
		{
			$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
		}
	break;
	case 2:
		if (csrfguard_check_referer('_admin_comments'))
		{
			@mysql_query("UPDATE pm_comments SET approved='1' WHERE id = '".$comment_id."'");
			
			if (_MOD_SOCIAL)
			{
				$sql = "SELECT id, uniq_id, user_id 
						FROM pm_comments WHERE id = '" . $comment_id . "'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				if (strpos($row['uniq_id'], 'article-') !== false)
				{
					$tmp_parts = explode('-', $row['uniq_id']);
					$id = array_pop($tmp_parts);
					$article = get_article($id);
					log_activity(array(
							'user_id' => $row['user_id'],
							'activity_type' => ACT_TYPE_COMMENT,
							'object_id' => $row['id'],
							'object_type' => ACT_OBJ_COMMENT,
							'object_data' => array(),
							'target_id' => $id,
							'target_type' => ACT_OBJ_ARTICLE,
							'target_data' => $article
							)
						);
				}
				else
				{
					$video = request_video($row['uniq_id']);
					log_activity(array(
							'user_id' => $row['user_id'],
							'activity_type' => ACT_TYPE_COMMENT,
							'object_id' => $row['id'],
							'object_type' => ACT_OBJ_COMMENT,
							'object_data' => array(),
							'target_id' => $video['id'],
							'target_type' => ACT_OBJ_VIDEO,
							'target_data' => $video
							)
						);
				}
			}
			$info_msg = pm_alert_success('Comment(s) approved.');
		}
		else
		{
			$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
		}
	break;
}

$comments_nonce = csrfguard_raw('_admin_comments');

//	Search
if(!empty($_GET['submit']) || !empty($vid))
{
	if(!empty($vid))
	{
		$comments = a_list_comments($vid, 'uniq_id', $from, $limit, $page);
	}
	else
	{
		$search_query = ($_POST['keywords'] != '') ? trim($_POST['keywords']) : trim($_GET['keywords']);
		$search_type = ($_POST['search_type'] != '') ? $_POST['search_type'] : $_GET['search_type'];
		$search_query = urldecode($search_query);
		$comments = a_list_comments($search_query, $search_type, $from, $limit, $page);
	}
	$total_comments = $comments['total'];
}
else 
{
	$total_comments = count_entries('pm_comments', '', '');
	
	if($total_comments - $from == 1)
		$page--;
		
	$comments = a_list_comments('', '', $from, $limit, $page, $filter);

	if($total_comments - $from == 1)
		$page++;
	
	$total_comments = $comments['total'];
}

// generate smart pagination
$filename = 'comments.php';
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('?', $uri);
$uri[1] = str_replace(array("<", ">", '"', "'", '/'), '', $uri[1]);

$pagination = '';
$pagination = a_generate_smart_pagination($page, $total_comments, $limit, 1, $filename, $uri[1]);


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
            <p>Comments posted on your site, are organized into &quot;video comments&quot; and &quot;article comments&quot;. An icon will represent the comment type. Selecting the &quot;COMMENTS&quot; item from the menu will list all existing comments with the latest ones showing first.</p>
			<p>If the site has comment moderation enabled, pending comments will also appear in the list. To approve a comment click the &quot;check&quot; icon from the &quot;Actions&quot; column.</p>
			<p>Hovering any existing message, both published and pending approval allows for easy in-line editing. This is helpful when it comes to removing unsolicited advertising, sensitive data and so on.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
            <p>Listing pages such as this one contain a filtering area which comes in handy when dealing with a large number of entries. The filtering options is always represented by a gear icon positioned on the top right area of the listings table. Clicking this icon usually reveals a  search form and one or more drop-down filters.</p>
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
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_comments); ?></strong><span>comment(s)</span></div>
                <div class="blueImg"><img src="img/ico-comments-new.png" width="19" height="13" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2><?php if($filter == 'articles') { ?>Article <?php } elseif($filter == 'videos') { ?>Video <?php } else {} ?> Comments</h2>
	<?php echo $info_msg; ?>
    

	<div class="row-fluid">
		<div class="span8">
		    <div class="pull-left">
			<?php if (!empty($_GET['keywords'])) : ?>
			<div class="pull-left">
			<h4>SEARCH RESULTS FOR "<em><?php echo $_GET['keywords']; ?></em>" <a href="#" onClick="parent.location='comments.php'" class="opac5"><i class="icon-remove-sign"></i></a></h4>
			</div>
			<div class="clearfix"></div>
			<?php endif; ?>
    		</div>
		</div><!-- .span8 -->
		<div class="span4">
		    <div class="pull-right">
		    <form name="comments_per_page" action="comments.php" method="get" class="form-inline pull-right">
		    <label><small>Comments/page</small></label>
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
                  <?php
                  if(!empty($_GET['filter'])) {
                  ?>
                  <button type="button" id="appendedInputButtons" class="btn btn-danger btn-strong" onClick="parent.location='comments.php'">Remove filter</button>
                  <?php } else { ?>
                  <button type="button" id="appendedInputButtons" class="btn">Filter</button>
                  <?php } ?>
                    <form name="other_filter" action="comments.php" class="form-inline">
                      <select name="URL" onChange="window.parent.location=this.form.URL.options[this.form.URL.selectedIndex].value" class="inline last-filter">
                        <option value="comments.php">choose ...</option>
                        <option value="comments.php?filter=flagged&page=1" <?php if ($filter == 'flagged') echo 'selected="selected"'; ?>>Flagged</option>
                        <option value="comments.php?filter=pending&page=1" <?php if ($filter == 'pending') echo 'selected="selected"'; ?>>Pending</option>
                      </select>
                    </form>
                  </div><!-- .form-filter-inline -->
                </div><!-- .btn-group -->
                </div><!-- .qsFilter -->
            </div>
            <div class="span4">
            	<div class="pull-right">
                    <form name="search" action="comments.php" method="get" class="form-search-listing form-inline">
                    <div class="input-append">
                    <input type="text" name="keywords" value="<?php echo $_GET['keywords']; ?>" size="30" class="search-query search-quez input-medium" placeholder="Enter keyword" id="form-search-input" />
                    <select name="search_type" class="input-small">
                     <option value="comment" <?php echo ($_GET['search_type'] == "comment") ? 'selected="selected"' : ''; ?> >Comment</option>
                     <option value="uniq_id" <?php echo ($_GET['search_type'] == "uniq_id") ? 'selected="selected"' : ''; ?> >Video Unique ID</option>
                     <option value="username" <?php echo ($_GET['search_type'] == "username") ? 'selected="selected"' : ''; ?> >Username</option>
                     <option value="ip" <?php echo ($_GET['search_type'] == "ip") ? 'selected="selected"' : ''; ?> >IP Address</option>
                    </select> 
                    <button type="submit" name="submit" class="btn" value="Search" id="submitFind"><i class="icon-search findIcon"></i><span class="findLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span></button>
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="clearfix"></div>
    <?php 
	/*
	 * */
	$form_action = 'comments.php?page='. $page;
	
	$form_action .= ($filter != '') ? '&filter='. $filter : '';
	$form_action .= ($_GET['vid'] != '') ? '&vid='. $_GET['vid'] : '';
	$form_action .= ($_GET['keywords'] != '') ? '&keywords='. $_GET['keywords'] .'&search_type='. $_GET['search_type'] .'&submit=Search' : '';
	?>
    <form name="comments_checkboxes" action="<?php echo $form_action;?>" method="post">
    <table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
     <thead>
      <tr>
        <th align="center" width="20"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
        <th align="center" style="text-align:center" width="20"> </th>
        <th width="35%">Comment for</th>
        <th width="100">Added</th>
        <th>Comment</th>
        <th width="120">Posted by</th>
        <th width="100">IP</th>
        <th width="" style="width: 120px">Action</th>
      </tr>
     </thead>
     <tbody>
		<?php if ($pagination != '') : ?>
		<tr class="tablePagination">
			<td colspan="8" class="tableFooter">
				<div class="pagination pull-right"><?php echo $pagination; ?></div>
			</td>
		</tr>
		<?php endif; ?>
		
        <?php echo $comments['comments']; ?>
        
        <?php if ($pagination != '') : ?>
        <tr class="tablePagination">
			<td colspan="8" class="tableFooter">
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
		<button type="submit" name="Submit" value="Remove flag" class="btn btn-small btn-strong">Remove flag</button>
        </div>
        <div class="btn-group">
		<button type="submit" name="Submit" value="Approve" class="btn btn-small btn-success btn-strong">Approve</button>
        </div>
        <div class="btn-group">
		<button type="submit" name="Submit" value="Delete" class="btn btn-small btn-danger btn-strong">Delete</button>
        </div>
    </div>
    </div><!-- #list-controls -->
   
	<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $comments_nonce['_pmnonce'];?>" value="<?php echo $comments_nonce['_pmnonce'];?>" />
	<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $comments_nonce['_pmnonce'];?>" value="<?php echo $comments_nonce['_pmnonce_t'];?>" />
    
    </form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>