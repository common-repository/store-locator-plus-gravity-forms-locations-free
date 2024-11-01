<?php



/**
 * Simplify the Gravity Forms action and filters
 *
 */
add_filter( 'gform_addon_navigation',           'slp_gfl_gform_addon_navigation'             );
add_filter( 'gform_entry_info',                 'slp_gfl_gform_entry_info'          ,  10, 2 );

add_action( 'gform_entry_post_save',            'slp_gfl_gform_entry_post_save'     ,  10, 2 );

/**
 * Gravity Forms addon navigation
 *
 * @param $menus array with addon menu items
 * @return array
 */
function slp_gfl_gform_addon_navigation( $menus ) {

	// Add the submenu for the SLP_GFL_Mappings
//	$this->debugMP('msg', __FUNCTION__ . ' started.');
	$menus[] = array(
		'name'       => 'edit.php?post_type=' . POST_TYPE_SLP_GFL_MAPPING,
		'label'      => __( 'GFL Mappings', 'slp-gravity-forms-locations-free' ),
		'callback'   => null,
		'permission' => 'manage_options',
	);
//			$this->debugMP('pr', __FUNCTION__ . ' menus:', $menus);

	return $menus;
}

/**
 * Render entry info of the specified form and lead
 *
 * @param string $form_id
 * @param array $lead
 */
function slp_gfl_gform_entry_info( $form_id, $lead ) {
	slp_gfl_debugMP('msg',__FUNCTION__ . ' started');
	$slp_gfl_mapping_id = gform_get_meta( $lead['id'], SLP_GFL_MAPPING_ID_SLUG );
	slp_gfl_debugMP('msg',__FUNCTION__ . ' slp_gfl_mapping_id = ' . $slp_gfl_mapping_id);

	if ( $slp_gfl_mapping_id ) {
		printf(
			'<a href="%s">%s</a>',
			get_edit_post_link( $slp_gfl_mapping_id ),
			get_the_title( $slp_gfl_mapping_id )
		);
	}
}

/**
 * Entry post save
 *
 * @param array $entry
 * @param array $form
 */
function slp_gfl_gform_entry_post_save( $entry, $form ) {
	slp_gfl_debugMP('msg',__FUNCTION__ . ' started');

	// Update entry meta with slp_gfl_mapping ID
	$slp_gfl_debug = array();

	// Check for spam
	if ($entry["status"] == "spam") {
		return $entry;
	}

	// Get the mapping to use for GFL to SLP
	$gflMapping = get_slp_gfl_mapping_by_form_id( $form['id'] );
	if ( $gflMapping != null ) {

		// Check for is_condition_true
		if ( ! $gflMapping->is_condition_true($entry)) {
			return $entry;
		}

		// Get the data for this new location
		$gflLocationData = $gflMapping->get_location_data( $entry, $form );
		$slp_gfl_debug['gflMapping'] = $gflMapping;

		if ($gflLocationData) {
			global $slplus_plugin;

			$slp_gfl_debug['gflLocationData'] = $gflLocationData;
			// Create the new location
//			$gflOptions = $this->addon->options;
			$gflOptions = get_option(SLP_GFL_OPTION_NAME);
			$duplicates_handling = $gflOptions['gfl_duplicates_handling'];


			//-------------------------
			// GFI_Pro FILTER: slp_get_gfi_option_value
			//   @params: $gfiOptionName, $gfiDefault
			$skip_geocoding = $slplus_plugin->is_CheckTrue( $gflOptions['gfl_skip_geocoding'] );
			$skip_geocoding = apply_filters('slp_get_gfi_option_value', 'gfi_skip_geocoding', $skip_geocoding);

			// Add the locationData as a new or updated location to the database
			$resultOfAdd = $slplus_plugin->currentLocation->add_to_database($gflLocationData, $duplicates_handling, $skip_geocoding);

			// Check skip_geocoding to remove the geocoding if necessary
			if ( $slplus_plugin->is_CheckTrue( $skip_geocoding ) ) {
				slp_gfl_debugMP('msg',__FUNCTION__ . ' skip_geocoding is set so remove lat/long to prevent immediate publishing.');
				$slplus_plugin->currentLocation->latitude  = '';
				$slplus_plugin->currentLocation->longitude = '';
				$slplus_plugin->currentLocation->MakePersistent();
			}

			// Store meta data in the entry
			gform_update_meta( $entry['id'], SLP_GFL_RESUME_TOKEN_SLUG,   gmdate('Y-m-d H:i:59') );
			gform_update_meta( $entry['id'], SLP_GFL_POST_ID_SLUG,        get_the_ID() );
			gform_update_meta( $entry['id'], SLP_GFL_MAPPING_ID_SLUG,     $gflMapping->id );
			gform_update_meta( $entry['id'], SLP_GFL_LOCATION_ID_SLUG,    $slplus_plugin->currentLocation->id );
			gform_update_meta( $entry['id'], SLP_GFL_LOCATION_NAME_SLUG,  $slplus_plugin->currentLocation->store );

			$slp_gfl_debug['gflOptions']			= $gflOptions;
			$slp_gfl_debug['duplicates_handling']	= $duplicates_handling;
			$slp_gfl_debug['skip_geocoding']		= $skip_geocoding;
			$slp_gfl_debug['resultOfAdd']			= $resultOfAdd;
			$slp_gfl_debug['gflMappingID']			= $gflMapping->id;
			$slp_gfl_debug['gflLocationID']			= $slplus_plugin->currentLocation->id;
		}

	}

	$slp_gfl_debug['entry']	= $entry;
	$slp_gfl_debug['form']	= $form;
	slp_gfl_debugMP('pr', __FUNCTION__ . ' slp_gfl_debug: ',	$slp_gfl_debug);

	return $entry;
}

