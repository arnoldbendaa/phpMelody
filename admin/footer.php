
<div id="loading-large">
    <div class="spinner">
      <div class="bounce1"></div>
      <div class="bounce2"></div>
      <div class="bounce3"></div>
    </div>
    <div class="loading-msg">Please wait</div>
</div>

</div><!-- #wrapper -->
<div class="clearfix"></div>

<footer class="row-fluid" id="footer">
	<p>Powered by <a href="http://www.phpsugar.com/phpmelody.html" target="_blank">PHP Melody v<?php echo _PM_VERSION; ?></a> <?php if (version_compare($official_version, $config['version']) == 1) : ?> (<em><a href="https://www.phpsugar.com/customer/" target="_blank">Newer version available</a>!</em>) <?php endif; ?><br />
	<a href="#feedback" data-toggle="modal">Help &amp; Feedback</a> / <a href="http://www.phpsugar.com/support.html" target="_blank">Customer Care</a> / <a href="https://www.phpsugar.com/forum/" target="_blank">Support Forums</a>
    </p>
</footer>


<div class="modal-help modal hide" id="feedback" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
	<img src="img/logo.png" />
	<h3 id="myModalLabel">Help &amp; Feedback</h3>
</div>
<div class="modal-body" style="margin:0;padding:0;">
	<ul class="unstyled modal-help-list" id="feedbackTabs">
		<li><a href="http://www.phpmelody.com/feedback-survey" target="_blank"><i class="fb-sprite fb-smile icon-bg-blue"></i> <p>Take our Survey</p></a></li>
		<li><a href="http://www.phpmelody.com/feedback-features" target="_blank"><i class="fb-sprite fb-star icon-bg-green"></i> <p>New Feature Requests</p></a></li>
		<li><a href="http://www.phpsugar.com/support.html"><i class="fb-sprite fb-question icon-bg-orange"></i> <p>Need help? Chat with us</p></a></li>
		<li><a href="http://www.phpmelody.com/feedback-report" target="_blank"><i class="fb-sprite fb-bug icon-bg-red"></i> <p>Send a bug report</p></a></li>
	</ul>
</div>
<div class="modal-footer">
	<a href="#" class="btn btn-sm btn-default" data-dismiss="modal">close</a>
</div>
</div>

<div class="modal hide" id="addVideo" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 id="myModalLabel">Add Media</h3>
</div>
<div class="modal-body" style="margin:0;padding:0;">

<table cellpadding="0" cellspacing="0" width="100%" class="pm-add-tables">
  <tr>
    <td width="13%" align="center" style="text-align:center; height:60px"><div class="pm-sprite ico-add-import"></div></td>
    <td width="83%" align="left">
    <form name="search_yt_videos" action="import.php?action=search" method="post" class="form-inline">
    <input name="keyword" type="text" value="" placeholder="Search for ..." style="width:180px" id="yt_query" /> 
    <select name="data_source" style="width:110px">
    <option value="youtube" selected="selected">Youtube</option>
    <option value="dailymotion">Dailymotion</option>
    <option value="vimeo">Vimeo</option>
    </select>
    <input type="hidden" name="autofilling" value="1" />
    <input type="hidden" name="autodata" value="1" />
    <input type="hidden" name="results" value="20"> <button type="submit" name="submit" class="btn" id="searchVideos" data-loading-text="Searching...">Search</button> <span class="searchLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span>
    </form>
    </td>
  </tr>
  <tr>
    <td align="center" style="text-align:center;"><div class="pm-sprite ico-add-link"></div></td>
    <td align="left">
    <form name="add" action="addvideo.php?step=2" method="post" onSubmit="return checkFields(this);" class="form-inline">
    <input type="text" id="addvideo_direct_input" name="url" placeholder="http://" style="width:282px" /> 
    <input type="hidden" name="" value=""> 
    <button type="submit" id="addvideo_direct_submit" name="Submit" value="Step 2" class="btn">Continue</button> <span class="addLoader"><img src="img/ico-loading.gif" width="16" height="16" /></span>
    </form>
    </td>
  </tr>
  <tr>
    <td align="center" style="text-align:center;"><div class="pm-sprite ico-add-local"></div></td>
    <td align="left">
    <form name="upload-video-modal-form" id="upload-video-modal-form" enctype="multipart/form-data" action="admin-ajax.php" method="post" style="margin-bottom:0;" class="form-inline">
    	<div class="upload-file-dropzone" id="upload-video-modal-dropzone">
			<span class="btn fileinput-button">
				<span><i class="fa fa-upload"></i> Select file...</span>
				<input type="file" name="file" id="upload-video-modal-btn" />
			</span>
			<input type="hidden" name="upload-type" value="" /> 
			<input type="hidden" name="p" value="upload" /> 
			<input type="hidden" name="do" value="upload-file" />
		</div>
    </span>
    </form>
    </td>
  </tr>
