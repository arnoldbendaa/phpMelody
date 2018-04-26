<?php

define('CRON_STATE_READY', 'ready');
define('CRON_STATE_BUSY', 'busy');
define('CRON_STATE_LOCK', 'lock');
define('CRON_STATE_ERROR', 'error');
define('CRON_STATUS_LIVE', 'live');
define('CRON_STATUS_PAUSED', 'paused');
define('CRON_STATUS_STOPPED', 'stopped');

if ( ! defined('DAY_IN_SECONDS'))
	define('DAY_IN_SECONDS', 86400);

if ( ! defined('WEEK_IN_SECONDS'))
	define('WEEK_IN_SECONDS', 604800);

if ( ! defined('MONTH_IN_SECONDS'))
	define('MONTH_IN_SECONDS', 2592000);

/**
 * 
 * @param array $job
 * @return bool|int false on failure, mysql_insert_id on success
 */
function add_cron_job($job)
{
	$defaults = array('name' => date('F j, Y g:i A'),
					  'type' => '',
					  'status' => CRON_STATUS_LIVE,
					  'state' => CRON_STATE_READY,
					  'exec_frequency' => DAY_IN_SECONDS,
					  'last_exec_time' => 0,
					  'rel_object_id' => 0,
					  'data' => array(),
					  'created_time' => time()
					);
	
	$job = array_merge($defaults, $job);
	
	$sql = "INSERT INTO pm_cron_jobs (name, type, status, state, exec_frequency, last_exec_time, rel_object_id, data, created_time) 
				 VALUES ('". secure_sql(trim($job['name'])) ."', 
						 '". secure_sql($job['type']) ."',
						 '". secure_sql($job['status']) ."',
						 '". secure_sql($job['state']) ."',
						 '". secure_sql($job['exec_frequency']) ."',
						 '0',
						 '". secure_sql($job['rel_object_id']) ."',
						 '". secure_sql(serialize($job['data'])) ."',
						 '". $job['created_time'] ."')";
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	return mysql_insert_id();
}

/**
 * Updates a cron job (all columns, so pass in the full array).
 * 
 * @param array $job
 * @return bool|resource mysql_query()
 */
function update_cron_job($job)
{
	if (is_array($job['data']))
	{
		$job['data'] = serialize($job['data']);
	}

	$sql = "UPDATE pm_cron_jobs 
			   SET name = '". secure_sql(trim($job['name'])) ."', 
				   type = '". secure_sql($job['type']) ."',
				   status = '". secure_sql($job['status']) ."', 
				   state = '". secure_sql($job['state']) ."', 
				   exec_frequency = '". secure_sql($job['exec_frequency']) ."', 
				   last_exec_time = '". secure_sql($job['last_exec_time']) ."', 
				   data = '". secure_sql($job['data']) ."'
			WHERE job_id = ". $job['job_id']; 
	
	return mysql_query($sql);
}

/**
 * Update single column for multiple jobs at once. 
 * 
 * @param array $job_ids
 * @param string $column_name
 * @param mixed $column_value
 * @return bool|resource mysql_query()
 */
function mass_update_cron_jobs($job_ids, $column_name, $column_value)
{
	if (is_array($job_ids))
	{
		$job_ids = implode(',', $job_ids);
	}
	
	$sql = "UPDATE pm_cron_jobs 
			   SET ". secure_sql($column_name) ." = '". secure_sql($column_value) ."' 
			WHERE job_id IN (". secure_sql($job_ids) .")";
	
	return mysql_query($sql);
}

/**
 * 
 * @param int $job_id
 * @return bool|resource mysql_query()
 */
function delete_cron_job($job_id)
{
	$sql = "DELETE FROM pm_cron_jobs
			WHERE job_id = ". (int) $job_id;
	
	return mysql_query($sql);
}

/**
 * 
 * @param array $job_ids
 * @return bool|resource mysql_query()
 */
function mass_delete_cron_jobs($job_ids)
{
	if (is_array($job_ids))
	{
		$job_ids = implode(',', $job_ids);
	}
	
	$sql = "DELETE FROM pm_cron_jobs
			WHERE job_id IN (". secure_sql($job_ids) .")
			  AND type NOT IN ('vscheck')";
	
	return mysql_query($sql);
}

