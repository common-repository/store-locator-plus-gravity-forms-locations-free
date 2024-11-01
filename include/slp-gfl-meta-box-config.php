<?php

$post_id = get_the_ID();

$form_id = get_post_meta( $post_id, '_slp_gfl_mapping_form_id', true );
slp_gfl_debugMP('msg', __FUNCTION__ . ' _slp_gfl_mapping_form_id = ', $form_id);

$form_meta = GFAPI::get_form( $form_id );
slp_gfl_debugMP('pr', __FUNCTION__ . ' form_meta = ', $form_meta);

$feed = new stdClass();
$feed->conditionFieldId       = get_post_meta( $post_id, '_slp_gfl_mapping_condition_field_id', true );
$feed->conditionOperator      = get_post_meta( $post_id, '_slp_gfl_mapping_condition_operator', true );
$feed->conditionValue         = get_post_meta( $post_id, '_slp_gfl_mapping_condition_value', true );
$feed->fields                 = get_post_meta( $post_id, '_slp_gfl_mapping_fields', true );
slp_gfl_debugMP('pr', __FUNCTION__ . ' feed->fields = ', $feed->fields);

?>

<div id="slp-gfl-feed-editor">
	<?php wp_nonce_field( 'slp_gfl_mapping_save', 'slp_gfl_nonce' ); ?>

	<input id="slp_gfl_gravity_form" name="slp_gfl_gravity_form" value="<?php echo esc_attr( json_encode( $form_meta ) ); ?>" type="hidden" />
	<input id="slp_gfl_feed" name="slp_gfl_feed" value="<?php echo esc_attr( json_encode( $feed ) ); ?>" type="hidden" />

	<table class="form-table">
		<tr>
			<th scope="row">
				<label for="_slp_gfl_mapping_form_id">
					<?php _e( 'Gravity Form', 'slp-gravity-forms-locations-free' ); ?>
				</label>
			</th>
			<td>
				<select id="_slp_gfl_mapping_form_id" name="_slp_gfl_mapping_form_id">
					<option value=""><?php _e( '&mdash; Select a form &mdash;', 'slp-gravity-forms-locations-free' ); ?></option>

					<?php foreach ( GFAPI::get_forms() as $form ) : ?>

						<option value="<?php echo $form['id']; ?>" <?php selected( $form_id, $form['id'] ); ?>>
							<?php echo esc_html( $form['title'] ); ?>
						</option>

					<?php endforeach; ?>
				</select>
			</td>
		</tr>
		<tr>
			<th scope="row">
				<label for="slp_gfl_condition_enabled">
					<?php _e( 'Condition', 'slp-gravity-forms-locations-free' ); ?>
				</label>
			</th>
			<td>
				<?php

				$condition_enabled  = get_post_meta( $post_id, '_slp_gfl_mapping_condition_enabled', true );
				$condition_field_id = get_post_meta( $post_id, '_slp_gfl_mapping_condition_field_id', true );
				$condition_operator = get_post_meta( $post_id, '_slp_gfl_mapping_condition_operator', true );
				$condition_value    = get_post_meta( $post_id, '_slp_gfl_mapping_condition_value', true );

				?>
				<div>
					<input id="slp_gfl_condition_enabled" name="_slp_gfl_mapping_condition_enabled" value="true" type="checkbox" <?php checked( $condition_enabled ); ?> />

					<label for="slp_gfl_condition_enabled">
						<?php _e( 'Enable', 'slp-gravity-forms-locations-free' ); ?>
					</label>
				</div>

				<div id="slp_gfl_condition_config">
					<?php

					// Select field
					$select_field = '<select id="slp_gfl_condition_field_id" name="_slp_gfl_mapping_condition_field_id"></select>';

					// Select operator
					$select_operator = '<select id="slp_gfl_condition_operator" name="_slp_gfl_mapping_condition_operator">';

					$operators = array(
						'' => '',
						SLP_GFL_OPERATOR_IS     => __( 'is', 'slp-gravity-forms-locations-free' ),
						SLP_GFL_OPERATOR_IS_NOT => __( 'is not', 'slp-gravity-forms-locations-free' ),
					);

					foreach ( $operators as $value => $label ) {
						$select_operator .= sprintf(
							'<option value="%s" %s>%s</option>',
							esc_attr( $value ),
							selected( $condition_operator, $value, false ),
							esc_html( $label )
						);
					}

					$select_operator .= '</select>';

					// Select value
					$select_value = '<select id="slp_gfl_condition_value" name="_slp_gfl_mapping_condition_value"></select>';

					// Print
					printf(
						__( 'Create a location if %s %s %s', 'slp-gravity-forms-locations-free' ),
						$select_field,
						$select_operator,
						$select_value
					);

					?>
				</div>

				<div id="slp_gfl_condition_message">
					<span class="description"><?php _e( 'To create a condition, your form must have a drop down, checkbox or multiple choice field.', 'slp-gravity-forms-locations-free' ); ?></span>
				</div>
			</td>
		</tr>
	</table>

	<h4>
		<?php _e( 'Fields', 'slp-gravity-forms-locations-free' ); ?>
	</h4>

	<?php

	$fields = array(
		'first_name' => __( 'First Name', 'slp-gravity-forms-locations-free' ),
		'last_name'  => __( 'Last Name', 'slp-gravity-forms-locations-free' ),
		'email'      => __( 'Email', 'slp-gravity-forms-locations-free' ),
		'address1'   => __( 'Address', 'slp-gravity-forms-locations-free' ),
		'address2'   => __( 'Address 2', 'slp-gravity-forms-locations-free' ),
		'city'       => __( 'City', 'slp-gravity-forms-locations-free' ),
		'state'      => __( 'State', 'slp-gravity-forms-locations-free' ),
		'zip'        => __( 'Zip', 'slp-gravity-forms-locations-free' ),
		'country'    => __( 'Country', 'slp-gravity-forms-locations-free' ),
	);
	$fields = $LocationsFields;

	?>

	<table class="form-table">

		<?php foreach ( $fields as $name => $label ) : ?>

			<tr>
				<th scope="row">
					<?php echo $label; ?>
				</th>
				<td>
					<?php

					printf(
						'<select id="%s" name="%s" data-slp-gfl-field-name="%s" class="field-select"><select>',
						esc_attr( 'slp_gfl_fields_' . $name ),
						esc_attr( '_slp_gfl_mapping_fields[' . $name . ']' ),
						esc_attr( $name )
					);

					?>
				</td>
			</tr>

		<?php endforeach; ?>

	</table>

</div>
