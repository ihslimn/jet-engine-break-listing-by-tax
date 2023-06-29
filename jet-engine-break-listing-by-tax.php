<?php
/**
 * Plugin Name: JetEngine - break listing by terms
 * Plugin URI:  
 * Description: Separate JetEngine listing by terms
 * Version:     1.0.0
 * Author:      Crocoblock
 * Author URI:  https://crocoblock.com/
 * License:     GPL-3.0+
 * License URI: http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path: /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die();
}

class Jet_Engine_Break_Listing_By_Terms {

	private $tax = '';

	public static $is_even = false;
	public static $last    = false;

	public function __construct() {

		add_action( 'init', array( $this, 'setup' ) );
		add_action( 'jet-engine/listing/before-grid-item', array( $this, 'handle_item' ), 10, 2 );
		add_action( 'jet-engine/listing/after-grid-item', array( $this, 'maybe_print_separator' ), 10, 2 );
		add_filter( 'get_post_metadata', array( $this, 'handle_injection_meta' ), 0, 3 );

	}

	public function handle_injection_meta( $value, $post_id, $meta_key ) {
		
		if ( $meta_key !== 'break_by_tax_is_even' ) {
			return $value;
		}

		return array( ( int ) Jet_Engine_Break_Listing_By_Terms::$is_even );    
		
	}

	/**
	 * These constants could be defined from functions.php file of your active theme
	 * @return [type] [description]
	 */
	public function setup() {

		if ( ! defined( 'JET_ENGINE_BREAK_BY_TAX' ) ) {
			// set taxonomy to break by
			define( 'JET_ENGINE_BREAK_BY_TAX', '' );
		}

		if ( ! defined( 'JET_ENGINE_BREAK_TAX_OPEN_HTML' ) ) {
			// set opening html tag(s) for term name
			define( 'JET_ENGINE_BREAK_TAX_OPEN_HTML', '<h4 class="jet-engine-break-listing" style="width:100%; flex: 0 0 100%;">' );
		}

		if ( ! defined( 'JET_ENGINE_BREAK_TAX_CLOSE_HTML' ) ) {
			// set closing html tag(s) for term name
			define( 'JET_ENGINE_BREAK_TAX_CLOSE_HTML', '</h4>' );
		}

	}

	public function maybe_print_separator( $post, $listing ) {

		if ( empty( $listing->query_vars['request']['query_id'] ) ) {
			return;
		}

		$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $listing->query_vars['request']['query_id'] );

		if ( ! $query ) {
			return;
		}

		if ( false === strpos( $query->name, '--break-by-tax' ) ) {
			return;
		}

		if ( self::$last ) {
			echo sprintf( '<div class="listing-item-spacing-%s"></div>', self::$is_even ? 'odd' : 'even' );
		}

	}

	public function handle_item( $post, $listing ) {

		if ( empty( $listing->query_vars['request']['query_id'] ) ) {
			return;
		}

		$query = \Jet_Engine\Query_Builder\Manager::instance()->get_query_by_id( $listing->query_vars['request']['query_id'] );

		if ( ! $query ) {
			return;
		}

		if ( false === strpos( $query->name, '--break-by-tax' ) ) {
			return;
		}

		$matches = array();

		if ( preg_match( '/--break-by-tax-([^\s]+)/', $query->name, $matches ) ) {
			$this->tax = $matches[1];
		} else {
			$this->tax = JET_ENGINE_BREAK_BY_TAX;
		}

		$index = jet_engine()->listings->data->get_index();

		$items = $query->get_items();

		$prev_post = $items[ $index - 1 ] ?? null;
		$next_post = $items[ $index + 1 ] ?? null;

		$prev_term    = $this->get_post_term( $prev_post );
		$next_term    = $this->get_post_term( $next_post );
		$current_term = $this->get_post_term( $post );

		if ( $current_term && $prev_term !== $current_term ) {
			$this->render_term( $post );
		}

		if ( $next_term !== $current_term ) {
			self::$is_even = ! self::$is_even;
			self::$last    = true;
		} else {
			self::$last = false;
		}

		if ( ! $next_post ) {
			self::$last = true;
		}

	}

	public function get_post_id( $post ) {
		return jet_engine()->listings->data->get_current_object_id( $post );
	}

	public function get_post_term( $post ) {

		if ( ! $post ) {
			return;
		}

		$terms = get_the_terms( $this->get_post_id( $post ), $this->tax );

		if ( is_wp_error( $terms ) || empty( $terms ) ) {
			return;
		}

		return $terms[0]->name;

	}

	public function render_term( $post ) {

		$term_name = $this->get_post_term( $post );

		if ( ! $term_name ) {
			return;
		}

		echo JET_ENGINE_BREAK_TAX_OPEN_HTML;
		echo $term_name;
		echo JET_ENGINE_BREAK_TAX_CLOSE_HTML;

	}

}

new Jet_Engine_Break_Listing_By_Terms();
