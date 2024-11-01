<?php
if (! class_exists('SLP_GFL_Free_Activation')) {
	require_once(SLPLUS_PLUGINDIR.'/include/base_class.activation.php');

	/**
	 * Manage plugin activation.
	 *
	 * @package StoreLocatorPlus\SLP_GFL_Free\Activation
	 * @author De B.A.A.T. <slp-gfl@de-baat.nl>
	 * @copyright 2014 - 2016 Charleston Software Associates, LLC - De B.A.A.T.
	 *
	 */
	class SLP_GFL_Free_Activation extends SLP_BaseClass_Activation {

		/**
		 * Update or create the data tables.
		 *
		 * This can be run as a static function or as a class method.
		 */
		function update() {

			// Add (or update) the Extended Data fields for GFI
			$this->slplus->database->extension->add_field( __( 'GF Entry ID'      ,'slp-gravity-forms-locations-free' ), 'varchar', array( 'slug' => SLP_GFL_ENTRY_ID_SLUG,     'addon' => $this->addon->short_slug ), 'wait' );
			$this->slplus->database->extension->add_field( __( 'GF Forms ID'      ,'slp-gravity-forms-locations-free' ), 'varchar', array( 'slug' => SLP_GFL_FORMS_ID_SLUG,     'addon' => $this->addon->short_slug ), 'wait' );
			$this->slplus->database->extension->add_field( __( 'GF Mapping ID'    ,'slp-gravity-forms-locations-free' ), 'varchar', array( 'slug' => SLP_GFL_MAPPING_ID_SLUG,   'addon' => $this->addon->short_slug ), 'wait' );
			$this->slplus->database->extension->add_field( __( 'GF Post ID'       ,'slp-gravity-forms-locations-free' ), 'varchar', array( 'slug' => SLP_GFL_POST_ID_SLUG,      'addon' => $this->addon->short_slug ), 'wait' );
			$this->slplus->database->extension->add_field( __( 'GF Resume Token'  ,'slp-gravity-forms-locations-free' ), 'varchar', array( 'slug' => SLP_GFL_RESUME_TOKEN_SLUG, 'addon' => $this->addon->short_slug ), 'wait' );
            $this->slplus->database->extension->update_data_table( array('mode'=>'force') );

			$this->addon->options['installed_version'] =  $this->addon->version ;  // made persistent via addon admin_init call

		}
	}
}
// Dad. Explorer. Rum Lover. Code Geek. Not necessarily in that order.