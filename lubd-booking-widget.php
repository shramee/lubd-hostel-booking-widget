<?php
/**
 * Plugin Name: Lubd booking widget
 * Plugin URI:
 * Description: Shows booking form for Lubd
 * Version: 1.1.0
 * Author: Shramee
 * Author URI: http://shramee.me/
 * License: GPL2
 */

define( 'LUBD_WIDGET_VERSION', '1.1.0' );

// Register the widget
add_action( 'widgets_init', 'Lubd_Booking_Widget::register_widget' );

/**
 * Class Lubd_Booking_Widget
 */
class Lubd_Booking_Widget extends WP_Widget {
	/** Basic Widget Settings */
	const WIDGET_NAME = "Lubd booking widget";
	const WIDGET_DESCRIPTION = "Shows booking form for Lubd";

	public $textdomain;
	public $fields;

	/**
	 * Registers widget
	 * @action widgets_init
	 */
	public static function register_widget() {
		register_widget( "Lubd_Booking_Widget" );
	}

	/**
	 * Construct the widget
	 */
	function __construct() {
		//We're going to use $this->textdomain as both the translation domain and the widget class name and ID
		$this->textdomain = strtolower( get_class( $this ) );

		//Figure out your textdomain for translations via this handy debug print
		//var_dump($this->textdomain);

		//Add fields
		$this->add_field( 'preset_location', 'Preset location', '', 'select' );

		//Translations
		load_plugin_textdomain( $this->textdomain, false, basename( dirname( __FILE__ ) ) . '/languages' );

		//Init the widget
		parent::__construct( $this->textdomain, __( self::WIDGET_NAME, $this->textdomain ), array(
			'description' => __( self::WIDGET_DESCRIPTION, $this->textdomain ),
			'classname'   => $this->textdomain
		) );
	}

	/**
	 * Adds a text field to the widget
	 *
	 * @param $field_name
	 * @param string $field_description
	 * @param string $field_default_value
	 * @param string $field_type
	 */
	private function add_field( $field_name, $field_description = '', $field_default_value = '', $field_type = 'text' ) {
		if ( ! is_array( $this->fields ) ) {
			$this->fields = array();
		}

		$this->fields[ $field_name ] = array(
			'name'          => $field_name,
			'description'   => $field_description,
			'default_value' => $field_default_value,
			'type'          => $field_type
		);
	}

	/**
	 * Widget frontend
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		/* Before and after widget arguments are usually modified by themes */
		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			$title = apply_filters( 'widget_title', $instance['title'] );
			echo $args['before_title'] . $title . $args['after_title'];
		}

		/* Widget output here */
		include 'tpl-widget.php';

		/* After widget */
		echo $args['after_widget'];
	}

	/**
	 * Widget backend
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		/* Generate admin for fields */
		foreach ( $this->fields as $field_name => $field_data ) {
			$val = esc_attr( isset( $instance[ $field_name ] ) ? $instance[ $field_name ] : '' ); ?>
			<p>
				<label><?php echo $field_data['description']; ?></label>
				<select class="widefat" name="<?php echo $this->get_field_name( $field_name ); ?>">
					<option <?php selected( $val, '' ) ?> value="">Show selection</option>
					<option <?php selected( $val, '419' ) ?> value="419">Bangkok Silom</option>
					<option <?php selected( $val, '420' ) ?> value="420">Bangok Siam</option>
					<option <?php selected( $val, '421' ) ?> value="421">Phuket Patong</option>
					<option <?php selected( $val, '504' ) ?> value="504">Cambodia Siem Reap</option>
					<option <?php selected( $val, 'https://hotels.cloudbeds.com/reservation/ZH7GQ6#' ) ?>
						value="https://hotels.cloudbeds.com/reservation/ZH7GQ6#">Philippines Makati
					</option>
				</select>
			</p>
			<?php
		}
	}

	/**
	 * Updating widget by replacing the old instance with new
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
}

add_action( 'wp_enqueue_scripts', function () {
	$url = plugin_dir_url( __FILE__ );
	wp_register_script( 'lubd-booking', "$url/js/booking.js", [ 'jquery-ui-datepicker' ], LUBD_WIDGET_VERSION );
	wp_register_style( 'lubd-booking', "$url/css/booking.css", [], LUBD_WIDGET_VERSION );
} );

add_shortcode( 'lubd_booking', function( $instance ) {
	ob_start();
	include 'tpl-widget.php';
	return ob_get_clean();
} );