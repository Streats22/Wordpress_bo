<?php
if (!defined('WPINC')) {
	die;
}
?>
<div>
	<h3><?php echo __('Amazon S3', 'wp-migration-duplicator'); ?></h3>
</div>
<div class="wt_info_box" style="margin-bottom:35px;">
<?php _e('AWS requires different types of security credentials depending on how you access AWS. For example, you need a user name and password to sign in to the AWS Management Console and you need access keys to make programmatic calls to AWS or to use the AWS Command Line Interface or AWS Tools for PowerShell.','wp-migration-duplicator');?>
<ul style="list-style:disc; margin-left:20px;">
		<li><?php _e('To connect Amazon S3 you need to obtain the access key & secret key from Amazon','wp-migration-duplicator'); ?></li>
		<li><?php echo sprintf(wp_kses(__('The Access key and Secret key can be obtained from your IAM console. Kindly refer <a href="%s" target="_blank">Amazon developer documentation</a>', 'wp-migration-duplicator'), array('a' => array('href' => array(), 'target' => array()))), esc_url('https://docs.aws.amazon.com/general/latest/gr/aws-sec-cred-types.html')); ?></li>
		<li><?php _e('To update the credentials: disconnect, update and authenticate.','wp-migration-duplicator'); ?></li>
	</ul>
</div>
<div id="wt_s3bucket_auth_form">
	<form action="">
		<table class="form-table wf-form-table" style="max-width:800px;">
			<tbody>
				<tr class="">
					<th scope="row" class="" style=""><?php echo __('Access key', 'wp-migration-duplicator'); ?><span class="wt-mgdp-tootip" data-wt-mgdp-tooltip="<?php _e('Access keys are used to sign programmatic requests to the AWS CLI or AWS API (directly or using the AWS SDK).You can use the AWS Management Console to manage access keys.', 'wp-migration-duplicator'); ?>"><span class="wt-mgdp-tootip-icon"></span></span></th>
					<td class="">
						<input type="text" class="" name="wt_s3bucket_access_key" value="<?php echo $accesskey; ?>" id="wt_s3bucket_access_key">
					</td>
				</tr>
				<tr class="">
					<th scope="row" class="" style=""><?php echo __('Secret key', 'wp-migration-duplicator'); ?><span class="wt-mgdp-tootip" data-wt-mgdp-tooltip="<?php _e('The secret key is used along with the access key to sign programmatic requests to the AWS CLI or AWS API (directly or using the AWS SDK).You can use the AWS Management Console to manage access keys.', 'wp-migration-duplicator'); ?>"><span class="wt-mgdp-tootip-icon"></span></span></th>
					<td class="">
						<input type="text" class="" name="wt_s3bucket_secret_key" value="<?php echo $secretkey; ?>" id="wt_s3bucket_secret_key" >
					</td>
				</tr>
				<tr class="">
					<th scope="row" class="" style=""><?php echo __('S3 location', 'wp-migration-duplicator'); ?><span class="wt-mgdp-tootip" data-wt-mgdp-tooltip="<?php _e('Specify the Amazon S3 bucket that you want to use as a source or destination location.', 'wp-migration-duplicator'); ?>"><span class="wt-mgdp-tootip-icon"></span></span></th>
					<td class="">
						<input type="text" class="" name="wt_s3bucket_location" value="<?php echo $location; ?>" id="wt_s3bucket_location" placeholder="e.g. test-migrator">
					</td>
				</tr>
			</tbody>
		</table>
		<div style="clear: both;"></div>
		<?php if( $authenticated === false ):?> 
		<div class="wf-plugin-toolbar wt-migrator-action-bar bottom wt-migrator-authenticate-bar">
			<div class="left">
			</div>
			<div class="right">
				<span class="wt-migrator-notice wt-migrator-notice-inline" style=" margin-top: 10px; display: inline-block; margin-right: -20px;"></span>
				<input type="submit" name="wt_authenticate_s3bucket" value="<?php _e('Authenticate', 'wp-migration-duplicator'); ?>" class="button button-primary" style="float:right;" />
				<span class="spinner" style="margin-top:11px"></span>
			</div>
		</div>
		<?php else :?>
			<div class="wf-plugin-toolbar bottom wt-migrator-action-bar wt-migrator-disconnect-bar">
			<div class="left">
			</div>
			<div class="right">
				<span class="wt-migrator-notice wt-migrator-notice-inline" style=" margin-top: 10px; display: inline-block; margin-right: -20px;"></span>
				<button type="submit" id="wt_disconnect_s3bucket" name="wt_disconnect_s3bucket" class="button button-primary" style="float:right;"><?php _e('Disconnect', 'wp-migration-duplicator'); ?></button>
				<span class="spinner" style="margin-top:11px"></span>
			</div>
		</div>
		<?php endif; ?>
	</form>
</div>