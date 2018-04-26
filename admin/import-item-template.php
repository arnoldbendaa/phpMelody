<div class="video-stack<?php if ($_POST['checkall'] == 'true') echo ' stack-selected'; if ( ! $item['embeddable'] || $item['private']) echo ' stack-unusable';?>" id="stack-id-<?php echo str_replace(' ', '-', $counter); ?>">

	<input type="hidden" name="stack_id[<?php echo $counter;?>]" value="stack-id-<?php echo str_replace(' ', '-', $counter); ?>" />
	<?php if ($item['has_errors'] && count($item['errors']) > 0) : // CSV items ?>
	<div class="alert alert-error">
		<ul class="list-unstyled">
		<?php foreach ($item['errors'] as $k => $error_msg) : ?>
			<li><?php echo $error_msg; ?></li>
		<?php endforeach; ?>
		</ul>
	</div>
	<?php endif; ?>
	<div style="font-size: 10px; font-weight: normal">
		<div class="on_off" rel="tooltip" title="Select to import">
			<label for="video_ids[<?php echo $counter;?>]">IMPORT</label>
			<input type="checkbox" id="import-<?php echo $counter;?>" name="video_ids[<?php echo $counter;?>]" value="<?php echo $item['id'] .'" '; if( ! $item['embeddable'] || $item['private']) { echo 'disabled="disabled" class="check_ignore"'; } elseif ($_POST['checkall'] == 'true') { echo ' checked="checked"'; } ?> />
		</div>
	</div>
	<a id="video-id-[<?php echo $counter;?>]"></a>
	<input id="video-title[<?php echo $counter;?>]" name="video_title[<?php echo $counter;?>]" type="text" value="<?php echo htmlspecialchars($item['title']); ?>" size="20" class="video-stack-title required_field" rel="tooltip" title="Click to edit" />
	<div class="clearfix"></div>
	<div class="video-stack-left">
		<ul class="thumbs_ul_import">
			<li class="stack-thumb-selected stack-thumb">
				<?php if ( ! $item['embeddable'] || $item['private']) : ?>
				<h5>This owner of this video doesn't allow embedding.</h5>
				<?php endif; ?>
				<?php if (is_array($item['geo-restriction'])) : ?>
				<span class="video-stack-geo"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="<?php echo $georestriction; ?>"><img src="img/ico-geo-warn.png" /></a></span>
				<?php endif; ?>
				<span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="The default thumbnail for this video."><i class="icon-ok icon-white"></i></a></span>
				<span class="stack-video-duration"><?php echo ($item['duration']) ? sec2hms($item['duration']) : ''; ?></span>
				<?php if ($item['embeddable']) : ?>
					<span class="stack-preview"><a href="<?php echo $item['embed_url']; ?>" rel="prettyPop[flash]" title="<?php echo htmlspecialchars($item['title']); ?>"><div class="pm-sprite ico-playbutton"></div></a></span>
				<?php endif; ?>
				<img src="img/blank.gif" alt="" width="154" height="117" border="0" name="video_thumbnail" videoid="<?php echo $item['id']; ?>" rowid="<?php echo $counter;?>" class="" data-echo="<?php echo $item['thumbs'][0]['medium']; ?>" />
			</li>
			<?php if ($item['total_thumbs'] > 1) : ?>
			<li class="thumbs_li_default stack-thumb-small">
				<span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="The default thumbnail for this video."><i class="icon-ok icon-white"></i></a></span>
				<img src="img/blank.gif" alt="Thumb 2" width="71" height="53" border="0" name="video_thumbnail" videoid="<?php echo $item['id']; ?>" rowid="<?php echo $counter;?>" class="" data-echo="<?php echo $item['thumbs'][1]['small']; ?>" />
			</li>
			<?php endif; ?>
			<?php if ($item['total_thumbs'] > 2) : ?>
			<li class="thumbs_li_default stack-thumb-small">
				<span class="stack-thumb-text"><a href="#video-id-[<?php echo $counter;?>]" rel="tooltip" data-placement="right" title="The default thumbnail for this video."><i class="icon-ok icon-white"></i></a></span>
				<img src="img/blank.gif" alt="Thumb 3" width="71" height="53" border="0" name="video_thumbnail" videoid="<?php echo $item['id']; ?>" rowid="<?php echo $counter;?>" class="" data-echo="<?php echo $item['thumbs'][2]['small']; ?>" />
			</li>
			<?php endif; ?>
		</ul>
		<div class="clearfix"></div>
		<label>
			<input type="checkbox" name="featured[<?php echo $counter;?>]" id="check_ignore" value="1" /> <small>Mark as <span class="label label-featured">FEATURED</span></small>
		</label>
		<?php if ( ! $item['embeddable']) : ?>
		
		<?php endif; ?>
	</div><!-- .video-stack-left -->
	<div class="video-stack-right noSearch clearfix">
		<label>CATEGORY <small style="color:red;">*</small></label>
		<div class="video-stack-cats">
			<?php
			$categories_dropdown_options = array(
						'attr_name' => 'category['. $counter .'][]',
						'attr_id' => 'select_category-'. $counter,
						'select_all_option' => false,
						'spacer' => '&mdash;',
						'selected' => $overwrite_category,
						'other_attr' => 'multiple="multiple" size="3"',
						'option_attr_id' => 'check_ignore'
						);
			echo categories_dropdown($categories_dropdown_options);
			?>
		</div>
	
		<div class="clear"></div>
		<label>DESCRIPTION</label>
		<textarea name="description[<?php echo $counter;?>]" id="description[<?php echo $counter;?>]" rows="2" class="video-stack-desc"><?php if($autodata) echo $item['description'];?></textarea>
		<label class="control-label" for="tags">TAGS</label>
		<div class="tagsinput">
			<input type="text" id="tags_addvideo_<?php echo $counter;?>" name="tags[<?php echo $counter;?>]" value="<?php if($autodata) echo $item['keywords'];?>" class="tags" />
		</div>		  
		<input type="hidden" id="thumb_url_<?php echo $counter;?>" name="thumb_url[<?php echo $counter;?>]" value="<?php echo $item['thumbs'][0][$config['download_thumb_res']]; ?>" />
		
		<input type="hidden" name="duration[<?php echo $counter;?>]" value="<?php echo $item['duration']; ?>" />
		<input type="hidden" name="direct[<?php echo $counter;?>]" value="<?php echo $item['url']; ?>" />
		<input type="hidden" name="url_flv[<?php echo $counter;?>]" value="" />
		<?php if ($data_source == 'csv') : ?>
		<input type="hidden" name="csv_item_id[<?php echo $counter;?>]" value="<?php echo $item['item_id'];?>" />
		<input type="hidden" name="source_id[<?php echo $counter;?>]" value="<?php echo $item['source_id'];?>" />
		<?php endif; ?>
		
	</div> <!-- .video-stack-right -->
</div><!-- .video-stack -->