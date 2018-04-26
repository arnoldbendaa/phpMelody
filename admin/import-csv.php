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
// | Copyright: (c) 2004-2015 PHPSUGAR. All rights reserved.
// +------------------------------------------------------------------------+

$showm = '2';

$step = (int) $_GET['step'];
$step = ( ! $step) ? 1 : $step;

$load_import_js = 1;
$load_swfupload = 1;
$load_swfupload_upload_image_handlers = 1;

switch ($step)
{
	case 1: // Upload file / Files table
	break;
	
	case 2: // Process Queue
	break;
	
	case 3: // Import
	
		$load_scrolltofixed = 1;
		$load_chzn_drop = 1;
		$load_tagsinput = 1;
		$load_ibutton = 1;
		$load_prettypop = 1;
		$load_lazy_load = 1;
		
	break;
}


$_page_title = 'Import from CSV file';
include('header.php');

$sources = a_fetch_video_sources();
?>

<div id="adminPrimary">
	<div class="row-fluid" id="help-assist">
		<div class="span12">
			<div class="tabbable tabs-left">
				<ul class="nav nav-tabs">
					<li class="active"><a href="#help-overview" data-toggle="tab">Overview</a></li>
					<li><a href="#help-csv-structure" data-toggle="tab">About the CSV structure</a></li>
					<li><a href="#help-onthispage" data-toggle="tab">On this page</a></li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane fade in active" id="help-overview">
						<p>From this page you can bulk-import media files from a <a href="#help-csv-structure" data-toggle="tab">pre-defined CSV file</a>. Among the use cases, we'd like to mention: migrating data from a previous database/CMS, adding external videos in bulk (i.e. videos stored in the cloud or on another server, etc.) and quickly importing a specific list of YouTube videos.</p>
						<p>The CSV import process has 3 steps: uploading the CSV file, processing it's contents and finally importing the selected videos into your database. Each of these 3 steps can be resumed.</p>
						<p>For the import to work flawlessly a specific CSV format should be used. Please review the required CSV structure in the &ldquo;<a href="#help-csv-structure" data-toggle="tab">About the CSV structure</a>&rdquo; tab before uploading any file.</p>
						<p><strong>Warning</strong>: Loading a large amount of data from a CSV might negatively impact the import process. If performance is affected, we recommend splitting very large CSV files into smaller ones. This is especially recommended if the CSV file contains external video links from sites such as: YouTube, DailyMotion and Vimeo.</p>
					</div>
					<div class="tab-pane fade" id="help-csv-structure">
						<p>PHP Melody will only process CSV files which are formatted in a specific way. Feel free to review the provided <a href="temp/phpmelody-csv-example.csv">example CSV file</a>. If you're unsure about your data, always test with a small batch beforehand.</p>
						<h5>Required CSV Structure:</h5>
						<ul class="list-unstyled">
							<li>Column 1: <strong>Video URL</strong> (<strong><em>mandatory</em></strong>). Can be anything from a local video to YouTube or DailyMotion links.</li>
							<li>Column 2: <strong>Video title</strong> (optional). Leave blank when you wish to use data from the API for YouTube, DailyMotion &amp; Vimeo videos.</li>
							<li>Column 3: <strong>Description</strong> (optional). Leave blank when you wish to use data from the API for YouTube, DailyMotion &amp; Vimeo videos.</li>
							<li>Column 4: <strong>Tags</strong> (optional). Tags must be comma separated (e.g. apple, pear, banana)</li>
							<li>Column 5: <strong>Thumbnail URL</strong> (optional). Leave blank when you wish to use data from the API for YouTube, DailyMotion &amp; Vimeo videos.</li>
							<li>Column 6: <strong>Duration</strong> (optional). Expressed in seconds; for example, the duration for a 2 minute video is <strong>120</strong> seconds.</li>
						</ul>
						<h5>Additionally, the CSV file must have the following format/properties:</h5>
						<ul class="list-unstyled">
							<li><strong>Field delimiter</strong>: , (comma)</li>
							<li><strong>Text delimiter</strong>: " (double quotes)</li>
							<li><strong>Wrap text fields in</strong>: " (double quotes)</li>
							<li><strong>Should NOT</strong> include column names on the first row (e.g. 'Url', 'Video title'). The CSV should only include rows with data.</li>
							<li><strong>UTF-8 encoding</strong> is recommended.</li>
						</ul>
						<p class="alert"><strong>You can use <a href="temp/phpmelody-csv-example.csv">this example CSV</a> as a template for your own CSV.</strong></p>
					</div>
					<div class="tab-pane fade" id="help-onthispage">
						<p>Each result is organized in a stack containing thumbnails, the video title, category, description and tags. Data such as video duration, original URL and more will be imported automatically.</p>
						<p>Youtube provides three thumbnails for each video and PHP MELODY allows you to choose the best one for your site. By default, the chosen thumbnail is the largest one, but changing it will be represented by a blue border.
						You can also do a quality control by using the video preview. Just click the play button overlaying the large thumbnail image and the video will be loaded in a window.</p>
						<p>By default none of the results is selected for import. Clicking on the top right switch from each stack will select it for importing. This is indicated by a green highlight of the stack. If you're satisfied with all the results and wish to import them all at once, you can do that as well by selecting the &quot;SELECT ALL VIDEOS” checkbox (bottom left).<br />
						Enjoy!</p>
					</div>
				</div>
			</div> <!-- /tabbable -->
		</div><!-- .span12 -->
	</div><!-- /help-assist -->
	<div class="content">
		<a href="#" id="show-help-assist">Help</a>
	
		<nav id="import-nav" class="tabbable" role="navigation">
		<h2 class="h2-import pull-left">Import from CSV file</h2>
			<ul class="nav nav-tabs pull-right">
				<li><a href="import.php" class="tab-pane">Import by Keyword</a></li>
				<li><a href="import-user.php" class="tab-pane">Import from User</a></li>
				<li class="active"><a href="import-csv.php" class="tab-pane">Import from CSV</a></li>
			</ul>
		</nav>
		<br /><br />
		
		<div class="clearfix"></div>
	
		
		<?php 
		
		load_categories();
		if (count($_video_categories) == 0) 
		{
			echo pm_alert_error('Please <a href="edit_category.php?do=add&type=video">create a category</a> first.');
		}
		 
		if ($step == 2 || $step == 3)
		{
			$file_id = (int) $_GET['file-id'];
			if ( ! $file_id)
			{
				echo pm_alert_error('Please select a file to process.');
				$step = 1;
			}
			else 
			{
				$sql = "SELECT * 
						FROM pm_import_csv_files
						WHERE file_id = $file_id";
				if ( ! $result = mysql_query($sql))
				{
					echo pm_alert_error('Could not retrieve file data.<br /><strong>MySQL Error</strong>: '. mysql_error());
					
					$step = 1;
				}
				else
				{
					$csv_file = mysql_fetch_assoc($result);
					mysql_free_result($result);
					
					if ( ! $csv_file)
					{
						echo pm_alert_error('The requested file was not found');
					
						$step = 1;
					}
				}
			}
		}
		
		if ($step == 1) : ?>

		<div class="row-fluid">
			<div class="row-fluid">
				<div class="span6">
					<h3>Upload CSV</h3>
				</div>
				<div class="span6">
					<ul class="progress-steps list-inline pull-right">
						<li class="current-step"><a href="import-csv.php"><span>1</span> Upload CSV</a></li>
						<li><span>2</span> Process</li>
						<li><span>3</span> Import</li>
					</ul>
				</div>
			</div>
			
			<form id="import-csv-upload-file-form" name="import-csv-upload-file-form" enctype="multipart/form-data" action="import-csv.php?step=1" method="post" onsubmit="return false;">
				<ol id="upload-csv-log" class="list-unstyled"></ol>
				<input type="text" name="selected-csv-filename" value="" id="" class="span5" disabled />
				<span class="btn btn-upload-csv fileinput-button">
					<span>Select &amp; Upload</span>
					<input type="file" name="file" id="upload-csv-btn" multiple />
				</span>
				<span id="debugging-output-serverdata"></span>
				<span class="import-ajax-loading-animation"><img src="img/ico-loading.gif" width="16" height="16" /></span>
				<input type="hidden" name="upload-type" value="csv" />
				<input type="hidden" name="p" value="upload" /> 
				<input type="hidden" name="do" value="upload-file" />
			</form>
		</div><!-- .row-fluid --> 
		
		<?php
		
		$total_files = count_entries('pm_import_csv_files', '', '');
		
		if ($total_files > 0)
		{
			$sql = "SELECT * 
					FROM pm_import_csv_files 
					ORDER BY file_id DESC";
			if ( ! $result = mysql_query($sql))
			{
				echo pm_alert_error('Could not retrieve files data.<br /><strong>MySQL Error</strong>: '. mysql_error());
			}
			else
			{
				?>
				<h3>Uploaded Files</h3>
				<table cellpadding="0" cellspacing="0" width="100%" class="table table-hover table-files">
					<thead>
						<tr>
							<th width="30%">File Name</th>
							<th width="18%" align="center" style="text-align:center">Upload Date</th>
							<th width="7%" align="center" style="text-align:center">Videos</th>
							<th width="7%" align="center" style="text-align:center">Processed</th>
							<th width="7%" align="center" style="text-align:center">Imported</th>
							<th width="15%" align="center" style="min-width: 190px; text-align:center">Action</th>
						</tr>
					</thead>
					<tbody>
					<?php while ($row = mysql_fetch_assoc($result)) : ?>
						<tr id="import-csv-table-row-<?php echo $row['file_id']; ?>">
							<!-- File Name -->
							<td>
								<strong><?php echo $row['filename']; ?></strong>
							</td>
							<!-- Upload Date -->
							<td align="center" style="text-align:center">
								<?php echo date('M d, Y h:i:s A', $row['upload_date']); ?>
							</td>
							<!-- Items Detected -->
							<td align="center" style="text-align:center">
									<?php echo pm_number_format($row['items_detected']); ?>
							</td>
							<!-- Processed -->
							<td align="center" style="text-align:center">
									<?php 
									$processed = ($row['items_detected'] > 0 ) ? round(($row['items_processed'] / $row['items_detected']) * 100, 0) : 0;
									$processed = ($processed > 100) ? 100 : $processed;
									?>
									<div class="progress progress-success" rel="tooltip" title="<?php echo $processed; ?>%">
										<div class="bar" style="width: <?php echo $processed; ?>%;"></div>
									</div>
							</td>
							<!-- Imported -->
							<td align="center" style="text-align:center">
									<div class="progress progress-success" rel="tooltip" title="<?php echo ($row['items_detected'] > 0) ? round(($row['items_imported'] / $row['items_detected']) * 100, 0) : 0; ?>%">
									<div class="bar" style="width: <?php echo ($row['items_detected'] > 0) ? round(($row['items_imported'] / $row['items_detected']) * 100, 0) : 0; ?>%;"></div>
									</div>
							</td>
							<!-- action -->
							<td align="center" style="text-align:center">
								<a href="import-csv.php?step=2&file-id=<?php echo $row['file_id'];?>" class="btn btn-mini" <?php if ($processed >= 100) echo 'disabled="disabled" onclick="return false;"'; ?>><?php echo ($processed > 0 && $processed < 100) ? 'Resume' : 'Process'; ?></a>
								<a href="import-csv.php?step=3&file-id=<?php echo $row['file_id'];?>" class="btn btn-mini" <?php if ($processed < 100 || ($row['items_imported'] == $row['items_detected'])) echo 'disabled="disabled"  onclick="return false;"'; ?>>Import</a>
								<a href="#" data-file-id="<?php echo $row['file_id']; ?>" class="btn btn-mini btn-danger import-csv-delete-file">Delete</a>
							</td>
						</tr>
					<?php endwhile;
					mysql_free_result($result);
				?>
				</table>
				<?php
			}
		}
		endif; // end step 1 ?>
				
		<?php if ($step == 2) :
		
			$processed = 0;
			if ($csv_file['items_detected'] > 0)
			{
				$processed = round(($csv_file['items_processed'] / $csv_file['items_detected']) * 100, 0);
				$processed = ($processed > 100) ? 100 : $processed;
			}
			
			?>
			<div class="row-fluid">
				<div class="span6">
					<h3>Processing entries</h3>
				</div>
				<div class="span6">
					<ul class="progress-steps list-inline pull-right">
						<li><a href="import-csv.php"><span>1</span> Upload CSV</a></li>
						<li class="current-step"><span>2</span> <a href="import-csv.php?step=2&file-id=<?php echo $csv_file['file_id']; ?>">Process</a></li>
						<li><span>3</span> Import</li>
					</ul>
				</div>
			</div>

			<div class="pm-csv-data">
				<div class="pm-file-data">
					<div id="import-csv-ajax-response">
						<div class="alert alert-info">
							PHP Melody will attempt to read <strong><?php echo $csv_file['filename']; ?></strong> and gather information about each media file or URL. Click &ldquo;<strong>Process entries</strong>&rdquo; to begin.
						</div>
					</div>
					<div style="width: 100%;" id="progressbar" class="progress progress-success progress-striped active hide">
						<div class="bar" style="width: <?php echo $processed; ?>%;"></div>
					</div>
					<div class="pm-file-icon">
						<span class="pm-file-entries-count"><?php echo pm_number_format($csv_file['items_detected']); ?></span>
						<img src="img/ico-file-csv.png" height="34" width="34" alt="" class="pull-right">
					</div>
					<div class="pm-file-attr">
						<ul class="list-unstyled">
							<li><strong>File</strong>: <?php echo $csv_file['filename']; ?> </li>
							<li><strong>Uploaded on</strong>: <?php echo date('M d, Y h:i:s A', $csv_file['upload_date']); ?></li>
							<li><strong>Entries available</strong>: <?php echo pm_number_format($csv_file['items_detected']); ?></li>
							<li><span class="hide" id="import-csv-eta"><strong>Time required to process</strong>: <span id="import-csv-eta-value">n/a</span></span></li>
						</ul>
					</div>
					<div class="pm-file-action">
						<input type="hidden" name="items_detected" value="<?php echo $csv_file['items_detected']; ?>" />
						<input type="hidden" name="file_id" value="<?php echo $csv_file['file_id']; ?>" />
						<button type="submit" name="process" value="Process Queue" id="import-csv-process-btn" class="btn btn-default"><?php echo ($processed > 0) ? 'Resume Process' : 'Process '. pm_number_format($csv_file['items_detected']) .' entries'; ?></button> 
						<br /><em class="import-ajax-loading-animation animated infinite fadeIn"><small>Please wait...</small></em>
					</div>
				</div>
			<div class="clearfix"></div>
			</div>


		<?php endif; // end step 2
		
		if ($step == 3) : 
		
		$items_left = $csv_file['items_detected'] - $csv_file['items_imported'];
		?>
			
			<div class="row-fluid">
				<div class="span6">
					<h3>Import from &ldquo;<?php echo $csv_file['filename']; ?>&rdquo;</h3>
				</div>
				<div class="span6">
					<ul class="progress-steps list-inline pull-right">
						<li><a href="import-csv.php"><span>1</span> Upload CSV</a></li>
						<li><a href="import-csv.php?step=2&file-id=<?php echo $csv_file['file_id']; ?>"><span>2</span> Process</a></li>
						<li class="current-step"><span>3</span> Import</li>
					</ul>
				</div>
			</div>

			<form name="import-csv-options-form" id="import-csv-options-form" action="" method="post" class="form-inline">
			<div class="pm-csv-data">
				<div class="pm-file-data">
					<div id="import-csv-ajax-response">
						<div class="alert alert-info">
							<strong><?php echo pm_number_format($items_left); ?> <?php echo ($items_left == 1) ? 'item is' : 'items are'; ?> ready to be imported</strong> into your database. Click &ldquo;<strong>Show videos</strong>&rdquo; to select the items you wish to import.
						</div>
					</div>
					<div style="width: 100%;" id="progressbar" class="progress progress-success progress-striped active hide">
						<div class="bar" style="width: <?php echo $processed; ?>%;"></div>
					</div>
					<div class="pm-file-icon">
						<span class="pm-file-entries-count"><?php echo pm_number_format($items_left); ?></span>
						<img src="img/ico-file-csv.png" height="34" width="34" alt="" class="pull-right">
					</div>
					<div class="pm-file-attr">
						<strong>Auto-complete results with category:</strong>
						<?php 
						$categories_dropdown_options = array(
														'attr_name' => 'use_this_category[]',
														'attr_id' => 'main_select_category',
														'select_all_option' => false,
														'spacer' => '&mdash;',
														'selected' => '',
														'other_attr' => 'multiple="multiple" size="3" data-placeholder="Import videos into..."',
														'option_attr_id' => 'check_ignore'
														);
						echo categories_dropdown($categories_dropdown_options);
						?>

					</div>
					<div class="pm-file-action">
						<button type="submit" name="submit" class="btn" id="import-csv-show-videos-btn" data-loading-text="Getting data...">Show videos</button>
					</div>
				</div>
			<div class="clearfix"></div>
			</div>
			
						
			<input type="hidden" name="file_id" value="<?php echo $csv_file['file_id']; ?>" />
			<select name="data_source" class="hide">
				<option value="csv" selected="selected">CSV</option>
			</select>
			<input type="hidden" name="results" value="50" />
			</form><!-- import-csv-options-form -->
			
			<hr />
			
			<div class="clearfix"></div>
			
			<form name="import-search-results-form" id="import-search-results-form" action="" method="post">
				
				<input type="hidden" name="file_id" value="<?php echo $csv_file['file_id']; ?>" />
				
				<div id="vs-grid">
					<span id="import-content-placeholder">
						
					</span><!-- #import-content-placeholder -->
				</div><!-- #vs-grid -->
				
				<div class="clearfix"></div>
				
				<div id="import-load-more-div" class="hide">
					<button id="import-csv-load-more-btn" name="import-load-more" class="btn btn-load-more">Load more</button>
				</div>
				
				<div id="stack-controls" class="row-fluid hide">
					<div class="span4" style="text-align: left;">
						<label class="checkbox import-all">
							<input type="checkbox" name="checkall" id="checkall" class="btn" /> <small>SELECT ALL VIDEOS</small>
						</label>				
					</div>
					<div class="span4">
	
					</div>
					<div class="span4">
						<div style="padding-right: 10px;">
							<span class="import-ajax-loading-animation"><img src="img/ico-loader.gif" width="16" height="16" /></span>
							<button type="submit" name="submit" class="btn btn-success btn-strong" value="Import" id="import-submit-btn" data-loading-text="Importing...">Import <span id="status"><span id="count"></span></span> videos </button>
						</div>
					</div>
				</div><!-- #stack-controls -->
				
			</form><!-- import-search-results-form -->
						
		<?php endif; // end step 3 ?>
		
		<div id="import-ajax-message-placeholder" class="hide" style="position: fixed; left: 40%; top: 60px; width: 550px; z-index: 99999;"></div>

	</div><!-- .content -->
</div><!-- .primary -->
<?php
include('footer.php');