</table>
</div>
</div>

<?php if($config['keyboard_shortcuts'] == 1) : ?>
<div class="modal hide fade" id="seeShortcuts" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="modal-header">
<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
<h3 id="myModalLabel">Shortcuts</h3>
</div>
<div class="modal-body" style="margin:0;padding:0;">
    <div class="row-fluid">
        <div class="span6">
		<h6>QUICK ACCESS TO PAGES</h6>
        <ul>
            <li><span class="keycombo">ALT + v</span> videos</li>
            <li><span class="keycombo">ALT + a</span> articles</li>
            <li><span class="keycombo">ALT + p</span> pages</li>
            <li><span class="keycombo">ALT + c</span> comments</li>
            <li><span class="keycombo">ALT + s</span> general settings</li>
            <li><span class="keycombo">ALT + l</span> layout settings</li>
        </ul>

        <h6>MODALS</h6>
        <ul>
            <li><span class="keycombo">c</span> Launch the 'Add Video' modal</li>
            <li><span class="keycombo">?</span> This (help) screen</li>
        </ul>
        
		<h6>LISTINGS</h6>
        <ul>
            <li><span class="keycombo">shift+a</span> select all listings (videos, comments, etc.)</li>
            <li><span class="keycombo">shift+s</span> jump to the on-page search</li>
        </ul>
        </div>
        <div class="span6">
        <h6>Within text editors</h6>
        <ul>
            <li><span class="keycombo">ctrl+z</span> Undo</li>
            <li><span class="keycombo">ctrl+y</span> Redo</li>
            <li><span class="keycombo">ctrl+b</span> Bold</li>
            <li><span class="keycombo">ctrl+i</span> Italic</li>
            <li><span class="keycombo">ctrl+u</span> Underline</li>
            <li><span class="keycombo">ctrl+1-6</span> h1-h6</li>
            <li><span class="keycombo">ctrl+7</span> p</li>
            <li><span class="keycombo">ctrl+8</span> div</li>
            <li><span class="keycombo">ctrl+9</span> address</li>
        </ul>
        </div>
    </div>
</div>
</div>
<script src="js/jquery.hotkeys.js" type="text/javascript"></script>
<?php endif; ?>
<script type="text/javascript" src="js/jquery.typewatch.js"></script>
<script src="js/bootstrap.min.js" type="text/javascript"></script>
<script src="js/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
<script src="js/jquery.ajaxmanager.js" type="text/javascript"></script>
<script src="js/jquery.cookee.js" type="text/javascript"></script>
<script src="js/jquery.ba-dotimeout.min.js" type="text/javascript"></script>
<?php if ($load_datepicker) : ?>
<script src="js/bootstrap-datepicker.js" type="text/javascript"></script>
<?php endif;?>
<?php if($load_tagsinput == 1): ?>
<script src="js/jquery.tagsinput.js" type="text/javascript"></script>
<?php endif; ?>
<script src="js/melody.js" type="text/javascript"></script>

<script type="text/javascript" src="js/vscheck.js"></script>
<script type="text/javascript">
	jQuery(function($){
		$(document).ready(function(){
			if(($.browser.msie)&(parseInt($.browser.version)<7)){
				$("img[src$='.png']").each(function(){$(this).addClass("png");});
				//$("span").each(function(){$(this).addClass("pngbg");});
			}
		});
	});
