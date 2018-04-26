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

$showm = '6';
$load_scrolltofixed = 1;
$_page_title = 'Users';
include('header.php');

$action = (int) $_GET['a'];
$userid = (int) trim($_GET['uid']);

$page = (int) $_GET['page'];

if(empty($page))
	$page = 1;

// users per page
$limit = (isset($_COOKIE['aa_users_per_page'])) ? $_COOKIE['aa_users_per_page'] : 25;
$from = $page * $limit - ($limit);

$filter = $filter_value = $search_type = $search_query = '';
$filters = array('power', 'register', 'followers', 'following', 'lastlogin', 'register', 'id'); 

if(in_array(strtolower($_GET['filter']), $filters) !== false)
{
	$filter = strtolower($_GET['filter']);
	$filter_value = $_GET['fv'];
}

// Action buttons
if ($_POST['Submit'] != '' && ! csrfguard_check_referer('_admin_members'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
}
else if ($_POST['Submit'] == 'Activate account' && (is_admin() || is_moderator()))
{
	$user_ids = $_POST['user_ids'];
	if (count($user_ids) > 0)
	{
		$sql = "UPDATE pm_users 
				SET power = '". U_ACTIVE ."' 
				WHERE id IN (". implode(',', $user_ids) .") 
				  AND power = '". U_INACTIVE ."'";
		$result = @mysql_query($sql);
		
		if ( ! $result)
		{
			$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
		}
		else
		{
			$info_msg = pm_alert_success('The selected user accounts were updated.');
		}
	}
	else
	{
		$info_msg = pm_alert_warning('Please select something first.');
	}
}
else if ($_POST['Submit'] == 'Delete' && (is_admin() || is_moderator()))
{
	$user_ids = $_POST['user_ids'];
	$total = count($user_ids);
	
	// exclude self;
	if ($total > 0)
	{
		foreach ($user_ids as $k => $id)
		{
			if ($userdata['id'] == $id)
			{
				unset($user_ids[$k]);
				$total--;
				break;
			}
		}
	}
	
	if ($total > 0)
	{
		$sql_in_user_ids = implode(',', $user_ids);
		if (is_admin())
		{
			$sql = "DELETE FROM pm_users 
					WHERE id IN (". $sql_in_user_ids .") 
					  AND power != '". U_ADMIN ."'";
		}
		else // is moderator
		{
			$sql = "SELECT id, power FROM pm_users WHERE id IN (". $sql_in_user_ids .")";
			$result = mysql_query($sql);
			while ($row = mysql_fetch_assoc($result))
			{
				if ( ! in_array($row['power'], array(U_ACTIVE, U_INACTIVE)))
				{
					if(($key = array_search($row['id'], $user_ids)) !== false) 
					{
						unset($user_ids[$key]);
					}
				}
			}
			mysql_free_result($result);
			
			if(($key = array_search($userdata['id'], $user_ids)) !== false) 
			{
				unset($user_ids[$key]);
			}
			
			$sql_in_user_ids = implode(',', $user_ids);
			
			$sql = "DELETE FROM pm_users 
					WHERE id IN (". $sql_in_user_ids .")";
		}
		
		$user_ids_count = count($user_ids);
		
		// get accounts data @since v2.6
		if ($user_ids_count > 0)
		{
			$accounts_data = array();
			$sql_2 = "SELECT * FROM pm_users 
						WHERE id IN (". $sql_in_user_ids .")";
			if ($result_2 = mysql_query($sql_2))
			{
				while ($row = mysql_fetch_assoc($result_2))
				{
					$accounts_data[$row['id']] = $row;
				}
				
				mysql_free_result($result_2);
			}
		}
		
		$result = ($user_ids_count > 0) ? @mysql_query($sql) : true;

		if ( ! $result)
		{
			$info_msg = pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error());
		}
		else if ($user_ids_count > 0)
		{
			$affected_rows = mysql_affected_rows();

			@mysql_query('DELETE FROM pm_comments WHERE user_id IN ('. $sql_in_user_ids .')');
			@mysql_query('DELETE FROM pm_comments_reported WHERE user_id IN ('. $sql_in_user_ids .')');
			//@mysql_query('DELETE FROM pm_favorites WHERE user_id IN ('. $sql_in_user_ids .')'); // @deprecated since v2.2
			
			$sql = "SELECT list_id FROM pm_playlists WHERE user_id  IN (". $sql_in_user_ids .")";
			$result = @mysql_query($sql);

			$playlists = array();
			while ($row = @mysql_fetch_assoc($result))
			{
				$playlists[] = $row['list_id'];
			}
			@mysql_free_result($result);
			
			if (count($playlists) > 0)
			{
				@mysql_query("DELETE FROM pm_playlist_items WHERE list_id IN (". implode(',', $playlists) .")"); 
				@mysql_query("DELETE FROM pm_playlists WHERE list_id IN (". implode(',', $playlists) .")"); 
			}
			
			if (_MOD_SOCIAL && $affected_rows > 0)
			{
				foreach ($user_ids as $k => $id)
				{
					remove_all_related_activity($id, ACT_OBJ_USER);
				}
				
				// handle followers and following too
				follow_delete_user($user_ids);
				
				// handle notifications
				foreach ($user_ids as $k => $uid)
				{
					notifications_delete_user($uid);
				}
			}
			
			if (count($accounts_data) > 0)
			{
				foreach ($accounts_data as $id => $account)
				{
					if ($account['avatar'] != '' && $account['avatar'] != 'default.gif' && file_exists(_AVATARS_DIR_PATH . $account['avatar']))
					{
						@unlink(_AVATARS_DIR_PATH . $account['avatar']);
					}
					
					if ($account['channel_cover'] != '')
					{
						delete_channel_cover_files($account['channel_cover']);
					}
				}
			}
			
			if ($affected_rows == 0)
			{
				$info_msg = pm_alert_success('No accounts were removed.');
			}
			else if ($affected_rows > 1)
			{
				$info_msg = pm_alert_success($affected_rows .' user accounts deleted.');
			}
			else
			{
				$info_msg = pm_alert_success('1 user account deleted.');
			}
		}
		else
		{
			$info_msg = pm_alert_success('You can only delete accounts belonging to <em>Active</em> and <em>Inactive</em> groups.');
		}
	}
	else
	{	
		$info_msg = pm_alert_warning('Please select something first.');
	}
}
else if ($_POST['Submit'] == 'Delete' && !(is_admin() || is_moderator()))
{
	$info_msg = pm_alert_warning('Sorry, only Administrators are allowed to perform this action.');
}


