<?php

// Define some constants for use by this add-on
//	Our SLP_GFL_MAPPING post_type.
if ( defined( 'POST_TYPE_SLP_GFL_MAPPING'         ) === false ) { define( 'POST_TYPE_SLP_GFL_MAPPING'         , 'slp_gfl_mapping'                          ); } // 

//	Our SLP_GFL_ generic definitions.
if ( defined( 'SLP_GFL_MAPPING_ID_SLUG'           ) === false ) { define( 'SLP_GFL_MAPPING_ID_SLUG'           , 'slp_gfl_mapping_id'                       ); } // 
if ( defined( 'SLP_GFL_SLUG_PREFIX'               ) === false ) { define( 'SLP_GFL_SLUG_PREFIX'               , 'slp_gfl_'                                 ); } // 
if ( defined( 'SLP_GFL_FORMS_ID_SLUG'             ) === false ) { define( 'SLP_GFL_FORMS_ID_SLUG'             , 'slp_gfl_forms_id'                         ); } // 
if ( defined( 'SLP_GFL_ENTRY_ID_SLUG'             ) === false ) { define( 'SLP_GFL_ENTRY_ID_SLUG'             , 'slp_gfl_entry_id'                         ); } // 
if ( defined( 'SLP_GFL_POST_ID_SLUG'              ) === false ) { define( 'SLP_GFL_POST_ID_SLUG'              , 'slp_gfl_post_id'                          ); } // 
if ( defined( 'SLP_GFL_RESUME_TOKEN_SLUG'         ) === false ) { define( 'SLP_GFL_RESUME_TOKEN_SLUG'         , 'slp_gfl_resume_token'                     ); } // 
if ( defined( 'SLP_GFL_LOCATION_ID_SLUG'          ) === false ) { define( 'SLP_GFL_LOCATION_ID_SLUG'          , 'slp_gfl_location_id'                      ); } // 
if ( defined( 'SLP_GFL_LOCATION_NAME_SLUG'        ) === false ) { define( 'SLP_GFL_LOCATION_NAME_SLUG'        , 'slp_gfl_location_name'                    ); } // 
if ( defined( 'SLP_GFL_OPERATOR_IS'               ) === false ) { define( 'SLP_GFL_OPERATOR_IS'               , '='                                        ); } // 
if ( defined( 'SLP_GFL_OPERATOR_IS_NOT'           ) === false ) { define( 'SLP_GFL_OPERATOR_IS_NOT'           , '!='                                       ); } // 
if ( defined( 'SLP_GFL_FORMS_ID_NONE'             ) === false ) { define( 'SLP_GFL_FORMS_ID_NONE'             , 'no_gf_form'                               ); } // 

//	Our SLP_GFI_ specific definitions.
if ( defined( 'SLP_GFL_OPTION_NAME'               ) === false ) { define( 'SLP_GFL_OPTION_NAME'               , 'slplus-gfl-free-options'                  ); } // 

//	Gravity Forms minimum required version
if ( defined( 'SLP_GRAVITY_FORMS_MINIMUM_VERSION' ) === false ) { define( 'SLP_GRAVITY_FORMS_MINIMUM_VERSION' , '1.9'                                      ); } // 
if ( defined( 'SLP_GRAVITY_FORMS_PLUGIN_NAME'     ) === false ) { define( 'SLP_GRAVITY_FORMS_PLUGIN_NAME'     , 'Gravity Forms'                            ); } // 
if ( defined( 'SLP_GRAVITY_FORMS_DOWNLOAD_URL'    ) === false ) { define( 'SLP_GRAVITY_FORMS_DOWNLOAD_URL'    , 'https://bit.ly/getgravityforms'           ); } // 

//	Our SLP_GFL_FREE_ definitions.
if ( defined( 'SLP_GFL_FREE_SLUG'                  ) === false ) { define( 'SLP_GFL_FREE_SLUG'                , 'slp-gravity-forms-locations-free'         ); } // 

// Get the Gravity Forms specific actions and filters
// And include SLP_GFL specific functions
require_once('slp-gfl-gravityforms.php');

if (class_exists('SLP_GFL_Mapping') == false) {
	require_once('class.slp-gfl-mapping.php');
}

