<?php

namespace OpenClub\Fields;

use OpenClub\Data_Set_Input;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/fields/class-base.php' );
require_once( OPENCLUB_CSV_PLUGIN_DIR . '/inc/fields/interface-field.php' );

class StringField extends Base_Field implements Field {

	public function __construct( $data, Data_Set_Input $input ) {
		parent::__construct( $data, $input );

	}

	public function validate( $value ) {

		parent::_validate( $value );

	}

}