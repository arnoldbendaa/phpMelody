#
# Table structure for table `art_articles`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `art_articles`
#



#
# Table structure for table `art_categories`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `art_categories`
#



#
# Table structure for table `art_tags`
#

DROP TABLE IF EXISTS `art_tags`;
CREATE TABLE `art_tags` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `article_id` int(6) NOT NULL DEFAULT '0',
  `tag` varchar(255) NOT NULL DEFAULT '',
  `safe_tag` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

#
# Dumping data for table `art_tags`
#



#
# Table structure for table `pm_activity`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_activity`
#



#
# Table structure for table `pm_ads`
#

DROP TABLE IF EXISTS `pm_ads`;
CREATE TABLE `pm_ads` (
  `id` smallint(4) NOT NULL AUTO_INCREMENT,
  `position` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `code` text NOT NULL,
  `active` enum('0','1') NOT NULL DEFAULT '1',
  `disable_stats` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=12;

#
# Dumping data for table `pm_ads`
#

INSERT INTO `pm_ads` VALUES ('1', 'Header', 'Appears on all pages right under the horizontal menu', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('2', 'Footer', 'Appears on all pages right before the footer', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('3', 'Video Player', 'Appears on video pages under the video player. (Recommended max. width: 540px)', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('4', 'Article Page', 'Appears at the end of all articles (Recommended max. width: 540px)', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('5', 'Index page', 'Appears as the first widget block on the right site of your homepage (Recommended max. width: 250px)', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('6', 'Floating Skyscraper (Left)', 'Appears on the left side of the page container', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('7', 'Floating Skyscraper (Right)', 'Appears on the right side of the page container', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('8', 'mobile_header', 'Appears in the header of Mobile Melody (requires <a href=\"http://www.phpsugar.com/order.php?id=mobile\" target=\"_blank\">Mobile Melody</a>)', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('9', 'mobile_footer', 'Appears in the footer of Mobile Melody (requires <a href=\"http://www.phpsugar.com/order.php?id=mobile\" target=\"_blank\">Mobile Melody</a>)', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('10', 'mobile_video', 'Appears on the video pages of Mobile Melody (requires <a href=\"http://www.phpsugar.com/order.php?id=mobile\" target=\"_blank\">Mobile Melody</a>)', '', '0', '0');
INSERT INTO `pm_ads` VALUES ('11', 'mobile_article', 'Appears on the article pages of Mobile Melody (requires <a href=\"http://www.phpsugar.com/order.php?id=mobile\" target=\"_blank\">Mobile Melody</a>)', '', '0', '0');


#
# Table structure for table `pm_ads_log`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_ads_log`
#



#
# Table structure for table `pm_banlist`
#

DROP TABLE IF EXISTS `pm_banlist`;
CREATE TABLE `pm_banlist` (
  `id` int(6) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(6) unsigned NOT NULL DEFAULT '0',
  `reason` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_banlist`
#



#
# Table structure for table `pm_bin_rating_meta`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=8;

#
# Dumping data for table `pm_bin_rating_meta`
#

INSERT INTO `pm_bin_rating_meta` VALUES ('7', '14a4e06f8', '1', '0', '1');


#
# Table structure for table `pm_bin_rating_votes`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=2;

#
# Dumping data for table `pm_bin_rating_votes`
#

INSERT INTO `pm_bin_rating_votes` VALUES ('1', '14a4e06f8', '1', '127.0.0.1', '1', '1524757198');


#
# Table structure for table `pm_categories`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=5;

#
# Dumping data for table `pm_categories`
#

INSERT INTO `pm_categories` VALUES ('1', '0', 'categoryone', 'Sample Category #1', '1', '1', '1', '', '', '');
INSERT INTO `pm_categories` VALUES ('2', '1', 'subcat', 'Sample Sub-cat', '2', '2', '1', '', '', '');
INSERT INTO `pm_categories` VALUES ('3', '0', 'categorytwo', 'Sample Category #2', '1', '1', '2', '', '', '');
INSERT INTO `pm_categories` VALUES ('4', '0', 'test', 'test', '0', '0', '3', '', '', '');


#
# Table structure for table `pm_chart`
#

DROP TABLE IF EXISTS `pm_chart`;
CREATE TABLE `pm_chart` (
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `views` int(9) unsigned NOT NULL DEFAULT '0',
  `views_this` int(6) NOT NULL DEFAULT '0',
  `views_last` int(6) NOT NULL DEFAULT '0',
  `views_seclast` int(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`uniq_id`),
  KEY `views` (`views`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_chart`
#

INSERT INTO `pm_chart` VALUES ('14a4e06f8', '4', '4', '0', '0');
INSERT INTO `pm_chart` VALUES ('ac0266df0', '2', '2', '0', '0');
INSERT INTO `pm_chart` VALUES ('9ce8ca852', '2', '2', '0', '0');


#
# Table structure for table `pm_comments`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=2;

#
# Dumping data for table `pm_comments`
#

INSERT INTO `pm_comments` VALUES ('1', 'ac0266df0', 'admin', 'Lovely!', '1524757198', '127.0.0.1', '1', '1', '0', '0', '0', '0');


#
# Table structure for table `pm_comments_reported`
#

DROP TABLE IF EXISTS `pm_comments_reported`;
CREATE TABLE `pm_comments_reported` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `comment_id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`,`comment_id`),
  KEY `user_id_2` (`user_id`),
  KEY `comment_id` (`comment_id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_comments_reported`
#



#
# Table structure for table `pm_config`
#

DROP TABLE IF EXISTS `pm_config`;
CREATE TABLE `pm_config` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=151;

#
# Dumping data for table `pm_config`
#

INSERT INTO `pm_config` VALUES ('1', 'contact_mail', 'noreply@domain.com');
INSERT INTO `pm_config` VALUES ('2', 'thumb_from', '2');
INSERT INTO `pm_config` VALUES ('3', 'browse_page', '16');
INSERT INTO `pm_config` VALUES ('4', 'browse_articles', '5');
INSERT INTO `pm_config` VALUES ('5', 'player_w', '638');
INSERT INTO `pm_config` VALUES ('6', 'player_h', '401');
INSERT INTO `pm_config` VALUES ('7', 'player_w_index', '638');
INSERT INTO `pm_config` VALUES ('8', 'player_h_index', '344');
INSERT INTO `pm_config` VALUES ('9', 'player_w_favs', '575');
INSERT INTO `pm_config` VALUES ('10', 'player_h_favs', '466');
INSERT INTO `pm_config` VALUES ('11', 'player_w_embed', '640');
INSERT INTO `pm_config` VALUES ('12', 'player_h_embed', '360');
INSERT INTO `pm_config` VALUES ('13', 'isnew_days', '7');
INSERT INTO `pm_config` VALUES ('14', 'ispopular', '100');
INSERT INTO `pm_config` VALUES ('15', 'stopbadcomments', '1');
INSERT INTO `pm_config` VALUES ('16', 'comments_page', '10');
INSERT INTO `pm_config` VALUES ('17', 'template_f', 'default');
INSERT INTO `pm_config` VALUES ('18', 'firstinstall', '1524757198');
INSERT INTO `pm_config` VALUES ('19', 'counterhtml', '');
INSERT INTO `pm_config` VALUES ('20', 'voth_cat', '0');
INSERT INTO `pm_config` VALUES ('21', 'views_from', '2');
INSERT INTO `pm_config` VALUES ('22', 'fav_limit', '20');
INSERT INTO `pm_config` VALUES ('23', 'version', '2.7.3');
INSERT INTO `pm_config` VALUES ('24', 'seomod', '0');
INSERT INTO `pm_config` VALUES ('25', 'new_videos', '24');
INSERT INTO `pm_config` VALUES ('26', 'top_videos', '10');
INSERT INTO `pm_config` VALUES ('27', 'chart_days', '7');
INSERT INTO `pm_config` VALUES ('28', 'chart_last_reset', '1524757198');
INSERT INTO `pm_config` VALUES ('29', 'guests_can_comment', '1');
INSERT INTO `pm_config` VALUES ('30', 'comm_moderation_level', '0');
INSERT INTO `pm_config` VALUES ('31', 'show_tags', '0');
INSERT INTO `pm_config` VALUES ('32', 'shuffle_tags', '0');
INSERT INTO `pm_config` VALUES ('33', 'tag_cloud_limit', '20');
INSERT INTO `pm_config` VALUES ('34', 'show_stats', '0');
INSERT INTO `pm_config` VALUES ('35', 'account_activation', '0');
INSERT INTO `pm_config` VALUES ('36', 'issmtp', '1');
INSERT INTO `pm_config` VALUES ('37', 'player_timecolor', '545454');
INSERT INTO `pm_config` VALUES ('38', 'player_bgcolor', '5e5e5e');
INSERT INTO `pm_config` VALUES ('39', 'player_autoplay', '0');
INSERT INTO `pm_config` VALUES ('40', 'player_autobuff', '0');
INSERT INTO `pm_config` VALUES ('41', 'player_watermarkurl', '');
INSERT INTO `pm_config` VALUES ('42', 'player_watermarklink', '');
INSERT INTO `pm_config` VALUES ('43', 'player_watermarkshow', 'fullscreen');
INSERT INTO `pm_config` VALUES ('44', 'search_suggest', '1');
INSERT INTO `pm_config` VALUES ('45', 'use_hq_vids', '1');
INSERT INTO `pm_config` VALUES ('46', 'total_videoads', '0');
INSERT INTO `pm_config` VALUES ('47', 'videoads_delay', '20');
INSERT INTO `pm_config` VALUES ('48', 'default_lang', '1');
INSERT INTO `pm_config` VALUES ('49', 'last_video', '');
INSERT INTO `pm_config` VALUES ('50', 'top_videos_sort', 'views');
INSERT INTO `pm_config` VALUES ('51', 'video_player', 'videojs');
INSERT INTO `pm_config` VALUES ('52', 'gzip', '0');
INSERT INTO `pm_config` VALUES ('53', 'mod_article', '0');
INSERT INTO `pm_config` VALUES ('54', 'mail_server', 'mail.domain.com');
INSERT INTO `pm_config` VALUES ('55', 'mail_port', '25');
INSERT INTO `pm_config` VALUES ('56', 'mail_user', 'noreply+domain.com');
INSERT INTO `pm_config` VALUES ('57', 'mail_pass', 'domain');
INSERT INTO `pm_config` VALUES ('58', 'show_ads', '0');
INSERT INTO `pm_config` VALUES ('59', 'total_videos', '3');
INSERT INTO `pm_config` VALUES ('60', 'total_articles', '0');
INSERT INTO `pm_config` VALUES ('61', 'total_pages', '3');
INSERT INTO `pm_config` VALUES ('62', 'homepage_title', 'PHP Melody');
INSERT INTO `pm_config` VALUES ('63', 'homepage_description', '');
INSERT INTO `pm_config` VALUES ('64', 'homepage_keywords', '');
INSERT INTO `pm_config` VALUES ('65', 'moderator_can', 'manage_users:1;manage_comments:1;manage_videos:1;manage_articles:1;');
INSERT INTO `pm_config` VALUES ('66', 'last_autosync', '1524757198');
INSERT INTO `pm_config` VALUES ('67', 'allow_user_uploadvideo', '1');
INSERT INTO `pm_config` VALUES ('68', 'allow_user_uploadvideo_bytes', '10485760');
INSERT INTO `pm_config` VALUES ('69', 'jwplayerskin', 'modieus.zip');
INSERT INTO `pm_config` VALUES ('70', 'video_sitemap_options', 'a:7:{s:14:\"media_keywords\";b:0;s:14:\"media_category\";b:0;s:12:\"item_pubDate\";b:0;s:10:\"last_build\";i:0;s:11:\"ping_google\";s:2:\"no\";s:9:\"ping_bing\";s:2:\"no\";s:5:\"limit\";i:50000;}');
INSERT INTO `pm_config` VALUES ('71', 'auto_feature', '300');
INSERT INTO `pm_config` VALUES ('72', 'bin_rating_allow_anon_voting', '0');
INSERT INTO `pm_config` VALUES ('73', 'published_articles', '0');
INSERT INTO `pm_config` VALUES ('74', 'published_videos', '3');
INSERT INTO `pm_config` VALUES ('75', 'comment_default_sort', 'added');
INSERT INTO `pm_config` VALUES ('76', 'comment_rating_hide_threshold', '3');
INSERT INTO `pm_config` VALUES ('77', 'user_following_limit', '1000');
INSERT INTO `pm_config` VALUES ('78', 'mod_social', '1');
INSERT INTO `pm_config` VALUES ('79', 'activity_options', 'a:17:{s:6:\"follow\";i:1;s:8:\"unfollow\";i:0;s:5:\"watch\";i:0;s:4:\"like\";i:1;s:7:\"dislike\";i:1;s:8:\"favorite\";i:1;s:10:\"send-video\";i:0;s:12:\"upload-video\";i:1;s:13:\"suggest-video\";i:1;s:4:\"read\";i:0;s:7:\"comment\";i:1;s:4:\"join\";i:1;s:13:\"update-avatar\";i:1;s:12:\"update-cover\";i:1;s:6:\"status\";i:1;s:15:\"create-playlist\";i:1;s:15:\"update-playlist\";i:1;}');
INSERT INTO `pm_config` VALUES ('80', 'pm_notifications_last_prune', '1524757198');
INSERT INTO `pm_config` VALUES ('81', 'total_preroll_ads', '0');
INSERT INTO `pm_config` VALUES ('82', 'preroll_ads_delay', '300');
INSERT INTO `pm_config` VALUES ('83', 'default_tpl_customizations', 'YTowOnt9');
INSERT INTO `pm_config` VALUES ('84', 'custom_logo_url', '');
INSERT INTO `pm_config` VALUES ('85', 'article_widget_limit', '10');
INSERT INTO `pm_config` VALUES ('86', 'new_page_limit', '50');
INSERT INTO `pm_config` VALUES ('87', 'top_page_limit', '50');
INSERT INTO `pm_config` VALUES ('88', 'allow_registration', '1');
INSERT INTO `pm_config` VALUES ('89', 'allow_user_suggestvideo', '1');
INSERT INTO `pm_config` VALUES ('90', 'maintenance_mode', '0');
INSERT INTO `pm_config` VALUES ('91', 'maintenance_display_message', '');
INSERT INTO `pm_config` VALUES ('92', 'thumb_video_w', '480');
INSERT INTO `pm_config` VALUES ('93', 'thumb_video_h', '360');
INSERT INTO `pm_config` VALUES ('94', 'thumb_article_w', '180');
INSERT INTO `pm_config` VALUES ('95', 'thumb_article_h', '180');
INSERT INTO `pm_config` VALUES ('96', 'thumb_avatar_w', '180');
INSERT INTO `pm_config` VALUES ('97', 'thumb_avatar_h', '180');
INSERT INTO `pm_config` VALUES ('98', 'allow_nonlatin_usernames', '1');
INSERT INTO `pm_config` VALUES ('99', 'featured_autoplay', '0');
INSERT INTO `pm_config` VALUES ('100', 'jwplayerkey', '');
INSERT INTO `pm_config` VALUES ('101', 'auto_approve_suggested_videos', '0');
INSERT INTO `pm_config` VALUES ('102', 'keyboard_shortcuts', '1');
INSERT INTO `pm_config` VALUES ('103', 'show_addthis_widget', '0');
INSERT INTO `pm_config` VALUES ('104', 'playingnow_limit', '9');
INSERT INTO `pm_config` VALUES ('105', 'watch_related_limit', '10');
INSERT INTO `pm_config` VALUES ('106', 'watch_toprated_limit', '10');
INSERT INTO `pm_config` VALUES ('107', 'user_upload_daily_limit', '20');
INSERT INTO `pm_config` VALUES ('108', 'spambot_prevention', 'securimage');
INSERT INTO `pm_config` VALUES ('109', 'recaptcha_public_key', '');
INSERT INTO `pm_config` VALUES ('110', 'recaptcha_private_key', '');
INSERT INTO `pm_config` VALUES ('111', 'comment_system', 'on');
INSERT INTO `pm_config` VALUES ('112', 'unread_system_messages', '0');
INSERT INTO `pm_config` VALUES ('113', 'disable_indexing', '0');
INSERT INTO `pm_config` VALUES ('114', 'rtl_support', '0');
INSERT INTO `pm_config` VALUES ('115', 'allow_playlists', '1');
INSERT INTO `pm_config` VALUES ('116', 'playlists_limit', '25');
INSERT INTO `pm_config` VALUES ('117', 'playlists_items_limit', '100');
INSERT INTO `pm_config` VALUES ('118', 'admin_welcome', '0');
INSERT INTO `pm_config` VALUES ('119', 'admin_color_scheme', 'default');
INSERT INTO `pm_config` VALUES ('120', 'vimeo_api_token', '');
INSERT INTO `pm_config` VALUES ('121', 'register_time_to_submit', '3');
INSERT INTO `pm_config` VALUES ('122', 'comment_system_primary', 'native');
INSERT INTO `pm_config` VALUES ('123', 'comment_system_native', '1');
INSERT INTO `pm_config` VALUES ('124', 'comment_system_facebook', '0');
INSERT INTO `pm_config` VALUES ('125', 'comment_system_disqus', '0');
INSERT INTO `pm_config` VALUES ('126', 'disqus_shortname', '');
INSERT INTO `pm_config` VALUES ('127', 'fb_comment_sorting', 'social');
INSERT INTO `pm_config` VALUES ('128', 'fb_app_id', '');
INSERT INTO `pm_config` VALUES ('129', 'youtube_api_key', 'AIzaSyBX058mpKprHzb88vwtyjT-d6kcY2zJQxU');
INSERT INTO `pm_config` VALUES ('130', 'homepage_featured_limit', '10');
INSERT INTO `pm_config` VALUES ('131', 'allow_embedding', '1');
INSERT INTO `pm_config` VALUES ('132', 'timezone', 'UTC');
INSERT INTO `pm_config` VALUES ('133', 'jwplayer7key', '');
INSERT INTO `pm_config` VALUES ('134', 'homepage_featured_categories', 'a:0:{}');
INSERT INTO `pm_config` VALUES ('135', 'eu_cookie_warning', '0');
INSERT INTO `pm_config` VALUES ('136', 'eu_cookie_warning_position', 'floating');
INSERT INTO `pm_config` VALUES ('137', 'allow_emojis', '1');
INSERT INTO `pm_config` VALUES ('138', 'trashed_videos', '0');
INSERT INTO `pm_config` VALUES ('139', 'auto_approve_suggested_videos_verified', '1');
INSERT INTO `pm_config` VALUES ('140', 'allow_user_edit_video', '1');
INSERT INTO `pm_config` VALUES ('141', 'allow_user_delete_video', '0');
INSERT INTO `pm_config` VALUES ('142', 'cron_secret_key', '1f4c18278e01d0e3c6b402953724839a');
INSERT INTO `pm_config` VALUES ('143', 'oauth_facebook', '0');
INSERT INTO `pm_config` VALUES ('144', 'oauth_twitter', '0');
INSERT INTO `pm_config` VALUES ('145', 'oauth_fb_app_id', '');
INSERT INTO `pm_config` VALUES ('146', 'oauth_fb_app_secret', '');
INSERT INTO `pm_config` VALUES ('147', 'oauth_twitter_consumer_key', '');
INSERT INTO `pm_config` VALUES ('148', 'oauth_twitter_consumer_secret', '');
INSERT INTO `pm_config` VALUES ('149', 'download_thumb_res', 'medium');
INSERT INTO `pm_config` VALUES ('150', 'csrfguard', '1');


#
# Table structure for table `pm_countries`
#

DROP TABLE IF EXISTS `pm_countries`;
CREATE TABLE `pm_countries` (
  `countryid` smallint(3) NOT NULL AUTO_INCREMENT,
  `country` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`countryid`),
  KEY `location` (`country`)
) ENGINE=MyISAM AUTO_INCREMENT=505;

#
# Dumping data for table `pm_countries`
#

INSERT INTO `pm_countries` VALUES ('500', 'USA');
INSERT INTO `pm_countries` VALUES ('184', 'Albania');
INSERT INTO `pm_countries` VALUES ('301', 'Algeria');
INSERT INTO `pm_countries` VALUES ('240', 'American Samoa');
INSERT INTO `pm_countries` VALUES ('241', 'Andorra');
INSERT INTO `pm_countries` VALUES ('302', 'Angola');
INSERT INTO `pm_countries` VALUES ('303', 'Anguilla');
INSERT INTO `pm_countries` VALUES ('304', 'Antigua');
INSERT INTO `pm_countries` VALUES ('115', 'Antilles');
INSERT INTO `pm_countries` VALUES ('305', 'Argentina');
INSERT INTO `pm_countries` VALUES ('185', 'Armenia');
INSERT INTO `pm_countries` VALUES ('306', 'Aruba');
INSERT INTO `pm_countries` VALUES ('307', 'Australia');
INSERT INTO `pm_countries` VALUES ('308', 'Austria');
INSERT INTO `pm_countries` VALUES ('186', 'Azerbaijan');
INSERT INTO `pm_countries` VALUES ('187', 'Azores');
INSERT INTO `pm_countries` VALUES ('309', 'Bahamas');
INSERT INTO `pm_countries` VALUES ('310', 'Bahrain');
INSERT INTO `pm_countries` VALUES ('311', 'Bangladesh');
INSERT INTO `pm_countries` VALUES ('312', 'Barbados');
INSERT INTO `pm_countries` VALUES ('313', 'Barbuda');
INSERT INTO `pm_countries` VALUES ('315', 'Belgium');
INSERT INTO `pm_countries` VALUES ('316', 'Belize');
INSERT INTO `pm_countries` VALUES ('314', 'Belorus');
INSERT INTO `pm_countries` VALUES ('317', 'Benin');
INSERT INTO `pm_countries` VALUES ('318', 'Bermuda');
INSERT INTO `pm_countries` VALUES ('319', 'Bhutan');
INSERT INTO `pm_countries` VALUES ('320', 'Bolivia');
INSERT INTO `pm_countries` VALUES ('321', 'Bonaire');
INSERT INTO `pm_countries` VALUES ('188', 'Bosnia-Hercegovina');
INSERT INTO `pm_countries` VALUES ('322', 'Botswana');
INSERT INTO `pm_countries` VALUES ('324', 'Br. Virgin Islands');
INSERT INTO `pm_countries` VALUES ('323', 'Brazil');
INSERT INTO `pm_countries` VALUES ('325', 'Brunei');
INSERT INTO `pm_countries` VALUES ('326', 'Bulgaria');
INSERT INTO `pm_countries` VALUES ('327', 'Burkina Faso');
INSERT INTO `pm_countries` VALUES ('328', 'Burundi');
INSERT INTO `pm_countries` VALUES ('189', 'Caicos Island');
INSERT INTO `pm_countries` VALUES ('329', 'Cameroon');
INSERT INTO `pm_countries` VALUES ('330', 'Canada');
INSERT INTO `pm_countries` VALUES ('190', 'Canary Islands');
INSERT INTO `pm_countries` VALUES ('331', 'Cape Verde');
INSERT INTO `pm_countries` VALUES ('332', 'Cayman Islands');
INSERT INTO `pm_countries` VALUES ('333', 'Central African Republic');
INSERT INTO `pm_countries` VALUES ('334', 'Chad');
INSERT INTO `pm_countries` VALUES ('335', 'Channel Islands');
INSERT INTO `pm_countries` VALUES ('336', 'Chile');
INSERT INTO `pm_countries` VALUES ('337', 'China');
INSERT INTO `pm_countries` VALUES ('338', 'Colombia');
INSERT INTO `pm_countries` VALUES ('191', 'Commonwealth of Ind');
INSERT INTO `pm_countries` VALUES ('339', 'Congo');
INSERT INTO `pm_countries` VALUES ('242', 'Cook Islands');
INSERT INTO `pm_countries` VALUES ('192', 'Cooper Island');
INSERT INTO `pm_countries` VALUES ('340', 'Costa Rica');
INSERT INTO `pm_countries` VALUES ('193', 'Cote D\'Ivoire');
INSERT INTO `pm_countries` VALUES ('194', 'Croatia');
INSERT INTO `pm_countries` VALUES ('341', 'Curacao');
INSERT INTO `pm_countries` VALUES ('342', 'Cyprus');
INSERT INTO `pm_countries` VALUES ('343', 'Czech Republic');
INSERT INTO `pm_countries` VALUES ('344', 'Denmark');
INSERT INTO `pm_countries` VALUES ('345', 'Djibouti');
INSERT INTO `pm_countries` VALUES ('346', 'Dominica');
INSERT INTO `pm_countries` VALUES ('347', 'Dominican Republic');
INSERT INTO `pm_countries` VALUES ('348', 'Ecuador');
INSERT INTO `pm_countries` VALUES ('349', 'Egypt');
INSERT INTO `pm_countries` VALUES ('350', 'El Salvador');
INSERT INTO `pm_countries` VALUES ('351', 'England');
INSERT INTO `pm_countries` VALUES ('352', 'Equatorial Guinea');
INSERT INTO `pm_countries` VALUES ('353', 'Estonia');
INSERT INTO `pm_countries` VALUES ('354', 'Ethiopia');
INSERT INTO `pm_countries` VALUES ('355', 'Fiji');
INSERT INTO `pm_countries` VALUES ('356', 'Finland');
INSERT INTO `pm_countries` VALUES ('357', 'France');
INSERT INTO `pm_countries` VALUES ('358', 'French Guiana');
INSERT INTO `pm_countries` VALUES ('243', 'French Polynesia');
INSERT INTO `pm_countries` VALUES ('254', 'Futuna Island');
INSERT INTO `pm_countries` VALUES ('359', 'Gabon');
INSERT INTO `pm_countries` VALUES ('360', 'Gambia');
INSERT INTO `pm_countries` VALUES ('215', 'Georgia');
INSERT INTO `pm_countries` VALUES ('361', 'Germany');
INSERT INTO `pm_countries` VALUES ('362', 'Ghana');
INSERT INTO `pm_countries` VALUES ('216', 'Gibraltar');
INSERT INTO `pm_countries` VALUES ('363', 'Greece');
INSERT INTO `pm_countries` VALUES ('364', 'Grenada');
INSERT INTO `pm_countries` VALUES ('217', 'Grenland');
INSERT INTO `pm_countries` VALUES ('365', 'Guadeloupe');
INSERT INTO `pm_countries` VALUES ('366', 'Guam');
INSERT INTO `pm_countries` VALUES ('367', 'Guatemala');
INSERT INTO `pm_countries` VALUES ('368', 'Guinea');
INSERT INTO `pm_countries` VALUES ('369', 'Guinea-Bissau');
INSERT INTO `pm_countries` VALUES ('370', 'Guyana');
INSERT INTO `pm_countries` VALUES ('195', 'Haiti');
INSERT INTO `pm_countries` VALUES ('244', 'Holland');
INSERT INTO `pm_countries` VALUES ('371', 'Honduras');
INSERT INTO `pm_countries` VALUES ('372', 'Hong Kong');
INSERT INTO `pm_countries` VALUES ('373', 'Hungary');
INSERT INTO `pm_countries` VALUES ('374', 'Iceland');
INSERT INTO `pm_countries` VALUES ('375', 'India');
INSERT INTO `pm_countries` VALUES ('376', 'Indonesia');
INSERT INTO `pm_countries` VALUES ('377', 'Iran');
INSERT INTO `pm_countries` VALUES ('196', 'Iraq');
INSERT INTO `pm_countries` VALUES ('378', 'Ireland, Northern');
INSERT INTO `pm_countries` VALUES ('379', 'Ireland, Republic of');
INSERT INTO `pm_countries` VALUES ('197', 'Isle of Man');
INSERT INTO `pm_countries` VALUES ('380', 'Israel');
INSERT INTO `pm_countries` VALUES ('381', 'Italy');
INSERT INTO `pm_countries` VALUES ('382', 'Ivory Coast');
INSERT INTO `pm_countries` VALUES ('383', 'Jamaica');
INSERT INTO `pm_countries` VALUES ('384', 'Japan');
INSERT INTO `pm_countries` VALUES ('385', 'Jordan');
INSERT INTO `pm_countries` VALUES ('198', 'Jost Van Dyke Island');
INSERT INTO `pm_countries` VALUES ('218', 'Kampuchea');
INSERT INTO `pm_countries` VALUES ('199', 'Kazakhstan');
INSERT INTO `pm_countries` VALUES ('386', 'Kenya');
INSERT INTO `pm_countries` VALUES ('219', 'Kiribati');
INSERT INTO `pm_countries` VALUES ('239', 'Korea');
INSERT INTO `pm_countries` VALUES ('387', 'Korea, South');
INSERT INTO `pm_countries` VALUES ('256', 'Kosrae');
INSERT INTO `pm_countries` VALUES ('388', 'Kuwait');
INSERT INTO `pm_countries` VALUES ('200', 'Kyrgyzstan');
INSERT INTO `pm_countries` VALUES ('220', 'Laos');
INSERT INTO `pm_countries` VALUES ('389', 'Latvia');
INSERT INTO `pm_countries` VALUES ('390', 'Lebanon');
INSERT INTO `pm_countries` VALUES ('391', 'Lesotho');
INSERT INTO `pm_countries` VALUES ('221', 'Liberia');
INSERT INTO `pm_countries` VALUES ('392', 'Liechtenstein');
INSERT INTO `pm_countries` VALUES ('393', 'Lithuania');
INSERT INTO `pm_countries` VALUES ('394', 'Luxembourg');
INSERT INTO `pm_countries` VALUES ('395', 'Macau');
INSERT INTO `pm_countries` VALUES ('222', 'Macedonia');
INSERT INTO `pm_countries` VALUES ('396', 'Madagascar');
INSERT INTO `pm_countries` VALUES ('201', 'Madeira Islands');
INSERT INTO `pm_countries` VALUES ('202', 'Malagasy');
INSERT INTO `pm_countries` VALUES ('397', 'Malawi');
INSERT INTO `pm_countries` VALUES ('398', 'Malaysia');
INSERT INTO `pm_countries` VALUES ('399', 'Maldives');
INSERT INTO `pm_countries` VALUES ('100', 'Mali');
INSERT INTO `pm_countries` VALUES ('101', 'Malta');
INSERT INTO `pm_countries` VALUES ('102', 'Marshall Islands');
INSERT INTO `pm_countries` VALUES ('103', 'Martinique');
INSERT INTO `pm_countries` VALUES ('104', 'Mauritania');
INSERT INTO `pm_countries` VALUES ('105', 'Mauritius');
INSERT INTO `pm_countries` VALUES ('106', 'Mexico');
INSERT INTO `pm_countries` VALUES ('107', 'Micronesia');
INSERT INTO `pm_countries` VALUES ('203', 'Moldova');
INSERT INTO `pm_countries` VALUES ('108', 'Monaco');
INSERT INTO `pm_countries` VALUES ('223', 'Mongolia');
INSERT INTO `pm_countries` VALUES ('109', 'Montserrat');
INSERT INTO `pm_countries` VALUES ('110', 'Morocco');
INSERT INTO `pm_countries` VALUES ('111', 'Mozambique');
INSERT INTO `pm_countries` VALUES ('224', 'Myanmar');
INSERT INTO `pm_countries` VALUES ('112', 'Namibia');
INSERT INTO `pm_countries` VALUES ('225', 'Nauru');
INSERT INTO `pm_countries` VALUES ('113', 'Nepal');
INSERT INTO `pm_countries` VALUES ('114', 'Netherlands');
INSERT INTO `pm_countries` VALUES ('204', 'Nevis');
INSERT INTO `pm_countries` VALUES ('246', 'Nevis (St. Kitts)');
INSERT INTO `pm_countries` VALUES ('116', 'New Caledonia');
INSERT INTO `pm_countries` VALUES ('117', 'New Zealand');
INSERT INTO `pm_countries` VALUES ('118', 'Nicaragua');
INSERT INTO `pm_countries` VALUES ('119', 'Niger');
INSERT INTO `pm_countries` VALUES ('120', 'Nigeria');
INSERT INTO `pm_countries` VALUES ('226', 'Niue');
INSERT INTO `pm_countries` VALUES ('258', 'Norfolk Island');
INSERT INTO `pm_countries` VALUES ('205', 'Norman Island');
INSERT INTO `pm_countries` VALUES ('257', 'Northern Mariana Island');
INSERT INTO `pm_countries` VALUES ('121', 'Norway');
INSERT INTO `pm_countries` VALUES ('122', 'Oman');
INSERT INTO `pm_countries` VALUES ('123', 'Pakistan');
INSERT INTO `pm_countries` VALUES ('124', 'Palau');
INSERT INTO `pm_countries` VALUES ('125', 'Panama');
INSERT INTO `pm_countries` VALUES ('126', 'Papua New Guinea');
INSERT INTO `pm_countries` VALUES ('127', 'Paraguay');
INSERT INTO `pm_countries` VALUES ('128', 'Peru');
INSERT INTO `pm_countries` VALUES ('129', 'Philippines');
INSERT INTO `pm_countries` VALUES ('130', 'Poland');
INSERT INTO `pm_countries` VALUES ('260', 'Ponape');
INSERT INTO `pm_countries` VALUES ('131', 'Portugal');
INSERT INTO `pm_countries` VALUES ('132', 'Qatar');
INSERT INTO `pm_countries` VALUES ('133', 'Reunion');
INSERT INTO `pm_countries` VALUES ('134', 'Romania');
INSERT INTO `pm_countries` VALUES ('261', 'Rota');
INSERT INTO `pm_countries` VALUES ('135', 'Russia');
INSERT INTO `pm_countries` VALUES ('136', 'Rwanda');
INSERT INTO `pm_countries` VALUES ('137', 'Saba');
INSERT INTO `pm_countries` VALUES ('147', 'Saipan');
INSERT INTO `pm_countries` VALUES ('228', 'San Marino');
INSERT INTO `pm_countries` VALUES ('229', 'Sao Tome');
INSERT INTO `pm_countries` VALUES ('148', 'Saudi Arabia');
INSERT INTO `pm_countries` VALUES ('149', 'Scotland');
INSERT INTO `pm_countries` VALUES ('150', 'Senegal');
INSERT INTO `pm_countries` VALUES ('207', 'Serbia');
INSERT INTO `pm_countries` VALUES ('151', 'Seychelles');
INSERT INTO `pm_countries` VALUES ('152', 'Sierra Leone');
INSERT INTO `pm_countries` VALUES ('153', 'Singapore');
INSERT INTO `pm_countries` VALUES ('208', 'Slovakia');
INSERT INTO `pm_countries` VALUES ('209', 'Slovenia');
INSERT INTO `pm_countries` VALUES ('210', 'Solomon Islands');
INSERT INTO `pm_countries` VALUES ('154', 'Somalia');
INSERT INTO `pm_countries` VALUES ('155', 'South Africa');
INSERT INTO `pm_countries` VALUES ('156', 'Spain');
INSERT INTO `pm_countries` VALUES ('157', 'Sri Lanka');
INSERT INTO `pm_countries` VALUES ('138', 'St. Barthelemy');
INSERT INTO `pm_countries` VALUES ('206', 'St. Christopher');
INSERT INTO `pm_countries` VALUES ('139', 'St. Croix');
INSERT INTO `pm_countries` VALUES ('140', 'St. Eustatius');
INSERT INTO `pm_countries` VALUES ('141', 'St. John');
INSERT INTO `pm_countries` VALUES ('142', 'St. Kitts');
INSERT INTO `pm_countries` VALUES ('143', 'St. Lucia');
INSERT INTO `pm_countries` VALUES ('144', 'St. Maarten');
INSERT INTO `pm_countries` VALUES ('245', 'St. Martin');
INSERT INTO `pm_countries` VALUES ('145', 'St. Thomas');
INSERT INTO `pm_countries` VALUES ('146', 'St. Vincent');
INSERT INTO `pm_countries` VALUES ('158', 'Sudan');
INSERT INTO `pm_countries` VALUES ('159', 'Suriname');
INSERT INTO `pm_countries` VALUES ('160', 'Swaziland');
INSERT INTO `pm_countries` VALUES ('161', 'Sweden');
INSERT INTO `pm_countries` VALUES ('162', 'Switzerland');
INSERT INTO `pm_countries` VALUES ('163', 'Syria');
INSERT INTO `pm_countries` VALUES ('247', 'Tahiti');
INSERT INTO `pm_countries` VALUES ('164', 'Taiwan');
INSERT INTO `pm_countries` VALUES ('211', 'Tajikistan');
INSERT INTO `pm_countries` VALUES ('165', 'Tanzania');
INSERT INTO `pm_countries` VALUES ('166', 'Thailand');
INSERT INTO `pm_countries` VALUES ('248', 'Tinian');
INSERT INTO `pm_countries` VALUES ('167', 'Togo');
INSERT INTO `pm_countries` VALUES ('230', 'Tonaga');
INSERT INTO `pm_countries` VALUES ('249', 'Tonga');
INSERT INTO `pm_countries` VALUES ('250', 'Tortola');
INSERT INTO `pm_countries` VALUES ('168', 'Trinidad and Tobago');
INSERT INTO `pm_countries` VALUES ('251', 'Truk');
INSERT INTO `pm_countries` VALUES ('169', 'Tunisia');
INSERT INTO `pm_countries` VALUES ('170', 'Turkey');
INSERT INTO `pm_countries` VALUES ('212', 'Turkmenistan');
INSERT INTO `pm_countries` VALUES ('171', 'Turks and Caicos Island');
INSERT INTO `pm_countries` VALUES ('231', 'Tuvalu');
INSERT INTO `pm_countries` VALUES ('175', 'U.S. Virgin Islands');
INSERT INTO `pm_countries` VALUES ('172', 'Uganda');
INSERT INTO `pm_countries` VALUES ('173', 'Ukraine');
INSERT INTO `pm_countries` VALUES ('252', 'Union Island');
INSERT INTO `pm_countries` VALUES ('174', 'United Arab Emirates');
INSERT INTO `pm_countries` VALUES ('176', 'Uruguay');
INSERT INTO `pm_countries` VALUES ('262', 'United Kingdom');
INSERT INTO `pm_countries` VALUES ('232', 'Uzbekistan');
INSERT INTO `pm_countries` VALUES ('233', 'Vanuatu');
INSERT INTO `pm_countries` VALUES ('177', 'Vatican City');
INSERT INTO `pm_countries` VALUES ('178', 'Venezuela');
INSERT INTO `pm_countries` VALUES ('234', 'Vietnam');
INSERT INTO `pm_countries` VALUES ('235', 'Virgin Islands (Brit');
INSERT INTO `pm_countries` VALUES ('236', 'Virgin Islands (U.S.');
INSERT INTO `pm_countries` VALUES ('237', 'Wake Island');
INSERT INTO `pm_countries` VALUES ('179', 'Wales');
INSERT INTO `pm_countries` VALUES ('253', 'Wallis Island');
INSERT INTO `pm_countries` VALUES ('238', 'Western Samoa');
INSERT INTO `pm_countries` VALUES ('255', 'Yap');
INSERT INTO `pm_countries` VALUES ('180', 'Yemen, Republic of');
INSERT INTO `pm_countries` VALUES ('213', 'Yugoslavia');
INSERT INTO `pm_countries` VALUES ('181', 'Zaire');
INSERT INTO `pm_countries` VALUES ('182', 'Zambia');
INSERT INTO `pm_countries` VALUES ('183', 'Zimbabwe');
INSERT INTO `pm_countries` VALUES ('501', 'Kosova');
INSERT INTO `pm_countries` VALUES ('502', 'Afghanistan');
INSERT INTO `pm_countries` VALUES ('503', 'Libya');
INSERT INTO `pm_countries` VALUES ('504', 'Eritrea');


#
# Table structure for table `pm_cron_jobs`
#

DROP TABLE IF EXISTS `pm_cron_jobs`;
CREATE TABLE `pm_cron_jobs` (
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
) ENGINE=MyISAM AUTO_INCREMENT=4;

#
# Dumping data for table `pm_cron_jobs`
#

INSERT INTO `pm_cron_jobs` VALUES ('1', 'Video Status Checker', 'vscheck', 'stopped', 'ready', '259200', '0', '9', 'a:5:{s:9:\"sql_start\";i:0;s:12:\"time_started\";i:0;s:16:\"videos_processed\";i:0;s:13:\"video_sorting\";s:6:\"latest\";s:11:\"video_limit\";s:2:\"20\";}', '1524757198');
INSERT INTO `pm_cron_jobs` VALUES ('2', 'Regular Sitemap', 'sitemap', 'stopped', 'ready', '604800', '0', '0', 'a:4:{s:8:\"progress\";i:0;s:20:\"sql_added_time_limit\";i:0;s:13:\"time_last_run\";i:0;s:12:\"time_started\";i:0;}', '1524757198');
INSERT INTO `pm_cron_jobs` VALUES ('3', 'Video Sitemap', 'video-sitemap', 'stopped', 'ready', '604800', '0', '0', 'a:4:{s:8:\"progress\";i:0;s:20:\"sql_added_time_limit\";i:0;s:13:\"time_last_run\";i:0;s:12:\"time_started\";i:0;}', '1524757198');


#
# Table structure for table `pm_cron_log`
#

DROP TABLE IF EXISTS `pm_cron_log`;
CREATE TABLE `pm_cron_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `job_id` int(10) unsigned NOT NULL DEFAULT '0',
  `time` int(10) unsigned NOT NULL DEFAULT '0',
  `notes` text NOT NULL,
  PRIMARY KEY (`log_id`),
  KEY `job_id` (`job_id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_cron_log`
#



#
# Table structure for table `pm_embed_code`
#

DROP TABLE IF EXISTS `pm_embed_code`;
CREATE TABLE `pm_embed_code` (
  `id` int(9) NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `embed_code` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_embed_code`
#



#
# Table structure for table `pm_import_csv_files`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_import_csv_files`
#



#
# Table structure for table `pm_import_csv_items`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_import_csv_items`
#



#
# Table structure for table `pm_import_subscriptions`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_import_subscriptions`
#



#
# Table structure for table `pm_internal_log`
#

DROP TABLE IF EXISTS `pm_internal_log`;
CREATE TABLE `pm_internal_log` (
  `log_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `log_date` datetime NOT NULL,
  `log_info` text NOT NULL,
  PRIMARY KEY (`log_id`)
) ENGINE=MyISAM AUTO_INCREMENT=2;

#
# Dumping data for table `pm_internal_log`
#

INSERT INTO `pm_internal_log` VALUES ('1', '2018-04-26 22:39:58', 'Installed');


#
# Table structure for table `pm_languages`
#

DROP TABLE IF EXISTS `pm_languages`;
CREATE TABLE `pm_languages` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) NOT NULL DEFAULT '',
  `tag` varchar(2) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `id` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=147;

#
# Dumping data for table `pm_languages`
#

INSERT INTO `pm_languages` VALUES ('1', 'Abkhazian', 'ab');
INSERT INTO `pm_languages` VALUES ('2', 'Afar', 'aa');
INSERT INTO `pm_languages` VALUES ('3', 'Afrikaans', 'af');
INSERT INTO `pm_languages` VALUES ('4', 'Albanian', 'sq');
INSERT INTO `pm_languages` VALUES ('5', 'Amharic', 'am');
INSERT INTO `pm_languages` VALUES ('6', 'Arabic', 'ar');
INSERT INTO `pm_languages` VALUES ('7', 'Aragonese', 'an');
INSERT INTO `pm_languages` VALUES ('8', 'Armenian', 'hy');
INSERT INTO `pm_languages` VALUES ('9', 'Assamese', 'as');
INSERT INTO `pm_languages` VALUES ('10', 'Aymara', 'ay');
INSERT INTO `pm_languages` VALUES ('11', 'Azerbaijani', 'az');
INSERT INTO `pm_languages` VALUES ('12', 'Bashkir', 'ba');
INSERT INTO `pm_languages` VALUES ('13', 'Basque', 'eu');
INSERT INTO `pm_languages` VALUES ('14', 'Bengali (Bangla)', 'bn');
INSERT INTO `pm_languages` VALUES ('15', 'Bhutani', 'dz');
INSERT INTO `pm_languages` VALUES ('16', 'Bihari', 'bh');
INSERT INTO `pm_languages` VALUES ('17', 'Bislama', 'bi');
INSERT INTO `pm_languages` VALUES ('18', 'Breton', 'br');
INSERT INTO `pm_languages` VALUES ('19', 'Bulgarian', 'bg');
INSERT INTO `pm_languages` VALUES ('20', 'Burmese', 'my');
INSERT INTO `pm_languages` VALUES ('21', 'Byelorussian (Belarusian)', 'be');
INSERT INTO `pm_languages` VALUES ('22', 'Cambodian', 'km');
INSERT INTO `pm_languages` VALUES ('23', 'Catalan', 'ca');
INSERT INTO `pm_languages` VALUES ('24', 'Chinese (Simplified)', 'zh');
INSERT INTO `pm_languages` VALUES ('25', 'Chinese (Traditional)', 'zh');
INSERT INTO `pm_languages` VALUES ('26', 'Corsican', 'co');
INSERT INTO `pm_languages` VALUES ('27', 'Croatian', 'hr');
INSERT INTO `pm_languages` VALUES ('28', 'Czech', 'cs');
INSERT INTO `pm_languages` VALUES ('29', 'Danish', 'da');
INSERT INTO `pm_languages` VALUES ('30', 'Dutch', 'nl');
INSERT INTO `pm_languages` VALUES ('31', 'English', 'en');
INSERT INTO `pm_languages` VALUES ('32', 'Esperanto', 'eo');
INSERT INTO `pm_languages` VALUES ('33', 'Estonian', 'et');
INSERT INTO `pm_languages` VALUES ('34', 'Faeroese', 'fo');
INSERT INTO `pm_languages` VALUES ('35', 'Farsi', 'fa');
INSERT INTO `pm_languages` VALUES ('36', 'Fiji', 'fj');
INSERT INTO `pm_languages` VALUES ('37', 'Finnish', 'fi');
INSERT INTO `pm_languages` VALUES ('38', 'French', 'fr');
INSERT INTO `pm_languages` VALUES ('39', 'Frisian', 'fy');
INSERT INTO `pm_languages` VALUES ('40', 'Galician', 'gl');
INSERT INTO `pm_languages` VALUES ('41', 'Gaelic (Scottish)', 'gd');
INSERT INTO `pm_languages` VALUES ('42', 'Gaelic (Manx)', 'gv');
INSERT INTO `pm_languages` VALUES ('43', 'Georgian', 'ka');
INSERT INTO `pm_languages` VALUES ('44', 'German', 'de');
INSERT INTO `pm_languages` VALUES ('45', 'Greek', 'el');
INSERT INTO `pm_languages` VALUES ('46', 'Greenlandic', 'kl');
INSERT INTO `pm_languages` VALUES ('47', 'Guarani', 'gn');
INSERT INTO `pm_languages` VALUES ('48', 'Gujarati', 'gu');
INSERT INTO `pm_languages` VALUES ('49', 'Haitian Creole', 'ht');
INSERT INTO `pm_languages` VALUES ('50', 'Hausa', 'ha');
INSERT INTO `pm_languages` VALUES ('51', 'Hebrew', 'he');
INSERT INTO `pm_languages` VALUES ('52', 'Hindi', 'hi');
INSERT INTO `pm_languages` VALUES ('53', 'Hungarian', 'hu');
INSERT INTO `pm_languages` VALUES ('54', 'Icelandic', 'is');
INSERT INTO `pm_languages` VALUES ('55', 'Ido', 'io');
INSERT INTO `pm_languages` VALUES ('56', 'Indonesian', 'id');
INSERT INTO `pm_languages` VALUES ('57', 'Interlingua', 'ia');
INSERT INTO `pm_languages` VALUES ('58', 'Interlingue', 'ie');
INSERT INTO `pm_languages` VALUES ('59', 'Inuktitut', 'iu');
INSERT INTO `pm_languages` VALUES ('60', 'Inupiak', 'ik');
INSERT INTO `pm_languages` VALUES ('61', 'Irish', 'ga');
INSERT INTO `pm_languages` VALUES ('62', 'Italian', 'it');
INSERT INTO `pm_languages` VALUES ('63', 'Japanese', 'ja');
INSERT INTO `pm_languages` VALUES ('64', 'Javanese', 'jv');
INSERT INTO `pm_languages` VALUES ('65', 'Kannada', 'kn');
INSERT INTO `pm_languages` VALUES ('66', 'Kashmiri', 'ks');
INSERT INTO `pm_languages` VALUES ('67', 'Kazakh', 'kk');
INSERT INTO `pm_languages` VALUES ('68', 'Kinyarwanda (Ruanda)', 'rw');
INSERT INTO `pm_languages` VALUES ('69', 'Kirghiz', 'ky');
INSERT INTO `pm_languages` VALUES ('70', 'Kirundi (Rundi)', 'rn');
INSERT INTO `pm_languages` VALUES ('71', 'Korean', 'ko');
INSERT INTO `pm_languages` VALUES ('72', 'Kurdish', 'ku');
INSERT INTO `pm_languages` VALUES ('73', 'Laothian', 'lo');
INSERT INTO `pm_languages` VALUES ('74', 'Latin', 'la');
INSERT INTO `pm_languages` VALUES ('75', 'Latvian (Lettish)', 'lv');
INSERT INTO `pm_languages` VALUES ('76', 'Limburgish (Limburger)', 'li');
INSERT INTO `pm_languages` VALUES ('77', 'Lingala', 'ln');
INSERT INTO `pm_languages` VALUES ('78', 'Lithuanian', 'lt');
INSERT INTO `pm_languages` VALUES ('79', 'Macedonian', 'mk');
INSERT INTO `pm_languages` VALUES ('80', 'Malagasy', 'mg');
INSERT INTO `pm_languages` VALUES ('81', 'Malay', 'ms');
INSERT INTO `pm_languages` VALUES ('82', 'Malayalam', 'ml');
INSERT INTO `pm_languages` VALUES ('83', 'Maltese', 'mt');
INSERT INTO `pm_languages` VALUES ('84', 'Maori', 'mi');
INSERT INTO `pm_languages` VALUES ('85', 'Marathi', 'mr');
INSERT INTO `pm_languages` VALUES ('86', 'Moldavian', 'mo');
INSERT INTO `pm_languages` VALUES ('87', 'Mongolian', 'mn');
INSERT INTO `pm_languages` VALUES ('88', 'Nauru', 'na');
INSERT INTO `pm_languages` VALUES ('89', 'Nepali', 'ne');
INSERT INTO `pm_languages` VALUES ('90', 'Norwegian', 'no');
INSERT INTO `pm_languages` VALUES ('91', 'Occitan', 'oc');
INSERT INTO `pm_languages` VALUES ('92', 'Oriya', 'or');
INSERT INTO `pm_languages` VALUES ('93', 'Oromo (Afan Galla)', 'om');
INSERT INTO `pm_languages` VALUES ('94', 'Pashto (Pushto)', 'ps');
INSERT INTO `pm_languages` VALUES ('95', 'Polish', 'pl');
INSERT INTO `pm_languages` VALUES ('96', 'Portuguese', 'pt');
INSERT INTO `pm_languages` VALUES ('97', 'Punjabi', 'pa');
INSERT INTO `pm_languages` VALUES ('98', 'Quechua', 'qu');
INSERT INTO `pm_languages` VALUES ('99', 'Rhaeto-Romance', 'rm');
INSERT INTO `pm_languages` VALUES ('100', 'Romanian', 'ro');
INSERT INTO `pm_languages` VALUES ('101', 'Russian', 'ru');
INSERT INTO `pm_languages` VALUES ('102', 'Samoan', 'sm');
INSERT INTO `pm_languages` VALUES ('103', 'Sangro', 'sg');
INSERT INTO `pm_languages` VALUES ('104', 'Sanskrit', 'sa');
INSERT INTO `pm_languages` VALUES ('105', 'Serbian', 'sr');
INSERT INTO `pm_languages` VALUES ('106', 'Serbo-Croatian', 'sh');
INSERT INTO `pm_languages` VALUES ('107', 'Sesotho', 'st');
INSERT INTO `pm_languages` VALUES ('108', 'Setswana', 'tn');
INSERT INTO `pm_languages` VALUES ('109', 'Shona', 'sn');
INSERT INTO `pm_languages` VALUES ('110', 'Sichuan Yi', 'ii');
INSERT INTO `pm_languages` VALUES ('111', 'Sindhi', 'sd');
INSERT INTO `pm_languages` VALUES ('112', 'Sinhalese', 'si');
INSERT INTO `pm_languages` VALUES ('113', 'Siswati', 'ss');
INSERT INTO `pm_languages` VALUES ('114', 'Slovak', 'sk');
INSERT INTO `pm_languages` VALUES ('115', 'Slovenian', 'sl');
INSERT INTO `pm_languages` VALUES ('116', 'Somali', 'so');
INSERT INTO `pm_languages` VALUES ('117', 'Spanish', 'es');
INSERT INTO `pm_languages` VALUES ('118', 'Sundanese', 'su');
INSERT INTO `pm_languages` VALUES ('119', 'Swahili (Kiswahili)', 'sw');
INSERT INTO `pm_languages` VALUES ('120', 'Swedish', 'sv');
INSERT INTO `pm_languages` VALUES ('121', 'Tagalog', 'tl');
INSERT INTO `pm_languages` VALUES ('122', 'Tajik', 'tg');
INSERT INTO `pm_languages` VALUES ('123', 'Tamil', 'ta');
INSERT INTO `pm_languages` VALUES ('124', 'Tatar', 'tt');
INSERT INTO `pm_languages` VALUES ('125', 'Telugu', 'te');
INSERT INTO `pm_languages` VALUES ('126', 'Thai', 'th');
INSERT INTO `pm_languages` VALUES ('127', 'Tibetan', 'bo');
INSERT INTO `pm_languages` VALUES ('128', 'Tigrinya', 'ti');
INSERT INTO `pm_languages` VALUES ('129', 'Tonga', 'to');
INSERT INTO `pm_languages` VALUES ('130', 'Tsonga', 'ts');
INSERT INTO `pm_languages` VALUES ('131', 'Turkish', 'tr');
INSERT INTO `pm_languages` VALUES ('132', 'Turkmen', 'tk');
INSERT INTO `pm_languages` VALUES ('133', 'Twi', 'tw');
INSERT INTO `pm_languages` VALUES ('134', 'Uighur', 'ug');
INSERT INTO `pm_languages` VALUES ('135', 'Ukrainian', 'uk');
INSERT INTO `pm_languages` VALUES ('136', 'Urdu', 'ur');
INSERT INTO `pm_languages` VALUES ('137', 'Uzbek', 'uz');
INSERT INTO `pm_languages` VALUES ('138', 'Vietnamese', 'vi');
INSERT INTO `pm_languages` VALUES ('139', 'Volap&uuml;k', 'vo');
INSERT INTO `pm_languages` VALUES ('140', 'Wallon', 'wa');
INSERT INTO `pm_languages` VALUES ('141', 'Welsh', 'cy');
INSERT INTO `pm_languages` VALUES ('142', 'Wolof', 'wo');
INSERT INTO `pm_languages` VALUES ('143', 'Xhosa', 'xh');
INSERT INTO `pm_languages` VALUES ('144', 'Yiddish', 'yi');
INSERT INTO `pm_languages` VALUES ('145', 'Yoruba', 'yo');
INSERT INTO `pm_languages` VALUES ('146', 'Zulu', 'zu');


#
# Table structure for table `pm_log`
#

DROP TABLE IF EXISTS `pm_log`;
CREATE TABLE `pm_log` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `log_msg` text NOT NULL,
  `area` varchar(50) NOT NULL DEFAULT '',
  `added` int(11) NOT NULL DEFAULT '0',
  `msg_type` enum('0','1') NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `area` (`area`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_log`
#



#
# Table structure for table `pm_meta`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=10;

#
# Dumping data for table `pm_meta`
#

INSERT INTO `pm_meta` VALUES ('4', '1', '3', '_meta_keywords', '');
INSERT INTO `pm_meta` VALUES ('5', '1', '3', '_meta_description', '');
INSERT INTO `pm_meta` VALUES ('6', '2', '3', '_meta_keywords', '');
INSERT INTO `pm_meta` VALUES ('7', '2', '3', '_meta_description', '');
INSERT INTO `pm_meta` VALUES ('8', '3', '3', '_meta_keywords', '');
INSERT INTO `pm_meta` VALUES ('9', '3', '3', '_meta_description', '');


#
# Table structure for table `pm_notifications`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_notifications`
#



#
# Table structure for table `pm_pages`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=4;

#
# Dumping data for table `pm_pages`
#

INSERT INTO `pm_pages` VALUES ('1', 'Terms of Agreement', '<h2>Code of Conduct</h2>
<p>In using this Service, you must behave in a civil and respectful manner at all times. Further, you will not:</p>
<ul>
<li>Act in a deceptive manner by, among other things, impersonating any person;</li>
<li>Harass or stalk any other person;</li>
<li>Harm or exploit minors;</li>
<li>Distribute \"spam\";</li>
<li>Collect information about others; or</li>
<li>Advertise or solicit others to purchase any product or service within the Site (unless you are an official partner or advertiser and have a written agreement with us).</li>
</ul>
<p>The Site owner has the right, but not the obligation, to monitor all conduct on and content submitted to the Service.</p>
<hr />
<h2>Membership</h2>
<p>REGISTRATION: To fully use the the Service, you must register as a member by providing a user name, password, and valid email address. You must provide complete and accurate registration information and notify us if your information changes. If you are a business, government, or non-profit entity, the person whose email address is associated with the account must have the authority to bind the entity to this Agreement.</p>
<p>USER NAME: We encourage you to use your real name. If you are a business, government, or non-profit entity, you must use the actual name of your organization. You may not use someone else\'s name, a name that violates any third party right, or a name that is obscene or otherwise objectionable.</p>
<p>ACCOUNT SECURITY: You are responsible for all activity that occurs under your account, including any activity by unauthorized users. You must not allow others to use your account. You must safeguard the confidentiality of your password. If you are using a computer that others have access to, you must log out of your account after using the Service.</p>
<hr />
<h2>Content Restrictions</h2>
<p>You may not upload, post, or transmit (collectively, \"submit\") any video, image, text, audio recording, or other work (collectively, \"content\") that:</p>
<ul>
<li>Infringes any third party\'s copyrights or other rights (e.g., trademark, privacy rights, etc.);</li>
<li>Contains sexually explicit content or pornography (provided, however, that non-sexual nudity is permitted);</li>
<li>Contains hateful, defamatory, or discriminatory content or incites hatred against any individual or group;</li>
<li>Exploits minors;</li>
<li>Depicts unlawful acts or extreme violence;</li>
<li>Depicts animal cruelty or extreme violence towards animals;</li>
<li>Promotes fraudulent schemes, multi level marketing (MLM) schemes, get rich quick schemes, online gaming and gambling, cash gifting, work from home businesses, or any other dubious money-making ventures; or Violates any law.</li>
</ul>', '1', '1366891687', '1', 'terms-toa', '0', '0');
INSERT INTO `pm_pages` VALUES ('2', '404 Error', '<h3>Sorry, page not found!</h3>
<p>The page you are looking for could not be found. Please check the link you followed to get here and try again.</p>', '1', '1366891687', '1', '404', '0', '0');
INSERT INTO `pm_pages` VALUES ('3', 'test', '<p>testset</p>
<p><a href=\"http://localhost/uploads/articles/18f3a5c1.jpg\" rel=\"prettyPhoto[phpmelody]\"><img src=\"http://localhost/uploads/articles/18f3a5c1.jpg\" alt=\"\" width=\"500\" height=\"500\" border=\"0\" hspace=\"\" vspace=\"\" /></a>fdsafdsfsafsdafsdafsdafsd</p>
<p>&nbsp;</p>', '1', '1524757319', '1', 'test', '4', '1');


#
# Table structure for table `pm_playlist_items`
#

DROP TABLE IF EXISTS `pm_playlist_items`;
CREATE TABLE `pm_playlist_items` (
  `list_item_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `list_id` int(10) unsigned NOT NULL DEFAULT '0',
  `video_id` int(10) unsigned NOT NULL DEFAULT '0',
  `position` smallint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`list_item_id`),
  KEY `list_id` (`list_id`),
  KEY `video_id` (`video_id`)
) ENGINE=MyISAM AUTO_INCREMENT=8;

#
# Dumping data for table `pm_playlist_items`
#

INSERT INTO `pm_playlist_items` VALUES ('1', '3', '1', '1');
INSERT INTO `pm_playlist_items` VALUES ('2', '2', '2', '1');
INSERT INTO `pm_playlist_items` VALUES ('3', '4', '1', '1');
INSERT INTO `pm_playlist_items` VALUES ('4', '4', '2', '2');
INSERT INTO `pm_playlist_items` VALUES ('5', '4', '3', '3');
INSERT INTO `pm_playlist_items` VALUES ('6', '2', '3', '2');


#
# Table structure for table `pm_playlists`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=5;

#
# Dumping data for table `pm_playlists`
#

INSERT INTO `pm_playlists` VALUES ('1', 'FBA46DCBE461', '1', '1', '0', '1524757198', '0', 'default', '', '', '');
INSERT INTO `pm_playlists` VALUES ('2', '913554463FAF', '1', '2', '2', '1524757198', '1', 'default', '', '', '');
INSERT INTO `pm_playlists` VALUES ('3', '23BA3BE660A1', '1', '3', '1', '1524757198', '1', 'default', '', '', '');
INSERT INTO `pm_playlists` VALUES ('4', '6A6FCDD83A1A', '1', '4', '3', '1524757198', '0', 'date-added-desc', '', '', '');


#
# Table structure for table `pm_preroll_ads`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_preroll_ads`
#



#
# Table structure for table `pm_ratings`
#

DROP TABLE IF EXISTS `pm_ratings`;
CREATE TABLE `pm_ratings` (
  `id` varchar(10) NOT NULL DEFAULT '',
  `total_votes` mediumint(6) unsigned NOT NULL DEFAULT '0',
  `total_value` mediumint(7) unsigned NOT NULL DEFAULT '0',
  `used_ips` longtext,
  `which_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_ratings`
#



#
# Table structure for table `pm_reports`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_reports`
#



#
# Table structure for table `pm_searches`
#

DROP TABLE IF EXISTS `pm_searches`;
CREATE TABLE `pm_searches` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `string` varchar(100) NOT NULL DEFAULT '',
  `hits` mediumint(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_searches`
#



#
# Table structure for table `pm_sources`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=83;

#
# Dumping data for table `pm_sources`
#

INSERT INTO `pm_sources` VALUES ('1', 'localhost', '/(.*?)\\.flv/i', '', '0', '1', '0', '', 'flvplayer', '1', '0');
INSERT INTO `pm_sources` VALUES ('2', 'other', '/(.*?)\\.flv/i', 'http://www.example.com/uploads/video.flv', '0', '1', '0', '', 'flvplayer', '1', '0');
INSERT INTO `pm_sources` VALUES ('3', 'youtube', '/youtube\\./i', 'http://www.youtube.com/watch?v=[VIDEO ID]', '0', '1', '1', '<iframe src=\"//www.youtube.com/embed/%%yt_id%%?hl=en_US&hd=%%use_hq_vids%%&cc_load_policy=1&rel=0&fs=1&autoplay=%%player_autoplay%%&color2=0x%%player_bgcolor%%&showsearch=0&showinfo=0&iv_load_policy=3&modestbranding=1\" width=\"100%\" height=\"%%player_h%%\" frameborder=\"0\" allowfullscreen></iframe>', 'flvplayer', '1', '0');
INSERT INTO `pm_sources` VALUES ('5', 'dailymotion', '/dailymotion\\./i', 'http://www.dailymotion.com/en/category/[VIDEO ID]_video-title-here', '0', '0', '1', '<iframe frameborder=\"0\" width=\"100%\" height=\"%%player_h%%\" src=\"//www.dailymotion.com/embed/video/%%yt_id%%&autoplay=%%player_autoplay%%&highlight=20A8E1&info=0&logo=0&related=0&startscreen=html&html=1\" allowfullscreen></iframe>', 'embed', '1', '0');
INSERT INTO `pm_sources` VALUES ('6', 'metacafe', '/metacafe\\.com/i', 'http://www.metacafe.com/watch/[VIDEO ID]/video_title_here/', '0', '1', '1', '<embed src=\"//www.metacafe.com/fplayer/%%yt_id%%/video.swf\" width=\"%%player_w%%\" height=\"%%player_h%%\" wmode=\"%%player_wmode%%\" pluginspage=\"//www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\" allowScriptAccess=\"always\" name=\"Metacafe_%%yt_id%%\"> 
</embed>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('7', 'myspace', '/myspace\\.com/i', 'http://www.myspace.com/video/channel/video-title/123456781', '0', '1', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"allowFullScreen\" value=\"true\"/>
 <param name=\"wmode\" value=\"%%player_wmode%%\"/>
 <param name=\"movie\" value=\"//mediaservices.myspace.com/services/media/embed.aspx/m=%%yt_id%%,t=1,mt=video\"/>
 <embed src=\"//mediaservices.myspace.com/services/media/embed.aspx/m=%%yt_id%%,t=1,mt=video\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowFullScreen=\"true\" type=\"application/x-shockwave-flash\" wmode=\"%%player_wmode%%\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('9', 'veoh', '/veoh\\.com/i', 'http://www.veoh.com/collection/Artist-or-Group-Name/watch/[VIDEO ID]', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\" id=\"veohFlashPlayer\" name=\"veohFlashPlayer\">
 <param name=\"movie\" value=\"//www.veoh.com/static/swf/webplayer/WebPlayer.swf?permalinkId=%%yt_id%%&player=videodetailsembedded&videoAutoPlay=%%player_autoplay%%&id=anonymous\"></param>
 <param name=\"allowFullScreen\" value=\"true\"></param>
 <param name=\"wmode\" value=\"%%player_wmode%%\"></param>
 <param name=\"allowscriptaccess\" value=\"always\"></param>
 <embed src=\"//www.veoh.com/static/swf/webplayer/WebPlayer.swf?permalinkId=%%yt_id%%&player=videodetailsembedded&videoAutoPlay=%%player_autoplay%%&id=anonymous\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"%%player_w%%\" height=\"%%player_h%%\" id=\"veohFlashPlayerEmbed\" name=\"veohFlashPlayerEmbed\" wmode=\"%%player_wmode%%\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('10', 'break', '/break\\.com/i', 'http://www.break.com/index/video-title-here.html', '0', '1', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"//embed.break.com/%%yt_id%%\"></param>
 <param name=\"wmode\" value=\"%%player_wmode%%\"></param>
 <param name=\"allowScriptAccess\" value=\"always\"></param>
 <embed src=\"//embed.break.com/%%yt_id%%\" type=\"application/x-shockwave-flash\" allowScriptAccess=\"always\" wmode=\"%%player_wmode%%\" width=\"%%player_w%%\" height=\"%%player_h%%\"></embed>
</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('11', 'myvideo', '/myvideo\\.de/i', 'http://www.myvideo.de/watch/[VIDEO ID]/Video_title_here/', '0', '1', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"//www.myvideo.de/movie/%%yt_id%%\"></param>
 <param name=\"AllowFullscreen\" value=\"true\"></param>
 <param name=\"wmode\" value=\"%%player_wmode%%\"></param>
 <param name=\"AllowScriptAccess\" value=\"always\"></param>
 <embed src=\"//www.myvideo.de/movie/%%yt_id%%\" width=\"%%player_w%%\" height=\"%%player_h%%\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" wmode=\"%%player_wmode%%\"></embed>
</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('71', 'nhaccuatui', '/nhaccuatui\\.com/i', 'http://www.nhaccuatui.com/mv4u/xem-clip/cjidlr07OG3N/phai-lam-the-nao-wanbi-tuan-anh.html', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"%%url_flv%%\" />
 <param name=\"quality\" value=\"high\" />
 <param name=\"wmode\" value=\"%%player_wmode%%\" />
 <param name=\"allowscriptaccess\" value=\"always\" />
 <embed src=\"%%url_flv%%\" allowscriptaccess=\"always\" quality=\"high\" wmode=\"%%player_wmode%%\" type=\"application/x-shockwave-flash\" width=\"%%player_w%%\" height=\"%%player_h%%\">
 </embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('72', 'kure', '/kure\\.tv/i', 'http://www.kure.tv/otomobil/494-surucu/bmw-z4-test-surusu/151-Bolum/87652/', '0', '0', '1', '<iframe width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//www.kure.tv/VideoEmbed?ID=%%yt_id%%\" hspace=\"0\" vspace=\"0\" scrolling=\"no\" frameborder=\"0\" allowfullscreen=\"true\"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('43', 'windows media player', '/-(.*?)\\.(wmv|asf|wma)/i', 'http://www.example.com/video.wmv', '0', '0', '1', '<object id=\"wmv\" width=\"%%player_w%%\" height=\"%%player_h%%\" classid=\"CLSID:6BF52A52-394A-11d3-B153-00C04F79FAA6\" type=\"application/x-oleobject\">
<param name=\"URL\" value=\"%%url_flv%%\">
<param name=\"AutoStart\" value=\"true\">
<param name=\"ShowControls\" value=\"true\">
<param name=\"ShowStatusBar\" value=\"false\">
<param name=\"ShowDisplay\" value=\"false\">
<param name=\"EnableFullScreenControls\" value=\"true\">
<param name=\"FullScreenMode\" value=\"true\">
<param name=\"wmode\" value=\"%%player_wmode%%\"></param> 
<embed type=\"application/x-mplayer2\" src=\"%%url_flv%%\" name=\"MediaPlayer\"
width=\"%%player_w%%\" height=\"%%player_h%%\" ShowControls=\"1\" ShowStatusBar=\"0\" ShowDisplay=\"0\" AutoStart=\"%%player_autoplay%%\" EnableFullScreenControls=\"1\" FullScreenMode=\"1\" wmode=\"%%player_wmode%%\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('69', 'veevr', '/veevr\\.com/i', 'http://veevr.com/videos/videoID', '0', '0', '1', '<iframe src=\"//veevr.com/embed/%%yt_id%%?w=%%player_w%%&h=%%player_h%%\" width=\"%%player_w%%\" height=\"%%player_h%%\" scrolling=\"no\" frameborder=\"0\"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('70', '123video.nl', '/123video\\.nl/i', 'http://www.123video.nl/playvideos.asp?MovieID=1234567', '0', '0', '1', '<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"//fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=7,0,0,0\" width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"//www.123video.nl/123video_emb.swf?mediaSrc=%%yt_id%%\"></param>
 <param name=\"quality\" value=\"high\"></param>
 <param name=\"allowScriptAccess\" value=\"always\"></param>
 <param name=\"allowFullScreen\" value=\"true\"></param>
 <embed src=\"//www.123video.nl/123video_emb.swf?mediaSrc=%%yt_id%%\" quality=\"high\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowfullscreen=\"true\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" pluginspage=\"//www.macromedia.com/go/getflashplayer\" />
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('16', 'vimeo', '/vimeo\\.com/i', 'http://vimeo.com/[VIDEO ID]', '0', '1', '1', '<iframe src=\"//player.vimeo.com/video/%%yt_id%%?color=20A8E1&autoplay=%%player_autoplay%%&title=0&byline=0&badge=0\" width=\"100%\" height=\"%%player_h%%\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>', 'flvplayer', '1', '0');
INSERT INTO `pm_sources` VALUES ('17', 'trilulilu', '/trilulilu\\.ro/i', 'http://www.trilulilu.ro/user/[VIDEO ID]', '0', '1', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\"> <param name=\"wmode\" value=\"%%player_wmode%%\"></param> <param name=\"movie\" value=\"//embed.trilulilu.ro/video/%%username%%/%%yt_id%%.swf\"></param> <param name=\"allowFullScreen\" value=\"true\"></param> <param name=\"allowscriptaccess\" value=\"always\"></param> <param name=\"flashvars\" value=\"username=%%username%%&hash=%%yt_id%%&color=0x%%player_bgcolor%%\"></param> <embed src=\"//embed.trilulilu.ro/video/%%username%%/%%yt_id%%.swf\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"%%player_w%%\" height=\"%%player_h%%\" wmode=\"%%player_wmode%%\" flashvars=\"username=%%username%%&hash=%%yt_id%%&color=0x%%player_bgcolor%%\"></embed> </object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('18', 'bliptv', '/blip\\.tv/i', 'http://blip.tv/user/video-title-[VIDEO ID]', '0', '1', '1', '<embed src=\"//blip.tv/play/%%yt_id%%\" type=\"application/x-shockwave-flash\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowscriptaccess=\"always\" allowfullscreen=\"true\" wmode=\"%%player_wmode%%\"></embed> ', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('19', 'sevenload', '/sevenload\\.com/i', 'http://en.sevenload.com/videos/[VIDEO ID]-Video-title-here', '0', '1', '1', '<object type=\"application/x-shockwave-flash\" data=\"//static.sevenload.com/swf/player/player.swf?configPath=http%3A%2F%2Fflash.sevenload.com%2Fplayer%3FportalId%3Den%26autoplay%3D%%player_autoplay%%%26mute%3D0%26itemId%3D%%yt_id%%&locale=en_US&autoplay=%%player_autoplay%%\" width=\"%%player_w%%\" height=\"%%player_h%%\"> <param name=\"allowFullscreen\" value=\"true\" /> <param name=\"allowScriptAccess\" value=\"always\" /> <param name=\"movie\" value=\"//static.sevenload.com/swf/player/player.swf?configPath=http%3A%2F%2Fflash.sevenload.com%2Fplayer%3FportalId%3Den%26autoplay%3D%%player_autoplay%%%26mute%3D0%26itemId%3D%%yt_id%%&locale=en_US&autoplay=%%player_autoplay%%\" />', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('20', 'funnyordie', '/funnyordie\\.com/i', 'http://www.funnyordie.com/videos/[VIDEO ID]', '0', '1', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" id=\"ordie_player_%%yt_id%%\">
 <param name=\"wmode\" value=\"%%player_wmode%%\"></param>
 <param name=\"movie\" value=\"//player.ordienetworks.com/flash/fodplayer.swf\" />
 <param name=\"flashvars\" value=\"key=%%yt_id%%\" />
 <param name=\"allowfullscreen\" value=\"true\" />
 <param name=\"allowscriptaccess\" value=\"always\"></param>
 <embed width=\"%%player_w%%\" height=\"%%player_h%%\" flashvars=\"key=%%yt_id%%\" allowfullscreen=\"true\" allowscriptaccess=\"always\" quality=\"high\" src=\"//player.ordienetworks.com/flash/fodplayer.swf\" name=\"ordie_player_%%yt_id%%\" type=\"application/x-shockwave-flash\" wmode=\"%%player_wmode%%\"></embed>
</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('68', 'clip.vn', '/clip\\.vn//i', 'http://clip.vn/watch/Video-title,videoID', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"//clip.vn/w/%%yt_id%%\"/>
 <param name=\"allowFullScreen\" value=\"true\"/>
 <param name=\"allowScriptAccess\" value=\"always\"/>
 <embed type=\"application/x-shockwave-flash\" allowFullScreen=\"true\" allowScriptAccess=\"always\" width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//clip.vn/w/%%yt_id%%\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('23', 'filebox', '/filebox\\.ro/i', 'http://www.filebox.ro/video/play_video.php?key=[VIDEO ID]', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <embed type=\"application/x-shockwave-flash\" src=\"//www.filebox.ro/video/FileboxPlayer_provider.php\" style=\"\" id=\"mediaplayer\" name=\"mediaplayer\" quality=\"high\" allowfullscreen=\"true\" wmode=\"%%player_wmode%%\" flashvars=\"source_script=//videoserver325.filebox.ro/get_video.php&key=%%yt_id%%&autostart=%%player_autoplay%%&getLink=//fbx.ro/v/%%yt_id%%&splash=//imageserver.filebox.ro/get_splash.php?key=%%yt_id%%&link=\" height=\"%%player_h%%\" width=\"%%player_w%%\">
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('24', 'youku', '/youku\\.com/i', 'http://v.youku.com/v_show/id_[VIDEO ID].html', '0', '0', '1', '<embed src=\"//player.youku.com/player.php/sid/%%yt_id%%=/v.swf\" quality=\"high\" width=\"%%player_w%%\" height=\"%%player_h%%\" align=\"middle\" allowScriptAccess=\"sameDomain\" type=\"application/x-shockwave-flash\" wmode=\"%%player_wmode%%\"></embed>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('67', 'tudou', '/tudou\\.com/i', 'http://www.tudou.com/programs/view/video-id/', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"//www.tudou.com/v/%%yt_id%%/v.swf\"></param>
 <param value=\"true\" name=\"allowfullscreen\"></param>
 <param value=\"always\" name=\"allowscriptaccess\"></param>
 <param value=\"opaque\" name=\"%%player_wmode%%\"></param>
 <embed src=\"//www.tudou.com/v/%%yt_id%%/v.swf\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" wmode=\"%%player_wmode%%\" width=\"%%player_w%%\" height=\"%%player_h%%\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('66', 'publicdomainflicks', '/publicdomainflicks\\.com/i', 'http://www.publicdomainflicks.com/0123-video-title/', '0', '1', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"//www.publicdomainflicks.com/flvplayer.swf\"></param>
 <param name=\"wmode\" value=\"%%player_wmode%%\"></param>
 <param name=\"allowFullScreen\" value=\"true\"></param>
 <param name=\"allowScriptAccess\" value=\"always\"></param>
 <param name=\"flashvars\" value=\"file=%%url_flv%%&autostart=%%player_autoplay%%&volume=80\"></param>
 <embed src=\"//www.publicdomainflicks.com/flvplayer.swf\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowscriptaccess=\"always\" allowfullscreen=\"true\" flashvars=\"file=%%url_flv%%&autostart=%%player_autoplay%%&volume=80\"/>
</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('30', 'liveleak', '/liveleak\\.com/i', 'http://www.liveleak.com/view?i=[VIDEO ID]', '0', '0', '1', '<iframe width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//www.liveleak.com/ll_embed?f=%%yt_id%%\" frameborder=\"0\" allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('32', 'supervideo', '/balsas\\.lt/i', 'http://video.balsas.lt/video/[VIDEO ID]', '0', '0', '1', '<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" codebase=\"//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0\" width=\"%%player_w%%\" height=\"%%player_h%%\">
<param name=\"allowScriptAccess\" value=\"always\" />
<param name=\"allowFullScreen\" value=\"true\" />
<param name=\"movie\" value=\"//video.balsas.lt/pimg/Site/Flash/player.swf?configFile=//video.balsas.lt/Videos/getConfig/%%yt_id%%\" />
<param name=\"quality\" value=\"high\" />
<param name=\"bgcolor\" value=\"#%%player_bgcolor%%\" />
<param name=\"flashvars\" value=\"configFile=//video.balsas.lt/Videos/getConfig/%%yt_id%%\"/>
<embed src=\"//video.balsas.lt/pimg/Site/Flash/player.swf?configFile=//video.balsas.lt/Videos/getConfig/%%yt_id%%\" quality=\"high\" bgcolor=\"#%%player_bgcolor%%\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowScriptAccess=\"always\" allowFullScreen=\"true\" type=\"application/x-shockwave-flash\" pluginspage=\"//www.macromedia.com/go/getflashplayer\" /></object>
', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('65', 'peteava', '/peteava\\.ro/i', 'http://www.peteava.ro/id-123456-video-title', '0', '0', '1', '<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" width=\"%%player_w%%\" height=\"%%player_h%%\" id=\"swf_player_id_for_ie_who_sucks\">
 <param name=\"movie\" value=\"//www.peteava.ro/static/swf/player.swf\">
 <param name=\"allowfullscreen\" value=\"true\">
 <param name=\"allowscriptaccess\" value=\"always\">
 <param name=\"menu\" value=\"false\">
 <param name=\"flashvars\" value=\"streamer=//content.peteava.ro/stream.php&file=%%yt_id%%_standard.mp4&image=//storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerstandard&hd_file=&hd_image=//storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerhigh&autostart=%%player_autoplay%%\">
 <embed src=\"//www.peteava.ro/static/swf/player.swf\" id=\"__ptv_pl_%%yt_id%%_%%player_w%%_%%player_h%%__\" name=\"__ptv_pl_%%yt_id%%_%%player_w%%_%%player_h%%__\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowscriptaccess=\"always\" menu=\"false\" allowfullscreen=\"true\" 
 flashvars=\"streamer=//content.peteava.ro/stream.php&file=%%yt_id%%_standard.mp4&image=//storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerstandard&hd_file=&hd_image=//storage2.peteava.ro/serve/thumbnail/%%yt_id%%/playerhigh&autostart=%%player_autoplay%%\"/>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('35', 'musicme', '/musicme\\.com/i', 'http://www.musicme.com/#/Patrick-Bruel/videos/Epk-Patrick-Bruel-[VIDEO ID].html', '0', '0', '1', '<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width=\"%%player_w%%\" height=\"%%player_h%%\" id=\"musicmevideo%%yt_id%%\">
 <param name=\"movie\" value=\"//www.musicme.com/_share/vplayer.swf?cb=%%yt_id%%\"></param>
 <param name=\"wmode\" value=\"%%player_wmode%%\"></param>
 <param name=\"allowScriptAccess\" value=\"always\">
 <param name=\"bgcolor\" value=\"#000000\" />
 <embed src=\"//www.musicme.com/_share/vplayer.swf?cb=%%yt_id%%\" type=\"application/x-shockwave-flash\" width=\"%%player_w%%\" height=\"%%player_h%%\" bgcolor=\"#000000\" allowScriptAccess=\"always\" wmode=\"%%player_wmode%%\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('39', 'spike', '/spike\\.com/i', 'http://www.spike.com/video/cinemassacre-top-10/[VIDEO ID]', '0', '0', '1', '<embed width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//www.spike.com/efp\" quality=\"high\" bgcolor=\"000000\" name=\"efp\" align=\"middle\" type=\"application/x-shockwave-flash\" pluginspage=\"//www.macromedia.com/go/getflashplayer\" flashvars=\"flvbaseclip=%%yt_id%%\" allowfullscreen=\"true\" wmode=\"%%player_wmode%%\">
</embed> ', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('64', 'videozer', '/videozer\\.com/i', 'http://www.videozer.com/video/abcde', '0', '0', '1', '<object id=\"player\" width=\"%%player_w%%\" height=\"%%player_h%%\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\">
 <param name=\"movie\" value=\"//www.videozer.com/embed/%%yt_id%%\"></param>
 <param name=\"allowFullScreen\" value=\"true\"></param>
 <param name=\"allowscriptaccess\" value=\"always\"></param>
 <embed src=\"//www.videozer.com/embed/%%yt_id%%\" width=\"%%player_w%%\" height=\"%%player_h%%\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('42', 'musicplayon', '/musicplayon\\.com/i', 'http://en.musicplayon.com/play?v=[VIDEO ID]Video_Title', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,15,0\">
 <param name=\"wmode\" value=\"%%player_wmode%%\"></param>
 <param name=\"movie\" value=\"//en.musicplayon.com/embed?VID=%%yt_id%%&autoPlay=N&hideLeftPanel=Y&bgColor=0x232323&activeColor=0x%%player_bgcolor%%&inactiveColor=0x3C3C3C&titleColor=0x584596&textsColor=0x999999&selectedColor=0x0F0F0F&btnColor=0x000000&rnd=288950\" />
 <param name=\"quality\" value=\"high\" />
 <param name=\"allowfullscreen\" value=\"true\" />
 <param name=\"allowscriptaccess\" value=\"always\" />
 <embed width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//en.musicplayon.com/embed?VID=%%yt_id%%&autoPlay=N&hideLeftPanel=Y&bgColor=0x232323&activeColor=0x%%player_bgcolor%%&inactiveColor=0x3C3C3C&titleColor=0x584596&textsColor=0x999999&selectedColor=0x0F0F0F&btnColor=0x000000&rnd=288950\" quality=\"high\" allowfullscreen=\"true\" allowscriptaccess=\"always\" pluginspage=\"//www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" wmode=\"%%player_wmode%%\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('44', 'quicktime', '/-(.*?)\\.(mov|m2a|m2v|3gp|3g2|m4a|m4v)/i', 'http://www.example.com/video.mov', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase= \"//www.apple.com/qtactivex/qtplugin.cab\">
 <param name=\"src\" value=\"%%url_flv%%\" />
 <param name=\"autoplay\" value=\"false\" />
 <param name=\"controller\" value=\"true\" />
 <param name=\"scale\" value=\"tofit\" />
 <param name=\"wmode\" value=\"%%player_wmode%%\"></param>
 <embed src=\"%%url_flv%%\" width=\"%%player_w%%\" height=\"%%player_h%%\" scale=\"tofit\" wmode=\"%%player_wmode%%\" autoplay=\"false\" controller=\"true\" type=\"video/quicktime\" pluginspage=\"//www.apple.com/quicktime/download/\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('45', 'yahoomusic', '/music\\.yahoo\\.com/i', 'http://new.music.yahoo.com/videos/LadyGaGa/Bad-Romance--218606963', '0', '0', '1', '<object width=\"%%player_w%%\" id=\"uvp_fop\" height=\"%%player_h%%\" allowFullScreen=\"true\">
 <param name=\"movie\" value=\"//d.yimg.com/m/up/fop/embedflv/swf/fop.swf\"/>
 <param name=\"flashVars\" value=\"%%url_flv%%\"/>
 <param name=\"wmode\" value=\"%%player_wmode%%\"/>
 <embed width=\"%%player_w%%\" id=\"uvp_fop\" height=\"%%player_h%%\" allowFullScreen=\"true\" src=\"//d.yimg.com/m/up/fop/embedflv/swf/fop.swf\" type=\"application/x-shockwave-flash\" flashvars=\"%%url_flv%%\" />
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('47', '5min', '/5min\\.com\\/video/i', 'http://www.5min.com/Video/Video-Title-[VIDEO ID]', '0', '1', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\" id=\"FiveminPlayer\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\">
 <param name=\"allowfullscreen\" value=\"true\"/>
 <param name=\"allowScriptAccess\" value=\"always\"/>
 <param name=\"movie\" value=\"//www.5min.com/Embeded/%%yt_id%%/\"/>
 <embed name=\"FiveminPlayer\" src=\"//www.5min.com/Embeded/%%yt_id%%/\" type=\"application/x-shockwave-flash\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowfullscreen=\"true\" allowScriptAccess=\"always\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('63', 'vplay', '/vplay\\.ro/i', 'http://vplay.ro/watch/abcdef/', '0', '0', '1', '<object classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\" width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"//i.vplay.ro/f/embed.swf?key=%%yt_id%%\">
 <param name=\"allowfullscreen\" value=\"true\">
 <param name=\"quality\" value=\"high\">
 <embed src=\"//i.vplay.ro/f/embed.swf?key=%%yt_id%%\" quality=\"high\" pluginspage=\"//www.macromedia.com/go/getflashplayer\" type=\"application/x-shockwave-flash\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowfullscreen=\"true\" ></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('51', 'smotri', '/smotri\\.com\\/video/i', 'http://smotri.com/video/view/?id=[VIDEO ID]', '0', '0', '1', '<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"//pics.smotri.com/scrubber_custom8.swf?file=%%yt_id%%&bufferTime=3&autoStart=false&str_lang=eng&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color_black.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml\" />
 <param name=\"allowScriptAccess\" value=\"always\" />
 <param name=\"allowFullScreen\" value=\"true\" />
 <embed src=\"//pics.smotri.com/scrubber_custom8.swf?file=%%yt_id%%&bufferTime=3&autoStart=false&str_lang=eng&xmlsource=http%3A%2F%2Fpics.smotri.com%2Fcskins%2Fblue%2Fskin_color_black.xml&xmldatasource=http%3A%2F%2Fpics.smotri.com%2Fskin_ng.xml\" quality=\"high\" allowscriptaccess=\"always\" allowfullscreen=\"true\" wmode=\"window\" width=\"%%player_w%%\" height=\"%%player_h%%\" type=\"application/x-shockwave-flash\"></embed>
 </object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('52', 'divx', '/(.*?)\\.(avi|divx|mkv)/i', 'http://www.example.com/video.avi', '0', '0', '1', '<object classid=\"clsid:67DABFBF-D0AB-41fa-9C46-CC0F21721616\" width=\"%%player_w%%\" height=\"%%player_h%%\" codebase=\"//go.divx.com/plugin/DivXBrowserPlugin.cab\"> 
<param name=\"src\" value=\"%%url_flv%%\" />
<param name=\"autoPlay\" value=\"%%player_autoplay%%\" />
<param name=\"bannerEnabled\" value=\"false\" />
<param name=\"previewImage\" value=\"%%yt_thumb%%\" />
<embed type=\"video/divx\" src=\"%%url_flv%%\" autoPlay=\"%%player_autoplay%%\" previewImage=\"%%yt_thumb%%\" bannerEnabled=\"false\" width=\"%%player_w%%\" height=\"%%player_h%%\" pluginspage=\"//go.divx.com/plugin/download/\"></embed> 
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('53', 'vbox7', '/vbox7\\.com\\/play/i', 'http://vbox7.com/play:[VIDEO ID]', '0', '1', '1', '<iframe width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//vbox7.com/emb/external.php?vid=%%yt_id%%\" frameborder=\"0\" allowfullscreen></iframe>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('77', 'cloudy.ec', '/cloudy\\.ec/i', 'http://www.cloudy.ec/v/[VIDEO ID]', '0', '0', '1', '<iframe width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//www.cloudy.ec/embed.php?id=%%yt_id%%\" frameborder=\"0\" border=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\" allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('54', 'livestream', '/livestream\\.com/i', 'http://www.livestream.com/channel_name', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\" id=\"lsplayer\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\"><param name=\"movie\" value=\"%%url_flv%%&amp;autoPlay=false\"></param><param name=\"allowScriptAccess\" value=\"always\"></param><param name=\"allowFullScreen\" value=\"true\"></param><embed name=\"lsplayer\" src=\"%%url_flv%%&amp;autoPlay=false\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowScriptAccess=\"always\" allowFullScreen=\"true\" type=\"application/x-shockwave-flash\"></embed></object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('56', 'ustream', '/ustream\\.tv/i', 'http://www.ustream.tv/channel/user', '0', '0', '1', '<object type=\"application/x-shockwave-flash\" width=\"%%player_w%%\" height=\"%%player_h%%\" data=\"//www.ustream.tv/flash/viewer.swf\">
 <param name=\"flashvars\" value=\"autoplay=true&amp;%%yt_id%%&amp;v3=true&amp;locale=en_US&amp;referrer=unknown&amp;enablejsapi=true\"/>
 <param name=\"allowfullscreen\" value=\"true\"/>
 <param name=\"allowscriptaccess\" value=\"always\"/>
 <param name=\"movie\" value=\"%%url_flv%%\"/>
 <embed flashvars=\"autoplay=true&amp;%%yt_id%%&amp;v3=true&amp;locale=en_US&amp;referrer=unknown&amp;enablejsapi=true\" src=\"//www.ustream.tv/flash/viewer.swf\" width=\"%%player_w%%\" height=\"%%player_h%%\" allowfullscreen=\"true\" allowscriptaccess=\"always\" type=\"application/x-shockwave-flash\" />
 </object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('57', 'mp3', '/(.*?)\\.mp3/i', 'http://www.example.com/file.mp3', '0', '1', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\" classid=\"clsid:D27CDB6E-AE6D-11cf-96B8-444553540000\" codebase=\"//download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,40,0\">
 <param name=\"scale\" value=\"noscale\" />
 <param name=\"allowFullScreen\" value=\"true\" />
 <param name=\"allowScriptAccess\" value=\"always\" />
 <param name=\"allowNetworking\" value=\"all\" />
 <param name=\"bgcolor\" value=\"#%%player_bgcolor%%\" />
 <param name=\"wmode\" value=\"%%player_wmode%%\" />
 <param name=\"movie\" value=\"%%player_url%%\" />
 <param name=\"flashVars\" value=\"&plugins=revolt-1&file=%%url_flv%%&type=sound&image=%%yt_thumb%%&backcolor=%%player_bgcolor%%&frontcolor=FFFFFF&autostart=%%player_autoplay%%&screencolor=000000\" />
 <embed src=\"%%player_url%%\" width=\"%%player_w%%\" height=\"%%player_h%%\" scale=\"noscale\" bgcolor=\"#%%player_bgcolor%%\" type=\"application/x-shockwave-flash\" allowFullScreen=\"true\" allowScriptAccess=\"always\" wmode=\"%%player_wmode%%\" flashvars=\"&plugins=revolt-1&file=%%url_flv%%&type=sound&image=%%yt_thumb%%&backcolor=%%player_bgcolor%%&frontcolor=FFFFFF&autostart=%%player_autoplay%%&screencolor=000000\"></embed>
</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('58', 'mynet', '/video\\.mynet\\.com/i', 'http://video.mynet.com/username/video-title/video-id/', '0', '1', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"allowfullscreen\" value=\"true\" />
 <param name=\"allowscriptaccess\" value=\"always\" />
 <param name=\"autoplay\" value=\"%%player_autoplay%%\" />
 <param name=\"wmode\" value=\"%%player_wmode%%\" />
 <param name=\"movie\" value=\"//video.mynet.com/username/video-title/%%yt_id%%.swf\" />
 <embed src=\"//video.mynet.com/username/video-title/%%yt_id%%.swf\" type=\"application/x-shockwave-flash\" wmode=\"%%player_wmode%%\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"%%player_w%%\" height=\"%%player_h%%\" autoplay=\"%%player_autoplay%%\"></embed>
</object>', 'flvplayer', '0', '0');
INSERT INTO `pm_sources` VALUES ('59', 'vidivodo', '/vidivodo\\.com/i', 'http://www.vidivodo.com/video-id/video-title', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"movie\" value=\"%%url_flv%%\" />
 <param name=\"allowfullscreen\" value=\"true\" />
 <param name=\"allowscriptaccess\" value=\"always\" />
 <param name=\"autoplay\" value=\"%%player_autoplay%%\" />
 <param name=\"wmode\" value=\"%%player_wmode%%\" />
 <param name=\"bgcolor\" value=\"#%%player_bgcolor%%\" />
 <embed src=\"%%url_flv%%\" type=\"application/x-shockwave-flash\" wmode=\"%%player_wmode%%\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"%%player_w%%\" height=\"%%player_h%%\" autoplay=\"%%player_autoplay%%\" bgcolor=\"#%%player_bgcolor%%\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('61', 'izlesene', '/izlesene\\.com/i', 'http://www.izlesene.com/video/video-title/video-id', '0', '0', '1', '<object width=\"%%player_w%%\" height=\"%%player_h%%\">
 <param name=\"allowfullscreen\" value=\"true\" />
 <param name=\"allowscriptaccess\" value=\"always\" />
 <param name=\"wmode\" value=\"%%player_wmode%%\" />
 <param name=\"bgcolor\" value=\"#%%player_bgcolor%%\" />
 <param name=\"movie\" value=\"//www.izlesene.com/embedplayer.swf?video=%%yt_id%%\" />
 <embed src=\"//www.izlesene.com/embedplayer.swf?video=%%yt_id%%\" wmode=\"%%player_wmode%%\" bgcolor=\"#%%player_bgcolor%%\" allowfullscreen=\"true\" allowscriptaccess=\"always\" menu=\"false\" width=\"%%player_w%%\" height=\"%%player_h%%\" type=\"application/x-shockwave-flash\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('62', 'videobb', '/videobb\\./i', 'http://www.videobb.com/video/video-id', '0', '0', '1', '<object id=\"player\" width=\"%%player_w%%\" height=\"%%player_h%%\" classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\">
 <param name=\"movie\" value=\"%%url_flv%%\"></param>
 <param name=\"allowFullScreen\" value=\"true\" ></param>
 <param name=\"allowscriptaccess\" value=\"always\"></param>
 <param name=\"wmode\" value=\"%%player_wmode%%\" />
 <embed src=\"%%url_flv%%\" wmode=\"%%player_wmode%%\" type=\"application/x-shockwave-flash\" allowscriptaccess=\"always\" allowfullscreen=\"true\" width=\"%%player_w%%\" height=\"%%player_h%%\"></embed>
</object>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('73', 'mail.ru', '/mail\\.ru\\/video/i', 'http://my.mail.ru/video/mail/radnovomyznakomstvy/176/254.html', '0', '0', '1', '<iframe src=\"//api.video.mail.ru/videos/embed/%%yt_id%%\" width=\"%%player_w%%\" height=\"%%player_h%%\" frameborder=\"0\" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('74', 'vk', '/vk\\.(com|ru|me)\\/video/i', 'http://vk.com/video28908630_165233143', '0', '0', '1', '<iframe src=\"//vk.com/video_ext.php?%%yt_id%%\" width=\"%%player_w%%\" height=\"%%player_h%%\" frameborder=\"0\"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('75', 'rutube', '/rutube\\.ru\\/video/i', 'http://rutube.ru/video/852e974534e3527f16810a7a19c418b0/', '0', '0', '1', '<iframe width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//rutube.ru/video/embed/%%yt_id%%\" frameborder=\"0\" webkitAllowFullScreen mozallowfullscreen allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('76', 'novamov', '/novamov\\.com/i', 'http://www.novamov.com/video/video-id', '0', '0', '1', '<iframe style=\"overflow: hidden; border: 0; width: %%player_w%%px; height: %%player_h%%px;\" src=\"//embed.novamov.com/embed.php?v=%%yt_id%%\" scrolling=\"no\"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('78', 'myvideo.ge', '/myvideo\\.ge/i', 'http://www.myvideo.ge/?video_id=[VIDEO ID]', '0', '0', '1', '<iframe width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//embed.myvideo.ge/flv_player/player.php?video_id=%%yt_id%%\" frameborder=\"0\" border=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\" allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('79', 'vevo', '/vevo\\.com/i', 'http://www.vevo.com/watch/[VIDEO ID]', '0', '0', '1', '<iframe width=\"%%player_w%%\" height=\"%%player_h%%\" src=\"//cache.vevo.com/assets/html/embed.html?video=%%yt_id%%&autoplay=0\" frameborder=\"0\" border=\"0\" marginwidth=\"0\" marginheight=\"0\" scrolling=\"no\" allowfullscreen></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('80', 'facebook', '/facebook.com/i', 'https://www.facebook.com/video.php?v=video-id', '0', '0', '1', '<iframe src=\"//www.facebook.com/video/embed?video_id=%%yt_id%%\" width=\"%%player_w%%\" height=\"%%player_h%%\" frameborder=\"0\"></iframe>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('81', 'imgur', '/(.*?)\\imgur\\.com/i', 'http://imgur.com/[ID]', '0', '1', '1', '<blockquote class=\"imgur-embed-pub\" lang=\"en\" data-id=\"%%yt_id%%\" style=\"width:%%player_w%%px !important;max-width:%%player_w%%px !important; height:%%player_h%%px\"></blockquote><script async src=\"//s.imgur.com/min/embed.js\" charset=\"utf-8\"></script>', 'embed', '0', '0');
INSERT INTO `pm_sources` VALUES ('82', 'gfycat', '/gfycat\\.com/i', 'http://gfycat.com/[ID]', '0', '1', '1', '<iframe src=\"//gfycat.com/ifr/%%yt_id%%\" frameborder=\"0\" scrolling=\"no\" width=\"%%player_w%%\" height=\"%%player_h%%\" style=\"-webkit-backface-visibility: hidden;-webkit-transform: scale(1);\" ></iframe>', 'flvplayer', '0', '0');


#
# Table structure for table `pm_tags`
#

DROP TABLE IF EXISTS `pm_tags`;
CREATE TABLE `pm_tags` (
  `tag_id` int(7) NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `tag` varchar(200) NOT NULL DEFAULT '',
  `safe_tag` varchar(200) NOT NULL DEFAULT '',
  PRIMARY KEY (`tag_id`),
  KEY `uniq_id` (`uniq_id`),
  KEY `safe_tag` (`safe_tag`)
) ENGINE=MyISAM AUTO_INCREMENT=6;

#
# Dumping data for table `pm_tags`
#

INSERT INTO `pm_tags` VALUES ('1', '14a4e06f8', 'hudson', 'hudson');
INSERT INTO `pm_tags` VALUES ('2', '14a4e06f8', 'video', 'video');
INSERT INTO `pm_tags` VALUES ('3', '14a4e06f8', 'against the grain', 'against-the-grain');
INSERT INTO `pm_tags` VALUES ('4', 'ac0266df0', 'animation', 'animation');
INSERT INTO `pm_tags` VALUES ('5', 'ac0266df0', 'stop-motion', 'stop-motion');


#
# Table structure for table `pm_temp`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_temp`
#



#
# Table structure for table `pm_users`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=2;

#
# Dumping data for table `pm_users`
#

INSERT INTO `pm_users` VALUES ('1', 'admin', '21232f297a57a5a743894a0e4a801fc3', 'Admin', 'male', '500', '127.0.0.1', '1524757198', '1524794652', '::1', 'admin@example.com', '1', '1', '', 'default.gif', '', '', '0', '0', '0', 'a:6:{s:7:\"website\";s:0:\"\";s:7:\"youtube\";s:0:\"\";s:8:\"facebook\";s:0:\"\";s:7:\"twitter\";s:0:\"\";s:9:\"instagram\";s:0:\"\";s:11:\"google_plus\";s:0:\"\";}', '', '', '1', '1', '', '0', '', '0');


#
# Table structure for table `pm_users_follow`
#

DROP TABLE IF EXISTS `pm_users_follow`;
CREATE TABLE `pm_users_follow` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `follower_id` int(10) unsigned NOT NULL DEFAULT '0',
  `date` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`user_id`,`follower_id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_users_follow`
#



#
# Table structure for table `pm_video_subtitles`
#

DROP TABLE IF EXISTS `pm_video_subtitles`;
CREATE TABLE `pm_video_subtitles` (
  `id` int(8) NOT NULL AUTO_INCREMENT,
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `language` varchar(100) NOT NULL DEFAULT '',
  `language_tag` varchar(2) NOT NULL DEFAULT '',
  `filename` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniq_id` (`uniq_id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_video_subtitles`
#



#
# Table structure for table `pm_videoads`
#

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
) ENGINE=MyISAM;

#
# Dumping data for table `pm_videoads`
#



#
# Table structure for table `pm_videos`
#

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
) ENGINE=MyISAM AUTO_INCREMENT=4;

#
# Dumping data for table `pm_videos`
#

INSERT INTO `pm_videos` VALUES ('1', '14a4e06f8', 'Hudson - Against The Grain', '<p>I\'m asking questions time and time again<br />in a race that never ever ends<br />hanging from my limbs in the swaying breeze<br />im opening I gotta let it go<br /><br />in my life the good and bad they come and go<br />highs and lows are often all that show<br />know it\'s time to go against the grain<br />or it will kill me down below<br />to live in comfort and know its warm embrace<br />reminds me only to quicken up the pace<br />know it\'s time to go against the grain<br />or it will kill me down below<br /><br />singing a song never heard<br />all of a sudden I know every word<br />And I know that there\'s no direction home<br />it\'s right here where all the wild things grow<br /><br />in my life the good and bad they come and go<br />highs and lows are often all that show<br />know it\'s time to go against the grain<br />or it will kill me down below<br />to live in comfort and know its warm embrace<br />reminds me only to quicken up the pace<br />know it\'s time to go against the grain<br />or it will kill me down below<br /><br />The new music video for \'Against The Grain\' from emerging Melbourne indie-folk artist Hudson sees him collaborate with film-maker/animator/VJ Dropbear (aka Jonathan Chong), producing a vibrant and colourful clip based around a mainstay from our humble artistic efforts throughout childhood -- coloured pencils.<br /><br />\'Against The Grain\' is the first single lifted off Hudson\'s debut EP Open Up Slowly released in May 2011</p>', 'TuBMXS6vU3o', '204', '//i.ytimg.com/vi/TuBMXS6vU3o/0.jpg', '0', '3', '1', 'admin', '1524764920', '1524757198', '8', '//www.youtube.com/watch?v=TuBMXS6vU3o', '3', '1', '0', '0', '0', '1', '0', '1', '1', 'hudson-against-the-grain');
INSERT INTO `pm_videos` VALUES ('2', 'ac0266df0', 'Post-it Stop Motion', '', 'BpWM0FNPZSs', '115', '//i.ytimg.com/vi/BpWM0FNPZSs/0.jpg', '0', '1,2', '1', 'admin', '1524764971', '1524757198', '3', '//www.youtube.com/watch?v=BpWM0FNPZSs', '3', '1', '0', '0', '0', '0', '0', '1', '1', '');
INSERT INTO `pm_videos` VALUES ('3', '9ce8ca852', 'FC Barcelona vs Real Madrid ᴴᴰ', '<p>Real Madrid vs Barcelona ᴴᴰ EN VIVO #El Cl&aacute;sico EN VIVO HD <br /> FC Barcelona vs Real Madrid ᴴᴰ EN VIVO 2018 ElCl&aacute;sico EN VIVO - HD<br /> CHAT <br /> THANK YOU</p>', '_x1H9EABUaw', '0', '//i.ytimg.com/vi/_x1H9EABUaw/mqdefault_live.jpg', '0', '2', '1', 'admin', '1524794772', '1524762407', '2', 'https://www.youtube.com/watch?v=_x1H9EABUaw', '3', '1', '0', '0', '0', '0', '0', '1', '1', 'fc-barcelona-vs-real-madrid-%e1%b4%b4%e1%b4%b0');


#
# Table structure for table `pm_videos_trash`
#

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
  `video_slug` varchar(255) NOT NULL DEFAULT '',
  `mp4` varchar(255) NOT NULL DEFAULT '',
  `direct` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  KEY `uniq_id` (`uniq_id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_videos_trash`
#



#
# Table structure for table `pm_videos_urls`
#

DROP TABLE IF EXISTS `pm_videos_urls`;
CREATE TABLE `pm_videos_urls` (
  `uniq_id` varchar(10) NOT NULL DEFAULT '',
  `mp4` varchar(200) NOT NULL DEFAULT '',
  `direct` varchar(200) NOT NULL DEFAULT '',
  UNIQUE KEY `uniq_id` (`uniq_id`)
) ENGINE=MyISAM;

#
# Dumping data for table `pm_videos_urls`
#

INSERT INTO `pm_videos_urls` VALUES ('14a4e06f8', '', 'http://www.youtube.com/watch?v=TuBMXS6vU3o');
INSERT INTO `pm_videos_urls` VALUES ('ac0266df0', '', 'http://www.youtube.com/watch?v=BpWM0FNPZSs');
INSERT INTO `pm_videos_urls` VALUES ('9ce8ca852', '', 'https://www.youtube.com/watch?v=_x1H9EABUaw');


