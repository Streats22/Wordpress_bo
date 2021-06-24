<?php
if (!defined('ABSPATH')) {
	exit;
}
?>
<style type="text/css">
	.wt_mgdp_import_log_main {
		display: none;
		font-weight: bold;
		padding-bottom: 5px;
	}

	.wt_mgdp_import_loglist_main {
		display: none;
		float: left;
		width: 100%;
		height: 200px;
		overflow: auto;
		padding: 10px 0px;
		margin-bottom: 20px;
		background: #fdfdfd;
		box-shadow: inset 0 0 3px #ccc;
	}

	.wt_mgdp_import_loglist_inner {
		float: left;
		width: 98%;
		height: auto;
		overflow: auto;
		margin: 0px 1%;
		font-style: italic;
	}
	.wt_mgdp_import_form label {
		font-weight: bold;
	}
        .wf_import_loader {
  display: inline-block;
  width: 20px;
  height: 20px;
  border: 3px solid rgba(69,89,89,.3);
  border-radius: 50%;
  border-top-color: #fff;
  animation: spin 1s ease-in-out infinite;
  -webkit-animation: spin 1s ease-in-out infinite;
  margin-top: -2%;
  margin-left: 8px;
}

@keyframes spin {
  to { -webkit-transform: rotate(360deg); }
}
@-webkit-keyframes spin {
  to { -webkit-transform: rotate(360deg); }
}
</style>
<div style="padding-top:15px; padding-bottom:35px;">

	 <div style="display:inline-flex"><h3 style="margin-top:0px;"><?php _e('Import: Files and DB', 'wp-migration-duplicator'); ?></h3><div id="loading" class="wf_import_loader" style="display:none;" ></div></div>
	<p><?php _e('Import data via a zip file(contains files and DB from the server that is to be migrated) from your local/FTP/Gdrive/Amazon S3 locations.');?></p>
	<table class="wf-form-table wt_mgdp_import_options" style="max-width:650px;">
		<tr class="wt_mgdp_import_er" style="display:none;">
			<td colspan="3" style="color:red;"></td>
		</tr>
		<tr>
			<th><?php _e('Import from', 'wp-migration-duplicator') ?><span class="wt-mgdp-tootip" data-wt-mgdp-tooltip="<?php _e('Import data via a zip file(containing data from the server to be migrated).', 'wp-migration-duplicator'); ?>"><span class="wt-mgdp-tootip-icon"></span></span></th>
			<td>
				<?php
				$import_options = Wp_Migration_Duplicator_Import::get_possible_import_methods();
				?>
				<div class="wt-migrator-select-container">
					<?php
					if (is_array($import_options)) {
						echo '<select name="wt_mgdb_import_option" data-option-type="import" >';
						foreach ($import_options as $value => $import_option) {
							echo '<option value="' . $value . '">' . $import_option . '</option>';
						}

						echo '</select>';
					}

					?>
					<span class="spinner"></span>
				</div>

			</td>
		</tr>
	</table>
	<?php do_action('mgdp_after_import_form'); ?>
	<div class="child-form-item child-wt_mgdb_import_option wt_mgdb_import_option_local" style="display:block">
		<table class="wf-form-table wt_mgdp_import_form" style="max-width:650px;margin-bottom:20px; margin-top:10px;">
			<tr>
				<th></th>
				<td style="padding:0px 10px">
					<input type="hidden" name="attachment_url" id="attachment_url">
					<input style="text-align:center;" type="button" name="upload-btn" id="upload-btn" class="button button-primary" value="<?php _e('Upload backup file', 'wp-migration-duplicator') ?>">
					<span class="wt_mgdp_import_attachment_url"></span>
				</td>
				
			</tr>
		</table>
		<div class="wt_info_box" style=" width:98%;">
                    	<a href="https://www.webtoffee.com/increase-maximum-upload-file-size-in-wordpress-migrator/" target="_blank"><?php _e( 'How-to: Increase maximum upload file size', 'wp-migration-duplicator' ); ?></a><br/><br/>
		<?php _e('Bypass upload limit: If your server upload limit is smaller than the size of the file to be imported, follow the below mentioned steps.', 'wp-migration-duplicator') ?>
		<ul style="list-style:disc; margin-left:20px;">
			<li><?php _e('Upload a zip file into media library and replace that file through FTP with actual backup zip.', 'wp-migration-duplicator'); ?></li>
			<li><?php _e('Choose this file from media via Upload button.', 'wp-migration-duplicator'); ?></li>
			<li><?php _e('Click on import to process the file.', 'wp-migration-duplicator'); ?></li>
		</ul>
		</div>			
	</div>
	<div class="wt-migrator-import-section">
		<div class="wt_mgdp_import_log_main"></div>
		<div class="wt_mgdp_import_loglist_main">
			<div class="wt_mgdp_import_loglist_inner">

			</div>
		</div>
	</div>

	<div style="clear: both;"></div>
	
</div>

<div class="wf-plugin-toolbar bottom">
	<div class="left">
	</div>
	<div class="right">
               <input type="hidden" id="extension_zip_loaded_imp" name="extension_zip_loaded_imp" value=<?php $extension_zip_loaded = extension_loaded('zip')?'enabled':'disabled'; echo $extension_zip_loaded;?>>
                <input type="hidden" id="extension_zlib_loaded_imp" name="extension_zlib_loaded_imp" value=<?php $extension_zlib_loaded = extension_loaded('zlib')?'enabled':'disabled'; echo $extension_zlib_loaded;?>>
		<button name="wt_mgdp_import_btn" class="button button-primary" style="float:right;"><?php _e('Import', 'wf-woocommerce-packing-list'); ?></button>
		<button name="" class="button button-primary wt_mgdp__start_new_import" style="float:right; display:none;"><?php _e('Start new import', 'wf-woocommerce-packing-list'); ?></button>
		<span class="spinner" style="margin-top:11px;"></span>
	</div>
</div>