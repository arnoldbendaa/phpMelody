<?php
$parts = explode('/', $_SERVER['SCRIPT_NAME']);
$submenu = array_pop($parts);
$submenu = str_replace('.php', '', $submenu);

switch ($submenu)
{
	default:
	case 'index':
		
		$menu = 'index';
	
	break;
	
	case 'videos':
	case 'modify':
	case 'addvideo':
	case 'embedvideo':
	case 'streamvideo':
	case 'import':
	case 'import-user':
	case 'import-csv':
	case 'reports':
	case 'approve':
	case 'approve_edit':
		
		$menu = 'videos';
		
		$submenu = ($submenu == 'approve_edit') ? 'approve' : $submenu;
		$submenu = ($submenu == 'modify') ? 'videos' : $submenu; 
		
	break;
	
	case 'articles':
	case 'article_manager':
		
		$menu = 'articles';
		$submenu = ($_GET['do'] == 'edit') ? 'articles' : $submenu; 
		
	break;
	
	case 'comments':
	case 'blacklist':
		
		$menu = 'comments';
		
		if ($submenu == 'blacklist')
		{
			break;
		}
		
		switch ($_GET['filter'])
		{
			default:
			
				$submenu = '';
			
			break;
			
			case 'videos':
				
				$submenu = 'comments-video';
				
			break;
			
			case 'articles':

				$submenu = 'comments-article';
	
			break;
			
			case 'flagged':
				
				$submenu = 'comments-flagged';
					
			break;
			
			case 'pending':
				
				$submenu = 'comments-pending';
			
			break;
		}
		
		if ($menu == 'blacklist')
		{
			$submenu = 'blacklist';
		}
		
	break;
	
	case 'categories':
	case 'edit_category':
	
		$menu = 'categories';
		$submenu = ($_GET['type'] == 'article') ? 'article_categories' : 'video_categories';
		
	break;
	
	case 'pages':
	case 'page_manager':
		
		$menu = 'pages';
		
		$submenu = ($_GET['do'] == 'edit') ? 'pages' : $submenu;
		
	break;
	
	case 'members':
	case 'add_user':
	case 'banlist':
	case 'activity-stream':
	case 'members_export':
	case 'edit_user_profile':
		
		$menu = 'users';
	
	break;
	
	case 'ad_manager':
	case 'prerollstatic_ad_manager':
	case 'videoads':
	case 'ad-report':
		
		$menu = 'ads';
		
	break;
	
	case 'statistics':
	case 'show_searches':
	case 'readlog':
	case 'sys_phpinfo':
		
		$menu = 'stats';
		
	break;

	case 'settings':
	case 'settings_theme':
	case 'db_backup':
	case 'sitemap':
	case 'video-sitemap':
		
		$menu = 'settings';

		if ($submenu == 'sitemap' && $_GET['type'] == 'video-sitemap')
		{
			$submenu = 'video-sitemap';
		}
		
	break;
	
	case 'automated-jobs':
	case 'automated-jobs-setup':
		
		$menu = 'automated-jobs';
		
	break;
}