// DELETE A USER
if ($action == 1 && ! csrfguard_check_referer('_admin_members'))
{
	$info_msg = pm_alert_error('Invalid token or session expired. Please refresh this page and try again.');
}
else if($action == 1) 
{ 
	$query = mysql_query("SELECT * FROM pm_users WHERE id = '".$userid."'");
	$account = mysql_fetch_assoc($query);
	
	if (is_moderator() && in_array($account['power'], array(U_ADMIN, U_MODERATOR, U_EDITOR)))
	{
		$info_msg = pm_alert_info('Sorry, you can\'t delete this account.');
	}
	else if ($account['power'] == U_ADMIN) 
	{
		$info_msg = pm_alert_info('Nice sense of humour, but the administrator\'s account cannot be removed.');
	} 
	else 
	{
		$result = @mysql_query("DELETE FROM pm_users WHERE id = '".$userid."'");
		if(!$result)
		{
			$info_msg = pm_alert_error('An MySQL error occurred: '. mysql_error());
		}
		else
		{
			@mysql_query("DELETE FROM pm_comments WHERE user_id = '". $userid ."'");
			@mysql_query("DELETE FROM pm_comments_reported WHERE user_id = '". $userid ."'");
			//@mysql_query("DELETE FROM pm_favorites WHERE user_id = '". $userid ."'"); // @deprecated since v2.2
			
			$sql = "SELECT list_id FROM pm_playlists WHERE user_id = $userid";
			$result = @mysql_query($sql);
			
			$playlists = array();
			while ($row = @mysql_fetch_assoc($result))
			{
				$playlists[] = $row['list_id'];
			}
			@mysql_free_result($result);
			
			if (count($playlists) > 0)
			{
				@mysql_query("DELETE FROM pm_playlist_items WHERE list_id IN (". implode(',', $playlists) .")"); 
				@mysql_query("DELETE FROM pm_playlists WHERE list_id IN (". implode(',', $playlists) .")");
			}
			
			if ($account['avatar'] != '' && $account['avatar'] != 'default.gif' && file_exists(_AVATARS_DIR_PATH . $account['avatar']))
			{
				@unlink(_AVATARS_DIR_PATH . $account['avatar']);
			}
			
			if ($account['channel_cover'] != '')
			{
				delete_channel_cover_files($account['channel_cover']);
			}
			
			if (_MOD_SOCIAL)
			{
				remove_all_related_activity($userid, ACT_OBJ_USER);
				
				// handle followers and following too
				follow_delete_user($userid);
				
				// handle notifications too
				notifications_delete_user($userid);
			}
			
			$info_msg = pm_alert_success('Account <strong>'. htmlentities($account['username']) .'</strong> was deleted from your database.');
		}
	}
}

$members_nonce = csrfguard_raw('_admin_members');