</script>
<?php if($load_colorpicker == 1): ?>
<script src="js/bootstrap-colorpicker.min.js" type="text/javascript"></script>
<?php endif; ?>
<?php if($load_tinymce == 1): ?>
<script src="js/tiny_mce/jquery.tinymce.js" type="text/javascript"></script>
<script type="text/javascript">
// Initializes all textareas with the tinymce class
$(document).ready(function () {
   $('textarea.tinymce').tinymce({
      script_url: 'js/tiny_mce/tiny_mce.js',
      disk_cache: true,
      theme : "advanced",
      skin:"cirkuit",
      language:"en",
      plugins : "pdw,autosave,fullscreen,wordcount,lists,preview,paste,directionality,media,tabfocus,autolink,spellchecker",
      theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,hr,|,formatselect,fontselect,fontsizeselect,|,pdw_toggle,",
      theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,",
      theme_advanced_buttons3 : "preview,|,forecolor,backcolor,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,advhr,|,print,|,ltr,rtl,|,media,fullscreen",
      theme_advanced_font_sizes: "12px,13px,14px,15px,16px,18px,20px",
      font_size_style_values : "12px,13px,14px,15px,16px,18px,20px",
      pdw_toggle_on : 1,
      pdw_toggle_toolbars : "2,3",
      theme_advanced_resizing : true,
      theme_advanced_resize_horizontal : false,
      relative_urls : false,
      browser_spellcheck : true,
      content_css : "css/frontend-look.css",
      paste_data_images: true,
      relative_urls : false,
      remove_script_host : false,
      convert_urls : true,
   });
});
</script>
<?php endif; ?>
<?php if ($load_jquery_ui) : ?>
<link rel="stylesheet" href="css/jquery-ui.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="js/jquery-ui.min.js"></script>
<?php endif; ?>
<?php if ($load_sortable) : ?>
<script type="text/javascript" src="js/jquery.mjs.nestedSortable.js"></script>
<?php endif; ?>
<?php if ($showm == 'mod_article' || $showm == 'mod_pages'):  ?>
<script type="text/javascript" src="js/article.js"></script>
<?php endif; ?>
<script type="text/javascript" src="<?php echo _URL; ?>/js/jquery.ui.widget.js"></script>
<script type="text/javascript" src="<?php echo _URL; ?>/js/jquery.iframe-transport.js"></script>
<script type="text/javascript" src="<?php echo _URL; ?>/js/jquery.fileupload.js"></script>
<script type="text/javascript" src="js/fileupload-handlers.js"></script>
<?php /*if($load_uniform == 1): ?>
<link rel="stylesheet" href="css/uniform.default.css" type="text/css" media="screen" charset="utf-8" />
<script src="js/jquery.uniform.min.js" type="text/javascript"></script> 
<?php endif;*/ ?>
<?php if($load_ibutton == 1): ?>
<script type="text/javascript" src="js/jquery.ibutton.js"></script>
<?php endif; ?>
<?php if($load_prettypop == 1): ?>
<link rel="stylesheet" href="css/prettyPop.css" type="text/css" media="screen" charset="utf-8" />
<script type="text/javascript" src="js/jquery.prettyPhoto.js"></script>
<?php endif; ?>
<?php if($load_scrolltofixed == 1): ?>
<script type="text/javascript" src="js/jquery-scrolltofixed-min.js"></script>
<?php endif; ?>
<script type="text/javascript" src="js/a_general.js"></script>
<?php if($load_chzn_drop == 1): ?>
<script type="text/javascript" src="js/chosen.jquery.min.js"></script>
<?php endif; ?>
<script type="text/javascript" src="js/jquery.gritter.js"></script>
<script type="text/javascript" src="<?php echo _URL; ?>/js/bootstrap-notify.min.js"></script>
<script type="text/javascript">
// Global settings for Admin Area notifications
$.notifyDefaults({
	// settings
	element: 'body',
	position: null,
	type: "info",
	allow_dismiss: true,
	newest_on_top: true,
	showProgressbar: false,
	placement: {
		from: "top", // top, bottom
		align: "right" // left, right, center
	},
	offset: {
	    x: 20,
	    y: 100
	  },
	spacing: 10,
	z_index: 1031,
	delay: 0,
	timer: 1000,
	url_target: '_blank',
	mouse_over: null,
	animate: {
		enter: 'animated fadeInDown',//'animated fadeIn',
		exit: 'animated fadeOutUpBig',//'animated fadeOut'
	},
	onShow: null,
	onShown: null,
	onClose: null,
	onClosed: null,
	template: '<div data-notify="container" class="growl alert alert-{0}" role="alert">' +
				'<button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button>' +
				'<span data-notify="icon" class="growl-icon"></span> ' +
				'<span data-notify="title" class="growl-title">{1}</span> ' +
				'<span data-notify="message" class="growl-message">{2}</span>' +
				'<div class="progress growl-progress" data-notify="progressbar">' +
					'<div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div>' +
				'</div>' +
				'<a href="{3}" target="{4}" data-notify="url"></a>' +
			'</div>',
	// PHP Melody custom settings
	PM_exitAnimationTimeout: 0 
});

var show_pm_notes = $.cookie('showNotice');
if (show_pm_notes != 'off') {
	$(document).ready(function () {
		<?php show_pm_notes(); ?>
	});
}
</script>

<?php if($load_import_js == 1): ?>
<script type="text/javascript" src="js/unserialize.jquery.latest.js"></script>
<script type="text/javascript" src="js/import.js"></script>
<?php endif; ?>


<?php if (($showm == '5' || $showm == '6') && $config['allow_emojis'] == 1) : ?>
<script src="<?php echo _URL; ?>/js/jquery.textcomplete.min.js"></script>
<script src="<?php echo _URL; ?>/js/melody.emoji.js"></script>
<?php endif; ?>

<?php if ($showm == 'cron' || $showm == 2) : ?>
<script type="text/javascript" src="js/cron.js"></script> 
<?php endif; ?>

<?php include('footer-js.php'); ?>
<?php
if (is_user_logged_in() && is_admin()) 
{
    $force = false;
    if ($_GET['forcesync'] == '1' || $_GET['forcesync'] == 'true' || $_GET['force-sync'] == '1' || $_GET['force-sync'] == 'true')
    {
        $force = true;
    }
    autosync($force);
}

if ($conn_id)
{
    mysql_close($conn_id);
}
?>
</body>
</html>

