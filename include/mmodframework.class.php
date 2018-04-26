<?php
/**
 * Melodymods.com PHP Melody Mod framework - core file
 *
 * This framework is provided free of charge for people who want to create
 * modifications for non-commercial purposes. If you do want to commercially
 * distribute plugins built on or relying on this framework, please contact us
 * at info@melodymods.com for licencing.
 *
 * You not allowed to sell this framework or any of its code, whether in
 * the original, modified or extended state without prior written permission
 * from Melodymods.com / Sano Webdevelopment registered under number 27373275
 * at the Chamber of Commerce in The Netherlands.
 *
 * Additionally you are not allowed to use this framework to create
 * and distribute any mods which mimic the functionality of any mods
 * sold by melodymods.com
 *
 * @author Dirk-jan Mollema - Melodymods.com
 * @license All rights reserved
 * @version 1.3.0 (August 6th 2015)
 * @package com.melodymods.modframework
 */

/**
 * Framework class, will be available in every file as the variable $modframework
 * @author Dirkjan
 */
class modframework {
	const version = '1.3.0';
	public $enabled = false;
	public $installed = false;
	public $debug = false;
	public $curpage = '';
	private $plugins = array();
	/**
	 * Loads the plugins from database and includes require classes
	 * @return bool success
	 */
	public function initframework(){
		$this->installed = true;
		//Check if we are in admin panel, if not, no need to load backend only plugins
		if(strpos($_SERVER['PHP_SELF'], _ADMIN_FOLDER) !== false) $load = ''; else $load = 'AND backend_only != 1 ';
		//Set current page
		$this->curpage = substr($_SERVER['PHP_SELF'],strrpos($_SERVER['PHP_SELF'],'/')+1);
		//Only load active plugins
		$sql = mysql_query('SELECT plugin FROM mm_plugins WHERE active = 1 '.$load.'ORDER BY priority ASC');
		if($sql == false){ $this->log_error('Could not load plugins: '.mysql_error()); return false; }
		if(mysql_num_rows($sql) == 0){ /*if($this->debug) echo 'Modframework: No plugins active';*/ return; }
		$this->enabled = true; //No point in enabling if no plugins exist
		while(list($plugin) = mysql_fetch_row($sql)){
			if(!preg_match('/^[a-z0-9_]+$/i',$plugin)){ $this->log_error('Security error: Invalid plugin name: '.$plugin); continue; } //Prevent hacking from database
			if(!file_exists(ABSPATH.'plugins/'.$plugin.'.class.php')){ $this->log_error($plugin.'.class.php not found in /plugins/ directory!'); continue; }
			//It is now safe to include the file
			require_once(ABSPATH.'plugins/'.$plugin.'.class.php');
			//Init config
			$this->plugins[$plugin] = new $plugin();
		}
		//Clean "cron" which runs every hour
		global $config;
		if(isset($config['mm_last_clean'])){
			if($config['mm_last_clean'] + 3600 < time()){
				$this->trigger_hook('cleancron');
				update_config('mm_last_clean', time());
			}
		}else{
			//Lock table for exclusive usage to make sure we dont insert the value twice
			mysql_query('LOCK TABLES pm_config WRITE');
			$res = mysql_query("SELECT id FROM pm_config WHERE `name` = 'mm_last_clean'");
			if(mysql_num_rows($res) == 0){
				mysql_query("INSERT INTO pm_config SET `name` = 'mm_last_clean', `value` = '0'");
			}
			mysql_query('UNLOCK TABLES');
		}
		//if($this->debug){ echo 'Modframework loaded. Plugins: '; var_dump($this->plugins); }
	}
	/**
	 * Execute hooks for all loaded plugins
	 */
	public function trigger_hook($hook){
		if(!$this->enabled) return true;
		if(!method_exists('mm_plugin', 'hook_'.$hook)){ /*trigger_error('Unknown hook: '.$hook,E_USER_ERROR);*/ $this->log_error('Unknown hook: '.$hook); return false; }
		foreach($this->plugins as $val){
			$val->{'hook_'.$hook}();
		}
	}
	/**
	 * Log Mod Framework Error - doesn't do anything for now
	 * @param str $msg
	 */
	public function log_error($msg){
		if($this->debug) echo 'Modframework ERROR: '.$msg;
		return;
	}
	/**
	 * Additional menu items for admin header
	 */
	public function show_admin_menu(){
		global $showm,$config;
		if(!$this->installed) return true;
		if($this->is_17()){
			echo '<li class="pm-menu">
	        <a href="mm_plugins.php" class="pm-menu-parent"><div class="pm-sprite ico-plugins"></div> <span>Plugins</span></a>
	        </li>';
		}else{
			echo '<li class="pm-menu active"><a href="mm_plugins.php" '.(($showm==12)? 'class="pm-menu-parent"':'').'><div class="pm-sprite ico-plugins"></div> <span>Plugins</span></a></li>';
		}
	}
	/**
	 * Returns if the given plugin is loaded
	 * @param str $plugin
	 * @return bool
	 */
	public function is_loaded($plugin){
		return isset($this->plugins[$plugin]);
	}
	/**
	 * Get config values for plugin
	 * @param str $plugin
	 * @return array
	 */
	public function get_pluginconfig($plugin){
		if(!isset($this->plugins[$plugin])) return array();
		return $this->plugins[$plugin]->config;
	}
	/**
	 * More menu items from plugin config
	 * @param str $menu
	 */
	public function admin_submenu($menu){
		if(!$this->installed) return true;
		$sql = "SELECT value FROM mm_settings WHERE setting = 'menu".$menu."'";
		$res = mysql_query($sql);
		if($res===false) return false; //Probably not yet installed
		while(list($value) = mysql_fetch_row($res)){
			$value = explode('|',$value);
			echo '<li><a href="'.$value[1].'">'.$value[0].'</a></li>';
		}
	}
	/**
	 * Checks whether the PHP Melody version is newer than or equal to 1.7
	 * @return bool
	 */
	public function is_17(){
		global $config;
		return version_compare($config['version'], '1.7','>=');
	}
	/**
	 * Check if given mod is installed
	 * @param str $modname Plugin name
	 * @return bool
	 */
	function is_installed($modname){
		$res = mysql_query("SELECT * FROM mm_plugins WHERE plugin LIKE '".secure_sql($modname)."' LIMIT 1");
		if(mysql_num_rows($res)==1) return true;
		return false;
	}
	/**
	 * Activate/deactivate a given mod
	 * Also run mod method for this
	 * @param str $modname Plugin name
	 * @param int $active Active
	 * @return bool
	 */
	function change_state($modname,$active){
		$res = mysql_query("UPDATE mm_plugins SET active = ".(int) $active." WHERE plugin LIKE '".secure_sql($modname)."' LIMIT 1");
		if($res===false) return false;
		if($active==1){
			if(!isset($this->plugins[$modname])){
				$this->plugins[$modname] = new $modname();
			}
			$this->plugins[$modname]->activate();
		}else{
			if(!isset($this->plugins[$modname])){
				if(file_exists(ABSPATH.'plugins/'.$modname.'.class.php')){
					@include(ABSPATH.'plugins/'.$modname.'.class.php');
					$this->plugins[$modname] = new $modname();
				}else{
					return true;
				}
			}
			$this->plugins[$modname]->deactivate();
		}
	}
}
/**
 * Abstract class for plugins. Defines methods all plugins can implement
 * @author Dirkjan
 * @package com.melodymods.modframework
 */