//	Search
if(!empty($_POST['submit']) || !empty($_GET['submit']) || !empty($_POST['_pmnonce']) || !empty($_POST['_pmnonce_t']))
{
	$search_query = ($_POST['keywords'] != '') ? trim($_POST['keywords']) : trim($_GET['keywords']);
	$search_type = ($_POST['search_type'] != '') ? $_POST['search_type'] : $_GET['search_type'];
	$members = a_list_users($search_query, $search_type,  $from, $limit, $page);
	$total_members = $members['total'];
} 
else 
{	
	if (in_array($filter, array('register', 'followers', 'following', 'lastlogin', 'register', 'id'))) // sorters
	{
		$total_members = count_entries('pm_users', '', '');
	}
	else
	{
		$total_members = count_entries('pm_users', $filter, $filter_value);
	}
	if($total_members - $from == 1)
		$page--;
		
	$members = a_list_users('', '', $from, $limit, $page, $filter, $filter_value);
	if($total_members - $from == 1)
		$page++;
}

// generate smart pagination
$filename = 'members.php';
$uri = $_SERVER['REQUEST_URI'];
$uri = explode('?', $uri);
$uri[1] = str_replace(array("<", ">", '"', "'", '/'), '', $uri[1]);
parse_str($uri[1], $temp);
unset($temp['_pmnonce'], $temp['_pmnonce_t'], $temp['a'], $temp['a'], $temp['uid']);
$uri[1] = http_build_query($temp);

$pagination = '';
$pagination = a_generate_smart_pagination($page, $total_members, $limit, 1, $filename, $uri[1]);


?>
<div id="adminPrimary">
    <div class="row-fluid" id="help-assist">
        <div class="span12">
        <div class="tabbable tabs-left">
          <ul class="nav nav-tabs">
            <li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
            <li><a href="#help-onthispage" data-toggle="tab">Export to CSV</a></li>
            <li><a href="#help-bulk" data-toggle="tab">Filtering</a></li>
          </ul>
          <div class="tab-content">
            <div class="tab-pane fade in active" id="help-overview">
            <p>This page provides a quick overview of your users. Listings below contain each user's details such name, registration date, last login date, IP address and user rank/group.</p>
            <p>If the site requires you to approve each registered user, you will have to do so from this page. You can also approve registrations in bulk. To approve a user click the &quot;check&quot; icon from the &quot;Actions&quot; column.</p>
            <p>Note: Banned users will have a strikeout username.</p>
            </div>
            <div class="tab-pane fade" id="help-onthispage">
            <p>A sub-menu of this area, &quot;Export to CSV&quot; generates a CSV file compatible with Microsoft Outlook, GMAIL Contacts, Facebook Friends Import and so on.<br />
You can then import this CSV to your favorite service and use the list to get in touch with your users.</p>
            </div>
            <div class="tab-pane fade" id="help-bulk">
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
                <div class="floatL"><strong class="blue"><?php echo pm_number_format($total_members); ?></strong><span>members</span></div>
                <div class="blueImg"><img src="img/ico-users-new.png" width="19" height="18" alt="" /></div>
            </li>
        </ul><!-- .pageControls -->
    </div>
	<h2>Users <a class="label opac5" href="add_user.php">+ add new</a></h2>

<?php echo $info_msg; ?>

<div class="row-fluid">
	<div class="span8">
	    <div class="pull-left">
		<?php if ( ! empty($_GET['keywords'])) : ?>
	    <h4>SEARCH RESULTS FOR "<em><?php echo $_GET['keywords']; ?></em>" <a href="#" onClick="parent.location='members.php'" class="opac5"><i class="icon-remove-sign"></i></a></h4>
		<?php endif; ?>
		<div class="clearfix"></div>
	    </div>
	</div><!-- .span8 -->
	<div class="span4">
	    <div class="pull-right">
	    <form name="users_per_page" action="members.php" method="get" class="form-inline pull-right">
	    <label><small>Users/page</small></label>
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
    <div class="span6">
        <div class="qsFilter pull-left">
            <form name="power_filter" action="members.php" method="get" class="form-inline">
            <input type="hidden" name="filter" value="power" />
            <div class="btn-group input-prepend">
            <div class="form-filter-inline">
            <?php if ( ! empty($filter)) : ?>
            <button type="button" class="btn btn-danger btn-strong" onClick="parent.location='members.php'">Remove filter</button>
            <?php else : ?>
            <button type="button" class="btn">Filter</button>
            <?php endif; ?>
            <select name="fv" class="inline last-filter" onchange="submit()">
            <option value="">by group ...</option>
            <option value="1" <?php if ($filter_value == '1') echo 'selected="selected"'; ?> >Admin</option>
            <option value="3" <?php if ($filter_value == '3') echo 'selected="selected"'; ?> >Moderators</option>
            <option value="4" <?php if ($filter_value == '4') echo 'selected="selected"'; ?> >Editors</option>
            <option value="0" <?php if ($filter_value == '0') echo 'selected="selected"'; ?> >Regular users</option>
            <option value="2" <?php if ($filter_value == '2') echo 'selected="selected"'; ?> >Inactive</option>
            </select>
            </div>
            </div><!-- .btn-group -->
            </form>    
        </div><!-- .qsFilter -->
    </div>
    <div class="span6">
    	<div class="pull-right">
        <form name="search" action="members.php" method="get" class="form-search-listing form-inline">
            <div class="input-append">
            <input type="text" name="keywords" value="<?php echo $search_query; ?>" size="30" class="search-query search-quez input-medium" placeholder="Enter keyword" id="form-search-input" />
            <select name="search_type" class="input-small">
             <option value="username" <?php echo ($search_type == "username") ? 'selected="selected"' : ''; ?> >Username</option>
             <option value="fullname" <?php echo ($search_type == "fullname") ? 'selected="selected"' : ''; ?> >Name</option>
             <option value="email" <?php echo ($search_type == "email") ? 'selected="selected"' : ''; ?> >Email</option>
             <option value="ip" <?php echo ($search_type == "ip") ? 'selected="selected"' : ''; ?> >IP Address</option>
            </select> 
            <button type="submit" name="submit" class="btn" value="Search" id="submitFind"><i class="icon-search findIcon"></i><span class="findLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span></button>
            </div>
        </form>
        </div>
    </div>
