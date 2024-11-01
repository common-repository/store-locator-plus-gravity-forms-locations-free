<?php

/**
 * @package StoreLocatorPlus\SLP_GFL_Free\SLP_GFL_Mapping
 * @author De B.A.A.T. <slp-gfl@de-baat.nl>
 * @copyright 2014 - 2016 Charleston Software Associates, LLC - De B.A.A.T.
 */
class SLP_GFL_Mapping {

	/**
	 * The mapping (post) ID.
	 *
	 * @var int
	 */
	public $id;

	/**
	 * The slp_gfl_mapping post object
	 */
	public $mapping;

	/**
	 * Indication whether a condition is used
	 */
	public $condition_enabled;
	public $condition_field_id;
	public $condition_operator;
	public $condition_value;

	/**
	 * The slp_gfl_mapping fields
	 */
	public $fields;
	protected $slplus;

	//////////////////////////////////////////////////

	/**
	 * Construct and intialize mapping object
	 *
	 */
	public function __construct( $post_id ) {
		$this->id      = $post_id;
		$this->mapping = get_post( $post_id );

		// Load
		$this->condition_enabled        = get_post_meta( $post_id, '_slp_gfl_mapping_condition_enabled', true );
		$this->condition_field_id       = get_post_meta( $post_id, '_slp_gfl_mapping_condition_field_id', true );
		$this->condition_operator       = get_post_meta( $post_id, '_slp_gfl_mapping_condition_operator', true );
		$this->condition_value          = get_post_meta( $post_id, '_slp_gfl_mapping_condition_value', true );

		$fields = get_post_meta( $post_id, '_slp_gfl_mapping_fields', true );
		$this->fields                   = is_array( $fields ) ? $fields : array();

		// Set the local slplus object
		global $slplus_plugin;
		$this->slplus = $slplus_plugin;

	}

	//////////////////////////////////////////////////

	/**
	 * Get the information from the entry, using the mapping and existing location data
	 *
	 * @param array $entry
	 * @param array $form
	 * @param array $mapping
	 */
	public function get_location_data( $entry, $form ) {

		// Check input parameters
		if ( ! isset($entry['id']) ) { return false; }
		if ( ! isset($form['id'])  ) { return false; }

		// Init the locationData
		$locationData = array();
		$locationData['sl_store'] = 'test';

		// Check whether this is an existing location and get the data
		//
		$gflLocationID   = gform_get_meta( $entry['id'], SLP_GFL_LOCATION_ID_SLUG );
		$curLocationData = $this->slplus->currentLocation->get_location( $gflLocationID );

		// Current Location Data found
		if ( ! is_wp_error( $curLocationData ) ) {
			$locationData          = $this->slplus->currentLocation->locationData;
            unset( $locationData['id'] );
			$locationData['sl_id'] = $gflLocationID;
		}

		$locationData[SLP_GFL_ENTRY_ID_SLUG]     = $entry['id'];
		$locationData[SLP_GFL_FORMS_ID_SLUG]     = $form['id'];
		$locationData[SLP_GFL_MAPPING_ID_SLUG]   = $this->id;
		$locationData[SLP_GFL_POST_ID_SLUG]      = get_the_ID();	// Get the post_id of the current post
		$locationData[SLP_GFL_RESUME_TOKEN_SLUG] = '';


		// Get the locationData from the $entry, using the $mapping fields
		foreach ($this->fields as $locationLabel => $entryLabel) {
			//$locationData[$locationLabel] = '';
			if (isset($entry[$entryLabel])) {
				$locationData[$locationLabel] = $this->gfl_get_value_entry_detail($entry[$entryLabel]);
			}
		}

		$this->debugMP('pr',__FUNCTION__ . ' locationData : ', $locationData);

		return $locationData;
	}

	/**
	 * Get the first entry of a combined set separated by "|:|" like the images of a form
	 */
	public function gfl_get_value_entry_detail( $value ) {
		$ary = explode( "|:|", $value );
		return count( $ary ) > 0 ? $ary[0] : '';
	}

	/**
	 * Check if the Mapping condition is true
	 *
	 * @param mixed $entry
	 * @param mixed $mapping
	 */
	public function is_condition_true( $entry ) {
		$result = true;

		if ( $this->condition_enabled ) {
			if (isset($entry[$this->condition_field_id])) {
				$value = $entry[$this->condition_field_id];
			}

			if ( empty( $value ) ) {
				// unknown field
				$result = true;
			} else {
				switch ( $this->condition_operator ) {
					case SLP_GFL_OPERATOR_IS:
						$result = $value == $this->condition_value;
						break;
					case SLP_GFL_OPERATOR_IS_NOT:
						$result = $value != $this->condition_value;
						break;
					default: // unknown operator
						$result = true;
						break;
				}
			}
		} else {
			// condition is disabled, result is true
			$result = true;
		}

		return $result;
	}

	/**
	 * Simplify the plugin debugMP interface.
	 *
	 * Typical start of function call: $this->debugMP('msg',__FUNCTION__);
	 *
	 * @param string $type
	 * @param string $hdr
	 * @param string $msg
	 */
	function debugMP($type,$hdr,$msg='') {
		global $slplus_plugin;
		if (!is_object($slplus_plugin)) {
			return;
		}

		if (($type === 'msg') && ($msg!=='')) {
			$msg = esc_html($msg);
		}
		if (($hdr!=='')) {
			$hdr = 'GFM: ' . $hdr;
		}
		$slplus_plugin->AddOns->instances[SLP_GFL_FREE_SLUG]->debugMP($type,$hdr,$msg,NULL,NULL,true);
	}

}
