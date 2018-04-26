-- PHP Melody - Video CMS
-- http://www.phpmelody.com
-- Installation SQL file


--
-- Table structure for table `art_articles`
--

DROP TABLE IF EXISTS `art_articles`;
CREATE TABLE `art_articles` (
  `id` int(6) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `category` varchar(100) NOT NULL DEFAULT '',
  `status` smallint(3) NOT NULL DEFAULT '0',
  `date` int(10) NOT NULL DEFAULT '0',
  `author` int(5) NOT NULL DEFAULT '0',
  `allow_comments` enum('0','1') NOT NULL DEFAULT '1',
  `comment_count` int(7) NOT NULL DEFAULT '0',
  `views` int(8) unsigned NOT NULL DEFAULT '0',
  `featured` enum('0','1') NOT NULL DEFAULT '0',
  `restricted` enum('0','1') NOT NULL DEFAULT '0',
  `article_slug` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `art_articles`
--


-- --------------------------------------------------------

--
-- Table structure for table `art_categories`
--

DROP TABLE IF EXISTS `art_categories`;
CREATE TABLE `art_categories` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `parent_id` int(3) NOT NULL DEFAULT '0',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `published_articles` int(7) unsigned NOT NULL DEFAULT '0',
  `total_articles` int(7) NOT NULL DEFAULT '0',
  `position` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `meta_tags` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `art_categories`
--


-- --------------------------------------------------------

--
-- Table structure for table `art_tags`
--

DROP TABLE IF EXISTS `art_tags`;
CREATE TABLE `art_tags` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `article_id` int(6) NOT NULL DEFAULT '0',
  `tag` varchar(255) NOT NULL DEFAULT '',
  `safe_tag` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `art_tags`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_activity`
--

DROP TABLE IF EXISTS `pm_activity`;
CREATE TABLE `pm_activity` (
  `activity_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `activity_type` varchar(50) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `object_type` varchar(50) NOT NULL DEFAULT '',
  `target_id` int(10) unsigned NOT NULL DEFAULT '0',
  `target_type` varchar(50) NOT NULL DEFAULT '',
  `hide` enum('0','1') NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  PRIMARY KEY (`activity_id`),
  KEY `activity_type` (`activity_type`),
  KEY `hide` (`hide`),
  KEY `objects` (`object_id`,`object_type`),
  KEY `targets` (`target_id`,`target_type`),
  KEY `user_id` (`user_id`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_activity`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_ads`
--

DROP TABLE IF EXISTS `pm_ads`;
CREATE TABLE `pm_ads` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `position` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `code` text NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `disable_stats` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_ads`
--

INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(1, 'Header', 'Appears on all pages right under the horizontal menu', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(2, 'Footer', 'Appears on all pages right before the footer', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(3, 'Video Player', 'Appears on video pages under the video player. (Recommended max. width: 540px)', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(4, 'Article Page', 'Appears at the end of all articles (Recommended max. width: 540px)', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(5, 'Index page', 'Appears as the first widget block on the right site of your homepage (Recommended max. width: 250px)', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(6, 'Floating Skyscraper (Left)', 'Appears on the left side of the page container', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(7, 'Floating Skyscraper (Right)', 'Appears on the right side of the page container', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(8, 'mobile_header', 'Appears in the header of Mobile Melody (requires <a href="http://www.phpsugar.com/order.php?id=mobile" target="_blank">Mobile Melody</a>)', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(9, 'mobile_footer', 'Appears in the footer of Mobile Melody (requires <a href="http://www.phpsugar.com/order.php?id=mobile" target="_blank">Mobile Melody</a>)', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(10, 'mobile_video', 'Appears on the video pages of Mobile Melody (requires <a href="http://www.phpsugar.com/order.php?id=mobile" target="_blank">Mobile Melody</a>)', '', '0', '0');
INSERT INTO `pm_ads` (`id`, `position`, `description`, `code`, `active`, `disable_stats`) VALUES(11, 'mobile_article', 'Appears on the article pages of Mobile Melody (requires <a href="http://www.phpsugar.com/order.php?id=mobile" target="_blank">Mobile Melody</a>)', '', '0', '0');
-- --------------------------------------------------------

--
-- Table structure for table `pm_ads_log`
--

DROP TABLE IF EXISTS `pm_ads_log`;
CREATE TABLE `pm_ads_log` (
  `log_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `date` date NOT NULL DEFAULT '0000-00-00',
  `ad_id` mediumint(6) NOT NULL DEFAULT '0',
  `ad_type` smallint(2) NOT NULL DEFAULT '0',
  `impressions` int(11) unsigned NOT NULL DEFAULT '0',
  `clicks` int(11) unsigned NOT NULL DEFAULT '0',
  `skips` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`log_id`),
  UNIQUE KEY `date` (`date`,`ad_id`,`ad_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pm_banlist`
--

DROP TABLE IF EXISTS `pm_banlist`;
CREATE TABLE `pm_banlist` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(6) unsigned NOT NULL DEFAULT '0',
  `reason` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_banlist`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_bin_rating_meta`
--

DROP TABLE IF EXISTS `pm_bin_rating_meta`;
CREATE TABLE `pm_bin_rating_meta` (
  `vote_meta_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `up_vote_count` int(11) NOT NULL DEFAULT '0',
  `down_vote_count` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_meta_id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `score` (`score`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_bin_rating_meta`
--

INSERT INTO `pm_bin_rating_meta` (`vote_meta_id`, `uniq_id`, `up_vote_count`, `down_vote_count`, `score`) VALUES(7, '14a4e06f8', 1, 0, 1);

-- --------------------------------------------------------

--
-- Table structure for table `pm_bin_rating_votes`
--

DROP TABLE IF EXISTS `pm_bin_rating_votes`;
CREATE TABLE `pm_bin_rating_votes` (
  `vote_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `vote_value` tinyint(1) NOT NULL DEFAULT '0',
  `vote_ip` varchar(40) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`vote_id`),
  KEY `uniq_id` (`uniq_id`,`vote_ip`,`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_bin_rating_votes`
--

INSERT INTO `pm_bin_rating_votes` (`vote_id`, `uniq_id`, `vote_value`, `vote_ip`, `user_id`, `date`) VALUES(1, '14a4e06f8', 1, '127.0.0.1', 1, 1355843312);

-- --------------------------------------------------------

--
-- Table structure for table `pm_categories`
--

DROP TABLE IF EXISTS `pm_categories`;
CREATE TABLE `pm_categories` (
  `id` smallint(3) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` smallint(3) unsigned NOT NULL DEFAULT '0',
  `tag` varchar(50) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `published_videos` int(7) unsigned NOT NULL DEFAULT '0',
  `total_videos` int(7) NOT NULL DEFAULT '0',
  `position` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `meta_tags` text NOT NULL,
  `image` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_categories`
--

INSERT INTO `pm_categories` (`id`, `parent_id`, `tag`, `name`, `published_videos`, `total_videos`, `position`, `description`, `meta_tags`, `image`) VALUES(1, 0, 'categoryone', 'Sample Category #1', 1, 1, 1, '', '', '');
INSERT INTO `pm_categories` (`id`, `parent_id`, `tag`, `name`, `published_videos`, `total_videos`, `position`, `description`, `meta_tags`, `image`) VALUES(2, 1, 'subcat', 'Sample Sub-cat', 0, 0, 1, '', '', '');
INSERT INTO `pm_categories` (`id`, `parent_id`, `tag`, `name`, `published_videos`, `total_videos`, `position`, `description`, `meta_tags`, `image`) VALUES(3, 0, 'categorytwo', 'Sample Category #2', 1, 1, 2, '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `pm_chart`
--

DROP TABLE IF EXISTS `pm_chart`;
CREATE TABLE `pm_chart` (
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `views` int(9) unsigned NOT NULL DEFAULT '0',
  `views_this` int(6) NOT NULL DEFAULT '0',
  `views_last` int(6) NOT NULL DEFAULT '0',
  `views_seclast` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uniq_id`),
  KEY `views` (`views`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_chart`
--

INSERT INTO `pm_chart` (`uniq_id`, `views`, `views_this`, `views_last`, `views_seclast`) VALUES('14a4e06f8', 1, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pm_comments`
--

DROP TABLE IF EXISTS `pm_comments`;
CREATE TABLE `pm_comments` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(50) DEFAULT NULL,
  `username` varchar(100) NOT NULL DEFAULT '',
  `comment` text NOT NULL,
  `added` int(10) unsigned NOT NULL DEFAULT '0',
  `user_ip` varchar(40) NOT NULL DEFAULT '',
  `user_id` mediumint(7) NOT NULL DEFAULT '0',
  `approved` enum('0','1') NOT NULL DEFAULT '0',
  `up_vote_count` int(10) unsigned NOT NULL DEFAULT '0',
  `down_vote_count` int(10) unsigned NOT NULL DEFAULT '0',
  `score` int(10) NOT NULL DEFAULT '0',
  `report_count` mediumint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `score` (`score`),
  KEY `report_count` (`report_count`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_comments`
--

INSERT INTO `pm_comments` (`id`, `uniq_id`, `username`, `comment`, `added`, `user_ip`, `user_id`, `approved`, `up_vote_count`, `down_vote_count`, `score`, `report_count`) VALUES(1, 'ac0266df0', 'admin', 'Lovely!', 1355843312, '127.0.0.1', 1, '1', 0, 0, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `pm_comments_reported`
--

DROP TABLE IF EXISTS `pm_comments_reported`;
CREATE TABLE `pm_comments_reported` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`comment_id`),
  KEY `user_id_2` (`user_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_comments_reported`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_config`
--

DROP TABLE IF EXISTS `pm_config`;
CREATE TABLE `pm_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_config`
--

INSERT INTO `pm_config` (`name`, `value`) VALUES('contact_mail', 'noreply@domain.com');
INSERT INTO `pm_config` (`name`, `value`) VALUES('thumb_from', '2');
INSERT INTO `pm_config` (`name`, `value`) VALUES('browse_page', '16');
INSERT INTO `pm_config` (`name`, `value`) VALUES('browse_articles', '5');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_w', '638');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_h', '401');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_w_index', '638');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_h_index', '344');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_w_favs', '575');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_h_favs', '466');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_w_embed', '640');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_h_embed', '360');
INSERT INTO `pm_config` (`name`, `value`) VALUES('isnew_days', '7');
INSERT INTO `pm_config` (`name`, `value`) VALUES('ispopular', '100');
INSERT INTO `pm_config` (`name`, `value`) VALUES('stopbadcomments', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('comments_page', '10');
INSERT INTO `pm_config` (`name`, `value`) VALUES('template_f', 'default');
INSERT INTO `pm_config` (`name`, `value`) VALUES('firstinstall', 'n');
INSERT INTO `pm_config` (`name`, `value`) VALUES('counterhtml', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('voth_cat', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('views_from', '2');
INSERT INTO `pm_config` (`name`, `value`) VALUES('fav_limit', '20');
INSERT INTO `pm_config` (`name`, `value`) VALUES('version', '2.7.3');
INSERT INTO `pm_config` (`name`, `value`) VALUES('seomod', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('new_videos', '24');
INSERT INTO `pm_config` (`name`, `value`) VALUES('top_videos', '10');
INSERT INTO `pm_config` (`name`, `value`) VALUES('chart_days', '7');
INSERT INTO `pm_config` (`name`, `value`) VALUES('chart_last_reset', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('guests_can_comment', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('comm_moderation_level', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('show_tags', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('shuffle_tags', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('tag_cloud_limit', '20');
INSERT INTO `pm_config` (`name`, `value`) VALUES('show_stats', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('account_activation', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('issmtp', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_timecolor', '545454');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_bgcolor', '5e5e5e');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_autoplay', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_autobuff', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_watermarkurl', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_watermarklink', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('player_watermarkshow', 'fullscreen');
INSERT INTO `pm_config` (`name`, `value`) VALUES('search_suggest', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('use_hq_vids', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('total_videoads', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('videoads_delay', '20');
INSERT INTO `pm_config` (`name`, `value`) VALUES('default_lang', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('last_video', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('top_videos_sort', 'views');
INSERT INTO `pm_config` (`name`, `value`) VALUES('video_player', 'videojs');
INSERT INTO `pm_config` (`name`, `value`) VALUES('gzip', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('mod_article', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('mail_server', 'mail.domain.com');
INSERT INTO `pm_config` (`name`, `value`) VALUES('mail_port', '25');
INSERT INTO `pm_config` (`name`, `value`) VALUES('mail_user', 'noreply+domain.com');
INSERT INTO `pm_config` (`name`, `value`) VALUES('mail_pass', 'domain');
INSERT INTO `pm_config` (`name`, `value`) VALUES('show_ads', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('total_videos', '2');
INSERT INTO `pm_config` (`name`, `value`) VALUES('total_articles', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('total_pages', '2');
INSERT INTO `pm_config` (`name`, `value`) VALUES('homepage_title', 'PHP Melody');
INSERT INTO `pm_config` (`name`, `value`) VALUES('homepage_description', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('homepage_keywords', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('moderator_can', 'manage_users:1;manage_comments:1;manage_videos:1;manage_articles:1;');
INSERT INTO `pm_config` (`name`, `value`) VALUES('last_autosync', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_user_uploadvideo', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_user_uploadvideo_bytes', '10485760');
INSERT INTO `pm_config` (`name`, `value`) VALUES('jwplayerskin', 'modieus.zip');
INSERT INTO `pm_config` (`name`, `value`) VALUES('video_sitemap_options', 'a:4:{s:14:"media_keywords";b:0;s:14:"media_category";b:0;s:12:"item_pubDate";b:0;s:10:"last_build";i:0;}');
INSERT INTO `pm_config` (`name`, `value`) VALUES('auto_feature', '300');
INSERT INTO `pm_config` (`name`, `value`) VALUES('bin_rating_allow_anon_voting', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('published_articles', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('published_videos', '2');
INSERT INTO `pm_config` (`name`, `value`) VALUES('comment_default_sort', 'added');
INSERT INTO `pm_config` (`name`, `value`) VALUES('comment_rating_hide_threshold', '3');
INSERT INTO `pm_config` (`name`, `value`) VALUES('user_following_limit', '1000');
INSERT INTO `pm_config` (`name`, `value`) VALUES('mod_social', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('activity_options', 'a:17:{s:6:"follow";i:1;s:8:"unfollow";i:0;s:5:"watch";i:0;s:4:"like";i:1;s:7:"dislike";i:1;s:8:"favorite";i:1;s:10:"send-video";i:0;s:12:"upload-video";i:1;s:13:"suggest-video";i:1;s:4:"read";i:0;s:7:"comment";i:1;s:4:"join";i:1;s:13:"update-avatar";i:1;s:12:"update-cover";i:1;s:6:"status";i:1;s:15:"create-playlist";i:1;s:15:"update-playlist";i:1;}');
INSERT INTO `pm_config` (`name`, `value`) VALUES('pm_notifications_last_prune', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('total_preroll_ads', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('preroll_ads_delay', '300');
INSERT INTO `pm_config` (`name`, `value`) VALUES('default_tpl_customizations', 'YTowOnt9');
INSERT INTO `pm_config` (`name`, `value`) VALUES('custom_logo_url', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('article_widget_limit', '10');
INSERT INTO `pm_config` (`name`, `value`) VALUES('new_page_limit', '50');
INSERT INTO `pm_config` (`name`, `value`) VALUES('top_page_limit', '50');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_registration', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_user_suggestvideo', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('maintenance_mode', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('maintenance_display_message', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('thumb_video_w', '480');
INSERT INTO `pm_config` (`name`, `value`) VALUES('thumb_video_h', '360');
INSERT INTO `pm_config` (`name`, `value`) VALUES('thumb_article_w', '180');
INSERT INTO `pm_config` (`name`, `value`) VALUES('thumb_article_h', '180');
INSERT INTO `pm_config` (`name`, `value`) VALUES('thumb_avatar_w', '180');
INSERT INTO `pm_config` (`name`, `value`) VALUES('thumb_avatar_h', '180');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_nonlatin_usernames', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('featured_autoplay', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('jwplayerkey', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('auto_approve_suggested_videos', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('keyboard_shortcuts', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('show_addthis_widget', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('playingnow_limit', '9');
INSERT INTO `pm_config` (`name`, `value`) VALUES('watch_related_limit', '10');
INSERT INTO `pm_config` (`name`, `value`) VALUES('watch_toprated_limit', '10');
INSERT INTO `pm_config` (`name`, `value`) VALUES('user_upload_daily_limit', '20');
INSERT INTO `pm_config` (`name`, `value`) VALUES('spambot_prevention', 'securimage');
INSERT INTO `pm_config` (`name`, `value`) VALUES('recaptcha_public_key', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('recaptcha_private_key', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('comment_system', 'on');
INSERT INTO `pm_config` (`name`, `value`) VALUES('unread_system_messages', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('disable_indexing', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('rtl_support', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_playlists', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('playlists_limit', '25');
INSERT INTO `pm_config` (`name`, `value`) VALUES('playlists_items_limit', '100');
INSERT INTO `pm_config` (`name`, `value`) VALUES('admin_welcome', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('admin_color_scheme', 'default');
INSERT INTO `pm_config` (`name`, `value`) VALUES('vimeo_api_token', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('register_time_to_submit', '3');
INSERT INTO `pm_config` (`name`, `value`) VALUES('comment_system_primary', 'native');
INSERT INTO `pm_config` (`name`, `value`) VALUES('comment_system_native', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('comment_system_facebook', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('comment_system_disqus', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('disqus_shortname', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('fb_comment_sorting', 'social');
INSERT INTO `pm_config` (`name`, `value`) VALUES('fb_app_id', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('youtube_api_key', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('homepage_featured_limit', '10');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_embedding', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('timezone', 'UTC');
INSERT INTO `pm_config` (`name`, `value`) VALUES('jwplayer7key', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('homepage_featured_categories', 'a:0:{}');
INSERT INTO `pm_config` (`name`, `value`) VALUES('eu_cookie_warning', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('eu_cookie_warning_position', 'floating');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_emojis', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('trashed_videos', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('auto_approve_suggested_videos_verified', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_user_edit_video', '1');
INSERT INTO `pm_config` (`name`, `value`) VALUES('allow_user_delete_video', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('cron_secret_key', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('oauth_facebook', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('oauth_twitter', '0');
INSERT INTO `pm_config` (`name`, `value`) VALUES('oauth_fb_app_id', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('oauth_fb_app_secret', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('oauth_twitter_consumer_key', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('oauth_twitter_consumer_secret', '');
INSERT INTO `pm_config` (`name`, `value`) VALUES('download_thumb_res', 'medium');
INSERT INTO `pm_config` (`name`, `value`) VALUES('csrfguard', '1');

-- --------------------------------------------------------

--
-- Table structure for table `pm_countries`
--

DROP TABLE IF EXISTS `pm_countries`;
CREATE TABLE `pm_countries` (
  `countryid` smallint(3) NOT NULL AUTO_INCREMENT,
  `country` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`countryid`),
  KEY `location` (`country`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_countries`
--

INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(500, 'USA');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(184, 'Albania');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(301, 'Algeria');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(240, 'American Samoa');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(241, 'Andorra');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(302, 'Angola');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(303, 'Anguilla');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(304, 'Antigua');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(115, 'Antilles');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(305, 'Argentina');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(185, 'Armenia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(306, 'Aruba');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(307, 'Australia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(308, 'Austria');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(186, 'Azerbaijan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(187, 'Azores');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(309, 'Bahamas');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(310, 'Bahrain');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(311, 'Bangladesh');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(312, 'Barbados');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(313, 'Barbuda');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(315, 'Belgium');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(316, 'Belize');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(314, 'Belorus');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(317, 'Benin');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(318, 'Bermuda');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(319, 'Bhutan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(320, 'Bolivia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(321, 'Bonaire');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(188, 'Bosnia-Hercegovina');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(322, 'Botswana');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(324, 'Br. Virgin Islands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(323, 'Brazil');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(325, 'Brunei');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(326, 'Bulgaria');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(327, 'Burkina Faso');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(328, 'Burundi');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(189, 'Caicos Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(329, 'Cameroon');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(330, 'Canada');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(190, 'Canary Islands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(331, 'Cape Verde');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(332, 'Cayman Islands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(333, 'Central African Republic');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(334, 'Chad');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(335, 'Channel Islands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(336, 'Chile');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(337, 'China');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(338, 'Colombia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(191, 'Commonwealth of Ind');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(339, 'Congo');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(242, 'Cook Islands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(192, 'Cooper Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(340, 'Costa Rica');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(193, 'Cote D''Ivoire');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(194, 'Croatia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(341, 'Curacao');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(342, 'Cyprus');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(343, 'Czech Republic');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(344, 'Denmark');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(345, 'Djibouti');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(346, 'Dominica');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(347, 'Dominican Republic');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(348, 'Ecuador');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(349, 'Egypt');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(350, 'El Salvador');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(351, 'England');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(352, 'Equatorial Guinea');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(353, 'Estonia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(354, 'Ethiopia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(355, 'Fiji');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(356, 'Finland');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(357, 'France');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(358, 'French Guiana');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(243, 'French Polynesia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(254, 'Futuna Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(359, 'Gabon');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(360, 'Gambia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(215, 'Georgia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(361, 'Germany');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(362, 'Ghana');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(216, 'Gibraltar');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(363, 'Greece');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(364, 'Grenada');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(217, 'Grenland');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(365, 'Guadeloupe');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(366, 'Guam');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(367, 'Guatemala');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(368, 'Guinea');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(369, 'Guinea-Bissau');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(370, 'Guyana');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(195, 'Haiti');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(244, 'Holland');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(371, 'Honduras');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(372, 'Hong Kong');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(373, 'Hungary');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(374, 'Iceland');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(375, 'India');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(376, 'Indonesia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(377, 'Iran');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(196, 'Iraq');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(378, 'Ireland, Northern');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(379, 'Ireland, Republic of');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(197, 'Isle of Man');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(380, 'Israel');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(381, 'Italy');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(382, 'Ivory Coast');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(383, 'Jamaica');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(384, 'Japan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(385, 'Jordan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(198, 'Jost Van Dyke Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(218, 'Kampuchea');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(199, 'Kazakhstan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(386, 'Kenya');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(219, 'Kiribati');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(239, 'Korea');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(387, 'Korea, South');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(256, 'Kosrae');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(388, 'Kuwait');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(200, 'Kyrgyzstan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(220, 'Laos');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(389, 'Latvia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(390, 'Lebanon');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(391, 'Lesotho');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(221, 'Liberia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(392, 'Liechtenstein');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(393, 'Lithuania');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(394, 'Luxembourg');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(395, 'Macau');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(222, 'Macedonia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(396, 'Madagascar');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(201, 'Madeira Islands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(202, 'Malagasy');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(397, 'Malawi');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(398, 'Malaysia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(399, 'Maldives');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(100, 'Mali');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(101, 'Malta');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(102, 'Marshall Islands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(103, 'Martinique');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(104, 'Mauritania');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(105, 'Mauritius');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(106, 'Mexico');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(107, 'Micronesia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(203, 'Moldova');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(108, 'Monaco');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(223, 'Mongolia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(109, 'Montserrat');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(110, 'Morocco');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(111, 'Mozambique');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(224, 'Myanmar');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(112, 'Namibia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(225, 'Nauru');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(113, 'Nepal');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(114, 'Netherlands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(204, 'Nevis');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(246, 'Nevis (St. Kitts)');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(116, 'New Caledonia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(117, 'New Zealand');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(118, 'Nicaragua');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(119, 'Niger');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(120, 'Nigeria');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(226, 'Niue');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(258, 'Norfolk Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(205, 'Norman Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(257, 'Northern Mariana Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(121, 'Norway');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(122, 'Oman');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(123, 'Pakistan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(124, 'Palau');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(125, 'Panama');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(126, 'Papua New Guinea');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(127, 'Paraguay');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(128, 'Peru');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(129, 'Philippines');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(130, 'Poland');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(260, 'Ponape');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(131, 'Portugal');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(132, 'Qatar');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(133, 'Reunion');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(134, 'Romania');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(261, 'Rota');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(135, 'Russia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(136, 'Rwanda');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(137, 'Saba');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(147, 'Saipan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(228, 'San Marino');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(229, 'Sao Tome');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(148, 'Saudi Arabia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(149, 'Scotland');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(150, 'Senegal');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(207, 'Serbia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(151, 'Seychelles');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(152, 'Sierra Leone');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(153, 'Singapore');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(208, 'Slovakia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(209, 'Slovenia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(210, 'Solomon Islands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(154, 'Somalia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(155, 'South Africa');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(156, 'Spain');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(157, 'Sri Lanka');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(138, 'St. Barthelemy');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(206, 'St. Christopher');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(139, 'St. Croix');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(140, 'St. Eustatius');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(141, 'St. John');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(142, 'St. Kitts');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(143, 'St. Lucia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(144, 'St. Maarten');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(245, 'St. Martin');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(145, 'St. Thomas');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(146, 'St. Vincent');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(158, 'Sudan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(159, 'Suriname');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(160, 'Swaziland');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(161, 'Sweden');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(162, 'Switzerland');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(163, 'Syria');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(247, 'Tahiti');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(164, 'Taiwan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(211, 'Tajikistan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(165, 'Tanzania');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(166, 'Thailand');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(248, 'Tinian');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(167, 'Togo');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(230, 'Tonaga');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(249, 'Tonga');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(250, 'Tortola');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(168, 'Trinidad and Tobago');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(251, 'Truk');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(169, 'Tunisia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(170, 'Turkey');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(212, 'Turkmenistan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(171, 'Turks and Caicos Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(231, 'Tuvalu');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(175, 'U.S. Virgin Islands');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(172, 'Uganda');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(173, 'Ukraine');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(252, 'Union Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(174, 'United Arab Emirates');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(176, 'Uruguay');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(262, 'United Kingdom');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(232, 'Uzbekistan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(233, 'Vanuatu');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(177, 'Vatican City');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(178, 'Venezuela');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(234, 'Vietnam');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(235, 'Virgin Islands (Brit');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(236, 'Virgin Islands (U.S.');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(237, 'Wake Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(179, 'Wales');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(253, 'Wallis Island');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(238, 'Western Samoa');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(255, 'Yap');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(180, 'Yemen, Republic of');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(213, 'Yugoslavia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(181, 'Zaire');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(182, 'Zambia');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(183, 'Zimbabwe');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(501, 'Kosova');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(502, 'Afghanistan');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(503, 'Libya');
INSERT INTO `pm_countries` (`countryid`, `country`) VALUES(504, 'Eritrea');

-- --------------------------------------------------------

--
-- Table structure for table `pm_cron_jobs`
--

CREATE TABLE IF NOT EXISTS `pm_cron_jobs` (
  `job_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `type` varchar(100) NOT NULL DEFAULT '',
  `status` varchar(50) NOT NULL DEFAULT '',
  `state` varchar(50) NOT NULL DEFAULT '',
  `exec_frequency` int(10) unsigned NOT NULL DEFAULT '86400',
  `last_exec_time` int(10) unsigned NOT NULL DEFAULT '0',
  `rel_object_id` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  `created_time` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`job_id`),
  KEY `status` (`status`,`state`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_cron_jobs`
--

INSERT INTO `pm_cron_jobs` (`name`, `type`, `status`, `state`, `exec_frequency`, `last_exec_time`, `rel_object_id`, `data`, `created_time`) VALUES('Video Status Checker', 'vscheck', 'stopped', 'ready', 259200, 0, 9, 'a:5:{s:9:"sql_start";i:0;s:12:"time_started";i:0;s:16:"videos_processed";i:0;s:13:"video_sorting";s:6:"latest";s:11:"video_limit";s:2:"20";}', 0);
INSERT INTO `pm_cron_jobs` (`name`, `type`, `status`, `state`, `exec_frequency`, `last_exec_time`, `rel_object_id`, `data`, `created_time`) VALUES('Regular Sitemap', 'sitemap', 'stopped', 'ready', 604800, 0, 0, 'a:4:{s:8:"progress";i:0;s:20:"sql_added_time_limit";i:0;s:13:"time_last_run";i:0;s:12:"time_started";i:0;}', 0);
INSERT INTO `pm_cron_jobs` (`name`, `type`, `status`, `state`, `exec_frequency`, `last_exec_time`, `rel_object_id`, `data`, `created_time`) VALUES('Video Sitemap', 'video-sitemap', 'stopped', 'ready', 604800, 0, 0, 'a:4:{s:8:"progress";i:0;s:20:"sql_added_time_limit";i:0;s:13:"time_last_run";i:0;s:12:"time_started";i:0;}', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pm_cron_log`
--

CREATE TABLE IF NOT EXISTS `pm_cron_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `notes` text NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `job_id` (`job_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
-- Table structure for table `pm_embed_code`
--

DROP TABLE IF EXISTS `pm_embed_code`;
CREATE TABLE `pm_embed_code` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `embed_code` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_embed_code`
--

-- --------------------------------------------------------

--
-- Table structure for table `pm_import_csv_files`
--

DROP TABLE IF EXISTS `pm_import_csv_files`;
CREATE TABLE `pm_import_csv_files` (
  `file_id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL DEFAULT '',
  `upload_date` int(10) unsigned NOT NULL DEFAULT '0',
  `items_detected` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `items_processed` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `items_skipped` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `items_with_error` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `items_imported` mediumint(6) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pm_import_csv_items`
--

DROP TABLE IF EXISTS `pm_import_csv_items`;
CREATE TABLE `pm_import_csv_items` (
  `item_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `file_id` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `video_title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `yt_id` varchar(50) NOT NULL DEFAULT '',
  `yt_length` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `yt_thumb` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(30) NOT NULL DEFAULT '',
  `added` int(10) unsigned NOT NULL DEFAULT '0',
  `site_views` int(9) NOT NULL DEFAULT '0',
  `url_flv` varchar(255) NOT NULL DEFAULT '',
  `source_id` smallint(2) unsigned NOT NULL DEFAULT '0',
  `featured` enum('0','1') NOT NULL DEFAULT '0',
  `restricted` enum('0','1') NOT NULL DEFAULT '0',
  `allow_comments` enum('0','1') NOT NULL DEFAULT '1',
  `allow_embedding` enum('0','1') NOT NULL DEFAULT '1',
  `video_slug` varchar(255) NOT NULL DEFAULT '',
  `mp4` varchar(200) NOT NULL DEFAULT '',
  `direct` varchar(200) NOT NULL DEFAULT '',
  `tags` text NOT NULL,
  `embeddable` enum('0','1') NOT NULL DEFAULT '0',
  `private` enum('0','1') NOT NULL DEFAULT '0',
  `geo-restriction` text NOT NULL,
  `has_errors` enum('0','1') NOT NULL DEFAULT '0',
  `errors` text NOT NULL,
  `processed` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`item_id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `yt_id` (`yt_id`),
  KEY `file_id` (`file_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pm_import_subscriptions`
--

DROP TABLE IF EXISTS `pm_import_subscriptions`;
CREATE TABLE `pm_import_subscriptions` (
  `sub_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `sub_name` varchar(255) NOT NULL DEFAULT '',
  `sub_type` varchar(20) NOT NULL DEFAULT '',
  `last_query_time` int(10) unsigned NOT NULL DEFAULT '0',
  `last_query_results` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `data` text NOT NULL,
  PRIMARY KEY (`sub_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Table structure for table `pm_internal_log`
--

CREATE TABLE IF NOT EXISTS `pm_internal_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_date` datetime NOT NULL,
  `log_info` text NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_internal_log`
--

-- --------------------------------------------------------

--
-- Table structure for table `pm_languages`
--

DROP TABLE IF EXISTS `pm_languages`;
CREATE TABLE `pm_languages` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL DEFAULT '',
  `tag` varchar(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_languages`
--

INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(1, 'Abkhazian', 'ab');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(2, 'Afar', 'aa');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(3, 'Afrikaans', 'af');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(4, 'Albanian', 'sq');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(5, 'Amharic', 'am');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(6, 'Arabic', 'ar');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(7, 'Aragonese', 'an');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(8, 'Armenian', 'hy');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(9, 'Assamese', 'as');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(10, 'Aymara', 'ay');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(11, 'Azerbaijani', 'az');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(12, 'Bashkir', 'ba');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(13, 'Basque', 'eu');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(14, 'Bengali (Bangla)', 'bn');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(15, 'Bhutani', 'dz');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(16, 'Bihari', 'bh');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(17, 'Bislama', 'bi');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(18, 'Breton', 'br');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(19, 'Bulgarian', 'bg');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(20, 'Burmese', 'my');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(21, 'Byelorussian (Belarusian)', 'be');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(22, 'Cambodian', 'km');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(23, 'Catalan', 'ca');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(24, 'Chinese (Simplified)', 'zh');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(25, 'Chinese (Traditional)', 'zh');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(26, 'Corsican', 'co');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(27, 'Croatian', 'hr');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(28, 'Czech', 'cs');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(29, 'Danish', 'da');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(30, 'Dutch', 'nl');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(31, 'English', 'en');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(32, 'Esperanto', 'eo');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(33, 'Estonian', 'et');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(34, 'Faeroese', 'fo');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(35, 'Farsi', 'fa');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(36, 'Fiji', 'fj');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(37, 'Finnish', 'fi');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(38, 'French', 'fr');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(39, 'Frisian', 'fy');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(40, 'Galician', 'gl');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(41, 'Gaelic (Scottish)', 'gd');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(42, 'Gaelic (Manx)', 'gv');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(43, 'Georgian', 'ka');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(44, 'German', 'de');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(45, 'Greek', 'el');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(46, 'Greenlandic', 'kl');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(47, 'Guarani', 'gn');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(48, 'Gujarati', 'gu');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(49, 'Haitian Creole', 'ht');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(50, 'Hausa', 'ha');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(51, 'Hebrew', 'he');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(52, 'Hindi', 'hi');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(53, 'Hungarian', 'hu');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(54, 'Icelandic', 'is');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(55, 'Ido', 'io');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(56, 'Indonesian', 'id');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(57, 'Interlingua', 'ia');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(58, 'Interlingue', 'ie');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(59, 'Inuktitut', 'iu');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(60, 'Inupiak', 'ik');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(61, 'Irish', 'ga');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(62, 'Italian', 'it');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(63, 'Japanese', 'ja');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(64, 'Javanese', 'jv');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(65, 'Kannada', 'kn');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(66, 'Kashmiri', 'ks');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(67, 'Kazakh', 'kk');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(68, 'Kinyarwanda (Ruanda)', 'rw');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(69, 'Kirghiz', 'ky');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(70, 'Kirundi (Rundi)', 'rn');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(71, 'Korean', 'ko');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(72, 'Kurdish', 'ku');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(73, 'Laothian', 'lo');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(74, 'Latin', 'la');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(75, 'Latvian (Lettish)', 'lv');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(76, 'Limburgish (Limburger)', 'li');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(77, 'Lingala', 'ln');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(78, 'Lithuanian', 'lt');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(79, 'Macedonian', 'mk');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(80, 'Malagasy', 'mg');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(81, 'Malay', 'ms');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(82, 'Malayalam', 'ml');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(83, 'Maltese', 'mt');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(84, 'Maori', 'mi');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(85, 'Marathi', 'mr');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(86, 'Moldavian', 'mo');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(87, 'Mongolian', 'mn');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(88, 'Nauru', 'na');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(89, 'Nepali', 'ne');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(90, 'Norwegian', 'no');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(91, 'Occitan', 'oc');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(92, 'Oriya', 'or');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(93, 'Oromo (Afan Galla)', 'om');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(94, 'Pashto (Pushto)', 'ps');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(95, 'Polish', 'pl');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(96, 'Portuguese', 'pt');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(97, 'Punjabi', 'pa');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(98, 'Quechua', 'qu');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(99, 'Rhaeto-Romance', 'rm');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(100, 'Romanian', 'ro');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(101, 'Russian', 'ru');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(102, 'Samoan', 'sm');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(103, 'Sangro', 'sg');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(104, 'Sanskrit', 'sa');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(105, 'Serbian', 'sr');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(106, 'Serbo-Croatian', 'sh');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(107, 'Sesotho', 'st');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(108, 'Setswana', 'tn');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(109, 'Shona', 'sn');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(110, 'Sichuan Yi', 'ii');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(111, 'Sindhi', 'sd');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(112, 'Sinhalese', 'si');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(113, 'Siswati', 'ss');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(114, 'Slovak', 'sk');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(115, 'Slovenian', 'sl');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(116, 'Somali', 'so');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(117, 'Spanish', 'es');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(118, 'Sundanese', 'su');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(119, 'Swahili (Kiswahili)', 'sw');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(120, 'Swedish', 'sv');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(121, 'Tagalog', 'tl');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(122, 'Tajik', 'tg');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(123, 'Tamil', 'ta');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(124, 'Tatar', 'tt');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(125, 'Telugu', 'te');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(126, 'Thai', 'th');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(127, 'Tibetan', 'bo');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(128, 'Tigrinya', 'ti');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(129, 'Tonga', 'to');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(130, 'Tsonga', 'ts');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(131, 'Turkish', 'tr');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(132, 'Turkmen', 'tk');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(133, 'Twi', 'tw');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(134, 'Uighur', 'ug');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(135, 'Ukrainian', 'uk');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(136, 'Urdu', 'ur');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(137, 'Uzbek', 'uz');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(138, 'Vietnamese', 'vi');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(139, 'Volap&uuml;k', 'vo');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(140, 'Wallon', 'wa');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(141, 'Welsh', 'cy');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(142, 'Wolof', 'wo');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(143, 'Xhosa', 'xh');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(144, 'Yiddish', 'yi');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(145, 'Yoruba', 'yo');
INSERT INTO `pm_languages` (`id`, `label`, `tag`) VALUES(146, 'Zulu', 'zu');

-- --------------------------------------------------------

--
-- Table structure for table `pm_log`
--

DROP TABLE IF EXISTS `pm_log`;
CREATE TABLE `pm_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `log_msg` text NOT NULL,
  `area` varchar(50) NOT NULL DEFAULT '',
  `added` int(11) NOT NULL DEFAULT '0',
  `msg_type` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `area` (`area`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_log`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_meta`
--

DROP TABLE IF EXISTS `pm_meta`;
CREATE TABLE `pm_meta` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `item_id` int(10) unsigned NOT NULL DEFAULT '0',
  `item_type` smallint(3) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) NOT NULL DEFAULT '',
  `meta_value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`,`item_type`),
  KEY `meta_key` (`meta_key`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_meta`
--

INSERT INTO `pm_meta` (`id`, `item_id`, `item_type`, `meta_key`, `meta_value`) VALUES(4, 1, 3, '_meta_keywords', '');
INSERT INTO `pm_meta` (`id`, `item_id`, `item_type`, `meta_key`, `meta_value`) VALUES(5, 1, 3, '_meta_description', '');
INSERT INTO `pm_meta` (`id`, `item_id`, `item_type`, `meta_key`, `meta_value`) VALUES(6, 2, 3, '_meta_keywords', '');
INSERT INTO `pm_meta` (`id`, `item_id`, `item_type`, `meta_key`, `meta_value`) VALUES(7, 2, 3, '_meta_description', '');

-- --------------------------------------------------------

--
-- Table structure for table `pm_notifications`
--

DROP TABLE IF EXISTS `pm_notifications`;
CREATE TABLE `pm_notifications` (
  `notification_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `to_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `from_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `activity_type` varchar(50) NOT NULL DEFAULT '',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `seen` enum('0','1') NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  PRIMARY KEY (`notification_id`),
  KEY `to_user_id` (`to_user_id`,`seen`),
  KEY `activity_type` (`activity_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_notifications`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_pages`
--

DROP TABLE IF EXISTS `pm_pages`;
CREATE TABLE `pm_pages` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `content` longtext NOT NULL,
  `author` int(5) NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `status` smallint(3) NOT NULL DEFAULT '0',
  `page_name` varchar(255) NOT NULL DEFAULT '',
  `views` int(8) unsigned NOT NULL DEFAULT '0',
  `showinmenu` enum('0','1') NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_pages`
--

INSERT INTO `pm_pages` (`id`, `title`, `content`, `author`, `date`, `status`, `page_name`, `views`, `showinmenu`) VALUES(1, 'Terms of Agreement', '<h2>Code of Conduct</h2>\r\n<p>In using this Service, you must behave in a civil and respectful manner at all times. Further, you will not:</p>\r\n<ul>\r\n<li>Act in a deceptive manner by, among other things, impersonating any person;</li>\r\n<li>Harass or stalk any other person;</li>\r\n<li>Harm or exploit minors;</li>\r\n<li>Distribute "spam";</li>\r\n<li>Collect information about others; or</li>\r\n<li>Advertise or solicit others to purchase any product or service within the Site (unless you are an official partner or advertiser and have a written agreement with us).</li>\r\n</ul>\r\n<p>The Site owner has the right, but not the obligation, to monitor all conduct on and content submitted to the Service.</p>\r\n<hr />\r\n<h2>Membership</h2>\r\n<p>REGISTRATION: To fully use the the Service, you must register as a member by providing a user name, password, and valid email address. You must provide complete and accurate registration information and notify us if your information changes. If you are a business, government, or non-profit entity, the person whose email address is associated with the account must have the authority to bind the entity to this Agreement.</p>\r\n<p>USER NAME: We encourage you to use your real name. If you are a business, government, or non-profit entity, you must use the actual name of your organization. You may not use someone else''s name, a name that violates any third party right, or a name that is obscene or otherwise objectionable.</p>\r\n<p>ACCOUNT SECURITY: You are responsible for all activity that occurs under your account, including any activity by unauthorized users. You must not allow others to use your account. You must safeguard the confidentiality of your password. If you are using a computer that others have access to, you must log out of your account after using the Service.</p>\r\n<hr />\r\n<h2>Content Restrictions</h2>\r\n<p>You may not upload, post, or transmit (collectively, "submit") any video, image, text, audio recording, or other work (collectively, "content") that:</p>\r\n<ul>\r\n<li>Infringes any third party''s copyrights or other rights (e.g., trademark, privacy rights, etc.);</li>\r\n<li>Contains sexually explicit content or pornography (provided, however, that non-sexual nudity is permitted);</li>\r\n<li>Contains hateful, defamatory, or discriminatory content or incites hatred against any individual or group;</li>\r\n<li>Exploits minors;</li>\r\n<li>Depicts unlawful acts or extreme violence;</li>\r\n<li>Depicts animal cruelty or extreme violence towards animals;</li>\r\n<li>Promotes fraudulent schemes, multi level marketing (MLM) schemes, get rich quick schemes, online gaming and gambling, cash gifting, work from home businesses, or any other dubious money-making ventures; or Violates any law.</li>\r\n</ul>', 1, 1366891687, 1, 'terms-toa', 0, '0');
INSERT INTO `pm_pages` (`id`, `title`, `content`, `author`, `date`, `status`, `page_name`, `views`, `showinmenu`) VALUES(2, '404 Error', '<h3>Sorry, page not found!</h3>\r\n<p>The page you are looking for could not be found. Please check the link you followed to get here and try again.</p>', 1, 1366891687, 1, '404', 0, '0');

-- --------------------------------------------------------

--
-- Table structure for table `pm_playlists`
--

DROP TABLE IF EXISTS `pm_playlists`;
CREATE TABLE `pm_playlists` (
  `list_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `list_uniq_id` varchar(25) NOT NULL DEFAULT '',
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `type` smallint(2) NOT NULL DEFAULT '0',
  `items_count` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  `visibility` smallint(2) NOT NULL DEFAULT '0',
  `sorting` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `thumb_source` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`list_id`),
  KEY `list_uniq_id` (`list_uniq_id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_playlists`
--
-- --------------------------------------------------------

INSERT INTO `pm_playlists` (`list_id`, `list_uniq_id`, `user_id`, `type`, `items_count`, `date`, `visibility`, `sorting`, `title`, `description`, `thumb_source`) VALUES (1, 'FBA46DCBE461', 1, 1, 0, 1408110639, 0, 'default', '', '', '');
INSERT INTO `pm_playlists` (`list_id`, `list_uniq_id`, `user_id`, `type`, `items_count`, `date`, `visibility`, `sorting`, `title`, `description`, `thumb_source`) VALUES (2, '913554463FAF', 1, 2, 1, 1408110639, 1, 'default', '', '', '');
INSERT INTO `pm_playlists` (`list_id`, `list_uniq_id`, `user_id`, `type`, `items_count`, `date`, `visibility`, `sorting`, `title`, `description`, `thumb_source`) VALUES (3, '23BA3BE660A1', 1, 3, 1, 1408110639, 1, 'default', '', '', '');
INSERT INTO `pm_playlists` (`list_id`, `list_uniq_id`, `user_id`, `type`, `items_count`, `date`, `visibility`, `sorting`, `title`, `description`, `thumb_source`) VALUES (4, '6A6FCDD83A1A', 1, 4, 0, 1408110639, 0, 'date-added-desc', '', '', '');

--
-- Table structure for table `pm_playlist_items`
--

DROP TABLE IF EXISTS `pm_playlist_items`;
CREATE TABLE `pm_playlist_items` (
  `list_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(10) unsigned NOT NULL DEFAULT '0',
  `video_id` int(10) unsigned NOT NULL DEFAULT '0',
  `position` smallint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`list_item_id`),
  KEY `list_id` (`list_id`),
  KEY `video_id` (`video_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_playlist_items`
--

INSERT INTO `pm_playlist_items` (`list_item_id`, `list_id`, `video_id`, `position`) VALUES (1, 3, 1, 1);
INSERT INTO `pm_playlist_items` (`list_item_id`, `list_id`, `video_id`, `position`) VALUES (2, 2, 2, 1);

-- Table structure for table `pm_preroll_ads`
--

DROP TABLE IF EXISTS `pm_preroll_ads`;
CREATE TABLE `pm_preroll_ads` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `duration` mediumint(5) unsigned NOT NULL DEFAULT '5',
  `user_group` tinyint(2) unsigned NOT NULL DEFAULT '0',
  `impressions` int(10) unsigned NOT NULL DEFAULT '0',
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `code` text NOT NULL,
  `options` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- Table structure for table `pm_ratings`
--

DROP TABLE IF EXISTS `pm_ratings`;
CREATE TABLE `pm_ratings` (
  `id` varchar(10) NOT NULL DEFAULT '',
  `total_votes` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `total_value` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `used_ips` longtext,
  `which_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_ratings`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_reports`
--

DROP TABLE IF EXISTS `pm_reports`;
CREATE TABLE `pm_reports` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `r_type` enum('1','2') NOT NULL DEFAULT '1',
  `entry_id` varchar(20) NOT NULL DEFAULT '',
  `added` varchar(11) NOT NULL DEFAULT '',
  `reason` varchar(100) NOT NULL DEFAULT '',
  `submitted` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `entry_id` (`entry_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_reports`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_searches`
--

DROP TABLE IF EXISTS `pm_searches`;
CREATE TABLE `pm_searches` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `string` varchar(100) NOT NULL DEFAULT '',
  `hits` mediumint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_searches`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_sources`
--

DROP TABLE IF EXISTS `pm_sources`;
CREATE TABLE `pm_sources` (
  `source_id` smallint(2) NOT NULL AUTO_INCREMENT,
  `source_name` varchar(20) NOT NULL DEFAULT '',
  `source_rule` varchar(40) NOT NULL DEFAULT '',
  `url_example` varchar(100) NOT NULL DEFAULT '',
  `last_check` int(10) unsigned NOT NULL DEFAULT '0',
  `flv_player_support` enum('0','1') NOT NULL DEFAULT '0',
  `embed_player_support` enum('0','1') NOT NULL DEFAULT '0',
  `embed_code` text NOT NULL,
  `user_choice` varchar(15) NOT NULL DEFAULT '',
  `vscheck_support` enum('1','0') NOT NULL DEFAULT '0',
  `vscheck_autopilot` enum('1','0') NOT NULL DEFAULT '0',
  PRIMARY KEY (`source_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_sources`
--

INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(1, 'localhost', '/(.*?)\\.flv/i', '', 0, '1', '0', '', 'flvplayer', '1', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(2, 'other', '/(.*?)\\.flv/i', 'http://www.example.com/uploads/video.flv', 0, '1', '0', '', 'flvplayer', '1', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(3, 'youtube', '/youtube\\./i', 'http://www.youtube.com/watch?v=[VIDEO ID]', 0, '1', '1', '<iframe src="//www.youtube.com/embed/%%yt_id%%?hl=en_US&hd=%%use_hq_vids%%&cc_load_policy=1&rel=0&fs=1&autoplay=%%player_autoplay%%&color2=0x%%player_bgcolor%%&showsearch=0&showinfo=0&iv_load_policy=3&modestbranding=1" width="100%" height="%%player_h%%" frameborder="0" allowfullscreen></iframe>', 'flvplayer', '1', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(5, 'dailymotion', '/dailymotion\\./i', 'http://www.dailymotion.com/en/category/[VIDEO ID]_video-title-here', 0, '0', '1', '<iframe frameborder="0" width="100%" height="%%player_h%%" src="//www.dailymotion.com/embed/video/%%yt_id%%&autoplay=%%player_autoplay%%&highlight=20A8E1&info=0&logo=0&related=0&startscreen=html&html=1" allowfullscreen></iframe>', 'embed', '1', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(6, 'metacafe', '/metacafe\\.com/i', 'http://www.metacafe.com/watch/[VIDEO ID]/video_title_here/', 0, '1', '1', '<embed src="//www.metacafe.com/fplayer/%%yt_id%%/video.swf" width="%%player_w%%" height="%%player_h%%" wmode="%%player_wmode%%" pluginspage="//www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" name="Metacafe_%%yt_id%%"> \r\n</embed>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(7, 'myspace', '/myspace\\.com/i', 'http://www.myspace.com/video/channel/video-title/123456781', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="allowFullScreen" value="true"/>\r\n <param name="wmode" value="%%player_wmode%%"/>\r\n <param name="movie" value="//mediaservices.myspace.com/services/media/embed.aspx/m=%%yt_id%%,t=1,mt=video"/>\r\n <embed src="//mediaservices.myspace.com/services/media/embed.aspx/m=%%yt_id%%,t=1,mt=video" width="%%player_w%%" height="%%player_h%%" allowFullScreen="true" type="application/x-shockwave-flash" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(9, 'veoh', '/veoh\\.com/i', 'http://www.veoh.com/collection/Artist-or-Group-Name/watch/[VIDEO ID]', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%" id="veohFlashPlayer" name="veohFlashPlayer">\r\n <param name="movie" value="//www.veoh.com/static/swf/webplayer/WebPlayer.swf?permalinkId=%%yt_id%%&player=videodetailsembedded&videoAutoPlay=%%player_autoplay%%&id=anonymous"></param>\r\n <param name="allowFullScreen" value="true"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowscriptaccess" value="always"></param>\r\n <embed src="//www.veoh.com/static/swf/webplayer/WebPlayer.swf?permalinkId=%%yt_id%%&player=videodetailsembedded&videoAutoPlay=%%player_autoplay%%&id=anonymous" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%" id="veohFlashPlayerEmbed" name="veohFlashPlayerEmbed" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(10, 'break', '/break\\.com/i', 'http://www.break.com/index/video-title-here.html', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="//embed.break.com/%%yt_id%%"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowScriptAccess" value="always"></param>\r\n <embed src="//embed.break.com/%%yt_id%%" type="application/x-shockwave-flash" allowScriptAccess="always" wmode="%%player_wmode%%" width="%%player_w%%" height="%%player_h%%"></embed>\r\n</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(11, 'myvideo', '/myvideo\\.de/i', 'http://www.myvideo.de/watch/[VIDEO ID]/Video_title_here/', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="//www.myvideo.de/movie/%%yt_id%%"></param>\r\n <param name="AllowFullscreen" value="true"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="AllowScriptAccess" value="always"></param>\r\n <embed src="//www.myvideo.de/movie/%%yt_id%%" width="%%player_w%%" height="%%player_h%%" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="%%player_wmode%%"></embed>\r\n</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(71, 'nhaccuatui', '/nhaccuatui\\.com/i', 'http://www.nhaccuatui.com/mv4u/xem-clip/cjidlr07OG3N/phai-lam-the-nao-wanbi-tuan-anh.html', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="%%url_flv%%" />\r\n <param name="quality" value="high" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="allowscriptaccess" value="always" />\r\n <embed src="%%url_flv%%" allowscriptaccess="always" quality="high" wmode="%%player_wmode%%" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%">\r\n </embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(72, 'kure', '/kure\\.tv/i', 'http://www.kure.tv/otomobil/494-surucu/bmw-z4-test-surusu/151-Bolum/87652/', 0, '0', '1', '<iframe width="%%player_w%%" height="%%player_h%%" src="//www.kure.tv/VideoEmbed?ID=%%yt_id%%" hspace="0" vspace="0" scrolling="no" frameborder="0" allowfullscreen="true"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(43, 'windows media player', '/-(.*?)\\.(wmv|asf|wma)/i', 'http://www.example.com/video.wmv', 0, '0', '1', '<object id="wmv" width="%%player_w%%" height="%%player_h%%" classid="CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6" type="application/x-oleobject">\r\n<param name="URL" value="%%url_flv%%">\r\n<param name="AutoStart" value="true">\r\n<param name="ShowControls" value="true">\r\n<param name="ShowStatusBar" value="false">\r\n<param name="ShowDisplay" value="false">\r\n<param name="EnableFullScreenControls" value="true">\r\n<param name="FullScreenMode" value="true">\r\n<param name="wmode" value="%%player_wmode%%"></param> \r\n<embed type="application/x-mplayer2" src="%%url_flv%%" name="MediaPlayer"\r\nwidth="%%player_w%%" height="%%player_h%%" ShowControls="1" ShowStatusBar="0" ShowDisplay="0" AutoStart="%%player_autoplay%%" EnableFullScreenControls="1" FullScreenMode="1" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(69, 'veevr', '/veevr\\.com/i', 'http://veevr.com/videos/videoID', 0, '0', '1', '<iframe src="//veevr.com/embed/%%yt_id%%?w=%%player_w%%&h=%%player_h%%" width="%%player_w%%" height="%%player_h%%" scrolling="no" frameborder="0"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(70, '123video.nl', '/123video\\.nl/i', 'http://www.123video.nl/playvideos.asp?MovieID=1234567', 0, '0', '1', '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="//fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0" width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="//www.123video.nl/123video_emb.swf?mediaSrc=%%yt_id%%"></param>\r\n <param name="quality" value="high"></param>\r\n <param name="allowScriptAccess" value="always"></param>\r\n <param name="allowFullScreen" value="true"></param>\r\n <embed src="//www.123video.nl/123video_emb.swf?mediaSrc=%%yt_id%%" quality="high" width="%%player_w%%" height="%%player_h%%" allowfullscreen="true" type="application/x-shockwave-flash" allowscriptaccess="always" pluginspage="//www.macromedia.com/go/getflashplayer" />\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(16, 'vimeo', '/vimeo\\.com/i', 'http://vimeo.com/[VIDEO ID]', 0, '1', '1', '<iframe src="//player.vimeo.com/video/%%yt_id%%?color=20A8E1&autoplay=%%player_autoplay%%&title=0&byline=0&badge=0" width="100%" height="%%player_h%%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>', 'flvplayer', '1', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(17, 'trilulilu', '/trilulilu\\.ro/i', 'http://www.trilulilu.ro/user/[VIDEO ID]', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%"> <param name="wmode" value="%%player_wmode%%"></param> <param name="movie" value="//embed.trilulilu.ro/video/%%username%%/%%yt_id%%.swf"></param> <param name="allowFullScreen" value="true"></param> <param name="allowscriptaccess" value="always"></param> <param name="flashvars" value="username=%%username%%&hash=%%yt_id%%&color=0x%%player_bgcolor%%"></param> <embed src="//embed.trilulilu.ro/video/%%username%%/%%yt_id%%.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%" wmode="%%player_wmode%%" flashvars="username=%%username%%&hash=%%yt_id%%&color=0x%%player_bgcolor%%"></embed> </object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(18, 'bliptv', '/blip\\.tv/i', 'http://blip.tv/user/video-title-[VIDEO ID]', 0, '1', '1', '<embed src="//blip.tv/play/%%yt_id%%" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" allowscriptaccess="always" allowfullscreen="true" wmode="%%player_wmode%%"></embed> ', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(19, 'sevenload', '/sevenload\\.com/i', 'http://en.sevenload.com/videos/[VIDEO ID]-Video-title-here', 0, '1', '1', '<object type="application/x-shockwave-flash" data="//static.sevenload.com/swf/player/player.swf?configPath=http%3A%2F%2Fflash.sevenload.com%2Fplayer%3FportalId%3Den%26autoplay%3D%%player_autoplay%%%26mute%3D0%26itemId%3D%%yt_id%%&locale=en_US&autoplay=%%player_autoplay%%" width="%%player_w%%" height="%%player_h%%"> <param name="allowFullscreen" value="true" /> <param name="allowScriptAccess" value="always" /> <param name="movie" value="//static.sevenload.com/swf/player/player.swf?configPath=http%3A%2F%2Fflash.sevenload.com%2Fplayer%3FportalId%3Den%26autoplay%3D%%player_autoplay%%%26mute%3D0%26itemId%3D%%yt_id%%&locale=en_US&autoplay=%%player_autoplay%%" />', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(20, 'funnyordie', '/funnyordie\\.com/i', 'http://www.funnyordie.com/videos/[VIDEO ID]', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" id="ordie_player_%%yt_id%%">\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="movie" value="//player.ordienetworks.com/flash/fodplayer.swf" />\r\n <param name="flashvars" value="key=%%yt_id%%" />\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always"></param>\r\n <embed width="%%player_w%%" height="%%player_h%%" flashvars="key=%%yt_id%%" allowfullscreen="true" allowscriptaccess="always" quality="high" src="//player.ordienetworks.com/flash/fodplayer.swf" name="ordie_player_%%yt_id%%" type="application/x-shockwave-flash" wmode="%%player_wmode%%"></embed>\r\n</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(68, 'clip.vn', '/clip\\.vn//i', 'http://clip.vn/watch/Video-title,videoID', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="//clip.vn/w/%%yt_id%%"/>\r\n <param name="allowFullScreen" value="true"/>\r\n <param name="allowScriptAccess" value="always"/>\r\n <embed type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" width="%%player_w%%" height="%%player_h%%" src="//clip.vn/w/%%yt_id%%"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(23, 'filebox', '/filebox\\.ro/i', 'http://www.filebox.ro/video/play_video.php?key=[VIDEO ID]', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <embed type="application/x-shockwave-flash" src="//www.filebox.ro/video/FileboxPlayer_provider.php" style="" id="mediaplayer" name="mediaplayer" quality="high" allowfullscreen="true" wmode="%%player_wmode%%" flashvars="source_script=//videoserver325.filebox.ro/get_video.php&key=%%yt_id%%&autostart=%%player_autoplay%%&getLink=//fbx.ro/v/%%yt_id%%&splash=//imageserver.filebox.ro/get_splash.php?key=%%yt_id%%&link=" height="%%player_h%%" width="%%player_w%%">\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(24, 'youku', '/youku\\.com/i', 'http://v.youku.com/v_show/id_[VIDEO ID].html', 0, '0', '1', '<embed src="//player.youku.com/player.php/sid/%%yt_id%%=/v.swf" quality="high" width="%%player_w%%" height="%%player_h%%" align="middle" allowScriptAccess="sameDomain" type="application/x-shockwave-flash" wmode="%%player_wmode%%"></embed>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(67, 'tudou', '/tudou\\.com/i', 'http://www.tudou.com/programs/view/video-id/', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="//www.tudou.com/v/%%yt_id%%/v.swf"></param>\r\n <param value="true" name="allowfullscreen"></param>\r\n <param value="always" name="allowscriptaccess"></param>\r\n <param value="opaque" name="%%player_wmode%%"></param>\r\n <embed src="//www.tudou.com/v/%%yt_id%%/v.swf" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" wmode="%%player_wmode%%" width="%%player_w%%" height="%%player_h%%"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(66, 'publicdomainflicks', '/publicdomainflicks\\.com/i', 'http://www.publicdomainflicks.com/0123-video-title/', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="//www.publicdomainflicks.com/flvplayer.swf"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowFullScreen" value="true"></param>\r\n <param name="allowScriptAccess" value="always"></param>\r\n <param name="flashvars" value="file=%%url_flv%%&autostart=%%player_autoplay%%&volume=80"></param>\r\n <embed src="//www.publicdomainflicks.com/flvplayer.swf" width="%%player_w%%" height="%%player_h%%" allowscriptaccess="always" allowfullscreen="true" flashvars="file=%%url_flv%%&autostart=%%player_autoplay%%&volume=80"/>\r\n</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(30, 'liveleak', '/liveleak\\.com/i', 'http://www.liveleak.com/view?i=[VIDEO ID]', 0, '0', '1', '<iframe width="%%player_w%%" height="%%player_h%%" src="//www.liveleak.com/ll_embed?f=%%yt_id%%" frameborder="0" allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(32, 'supervideo', '/balsas\\.lt/i', 'http://video.balsas.lt/video/[VIDEO ID]', 0, '0', '1', '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="%%player_w%%" height="%%player_h%%">\r\n<param name="allowScriptAccess" value="always" />\r\n<param name="allowFullScreen" value="true" />\r\n<param name="movie" value="//video.balsas.lt/pimg/Site/Flash/player.swf?configFile=//video.balsas.lt/Videos/getConfig/%%yt_id%%" />\r\n<param name="quality" value="high" />\r\n<param name="bgcolor" value="#%%player_bgcolor%%" />\r\n<param name="flashvars" value="configFile=//video.balsas.lt/Videos/getConfig/%%yt_id%%"/>\r\n<embed src="//video.balsas.lt/pimg/Site/Flash/player.swf?configFile=//video.balsas.lt/Videos/getConfig/%%yt_id%%" quality="high" bgcolor="#%%player_bgcolor%%" width="%%player_w%%" height="%%player_h%%" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash" pluginspage="//www.macromedia.com/go/getflashplayer" /></object>\r\n', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(65, 'peteava', '/peteava\\.ro/i', 'http://www.peteava.ro/id-123456-video-title', 0, '0', '1', '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" width="%%player_w%%" height="%%player_h%%" id="swf_player_id_for_ie_who_sucks">\r\n <param name="movie" value="//www.peteava.ro/static/swf/player.swf">\r\n <param name="allowfullscreen" value="true">\r\n <param name="allowscriptaccess" value="always">\r\n <param name="menu" value="false">\r\n <param name="flashvars" value="streamer=//content.peteava.ro/stream.php&file=%%yt_id%%_standard.mp4&image=//storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerstandard&hd_file=&hd_image=//storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerhigh&autostart=%%player_autoplay%%">\r\n <embed src="//www.peteava.ro/static/swf/player.swf" id="__ptv_pl_%%yt_id%%_%%player_w%%_%%player_h%%__" name="__ptv_pl_%%yt_id%%_%%player_w%%_%%player_h%%__" width="%%player_w%%" height="%%player_h%%" allowscriptaccess="always" menu="false" allowfullscreen="true" \r\n flashvars="streamer=//content.peteava.ro/stream.php&file=%%yt_id%%_standard.mp4&image=//storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerstandard&hd_file=&hd_image=//storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerhigh&autostart=%%player_autoplay%%"/>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(35, 'musicme', '/musicme\\.com/i', 'http://www.musicme.com/#/Patrick-Bruel/videos/Epk-Patrick-Bruel-[VIDEO ID].html', 0, '0', '1', '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="%%player_w%%" height="%%player_h%%" id="musicmevideo%%yt_id%%">\r\n <param name="movie" value="//www.musicme.com/_share/vplayer.swf?cb=%%yt_id%%"></param>\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="allowScriptAccess" value="always">\r\n <param name="bgcolor" value="#000000" />\r\n <embed src="//www.musicme.com/_share/vplayer.swf?cb=%%yt_id%%" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" bgcolor="#000000" allowScriptAccess="always" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(39, 'spike', '/spike\\.com/i', 'http://www.spike.com/video/cinemassacre-top-10/[VIDEO ID]', 0, '0', '1', '<embed width="%%player_w%%" height="%%player_h%%" src="//www.spike.com/efp" quality="high" bgcolor="000000" name="efp" align="middle" type="application/x-shockwave-flash" pluginspage="//www.macromedia.com/go/getflashplayer" flashvars="flvbaseclip=%%yt_id%%" allowfullscreen="true" wmode="%%player_wmode%%">\r\n</embed> ', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(64, 'videozer', '/videozer\\.com/i', 'http://www.videozer.com/video/abcde', 0, '0', '1', '<object id="player" width="%%player_w%%" height="%%player_h%%" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">\r\n <param name="movie" value="//www.videozer.com/embed/%%yt_id%%"></param>\r\n <param name="allowFullScreen" value="true"></param>\r\n <param name="allowscriptaccess" value="always"></param>\r\n <embed src="//www.videozer.com/embed/%%yt_id%%" width="%%player_w%%" height="%%player_h%%" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(42, 'musicplayon', '/musicplayon\\.com/i', 'http://en.musicplayon.com/play?v=[VIDEO ID]Video_Title', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,15,0">\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <param name="movie" value="//en.musicplayon.com/embed?VID=%%yt_id%%&autoPlay=N&hideLeftPanel=Y&bgColor=0x232323&activeColor=0x%%player_bgcolor%%&inactiveColor=0x3C3C3C&titleColor=0x584596&textsColor=0x999999&selectedColor=0x0F0F0F&btnColor=0x000000&rnd=288950" />\r\n <param name="quality" value="high" />\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always" />\r\n <embed width="%%player_w%%" height="%%player_h%%" src="//en.musicplayon.com/embed?VID=%%yt_id%%&autoPlay=N&hideLeftPanel=Y&bgColor=0x232323&activeColor=0x%%player_bgcolor%%&inactiveColor=0x3C3C3C&titleColor=0x584596&textsColor=0x999999&selectedColor=0x0F0F0F&btnColor=0x000000&rnd=288950" quality="high" allowfullscreen="true" allowscriptaccess="always" pluginspage="//www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" wmode="%%player_wmode%%"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(44, 'quicktime', '/-(.*?)\\.(mov|m2a|m2v|3gp|3g2|m4a|m4v)/i', 'http://www.example.com/video.mov', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%" classid="clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B" codebase= "//www.apple.com/qtactivex/qtplugin.cab">\r\n <param name="src" value="%%url_flv%%" />\r\n <param name="autoplay" value="false" />\r\n <param name="controller" value="true" />\r\n <param name="scale" value="tofit" />\r\n <param name="wmode" value="%%player_wmode%%"></param>\r\n <embed src="%%url_flv%%" width="%%player_w%%" height="%%player_h%%" scale="tofit" wmode="%%player_wmode%%" autoplay="false" controller="true" type="video/quicktime" pluginspage="//www.apple.com/quicktime/download/"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(45, 'yahoomusic', '/music\\.yahoo\\.com/i', 'http://new.music.yahoo.com/videos/LadyGaGa/Bad-Romance--218606963', 0, '0', '1', '<object width="%%player_w%%" id="uvp_fop" height="%%player_h%%" allowFullScreen="true">\r\n <param name="movie" value="//d.yimg.com/m/up/fop/embedflv/swf/fop.swf"/>\r\n <param name="flashVars" value="%%url_flv%%"/>\r\n <param name="wmode" value="%%player_wmode%%"/>\r\n <embed width="%%player_w%%" id="uvp_fop" height="%%player_h%%" allowFullScreen="true" src="//d.yimg.com/m/up/fop/embedflv/swf/fop.swf" type="application/x-shockwave-flash" flashvars="%%url_flv%%" />\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(47, '5min', '/5min\\.com\\/video/i', 'http://www.5min.com/Video/Video-Title-[VIDEO ID]', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%" id="FiveminPlayer" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">\r\n <param name="allowfullscreen" value="true"/>\r\n <param name="allowScriptAccess" value="always"/>\r\n <param name="movie" value="//www.5min.com/Embeded/%%yt_id%%/"/>\r\n <embed name="FiveminPlayer" src="//www.5min.com/Embeded/%%yt_id%%/" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" allowfullscreen="true" allowScriptAccess="always"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(63, 'vplay', '/vplay\\.ro/i', 'http://vplay.ro/watch/abcdef/', 0, '0', '1', '<object classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0" width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="//i.vplay.ro/f/embed.swf?key=%%yt_id%%">\r\n <param name="allowfullscreen" value="true">\r\n <param name="quality" value="high">\r\n <embed src="//i.vplay.ro/f/embed.swf?key=%%yt_id%%" quality="high" pluginspage="//www.macromedia.com/go/getflashplayer" type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" allowfullscreen="true" ></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(51, 'smotri', '/smotri\\.com\\/video/i', 'http://smotri.com/video/view/?id=[VIDEO ID]', 0, '0', '1', '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="//pics.smotri.com/scrubber_custom8.swf?file=%%yt_id%%&bufferTime=3&autoStart=false&str_lang=eng&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color_black.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" />\r\n <param name="allowScriptAccess" value="always" />\r\n <param name="allowFullScreen" value="true" />\r\n <embed src="//pics.smotri.com/scrubber_custom8.swf?file=%%yt_id%%&bufferTime=3&autoStart=false&str_lang=eng&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color_black.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml" quality="high" allowscriptaccess="always" allowfullscreen="true" wmode="window" width="%%player_w%%" height="%%player_h%%" type="application/x-shockwave-flash"></embed>\r\n </object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(52, 'divx', '/(.*?)\\.(avi|divx|mkv)/i', 'http://www.example.com/video.avi', 0, '0', '1', '<object classid="clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616" width="%%player_w%%" height="%%player_h%%" codebase="//go.divx.com/plugin/DivXBrowserPlugin.cab"> \n<param name="src" value="%%url_flv%%" />\n<param name="autoPlay" value="%%player_autoplay%%" />\n<param name="bannerEnabled" value="false" />\n<param name="previewImage" value="%%yt_thumb%%" />\n<embed type="video/divx" src="%%url_flv%%" autoPlay="%%player_autoplay%%" previewImage="%%yt_thumb%%" bannerEnabled="false" width="%%player_w%%" height="%%player_h%%" pluginspage="//go.divx.com/plugin/download/"></embed> \n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(53, 'vbox7', '/vbox7\\.com\\/play/i', 'http://vbox7.com/play:[VIDEO ID]', 0, '1', '1', '<iframe width="%%player_w%%" height="%%player_h%%" src="//vbox7.com/emb/external.php?vid=%%yt_id%%" frameborder="0" allowfullscreen></iframe>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(77, 'cloudy.ec', '/cloudy\\.ec/i', 'http://www.cloudy.ec/v/[VIDEO ID]', 0, '0', '1', '<iframe width="%%player_w%%" height="%%player_h%%" src="//www.cloudy.ec/embed.php?id=%%yt_id%%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(54, 'livestream', '/livestream\\.com/i', 'http://www.livestream.com/channel_name', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%" id="lsplayer" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000"><param name="movie" value="%%url_flv%%&amp;autoPlay=false"></param><param name="allowScriptAccess" value="always"></param><param name="allowFullScreen" value="true"></param><embed name="lsplayer" src="%%url_flv%%&amp;autoPlay=false" width="%%player_w%%" height="%%player_h%%" allowScriptAccess="always" allowFullScreen="true" type="application/x-shockwave-flash"></embed></object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(56, 'ustream', '/ustream\\.tv/i', 'http://www.ustream.tv/channel/user', 0, '0', '1', '<object type="application/x-shockwave-flash" width="%%player_w%%" height="%%player_h%%" data="//www.ustream.tv/flash/viewer.swf">\r\n <param name="flashvars" value="autoplay=true&amp;%%yt_id%%&amp;v3=true&amp;locale=en_US&amp;referrer=unknown&amp;enablejsapi=true"/>\r\n <param name="allowfullscreen" value="true"/>\r\n <param name="allowscriptaccess" value="always"/>\r\n <param name="movie" value="%%url_flv%%"/>\r\n <embed flashvars="autoplay=true&amp;%%yt_id%%&amp;v3=true&amp;locale=en_US&amp;referrer=unknown&amp;enablejsapi=true" src="//www.ustream.tv/flash/viewer.swf" width="%%player_w%%" height="%%player_h%%" allowfullscreen="true" allowscriptaccess="always" type="application/x-shockwave-flash" />\r\n </object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(57, 'mp3', '/(.*?)\\.mp3/i', 'http://www.example.com/file.mp3', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%" classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0">\r\n <param name="scale" value="noscale" />\r\n <param name="allowFullScreen" value="true" />\r\n <param name="allowScriptAccess" value="always" />\r\n <param name="allowNetworking" value="all" />\r\n <param name="bgcolor" value="#%%player_bgcolor%%" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="movie" value="%%player_url%%" />\r\n <param name="flashVars" value="&plugins=revolt-1&file=%%url_flv%%&type=sound&image=%%yt_thumb%%&backcolor=%%player_bgcolor%%&frontcolor=FFFFFF&autostart=%%player_autoplay%%&screencolor=000000" />\r\n <embed src="%%player_url%%" width="%%player_w%%" height="%%player_h%%" scale="noscale" bgcolor="#%%player_bgcolor%%" type="application/x-shockwave-flash" allowFullScreen="true" allowScriptAccess="always" wmode="%%player_wmode%%" flashvars="&plugins=revolt-1&file=%%url_flv%%&type=sound&image=%%yt_thumb%%&backcolor=%%player_bgcolor%%&frontcolor=FFFFFF&autostart=%%player_autoplay%%&screencolor=000000"></embed>\r\n</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(58, 'mynet', '/video\\.mynet\\.com/i', 'http://video.mynet.com/username/video-title/video-id/', 0, '1', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always" />\r\n <param name="autoplay" value="%%player_autoplay%%" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="movie" value="//video.mynet.com/username/video-title/%%yt_id%%.swf" />\r\n <embed src="//video.mynet.com/username/video-title/%%yt_id%%.swf" type="application/x-shockwave-flash" wmode="%%player_wmode%%" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%" autoplay="%%player_autoplay%%"></embed>\r\n</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(59, 'vidivodo', '/vidivodo\\.com/i', 'http://www.vidivodo.com/video-id/video-title', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="movie" value="%%url_flv%%" />\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always" />\r\n <param name="autoplay" value="%%player_autoplay%%" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="bgcolor" value="#%%player_bgcolor%%" />\r\n <embed src="%%url_flv%%" type="application/x-shockwave-flash" wmode="%%player_wmode%%" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%" autoplay="%%player_autoplay%%" bgcolor="#%%player_bgcolor%%"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(61, 'izlesene', '/izlesene\\.com/i', 'http://www.izlesene.com/video/video-title/video-id', 0, '0', '1', '<object width="%%player_w%%" height="%%player_h%%">\r\n <param name="allowfullscreen" value="true" />\r\n <param name="allowscriptaccess" value="always" />\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <param name="bgcolor" value="#%%player_bgcolor%%" />\r\n <param name="movie" value="//www.izlesene.com/embedplayer.swf?video=%%yt_id%%" />\r\n <embed src="//www.izlesene.com/embedplayer.swf?video=%%yt_id%%" wmode="%%player_wmode%%" bgcolor="#%%player_bgcolor%%" allowfullscreen="true" allowscriptaccess="always" menu="false" width="%%player_w%%" height="%%player_h%%" type="application/x-shockwave-flash"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(62, 'videobb', '/videobb\\./i', 'http://www.videobb.com/video/video-id', 0, '0', '1', '<object id="player" width="%%player_w%%" height="%%player_h%%" classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000">\r\n <param name="movie" value="%%url_flv%%"></param>\r\n <param name="allowFullScreen" value="true" ></param>\r\n <param name="allowscriptaccess" value="always"></param>\r\n <param name="wmode" value="%%player_wmode%%" />\r\n <embed src="%%url_flv%%" wmode="%%player_wmode%%" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="%%player_w%%" height="%%player_h%%"></embed>\r\n</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(73, 'mail.ru', '/mail\\.ru\\/video/i', 'http://my.mail.ru/video/mail/radnovomyznakomstvy/176/254.html', 0, '0', '1', '<iframe src="//api.video.mail.ru/videos/embed/%%yt_id%%" width="%%player_w%%" height="%%player_h%%" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(74, 'vk', '/vk\\.(com|ru|me)\\/video/i', 'http://vk.com/video28908630_165233143', 0, '0', '1', '<iframe src="//vk.com/video_ext.php?%%yt_id%%" width="%%player_w%%" height="%%player_h%%" frameborder="0"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(75, 'rutube', '/rutube\\.ru\\/video/i', 'http://rutube.ru/video/852e974534e3527f16810a7a19c418b0/', 0, '0', '1', '<iframe width="%%player_w%%" height="%%player_h%%" src="//rutube.ru/video/embed/%%yt_id%%" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(76, 'novamov', '/novamov\\.com/i', 'http://www.novamov.com/video/video-id', 0, '0', '1', '<iframe style="overflow: hidden; border: 0; width: %%player_w%%px; height: %%player_h%%px;" src="//embed.novamov.com/embed.php?v=%%yt_id%%" scrolling="no"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(78, 'myvideo.ge', '/myvideo\\.ge/i', 'http://www.myvideo.ge/?video_id=[VIDEO ID]', 0, '0', '1', '<iframe width="%%player_w%%" height="%%player_h%%" src="//embed.myvideo.ge/flv_player/player.php?video_id=%%yt_id%%" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(79, 'vevo', '/vevo\\.com/i', 'http://www.vevo.com/watch/[VIDEO ID]', 0, '0', '1', '<iframe width="%%player_w%%" height="%%player_h%%" src="//cache.vevo.com/assets/html/embed.html?video=%%yt_id%%&autoplay=0" frameborder="0" border="0" marginwidth="0" marginheight="0" scrolling="no" allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(80, 'facebook', '/facebook.com/i', 'https://www.facebook.com/video.php?v=video-id', 0, '0', '1', '<iframe src="//www.facebook.com/video/embed?video_id=%%yt_id%%" width="%%player_w%%" height="%%player_h%%" frameborder="0"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(81, 'imgur', '/(.*?)\\imgur\\.com/i', 'http://imgur.com/[ID]', 0, '1', '1', '<blockquote class="imgur-embed-pub" lang="en" data-id="%%yt_id%%" style="width:%%player_w%%px !important;max-width:%%player_w%%px !important; height:%%player_h%%px"></blockquote><script async src="//s.imgur.com/min/embed.js" charset="utf-8"></script>', 'embed', '0', '0');
INSERT INTO `pm_sources` (`source_id`, `source_name`, `source_rule`, `url_example`, `last_check`, `flv_player_support`, `embed_player_support`, `embed_code`, `user_choice`, `vscheck_support`, `vscheck_autopilot`) VALUES(82, 'gfycat', '/gfycat\\.com/i', 'http://gfycat.com/[ID]', 0, '1', '1', '<iframe src="//gfycat.com/ifr/%%yt_id%%" frameborder="0" scrolling="no" width="%%player_w%%" height="%%player_h%%" style="-webkit-backface-visibility: hidden;-webkit-transform: scale(1);" ></iframe>', 'flvplayer', '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `pm_tags`
--

DROP TABLE IF EXISTS `pm_tags`;
CREATE TABLE `pm_tags` (
  `tag_id` int(7) NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `tag` varchar(200) NOT NULL DEFAULT '',
  `safe_tag` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`tag_id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `safe_tag` (`safe_tag`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_tags`
--

INSERT INTO `pm_tags` (`tag_id`, `uniq_id`, `tag`, `safe_tag`) VALUES(1, '14a4e06f8', 'hudson', 'hudson');
INSERT INTO `pm_tags` (`tag_id`, `uniq_id`, `tag`, `safe_tag`) VALUES(2, '14a4e06f8', 'video', 'video');
INSERT INTO `pm_tags` (`tag_id`, `uniq_id`, `tag`, `safe_tag`) VALUES(3, '14a4e06f8', 'against the grain', 'against-the-grain');
INSERT INTO `pm_tags` (`tag_id`, `uniq_id`, `tag`, `safe_tag`) VALUES(4, 'ac0266df0', 'animation', 'animation');
INSERT INTO `pm_tags` (`tag_id`, `uniq_id`, `tag`, `safe_tag`) VALUES(5, 'ac0266df0', 'stop-motion', 'stop-motion');

-- --------------------------------------------------------

--
-- Table structure for table `pm_temp`
--

DROP TABLE IF EXISTS `pm_temp`;
CREATE TABLE `pm_temp` (
  `id` int(5) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) NOT NULL DEFAULT '',
  `video_title` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `yt_length` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `tags` varchar(255) NOT NULL DEFAULT '',
  `category` varchar(30) NOT NULL DEFAULT '',
  `username` varchar(100) NOT NULL DEFAULT '',
  `user_id` int(5) NOT NULL DEFAULT '0',
  `added` int(10) NOT NULL DEFAULT '0',
  `source_id` mediumint(3) NOT NULL DEFAULT '0',
  `language` mediumint(3) NOT NULL DEFAULT '0',
  `thumbnail` varchar(255) NOT NULL DEFAULT '',
  `yt_id` varchar(50) NOT NULL DEFAULT '',
  `url_flv` varchar(255) NOT NULL DEFAULT '',
  `mp4` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_temp`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_users`
--

DROP TABLE IF EXISTS `pm_users`;
CREATE TABLE `pm_users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `username` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `name` varchar(150) NOT NULL DEFAULT '',
  `gender` varchar(10) NOT NULL DEFAULT '',
  `country` varchar(50) NOT NULL DEFAULT '',
  `reg_ip` varchar(40) NOT NULL DEFAULT '',
  `reg_date` int(10) unsigned NOT NULL DEFAULT '0',
  `last_signin` int(10) unsigned NOT NULL DEFAULT '0',
  `last_signin_ip` varchar(40) NOT NULL DEFAULT '',
  `email` varchar(150) NOT NULL DEFAULT '',
  `favorite` enum('0','1') NOT NULL DEFAULT '1',
  `power` enum('0','1','2','3','4') NOT NULL DEFAULT '0',
  `about` text NOT NULL,
  `avatar` varchar(255) NOT NULL DEFAULT 'default.gif',
  `activation_key` varchar(20) NOT NULL DEFAULT '',
  `new_password` varchar(32) NOT NULL DEFAULT '',
  `followers_count` int(10) unsigned NOT NULL DEFAULT '0',
  `following_count` int(10) unsigned NOT NULL DEFAULT '0',
  `unread_notifications_count` int(10) unsigned NOT NULL DEFAULT '0',
  `social_links` text NOT NULL,
  `channel_slug` varchar(255) NOT NULL DEFAULT '',
  `channel_cover` varchar(255) NOT NULL DEFAULT '',
  `channel_verified` enum('0','1') NOT NULL DEFAULT '0',
  `channel_featured` enum('0','1') NOT NULL DEFAULT '0',
  `channel_settings` text NOT NULL,
  `fb_user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `fb_access_token` varchar(255) NOT NULL DEFAULT '',
  `twitter_user_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `email` (`email`),
  KEY `channel_slug` (`channel_slug`),
  KEY `channel_featured` (`channel_featured`),
  KEY `fb_user_id` (`fb_user_id`),
  KEY `twitter_user_id` (`twitter_user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_users`
--

INSERT INTO `pm_users` (`id`, `username`, `password`, `name`, `gender`, `country`, `reg_ip`, `reg_date`, `last_signin`, `last_signin_ip`, `email`, `favorite`, `power`, `about`, `avatar`, `activation_key`, `new_password`, `followers_count`, `following_count`, `unread_notifications_count`, `social_links`, `channel_slug`, `channel_cover`, `channel_verified`, `channel_featured`, `channel_settings`, `fb_user_id`, `fb_access_token`, `twitter_user_id`) VALUES(1, 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', 'male', '500', '127.0.0.1', 1463767235, 1463767235, '', 'admin@example.com', '1', '1', '', 'default.gif', '', '', 0, 0, 0, 'a:6:{s:7:"website";s:0:"";s:7:"youtube";s:0:"";s:8:"facebook";s:0:"";s:7:"twitter";s:0:"";s:9:"instagram";s:0:"";s:11:"google_plus";s:0:"";}', '', '', '1', '1', '', 0, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `pm_users_follow`
--

DROP TABLE IF EXISTS `pm_users_follow`;
CREATE TABLE `pm_users_follow` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `follower_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`follower_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_users_follow`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_videoads`
--

DROP TABLE IF EXISTS `pm_videoads`;
CREATE TABLE `pm_videoads` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `hash` varchar(12) NOT NULL DEFAULT '',
  `name` varchar(50) NOT NULL DEFAULT '',
  `flv_url` varchar(255) NOT NULL DEFAULT '',
  `redirect_url` text NOT NULL,
  `redirect_type` enum('0','1') NOT NULL DEFAULT '0',
  `status` enum('0','1') NOT NULL DEFAULT '0',
  `disable_stats` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `hash` (`hash`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_videoads`
--


-- --------------------------------------------------------

--
-- Table structure for table `pm_videos`
--

DROP TABLE IF EXISTS `pm_videos`;
CREATE TABLE `pm_videos` (
  `id` mediumint(6) unsigned NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `video_title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `yt_id` varchar(50) NOT NULL DEFAULT '',
  `yt_length` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `yt_thumb` varchar(255) NOT NULL DEFAULT '',
  `yt_views` int(10) NOT NULL DEFAULT '0',
  `category` varchar(30) NOT NULL DEFAULT '',
  `submitted_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `submitted` varchar(100) NOT NULL DEFAULT '',
  `lastwatched` int(10) unsigned NOT NULL DEFAULT '0',
  `added` int(10) unsigned NOT NULL DEFAULT '0',
  `site_views` int(9) NOT NULL DEFAULT '0',
  `url_flv` varchar(255) NOT NULL DEFAULT '',
  `source_id` smallint(2) unsigned NOT NULL DEFAULT '0',
  `language` smallint(2) unsigned NOT NULL DEFAULT '0',
  `age_verification` enum('0','1') NOT NULL DEFAULT '0',
  `last_check` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `featured` enum('0','1') NOT NULL DEFAULT '0',
  `restricted` enum('0','1') NOT NULL DEFAULT '0',
  `allow_comments` enum('0','1') NOT NULL DEFAULT '1',
  `allow_embedding` enum('0','1') NOT NULL DEFAULT '1',
  `video_slug` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `added` (`added`),
  KEY `yt_id` (`yt_id`),
  KEY `featured` (`featured`),
  KEY `submitted_user_id` (`submitted_user_id`),
  FULLTEXT KEY `fulltext_index` (`video_title`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_videos`
--

INSERT INTO `pm_videos` (`id`, `uniq_id`, `video_title`, `description`, `yt_id`, `yt_length`, `yt_thumb`, `yt_views`, `category`, `submitted_user_id`, `submitted`, `lastwatched`, `added`, `site_views`, `url_flv`, `source_id`, `language`, `age_verification`, `last_check`, `status`, `featured`, `restricted`, `allow_comments`, `allow_embedding`, `video_slug`) VALUES(1, '14a4e06f8', 'Hudson - Against The Grain', '<p>I''m asking questions time and time again<br />in a race that never ever ends<br />hanging from my limbs in the swaying breeze<br />im opening I gotta let it go<br /><br />in my life the good and bad they come and go<br />highs and lows are often all that show<br />know it''s time to go against the grain<br />or it will kill me down below<br />to live in comfort and know its warm embrace<br />reminds me only to quicken up the pace<br />know it''s time to go against the grain<br />or it will kill me down below<br /><br />singing a song never heard<br />all of a sudden I know every word<br />And I know that there''s no direction home<br />it''s right here where all the wild things grow<br /><br />in my life the good and bad they come and go<br />highs and lows are often all that show<br />know it''s time to go against the grain<br />or it will kill me down below<br />to live in comfort and know its warm embrace<br />reminds me only to quicken up the pace<br />know it''s time to go against the grain<br />or it will kill me down below<br /><br />The new music video for ''Against The Grain'' from emerging Melbourne indie-folk artist Hudson sees him collaborate with film-maker/animator/VJ Dropbear (aka Jonathan Chong), producing a vibrant and colourful clip based around a mainstay from our humble artistic efforts throughout childhood -- coloured pencils.<br /><br />''Against The Grain'' is the first single lifted off Hudson''s debut EP Open Up Slowly released in May 2011</p>', 'TuBMXS6vU3o', 204, '//i.ytimg.com/vi/TuBMXS6vU3o/0.jpg', 0, '3', 1, 'admin', 1353582628, 1447269481, 5, '//www.youtube.com/watch?v=TuBMXS6vU3o', 3, 1, '0', 0, 0, '1', '0', '1', '1', 'hudson-against-the-grain');
INSERT INTO `pm_videos` (`id`, `uniq_id`, `video_title`, `description`, `yt_id`, `yt_length`, `yt_thumb`, `yt_views`, `category`, `submitted_user_id`, `submitted`, `lastwatched`, `added`, `site_views`, `url_flv`, `source_id`, `language`, `age_verification`, `last_check`, `status`, `featured`, `restricted`, `allow_comments`, `allow_embedding`, `video_slug`) VALUES(2, 'ac0266df0', 'Post-it Stop Motion', '', 'BpWM0FNPZSs', 115, '//i.ytimg.com/vi/BpWM0FNPZSs/0.jpg', 0, '1,2', 1, 'admin', 0, 1447269481, 1, '//www.youtube.com/watch?v=BpWM0FNPZSs', 3, 1, '0', 0, 0, '0', '0', '1', '1', '');

-- --------------------------------------------------------


--
-- Table structure for table `pm_videos_trash`
--

DROP TABLE IF EXISTS `pm_videos_trash`;
CREATE TABLE `pm_videos_trash` (
  `id` mediumint(6) unsigned NOT NULL,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `video_title` varchar(100) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `yt_id` varchar(50) NOT NULL DEFAULT '',
  `yt_length` mediumint(5) unsigned NOT NULL DEFAULT '0',
  `yt_thumb` varchar(255) NOT NULL DEFAULT '',
  `yt_views` int(10) NOT NULL DEFAULT '0',
  `category` varchar(30) NOT NULL DEFAULT '',
  `submitted_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `submitted` varchar(100) NOT NULL DEFAULT '',
  `lastwatched` int(10) unsigned NOT NULL DEFAULT '0',
  `added` int(10) unsigned NOT NULL DEFAULT '0',
  `site_views` int(9) NOT NULL DEFAULT '0',
  `url_flv` varchar(255) NOT NULL DEFAULT '',
  `source_id` smallint(2) unsigned NOT NULL DEFAULT '0',
  `language` smallint(2) unsigned NOT NULL DEFAULT '0',
  `age_verification` enum('0','1') NOT NULL DEFAULT '0',
  `last_check` int(10) unsigned NOT NULL DEFAULT '0',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `featured` enum('0','1') NOT NULL DEFAULT '0',
  `restricted` enum('0','1') NOT NULL DEFAULT '0',
  `allow_comments` enum('0','1') NOT NULL DEFAULT '1',
  `allow_embedding` enum('0','1') NOT NULL DEFAULT '1',
  `video_slug` varchar(255) NOT NULL  DEFAULT '',
  `mp4` varchar(255) NOT NULL  DEFAULT '',
  `direct` varchar(255) NOT NULL  DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniq_id` (`uniq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `pm_videos_urls`
--

DROP TABLE IF EXISTS `pm_videos_urls`;
CREATE TABLE `pm_videos_urls` (
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `mp4` varchar(200) NOT NULL DEFAULT '',
  `direct` varchar(200) NOT NULL DEFAULT '',
  UNIQUE KEY `uniq_id` (`uniq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `pm_videos_urls`
--

INSERT INTO `pm_videos_urls` (`uniq_id`, `mp4`, `direct`) VALUES('14a4e06f8', '', 'http://www.youtube.com/watch?v=TuBMXS6vU3o');
INSERT INTO `pm_videos_urls` (`uniq_id`, `mp4`, `direct`) VALUES('ac0266df0', '', 'http://www.youtube.com/watch?v=BpWM0FNPZSs');

-- --------------------------------------------------------

--
-- Table structure for table `pm_video_subtitles`
--

DROP TABLE IF EXISTS `pm_video_subtitles`;
CREATE TABLE `pm_video_subtitles` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL  DEFAULT '',
  `language` varchar(100) NOT NULL  DEFAULT '',
  `language_tag` varchar(2) NOT NULL  DEFAULT '',
  `filename` varchar(255) NOT NULL  DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniq_id` (`uniq_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------
INSERT INTO pm_internal_log (log_date, log_info) VALUES (NOW(), 'Installed');
UPDATE `pm_config` SET value = UNIX_TIMESTAMP() WHERE name = 'last_autosync';
UPDATE `pm_config` SET `value` = UNIX_TIMESTAMP() WHERE `name` = 'chart_last_reset';
UPDATE `pm_config` SET `value` = UNIX_TIMESTAMP() WHERE `name` = 'pm_notifications_last_prune';
UPDATE `pm_config` SET `value` = UNIX_TIMESTAMP() WHERE `name` = 'firstinstall';
UPDATE `pm_videos` SET `added` = UNIX_TIMESTAMP();
UPDATE `art_articles` SET `date` = UNIX_TIMESTAMP();
UPDATE `pm_comments` SET `added` = UNIX_TIMESTAMP();
UPDATE `pm_bin_rating_votes` SET `date` = UNIX_TIMESTAMP();
UPDATE `pm_users` SET `reg_date` = UNIX_TIMESTAMP(), `last_signin` = UNIX_TIMESTAMP() WHERE `username` = 'admin';
UPDATE `pm_cron_jobs` SET `created_time` = UNIX_TIMESTAMP();
UPDATE `pm_playlists` SET `date` = UNIX_TIMESTAMP();