/**
 * Simplify the plugin debugMP interface for static functions.
 *
 * Typical start of function call: $this->debugMP('msg',__FUNCTION__);
 *
 * @param string $type
 * @param string $hdr
 * @param string $msg
 */
function slp_gfl_log_debug($type, $hdr, $msg='') {

	// Store the debugMP Info in the options for later analysis
	switch ($type) {
		case 'msg':
			GFCommon::log_debug('SLP_GFL_msg: ' . $hdr);
			if ($msg != '') {
				GFCommon::log_debug('SLP_GFL: ' . $msg);
			}
			break;
		case 'pr':
			GFCommon::log_debug('SLP_GFL_PR: ' . $hdr);
			//GFCommon::log_debug('SLP_GFL: ' . $msg);
			break;

		default:
			GFCommon::log_debug('SLP_GFL DEF: ' . $hdr);
			break;
	}
}

/**
 * Simplify the plugin debugMP interface for static functions.
 *
 * Typical start of function call: $this->debugMP('msg',__FUNCTION__);
 *
 * @param string $type
 * @param string $hdr
 * @param string $msg
 */
function slp_gfl_debugMP($type, $hdr, $msg='') {
	global $slplus_plugin;
	if (!is_object($slplus_plugin)) {
		return;
	}

	if (($type === 'msg') && ($msg!=='')) {
		$msg = esc_html($msg);
	}
	if (($hdr!=='')) {
		$hdr = 'Static: ' . $hdr;
	}
	SLP_GFL_Free::debugMP($type, $hdr, $msg, NULL, NULL, true);
}

function get_slp_gfl_mapping_by_form_id( $form_id ) {
	global $wpdb;

	$slp_gfl_mapping = null;

	$db_query = $wpdb->prepare( "
		SELECT
			ID
		FROM
			$wpdb->posts
				LEFT JOIN
			$wpdb->postmeta
					ON (
				ID = post_ID
					AND
				meta_key = '_slp_gfl_mapping_form_id'
			)
		WHERE
			post_status = 'publish'
				AND
			post_type = '%s'
				AND
			meta_value = %s
		;
	", POST_TYPE_SLP_GFL_MAPPING, $form_id );

	$post_id = $wpdb->get_var( $db_query );

	if ( $post_id ) {
		$slp_gfl_mapping = new SLP_GFL_Mapping($post_id);
	}

	return $slp_gfl_mapping;
}

function get_slp_gfl_mapping_form_title( $form_id ) {
	$title = null;

	// Get the form info from the GFAPI
	$gf_form = GFAPI::get_form($form_id);
	if (isset($gf_form['title'])) {
		$title = $gf_form['title'];
	}
	slp_gfl_debugMP('pr', __FUNCTION__ . ' title: ',	$title);

	return $title;
}

/**
 * Buckaroo check if the key starts with an prefix
 *
 * @param string $string
 * @param string $prefix
 * @return boolean true if match, false otherwise
 */
function slp_gfl_string_starts_with( $string, $prefix ) {
	$string = substr( $string, 0, strlen( $prefix ) );

	return strcasecmp( $string, $prefix ) == 0;
}

/**
 * Checks if Gravity Forms is supported
 *
 * @return true if Gravity Forms is supported, false otherwise
 */
function slp_gfl_is_gravityforms_supported() {
	if ( class_exists( 'GFCommon' ) ) {
		return version_compare( GFCommon::$version, SLP_GRAVITY_FORMS_MINIMUM_VERSION, '>=' );
	} else {
		return false;
	}
}