abstract class mm_plugin {
	public $config = array();
	protected $sqlinstall = array();
	public $ignoresqlerrors = array();
	/**
	 * Licence this mod uses - recognized by installer.
	 * Melodymods.com official mods licences are distributed with the installer.
	 * To use your own licence, set this property to 'custom' and fill in the licence text below.
	 * To allow the user to install the mod without agreeing to the licence, use 'none' or leave the property empty.
	 * @var str
	 */
	public $licence = '';
	/**
	 * Full licence text associated with this mod.
	 * Will only be displayed if the property $this->licence is set to 'custom' above
	 * @var str
	 */
	public $licencetext = '';
	/**
	 * Installation steps required for this mod, will be recognized by installer and passed to the install() function
	 * @var int
	 */
	public $install_steps=1;
	/**
	 * Array of mod names that are required for this mod to run
	 * @var array
	 */
	public $requires_mods = array();
	/**
	 * Array of file names (relative to site root) that are required for this mod to install
	 * @var array
	 */
	public $requires_files = array();
	/**
	 * Minimum version of the modframework required for the mod to run
	 * @var str
	 */
	public $framework_minversion = '1.0.0';
	/**
	 * Constructor
	 * Load the config of the plugin here and perform initialization work
	 */
	abstract function __construct($autorun=true);
	/**
	 * Function which installs the script
	 * @param int $step Installation step
	 * @param str $error
	 * @return bool|int Next number step or true/false for completion/failure
	 */
	abstract function install($step,&$error);
	/**
	 * Load configuration for plugin
	 * @return bool
	 */
	public function load_config($reload=false){
		if(!empty($this->config) && $reload !== true) return; //No need to reload config if its already loaded 
		$res = mysql_query("SELECT * FROM mm_settings WHERE plugin = '".get_class($this)."'");
		if(mysql_num_rows($res)==0) return true;
		while($obj = mysql_fetch_object($res)){
			$this->config[$obj->setting] = $obj->value;
		}
		return true;
	}
	/**
	 * Is this mod installed already?
	 * @return bool
	 */
	public function is_installed(){
		$res = mysql_query("SELECT * FROM mm_plugins WHERE plugin LIKE '".get_class($this)."' LIMIT 1");
		if(mysql_num_rows($res)==1) return true;
		return false;
	}
	/**
	 * Log Plugin Error
	 * @param string $msg
	 */
	protected function log_error($msg){
		echo 'Plugin '.__CLASS__.' ERROR: '.$msg;
		return;
	}
	/**
	 * Perform mysql queries for installing the script
	 * @param str $error
	 * @param int $startat
	 */
	public function install_sql(&$error,$startat=0){
		foreach($this->sqlinstall as $key => $val){
			if($key<$startat) continue;
			$res = mysql_query($val);
			if($res === false){
				$errno = mysql_errno();
				if(!in_array($errno,$this->ignoresqlerrors)){
					$error.='Error at query '.$val.'<br />Mysql said: '.mysql_error().'<br />(Error '.$errno.')';
					return false;
				}
			}
		}
		return true;
	}
	/**
	 * Uninstall the plugin.
	 * Can be overridden by individual plugins for custom uninstall script
	 * By default reads the install queries to find added tables by the plugin, and removes those
	 * @return bool
	 */
	public function uninstall(){
		$createregex = '/CREATE\s+TABLE(\s+IF\s+NOT\s+EXISTS)?\s+`?([a-zA-Z0-9_-]+)`?/i';
		foreach($this->sqlinstall as $key => $val){
			if(preg_match($createregex,$val,$matches)){
				$table = $matches[2];
				if(substr($table,0,3) == 'pm_') continue; //Do not drop tables with the pm_ prefix
				mysql_query('DROP TABLE '.$table);
			}
		}
	}
	/*
	 * These functions are triggered if the plugin is activated/deactivated
	 */
	public function activate(){return;}
	public function deactivate(){return;}
	/*
	 * Start hooks - just empty functions
	 * The only reason they are here is to make sure that every plugin has them
	 */
	//Detail page
	public function hook_detail_top(){return;} //Detail page, before execution
	public function hook_detail_mid(){return;} //Detail page, after video is fetched from database
	public function hook_detail_bottom(){return;} //Detail page, before template display
	//Index page
	public function hook_index_top(){return;} //Index page, after file inclusions
	public function hook_index_bottom(){return;} //Index page, before template display
	//Register
	public function hook_register_top(){return;} //Register page, before code
	public function hook_register_fields(){return;} //Register page, process fields
	public function hook_register_done_active(){return;} //Register page, registration done, account active
	public function hook_register_done_activation(){return;} //Register page, registration done, account needs confirmation
	public function hook_register_done_display(){return;} //Register page, registration done, before success page display
	public function hook_register_show_form(){return;} //Register page, show registration form
	//Login page - Includes $dobreak
	public function hook_login_login_pre(){return;} //Login page, before login handler
	public function hook_login_login_mid(){return;} //Login page, valid post data, before logging in
	public function hook_login_login_post(){return;} //Login page, after login handler
	public function hook_login_login_show(){return;} //Login page, show login form
	public function hook_login_logout(){return;} //Login page, logout function
	public function hook_login_forgotpass_pre(){return;} //Login page, forgot pass, before
	public function hook_login_forgotpass_send(){return;} //Login page, forgot pass, before sending mail
	public function hook_login_activate_pre(){return;} //Login page, before activate account
	public function hook_login_activate_post(){return;} //Login page, after activate account
	public function hook_login_pwdreset_post(){return;} //Login page, after password reset
	//Edit profile
	public function hook_edit_profile_pre(){return;} //Edit profile page, before updating
	public function hook_edit_profile_sql(){return;} //Edit profile page, in sql query
	public function hook_edit_profile_post(){return;} //Edit profile page, after updating
	public function hook_edit_profile_display(){return;} //Edit profile page, before page display
	//Show profile
	public function hook_user_profile_display(){return;} //User profile page, before page display
	//Top videos
	public function hook_topvideos_top(){return;} //Top videos, before code
	public function hook_topvideos_bottom(){return;} //Top videos, before page display
	//New videos
	public function hook_newvideos_top(){return;} //New videos, before code
	public function hook_newvideos_bottom(){return;} //New videos, before page display
	//Category page
	public function hook_category_top(){return;} //Browse category page, before code
	public function hook_category_mid_1(){return;} //Browse category page, before loading categories
	public function hook_category_mid_2(){return;} //Browse category page, before videos query
	public function hook_category_mid_3(){return;} //Browse category page, after videos query
	public function hook_category_mid_4(){return;} //Browse category page, after items generation
	public function hook_category_bottom(){return;} //Browse category page, before page display
	//Suggest page
	public function hook_suggest_top(){return;} //Suggest page, before code
	public function hook_suggest_validate(){return;} //Suggest page, validate submitted suggestion
	public function hook_suggest_check(){return;} //Suggest page, check url in db
	/**
	 * @deprecated
	 */
	public function hook_suggest_insert(){return;} //Suggest page, before inserting in DB
	/**
	 * @deprecated
	 */
	public function hook_suggest_added(){return;} //Suggest page, after inserting in DB
	public function hook_suggest_bottom(){return;} //Suggest page, before page display
	//Upload page
	public function hook_upload_top(){return;} //Upload page, before code
	public function hook_upload_start(){return;} //Upload page, start processing uploaded file
	public function hook_upload_thumb_before(){return;} //Upload page, before thumbnail processing
	public function hook_upload_thumb_after(){return;} //Upload page, after thumbnail processing
	public function hook_upload_moveupload(){return;} //Upload page, before moving uploaded file
	public function hook_upload_insertvideo_before(){return;} //Upload page, before inserting video into DB (temp table)
	public function hook_upload_insertvideo_after(){return;} //Upload page (ajax.php!), after inserting video into DB (temp table)
	public function hook_upload_insertvideo_autoapprove_after(){return;} //Upload page, after inserting video into DB when autoapprove is enabled
	public function hook_upload_insertvideo_updatetempdata(){return;} //Upload page, after updating temp table data
	public function hook_upload_bottom(){return;} //Upload page, before page display
	//Ajax
	public function hook_ajax_top(){return;} //Ajax.php, before code
	public function hook_ajax_show_comments(){return;} //Ajax.php, show comments
	public function hook_ajax_show_comments_output(){return;} //Ajax.php, before comments output
	public function hook_ajax_onpage_delete_comment(){return;} //Ajax.php, delete comment
	public function hook_ajax_request_video(){return;} //Ajax.php, get video info
	public function hook_ajax_report_video(){return;} //Ajax.php, report video
	public function hook_ajax_favorites_request(){return;} //Ajax.php, request favorites video
	public function hook_ajax_favorites_request_output(){return;} //Ajax.php, request favorites, before output
	public function hook_ajax_favorites_delete(){return;} //Ajax.php, request favorites
	public function hook_ajax_request_player(){return;} //Ajax.php, request video player after Preroll ad
	/**
	 * @deprecated
	 */
	public function hook_ajax_detail_show_more_best(){return;} //Ajax.php, detail page, related best in category videos
	/**
	 * @deprecated
	 */
	public function hook_ajax_detail_show_more_artist(){return;} //Ajax.php, detail page, related same artist videos
	/**
	 * @deprecated
	 */
	public function hook_ajax_detail_show_more_related(){return;} //Ajax.php, detail page, related videos
	//Favorites
	public function hook_favorites_top(){return;} //Myfavorites.php, before code
	public function hook_favorites_videoloop(){return;} //Myfavorites.php, inside loop that iterates over videos
	public function hook_favorites_bottom(){return;} //Myfavorites.php, before page display
	//Logged in hooks
	public function hook_islogged_islogged(){return;} //Islogged.php, triggered when user is logged in after getting data
	public function hook_islogged_bottom(){return;} //Islogged.php, always triggered when this file is included
	//Article read functions (Since PHP Melody 2.1 / Modframework 1.1.6)
	public function hook_article_read_top(){return;}
	public function hook_article_read_bottom(){return;}

