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
// | Copyright: (c) 2004-2014 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '1';
//$_page_title = '';

$load_scrollpane = 1;
$load_dotdotdot = 1;
include('header.php');
include_once('syndicate_news.php');

if ($_GET['close-welcome'] != '' || (($time_now - (86400 * 14) > (int) $config['firstinstall']) && $config['admin_welcome'] == 1))
{
	update_config('admin_welcome', 0, true);
}

$widget_items_limit = 10;

?>
<div id="adminPrimary">
   <div class="row-fluid" id="help-assist">
		<div class="span12">
		<div class="tabbable tabs-left">
		  <ul class="nav nav-tabs">
			<li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
			<li><a href="#help-onthispage" data-toggle="tab">Navigation</a></li>
		  </ul>
		  <div class="tab-content">
			<div class="tab-pane fade in active" id="help-overview">
			<p></p>
			<p>Welcome to your dashboard! This page contains a quick overview of your excellent video site.</p>
			<p>A brief glance at the <strong>Quick Stats</strong> panel below will reveal all the important numbers. Actions requiring your attention will appear in dark orange, while critical items will appear in red. Such actions include comments and/or videos awaiting approval, reported videos, etc..</p>
			<p>The <strong>Quick Stats</strong> blocks of information are also clickable and act as shortcuts to the various areas of this administration panel.</p>
			<p>The <strong>Updates</strong> section is will keep you informed about critical PHP MELODY updates as well as current developments. New notifications will appear highlighted and they will stay so for 14 days.</p>
			</div>
			<div class="tab-pane fade" id="help-onthispage">
<p>The right hand top corner acts as a shortcuts area as well as an information center. Regardless of the page you're browsing, you'll always be able to see items which require your immediate attention. These notifications will appear as an item in your personal menu.</p>
<p>Regardless of the page you're browsing the &quot;Add Video&quot; button allows you to post, search and upload videos with a single click. These three forms will appear in the &quot;Add Video&quot; window. The first form (Youtube Import) allows you to quickly search the Youtube API for any sort of video imaginable. It's by far the easiest and most efficient way to add content to your site. The second form (Direct Input) allows you to simply paste any video URL, both of local and remotely hosted videos from any of the 50+ supported sources (Vimeo, DailyMotion, etc.). The third form allows you to upload your latest creations.</p>
<p>The left hand side navigation is partitioned by content type: videos, articles, pages, categories, comments, users and so on. Each of these menu items is clickable and most contain submenus which will appear on hover. The left hand side navigation also displays a notification in the form of a small icon containing a number with a red background. Those numbers indicate the required actions demanding your attention. Such notifications will include reported videos, videos pending approval and so on.</p>
			</div>
		  </div>
		</div> <!-- /tabbable -->
		</div><!-- .span12 -->
	</div><!-- /help-assist -->
  
	<div class="content">
	<a href="#" id="show-help-assist">Help</a>
	<!--[if lt IE 9]>
	<div class="alert alert-error"><strong>Your browser may be too old</strong>. We strongly recommend using a newer/different browser such as <strong><a href="https://www.google.com/intl/en/chrome/browser/" target="_blank">Chrome</a></strong> or <strong><a href="http://www.mozilla.org/en-US/firefox/new" target="_blank">Firefox</a></strong>.</div>
	<![endif]-->

<h2>Dashboard</h2>

