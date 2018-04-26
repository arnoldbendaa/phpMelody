<?php
// +------------------------------------------------------------------------+
// | PHP Melody version 1.7 ( www.phpsugar.com )
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

$showm = '7';
$_page_title = 'Statistics';
include('header.php');

function count_avatars() {
	$q = mysql_query("SELECT avatar FROM pm_users WHERE avatar NOT LIKE '' AND avatar NOT LIKE 'default.gif'");
	$count = mysql_num_rows($q);
	return $count;
}

function is_today($timestamp){
	return date('Y-m-d',$timestamp) == date('Y-m-d');
}

function is_yesterday($timestamp){
	$yesterday  = mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
	return date('Y-m-d',$timestamp) == date('Y-m-d', $yesterday);
}

function is_thismonth($timestamp){
	$this_month  = mktime(0, 0, 0, date("m")  , date("d"), date("Y"));
	return date('Y-m',$timestamp) == date('Y-m', $this_month);
}

function top1_search() {
	$query = mysql_query("SELECT string, hits FROM pm_searches ORDER BY hits DESC LIMIT 1")or die(" Error: ".mysql_error());
	$r = mysql_fetch_array($query);
	if(!empty($r['string'])) {
		$keyword = $r['string'];
	return $keyword .' ('. pm_number_format($r['hits']).')';
	} else {
	return 'N/A yet.';
	}
}

function top1_rated() 
{
	$sql = "SELECT uniq_id, up_vote_count, down_vote_count, score  
			FROM pm_bin_rating_meta
			ORDER BY score DESC 
			LIMIT 1";
	$result = mysql_query($sql);
	$r = mysql_fetch_array($result);
	mysql_free_result($result);
	
	$r['total_votes'] = $r['up_vote_count'] + $r['down_vote_count'];
	
	if ($r['uniq_id'] && $r['total_votes'] > 0) 
	{
		$vote = ($r['total_votes'] > 1) ? 'votes' : 'vote';

		return '<a href="'. _URL .'/watch.php?vid='. $r['uniq_id'] .'">'. vnamefromvid($r['uniq_id']) .' ('. pm_number_format($r['total_votes']) ." $vote)</a>";
	} 
	else 
	{
		return 'N/A yet.';
	}
}

function top1_commented() 
{
	
	$sql = "SELECT uniq_id, COUNT(*) as total  
			FROM pm_comments 
			WHERE uniq_id NOT LIKE 'article-%' 
			GROUP BY uniq_id 
			ORDER BY total DESC 
			LIMIT 1";
	$result = mysql_query($sql);
	$r = mysql_fetch_array($result);
	mysql_free_result($result);
	
	if ($r['uniq_id']) 
	{
		return '<a href="comments.php?vid='. $r['uniq_id']. '">'. vnamefromvid($r['uniq_id']) .' ('. pm_number_format($r['total']) .')</a>';
	} 
	else 
	{
		return 'N/A yet.';
	}
}
function top1_commentor() 
{
	
	$sql = "SELECT pm_comments.user_id, pm_users.username, COUNT(*) as total 
			FROM pm_comments
			JOIN pm_users ON ( pm_comments.user_id = pm_users.id )
			WHERE pm_comments.user_id != '0' 
			GROUP BY pm_comments.user_id
			LIMIT 1 ";

	$result = mysql_query($sql);
	$r = mysql_fetch_array($result);
	mysql_free_result($result);
	
	if ($r['username']) 
	{
		return $r['username'] .' (<a href="comments.php?keywords='. $id .'&search_type=username&submit=Search">'. pm_number_format($r['total']) .'</a>)';
	} 
	else 
	{
		return 'N/A yet.';
	}
}
function member_searches() {
	$query = mysql_query("SELECT user FROM pm_searches WHERE user != 'guest'");
	$r = mysql_num_rows($query);
	return $r;
}


function recent_dates($date, $table, $orderby, $datefield) {

	$query = mysql_query("SELECT ".$datefield." FROM ".$table." ORDER BY ".$orderby." DESC")or die(" Error: ".mysql_error());
	$count=0;
	while ($row = mysql_fetch_array($query)) 
	{ 
			if($date($row[$datefield])) {
				$count++;
			} 
	}
	return $count;
}

