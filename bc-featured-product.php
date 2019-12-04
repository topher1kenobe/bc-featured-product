<?php
/**
 * Plugin Name: Featured BigCommerce Product
 * Plugin URI: http://bigcommerce.com
 * Description: Provides a mechanism for associating a BigCommerce product with a Page or Post
 * Author: Topher
 * Version: 1.0
 * Author URI: http://topher1kenobe.com
 * Text Domain: wp-featured-bc-product
 */

/**
 * Provides a mechanism for associating a BigCommerce Product with a Page or Post
 *
 * @package BC_Featured_Product
 * @since BC_Featured_Product 1.0
 * @author Topher
 */

/**
 * Main BC Featured Products Class
 *
 * Contains the main functions for the admin side of BC Featured Product
 *
 * @class BC_Featured_Product
 * @version 1.0.0
 * @since 1.0
 * @package BC_Featured_Product
 * @author Topher
 */
class BC_Featured_Product {

	/**
	* Instance handle
	*
	* @static
	* @since 1.2
	* @var string
	*/
	private static $__instance = null;

	/**
	 * BC_Featured_Product Constructor, actually contains nothing
	 *
	 * @access public
	 * @return void
	 */
	private function __construct() {}

	/**
	 * Instance initiator, runs setup etc.
	 *
	 * @access public
	 * @return self
	 */
	public static function instance() {
		if ( ! is_a( self::$__instance, __CLASS__ ) ) {
			self::$__instance = new self;
			self::$__instance->setup();
		}
		
		return self::$__instance;
	}

	/**
	 * Runs things that would normally be in __construct
	 *
	 * @access private
	 * @return void
	 */
	private function setup() {

		// only do this in the admin area
		if ( is_admin() ) {
			add_action( 'save_post', array( $this, 'save' ) );
			add_action( 'add_meta_boxes', array( $this, 'bc_products_meta_box' ) );
		}

	}

	/**
	 * Make meta box holding select menu of Products
	 *
	 * @access public
	 * @return void
	 */
	public function bc_products_meta_box( $post_type ) {

		// limit meta box to certain post types
		$post_types = array( 'post', 'page' );

		if ( in_array( $post_type, $post_types ) ) {
			add_meta_box(
				'wp-featured-product',
				esc_html__( 'Featured BC Product', 'wp-featured-products' ),
				array( $this, 'render_bc_products_meta_box_contents' ),
				$post_type,
				'side',
				'default'
			);
		}
	}

	/**
	 * Render select box of BC Products
	 *
	 * @access public
	 * @return void
	 */
	public function render_bc_products_meta_box_contents() {

		global $post;

		// Add a nonce field so we can check for it later.
		wp_nonce_field( 'wp-featured-products', 'wp_featured_products_nonce' );

		// go get the meta field
		$bcp_meta_value = get_post_meta( $post->ID, '_bc_featured_product', true );

		// Display the form, using the current value.
		echo '<p>';
		esc_html_e( 'Please choose from the existing products below.  If you need to create a new Product, please go to ', 'wp-featured-products' );
		echo '<a href="' . esc_url( 'https://login.bigcommerce.com/' ) . '">';
		esc_html_e( 'The BigCommerce Admin ', 'wp-featured-products' );
		echo '</a>.';
		echo '</p>';

		$args = array (
			'post_type'      => 'bigcommerce_product',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
		);

		// The Query
		$products = get_posts( $args );

		// make sure we have results
		if ( count( $products ) > 0 ) {
			echo '<select name="_bc_featured_product">' . "\n";
			echo '<option value="">' . __( 'Please choose', 'wp-featured-products' ) . '</option>' . "\n";
			foreach ( $products as $key => $product ) {
				echo '<option value="' . absint( $product->ID ) . '"' . selected( $bcp_meta_value, $product->ID, false ) . '>' . esc_html( $product->post_title ) . '</option>' . "\n";
			}
			echo '</select>' . "\n";
		} else {
			echo '<p>';
			esc_html_e( 'No products found, ', 'wp-featured-products' );
		}

	}

	/**
	 * Updates the options table with the form data
	 *
	 * @access public
	 * @param int $post_id
	 * @return void
	 */
	public function save( $post_id ) {

		// Check if the current user is authorised to do this action. 
		$post_type = get_post_type_object( get_post( $post_id )->post_type );
		if ( ! current_user_can( $post_type->cap->edit_post, $post_id ) ) {
			return;
		}

		// Check if the user intended to change this value.
		if ( ! isset( $_POST['wp_featured_products_nonce'] ) || ! wp_verify_nonce( $_POST['wp_featured_products_nonce'], 'wp-featured-products' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $post_id;
		}

        // check to see if our input is an integer, so our absint doesn't save a 0
        if ( is_int( $_POST['_bc_featured_product'] ) ) {
		    // Update or create the key/value
		    update_post_meta( $post_id, '_bc_featured_product', absint( $_POST['_bc_featured_product'] ) );
        }

	}

	// end class
}

/**
 * Instantiate the BC_Featured_Product instance
 * @since BC_Featured_Product 1.0
 */
add_action( 'plugins_loaded', array( 'BC_Featured_Product', 'instance' ) );

?>
