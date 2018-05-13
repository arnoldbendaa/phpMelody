<script src="js/jquery.ui.widget.js" type="text/javascript"></script>
<script src="js/jquery.iframe-transport.js" type="text/javascript"></script>
<script src="{$smarty.const._URL}/templates/{$template_dir}/js/jquery.tagsinput.min.js" type="text/javascript"></script>
<script src="{$smarty.const._URL}/templates/{$template_dir}/js/jquery.maskedinput-1.3.min.js" type="text/javascript"></script>

<script type="text/javascript" src="js/jquery.fileupload.js"></script>
<script type="text/javascript" src="js/upload.js"></script>
<link rel="stylesheet" type="text/css"  href="css/upload.css">
<div class="wrap-popup rounded" id="upload-video-dropzone">
	<div class="popup-main-panel">
		<div class="popup-content">
			<div class="popup-container">
				<div class="popup-tabs">
					<div class="popup-tab active">
						<a href="#" data-href="helpers/upload-video-step1.html" data-max-width="850" class="popup-tab-link wrap-icon js__btn-popup">
							<svg class="svg-icon" width="29px" height="24px">
								<use xlink:href="#upload2"></use>
							</svg>
							<span>Upload Video</span>
						</a>
					</div>
				</div>
				<div class="form-container">
					<div class="form-row">
						<div class="form-sub-row">
							<div class="wrap-fcustominp">
								<span class="fcustominp green">
									<input id="check" type="radio" name="upload" value="upload_file" checked="checked"/>
									<label for="check"></label>
								</span>
								<label for="check" class="fcustominp-label">Upload file from your local disk:</label>
							</div>
						</div>
						<div class="holder">
							<div class="pseudoFile">
								<input id="pseudoFileLoadLocal" type="text" placeholder="Select video file here" class="inputFileText" />
								<input id="upload-video-file-btn" type="file" name="video" maxlength="100" size="60" onchange="document.getElementById('pseudoFileLoadLocal').value = this.value" class="fileInput btn confirm full" /><span class="choose">Browse...</span>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="popup-side-panel">
		<div class="popup-container">
			<div class="popup-title wrap-icon"><span> Upload Rules</span>
				<div class="popup-text">
					<p>Only upload videos that you made or that you are authorized to use.</p>
				</div>
				<div class="popup-text">
					<p>All videos are subject to removal by site administration without prior notice.</p>
				</div>
			</div>
		</div>
	</div>
</div>






<form id="upload-video-form" class="wrap-popup rounded upload-form-only" name="upload-video-form"  enctype="multipart/form-data"  action="{$form_action}">
	<div class="popup popup-wides wrap-popup-side-panel">
		<div class="popup-main-panel">
			<div class="popup-content">
				<div class="popup-container">
					<div class="popup-tabs">
						<div class="popup-tab active">
							<a href="#" data-href="helpers/upload-video-step1.html" data-max-width="850" class="popup-tab-link wrap-icon js__btn-popup">
								<svg class="svg-icon" width="29px" height="24px">
									<use xlink:href="#upload2"></use>
								</svg>
								<span>Upload Video</span>
							</a>
						</div>
					</div>
					<div class="hide" id="manage-video-ajax-message"></div>
					<div class="steps step2">
						<div class="steps-captions">
							<div class="steps-caption-left">Upload file</div>
							<div class="steps-caption-right">Add Info</div>
						</div>
						<div class="steps-progress">
							<div class="circle first"></div>
							<div class="circle last"></div>
							<div class="wrap-overflow">
								<div class="holder">
									<ol id="upload-video-selected-files-container"></ol>
								</div>

							</div>
						</div>
					</div>
					<div class="form-container">
						<div class="form-row">
							<label class="flabel">Change default screenshot:</label>
							<div class="holder">
								<div class="pseudoFile">
									<input id="pseudoscreenshot" type="text" placeholder="Select image file here" class="inputFileText" />

									<input type="file" name="capture" class="capture" value="" size="40" /><span class="choose">Browse...</span>
								</div>
							</div>
						</div>

						<div class="form-row">
							<label class="flabel">Title (*):</label>
							<div class="holder">
								<input type="text" name="video_title" maxlength="100" value="" placeholder="type video title here" class="finp" />
							</div>
						</div>
						<div class="form-row">
							<div class="flabel">{$lang.description} (*):</div>
							<div class="holder">
								<textarea id="description" name="description" placeholder="type description here" class="ftext"></textarea>
							</div>
						</div>
						<div class="form-row">
							<div class="flabel">Duration(*):</div>
							<div class="holder">
								<input id="duration" name="duration" class="input-mini" type="text" style="text-align:center;">
							</div>
						</div>
						<div class="wrap-list-selector">
							<div class="form-row">
								<div class="flabel">{$lang.category}</div>
								<div class="list-selector-popup-inner">
                                    {$categories_dropdown}
								</div>
							</div>
						</div>
						<div class="wrap-list-selector">
							<div class="form-row">
								<div class="flabel">Casino</div>
								<div class="list-selector-popup-inner">
                                    {$casinos_dropdown}
								</div>
							</div>
						</div>
						<div class="wrap-list-selector">
							<div class="form-row">
								<div class="flabel">Provider</div>
								<div class="list-selector-popup-inner">
                                    {$providers_dropdown}
								</div>
							</div>
						</div>

						<div class="control-group">
							<label class="control-label" for="tags">{$lang.tags}</label>
							<div class="controls">
								<div class="tagsinput">
									<input id="tags_upload" name="tags" type="text" class="tags" value="{$smarty.post.tags}">
									<span class="help-inline">
										<a href="#" rel="tooltip" title="{$lang.suggest_ex}">
											<i class="icon-info-sign"></i>
										</a>
									</span>
								</div>
							</div>
						</div>
						<input type="hidden" name="form_id" value="{$form_id}" />
						<input type="hidden" name="_pmnonce_t" id="upload-video-form-nonce" value="{$form_csrfguard_token}" />
						<input type="hidden" name="temp_id" id="temp_id" value="" />
						<input type="hidden" name="p" value="upload" />
						<input type="hidden" name="do" value="upload-media-file" />
						<input type="hidden" name="MAX_FILE_SIZE" value="{$max_file_size}">
					<div class="form-row">
						<div class="notedit">
							<button type="submit" id="upload-video-submit-btn" class="btn confirm full js__btn-popup">Upload</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="popup-side-panel">
		<div class="popup-container">
			<div class="popup-title wrap-icon"><span> Video preview</span>
			</div>
			<div class="popup-indent"></div>
			<div class="popup-title wrap-icon"><span> Upload Rules</span>
				<div class="popup-text">
					<p>Only upload videos that you made or that you are authorized to use.</p>
				</div>
				<div class="popup-text">
					<p>All videos are subject to removal by site administration without prior notice.</p>
				</div>
				</div>
			</div>
		</div>
	</div>
</form>