	//Admin panel
	public function hook_admin_header(){return;}//Admin panel header
	public function hook_admin_menu(){return;}//Admin panel menu additional item
	//Add video page
	public function hook_admin_addvideo_input(){return;}//Admin panel, form custom input fields hook
	public function hook_admin_addvideo_publishoptions(){return;}//Admin panel, form custom input fields hook (publish part, no separate widget)
	public function hook_admin_addvideo_step2_pre(){return;}//Admin panel, addvideo page, before step 2
	public function hook_admin_addvideo_step2_mid(){return;}//Admin panel, addvideo page, middle step 2
	public function hook_admin_addvideo_step2_post(){return;}//Admin panel, addvideo page, end step 2
	public function hook_admin_addvideo_step3_pre(){return;}//Admin panel, addvideo page, before step 3
	public function hook_admin_addvideo_step3_mid(){return;}//Admin panel, addvideo page, before thumbnail check/processing
	public function hook_admin_addvideo_step3_pre_video(){return;}//Admin panel, addvideo page, before inserting video in database
	public function hook_admin_addvideo_step3_post_video(){return;}//Admin panel, addvideo page, after successfully inserting new video
	public function hook_admin_addvideo_step3_final(){return;}//Admin panel, addvideo page, final hook before displaying 'video posted' message
	//Modify page
	public function hook_admin_modify_file_pre(){return;}//Admin panel, modify page, first file hook
	public function hook_admin_modify_save_start(){return;}//Admin panel, modify page, before input validation
	public function hook_admin_modify_save_pre_save(){return;}//Admin panel, modify page, before save sql query generation
	public function hook_admin_modify_save_pre_save_final(){return;}//Admin panel, modify page, before save sql query executions
	public function hook_admin_modify_save_post_save(){return;}//Admin panel, modify page, after succesful save
	public function hook_admin_modify_fields(){return;}//Admin panel, modify page, custom fields
	public function hook_admin_modify_publishfields(){return;}//Admin panel, modify page, custom fields (publish part, no separate widget)
	//Embed video page
	public function hook_admin_embed_top(){return;}//Admin panel, embed page, before code
	public function hook_admin_embed_add_start(){return;}//Admin panel, embed page, start adding movie
	public function hook_admin_embed_uploadthumb(){return;}//Admin panel, embed page, upload/download thumbnail
	public function hook_admin_embed_insertvideo_pre(){return;}//Admin panel, embed page, before inserting video
	public function hook_admin_embed_insertvideo_post(){return;}//Admin panel, embed page, after inserting video
	public function hook_admin_embed_insert_final(){return;}//Admin panel, embed page, before inserting video
	public function hook_admin_embed_input(){return;}//Admin panel, embed page, custom fields
	public function hook_admin_embed_publishfields(){return;}//Admin panel, embed page, custom fields (publish part, no separate widget)
	//Edit user profile
	public function hook_admin_edituser_top(){return;}//Admin panel, edit user, before code
	public function hook_admin_edituser_validate(){return;}//Admin panel, edit user, input validation
	public function hook_admin_edituser_sqlinsert(){return;}//Admin panel, edit user, query generation
	public function hook_admin_edituser_done(){return;}//Admin panel, edit user, after user updated
	public function hook_admin_edituser_fieldsinject(){return;}//Admin panel, edit user, profile fields
	//Streamvideo page (Since PHP Melody 2.0)
	public function hook_admin_streamvideo_top(){return;}//Admin panel, streamvideo page, before code
	public function hook_admin_streamvideo_add_start(){return;}//Admin panel, streamvideo page, start adding movie
	public function hook_admin_streamvideo_uploadthumb(){return;}//Admin panel, streamvideo page, upload/download thumbnail
	public function hook_admin_streamvideo_insertvideo_pre(){return;}//Admin panel, streamvideo page, before inserting video
	public function hook_admin_streamvideo_insertvideo_post(){return;}//Admin panel, streamvideo page, after inserting video
	public function hook_admin_streamvideo_insert_final(){return;}//Admin panel, streamvideo page, before inserting video
	public function hook_admin_streamvideo_input(){return;}//Admin panel, streamvideo page, custom fields
	public function hook_admin_streamvideo_publishfields(){return;}//Admin panel, streamvideo page, custom fields (publish part, no separate widget)
	//Import page (Since PHP Melody 2.0.1 / Framework 1.1.3)
	public function hook_admin_import_importopts(){return;}
	public function hook_admin_import_insertvideo_pre(){return;}
	public function hook_admin_import_insertvideo_post(){return;}
	//Add Article (Since PHP Melody 2.1 / Framework 1.1.6)
	public function hook_admin_article_fields(){return;}
	public function hook_admin_article_publishfields(){return;}
	public function hook_admin_article_insert_before(){return;}
	public function hook_admin_article_insert_after(){return;}
	public function hook_admin_article_update_before(){return;}
	public function hook_admin_article_update_after(){return;}
	//Approve video (Since PHP Melody 2.2 / Framework 1.1.8)
	public function hook_admin_approve_top(){return;}//Admin panel, approve page, top
	public function hook_admin_approve_insert_before(){return;}//Admin panel, approve page, before inserting video (can be called multiple times when approveall is selected)
	public function hook_admin_approve_insert_after(){return;}//Admin panel, approve page, after inserting video (can be called multiple times when approveall is selected)
	public function hook_admin_approve_insert_final(){return;}//Admin panel, approve page, after inserting video and handling thumbnail and tags (can be called multiple times when approveall is selected)
	public function hook_admin_approve_edit_top(){return;}//Admin panel, approve page, top
	public function hook_admin_approve_edit_insert_pre(){return;}//Admin panel, approve edit page, start of inserting video
	public function hook_admin_approve_edit_insert_before(){return;}//Admin panel, approve edit page, before inserting video 
	public function hook_admin_approve_edit_insert_after(){return;}//Admin panel, approve edit page, after inserting video 
	public function hook_admin_approve_edit_insert_final(){return;}//Admin panel, approve edit page, after inserting video and handling thumbnail and tags
	public function hook_admin_approve_edit_input(){return;}//Admin panel, approve edit page, custom fields
	public function hook_admin_approve_edit_publishoptions(){return;}//Admin panel, approve edit page, custom fields (publish part, no separate widget)
	//Ads system (Since PHP Melody 2.2 / Framework 1.1.9)
	public function hook_admin_classic_ads_options(){return;} //Admin panel, classic ads, custom fields (Adding ad)
	public function hook_admin_classic_ads_editoptions(){return;}//Admin panel, classic ads, custom fields (Editing ad)
	public function hook_admin_classic_ads_editoptions_backup(){return;}//Admin panel, classic ads, custom fields (Editing ad popup)
	public function hook_admin_classic_ads_addnew_before(){return;}//Admin panel, classic ads, before adding new ad
	public function hook_admin_classic_ads_addnew_mid(){return;}//Admin panel, classic ads, before adding new ad to database
	public function hook_admin_classic_ads_addnew_after(){return;}//Admin panel, classic ads, after adding new ad
	public function hook_admin_classic_ads_edit_before(){return;}//Admin panel, classic ads, before editing ad
	public function hook_admin_classic_ads_edit_mid(){return;}//Admin panel, classic ads, before editing ad in database
	public function hook_admin_classic_ads_edit_after(){return;}//Admin panel, classic ads, after editing ad
	public function hook_admin_classic_ads_delete(){return;}//Admin panel, classic ads, before deleting ad
	public function hook_admin_classic_ads_activate_deactivate(){return;}//Admin panel, classic ads, before activating/deactivating ad
	//Preroll static ads (Since PHP Melody 2.2 / Framework 1.1.9)
	public function hook_admin_preroll_static_add_options(){return;}//Admin panel, preroll static ads, custom fields (Adding ad)
	public function hook_admin_preroll_static_add_before(){return;}//Admin panel, preroll static ads, before adding new ad
	public function hook_admin_preroll_static_add_after(){return;}//Admin panel, preroll static ads, after adding new ad
	public function hook_admin_preroll_static_edit_before(){return;}//Admin panel, preroll static ads, before editing ad
	public function hook_admin_preroll_static_edit_after(){return;}//Admin panel, preroll static ads, after editing ad
	public function hook_admin_preroll_static_activate_deactivate(){return;}//Admin panel, preroll static ads, before activating/deactivating
	public function hook_admin_preroll_static_delete(){return;}//Admin panel, preroll static ads, delete ad 
	public function hook_admin_preroll_static_edit_options(){return;}//Admin panel, preroll static ads, custom fields (Editing ad)
	//Suggest page ajax (Since PHP Melody 2.3 / Framework 1.2.0)
	public function hook_suggest_ajax_insert_before(){return;}//Ajax, suggest page, before inserting video
	public function hook_suggest_ajax_autoapprove_insert_before(){return;}//Ajax, suggest page, before inserting autoapproved video
	public function hook_suggest_ajax_autoapprove_insert_after(){return;}//Ajax, suggest page, after inserting autoapproved video
	public function hook_suggest_ajax_inserttemp_before(){return;}//Ajax, suggest page, before inserting video for approval
	public function hook_suggest_ajax_inserttemp_after(){return;}//Ajax, suggest page, after inserting video for approval
	//Upload page additions (Since PHP Melody 2.3 / Framework 1.2.0)
	public function hook_upload_insertvideo_autoapprove_before(){return;}
	//Preroll static ads hooks (Since PHP Melody 2.3 / Framework 1.2.0)
	public function hook_get_preroll_ad_sql(){return;}//Get static preroll ads, before sql query
	public function hook_serve_preroll_ad_sql(){return;}//Serve static preroll ads, before sql query
	//Preroll video ads (Since PHP Melody 2.3 / Framework 1.2.0)
	public function hook_admin_videoads_add_options(){return;}//Admin panel, preroll video ads, custom fields (Adding ad)
	public function hook_admin_videoads_edit_options(){return;}//Admin panel, preroll video ads, custom fields (Editing ad)
	public function hook_admin_videoads_reset(){return;}//Admin panel, preroll video ads, reset ad statistics
	public function hook_admin_videoads_activate_deactivate(){return;}//Admin panel, preroll video ads, before activating/deactivating
	public function hook_admin_videoads_delete(){return;}//Admin panel, preroll video ads, delete ad 
	public function hook_admin_videoads_add_before(){return;}//Admin panel, preroll video ads, before adding ad
	public function hook_admin_videoads_add_after(){return;}//Admin panel, preroll video ads, after adding ad
	public function hook_admin_videoads_edit_before(){return;}//Admin panel, preroll video ads, before editing ad
	public function hook_admin_videoads_edit_after(){return;}//Admin panel, preroll video ads, after editing ad 
	//Clean function (Since PHP Melody 2.4 / Framework 1.3.0)
	public function hook_cleancron(){return;}//Clean "cron", is run every hour to allow plugins to clear caches
}
/**
 *
 * Framework manager
 * includes functions for managing plugins and install/change settings
 * @author Dirkjan
 * @package com.melodymods.modframework
 */
