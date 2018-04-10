<?php

namespace OpenClub\Fields;

use OpenClub\Data_Set_Input;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

require_once( 'class-base.php' );
require_once( 'interface-field.php' );
require_once( 'exception-field.php' );

abstract class Base_Field implements Field {

	/**
	 * @var $data array
	 */
	protected $data;

	/**
	 * @var $options array
	 */
	protected $options = array();

	/**
	 * @var $error_message string|bool
	 */
	private $error_message = false;

	/**
	 * @var $display_field bool
	 */
	private $display_field = true;

	/**
	 * @var bool
	 */
	private $required = false;

	/**
	 * @var $input Data_Set_Input
	 */
	private $input;

	private $field_name = null;
	
	public function __construct( $data, Data_Set_Input $input ) {

		$this->field_name = $data[ 'field_name' ];
		$this->type = $data['type'];

		$this->data = $data;
		$this->input = $input;

		if ( isset( $this->data['options'] ) && ! array( $this->data['options'] ) ) {
			throw new Field_Exception( 'options is not an array' );
		}


		if ( isset( $this->data['options'] ) ) {
			$this->options = explode( ',', $this->data['options'] );
		}

		if ( ! empty( $this->data['required'] ) ) {
			$this->required = true;
		}

		$this->set_field_display_flag();
	}

	private function set_field_display_flag(){

		$overridden_display_fields = $this->input->get_overridden_display_fields();

		$fields_overridden = false;

		if( $this->input->has_reset_display_fields() ){
			if(!empty( $overridden_display_fields )){
				throw new \Exception('You can\'t override fields and reset fields, choose one setting');
			}
			return;
		}

		if( !empty( $overridden_display_fields ) ) {

			if( !in_array( $this->data[ 'field_name' ], $overridden_display_fields ) ) {
				$this->display_field = false;
			}
		}

		if( isset( $this->data[ 'display' ] ) && !$this->data[ 'display' ] ){
			$this->display_field = false;
		}
	}

	protected function _validate( $value ) {

		if ( $this->is_required() && empty( $value ) ) {
			throw new Field_Exception( 'Data error in field ' . $this->data['field_name'] . ' requires value' );
		}

		$this->string_has_valid_length( $value );
		$this->hasValidOption( $value );
	}

	private function hasValidOption( $value ) {

		if ( $this->hasOptions() && ! empty( $value ) ) {
			if ( ! $this->isValidOption( $value ) ) {
				throw new Field_Exception( 'Data error in field ' . $this->data['field_name'] . ' if present, expected one of "' . $this->getOptions( true ) . '" got "' . $value . '"' );
			}
		}
	}

	public function getType() {
		return $this->type;
	}

	/**
	 * @param $value
	 *
	 * @return bool
	 */
	abstract function validate( $value );

	protected function isValidOption( $value ) {

		if ( empty( $this->options ) ) {
			throw new Field_Exception( 'Data error in field ' . $this->data['field_name'] . 'is missing options.' );
		}

		return in_array( $value, $this->options );

	}

	/**
	 * For the case where a field as a number of options e.g.
	 *
	 * [options] => A,B,C,D,E,F,G,H,1,2,3,4,5,6,7,8,9
	 *
	 * @return bool
	 */
	public function hasOptions() {
		return count( $this->options ) > 0 ? true : false;
	}

	/**
	 * Return defined options as a string or array
	 *
	 * @param bool $string
	 *
	 * @return array|string
	 */
	public function getOptions( $string = false ) {

		if ( empty( $this->options ) ) {
			throw new Field_Exception( 'There are no options for defined for this field type' );
		}

		if ( $string ) {
			return implode( ',', $this->options );
		} else {
			return $this->options;
		}
	}

	/**
	 * @return string\null
	 */
	public function getMessage() {
		return $this->errorMessage;
	}

	


	public function is_required() {
		return $this->required;
	}
	
	
	public function is_displayed(){
		return $this->display_field;
	}

	protected function string_has_valid_length( $value ) {
		if ( isset( $this->data['max-length'] ) && ! empty( $value ) && strlen( $value ) > $this->data['max-length'] ) {
			throw new Field_Exception( 'Data error in field ' . $this->data['field_name'] . ' value too long, a max length of  ' . $this->data['max-length'] . ' is expected. ' . strlen( $value ) . ' given. Value: "' . $value . '"' );
		}
	}

	public function format_value( $value ) {
		return $value;
	}

}