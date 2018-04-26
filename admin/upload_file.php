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

// Session Cookie workaround
if (isset($_POST["PHPSESSID"])) {
	session_id($_POST["PHPSESSID"]);
} else if (isset($_GET["PHPSESSID"])) {
	session_id($_GET["PHPSESSID"]);
}

session_start();

require_once('../config.php');
include_once('functions.php');
include_once( ABSPATH . 'include/user_functions.php');
include_once( ABSPATH . 'include/islogged.php');

$error_msg = '';
$allow = 1;
$ext = pm_get_file_extension($_FILES['Filedata']['name'], true);

if ( ! $conn_id)
{
	if ( ! ($conn_id = db_connect()))
	{
		$allow = 0;
	}
}

if ($_POST['doing'] == 'upload_subtitle') // Uploading subtitle form modify.php (swfuploader)
{
	$allowed_ext 	= array('.vtt', '.srt');
	$uploadDir 	= _SUBTITLES_DIR_PATH;
}
else if ($_POST['doing'] == 'upload_video') // Uploading media form modify.php (swfuploader)
{
	$allowed_ext 	= array('.flv', '.mp4', '.mov', '.wmv', '.divx', '.avi', '.mkv', 
							'.asf', '.wma', '.mp3', '.m4v', '.m4a', '.3gp', '.3g2');
	$uploadDir 	= _VIDEOS_DIR_PATH;
}
else if ($_POST['doing'] == 'upload_csv')
{
	$allowed_ext 	= array('.csv', '.txt');
	$uploadDir 	= _VIDEOS_DIR_PATH;
}
else // Uploading media form 'ADD VIDEO' modal (jquploader)
{
	$allowed_ext 	= array('.flv', '.mp4', '.mov', '.wmv', '.divx', '.avi', '.mkv', 
							'.asf', '.wma', '.mp3', '.m4v', '.m4a', '.3gp', '.3g2');
	$uploadDir 	= _VIDEOS_DIR_PATH;
}

$allowed_type 	= array('application/octet-stream');

$uploadFile = $uploadDir . basename($_FILES['Filedata']['name']);

if ( ! in_array($ext, $allowed_ext) || ! in_array($_FILES['Filedata']['type'], $allowed_type))
{
	$uploadFile = str_replace($ext, '.flv', $uploadFile);
	$allow = 0;
	
	if ( ! in_array($ext, $allowed_ext))
	{
		$error_msg = 'Bad file type. You can upload only <code>'. implode(', ', $allowed_ext) .'</code> files.';
	}
	else
	{
		$error_msg = 'Bad file type. Please use the Flash Uploader.';
	}
}

if ( ! $logged_in || ( ! is_admin() && ! is_moderator() && ! is_editor()))
{
	$allow = 0;
	$error_msg = 'You do not have permission to upload videos.';
}

if (is_moderator() && mod_cannot('manage_videos'))
{
	$allow = 0;
	$error_msg = 'You do not have permission to manage and upload videos.';
}

if ( ! is_array($_FILES['Filedata']) || $_FILES['Filedata']['size'] == 0)
{
	$allow = 0;
	$error_msg = 'No file provided. File size: 0 bytes.';
}

if( $_POST['doing'] == 'upload_subtitle' && empty($_POST['language']) )
{
	$allow = 0;
	$error_msg = 'Specify the language before assigning a subtitle to this video.';
}

if ($allow == 1 && $_FILES['Filedata']['error'] != 0)
{
	switch($_FILES['Filedata']['error'])
	{
		case UPLOAD_ERR_INI_SIZE:
			$error_msg = 'The uploaded file exceeds the upload_max_filesize directive in php.ini which is currently set at <strong>'. ini_get('upload_max_filesize') .'</strong>';
			break;

		case UPLOAD_ERR_FORM_SIZE:
			$error_msg = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML/Flash upload form.';
			break;

		case UPLOAD_ERR_PARTIAL:
			$error_msg = 'The uploaded file was only partially uploaded. Possible cause: user cancelled the upload.';
			break;

		case UPLOAD_ERR_NO_FILE:
			$error_msg = 'No file was uploaded. Please select a file first.';
			break;

		case UPLOAD_ERR_NO_TMP_DIR:
			$error_msg = 'Missing a temporary folder. Please contact your hosting provider for this issue.';
			break;

		case UPLOAD_ERR_CANT_WRITE:
			$error_msg = 'Failed to write file to disk. Please contact your hosting provider for this issue.';
			break;

		case UPLOAD_ERR_EXTENSION:
			$error_msg = 'File upload stopped by extension. A PHP extension stopped the file upload. Can\'t tell which extension caused the file upload to stop.';
			break;

		default:
			$error_msg = 'Unknown upload error.';
			break;
	}

	$allow = 0;
}