?>
<div id="adminPrimary">
    <div class="content">
	<h2>Statistics</h2>
    
    <table width="100%" border="0" cellspacing="5" cellpadding="10">
      <tr>
        <td width="50%" valign="top">
			<div class="enclosed shadow-div">
				<h3>Videos</h3>
					<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped pm-tables tablesorter no-shadow">
					  <tr class="table_row1">
						<td width="30%"><strong>Total videos:</strong></td>
						<td width="70%"><?php echo pm_number_format($config['total_videos']); ?> <a href="videos.php"><img src="img/ico-go.gif" alt="Go" width="12" height="7" border="0" /></a></td>
					  </tr>
					  <tr class="table_row2">
						<td width="30%"><strong>Most comments:</strong></td>
						<td width="70%"><?php echo top1_commented(); ?></td>
					  </tr>
					  <tr class="table_row1">
						<td width="30%"><strong>Most rated:</strong></td>
						<td width="70%"><?php echo top1_rated(); ?></td>
					  </tr>
					  <tr class="table_row2">
						<td width="30%"><strong>Reported videos:</strong></td>
						<td width="70%"><?php echo pm_number_format(count_entries('pm_reports', 'r_type', '1')); ?> <a href="reports.php"><img src="img/ico-go.gif" alt="Go" width="12" height="7" border="0" /></a></td>
					  </tr>
					</table>
			</div>
        </td>
        <td valign="top">
			<div class="enclosed shadow-div">
				<h3>Video Comments</h3>
					<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped pm-tables tablesorter no-shadow">
					  <tr class="table_row1">
						<td width="30%"><strong>Total comments:</strong></td>
						<td width="70%"><?php echo pm_number_format(count_entries('pm_comments', '', '')); ?> <a href="comments.php?filter=videos"><img src="img/ico-go.gif" alt="Go" width="12" height="7" border="0" /></a></td>
					  </tr>
					  <tr class="table_row2">
						<td width="30%"><strong>Posted today:</strong></td>
						<td width="70%"><?php echo pm_number_format(recent_dates('is_today', 'pm_comments', 'id', 'added')); ?></td>
					  </tr>
					  <tr class="table_row1">
						<td width="30%"><strong>Posted yesterday:</strong></td>
						<td width="70%"><?php echo pm_number_format(recent_dates('is_yesterday', 'pm_comments', 'id', 'added')); ?></td>
					  </tr>
					  <tr class="table_row2">
						<td width="30%"><strong>#1 Comment Poster: </strong></td>
						<td width="70%"><?php echo top1_commentor(); ?></td>
					  </tr>
					</table>
			</div>
        </td>
      </tr>
      <tr>
        <td valign="top">
			<div class="enclosed shadow-div">
				<h3>Members</h3>
				<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped pm-tables tablesorter no-shadow">
				  <tr class="table_row1">
					<td width="30%"><strong>Total members:</strong></td>
					<td width="70%"><?php echo pm_number_format(count_entries('pm_users', '', '')); ?> <a href="members.php"><img src="img/ico-go.gif" alt="Go" width="12" height="7" border="0" /></a></td>
				  </tr>
				  <tr class="table_row2">
					<td width="30%"><strong>Joined today:</strong></td>
					<td width="70%"><?php echo pm_number_format(recent_dates('is_today', 'pm_users', 'id', 'reg_date')); ?></td>
				  </tr>
				  <tr class="table_row1">
					<td width="30%"><strong>Joined yesterday:</strong></td>
					<td width="70%"><?php echo pm_number_format(recent_dates('is_yesterday', 'pm_users', 'id', 'reg_date')); ?></td>
				  </tr>
				  <tr class="table_row2">
					<td width="30%"><strong>Joined this month: </strong></td>
					<td width="70%"><?php echo pm_number_format(recent_dates('is_thismonth', 'pm_users', 'id', 'reg_date')); ?></td>
				  </tr>
				  <tr class="table_row1">
					<td width="30%"><strong>Avatars uploaded:</strong></td>
					<td width="70%"><?php echo pm_number_format(count_avatars()); ?></td>
				  </tr>
				</table>
			</div>
        </td>
        <td valign="top">
			<div class="enclosed shadow-div">
				<h3>Articles</h3>
				<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped pm-tables tablesorter no-shadow">
				  <tr class="table_row1">
					<td width="30%"><strong>Total articles:</strong></td>
					<td width="70%"><?php echo $config['total_articles']; ?> <a href="articles.php"><img src="img/ico-go.gif" alt="Go" width="12" height="7" border="0" /></a></td>
				  </tr>
				  <tr class="table_row2">
					<td width="30%"><strong>Most commented:</strong></td>
					<td width="70%">
						<?php
						$sql = "SELECT uniq_id, COUNT(*) as total 
								 FROM pm_comments 
								 WHERE uniq_id LIKE 'article-%' 
								 GROUP BY uniq_id 
								 ORDER BY total DESC 
								 LIMIT 1";
						$result = mysql_query($sql);
						$row = mysql_fetch_assoc($result);
						mysql_free_result($result);
				
						if ($row['uniq_id'])
						{
							$row['article_id'] = str_replace('article-', '', $row['uniq_id']);
							$article = get_article($row['article_id']);
							
							echo '<a href="comments.php?vid=article-'. $article['id']. '">'. $article['title'] .' ('. $row['total'] .')</a>';
						}
						else
						{
							echo 'N/A yet.';
						}
						?>
					</td>
				  </tr>
				</table>
			</div>
			<br />
			<div class="enclosed shadow-div">
        		<h3>Searches</h3>
				<table cellpadding="0" cellspacing="0" width="100%" class="table table-striped pm-tables tablesorter no-shadow">
				  <tr class="table_row1">
					<td width="30%"><strong>Searches:</strong></td>
					<td width="70%"><?php echo pm_number_format(count_entries('pm_searches', '', '')); ?> <a href="show_searches.php"><img src="img/ico-go.gif" alt="Go" width="12" height="7" border="0" /></a></td>
				  </tr>
				  <tr class="table_row2">
					<td width="30%"><strong>Most popular query:</strong></td>
					<td width="70%"><?php echo top1_search(); ?></td>
				  </tr>
				</table>
			</div>
        </td>
      </tr>
      <tr>
        <td valign="top">
 
        </td>
        <td valign="top">
    
        </td>
      </tr>
    </table>

    </div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');
?>