/**
 * 
 * @param array $job
 * @param string $new_state
 * @return bool|resource mysql_query()
 */
function update_cron_job_state(&$job, $new_state)
{
	$job['state'] = $new_state;
	
	$sql = "UPDATE pm_cron_jobs 
			   SET state = '". $new_state ."' 
			WHERE job_id = ". $job['job_id']; 
	
	return mysql_query($sql);
}

/**
 * Lock a job so that no other process starts it again, in case it takes a long while. 
 * 
 * @param array $job
 * @return bool|resource mysql_query()
 */
function lock_cron_job(&$job) 
{
	$job['data']['lock_time'] = time();
	$job['data']['previous_state'] = $job['state'];
	
	$sql = "UPDATE pm_cron_jobs 
			   SET state = '". CRON_STATE_LOCK ."', 
				   data = '". secure_sql(serialize($job['data'])) ."'
			WHERE job_id = ". $job['job_id']; 
	
	return mysql_query($sql);
}

/**
 * Unlock a locked job.
 * 
 * @param array $job
 * @return bool|resource mysql_query()
 */
function unlock_cron_job(&$job)
{
	$previous_state = $job['data']['previous_state'];
	
	unset($job['lock_time'], $job['previous_state']);
	
	$sql = "UPDATE pm_cron_jobs 
			   SET state = '". $previous_state ."', 
				   data = '". secure_sql(serialize($job['data'])) ."'
			WHERE job_id = ". $job['job_id']; 
	
	return mysql_query($sql);
}


/**
 * Retrieves the first available job in order of priority.
 *
 * Job priority is based on job type:
 * 1. import
 * 2. sitemap(s) (@todo)
 * 3. vscheck (multi-part job)
 * 
 * @return array 
 */
function get_first_job_available()
{
	global $time_now;

	$sql = "SELECT * FROM pm_cron_jobs 
			WHERE type = 'import' 
			  AND status = '". CRON_STATUS_LIVE ."' 
			  AND state = '". CRON_STATE_READY ."'
			  AND (($time_now - exec_frequency) > last_exec_time)
			LIMIT 0, 1";
	
	if ( ! $result = mysql_query($sql))
	{
		cron_exit_on_error(sprintf('MySQL Error: %s <br />Line: %d', mysql_error(), __LINE__));
	}
	
	$job = mysql_fetch_assoc($result);
	mysql_free_result($result);
	
	if (empty($job) || count($job) == 0)
	{
		$sql = "SELECT * FROM pm_cron_jobs 
				WHERE type IN ('vscheck', 'sitemap', 'video-sitemap')
				  AND status = '". CRON_STATUS_LIVE ."' 
				  AND state = '". CRON_STATE_READY ."' 
				  AND (($time_now - exec_frequency) > last_exec_time)";
		
		if ( ! $result = mysql_query($sql))
		{
			cron_exit_on_error(sprintf('MySQL Error: %s <br />Line: %d', mysql_error(), __LINE__));
		}
		
		$job = $data = array();
		$time_last_run = $time_now * 2;
		while ($row = mysql_fetch_assoc($result))
		{
			$data = unserialize($row['data']);
			
			if ($time_last_run > $data['time_last_run'])
			{
				$time_last_run = $data['time_last_run'];
				$job = $row;
			}
		}
		mysql_free_result($result);
		
		if (empty($job) || count($job) == 0)
		{
			cron_debug('No jobs to do.'); // don't log; avoid build-up of 5-minute duplicate entries
			cron_end();
		}
	}
	
	$job['data'] = unserialize($job['data']);
	
	return $job; 
}


/**
 * Get job data by ID
 *  
 * @param int $job_id
 * @return bool|array false on failure, array of data on success
 */
function get_cron_job($job_id)
{
	$sql = "SELECT * FROM pm_cron_jobs 
			WHERE job_id = ". (int) $job_id;
	
	if ($result = mysql_query($sql))
	{
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if ( ! $row)
			return false;
		
		$row['data'] = unserialize($row['data']);
		
		return $row;
	}
	
	return false;
}

