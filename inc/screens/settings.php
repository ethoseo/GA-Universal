<?php
	global $wpdb, $wp_roles;
?>
<div class="wrap">
<!--
Hi There <?php 
global $current_user;
echo $current_user->display_name;
?>!
You're reading the code, that means I think you're pretty awesome. <?php /* Especially if you're reading the PHP code. */ ?>
This plugin implements Google's Universal Analytics script Analytics.js.
If you have a better way of doing this or anything else, or want to talk WordPress, PHP, internet marketing, or similarly nerdy things drop me an email: <nick@ethoseo.com>.
Enjoy The Plugin!
--
Nick of Ethoseo Internet Marketing
-->
	<div id="icon-options-general" class="icon32"><br /></div><h2>GA Universal Settings</h2>
	<?php
		if($_POST['submit'] == "Save Changes" && wp_verify_nonce($_POST['ethoseo_gau_nonce'], plugin_basename( __FILE__ ))){

			update_option("ethoseo_gau_properties", $_POST['properties']);
			update_option("ethoseo_gau_titleoverride", $_POST['titleoverride']);
			update_option("ethoseo_gau_before", $_POST['before']);
			update_option("ethoseo_gau_after", $_POST['after']);

			update_option("ethoseo_gau_infooter", $_POST['infooter']);
			update_option("ethoseo_gau_debug", $_POST['debug']);
			update_option("ethoseo_gau_consoledebug", $_POST['consoledebug']);

			echo '<div id="setting-error-settings_updated" class="updated settings-error"><p><strong>Settings saved.</strong></p></div>';
		}
		$properties = get_option("ethoseo_gau_properties");
	?>
	<?php include(ETHOSEO_GAU_PATH . "inc/support/ethoseo.php"); ?>
	<form method="POST">
		<h3>Properties</h3>
		<table class="form-table" style="clear: left; width: auto;">
			<thead>
				<tr valign="top">
					<th>Property ID</th>
					<th class="users">Ignore Roles</th>
					<th><span class="gau-advanced-only">Property Custom</span></th>
					<th class="add-remove"></th>
					<th class="add-remove"></th>
				</tr>
			</thead>
			<tbody id="properties">
				<?php
					if(!$properties[0]) { 
						$properties = array( array() );
					}
					$roles = $wp_roles->role_names;
					$roles['non-user'] = "Not Logged In";
					foreach ($properties as $key => $item) {
				?>
				<tr valign="top" class="property-group">
					<td><input type="text" name="properties[<?php echo $key; ?>][id]" id="properties_<?php echo $key; ?>_id" placeholder="UA-XXXX-Y" data-pattern-name="properties[++][id]" data-pattern-id="dictionary_++_id"  value="<?php echo htmlspecialchars($item['id']); ?>"  /></td>
					<td class="users">
						<?php foreach($roles as $role => $name){ ?>
							<label><input type="checkbox" name="properties[<?php echo $key; ?>][roles][<?php echo $role; ?>]" id="properties_<?php echo $key; ?>_roles_<?php echo $role; ?>" value="on" data-pattern-name="properties[++][roles][<?php echo $role; ?>]" data-pattern-id="properties_++_roles_<?php echo $role; ?>" data-pattern-value="on"<?php if($item['roles'][$role]){ echo 'checked="checked"'; } ?>> <?php echo $name; ?></label>
						<?php } ?>
					</td>
					<td><textarea name="properties[<?php echo $key; ?>][custom]" id="properties_<?php echo $key; ?>_custom" placeholder="{'cookieDomain': 'none'}" data-pattern-name="properties[++][custom]" data-pattern-id="dictionary_++_custom" class="gau-advanced-only"><?php echo htmlspecialchars($item['custom'], ENT_NOQUOTES); ?></textarea></td>
					<td class="add-remove"><button type="button" class="btnRemove gau-advanced-only">Remove -</button></td>
					<td class="add-remove"><button type="button" class="btnAdd gau-advanced-only">Add +</button></td>
				</tr>
				<?php } ?>
			</tbody>
			<tfoot>
				<tr valign="middle">
					<td class="exp">The Property ID supplied by Google.</td>
					<td class="exp users">GA Universal can ignore properties for certain users. This is helpful if you want to not track admins or something similar.</td>
					<td class="exp"><span class="gau-advanced-only">An <code>opt_configObject</code> as defined in the <a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/field-reference#create" target="_blank">creation of a tracker</a>.</span></td>
					<td class="exp add-remove"></td>
					<td class="exp add-remove"></td>
				</tr>
			</tfoot>
		</table>
		<h3>General Tracking</h3>
		<table class="form-table" style="clear: left; width: auto;">
			<tr valign="top">
				<th scope="row"><label for="overridetitle" id="overridetitlelabel">Override Title Tag</label></th>
				<td>
					<input name="overridetitle" type="checkbox" id="overridetitle" aria-labelledby="overridetitlelabel" value="true" <?php echo get_option("ethoseo_gau_overridetitle") ? 'checked="checked"' : ""; ?>/>
					<label for="overridetitle" class="description">This will push the title of the post or page to Google Analytics, rather than what's found in the title tag.</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="infooter" id="infooterlabel">Place in Footer</label></th>
				<td>
					<input name="infooter" type="checkbox" id="infooter" aria-labelledby="infooterlabel" value="true" <?php echo get_option("ethoseo_gau_infooter") ? 'checked="checked"' : ""; ?>/>
					<label for="infooter" class="description">Google reccomends placing the tracking code in the header of your document, however this can slow down your site.</label>
				</td>
			</tr>
			<tr valign="top" class="gau-advanced-only">
				<th scope="row"><label for="before" id="beforelabel">Code Before ga() Functions</label></th>
				<td class="textarea-d">
					<textarea name="before" type="checkbox" id="before" aria-labelledby="beforelabel"><?php echo get_option("ethoseo_gau_before"); ?></textarea>
					<label for="before" class="description">If you have any code you wish to put before the GA functions, place it here.</label>
				</td>
			</tr>
			<tr valign="top" class="gau-advanced-only">
				<th scope="row"><label for="after" id="afterlabel">Code After ga() Functions</label></th>
				<td class="textarea-d">
					<textarea name="after" type="checkbox" id="after" aria-labelledby="afterlabel"><?php echo get_option("ethoseo_gau_after"); ?></textarea>
					<label for="after" class="description">If you have any code you wish to put after the GA functions, place it here.</label>
				</td>
			</tr>
		</table>
		<h3>Advanced <span id="show-advanced" class="gau-basic-only">(Show Advanced)</span></h3>
		<table class="form-table gau-advanced-only" style="clear: left; width: auto;">
			<tr valign="top">
				<th scope="row"><label for="debug" id="debuglabel">Debug</label></th>
				<td>
					<input name="debug" type="checkbox" id="debug" aria-labelledby="debuglabel" value="true" <?php echo get_option("ethoseo_gau_debug") ? 'checked="checked"' : ""; ?>/>
					<label for="debug" class="description">Debug makes adds comments to the tracking code.</label>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="consoledebug" id="consoledebuglabel">Debug GA to Console</label></th>
				<td>
					<input name="consoledebug" type="checkbox" id="consoledebug" aria-labelledby="consoledebuglabel" value="true" <?php echo get_option("ethoseo_gau_consoledebug") ? 'checked="checked"' : ""; ?>/>
					<label for="consoledebug" class="description"><span style="color:#8a0000">Important:</span> Activating this will stop Google Analytics from functioning. Make sure you deactivate this after debugging or you will not get data.</label>
				</td>
			</tr>
		</table>
		<?php wp_nonce_field( plugin_basename( __FILE__ ), 'ethoseo_gau_nonce'); ?>
		<p class="submit"><input type="submit" name="submit" id="submit" class="button-primary" value="Save Changes"	/></p>
	</form>
</div>
<script>
jQuery(function ($){
	// TODO CSS HIDE ONLY/ONLY
	$("#show-advanced").click(function () {
		$("body").addClass("gau-advanced");
		$(".gau-advanced-only").show();
		repeater( "#properties", ".property-group" );
	});
});

</script>