</div>
</div>
<div class="clearfix"></div>
<form name="users_checkboxes" id="users_checkboxes" action="members.php?page=<?php echo $page;?>&filter=<?php echo $filter;?>&fv=<?php echo $filter_value;?>" method="post">
<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped table-bordered pm-tables tablesorter">
 <thead>
  <tr>
   <th align="center" style="text-align:center" width="3%"><input type="checkbox" name="checkall" id="selectall" onclick="checkUncheckAll(this);"/></th>
   <th width="50"><a href="members.php?filter=id&fv=<?php echo ($filter_value == 'desc' && $filter == 'id') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'id') ? 'ascending' : 'descending';?>">ID</a></th>
   <th>Username</th>
   <th>Name</th>
   <th>Email</th>
   <th><a href="members.php?filter=register&fv=<?php echo ($filter_value == 'desc' && $filter == 'register') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'register') ? 'ascending' : 'descending';?>">Register date</a></th>
   <th><a href="members.php?filter=followers&fv=<?php echo ($filter_value == 'desc' && $filter == 'followers') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'followers') ? 'ascending' : 'descending';?>">Followers</a></th>
   <th><a href="members.php?filter=following&fv=<?php echo ($filter_value == 'desc' && $filter == 'following') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'following') ? 'ascending' : 'descending';?>">Following</a></th>
   <th><a href="members.php?filter=lastlogin&fv=<?php echo ($filter_value == 'desc' && $filter == 'lastlogin') ? 'asc' : 'desc';?>" rel="tooltip" title="Sort <?php echo ($filter_value == 'desc' && $filter == 'lastlogin') ? 'ascending' : 'descending';?>">Last seen</a></th>
   <th>Last logged IP</th>
   <th>Group</th>
   <th style="width: 90px;">Action</th>
  </tr>
 </thead>
 <tbody>

  <?php if ($pagination != '') : ?>
  <tr class="tablePagination">
	<td colspan="12" class="tableFooter">
		<div class="pagination pull-right"><?php echo $pagination; ?></div>
	</td>
  </tr>
  <?php endif; ?>
  
  <?php echo $members['users']; ?>
  
  <?php if ($pagination != '') : ?>
  <tr class="tablePagination">
	<td colspan="12" class="tableFooter">
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
    	<button type="submit" name="Submit" value="Activate account" class="btn btn-small btn-success btn-strong">Activate account(s)</button>
    </div>
    <div class="btn-group">
    	<button type="submit" name="Submit" value="Delete" class="btn btn-small btn-danger btn-strong" onClick="return confirm_delete_all();">Delete</button>
	</div>
</div>
</div><!-- #list-controls -->
<input type="hidden" name="_pmnonce" id="_pmnonce<?php echo $members_nonce['_pmnonce'];?>" value="<?php echo $members_nonce['_pmnonce'];?>" />
<input type="hidden" name="_pmnonce_t" id="_pmnonce_t<?php echo $members_nonce['_pmnonce'];?>" value="<?php echo $members_nonce['_pmnonce_t'];?>" />
<input type="hidden" name="filter" id="listing-filter" value="<?php echo $filter;?>" />
<input type="hidden" name="fv" id="listing-filter_value" value="<?php echo $filter_value;?>" />
<input type="hidden" name="search_type" id="listing-filter_search_type" value="<?php echo $search_type;?>" />
<input type="hidden" name="keywords" id="listing-filter_keywords" value="<?php echo $search_query;?>" />
</form>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>