<?php if ($config['admin_welcome']) : ?>
<ul class="unstyled dashboard-widgets dashboard-widgets-1x" id="dashboard-welcome">
	<li class="widget widget-home widget-home-welcome border-radius2 shadow-div">
	<h4>Get started with PHP Melody</h4>
	<div class="widget-handle widget-hide"><a href="index.php?close-welcome=yes">&times;</a></div>
		<div class="row-fluid widget-inside">
			<div class="span4">
			<h3>Make it your own</h3>
			<?php if ($config['template_f'] == 'default') : ?>
			<a href="customize.php" class="btn btn-large btn-success" target="_blank">Customize the layout</a>
			<?php else : ?>
			<a href="settings_theme.php" class="btn btn-large btn-success" target="_blank">Customize the layout</a>
			<?php endif; ?>
			<p><a href="settings_theme.php">Change your site&rsquo;s title</a> or <a href="settings_theme.php">upload the logo</a>.</p>
			</div>
			<div class="span4">
			<h3>Publish content</h3>
			<ul class="unstyled">
				<li><i class="icon-play-circle"></i> <a href="#addVideo" data-toggle="modal">Add your first video</a></li>
				<li><i class="icon-font"></i> <a href="article_manager.php?do=new">Write your first article</a></li>
				<li><i class="icon-file"></i> <a href="page_manager.php?do=new">Create your first page</a></li>
			</ul>
			</div>
			<div class="span4">
			<h3>More actions</h3>
			<ul class="unstyled">
				<li><i class="icon-user"></i> <a href="edit_user_profile.php?uid=1">Customize your profile</a></li>
				<li><i class="icon-film"></i> <a href="prerollstatic_ad_manager.php">Create some pre-roll ads</a></li>
				<li><i class="icon-cog"></i> <a href="settings.php">Update existing settings</a></li>
			</ul>
			</div>
		</div>
	</li>
</ul>
<?php endif; ?>

<ul class="unstyled dashboard-widgets dashboard-widgets-2x">
		<li class="widget widget-home border-radius2 shadow-div">
		<h4>Quick Stats</h4> 
		<div class="widget-handle"><div class="widget-arrow"></div></div>
			<div class="widget-inside">
				<ul class="unstyled qsData">
					<li>
						<a href="videos.php">
							<span class="number"><?php echo pm_number_format($config['total_videos']); ?></span>
							<span class="head">Video<?php echo ($config['total_videos'] == 1) ? '' : 's'; ?></span>
						</a>
					</li>
					<li>
						<a href="approve.php">
							<?php
							$vapprv = ($vapprv > 0) ? $vapprv : count_entries('pm_temp', '', '');
							?>
							<span class="number <?php if($vapprv > 0) {?>qspending<?php } ?>"><?php echo pm_number_format($vapprv); ?></span>
							<span class="head <?php if($vapprv > 0) {?>qspending<?php } ?>">Pending Approval</span>
						</a>
					</li>
					<li>
						<a href="reports.php">
							<span class="number <?php if($crps > 0) {?>qsreported<?php } ?>"><?php echo pm_number_format($crps); ?></span>
							<span class="head <?php if($crps > 0) {?>qsreported<?php } ?>">Reported video<?php echo ($crps == 1) ? '' : 's';?></span>
						</a>
					</li>
					<li>
						<a href="comments.php">
							<span class="number"><?php echo pm_number_format($comments_count = count_entries('pm_comments', '', '')); ?></span>
							<span class="head">Comment<?php echo ($comments_count == 1) ? '' : 's';?></span>
						</a>
					</li>
					<?php if ($config['comment_system'] == 'on') : ?>
					<li>
						<a href="comments.php?filter=pending">
							<span class="number <?php if($capprv > 0) {?>qspending<?php } ?>"><?php echo pm_number_format($capprv); ?></span>
							<span class="head <?php if($capprv > 0) {?>qspending<?php } ?>">New Comment<?php echo ($capprv == 1) ? '' : 's';?></span>
						</a>
					</li>
					<?php endif; ?>
					<li>
						<a href="members.php">
							<span class="number"><?php echo pm_number_format($member_count = count_entries('pm_users', '', '')); ?></span>
							<span class="head">User<?php echo ($member_count == 1) ? '' : 's';?></span>
						</a>
					</li>
					<?php if (_MOD_ARTICLE == 1) : ?>
					<li>
						<a href="articles.php">
							<span class="number"><?php echo pm_number_format($config['total_articles']); ?></span>
							<span class="head">Article<?php echo ($config['total_articles'] == 1) ? '' : 's';?></span>
						</a>
					</li>
					<?php endif; ?>
					<li>
						<a href="pages.php">
							<span class="number"><?php echo pm_number_format($config['total_pages']); ?></span>
							<span class="head">Page<?php echo ($config['total_pages'] == 1) ? '' : 's';?></span>
						</a>
					</li>
				</ul>
			</div>
		</li>


		<li class="widget widget-home border-radius2 shadow-div">
		<h4>PHP Melody News</h4> 
		<div class="widget-handle"><div class="widget-arrow"></div></div>
			<div class="widget-inside scroll-panel">
				<ul class="unstyled morning-news">
				<?php echo cache_this('get_rss_news', 'home_news'); ?>
				</ul>
			</div>
		</li>