// Make sure the class is only defined once.
//
if (!class_exists('SLP_GFL_Free')) {
	require_once( WP_PLUGIN_DIR . '/store-locator-le/include/base_class.addon.php');


	/**
	 * The Gravity Forms add-on pack for Store Locator Plus.
	 *
	 * @package StoreLocatorPlus\SLP_GFL_Free
	 * @author De B.A.A.T. <slp-gfl@de-baat.nl>
	 * @copyright 2014 - 2016 Store Locator Plus - DeBAAT
	 */
	class SLP_GFL_Free extends SLP_BaseClass_Addon {
		const ADMIN_PAGE_SLUG = 'slp-gravity-forms-locations-free';

		/**
		 * Our admin page slug.
		 */
		//const ADMIN_PAGE_SLUG = 'slp_gfl_free';

		/**
		 * Settable options for this plugin.
		 *
		 * @var mixed[] $options
		 */
		public  $options                     = array(
			'installed_version'              => '',
			'gfl_skip_geocoding'             => 'off',
			'gfl_duplicates_handling'        => 'update',
		);

		/**
		 * Invoke the plugin.
		 *
		 * This ensures a singleton of this plugin.
		 *
		 * @static
		 */
		public static function init() {
			static $instance = false;
			if (!$instance) {
				load_plugin_textdomain('slp-gravity-forms-locations-free', false, SLP_GFL_REL_DIR . '/languages/');
				$instance = new SLP_GFL_Free(
					array(
						'version'                   => SLP_GFL_VERSION,
						'min_slp_version'           => SLP_GFL_MIN_SLP,

						'name'                      => __('Gravity Forms Locations', 'slp-gravity-forms-locations-free'),
						'option_name'               => SLP_GFL_OPTION_NAME,
						'file'                      => SLP_GFL_FILE,

						'admin_class_name'          => 'SLP_GFL_Free_Admin',
						'activation_class_name'     => 'SLP_GFL_Free_Activation',
						'ajax_class_name'           => 'SLP_GFL_Free_AJAX',
						'userinterface_class_name'  => 'SLP_GFL_Free_UI',
					)
				);
			}

			// Validate this version of GFL, priority should assure late validation but BEFORE GFI
			add_action('slp_init_complete',              array($instance,'slp_gfl_VersionCheck'   ), 98   );

			return $instance;
		}

		/**
		 * Initialize the options properties from the WordPress database.
		 */
		function init_options() {
			parent::init_options();
		}

		/**
		 * Compare current plugin version with minimum required.
		 *
		 * Set a notification message.
		 * Disable this add-on pack if requirement is not met.
		 */
		function slp_gfl_VersionCheck() {

			$this->debugMP('msg',__FUNCTION__ . ' Validate for ' . $this->name );
			$slp_validated_ok = true;

			// Validate availability of Gravity Forms
			if ( ! slp_gfl_is_gravityforms_supported() ) {
				$slp_validated_ok = false;
				if (is_admin()) {
					if (isset($this->slplus->notifications)) {
						$this->slplus->notifications->add_notice(4, '<strong>' .
							sprintf(__('SLP %s has been deactivated.', 'slp-gravity-forms-locations-free'
									), $this->name
							) . '<br/> ' .
							'</strong>' .
							sprintf(__('This plugin requires %s to be installed and active.', 'slp-gravity-forms-locations-free'
									), SLP_GRAVITY_FORMS_PLUGIN_NAME
							) .
							'<br/> ' .
							sprintf(__('Please <a href="%s">download</a> at least version %s of %s and try again.', 'slp-gravity-forms-locations-free'
									), SLP_GRAVITY_FORMS_DOWNLOAD_URL, SLP_GRAVITY_FORMS_MINIMUM_VERSION, SLP_GRAVITY_FORMS_PLUGIN_NAME
							) .
							'<br/>'
						);
					}
				}
			}

 
			// Act on validation result
			if (! $slp_validated_ok ) {
				$this->debugMP('msg',__FUNCTION__ . ' ' . $this->name . ' VALIDATED FALSE so deactivate_plugins [' . $this->slug . ']!');
				deactivate_plugins($this->slug);
			}

			return $slp_validated_ok;
		}

		/**
		 * Add the tabs/main menu items.
		 *
		 * @param mixed[] $menuItems
		 * @return mixed[]
		 */
		public function filter_AddMenuItems($menuItems) {
			$this->debugMP('msg',__FUNCTION__);

			$this->createobject_Admin();
			$this->admin_menu_entries = array(
				array(
					'label'     => __('Gravity Forms','slp-gravity-forms-locations-free'),
					'slug'      => $this->addon->short_slug,
					'class'     => $this->admin,
					'function'  => 'createpage_GFL_Free_Admin'
				),
			);

			return parent::filter_AddMenuItems( $menuItems );

		}

		/**
		 * Create a Map Settings Debug My Plugin panel.
		 *
		 * @return null
		 */
		static function create_DMPPanels() {
			if (!isset($GLOBALS['DebugMyPlugin'])) {
				return;
			}
			if (class_exists('DMPPanelSLPGFLFree') == false) {
				require_once('class.dmppanels.php');
			}
			$GLOBALS['DebugMyPlugin']->panels['slp.gflf'] = new DMPPanelSLPGFLFree();
		}


		/**
		 * Add DebugMyPlugin messages.
		 *
		 * @param string $panel - panel name
		 * @param string $type - what type of debugging (msg = simple string, pr = print_r of variable)
		 * @param string $header - the header
		 * @param string $message - what you want to say
		 * @param string $file - file of the call (__FILE__)
		 * @param int $line - line number of the call (__LINE__)
		 * @param boolean $notime - show time? default true = yes.
		 * @return null
		 */
		public static function debugMP($type='msg', $header='',$message='',$file=null,$line=null,$notime=true) {

			$panel='slp.gflf';

			// Panel not setup yet?  Return and do nothing.
			//
			if (
				!isset($GLOBALS['DebugMyPlugin']) ||
				!isset($GLOBALS['DebugMyPlugin']->panels[$panel])
			   ) {
				return;
			}

			// Do normal real-time message output.
			//
			switch (strtolower($type)):
				case 'pr':
					$GLOBALS['DebugMyPlugin']->panels[$panel]->addPR($header,$message,$file,$line,$notime);
					break;
				default:
					$GLOBALS['DebugMyPlugin']->panels[$panel]->addMessage($header,$message,$file,$line,$notime);
					break;
			endswitch;
		}

	}

	// Hook to invoke the plugin.
	//
	add_action('init',           array('SLP_GFL_Free', 'init'               ), 9 );

	// DMP
	//
	add_action('dmp_addpanel',   array('SLP_GFL_Free', 'create_DMPPanels'   ));

}

// Dad. Explorer. Rum Lover. Code Geek. Not necessarily in that order.