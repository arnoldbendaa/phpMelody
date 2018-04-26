<?php
session_start();

define('IGNORE_MOBILE', true);

require('config.php');
require_once('include/functions.php');
require_once('include/user_functions.php');
require_once('include/islogged.php');

$uniq_id = $_GET['vid'];

if ( ! empty($uniq_id) && strlen($uniq_id) < 10) 
{
	$video = request_video($uniq_id);
	
	if ( ! $logged_in && $video['restricted'] == '1')
	{
		$video_is_restricted = true;
	}
}


$preroll_ad_data = serve_preroll_ad('embed', $video);
$display_preroll_ad = (is_array($preroll_ad_data)) ? true : false;

//
// ---- output ----
//  
?>
<!DOCTYPE html>
<!--[if IE 7 | IE 8]>
<html class="ie" dir="ltr" lang="en">
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html dir="ltr" lang="en">
<!--<![endif]-->
<head>
<meta charset="UTF-8" />
<meta name="robots" content="noindex">
<meta name="googlebot" content="noindex">
<title><?php echo $video['video_title'];?></title>
<link rel="canonical" href="<?php echo makevideolink($video['uniq_id'], $video['video_title'], $video['video_slug']);?>">
<style type="text/css">
html{overflow:hidden}
body{margin:0;padding:0;border:0;font-size:100%;font:12px Arial,sans-serif;background-color:#000;color:#fff;height:100%;width:100%;overflow:hidden;position:absolute;-webkit-tap-highlight-color:rgba(0,0,0,0)}
a{color:#fff}
p{padding:5px 10px}
object,embed,iframe{margin:0;padding:0;border:0;font-size:100%;background:transparent}
.full-frame{width:100%;height:100%}
h1,h2,h3,h4,h5{text-align:center;color:#fff}
#preroll_placeholder{position:relative;display:block;width:100%;text-align:center}
#preroll_placeholder embed,iframe{max-width:99%;}
.preroll_countdown{padding:3px 0}
.embed_logo img{max-width:95%}
.restricted-video{display:block;height:100%;background:url('<?php echo show_thumb($video['uniq_id'], 1, $video); ?>') no-repeat center center}
.btn {font-family: Arial, Helvetica, sans-serif;border: 1px solid #c6c6c6;outline: 0;}
.btn-success{margin:0 auto;display:block;width:130px;font-size:11px;font-weight:bold;text-align:center;text-decoration:none;padding:5px 10px;color:#fff;text-shadow:0 -1px 0 rgba(0,0,0,0.25);background-color:#77a201;border-width:2px;border-style:solid;border-color:#688e00 #688e00 #8eaf33;border-color:rgba(0,0,0,0.1) rgba(0,0,0,0.1) rgba(0,0,0,0.25);-moz-border-radius:4px;-webkit-border-radius:4px;border-radius:4px;box-shadow:0 1px 3px #000}
.btn-success:hover,.btn-success:active,.btn-success.active,.btn-success.disabled,.btn-success[disabled]{color:#fff;background-color:#8eaf33;box-shadow:none}
.btn-success:active,.btn-success.active{background-color:#3C0}
.btn-blue {  color: #fff; text-shadow: 0 1px 0 #2d8fc4; background-color: #359ad1;}
@-o-viewport{width:device-width}
@-moz-viewport{width:device-width}
@-ms-viewport{width:device-width}
@-webkit-viewport{width:device-width}
@viewport{width:device-width}
</style>

<script src="//code.jquery.com/jquery-1.11.0.min.js"></script>
<script src="//code.jquery.com/jquery-migrate-1.2.1.min.js"></script>
<?php if ($display_preroll_ad) : ?>
	<script src="<?php echo _URL; ?>/js/jquery.timer.min.js" type="text/javascript"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#video_player_container').hide();
		});
	</script>
<?php endif; ?>
</head>
<body>
	
	<?php if ( ! $video) : // video not found ?>
		<h1><?php echo $lang['sorry'];?></h1>
        <h3><?php echo $lang['video_not_found'];?></h3>
		<p>
			<?php if ($config['custom_logo_url'] != '') : ?> 
				<div class="embed_logo" align="center"><a href="<?php echo _URL;?>" target="_blank"><img src="<?php echo make_url_https($config['custom_logo_url']); ?>" alt="<?php echo htmlspecialchars(_SITENAME);?>" title="<?php echo htmlspecialchars(_SITENAME);?>" border="0"></a></div>
			<?php else : ?>
				<h3><a href="<?php echo _URL;?>" target="_blank"><?php echo _SITENAME;?></a></h3>
			<?php endif; ?>
		</p>
</body>
</html>
	<?php
			exit(); 
		endif;
	?>
	
	<?php if ($video_is_restricted) : ?>
	<div class="restricted-video">
		<h2><?php echo $video['video_title'];?></h2>
        <h3><?php echo $lang['restricted_sorry'];?></h3>
		<p>
			<a href="<?php echo makevideolink($video['uniq_id'], $video['video_title'], $video['video_slug']);?>" target="_blank" class="btn-success"><?php echo $lang['proceed'];?></a>
		</p>
	</div>
</body>
</html>
	<?php
			exit(); 
		endif;
	?>
	<?php if ($display_preroll_ad) : ?>
		<div id="preroll_placeholder">
			<div class="preroll_countdown">
				<?php echo $lang['preroll_ads_timeleft']; ?> <span class="preroll_timeleft"><?php echo $preroll_ad_data['timeleft_start'];?></span>
			</div>
			<?php echo $preroll_ad_data['code']; ?>

			<?php if ($preroll_ad_data['skip']) : ?>
				<div class="preroll_skip_countdown">
					<?php echo $lang['preroll_ads_skip_msg']; ?> <span class="preroll_skip_timeleft"><?php echo $preroll_ad_data['skip_delay_seconds']; ?></span>
				</div>
				<br />
				<button class="btn btn-blue" id="preroll_skip_btn"><?php echo $lang['preroll_ads_skip']; ?></button>
			<?php endif; ?>
			<?php if ($preroll_ad_data['disable_stats'] == 0) : ?>
				<img src="<?php echo _URL; ?>/ajax.php?p=stats&do=show&aid=<?php echo $preroll_ad_data['id']; ?>&at=<?php echo _AD_TYPE_PREROLL; ?>" width="1" height="1" border="0" />
			<?php endif; ?>
		</div>
	<?php 
		endif;
	?>
		<span id="video_player_container">
			<?php 
			
			if ( (int) $video['allow_embedding'] == 1)
			{
				$embed_code = generate_embed_code($video['uniq_id'], $video, false, 'embed');
				$embed_code = str_replace('width="'. _PLAYER_W_EMBED .'"', 'width="100%"', $embed_code);
				$embed_code = str_replace('height="'. _PLAYER_H_EMBED .'"', 'height="100%"', $embed_code);
				if ($config['video_player'] == 'jwplayer6')
				{
					$embed_code = str_replace("width: '". _PLAYER_W_EMBED ."'", "width: '100%'", $embed_code);
					$embed_code = str_replace("height: '". _PLAYER_H_EMBED ."'", "height: '100%'", $embed_code);
				}
				echo $embed_code;
			}
			else
			{
				// show the fail message and a backlink to the video
				?>
				<h3><?php echo $lang['embedding_not_allowed']; ?></h3>
				<p>
					<div class="embed_logo" align="center">
						<a href="<?php echo $video['video_href']; ?>" target="_blank"><?php echo $video['video_href']; ?></a>
					</div>
				</p>
				<?php
			}
			?>
		</span>
	<!-- Footer -->
	
	<?php if ($display_preroll_ad) : ?>
	<script type="text/javascript">
	
	function timer_pad(number, length) {
		var str = '' + number;
		while (str.length < length) {str = '0' + str;}
		return str;
	}
	
	var preroll_timer;
	var preroll_player_called = false;
	var skippable = <?php echo ($preroll_ad_data['skip'] != 1) ? 0 : 1; ?>; 
	var skippable_timer_current = <?php echo ($preroll_ad_data['skip_delay_seconds']) ? $preroll_ad_data['skip_delay_seconds'] : 0; ?> * 1000;
	
	$(document).ready(function(){
		if (skippable == 1) {
			$('#preroll_skip_btn').hide();
		}
		
		var preroll_timer_current = <?php echo $preroll_ad_data['duration']; ?> * 1000;
		
		preroll_timer = $.timer(function(){
		
			var seconds = parseInt(preroll_timer_current / 1000);
			var hours = parseInt(seconds / 3600);
			var minutes = parseInt((seconds / 60) % 60);
			var seconds = parseInt(seconds % 60);
			
			var output = "00";
			if (hours > 0) {
				output = timer_pad(hours, 2) +":"+ timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
			} else if (minutes > 0) { 
				output = timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
			} else {
				output = timer_pad(seconds, 1);
			}
			
			$('.preroll_timeleft').html(output);
			
			if (preroll_timer_current == 0 && preroll_player_called == false) {
								
				//$('#preroll_placeholder').replaceWith(video_embed_code);
				$('#preroll_placeholder').hide();
				$('#video_player_container').show();

				preroll_player_called = true;
				preroll_timer.stop();
			} else {
				preroll_timer_current -= 1000;
				if(preroll_timer_current < 0) {
					preroll_timer_current = 0;
				}
			}
		}, 1000, true);
		if (skippable == 1) {
		
			skippable_timer = $.timer(function(){
		
				var seconds = parseInt(skippable_timer_current / 1000);
				var hours = parseInt(seconds / 3600);
				var minutes = parseInt((seconds / 60) % 60);
				var seconds = parseInt(seconds % 60);
				
				var output = "00";
				if (hours > 0) {
					output = timer_pad(hours, 2) +":"+ timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
				} else if (minutes > 0) { 
					output = timer_pad(minutes, 2) +":"+ timer_pad(seconds, 2);
				} else {
					output = timer_pad(seconds, 1);
				}
				
				$('.preroll_skip_timeleft').html(output);
				
				if (skippable_timer_current == 0 && preroll_player_called == false) {
					$('#preroll_skip_btn').show();
					$('.preroll_skip_countdown').hide();
					skippable_timer.stop();
				} else {
					skippable_timer_current -= 1000;
					if(skippable_timer_current < 0) {
						skippable_timer_current = 0;
					}
				}
			}, 1000, true);
			
			$('#preroll_skip_btn').click(function(){
				preroll_timer_current = 0;
				skippable_timer_current = 0;
				
				<?php if ($preroll_ad_data['disable_stats'] == 0) : ?>
				$.ajax({
			        type: "GET",
			        url: "<?php echo _URL .'/ajax.php';?>",
					dataType: "html",
			        data: {
						"p": "stats",
						"do": "skip",
						"aid": "<?php echo $preroll_ad_data['id']; ?>",
						"at": "<?php echo _AD_TYPE_PREROLL; ?>",
			        },
			        dataType: "html",
			        success: function(data){}
				});
				<?php endif; ?>
				return false;
			});
			
		}
		
	});
	</script>
	<?php endif; ?>
</body>
</html>