/**
 * Get job data by job type. Returns just one job.
 * 
 * @param string $type
 * @return bool|array false on failure, array of data on success
 */
function get_cron_job_by_type($type)
{
	$sql = "SELECT * FROM pm_cron_jobs 
			WHERE type = '". $type ."'";
	
	if ($result = mysql_query($sql))
	{
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if ( ! $row)
			return false;
		
		$row['data'] = unserialize($row['data']);
		
		return $row;
	}
	
	return false;
}

/**
 * Get a set of cron jobs; data is unserialized.
 * 
 * @since 2.7 changed sql order to put 'system' jobs (e.g. sitemap) on top of 'user defined' jobs (e.g. import)
 * 
 * @param int $from [optional]
 * @param int $limit [optional]
 * @param string $filter [optional]
 * @param mixed $filter_value [optional]
 * @return bool|array false on failure, array of data on success
 */
function get_all_cron_jobs($from = 0, $limit = 20, $filter = 'job_id', $filter_value = 'ASC')
{
	$sql = "SELECT * FROM pm_cron_jobs ";
	if ($filter == 'name')
	{
		$sql .= " WHERE name LIKE '%". secure_sql($filter_value) ."%' ";
	}
	else
	{
		if ($filter == 'job_id' && $filter_value == 'ASC')
		{
			$sql .= " ORDER BY type DESC, job_id ASC ";
		}
		else
		{
			$sql .= ($filter != '' && $filter_value != '') ? " ORDER BY $filter $filter_value " : '';
		}
		$sql .= " LIMIT $from, $limit";
	}
	
	if ($result = mysql_query($sql))
	{
		$jobs = array();
		while ($row = mysql_fetch_assoc($result))
		{
			$jobs[$row['job_id']] = $row;
			$jobs[$row['job_id']]['data'] = unserialize($row['data']);
		}
		mysql_free_result($result);
		
		return $jobs;
	}
	
	return false;
}

/**
 * Utility for generating a new cron security key
 * 
 * @return string
 */
function generate_cron_key()
{
	return md5( ((function_exists('microtime')) ? microtime() : time()) . rand(0, 99999) );
}

/**
 * Stop the process and do some cleanup, like change back the state of the job, update the last_exec_time 
 * and other end-of-process tasks.
 * 
 * @todo handle locked jobs.
 * 
 * @param object $mark_time [optional]
 * @return 
 */
function cron_end($mark_time = true)
{
	global $job;
	
	if ($job)
	{
		if ($mark_time)
		{
			$job['last_exec_time'] = time();
		}
		
		$job['state'] = CRON_STATE_READY;
		update_cron_job($job);
	}
	
	exit();
}

/**
 * Critical error handler: logs the error and stops the process.
 * Triggers a PM system error in case it's not related to a job. 
 * 
 * @param string $error_msg
 * @return 
 */
function cron_exit_on_error($error_msg)
{
	global $job;
	
	if ( ! cron_debug($error_msg))
	{
		if ($job)
		{
			$job['last_exec_time'] = time();
			$job['state'] = CRON_STATE_ERROR;
			$job['status'] = CRON_STATUS_STOPPED;
			update_cron_job($job);
			
			cron_log($error_msg);
		}
		else
		{
			log_error($error_msg, 'Automated Jobs');
		}
	}
	
	exit();
}

/**
 * 
 * @param string $notes
 * @return bool|resource mysql_query()
 */
function cron_log($notes)
{
	global $job, $time_now;
	
	if (cron_debug($notes))
	{
		return;
	}
	
	$time_now = ( ! $time_now) ? time() : $time_now;
	
	$sql = "INSERT INTO pm_cron_log (job_id, time, notes)
				VALUES (". (($job['job_id']) ? $job['job_id'] : 0) .", ". $time_now .", '". secure_sql($notes) ."')";
	
	return mysql_query($sql);
}

/**
 * Dump variable(s) and/or check if debugging mode is ON. 
 * Works with infinite args. 
 * 
 * @return bool true/false if debugging is on/off; also dumps any arguments passed. 
 */