class frameworkmanager {
	public function getactiveplugins(){
		$res = mysql_query('SELECT * FROM mm_plugins');
		if($res == false && mysql_errno()==1146){
			echo '<tr><td colspan="3">Framework is not yet installed. <a href="mm_plugins.php?installframework">Click here to install</a></td></tr>';
			return false;
		}
		if(mysql_num_rows($res)==0) return 0;
		while($obj = mysql_fetch_object($res)){
			if($obj->active==1){
				echo '<tr><td align="center" style="text-align:center"><span class="label label-success">active</span></td><td><strong>'.$obj->plugin_name.'</strong>
				<td align="center" class="table-col-action" style="text-align:left; width: 90px;"><form method="post" action="mm_plugins.php?do=deactivate" style="margin:0px;" class="'.$obj->plugin.'_act"><input type="hidden" name="plugin" value="'.$obj->plugin.'" /><a href="javascript: void(0);" onClick="actplugin(\''.$obj->plugin.'\');" rel="tooltip" title="Deactivate this plugin" class="btn btn-mini btn-link"><i class="icon-remove-sign"></i></a>';

			}else{
				echo '<tr><td align="center" style="text-align:center"><span class="label">inactive</span></td><td><em>'.$obj->plugin_name.'</em>
				<td align="center" class="table-col-action" style="text-align:left; width: 90px;"><form method="post" action="mm_plugins.php?do=activate" style="margin:0px;" class="'.$obj->plugin.'_act"><input type="hidden" name="plugin" value="'.$obj->plugin.'" /><a href="javascript: void(0);" onClick="actplugin(\''.$obj->plugin.'\');" rel="tooltip" title="Activate this plugin" class="btn btn-mini btn-link"><i class="icon-ok-sign"></i></a>';
			}
			if($this->has_settings($obj->plugin)){
				echo '<a href="mm_settings.php?mod='.$obj->plugin.'" class="btn btn-mini btn-link" rel="tooltip" title="Settings"><i class="icon-wrench"></i></a>';
			}else{
				echo '<span class="spacing"></span>';
			}

			echo '<a href="mm_uninstall.php?mod='.$obj->plugin.'" class="btn btn-mini btn-link" rel="tooltip" title="Uninstall"><i class="icon-trash"></i></a>';

			echo '</form></td></tr>';
		}
		return true;
	}
	public function has_settings($pluginname){
		$res = mysql_query("SELECT count(*) FROM mm_settings WHERE plugin = '".secure_sql($pluginname)."'");
		$row = mysql_fetch_row($res);
		return $row[0] > 0;		
	}
	public function uninstall($pluginname,&$error){
		$res1 = mysql_query("DELETE FROM mm_settings WHERE plugin = '".secure_sql($pluginname)."'");
		$res2 = mysql_query("DELETE FROM mm_plugins WHERE plugin = '".secure_sql($pluginname)."'");
		return $res1 !== false && $res2 !== false;
	}
	public function install(&$error){
		global $config;
		$res = mysql_query("CREATE TABLE IF NOT EXISTS `mm_plugins` (
  `plugin` varchar(30) NOT NULL,
  `plugin_name` varchar(40) NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `priority` tinyint(3) unsigned NOT NULL DEFAULT '10',
  `backend_only` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`plugin`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		if($res===false) $error.=mysql_error();
		$res2= mysql_query("CREATE TABLE IF NOT EXISTS `mm_settings` (
  `plugin` varchar(30) NOT NULL,
  `setting` varchar(60) NOT NULL,
  `value` varchar(300) NOT NULL,
  `editable` tinyint(1) NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `valid` VARCHAR( 40 ),
  UNIQUE KEY `plugin` (`plugin`,`setting`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8");
		//Update PM config
		if(isset($config['mm_framework'])){
			update_config('mm_framework',1,true);
		}else{
			//Insert
			mysql_query("INSERT INTO pm_config (`name`,`value`) VALUES ('mm_framework','1')");
		}
		if($res2===false) $error.=mysql_error();
		return ($res && $res2);
	}
	public function getinstallable(){
		//Read /plugins/ directory for plugins
		if(!is_dir(ABSPATH.'plugins/')){
			echo '<tr><td><div class="alert alert-error">You need to create a /plugins/ directory in your site root!</div></td></tr>';
			return false;
		}
		$dir = opendir(ABSPATH.'plugins/');
		$found=false;
		while(true){
			$file = readdir($dir);
			if($file===false) break;
			if(filetype(ABSPATH.'plugins/'.$file) != 'file') continue;
			if(preg_match('/^([a-z0-9_-]+)\.class\.php$/i',$file,$matches)){
				//Valid plugin
				$res = mysql_query("SELECT count(*) FROM mm_plugins WHERE plugin = '".secure_sql($matches[1])."'");
				if($res == false && mysql_errno()==1146){
					//Framework not installed, exit this function. The next function will tell users to install it
					return false;
				}
				list($count) = mysql_fetch_row($res);
				if($count==0){
					$found=true;
					echo '<tr><td><strong>'.$matches[1].'</strong></td><td><form method="post" style="margin:0px" action="mm_install.php"><input type="hidden" name="plugin" value="'.$matches[1].'" /><input type="submit" class="btn btn-small btn-success" value="Install this plugin" /></form></td></tr>';
				}
			}
		}
		if($found!=true) return false;
	}
	public function getversion(){
		$url='http://melodymods.com/framework/version.txt';
		if (ini_get('allow_url_fopen') == 1)
		{
			$content = @file_get_contents($url);
		} else
		{
		   $ch = @curl_init();
		   @curl_setopt($ch, CURLOPT_URL, $url);
		   @curl_setopt($ch, CURLOPT_HEADER, 0);
		   @curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		   @curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0');
		   $content = @curl_exec($ch);
		   @curl_close($ch);
		}

		return $content;
	}
}