</ul>


<div class="clearfix"></div>

<ul class="unstyled dashboard-widgets">
		<li class="widget widget-home border-radius2 shadow-div">
		<h4>Latest Videos</h4> 
		<div class="widget-handle"><div class="widget-arrow"></div></div>
			<div class="widget-inside scroll-panel">
				<ul class="unstyled plist-videos">
					<?php 
					$latest_videos = array();
					if ($crps > 0)
					{
						$sql_limit = ($crps > 5) ? 5 : $crps; 
						$sql = "SELECT r.added, r.reason, v.uniq_id, v.video_title, v.yt_id, v.yt_thumb, v.source_id  
								FROM pm_reports r
								JOIN pm_videos v
								  ON (r.entry_id = v.uniq_id) 
								WHERE r.r_type = '1' 
								ORDER BY r.id DESC 
								LIMIT 0, $sql_limit";
						if ($result = mysql_query($sql))
						{
							while ($row = mysql_fetch_assoc($result))
							{
								$i = (int) $row['added'];
								while (array_key_exists($i, $latest_videos))
								{
									$i++;
								}
								$latest_videos[$i] = $row;
								$latest_videos[$i]['thumb_url'] = show_thumb($row['uniq_id'], 1, $row);
								$latest_videos[$i]['_type'] = 'flagged';
							}
							mysql_free_result($result);
						}
					}
					if ($vapprv > 0 && count($latest_videos) < $widget_items_limit)
					{
						$sql_limit = ($vapprv > 5) ? 5 : $vapprv;
						$sql = "SELECT * 
								FROM pm_temp 
								ORDER BY id DESC 
								LIMIT 0, $sql_limit";
						if ($result = mysql_query($sql))
						{
							while ($row = mysql_fetch_assoc($result))
							{
								$i = (int) $row['added'];
								while (array_key_exists($i, $latest_videos))
								{
									$i++;
								}
								$latest_videos[$i] = $row;
								$latest_videos[$i]['thumb_url'] = $row['thumbnail'];
								$latest_videos[$i]['_type'] = 'pending';
							}
							mysql_free_result($result);
						}
					}
					
					if (($sql_limit = count($latest_videos)) < $widget_items_limit)
					{
						$sql_limit = $widget_items_limit - $sql_limit;
						$sql = "SELECT uniq_id, video_title, yt_id, yt_thumb, added, source_id 
								FROM pm_videos 
								WHERE added < $time_now 
								ORDER BY added DESC
								LIMIT 0, $sql_limit";
						if ($result = mysql_query($sql))
						{
							while ($row = mysql_fetch_assoc($result))
							{
								$i = (int) $row['added'];
								while (array_key_exists($i, $latest_videos))
								{
									$i++;
								}
								$latest_videos[$i] = $row;
								$latest_videos[$i]['thumb_url'] = show_thumb($row['uniq_id'], 1, $row);
								$latest_videos[$i]['_type'] = false;
							}
							mysql_free_result($result);
						}
					}
					
					if (count($latest_videos) > 0)
					{
						foreach ($latest_videos as $time => $video)
						{
							if ($video['_type'] == 'pending') : 
								$video_link = 'approve_edit.php?id='. $video['id'];
							else :
								$video_link = _URL .'/watch.php?vid='. $video['uniq_id'];
							endif;
							
							if ($video['_type'] == 'pending') : ?>
							<li class="video-pending">
							<?php elseif ($video['_type'] == 'flagged') : ?>
							<li class="video-flagged">
							<?php else : ?>
							<li>
							<?php endif; ?>
								<div class="item-date"><?php echo date('M d', $video['added']); ?></div>
								<div class="item-img">
									<a href="<?php echo $video_link;?>" target="_blank"><img src="<?php echo (strpos($video['thumb_url'], 'http') === 0 || strpos($video['thumb_url'], '//') === 0) ? make_url_https($video['thumb_url']) : _THUMBS_DIR . $video['thumb_url']; ?>" width="60" height="30" /></a>
								</div>
								<div class="item-details">
									<a href="<?php echo $video_link;?>" target="_blank" class="item-url" title="<?php echo $video_title;?>"><?php echo $video['video_title']; ?></a>
								</div>
								<div class="item-actions">
								<?php if ($video['_type'] == 'pending') : ?>
									<a href="approve_edit.php?id=<?php echo $video['id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="Edit video"><i class="icon-pencil"></i></a>
								<?php elseif ($video['_type'] == 'flagged') : ?>
									<a href="modify.php?vid=<?php echo $video['uniq_id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="Edit video"><i class="icon-pencil"></i></a>
								<?php else : ?>
									<a href="modify.php?vid=<?php echo $video['uniq_id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="Edit video"><i class="icon-pencil"></i></a>
								<?php endif; ?>
									<!--<a href="#" class="btn btn-mini btn-link" rel="tooltip" title="Delete report"><i class="icon-remove"></i></a>-->
								</div>
								<?php if ($video['_type'] == 'pending') : ?>
								<a href="approve.php"><span class="label-pending border-radius2">Waiting Approval</span></a>
								<?php elseif ($video['_type'] == 'flagged') : ?>
								<a href="reports.php"><span class="label-flagged border-radius2">Reported</span></a>
								<?php endif; ?>
							</li>
						<?php
						}
					}
					else
					{
						?>
						<li><a href="#addVideo" data-toggle="modal">Add your first video</a></li>
						<?php
					}
					?>
				</ul>
			</div>
		</li>
		
		<li class="widget widget-home border-radius2 shadow-div">
		<h4>Newest Users</h4> 
		<div class="widget-handle"><div class="widget-arrow"></div></div>
			<div class="widget-inside scroll-panel">
				<ul class="unstyled plist-users">
					<?php
					$sql = "SELECT id, username, name, gender, country, reg_date, avatar  
							FROM pm_users 
							ORDER BY id DESC
							LIMIT 0, $widget_items_limit";
					if ($result = mysql_query($sql))
					{
						while ($row = mysql_fetch_assoc($result))
						{
							?>
							<li>
								<div class="item-date"><?php echo date('M d', $row['reg_date']); ?></div>
								<div class="item-img"><img src="<?php echo get_avatar_url($row['avatar']);?>" width="45" height="45" /></div>
								<div class="item-details"><a href="<?php echo get_profile_url($row); ?>" target="_blank" class="item-user-url"><?php echo $row['username']; ?></a> from <?php echo countryid2name($row['country']); ?></div>
								<div class="item-actions">
									<a href="edit_user_profile.php?uid=<?php echo $row['id']; ?>" class="btn btn-mini btn-link" rel="tooltip" title="Edit profile"><i class="icon-pencil"></i></a>
									<!--<a href="#" onClick='' class="btn btn-mini btn-link" rel="tooltip" title="Delete user"><i class="icon-remove"></i></a>-->
								</div>
							</li>
							
							<?php
						}
						mysql_free_result($result);
					}
					?>
				</ul>
			</div>
		</li>
		
		<?php if ($config['comment_system'] == 'on') : ?>
		<li class="widget widget-home border-radius2 shadow-div">
		<h4>Latest Comments</h4> 
		<div class="widget-handle"><div class="widget-arrow"></div></div>
			<div class="widget-inside scroll-panel">
				<ul class="unstyled plist-comments">
					<?php if ($comments_count > 0) : ?>
					<?php
					$sql = "SELECT c.id, c.username, c.comment, c.approved, c.added, c.user_id, v.uniq_id, v.video_title 
							FROM pm_comments c
							LEFT JOIN pm_videos v 
							  ON (c.uniq_id = v.uniq_id)
							ORDER BY id DESC
							LIMIT 0, $widget_items_limit";
					if ($result = mysql_query($sql))
					{
						while ($row = mysql_fetch_assoc($result))
						{
							if ($row['uniq_id'] !== null) 
							{
								$user_ids[] = $row['user_id'];
								$comments_data[] = $row;
							}
						}
						mysql_free_result($result);
						
						if (count($user_ids) > 0)
						{					
							$sql = "SELECT id, avatar, username 
									FROM pm_users 
									WHERE id IN (". implode(',', $user_ids) .")";
							$result = mysql_query($sql);
							while ($row = mysql_fetch_assoc($result))
							{
								$commenters[$row['id']] = $row['avatar'];
							}
							mysql_free_result($result);
							
							foreach ($comments_data as $k => $c)
							{
								$comment_excerpt = strip_tags($c['comment']);
								$comment_excerpt = fewchars($comment_excerpt, 70);
								if ($c['user_id'] != 0)
								{
									$profile_url = get_profile_url($c);
								}
								else 
								{
									$profile_url = '#';
								}
								?>
								<li>
									<div class="item-date"><?php echo date('M d', $c['added']); ?></div>
									<div class="item-img"><img src="<?php echo get_avatar_url($commenters[$c['user_id']]); ?>" width="45" height="45" /></div>
									<div class="item-details"><a href="<?php echo $profile_url; ?>"  target="_blank" class="item-user-url"><?php echo $c['username']; ?></a> commented on <a href="<?php echo _URL .'/watch.php?vid='. $c['uniq_id']; ?>" target="_blank" class="item-url" title="<?php echo $c['video_title']; ?>"><?php echo $c['video_title']; ?></a></div>
									<div class="item-comment">
									<p><?php echo $comment_excerpt;?></p>
									</div>
									<div class="item-actions"></div>
									<?php if ($c['approved'] == 0) : ?>
									<a href="comments.php?filter=pending"><span class="label-pending border-radius2">Waiting Approval</span></a>
									<?php endif; ?>
								</li>
								<?php
							}
						}
						else
						{
							?>
							<li>No comments have been posted yet.</li>
							<?php
						}
					}
					?>
					
					<?php else : ?>
					<li>No comments have been posted yet.</li>
					<?php endif; ?>
				</ul>
			</div>
		</li>
		<?php endif; ?>
		
		<?php if (_MOD_ARTICLE) : ?>
		<li class="widget widget-home border-radius2 shadow-div">
		<h4>Latest Articles</h4> 
		<div class="widget-handle"><div class="widget-arrow"></div></div>
			<div class="widget-inside scroll-panel">
				<ul class="unstyled plist-articles">
					<?php if ($config['total_articles'] > 0) : ?>
					<?php
					$articles = list_articles('', '', 0 , $widget_items_limit, 'public'); 
					
					foreach ($articles as $k => $article) :
					?>
					<li>
						<div class="item-date"><?php echo date('M d', $article['date'])?></div>
						<div class="item-details">
							<a href="<?php echo _URL.'/article_read.php?a='. $article['id']; if ($article['status'] == 0 || $article['date'] > $time_now) echo '&mode=preview'; ?>" target="_blank" class="item-user-url" title="Read: <?php echo $article['title']; ?>"><?php echo $article['title']; ?></a>
							in 
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
						</div>
						<div class="item-actions">
							<a href="article_manager.php?do=edit&id=<?php echo $article['id'];?>" class="btn btn-mini btn-link" rel="tooltip" title="Edit article"><i class="icon-pencil"></i></a>
						</div>
					</li>
					<?php endforeach; ?> 
					<?php else : ?>
					<li>
						<a href="article_manager.php?do=new">Write your first article</a> 
					</li> 
					<?php endif; ?>
				</ul>
			</div>
		</li>
		<?php endif; ?>
</ul>
<div class="clearfix"></div>
	</div><!-- .content -->
</div><!-- .primary -->
<?php

include('footer.php');