function cron_debug()
{
	if (array_key_exists('debugging', $_GET) || array_key_exists('debugging', $_POST)) 
	{
		$args = func_get_args();
		
		if (func_num_args())
		{
			foreach ($args as $k => $arg)
			{
				if (is_array($arg) || is_object($arg))
				{
					echo '<pre>';
					print_r( $arg );
					echo '</pre>';
				}
				else
				{
					var_dump($arg);
				}
			}
		}
		
		return true;
	}
	
	return false;
}

/**
 *  
 * @param int $job_id
 * @param int $start [optional]
 * @param int $limit [optional]
 * @return bool|array
 */
function get_cron_log($job_id, $start = 0, $limit = 50)
{
	$log = array();
	
	$sql = "SELECT time, notes FROM pm_cron_log 
			WHERE job_id = $job_id  
			ORDER BY log_id  DESC
			LIMIT $start, $limit"; 
	
	if ( ! $result = mysql_query($sql))
	{
		return false;
	}
	
	while ($row = mysql_fetch_assoc($result))
	{
		$log[] = $row;
	}
	mysql_free_result($result);
	
	return $log; 
}

/**
 * 
 * @param int $job_id
 * @return bool|resource mysql_query()
 */
function clear_cron_log($job_id)
{
	$sql = "DELETE FROM pm_cron_log 
			WHERE job_id = $job_id";
	return mysql_query($sql);
}

/**
 * Check if a cron job exists based on the relational object ID and job type. 
 * 
 * @param int $rel_object_id
 * @param string $job_type
 * @return bool|int job_id on success, false if not found
 */
function check_cron_job_exists($rel_object_id, $job_type)
{
	$sql = "SELECT job_id, COUNT(*) as total 
			FROM pm_cron_jobs 
			WHERE type = '$job_type' 
			  AND rel_object_id = ". (int) $rel_object_id;
	
	if ($result = mysql_query($sql))
	{
		$row = mysql_fetch_assoc($result);
		mysql_free_result($result);
		
		if ((int) $row['total'] > 0)
		{
			return $row['job_id'];
		}
	}
	
	return false;
}

/**
 * Converts int of seconds to human readable string
 * 
 * @param int $job_frequency
 * @return string
 */
function cron_frequency_sec_to_lang($job_frequency)
{
	$string = '';
	
	if ($job_frequency > DAY_IN_SECONDS && $job_frequency <= (DAY_IN_SECONDS * 6))
	{
		$string = 'every '. ceil($job_frequency / 86400) .' days';
	}
	
	if ($job_frequency > WEEK_IN_SECONDS && $job_frequency <= (WEEK_IN_SECONDS * 3))
	{
		$string = 'every '. ceil($job_frequency / 604800) .' weeks';
	}
	
	switch ($job_frequency)
	{
		case (DAY_IN_SECONDS / 24):
			$string = 'hourly';
		break;
		
		case (DAY_IN_SECONDS / 4):
			$string = 'every 6 hours';
		break;
		
		case (DAY_IN_SECONDS / 2):
			$string = 'every 12 hours';
		break;
		
		case DAY_IN_SECONDS:
			$string = 'daily';
		break;
		
		case WEEK_IN_SECONDS:
			$string = 'weekly';
		break;
		
		case MONTH_IN_SECONDS:
			$string = 'monthly';
		break;
	}
	
	return $string;
}


/**
 * Outputs the HTML add/edit form.
 * 
 * @param array $job [optional]
 * @return 
 */