$new_name  = md5($_FILES['Filedata']['name'].time());
$new_name  = substr($new_name, 0, 8);
$new_name .= $ext;
$uploadFile = $uploadDir . $new_name;

if ($allow == 1)
{
	if ($_POST['doing'] == 'upload_csv')
	{
		if ( ! ini_get('auto_detect_line_endings')) 
		{
			ini_set('auto_detect_line_endings', '1');
		}

		include( ABSPATH . _ADMIN_FOLDER .'/class.csvimporter.php');
		
		$sql = "INSERT INTO pm_import_csv_files 
						(filename, upload_date, items_detected, items_processed, items_skipped, items_with_error, items_imported)
				VALUES  ('". secure_sql($_FILES['Filedata']['name']) ."', ". time() .", 0, 0, 0, 0, 0)";
		
		if ( ! $result = mysql_query($sql))
		{
			exit(pm_alert_error('There was an error while processing your file.<br />MySQL returned: '. mysql_error(), null, true));
		}
		
		$file_id = (int) mysql_insert_id();		
		
		$total_items_detected = 0;
		$csv_headers = array('url', 'title', 'description', 'tags', 'thumb_url', 'duration');
		
		$importer = new CsvImporter($_FILES['Filedata']['tmp_name'], $csv_headers);
		
		while ($data = $importer->get(100))
		{
			foreach ($data as $k => $item)
			{
				// discard blank lines	
				$all_empty = true;
				
				// ignore blank lines
				foreach ($item as $kk => $vv)
				{
					if (strlen($vv) > 0)
					{
						$all_empty = false; 
						break;
					}
				}
				
				if ($all_empty)
				{
					continue;
				}
				
				$sql = "INSERT INTO pm_import_csv_items 
								(file_id, video_title, description, yt_length, yt_thumb, direct, tags) 
						VALUES (". $file_id .", 
								'". secure_sql($item['title']) ."',
								'". secure_sql($item['description']) ."',
								'". secure_sql($item['duration']) ."',
								'". secure_sql($item['thumb_url']) ."',
								'". secure_sql($item['url']) ."',
								'". secure_sql($item['tags']) ."'
								)";
				if ( ! $result = mysql_query($sql))
				{
					exit(pm_alert_error('There was an error while processing your file.<br />MySQL returned: '. mysql_error(), null, true));
				}
				
				$total_items_detected++;
			}
		}
		$importer->close_source_file();
		
		$sql = "UPDATE pm_import_csv_files 
				   SET items_detected = $total_items_detected 
				WHERE file_id = ". $file_id;
		
		if ( ! $result = mysql_query($sql))
		{
			exit(pm_alert_error('There was an error while updating your database.<br />MySQL returned: '. mysql_error(), null, true));
		}
		
		@unlink($_FILES['Filedata']['tmp_name']);
				
		$html = pm_alert_success( secure_sql($_FILES['Filedata']['name']) .' was successfully uploaded. This CSV appears to have '. pm_number_format($total_items_detected) .' entries.');
		$html .= '<div class="pm-file-action"><a href="'. _URL .'/'. _ADMIN_FOLDER .'/import-csv.php?step=2&file-id='. $file_id .'" class="btn">Continue</a></div>';
		exit($html);
	}
	
	
	$move = @move_uploaded_file($_FILES['Filedata']['tmp_name'], $uploadFile);
	
	if ($move !== false)
	{
		if ($_POST['doing'] == 'upload_subtitle')
		{
			if ($_POST['uniq_id'] != '')
			{
				$languages = a_get_languages();

				$sql = "SELECT * FROM pm_video_subtitles
						WHERE language_tag = '". secure_sql($_POST['language']) ."'
						  AND uniq_id = '". secure_sql($_POST['uniq_id']) ."'";
				$result = mysql_query($sql);
				$row = mysql_fetch_assoc($result);
				mysql_free_result($result);

				if (is_array($row))
				{
					$sql = "UPDATE pm_video_subtitles
							SET filename = '". $new_name ."'
							WHERE id = '". $row['id'] ."'";
					if ( ! mysql_query($sql))
					{
						exit(pm_alert_error('Failed to update database: <code>'. mysql_error() .'</code>', null, true));
					}

					$removed = true;
					if ($row['filename'] != '' && file_exists(_SUBTITLES_DIR_PATH . $row['filename']))
					{
						$removed = unlink(_SUBTITLES_DIR_PATH . $row['filename']);
					}

					// reload subtitles list
					$html = '';
					$subtitles = a_get_video_subtitles($_POST['uniq_id']);
					foreach ($subtitles as $k => $sub)
					{
						$html .= '<li id="subtitle-'. $sub['id'] .'"><span class="pull-right">';
						$html .= '<i class="icon-download opac7"></i> <strong><a href="'. _SUBTITLES_DIR . $sub['filename'] .'" title="Download file" target="_blank">Download</a></strong>';
						$html .= '<i class="icon-trash opac7"></i> <strong><a href="" title="Delete subtitle" data-sub-id="'. $sub['id'] .'" onclick="return delete_subtitle('. $sub['id'] .')">Delete</a></strong>';
						$html .= '</span>';
						$html .= '<strong>'. ucfirst($sub['language']) .'</strong>';
						if ($sub['filename'] == $new_name)
						{
							$html .= ' <span class="label label-info">updated</span>';
						}
						$html .= '</li>';
						$html .= '<input type="hidden" name="subtitle_id[]" value="'. $sub['id'] .'" />';
					}
//
//					$html = '<li><span class="pull-right">';
//					$html .= '<i class="icon-download opac7"></i> <strong><a href="'. _SUBTITLES_DIR . $new_name .'" title="Download file" target="_blank">Download</a></strong>';
//					$html .= '</span>';
//					$html .= '<strong>'. ucfirst($languages[$_POST['language']]) .'</strong> <span class="label label-info">updated</span></li>';

					if ( ! $removed)
					{
						$html .= '<hr />';
						$html .= pm_alert_error('Could not remove <code>'. _SUBTITLES_DIR_PATH . $row['filename'] .'</code> from your server.', null, true);
					}

					exit($html);
				}
				else
				{
					$sql = "INSERT INTO pm_video_subtitles (uniq_id, language, language_tag, filename)
							VALUES ('". secure_sql($_POST['uniq_id']) ."', '". secure_sql($languages[$_POST['language']]) ."', '". secure_sql($_POST['language']) ."', '". $new_name ."')";
					if ( ! mysql_query($sql))
					{
						exit(pm_alert_error('Failed to add to database: <code>'. mysql_error() .'</code>', null, true));
					}

					// reload subtitles list
					$html = '';
					$subtitles = a_get_video_subtitles($_POST['uniq_id']);
					foreach ($subtitles as $k => $sub)
					{
						$html .= '<li id="subtitle-'. $sub['id'] .'"><span class="pull-right">';
						$html .= '<i class="icon-download opac7"></i> <strong><a href="'. _SUBTITLES_DIR . $sub['filename'] .'" title="Download file" target="_blank">Download</a></strong>';
						$html .= '<i class="icon-trash opac7"></i> <strong><a href="" title="Delete subtitle" data-sub-id="'. $sub['id'] .'" onclick="return delete_subtitle('. $sub['id'] .')">Delete</a></strong>';
						$html .= '</span>';
						$html .= '<strong>'. ucfirst($sub['language']) .'</strong>';
						if ($sub['filename'] == $new_name)
						{
							$html .= ' <span class="label label-success">uploaded</span>';
						}
						$html .= '</li>';
					}

//					$html = '<li><span class="pull-right">';
//					$html .= '<i class="icon-download opac7"></i> <strong><a href="'. _SUBTITLES_DIR . $new_name .'" title="Download file" target="_blank">Download</a></strong>';
//					$html .= '</span>';
//					$html .= '<strong>'. ucfirst($languages[$_POST['language']]) .' </strong> <span class="label label-success">uploaded</span></li>';

					exit($html);
				}
			}
			else
			{
				$error_msg = 'Missing video ID';
			}
		}

		if ($_POST['doing'] == 'upload_video')
		{
			if ($_POST['uniq_id'] != '')
			{
				$sql = "SELECT url_flv 
						FROM pm_videos 
						WHERE uniq_id = '". secure_sql($_POST['uniq_id']) ."'";
				if ($result = mysql_query($sql))
				{
					$row = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					$sql = "UPDATE pm_videos 
							SET url_flv = '". $new_name ."' 
							WHERE uniq_id = '". secure_sql($_POST['uniq_id']) ."'";
							
					mysql_query($sql);
					
					$removed = true;
					if ($row['url_flv'] != '' && file_exists(_VIDEOS_DIR_PATH . $row['url_flv']))
					{
						$removed = unlink(_VIDEOS_DIR_PATH . $row['url_flv']);
					}

					$html = '<span class="pull-right">';
					$html .= '<i class="icon-download opac7"></i> <strong><a href="'. _VIDEOS_DIR . $new_name .'" title="Download file">Download</a></strong>';
	            	$html .= '</span>';
					$html .= '<strong>'. $new_name .'</strong> <span class="label label-success">updated</span>';
					
					if ( ! $removed)
					{
						$html .= '<hr />';
						$html .= pm_alert_error('Could not remove <code>'. _VIDEOS_DIR_PATH . $row['url_flv'] .'</code> from your server.');
					}

					exit($html);
				}
				else
				{
					$error_msg = 'Could not retrieve video data.';
				}
			}
			else
			{
				$error_msg = 'Missing video ID';
			}
		}
		else 
		{
			$uploadFile = str_replace("\\", "\\\\", $uploadFile);	// IIS path fix

			$result = update_config('last_video', $uploadFile);
			if (is_array($result))
			{
				$fp = @fopen('tmp.pm', "a");
				@fwrite($fp, $uploadFile);
				@fclose($fp);
			}
		}
	}
	else if ($move === FALSE)
	{
		$error_msg = 'Could not move uploaded file to <strong>'. $uploadDir .'</strong>.';
	}	
}


