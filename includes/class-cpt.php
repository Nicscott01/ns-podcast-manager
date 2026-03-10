<?php
defined( 'ABSPATH' ) || exit;

class NS_PM_CPT {

	public static function init(): void {
		add_action( 'init', [ __CLASS__, 'register' ] );
	}

	public static function register(): void {
		$opts   = ns_pm_options();
		$single = $opts['singular_label'];
		$plural = $opts['plural_label'];
		$show   = $opts['show_name'];

		$labels = [
			'name'                  => $plural,
			'singular_name'         => $single,
			'menu_name'             => $show,
			'name_admin_bar'        => $single,
			'add_new'               => __( 'Add New', 'ns-podcast-manager' ),
			'add_new_item'          => sprintf( __( 'Add New %s', 'ns-podcast-manager' ), $single ),
			'new_item'              => sprintf( __( 'New %s', 'ns-podcast-manager' ), $single ),
			'edit_item'             => sprintf( __( 'Edit %s', 'ns-podcast-manager' ), $single ),
			'view_item'             => sprintf( __( 'View %s', 'ns-podcast-manager' ), $single ),
			'all_items'             => sprintf( __( 'All %s', 'ns-podcast-manager' ), $plural ),
			'search_items'          => sprintf( __( 'Search %s', 'ns-podcast-manager' ), $plural ),
			'not_found'             => sprintf( __( 'No %s found.', 'ns-podcast-manager' ), strtolower( $plural ) ),
			'not_found_in_trash'    => sprintf( __( 'No %s found in Trash.', 'ns-podcast-manager' ), strtolower( $plural ) ),
		];

		$args = [
			'labels'             => $labels,
			'public'             => true,
			'show_in_rest'       => (bool) $opts['enable_rest'],
			'menu_icon'          => $opts['menu_icon'] ?: 'dashicons-microphone',
			'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author', 'revisions' ],
			'has_archive'        => (bool) $opts['enable_archive'],
			'rewrite'            => [
				'slug'       => sanitize_title( $opts['cpt_slug'] ),
				'with_front' => false,
			],
			'hierarchical'       => false,
			'show_in_menu'       => true,
		];

		register_post_type( 'podcast', $args );
	}
}