function show_edit_cron_job_form($job = false)
{
	global $userdata, $config;
	
	$defaults = array('job_id' => null,
					  'name' => '',
					  'type' => 'import',
					  'status' => CRON_STATUS_STOPPED,
					  'state' => CRON_STATE_READY,
					  'exec_frequency' => DAY_IN_SECONDS,
					  'last_exec_time' => 0,
					  'rel_object_id' => 0,
					  'data' => array(),
					  'created_time' => 0
					);
	$job = array_merge($defaults, $job);

	$exec_freq_val_opt = array( DAY_IN_SECONDS, DAY_IN_SECONDS * 2, DAY_IN_SECONDS * 3, DAY_IN_SECONDS * 4,
								DAY_IN_SECONDS * 5, DAY_IN_SECONDS * 6, WEEK_IN_SECONDS, WEEK_IN_SECONDS * 2,
								WEEK_IN_SECONDS * 3, MONTH_IN_SECONDS
						);
	
	if ($job['rel_object_id'] > 0)
	{
		$sub = get_import_subscription($job['rel_object_id']);
	}
	
	if ($job['name'] == '')
	{
		$job['name'] = $sub['sub_name'];
	}
	?>
	<div>

		<div class="control-group">
			<label>Name</label>
			<div class="input-prepend input-append">
				<input type="text" name="name" value="<?php echo htmlspecialchars($job['name']); ?>" placeholder="" />
				<?php if ($job['type'] == 'import') : ?>
					<?php if ($job['rel_object_id'] > 0) : ?>
						<?php if ($sub['data']['profile_avatar_url'] != '') : ?>
							<span class="add-on">
								<img src="<?php echo make_url_https($sub['data']['profile_avatar_url']); ?>" width="18" height="18" />
							</span>
						<?php endif; ?>
					<span class="add-on">
						<div class="sprite <?php echo ( ! empty($sub['data']['data_source'])) ? strtolower($sub['data']['data_source']) : 'youtube'; ?>" rel="tooltip" title="Source: <?php echo ( ! empty($sub['data']['data_source'])) ? ucfirst($sub['data']['data_source']) : 'youtube'; ?>"></div>
					</span>
					<?php endif; ?>
				<?php endif; ?>
			</div>
		</div>
		<hr />

		<?php if ($job['type'] == 'import' && $sub['sub_type'] == 'search') : ?>
		<div class="control-group">
			<label>Exclude these keywords</label>
			<input type="text" name="exclude_keywords" value="<?php echo (is_array($job['data']['exclude_keywords'])) ? htmlspecialchars(implode(',', $job['data']['exclude_keywords'])) : htmlspecialchars($job['data']['exclude_keywords']); ?>" placeholder="Use comma separated keywords" />
		</div>
		<?php endif; ?>
		
		<?php if ($job['type'] == 'import') : ?>
		<!--
		<div class="control-group">
			<label>Post videos as</label> 
		</div>
		-->
		<input type="hidden" name="username" value="<?php echo ($job['data']['userdata']['username'] != '') ? htmlspecialchars($job['data']['userdata']['username']) : htmlspecialchars($userdata['username']); ?>" />
		<div class="control-group">
			<label>Import videos uploaded after</label>
			<?php echo show_form_item_date( (($job['job_id']) ? $job['data']['uploaded_after'] : time()) ); ?>
		</div>			
		<?php endif; ?>
		
		<?php if ($job['type'] == 'vscheck') : ?>
		<div class="control-group">
			<label>Begin video checks with</label> 
			<select name="video_sorting">
				<option value="most-viewed">Most viewed videos</option>
				<option value="latest">Newest videos</option>
				<option value="oldest">Oldest videos</option>
			</select>
			<input type="hidden" name="video_limit" value="20" />
		</div>
		<?php endif; ?>
		
		<?php 
		if ($job['type'] == 'sitemap' || $job['type'] == 'video-sitemap')
		{
			$form_options = array(
				'limit' => 50000,
				'media_keywords' => false,
				'media_category' => false,
				'item_pubDate' => false,
				'ping_google' => 'no',
				'ping_bing' => 'no'
			);

			$last_options_used = sitemap_load_options();
			$form_options = array_merge($form_options, $last_options_used);
			if ($job['type'] == 'video-sitemap')
			{
				$form_options['max_limit'] = $config['published_videos'];
			}
			else
			{
				$form_options['max_limit'] = ($config['published_videos'] + $config['total_pages'] + $config['published_articles'] + 4);
			}
			
//			if ($config['published_videos'] < 50000)
//			{
//				$form_options['limit'] = $form_options['max_limit'];
//			}
		}
		
		if ($job['type'] == 'video-sitemap') : ?>
		<div class="control-group">
			<label>Videos per sitemap</label> 
			<input type="text" name="limit" size="9" value="<?php echo $form_options['limit']; ?>" />
			<input type="hidden" name="max-limit" value="<?php echo (int) $form_options['max_limit']; ?>" />
		</div>
		<div class="control-group">
			<label>
				<input type="checkbox" name="media_keywords" value="1" <?php echo ($form_options['media_keywords']) ? 'checked="checked"' : '';?> /> Include <code>&lt;media:keywords&gt;</code> 
			</label>
			<br /> 
			<label>
				<input type="checkbox" name="media_category" value="1" <?php echo ($form_options['media_category']) ? 'checked="checked"' : '';?> /> Include <code>&lt;media:category&gt;</code> 
			</label>
			<br />
			<label>
				<input type="checkbox" name="item_pubDate" value="1" <?php echo ($form_options['item_pubDate']) ? 'checked="checked"' : '';?> /> Include <code>&lt;pubDate&gt;</code> 
			</label> 
		</div>
		<?php endif; ?>
		
		<?php if ($job['type'] == 'sitemap') : ?>
		<div class="control-group">
			<label>URLs per sitemap</label> 
			<input type="text" name="limit" size="9" value="<?php echo $form_options['limit']; ?>" />
			<input type="hidden" name="max-limit" value="<?php echo (int) $form_options['max_limit']; ?>" />
		</div>
		<?php endif; ?>
		
		<?php if ($job['type'] == 'sitemap' || $job['type'] == 'video-sitemap') : ?>
		<div class="control-group">
			<label>Ping Google.com</label>
			<label>
        		<input type="radio" name="ping_google" value="yes" <?php echo ($form_options['ping_google'] == 'yes') ? 'checked="checked"' : ''; ?>> Yes
			</label>
			<label>
        		<input type="radio" name="ping_google" value="no" <?php echo ($form_options['ping_google'] != 'yes') ? 'checked="checked"' : ''; ?>> No
			</label>
		</div>
		<div class="control-group">
			<label>Ping Bing.com</label>
			<label>
        		<input type="radio" name="ping_bing" value="yes" <?php echo ($form_options['ping_bing'] == 'yes') ? 'checked="checked"' : ''; ?>> Yes
			</label>
			<label>
        		<input type="radio" name="ping_bing" value="no" <?php echo ($form_options['ping_bing'] != 'yes') ? 'checked="checked"' : ''; ?>> No
			</label>
		</div>
		<?php endif; ?>
		
		<div class="control-group">
			<label>Run this job</label>
			<select name="exec_frequency">
				<?php foreach ($exec_freq_val_opt as $k => $freq) : ?>
				<option value="<?php echo $freq; ?>" <?php echo ($job['exec_frequency'] == $freq) ? 'selected="selected"' : ''; ?>><?php echo ucfirst(cron_frequency_sec_to_lang($freq)); ?></option>
				<?php endforeach; ?>
			</select>
			<?php if ($config['total_videos'] >= 30000 && in_array($job['type'], array('vscheck', 'sitemap', 'video-sitemap'))) : ?>
				<div class="input-help input-help-highlight">Your database has more than 30,000 videos. <br />For optimal performance, run this job on a <strong>weekly</strong> basis.</div>
			<?php endif; ?>
		</div>
	</div>
	
	<input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>" />
	<input type="hidden" name="rel_object_id" value="<?php echo $job['rel_object_id']; ?>" />
	<input type="hidden" name="type" value="<?php echo $job['type']; ?>" />
	<input type="hidden" name="status" value="<?php echo $job['status']; ?>" />
	<input type="hidden" name="state" value="<?php echo $job['state']; ?>" />
	<input type="hidden" name="last_exec_time" value="<?php echo $job['last_exec_time']; ?>" />
	
	<?php echo csrfguard_form('_admin_edit_cron_job'); ?>
	<?php
}


