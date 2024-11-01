( function( $ ) {
	/**
	 * Gravity Forms SLP feed editor
	 */
	var GravityFormsSLPFeedEditor = function( element, options ) {
		var obj = this;
		var $element = $( element );

		// Elements
		var elements = {};
		elements.feed = $element.find( '#slp_gfl_feed' );
		elements.gravityForm = $element.find( '#slp_gfl_gravity_form' );
		elements.formId = $element.find( '#_slp_gfl_mapping_form_id' );
		elements.conditionEnabled = $element.find( '#slp_gfl_condition_enabled' );
		elements.conditionConfig = $element.find( '#slp_gfl_condition_config' );
		elements.conditionFieldId = $element.find( '#slp_gfl_condition_field_id' );
		elements.conditionOperator = $element.find( '#slp_gfl_condition_operator' );
		elements.conditionValue = $element.find( '#slp_gfl_condition_value' );
		elements.fieldSelectFields = $element.find( 'select.field-select' );

		// Data
		var feed = $.parseJSON( elements.feed.val() );
		var gravityForm = $.parseJSON( elements.gravityForm.val() );

		/**
		 * Toggle condition config
		 */
		this.toggleConditionConfig = function() {
			if ( elements.conditionEnabled.prop( 'checked' ) ) {
				elements.conditionConfig.fadeIn( 'fast' );
			} else {
				elements.conditionConfig.fadeOut( 'fast' );
			}
		};
		
		/**
		 * Update condition fields
		 */
		this.updateConditionFields = function() {
			elements.conditionFieldId.empty();
			$( '<option>' ).appendTo( elements.conditionFieldId );

			if ( gravityForm ) {
				$.each( gravityForm.fields, function( key, field ) {
					var type = field.inputType ? field.inputType : field.type;
	
					var index = $.inArray( type, [ 'checkbox', 'radio', 'select' ] );
					if ( index >= 0 ) {
						var label = field.adminLabel ? field.adminLabel : field.label;

						$( '<option>' )
							.attr( 'value', field.id )
							.text (label )
							.prop( 'selected', feed.conditionFieldId == field.id )
							.appendTo( elements.conditionFieldId );
					}
				});
				
				elements.conditionOperator.val( feed.conditionOperator );
			}
		};
		
		/**
		 * Update condition values
		 */
		this.updateConditionValues = function() {
			var id	= elements.conditionFieldId.val();
			var field = obj.getFieldById( id );
			
			elements.conditionValue.empty();
			$( '<option>' ).appendTo( elements.conditionValue );

			if ( field && field.choices ) {
				$.each( field.choices, function( key, choice ) {
					var value = choice.value ? choice.value : choice.text;

					$( '<option>' )
						.attr( 'value', value )
						.text( choice.text )
						.appendTo( elements.conditionValue );
				} );
				
				elements.conditionValue.val( feed.conditionValue );
			}
		};

		/**
		 * Get field by the specified id
		 */
		this.getFieldById = function( id ) {
			if ( gravityForm ) {
				for ( var i = 0; i < gravityForm.fields.length; i++ ) {
					if ( gravityForm.fields[i].id == id ) {
						return gravityForm.fields[i];
					}
				}
			}
			
			return null;
		};

		/**
		 * Get fields by types
		 * 
		 * @param types
		 * @return Array
		 */
		this.getFieldsByType = function( types ) {
			var fields = [];

			if ( gravityForm ) {				
				for ( var i = 0; i < gravityForm.fields.length; i++ ) {
					if ( $.inArray( gravityForm.fields[i].type, types ) >= 0 ) {
						fields.push(gravityForm.fields[i]);
					}
				}
			}

			return fields;
		};
		
		this.getInputs = function() {
			var inputs = [];
			
			if ( gravityForm ) {
				$.each( gravityForm.fields, function( key, field ) {
					if ( field.inputs ) {
						$.each( field.inputs, function( key, input ) {
							inputs.push( input );
						} );
					} else if ( ! field.displayOnly ) {
						inputs.push ( field );
					}
				} );
			}
			
			return inputs;
		};
		
		/**
		 * Change form
		 */
		this.changeForm = function() {
			jQuery.get(
				ajaxurl, {
					action: 'slp_get_form_data', 
					formId: elements.formId.val()
				},
				function( response ) {
					if ( response.success ) {
						gravityForm = response.data;

						obj.updateFields();
					}
				}
			);
		};
		
		/**
		 * Update select fields
		 */
		this.updateSelectFields = function() {
			if ( gravityForm ) {
				elements.fieldSelectFields.empty();

				elements.fieldSelectFields.each( function( i, element ) {
					$element = $( element );
					
					var name = $element.data( 'slp-gfl-field-name' );

					$( '<option>' ).text('--').appendTo( $element );

					$.each( obj.getInputs(), function( key, input ) {
						var label = input.adminLabel ? input.adminLabel : input.label;

						$( '<option>' )
							.attr( 'value', input.id )
							.text( label )
							.prop( 'selected', feed.fields[name] == input.id )
							.appendTo( $element );
					} );
				} );
			}
		};
		
		/**
		 * Update fields
		 */
		this.updateFields = function() {
			obj.toggleConditionConfig();
			obj.updateConditionFields();
			obj.updateConditionValues();
			obj.updateSelectFields();
		};

		// Function calls
		obj.updateFields();

		elements.formId.change( obj.changeForm );
		elements.conditionEnabled.change( obj.toggleConditionConfig );
		elements.conditionFieldId.change( obj.updateConditionValues );
	};
	
	//////////////////////////////////////////////////

	/**
	 * jQuery plugin - Gravity Forms SLP feed editor
	 */
	$.fn.gravityFormsSlpFeedEditor = function( options ) {
		return this.each( function() {
			$this = $( this );

			if ( $this.data( 'slp-gfl-feed-editor' ) ) return;

			var editor = new GravityFormsSLPFeedEditor( this, options );

			$this.data( 'slp-gfl-feed-editor', editor );
		} );
	};
	
	//////////////////////////////////////////////////

	/**
	 * Ready
	 */
	$( document ).ready( function() {

		$( '.gforms_edit_form .slp_gfl-edit-link' ).click( function( event ) {
			event.stopPropagation();
		} ); 
		
		$( '#slp-gfl-feed-editor' ).gravityFormsSlpFeedEditor();
	} );
} )( jQuery );