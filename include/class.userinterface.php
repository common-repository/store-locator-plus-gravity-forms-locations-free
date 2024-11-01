<?php
if (! class_exists('SLP_GFL_Free_UI')) {
	require_once(SLPLUS_PLUGINDIR.'/include/base_class.userinterface.php');

	/**
	 * Holds the UI-only code.
	 *
	 * This allows the main plugin to only include this file in the front end
	 * via the wp_enqueue_scripts call.   Reduces the back-end footprint.
	 *
	 * @package StoreLocatorPlus\SLP_GFL_Free\AdminUI
	 * @author De B.A.A.T. <slp-gfl@de-baat.nl>
	 * @copyright 2014 * 2016 Charleston Software Associates, LLC - De B.A.A.T.
	 */
	class SLP_GFL_Free_UI  extends SLP_BaseClass_UI {

		//-------------------------------------
		// Properties
		//-------------------------------------

		/**
		 * This addon pack.
		 *
		 * @var \SLP_GFL_Free $addon
		 */
		public $addon;

		//-------------------------------------
		// Methods
		//-------------------------------------

		/**
		 * Add WordPress and SLP hooks and filters.
		 *
		 * WP syntax reminder: add_filter( <filter_name> , <function> , <priority> , # of params )
		 *
		 * Remember: <function> can be a simple function name as a string
		 *  - or - array( <object> , 'method_name_as_string' ) for a class method
		 * In either case the <function> or <class method> needs to be declared public.
		 *
		 * @link http://codex.wordpress.org/Function_Reference/add_filter
		 *
		 */
		public function add_hooks_and_filters() {
			$this->debugMP('msg',__FUNCTION__ . ' started JdB');
		    parent::add_hooks_and_filters();

			/**
			 * Simplify the Gravity Forms action and filters
			 *
			 */
//			add_filter( 'gform_entry_info',                 array( $this, 'slp_gfl_gform_entry_info'          ),  10, 2 );

//			add_action( 'gform_entry_post_save',            array( $this, 'slp_gfl_gform_entry_post_save'     ),  10, 2 );
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
			if (($type === 'msg') && ($msg!=='')) {
				$msg = esc_html($msg);
			}
			if (($hdr!=='')) {
				$hdr = 'UI: ' . $hdr;
			}
			$this->addon->debugMP($type,$hdr,$msg,NULL,NULL,true);
		}
	}
}