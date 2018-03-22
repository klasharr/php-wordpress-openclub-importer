<?php

namespace OpenClub;

Use \WP_CLI;
Use \WP_Post;
Use \Exception;


class Field_Validator_Manager {

	/**
	 * @var $post WP_Post
	 */
	private $post;

	/**
	 * @var array
	 */
	private $fields = array();

	/**
	 * @param \WP_Post $post
	 */
	public function __construct( WP_Post $post ) {

		$this->post = $post;

		if ( empty( $this->post->field_settings ) ) {
			return;
		}

		foreach ( $this->post->field_settings as $field => $rules ) {

			if ( empty( $rules['type'] ) ) {
				throw new Exception( 'Field has no type' );
			}

			$rules['field_name'] = $field;

			$className              = ucwords( $rules['type'] ) . 'Field';
			$this->fields[ $field ] = Factory::get_field( $className, $rules );
		}


	}

	public function has_validators() {
		return ! empty( $this->fields ) ? true : false;
	}

	public function get_validator( $key ) {

		return isset( $this->fields[ $key ] ) ? $this->fields[ $key ] : false;

	}


}