?>
<div id="wrapper">
	<div id="adminSecondary" class="sideNav-bg">
	<!--<div id="adminmenushadow"></div>-->
	<ul id="sideNav" role="navigation">
	
	<?php
	if (is_moderator() || is_editor())
	{
		// Index
		?>
		<li class="pm-menu <?php echo ($menu == 'index') ? 'active' : ''; ?> pm-menu-first">
		<a href="index.php" class="pm-menu-parent"><div class="pm-sprite ico-dash-new"></div> <span>Dashboard</span></a>
		</li>        
		<?php
		// Videos
		if ($mod_can['manage_videos']) 
		{
		?>

		<li class="pm-menu has-subcats <?php echo ($menu == 'videos') ? 'active' : ''; ?>">
		<a href="videos.php" class="pm-menu-parent"><div class="pm-sprite ico-videos-new"></div> <span>Videos</span><?php if($tab_video_total > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_video_total); ?></span><?php } ?></a>
		<ul class="pm-sub-menu">
			<li <?php echo ($submenu == 'addvideo') ? 'class="active"' : ''; ?>><a href="addvideo.php">Add Video from URL</a></li>
			<li <?php echo ($submenu == 'embedvideo') ? 'class="active"' : ''; ?>><a href="embedvideo.php">Embed video</a></li>
			<li <?php echo ($submenu == 'streamvideo') ? 'class="active"' : ''; ?>><a href="streamvideo.php">Add video stream</a></li>
			<li <?php echo ($submenu == 'import' || $submenu == 'import-user' || $submenu == 'import-csv') ? 'class="active has-subcats"' : 'class="has-subcats"'; ?>><a href="import.php">Import Videos <i class="fa fa-ellipsis-h"></i></a>
				<ul class="pm-sub-sub-menu">
					<li><a href="import.php">Import by Keyword</a></li>
					<li><a href="import-user.php">Import from User</a></li>
					<li><a href="import-csv.php">Import from CSV</a></li>
				</ul>
			</li>

			<li <?php echo ($submenu == 'reports') ? 'class="active"' : ''; ?>><a href="reports.php">Reported Videos<?php if($crps > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($crps); ?></span><?php } ?></a></li>
			<li <?php echo ($submenu == 'approve') ? 'class="active"' : ''; ?>><a href="approve.php">Approve Videos<?php if($vapprv > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($vapprv); ?></span><?php } ?></a></li>
			<?php $modframework->admin_submenu(2);?>
		</ul>
		</li>
		<?php
		}

		// Articles
		if ($mod_can['manage_articles'] || is_editor()) 
		{
			if ( $config['mod_article'] == 1 ) 
			{
		?>
		<li class="pm-menu has-subcats <?php echo ($menu == 'articles') ? 'active' : ''; ?>">
		<a href="articles.php" class="pm-menu-parent"><div class="pm-sprite ico-articles-new"></div> <span>Articles</span></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'article_manager') ? 'class="active"' : ''; ?>><a href="article_manager.php?do=new">Post a new article</a></li>
		<li <?php echo ($submenu == 'articles') ? 'class="active"' : ''; ?>><a href="articles.php">Manage articles</a></li>
		</ul>
		</li>
		<?php 
			}
		}
		// Comments
		if ($mod_can['manage_comments'])
		{
		?>
		<li class="pm-menu has-subcats <?php echo ($menu == 'comments') ? 'active' : ''; ?>">
		<a href="comments.php" class="pm-menu-parent"><div class="pm-sprite ico-comments-new"></div> <span>Comments</span><?php if($tab_comments > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_comments); ?></span><?php } ?></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'comments-video') ? 'class="active"' : ''; ?>><a href="comments.php?filter=videos">Video comments</a></li>
		<?php 
		if ( $config['mod_article'] == 1 ) {
		?>
		<li <?php echo ($submenu == 'comments-article') ? 'class="active"' : ''; ?>><a href="comments.php?filter=articles">Article comments</a></li>
		<?php } ?>
		<li <?php echo ($submenu == 'comments-flagged') ? 'class="active"' : ''; ?>><a href="comments.php?filter=flagged">Flagged Comments<?php if($flagged_comments > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($flagged_comments); ?></span><?php } ?></a></li>
		<li <?php echo ($submenu == 'comments-pending') ? 'class="active"' : ''; ?>><a href="comments.php?filter=pending">Pending Comments<?php if($pending_comments > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($pending_comments); ?></span><?php } ?></a></li>
		<li <?php echo ($submenu == 'blacklist') ? 'class="active"' : ''; ?>><a href="blacklist.php">Abuse Prevention</a></li>
		</ul>
		</li>
		<?php
		}
		// Users
		if ($mod_can['manage_users'])
		{
		?>
		<li class="pm-menu has-subcats <?php echo ($menu == 'users') ? 'active' : ''; ?>">
		<a href="members.php" class="pm-menu-parent"><div class="pm-sprite ico-users-new"></div> <span>Users</span></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'add_user') ? 'class="active"' : ''; ?>><a href="add_user.php">Add New User</a></li>
		<li <?php echo ($submenu == 'banlist') ? 'class="active"' : ''; ?>><a href="banlist.php">Banned Users</a></li>
		<?php if (_MOD_SOCIAL) : ?>
		<li <?php echo ($submenu == 'activity-stream') ? 'class="active"' : ''; ?>><a href="activity-stream.php">Activity Stream</a></li>
		<?php endif;?>
		<?php $modframework->admin_submenu(6);?>
		</ul>
		</li>
		<?php
		}

	} // end  if (is_moderator() || is_editor())
	else
	{
	?>
	
		<li class="pm-menu <?php echo ($menu == 'index') ? 'active' : ''; ?>">
		<a href="index.php" class="pm-menu-parent"><div class="pm-sprite ico-dash-new"></div> <span>Dashboard</span></a>
		</li>
		<li class="pm-menu has-subcats <?php echo ($menu == 'videos') ? 'active' : ''; ?>">
		<a href="videos.php" class="pm-menu-parent"><div class="pm-sprite ico-videos-new"></div> <span>Videos</span><?php if($tab_video_total > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_video_total); ?></span><?php } ?></a>
		<ul class="pm-sub-menu">
			<li <?php echo ($submenu == 'addvideo') ? 'class="active"' : ''; ?>><a href="addvideo.php">Add Video from URL</a></li>
			<li <?php echo ($submenu == 'streamvideo') ? 'class="active"' : ''; ?>><a href="streamvideo.php">Add video stream</a></li>
			<li <?php echo ($submenu == 'import' || $submenu == 'import-user' || $submenu == 'import-csv') ? 'class="active has-subcats"' : 'class="has-subcats"'; ?>><a href="import.php">Import Videos <i class="fa fa-ellipsis-h"></i></a>
				<ul class="pm-sub-sub-menu">
					<li><a href="import.php">Import by Keyword</a></li>
					<li><a href="import-user.php">Import from User</a></li>
					<li><a href="import-csv.php">Import from CSV</a></li>
				</ul>
			</li>
			<li <?php echo ($submenu == 'embedvideo') ? 'class="active"' : ''; ?>><a href="embedvideo.php">Embed video</a></li>
			<li <?php echo ($submenu == 'reports') ? 'class="active"' : ''; ?>><a href="reports.php">Reported Videos<?php if($crps > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($crps); ?></span><?php } ?></a></li>
			<li <?php echo ($submenu == 'approve') ? 'class="active"' : ''; ?>><a href="approve.php">Approve Videos<?php if($vapprv > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($vapprv); ?></span><?php } ?></a></li>
			<?php $modframework->admin_submenu(2);?>
		</ul>
		</li>
		
		<?php if ( $config['mod_article'] == 1 ) { ?>
		<li class="pm-menu has-subcats <?php echo ($menu == 'articles') ? 'active' : ''; ?>">
		<a href="articles.php" class="pm-menu-parent"><div class="pm-sprite ico-articles-new"></div> <span>Articles</span></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'article_manager') ? 'class="active"' : ''; ?>><a href="article_manager.php?do=new">Post a new article</a></li>
		<li <?php echo ($submenu == 'articles') ? 'class="active"' : ''; ?>><a href="articles.php">Manage articles</a></li>
		</ul>
		</li>
		<?php } ?>

		<li class="pm-menu has-subcats <?php echo ($menu == 'pages') ? 'active' : ''; ?>">
		<a href="pages.php" class="pm-menu-parent"><div class="pm-sprite ico-page-new"></div> <span>Pages</span></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'page_manager') ? 'class="active"' : ''; ?>><a href="page_manager.php?do=new">Create new page</a></li>
		<li <?php echo ($submenu == 'pages') ? 'class="active"' : ''; ?>><a href="pages.php">Manage pages</a></li>
		</ul>
		</li>
	
		<li class="pm-menu has-subcats <?php echo ($menu == 'categories') ? 'active' : ''; ?>">
		<a href="categories.php" class="pm-menu-parent"><div class="pm-sprite ico-cats-new"></div> <span>Categories</span></a>
		<ul class="pm-sub-menu">
			<li <?php echo ($submenu == 'video_categories') ? 'class="active"' : ''; ?>><a href="categories.php">Video categories</a></li>
			<?php if ( $config['mod_article'] == 1 ) { ?>
			<li <?php echo ($submenu == 'article_categories') ? 'class="active"' : ''; ?>><a href="categories.php?type=article">Article categories</a></li>
			<?php } ?>
		</ul>
		</li>

		<li class="pm-menu has-subcats <?php echo ($menu == 'comments') ? 'active' : ''; ?>">
		<a href="comments.php" class="pm-menu-parent"><div class="pm-sprite ico-comments-new"></div> <span>Comments</span><?php if($tab_comments > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_comments); ?></span><?php } ?></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'comments-video') ? 'class="active"' : ''; ?>><a href="comments.php?filter=videos">Video comments</a></li>
		<?php 
		if ($config['mod_article'] == '1' && (is_admin() || (is_moderator() && mod_can('manage_comments')))) {
		?>
		<li <?php echo ($submenu == 'comments-article') ? 'class="active"' : ''; ?>><a href="comments.php?filter=articles">Article comments</a></li>
		<?php } ?>
		<li <?php echo ($submenu == 'comments-flagged') ? 'class="active"' : ''; ?>><a href="comments.php?filter=flagged">Flagged Comments<?php if($flagged_comments > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($flagged_comments); ?></span><?php } ?></a></li>
		<li <?php echo ($submenu == 'comments-pending') ? 'class="active"' : ''; ?>><a href="comments.php?filter=pending">Pending Comments<?php if($pending_comments > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($pending_comments); ?></span><?php } ?></a></li>
		<li <?php echo ($submenu == 'blacklist') ? 'class="active"' : ''; ?>><a href="blacklist.php">Abuse Prevention</a></li>
		<?php $modframework->admin_submenu(4);?>
		</ul>
		</li>
	
		<li class="pm-menu has-subcats <?php echo ($menu == 'users') ? 'active' : ''; ?>">
		<a href="members.php" class="pm-menu-parent"><div class="pm-sprite ico-users-new"></div> <span>Users</span></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'add_user') ? 'class="active"' : ''; ?>><a href="add_user.php">Add New User</a></li>
		<li <?php echo ($submenu == 'banlist') ? 'class="active"' : ''; ?>><a href="banlist.php">Banned Users</a></li>
		<?php if (_MOD_SOCIAL) : ?>
		<li <?php echo ($submenu == 'activity-stream') ? 'class="active"' : ''; ?>><a href="activity-stream.php">Activity Stream</a></li>
		<?php endif;?>
		<?php
		if (is_admin())
		{
		?>
		<li><a href="members_export.php" rel="tooltip" data-placement="right" title="A *.CSV file will be generated after clicking this link.">Export to CSV</a></li>
		<?php } ?>
		<?php $modframework->admin_submenu(6);?>
		</ul>
		</li>

		<li class="pm-menu has-subcats <?php echo ($menu == 'ads') ? 'active' : ''; ?>">
		<a href="ad_manager.php" class="pm-menu-parent"><div class="pm-sprite ico-ads-new"></div> <span>Advertisments</span></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'ad_manager') ? 'class="active"' : ''; ?>><a href="ad_manager.php">Classic Banners</a></li>
		<li <?php echo ($submenu == 'prerollstatic_ad_manager') ? 'class="active"' : ''; ?>><a href="prerollstatic_ad_manager.php">Pre-roll Static Ads</a>
		<li <?php echo ($submenu == 'videoads') ? 'class="active"' : ''; ?>><a href="videoads.php">Pre-roll Video Ads</a></li>
		<li <?php echo ($submenu == 'ad-report') ? 'class="active"' : ''; ?>><a href="ad-report.php">Ad Reports</a></li>
		<?php $modframework->admin_submenu(9);?>
		</ul>
		</li>
	
		<li class="pm-menu has-subcats <?php echo ($menu == 'stats') ? 'active' : ''; ?>">
		<a href="statistics.php" class="pm-menu-parent"><div class="pm-sprite ico-stats-new"></div> <span>Statistics &amp; Logs</span><?php if($tab_internallog > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_internallog); ?></span><?php } ?></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'show_searches') ? 'class="active"' : ''; ?>><a href="show_searches.php">Search Log</a></li>
		<li <?php echo ($submenu == 'readlog') ? 'class="active"' : ''; ?>><a href="readlog.php">System Log <?php if($tab_internallog > 0) {?><span class="pm-submenu-count"><?php echo pm_number_format($tab_internallog); ?></span><?php } ?></a></li>
		<li <?php echo ($submenu == 'sys_phpinfo') ? 'class="active"' : ''; ?>><a href="sys_phpinfo.php">PHP Configuration</a></li>
		<?php $modframework->admin_submenu(7);?>
		</ul>
		</li>
			
		<li class="pm-menu has-subcats <?php echo ($menu == 'settings') ? 'active' : ''; ?>">
        <a href="settings.php" class="pm-menu-parent"><div class="pm-sprite ico-settings-new"></div> <span>Settings</span></a>
		<ul class="pm-sub-menu">
		<li <?php echo ($submenu == 'settings_theme') ? 'class="active"' : ''; ?>><a href="settings_theme.php">Layout Settings</a></li>
        <li <?php echo ($submenu == 'db_backup') ? 'class="active"' : ''; ?>><a href="<?php echo csrfguard_url(_URL .'/'. _ADMIN_FOLDER .'/db_backup.php?restart=1', '_admin_backupdb');?>">Backup Database</a></li>
        
		<li <?php echo ($submenu == 'sitemap' || $submenu == 'video-sitemap') ? 'class="active has-subcats"' : 'class="has-subcats"'; ?>><a href="sitemap.php?type=sitemap">Sitemaps <i class="fa fa-ellipsis-h"></i></a>
			<ul class="pm-sub-sub-menu">
				<li <?php echo ($submenu == 'sitemap') ? 'class="active"' : ''; ?>><a href="sitemap.php?type=sitemap">Create Regular Sitemap</a></li>	
				<li <?php echo ($submenu == 'video-sitemap') ? 'class="active"' : ''; ?>><a href="sitemap.php?type=video-sitemap">Create Video Sitemap</a></li>
			</ul>
		</li>
		<?php $modframework->admin_submenu(8);?>
		</ul>
		</li>
		
		<li class="pm-menu has-subcats <?php echo ($menu == 'automated-jobs') ? 'active' : ''; ?>">
		<a href="automated-jobs.php" class="pm-menu-parent"><div class="pm-sprite ico-cronjobs"></div> <span>Automated Jobs</span><?php if($tab_cron > 0) {?><span class="pm-menu-count"><?php echo pm_number_format($tab_cron); ?></span><?php } ?></a>
		<ul class="pm-sub-menu">
			<li <?php echo ($submenu == 'automated-jobs-setup') ? 'class="active"' : ''; ?>><a href="automated-jobs-setup.php">Setup</a></li>
		</ul>
		</li>
	<?php
	} // end #subNav
	?>
		<?php
		if (is_admin() ) {
		$modframework->show_admin_menu();
		$modframework->trigger_hook('admin_menu');
		}
		?>
		<li class="pm-menu-last"></li>
	
	</ul><!-- .sideNav -->
	</div><!-- #sideNav -->
<?php
unset($parts, $menu, $submenu);