/**
 * Outputs the 'schedule' and 'unschedule' HTML elements.
 * 
 * @param int $rel_object_id (ex. subscription id)
 * @param string $job_type 
 * @param bool $is_scheduled button state; handy for external usage
 * @param array $btn_attrs [optional] html attributes (ex. array('class' => 'btn-primary jquery-element-indentifier')
 * @return 
 */
function show_cron_schedule_button($rel_object_id, $job_type, &$is_scheduled, $btn_attrs = array())
{
	$job_id = check_cron_job_exists($rel_object_id, $job_type);
	$is_scheduled = ($job_id) ? true : false; 
	
	
	$btn_attrs_str = '';
	if (is_array($btn_attrs) && count($btn_attrs) > 0)
	{
		foreach ($btn_attrs as $attr => $value)
		{
			$btn_attrs_str .= $attr .'="'. $value .'" ';
		}
	}
	// we use 'data-job-is-scheduled' instead of the 'hide' class to hide the element on $(document).ready() and avoid the smaller btn problem when doing .show();
	?>
	<a href="#" class="btn btn-small btn-success active cron-delete-btn" id="cron-delete-btn-<?php echo $rel_object_id; ?>" data-job-id="<?php echo $job_id; ?>" data-sub-id="<?php echo $rel_object_id; ?>" <?php echo $btn_attrs_str; ?> data-job-is-scheduled="<?php echo ($is_scheduled) ? 'true' : 'false'; ?>" rel="tooltip" title="Click to stop auto-importing"><i class="fa fa-refresh fa-check-circle"></i> Auto-importing</a> 
	<a href="#add-cron-job-modal" class="btn btn-small btn-yellow cron-add-btn" id="cron-add-btn-<?php echo $rel_object_id; ?>" data-job-id="" data-sub-id="<?php echo $rel_object_id; ?>" <?php echo $btn_attrs_str; ?> data-job-is-scheduled="<?php echo ($is_scheduled) ? 'true' : 'false'; ?>" rel="tooltip" title="Auto-import videos from this subscription"><i class="fa fa-check-circle"></i> Auto-import</a>
	<?php
}