if ($error_msg != '')
{
	if ($_POST['doing'] == 'upload_video')
	{
		$log_msg = 'Failed to upload file <code>' . $_FILES['Filedata']['name'] . '</code>. Error issued:<br /> ';

		$log_msg .= '<i>' . $error_msg . '</i>';

		if (strpos($error_msg, "0 bytes") !== false)
		{
			$log_msg .= '<br />To upload files larger than <strong>' . readable_filesize(get_true_max_filesize()) . '</strong>,
							you need to increase your server\'s <strong>upload_max_filesize</strong> and <strong>upload_max_filesize</strong> limits.';
			$log_msg .= '<br />You can do it yourself by reading <a href="http://help.phpmelody.com/how-to-fix-the-video-uploading-process/" target="_blank">this how-to</a>, or by contacting your hosting provider.';
			$log_msg .= '<br />Meanwhile you can upload the video(s) with an FTP client into the <strong>/uploads/videos/</strong> folder and add them to your site using the "<a href="addvideo.php">Add Video from URL</a>" page.';
		}

		log_error($log_msg, ($_POST['doing'] == 'upload_video') ? 'Upload video' : 'Upload subtitle', 1);
	}

	if (file_exists($_FILES['Filedata']['tmp_name']))
	{
		@unlink($_FILES['Filedata']['tmp_name']);
	}

	echo pm_alert_error($error_msg, array(), true);
}

exit();