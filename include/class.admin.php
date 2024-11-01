<?php

if (! class_exists('SLP_GFL_Free_Admin')) {
	require_once(SLPLUS_PLUGINDIR.'/include/base_class.admin.php');

	/**
	 * Admin interface methods.
	 *
	 * @package StoreLocatorPlus\SLP_GFL_Free\Admin
	 * @author De B.A.A.T. <slp-gfl@de-baat.nl>
	 * @copyright 2014 - 2019 Charleston Software Associates, LLC - De B.A.A.T.
	 *
	 */
	class SLP_GFL_Free_Admin extends SLP_BaseClass_Admin {

		/**
		 * This addon pack.
		 *
		 * @var \SLP_GFL_Free $addon
		 */
		public $addon;

		//-------------------------------------
		// Methods : Base Override
		//-------------------------------------

		/**
		 * Execute some admin startup things for this add-on pack.
		 *
		 * Base plugin override.
		 */
		function do_admin_startup() {
			$this->debugMP('msg',__FUNCTION__);
			parent::do_admin_startup();
		}

		/**
		 * Hooks and Filters for this plugin.
		 *
		 * Base plugin override.
		 */
		function add_hooks_and_filters() {
			$this->debugMP('msg',__FUNCTION__ . ' started JdB');
			parent::add_hooks_and_filters();

			// wpcsl_admin_slugs : skin and script the admin UI
			//
			add_filter( 'wpcsl_admin_slugs',                array( $this, 'filter_AddOurAdminSlug'            )        );

			// Admin skinning and scripts
			add_action( 'admin_enqueue_scripts',            array( $this, 'action_EnqueueAdminScriptsGFL'     )        );

			// Setup for GFL specific items
			$this->register_cpt_slp_gfl_mapping();

		}

		/**
		 * Create the SLP_GFL_Free Admin page.
		 *
		 * It is hooked here to ensure the AdminUI object is instantiated first.
		 */
		function createpage_GFL_Free_Admin() {
			$this->debugMP('msg',__FUNCTION__);
			$this->createobject_AdminInterface();
			$this->AdminUI->render_AdminPage();
		}

		/**
		 * Create an admin interface object.
		 *
		 * The admin interface handles all UI, API, and other admin-panel based operations.
		 */
		function createobject_AdminInterface() {
			$this->debugMP('msg',__FUNCTION__);
			if (class_exists('SLP_GFL_Free_AdminUI') == false) {
				require_once($this->addon->dir . 'include/class.adminui.php');
			}
			if (!isset($this->AdminUI)) {
				$this->AdminUI =
					new SLP_GFL_Free_AdminUI(
						array(
							'slplus'    => $this->slplus,
							'addon'     => $this->addon,
						)
					);
			}
		}

		//-------------------------------------
		// Methods : Custom : Actions
		//-------------------------------------

		/**
		 * Add our admin pages to the valid admin page slugs.
		 *
		 * @param string[] $slugs admin page slugs
		 * @return string[] modified list of admin page slugs
		 */
		function filter_AddOurAdminSlug($slugs) {

			$this->debugMP('msg', __FUNCTION__ . ' started.');

			return array_merge($slugs,
					array(
						$this->addon->short_slug,
						SLP_ADMIN_PAGEPRE . $this->addon->short_slug,
						)
					);
		}

	    /**
	     * Add meta links specific for this AddOn.
	     *
	     * @param string[] $links
	     * @param string   $file
	     *
	     * @return string
	     */
	    function add_meta_links( $links, $file ) {
		    if ( $file == $this->addon->slug ) {

		    	$link_text = __( 'Documentation', 'slp-gravity-forms-locations-free' );
			    $links[] = sprintf( '<a href="%s" title="%s" target="store_locator_plus">%s</a>' , $this->slplus->support_url .'/our-add-ons/' . $this->addon->name , $link_text, $link_text);


		    	$link_text = __( 'Settings', 'slp-gravity-forms-locations-free' );
			    $links[] = sprintf( '<a href="%s" title="%s">%s</a>' , admin_url( 'admin.php?page=' . SLP_GFL_Free::ADMIN_PAGE_SLUG ) , $link_text, $link_text);

			    $newer_version = $this->get_newer_version();
			    if ( ! empty( $newer_version ) ) {
			    	$links[] = '<strong>' . sprintf( __( 'Version %s in production ', 'slp-gravity-forms-locations-free' ), $newer_version ) . '</strong>';
			    }

		    }

		    return $links;
	    }

		/**
		 * Enqueue the admin scripts.
		 *
		 * @param string $hook
		 */
		function action_EnqueueAdminScriptsGFL($hook) {
			$this->debugMP('msg',__FUNCTION__ . ' hook=' . $hook);

			wp_enqueue_script ( 'slp_gfl_script' , $this->addon->url . '/js/gfl_gravityforms.js' );

			// Load up the gfl_admin.css style sheet
			//
			wp_register_style('slp_gfl_style', $this->addon->url . '/css/gfl_admin.css');
			wp_enqueue_style('slp_gfl_style');
			wp_enqueue_style(SLP_Admin_UI::get_instance()->styleHandle);

		}

		//-------------------------------------
		// Methods : Custom Post Type slp_gfl_mapping
		//-------------------------------------

		/**
		 * Create the Custom Post Type slp_gfl_mapping
		 *
		 */
		function register_cpt_slp_gfl_mapping() {
			$this->debugMP('msg',__FUNCTION__);
			slp_gfl_debugMP('msg',__FUNCTION__);

			$labels = array( 
				'name'					=> __( 'GFL Mappings', 'slp-gravity-forms-locations-free' ),
				'singular_name'			=> __( 'GFL Mapping', 'slp-gravity-forms-locations-free' ),
				'add_new'				=> __( 'Add New', 'slp-gravity-forms-locations-free' ),
				'add_new_item'			=> __( 'Add New GFL Mapping', 'slp-gravity-forms-locations-free' ),
				'edit_item'				=> __( 'Edit GFL Mapping', 'slp-gravity-forms-locations-free' ),
				'new_item'				=> __( 'New GFL Mapping', 'slp-gravity-forms-locations-free' ),
				'view_item'				=> __( 'View GFL Mapping', 'slp-gravity-forms-locations-free' ),
				'search_items'			=> __( 'Search GFL Mappings', 'slp-gravity-forms-locations-free' ),
				'not_found'				=> __( 'No gfl mappings found', 'slp-gravity-forms-locations-free' ),
				'not_found_in_trash'	=> __( 'No gfl mappings found in Trash', 'slp-gravity-forms-locations-free' ),
				'parent_item_colon'		=> __( 'Parent GFL Mapping:', 'slp-gravity-forms-locations-free' ),
				'menu_name'				=> __( 'GFL Mappings', 'slp-gravity-forms-locations-free' ),
			);

			$args = array( 
				'labels'				=> $labels,
				'hierarchical'			=> false,
				'description'			=> __('This post_type contains the mappings for GF to SLP.', 'slp-gravity-forms-locations-free'),
				'supports'				=> array( 'title' ),

				'public'				=> true,
				'show_ui'				=> true,
				'show_in_menu'			=> 'admin.php?page=gf_edit_forms',
				'menu_position'			=> 80,

				'show_in_nav_menus'		=> true,
				'publicly_queryable'	=> true,
				'exclude_from_search'	=> false,
				'has_archive'			=> true,
				'query_var'				=> true,
				'can_export'			=> true,
				'rewrite'				=> true,
				'capability_type'		=> 'post'
			);

			register_post_type( POST_TYPE_SLP_GFL_MAPPING, $args );

			// Filters for Custom Post Type slp_gfl_mapping support
			add_filter( 'manage_edit-slp_gfl_mapping_columns',           array( $this, 'slp_gfl_mapping_edit_columns'   )        );
			add_action( 'manage_slp_gfl_mapping_posts_custom_column',    array( $this, 'slp_gfl_mapping_custom_columns' ), 10, 2 );
			add_action( 'add_meta_boxes',                                array( $this, 'slp_gfl_mapping_add_meta_boxes' )        );
			add_action( 'save_post',                                     array( $this, 'slp_gfl_mapping_save_post'      )        );

		}

		public function slp_gfl_mapping_edit_columns( $columns ) {
			$columns = array(
				'cb'						=> '<input type="checkbox" />',
				'title'						=> __( 'Title', 'slp-gravity-forms-locations-free' ),
				'slp_gfl_mapping_form'		=> __( 'Form Title', 'slp-gravity-forms-locations-free' ),
				'slp_gfl_mapping_form_id'	=> __( 'Form ID', 'slp-gravity-forms-locations-free' ),
				'date'						=> __( 'Date', 'slp-gravity-forms-locations-free' ),
			);

			return $columns;
		}

		public function slp_gfl_mapping_custom_columns( $column, $post_id ) {
			global $post;
			$this->debugMP('msg',__FUNCTION__ . ' post_id = ' . $post_id . ', column = ' . $column);

			switch ( $column ) {
				case 'slp_gfl_mapping_form':
				case 'slp_gfl_mapping_form_id':
					$form_id = get_post_meta( $post_id, '_slp_gfl_mapping_form_id', true );
					if ($column == 'slp_gfl_mapping_form') {
						$value = get_slp_gfl_mapping_form_title( $form_id );
					} else {
						$value = $form_id;
					}

					if ( ! empty( $form_id ) ) {
						printf(
							'<a href="%s">%s</a>',
							add_query_arg( array(
								'page' => 'gf_edit_forms',
								'id'   => $form_id,
							), admin_url( 'admin.php' ) ),
							$value
						);
					} else {
						echo '—';
					}

					break;
			}
		}

		/**
		 * Add meta boxes
		 */
		public function slp_gfl_mapping_add_meta_boxes() {
			add_meta_box(
				POST_TYPE_SLP_GFL_MAPPING,
				__( 'Configuration', 'slp-gravity-forms-locations-free' ),
				array( $this, 'meta_box_config' ),
				POST_TYPE_SLP_GFL_MAPPING,
				'normal',
				'high'
			);
		}

		/**
		 * SLP_GFL_Mapping config meta box
		 *
		 * @param WP_Post $post The object for the current post/page.
		 */
		public function meta_box_config( $post ) {

			// Get the LocationsFields to process
			$LocationsFields = $this->get_location_fields();
			$this->debugMP('pr', __FUNCTION__ . ' LocationsFields = ', $LocationsFields);

			include $this->addon->dir . 'include/slp-gfl-meta-box-config.php';
			$this->debugMP('pr', __FUNCTION__ . ' included = ', $this->addon->dir . 'include/slp-gfl-meta-box-config.php');
		}

		/**
		 * When the post is saved, saves our custom data.
		 *
		 * @param int $post_id The ID of the post being saved.
		 */
		public function slp_gfl_mapping_save_post( $post_id ) {
			// Check if our nonce is set.
			if ( ! filter_has_var( INPUT_POST, 'slp_gfl_nonce' ) ) {
				return $post_id;
			}

			$nonce = filter_input( INPUT_POST, 'slp_gfl_nonce', FILTER_SANITIZE_STRING );

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'slp_gfl_mapping_save' ) ) {
				return $post_id;
			}

			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			// Check the user's permissions.
			if ( 'page' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return $post_id;
				}
			} else {
				if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
				}
			}

			/* OK, its safe for us to save the data now. */
			$definition = array(
				'_slp_gfl_mapping_form_id'               => 'sanitize_text_field',
				'_slp_gfl_mapping_condition_enabled'     => FILTER_VALIDATE_BOOLEAN,
				'_slp_gfl_mapping_condition_field_id'    => 'sanitize_text_field',
				'_slp_gfl_mapping_condition_operator'    => 'sanitize_text_field',
				'_slp_gfl_mapping_condition_value'       => 'sanitize_text_field',
				'_slp_gfl_mapping_fields'                => array(
					'filter'    => FILTER_SANITIZE_STRING,
					'flags'     => FILTER_REQUIRE_ARRAY,
				),
			);

			foreach ( $definition as $meta_key => $function ) {
				$meta_value = null;

				if ( 'sanitize_text_field' == $function ) {
					if ( isset( $_POST[ $meta_key ] ) ) {
						$meta_value = sanitize_text_field( $_POST[ $meta_key ] );
					}
				} else {
					$filter  = $function;
					$options = null;

					if ( is_array( $function ) && isset( $function['filter'] ) ) {
						$filter  = $function['filter'];
						$options = $function;
					}

					$meta_value = filter_input( INPUT_POST, $meta_key, $filter, $options );
				}

				if ( isset( $meta_value ) && '' != $meta_value ) {
					update_post_meta( $post_id, $meta_key, $meta_value );
				} else {
					delete_post_meta( $post_id, $meta_key );
				}
			}

			return $post_id;
		}

		//-------------------------------------
		// Methods : Custom
		//-------------------------------------

		/**
		 * Get the fields registered for a location
		 *
		 */
		public function get_location_fields() {

			// The default location fields we want to map for GravityForms
			$location_fields = array(
				'sl_store'      => __('Name'        ,'slp-gravity-forms-locations-free'),
				'sl_address'    => __('Address'     ,'slp-gravity-forms-locations-free'),
				'sl_address2'   => __('Address 2'   ,'slp-gravity-forms-locations-free'),
				'sl_city'       => __('City'        ,'slp-gravity-forms-locations-free'),
				'sl_state'      => __('State'       ,'slp-gravity-forms-locations-free'),
				'sl_zip'        => __('Zip'         ,'slp-gravity-forms-locations-free'),
				'sl_country'    => __('Country'     ,'slp-gravity-forms-locations-free'),
				'sl_tags'       => __('Tags'        ,'slp-gravity-forms-locations-free'),
				'sl_image'      => __('Image'       ,'slp-gravity-forms-locations-free'),
				'sl_description'=> __('Description' ,'slp-gravity-forms-locations-free'),
				'sl_email'      => __('Email'       ,'slp-gravity-forms-locations-free'),
				'sl_url'        => $this->slplus->WPML->get_text( 'label_website' ,
										$this->slplus->WPOption_Manager->get_wp_option( 'label_website', __('Website','slp-gravity-forms-locations-free') ) ,
										'slp-gravity-forms-locations-free'
									 ) ,
				'sl_hours'      => $this->slplus->WPML->get_text( 'label_hours' ,
										$this->slplus->WPOption_Manager->get_wp_option( 'label_hours' , __('Hours','slp-gravity-forms-locations-free') ),
										'slp-gravity-forms-locations-free'
									) ,
				'sl_phone'      => $this->slplus->WPML->get_text( 'label_phone' ,
										$this->slplus->WPOption_Manager->get_wp_option( 'label_phone' , __('Phone','slp-gravity-forms-locations-free') ),
										'slp-gravity-forms-locations-free'
									) ,
				'sl_fax'        => $this->slplus->WPML->get_text( 'label_fax' ,
										$this->slplus->WPOption_Manager->get_wp_option( 'label_fax'  , __('Fax','slp-gravity-forms-locations-free')  ),
										'slp-gravity-forms-locations-free'
									)
				);

			//-------------------------
			// GFI_Pro ACTION: slp_gfl_settings_page
			//    params: settings object, section name
			//-------------------------
			$location_fields = apply_filters('slp_gfl_get_location_fields', $location_fields);

			$this->debugMP('pr', __FUNCTION__ . ' location_fields:', $location_fields);

			return $location_fields;

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
				$hdr = 'Admin: ' . $hdr;
			}
			$this->addon->debugMP($type,$hdr,$msg,NULL,NULL,true);
		}

	}
}
// Dad. Explorer. Rum Lover. Code Geek. Not necessarily in that order.