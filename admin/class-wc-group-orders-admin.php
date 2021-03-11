<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://trevan.co
 * @since      1.0.0
 *
 * @package    Wc_Group_Orders
 * @subpackage Wc_Group_Orders/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Wc_Group_Orders
 * @subpackage Wc_Group_Orders/admin
 * @author     Trevan Hetzel <trevan@hetzelcreative.com>
 */
class Wc_Group_Orders_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Group_Orders_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Group_Orders_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wc-group-orders-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Wc_Group_Orders_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Wc_Group_Orders_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wc-group-orders-admin.js', array( 'jquery' ), $this->version, false );

	}

	/**
	 * Creates a group order taxonomy for product post types
	 *
	 * @since 	1.0.0
	 * @access 	public
	 * @uses 	register_taxonomy()
	 */
	public function create_taxonomy() {
		$labels = array(
			'name'                       => 'Group Orders',
			'singular_name'              => 'Group Order',
			'menu_name'                  => 'Group Orders',
			'all_items'                  => 'All Group Orders',
			'parent_item'                => 'Parent Group Order',
			'parent_item_colon'          => 'Parent Group Orders:',
			'new_item_name'              => 'New Group Orders Name',
			'add_new_item'               => 'Add New Group Order',
			'edit_item'                  => 'Edit Group Order',
			'update_item'                => 'Update Group Order',
			'separate_items_with_commas' => 'Separate Group Order with commas',
			'search_items'               => 'Search Group Orders',
			'add_or_remove_items'        => 'Add or remove Group Orders',
			'choose_from_most_used'      => 'Choose from the most used Group Orders',
		);

		$args = array(
			'labels'                     => $labels,
			'hierarchical'               => true,
			'public'                     => true,
			'show_ui'                    => true,
			'show_admin_column'          => true,
			'show_in_nav_menus'          => true,
			'show_tagcloud'              => false,
			'show_in_quick_edit'         => true,
			'meta_box_cb'                => false,
			'rewrite'                    => true
		);

		register_taxonomy( 'group_order', 'product', $args );
		register_taxonomy_for_object_type( 'group_order', 'product' );
	}

	/**
	 * Adds a group order filter to product page
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function filter_by_group_order( $output ) {

		global $wp_query;

		$output .= wc_product_dropdown_categories( array(
			'show_option_none' => 'Filter by Group Order',
			'taxonomy' => 'group_order',
			'name' => 'group_order',
			'selected' => isset( $wp_query->query_vars['group_order'] ) ? $wp_query->query_vars['group_order'] : '',
		) );

		return $output;
	}

	/**
	 * Append /%group_order%/ to single product permalinks
	 *
	 * This assumes your product permalinks are set to:
	 * /products/%group_order%/
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function group_order_permalink($permalink, $post_id, $leavename) {
		if ( strpos($permalink, '%group_order%') === FALSE ) {
			return $permalink;
		}

		// Get post
		$post = get_post($post_id);
		if ( !$post ) {
			return $permalink;
		}

		// Get taxonomy terms
		$terms = wp_get_object_terms( $post->ID, 'group_order' );
		if ( !is_wp_error($terms) && !empty($terms) && is_object($terms[0]) ) {
			$taxonomy_slug = $terms[0]->slug;
		} else {
			$taxonomy_slug = 'product';
		}

		return str_replace('%group_order%', $taxonomy_slug, $permalink);
	}

	/**
	 * Custom Delivery shipping option label
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function change_cart_shipping_method_full_label( $label, $method ) {
		global $woocommerce;

		// Using a global "flat rate" shipping option, we're gonna change the label
		if ( $method->method_id == 'flat_rate' ) {
			$label = 'Pick up';
			$cart_items = $woocommerce->cart->get_cart(); // grab current items in cart
			$term_ids = array();
			$locations = array();

			foreach( $cart_items as $item => $values ) {
				$_product = wc_get_product( $values['data']->get_id() ); // grab WC product
				$_terms = get_the_terms( $_product->id, 'group_order'); // grab the group order terms associated with product

				// loop through the product's group orders terms
				foreach($_terms as $_term) {
					$term_id = $_term->term_id;

					// push uniquely to a global array
					if ( !in_array($term_id, $term_ids) ) {
						array_push( $term_ids, $_term->term_id );
					}
				}
			}

			// loop through all unique terms in the cart
			foreach( $term_ids as $term ) {
				$group_order = get_term($term, 'group_order');
				array_push( $locations, get_field('pickup_location', $group_order) );
			}

			// Combine multiple pickup locations
			$label = 'Pick up at ' . implode (" and ", $locations);
		}

		return $label;
	}

	/**
	 * Add duplicate link to group orders
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function duplicate_group_order_link( $actions, $term ) {
		if (current_user_can('edit_posts')) {
    	$actions['duplicate'] = '<a href="' . wp_nonce_url('admin.php?action=duplicate_group_order_as_draft&term=' . $term->term_id, basename(__FILE__), 'duplicate_nonce' ) . '" title="Duplicate this group order" rel="permalink">Duplicate</a>';
		}

		return $actions;
	}

	/**
	 * Actually duplicate a group order
	 *
	 * @since 	1.0.0
	 * @access 	public
	 */
	public function duplicate_group_order_as_draft( ) {
		global $wpdb;

		if (! ( isset( $_GET['term']) || isset( $_POST['term'])  || ( isset($_REQUEST['action']) && 'duplicate_group_order_as_draft' == $_REQUEST['action'] ) ) ) {
			wp_die('No post to duplicate has been supplied!');
		}

		// Nonce verification
		if ( !isset( $_GET['duplicate_nonce'] ) || !wp_verify_nonce( $_GET['duplicate_nonce'], basename( __FILE__ ) ) ) {
			return;
		}

		// get the original post id
		$term_id = (isset($_GET['term']) ? absint( $_GET['term'] ) : absint( $_POST['term'] ) );

		// and all the original post data then
		$term = get_term( $term_id );

		$current_user = wp_get_current_user();
		$new_term_author = $current_user->ID;

		// if term data exists, create the term duplicate
		if (isset( $term ) && $term != null) {

			// insert the term by wp_insert_term() function
			$new_term = wp_insert_term(
				'Copy of ' . $term->name,
				'group_order',
				array(
					'description' => $term->description,
					'slug' => 'copy-of-' . $term->slug
				)
			);

			if (is_wp_error( $new_term )) {
				wp_die('Oops, you can only duplicate a group order once!');
			} else {
				// Copy over ACF fields
				$pickup_location = get_field('pickup_location', $term);

				if ($pickup_location) {
					update_field('pickup_location', $pickup_location, 'group_order_'.$new_term['term_id']);
				}

				$due_date = get_field('due_date', $term);

				if ($due_date) {
					update_field('due_date', $due_date, 'group_order_'.$new_term['term_id']);
				}

				$image = get_field('image', $term);

				if ($image) {
					update_field('image', $image, 'group_order_'.$new_term['term_id']);
				}

				$hidden = get_field('hidden', $term);

				if ($hidden) {
					update_field('hidden', $hidden, 'group_order_'.$new_term['term_id']);
				}

				// finally, redirect to the edit taxonomy screen for the new draft
				wp_redirect( admin_url( 'term.php?taxonomy=group_order&post_type=product&tag_ID=' . $new_term['term_id'] ) );
			}

			exit;
		} else {
			wp_die('Post creation failed, could not find original post: ' . $post_id);
		}
	}

}