/**
 * Converts the job 'state' property to human readable HTML element.
 * 
 * @param array $job
 * @return 
 */
function show_cron_job_state_html($job)
{
	?>
	<span id="cron-state-container-<?php echo $job['job_id']; ?>">
	<?php
	switch ($job['state'])
	{
		case CRON_STATE_ERROR:
			?>
			<span class="label label-important" rel="tooltip" title="View log for more details">Error</span>
			<?php
		break;
		
		case CRON_STATE_LOCK:
		case CRON_STATE_BUSY:
			?> 
			<span class="label label-warning" rel="tooltip" title="Job in progress">In Progress</span>
			<?php
		break;
		
		case CRON_STATE_READY:
			
			switch ($job['status'])
			{
				case CRON_STATUS_LIVE:
					
					// This job might take more than exec_frequency and so can be perceived as not working.
					// So, as a temporary measure we show it's busy until the job finishes.
					if (in_array($job['type'], array('vscheck', 'sitemap', 'video-sitemap')) && $job['data']['time_started'] > 0)
					{
						?>
						<span class="label label-warning" rel="tooltip" title="Job in progress">In Progress</span>
						<?php
					}
					else
					{
						?>
						<span class="label label-success">Active</span>
						<?php
					}
					
				break;
				
				case CRON_STATUS_STOPPED:
					?>
					<span class="label">Inactive</span>
					<?php
				break;
			}
			
		break;
	}
	?>
	</span>
	<?php 
}

/**
 * Outputs the 'stop' and 'start' HTML elements.
 * 
 * @param array $job
 * @return 
 */
function show_play_stop_button_html($job)
{
	?>
	
	<a href="#start" class="btn btn-mini btn-link cron-start-stop-btn <?php echo ($job['status'] == CRON_STATUS_LIVE) ? 'hide' : ''; ?>" id="cron-start-btn-<?php echo $job['job_id']; ?>" rel="tooltip" title="Activate Job" data-job-id="<?php echo $job['job_id']; ?>" data-btn-type="start"><i class="fa fa-check-circle"></i></a>
	<a href="#stop" class="btn btn-mini btn-link cron-start-stop-btn <?php echo ($job['status'] == CRON_STATUS_STOPPED) ? 'hide' : ''; ?>" id="cron-stop-btn-<?php echo $job['job_id']; ?>" rel="tooltip" title="Deactivate Job" data-job-id="<?php echo $job['job_id']; ?>" data-btn-type="stop"><i class="fa fa-pause-circle"></i></a>
	
	<?php 	
}

