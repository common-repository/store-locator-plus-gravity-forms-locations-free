<?php
if (! class_exists('SLP_GFL_Free_AdminUI')) {
	/**
	 * Admin interface methods.
	 *
	 * @package StoreLocatorPlus\SLP_GFL_Free\AdminUI
	 * @author De B.A.A.T. <slp-gfl@de-baat.nl>
	 * @copyright 2014 - 2016 Charleston Software Associates, LLC - De B.A.A.T.
	 *
	 */
	class SLP_GFL_Free_AdminUI {

		//----------------------------------
		// Properties
		//----------------------------------

		/**
		 * The add-on pack from whence we came.
		 * 
		 * @var \SLP_GFL_Free $addon
		 */
		public $addon;

		/**
		 * The base SLPlus object.
		 *
		 * @var \SLPlus $slplus
		 */
		private $slplus;

		/**
		 * The current action as determined by the incoming $_REQUEST['action'] string.
		 *
		 * @var string $current_action
		 */
		public $current_action;

		/**
		 * The tag list settings object.
		 *
		 * @var \SLP_Settings $Settings
		 */
		public $Settings;

		//-------------------------------------
		// Methods : Class Administration
		//-------------------------------------

		/**
		 * Instantiate an object of this class.
		 *
		 * @param mixed[] $params Admin UI properties.
		 */
		function __construct($params = null) {

			// Set properties based on constructor params,
			// if the property named in the params array is well defined.
			//
			if ($params !== null) {
				foreach ($params as $property=>$value) {
					if (property_exists($this,$property)) { $this->$property = $value; }
				}
			}
		}


		//-------------------------------------
		// Methods : WP Hooks and Filters
		//-------------------------------------

		/**
		 * Initialize all our Admin mode goodness.
		 *
		 * This is put into the admin_init stack of WordPress via the admin_menu hook.
		 * It must come AFTER admin_menu to ensure the base plugin has been initialized.
		 *
		 * We call admin_init in the slpgfl to wire this in, then relegate the rest to
		 * this class as this entire class file only loads into RAM when admin_init is
		 * called.  This prevents the base add-on pack from carrying around excess
		 * admin weight when processing front-end pages.
		 *
		 */
		function admin_init() {

			$this->debugMP('msg',__FUNCTION__.' started.');
		}

		/**
		 * Render the GFL_Free tab
		 */
		function render_AdminPage() {

			// Process the actions
			//
			$this->set_CurrentAction();
			$this->debugMP('msg',__FUNCTION__.' started for action =.' . $this->current_action);
			$this->process_Actions();

			// Show Notices
			//
			$this->slplus->notifications->display();

			// Setup and render settings page
			//
			$this->Settings = new SLP_Settings(
				array(
						'prefix'            => SLPLUS_PREFIX,
						'css_prefix'        => SLPLUS_PREFIX,
						'url'               => $this->slplus->url,
						'name'              => SLPLUS_NAME . ' - ' . $this->addon->name,
						'plugin_url'        => $this->slplus->plugin_url,
						'render_csl_blocks' => true,
						'form_name'         => 'locationForm',
						'form_enctype'      => 'multipart/form-data',
						'save_text'         => __('Save Settings','slp-gravity-forms-locations-free'),
						'form_action'       => admin_url().'admin.php?page='.$this->addon->short_slug
					)
			 );

			//-------------------------
			// Navbar Section
			//-------------------------
			$this->Settings->add_section(
				array(
					'name'          => 'Navigation',
					'div_id'        => 'navbar_wrapper',
					'description'   => SLP_Admin_UI::get_instance()->create_Navbar(),
					'innerdiv'      => false,
					'is_topmenu'    => true,
					'auto'          => false,
					'headerbar'     => false
				)
			);

			//-------------------------
			// GFL_Free Settings
			//-------------------------
			$this->render_GFLSettingsPage();

			//------------------------------------------
			// RENDER
			//------------------------------------------
			$this->Settings->render_settings_page();
		}

		/**
		 * Render the admin panel for GFI Pro.
		 */
		function render_GFIProSettingsPage() {
		}

		/**
		 * Render the admin panel for GFL Free.
		 */
		function render_GFLSettingsPage() {

			//-------------------------
			// SLP_GFL_Free Settings Panel
			//-------------------------
			$panelName  = __('Settings','slp-gravity-forms-locations-free');
			$this->Settings->add_section(array('name' => $panelName));

			// Group : Settings
			//
			$groupName  = __('Settings','slp-gravity-forms-locations-free');
			$this->Settings->add_ItemToGroup( array(
					'section'       => $panelName,
					'group'         => $groupName,
					'label'         => __('General','slp-gravity-forms-locations-free'),
					'type'          => 'subheader',
					'show_label'    => false,
					'description'   => '',
				));

			// Duplicates Handling
			//
			$this->Settings->add_ItemToGroup(array(
					'section'       => $panelName,
					'group'         => $groupName,
					'type'          => 'dropdown',
					'setting'       => 'SLP_GFL-gfl_duplicates_handling',
					'selectedVal'   => $this->addon->options['gfl_duplicates_handling'],
					'label'         => __('Duplicates Handling','slp-gravity-forms-locations-free'),
					'description'   =>
						__('How should duplicates be handled?','slp-gravity-forms-locations-free') . ' ' .
						__('Duplicates are records that match on name and complete address with country.','slp-gravity-forms-locations-free') . '<br/>' .
						__('Add (default) will add new records when duplicates are encountered.','slp-gravity-forms-locations-free') . '<br/>' .
						__('Skip will not process duplicate records.','slp-gravity-forms-locations-free') . '<br/>' .
						__('Update will update duplicate records.','slp-gravity-forms-locations-free') . '<br/>' .
						__('To update name and address fields the CSV must have the ID column with the ID of the existing location.','slp-gravity-forms-locations-free')
				,
					'custom'    =>
						array(
							array(
								'label'     => __('Add','slp-gravity-forms-locations-free'),
								'value'     =>'add',
							),
							array(
								'label'     => __('Skip','slp-gravity-forms-locations-free'),
								'value'     =>'skip',
							),
							array(
								'label'     => __('Update','slp-gravity-forms-locations-free'),
								'value'     =>'update',
							),
						)
				)
			);


			//-------------------------
			// GFI_Pro ACTION: slp_gfl_settings_page
			//    params: settings object, section name
			//-------------------------
			do_action('slp_gfl_settings_page', $this->Settings, $panelName, $groupName);



			// Explanation : GFL Custom Post Types
			//
			$groupName = __('GFL Custom Post Types','slp-gravity-forms-locations-free') ;
			$groupName = __('Explanation','slp-gravity-forms-locations-free') ;
			$post_type_url = admin_url('edit.php?post_type=' . POST_TYPE_SLP_GFL_MAPPING);
			$this->Settings->add_ItemToGroup(
				array(
					'section'       => $panelName                   ,
					'group'         => $groupName                   ,
					'label'         => __('GFL Mappings', 'slp-gravity-forms-locations-free'),
					'type'          => 'subheader'                  ,
					'show_label'    => false                        ,
					'description'   =>
						sprintf(__('Manage the <a href="%s">%s</a> post type entries.','slp-gravity-forms-locations-free'), $post_type_url, POST_TYPE_SLP_GFL_MAPPING)
					)
				);

			// Explanation : Documentation
			//
			$this->Settings->add_ItemToGroup(
				array(
					'section'       => $panelName                   ,
					'group'         => $groupName                   ,
					'label'         => __('Documentation', 'slp-gravity-forms-locations-free'),
					'type'          => 'subheader'                  ,
					'show_label'    => false                        ,
					'description'   =>
						sprintf(__('View the <a href="%s" target="csa">documentation</a> for more info.','slp-gravity-forms-locations-free'),$this->slplus->support_url)
					)
				);

		}

		/**
		 * Save our settings.
		 * @return type
		 */
		function save_Settings_for_GFL() {
			$this->debugMP('msg',__FUNCTION__);
			$BoxesToHit = array(
				'gfl_duplicates_handling',
				);
			foreach ($BoxesToHit as $BoxName) {
				if (!isset($_REQUEST[SLPLUS_PREFIX.'-SLP_GFL-'.$BoxName])) {
					$_REQUEST[SLPLUS_PREFIX.'-SLP_GFL-'.$BoxName] = '';
				}
			}

			// Check options, then save them all in one place (serialized)
			//
			array_walk($_REQUEST,array($this,'isSLP_GFL_Free_Option'));
			update_option($this->addon->option_name, $this->addon->options);

			$this->slplus->notifications->add_notice(
					9,
					__('Settings saved.','slp-gravity-forms-locations-free')
					);
			return;
		}

		/** ************************************************************************
		 * Process the actions for this request.
		 **************************************************************************/
		function process_Actions() {

			$this->current_action = $this->set_CurrentAction();
			$this->debugMP('msg',__FUNCTION__.': Process [' . $this->current_action . ']');

			if ( $this->current_action === '' ) { return; }

			switch ($this->current_action) {

				// Save the settings
				case 'update':
				case 'save_settings':
					$this->save_Settings_for_GFL();

					//-------------------------
					// GFI_Pro ACTION: slp_gfl_save_settings
					//-------------------------
					do_action('slp_gfl_save_settings');

					break;

				// Stuff that is not an exact string match
				//
				default:

					// Unsupported action
					$this->debugMP('msg',__FUNCTION__,"Unsupported Action: {" . $this->current_action . "}");
					break;
			}
		}

		/**
		 * Set the current action being executed by the plugin.
		 */
		function set_CurrentAction() {
			//if ( !isset( $_REQUEST['act'] ) ) { $this->current_action = '';                               }
			// Check for dedicated actions
			if ( isset( $_REQUEST['action'] ) && -1 != $_REQUEST['action'] ) {
				$this->current_action = strtolower( $_REQUEST['action'] );
			} elseif ( isset( $_REQUEST['action2'] ) && -1 != $_REQUEST['action2'] ) {
				$this->current_action = strtolower( $_REQUEST['action2'] );
			} elseif ( isset( $_REQUEST['act'] ) ) {
				$this->current_action = strtolower( $_REQUEST['act'] );
			}

			// Special Processing of Actions
			//
			switch ($this->current_action) {
				case 'edit':
					if ( isset( $_REQUEST['action'] ) ) {
						$this->current_action = $_REQUEST['action'];
					}
					break;

				default:
					break;
			}
			return $this->current_action;
		}

		/**
		 * Set the user-managed-location options from the incoming REQUEST
		 *
		 * @param mixed $val - the value of a form var
		 * @param string $key - the key for that form var
		 */
		function isSLP_GFL_Free_Option($val,$key) {
			$simpleKey = preg_replace('/^'.SLPLUS_PREFIX.'-SLP_GFL-/','',$key);
			if ($simpleKey !== $key){
				$this->addon->options[$simpleKey] = $val;
			}
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
				$hdr = 'AdminUI: ' . $hdr;
			}
			$this->addon->debugMP($type,$hdr,$msg,NULL,NULL,true);
		}
	}
}
