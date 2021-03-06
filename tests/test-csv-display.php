<?php

namespace OpenClubCSV\Test;

require_once( 'class-base.php' );

class CSVDisplayTest extends Base {

	public static function wpSetUpBeforeClass( \WP_UnitTest_Factory $factory ) {
	}


	function test_get_formatted_csv_row_returns_correct_string() {

		$data = array(
			'data' => array(
				array(
					'value'           => 'a',
					'formatted_value' => 'A',
					'display_default' => 1
				),
				array(
					'value'           => '1',
					'formatted_value' => '1.0',
					'display_default' => 1
				),
			)
		);

		$this->assertEquals( \OpenClub\CSV_Display::get_csv_row( $data ), 'A,1.0' );
	}

	function test_get_non_formatted_csv_row_returns_correct_string() {

		$data = array(
			'data' => array(
				array(
					'value'           => 'a',
					'formatted_value' => 'A',
					'display_default' => 1
				),
				array(
					'value'           => '1',
					'formatted_value' => '1.0',
					'display_default' => 1
				),
			)
		);

		$this->assertEquals( \OpenClub\CSV_Display::get_csv_row( $data, false ), 'a,1' );
	}

	function test_csv_row_with_empty_data_array_returns_empty_string() {

		$data = array( 'data' => array() );
		$this->assertEquals( \OpenClub\CSV_Display::get_csv_row( $data, false ), 'empty row' );
	}

	function test_get_csv_row_with_no_formatted_value_throws_exception() {

		$this->setExpectedException( 'Exception', 'The key formatted_value does not exist.' );

		$data = array(
			'data' => array(
				array(
					'value'           => 'a',
					'display_default' => 1
				),
				array(
					'value'           => '1',
					'display_default' => 1
				),
			)
		);

		\OpenClub\CSV_Display::get_csv_row( $data );
	}

	function test_get_csv_row_with_no_value_throws_exception() {

		$this->setExpectedException( 'Exception', 'The key value does not exist.' );

		$data = array(
			'data' => array(
				array(
					'formatted_value' => 'a',
					'display_default' => 1
				),
				array(
					'formatted_value' => '1',
					'display_default' => 1
				),
			)
		);

		\OpenClub\CSV_Display::get_csv_row( $data );
	}


	function test_post_content_outputs_correct_data_to_csv_rows_template_file() {

		$test_data_samples = array(
			'invalid_string_option',
			'validation_failure',
			'valid_content',
			'invalid_heading_column',
			'invalid_int',
			'valid_date_conversion',
			'invalid_date',
			'invalid_output_date_format',
			'invalid_input_date_format', 
			'test_data_future_events_displays_future_events_only',
		);


		foreach ( $test_data_samples as $key ) {

			$test_data = new Sailing_Programme_Data( $key );

			$post = $this->get_test_post_object( $test_data );

			$config            = $test_data->get( 'config' );
			$config['post_id'] = $post->ID;

			$s = \OpenClub\CSV_Display::get_html(
				\OpenClub\CSV_Display::get_config( $config )
			);


			//echo "\n".$s ."\n";
			//echo "\n" . $test_data->get( 'html_output' ) . "\n";

			$this->assertSame( $test_data->get( 'html_output' ), trim( $s ) );
		}

	}

	function test_post_content_with_column_mismatch_outputs_correct_data_to_csv_rows_template_file() {

		$test_data = new Sailing_Programme_Data( 'csv_row_column_mismatch' );
		$post      = $this->get_test_post_object( $test_data );

		$config            = $test_data->get( 'config' );
		$config['post_id'] = $post->ID;

		$s = \OpenClub\CSV_Display::get_html(
			\OpenClub\CSV_Display::get_config( $config )
		);

		$this->assertSame( sprintf(
			$test_data->get( 'html_output' ), $post->ID ),
			trim( $s ) );

	}

	function test_post_content_with_active_filter_removes_row() {

		$test_data = new Sailing_Programme_Data( 'active_filter_will_filter_content' );
		$post      = $this->get_test_post_object( $test_data );

		$config            = $test_data->get( 'config' );
		$config['post_id'] = $post->ID;

		$s = \OpenClub\CSV_Display::get_html(
			\OpenClub\CSV_Display::get_config( $config )
		);

		$this->assertSame( sprintf(
			$test_data->get( 'html_output' ), $post->ID ),
			trim( $s ) );

	}

	function test_grouped_rows_displays_correctly() {

		$test_data = new Sailing_Programme_Data( 'test_data_grouped_rows' );
		$post      = $this->get_test_post_object( $test_data );

		$config            = $test_data->get( 'config' );
		$config['post_id'] = $post->ID;

		$s = \OpenClub\CSV_Display::get_html(
			\OpenClub\CSV_Display::get_config( $config )
		);

		$this->assertSame( sprintf(
			$test_data->get( 'html_output' ), $post->ID ),
			trim( $s ) );

	}


}