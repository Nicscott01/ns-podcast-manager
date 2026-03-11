<?php
/**
 * Seeds featured images for seeded podcast episodes.
 * Images are sourced from SourceSplash (sourcesplash.com) — free, no API key needed.
 *
 * Seed:   wp eval-file web/app/plugins/ns-podcast-manager/seed-images.php --allow-root
 * Unseed: wp eval-file web/app/plugins/ns-podcast-manager/seed-images.php --allow-root -- --unseed
 *
 * Requires: episodes already seeded via seed-episodes.php
 */

$unseed = in_array( '--unseed', $args ?? [], true );

if ( $unseed ) {
	$seeded_ids = get_posts( [
		'post_type'      => 'podcast',
		'post_status'    => 'any',
		'posts_per_page' => -1,
		'meta_key'       => '_ns_pm_seeded',
		'meta_value'     => '1',
		'fields'         => 'ids',
	] );

	foreach ( $seeded_ids as $post_id ) {
		$thumb_id = get_post_thumbnail_id( $post_id );
		if ( $thumb_id ) {
			wp_delete_attachment( $thumb_id, true );
			delete_post_thumbnail( $post_id );
			WP_CLI::success( "Removed featured image from post {$post_id}" );
		}
	}

	WP_CLI::success( 'Featured images removed.' );
	return;
}

// Map episode_number → SourceSplash keyword(s).
$episode_images = [
	47 => [ 'query' => 'meditation healing',        'filename' => 'ep047-art.jpg' ],
	48 => [ 'query' => 'healthy food wellness',     'filename' => 'ep048-art.jpg' ],
	49 => [ 'query' => 'hope resilience sunrise',   'filename' => 'ep049-art.jpg' ],
	50 => [ 'query' => 'sleep calm night',          'filename' => 'ep050-art.jpg' ],
];

$upload_dir = wp_upload_dir();

foreach ( $episode_images as $ep_num => $image_info ) {
	// Find the seeded post for this episode number.
	$posts = get_posts( [
		'post_type'      => 'podcast',
		'post_status'    => 'any',
		'posts_per_page' => 1,
		'meta_query'     => [
			[ 'key' => '_ns_pm_seeded', 'value' => '1' ],
			[ 'key' => 'episode_number', 'value' => $ep_num ],
		],
		'fields' => 'ids',
	] );

	if ( empty( $posts ) ) {
		WP_CLI::warning( "No seeded post found for episode #{$ep_num} — skipping." );
		continue;
	}

	$post_id = $posts[0];

	// Build SourceSplash URL.
	$query     = urlencode( $image_info['query'] );
	$image_url = "https://www.sourcesplash.com/i/random?q={$query}&w=1200&h=630";

	WP_CLI::log( "Fetching image for episode #{$ep_num}: {$image_info['query']}..." );

	$response = wp_remote_get( $image_url, [
		'timeout'     => 30,
		'redirection' => 5,
	] );

	if ( is_wp_error( $response ) ) {
		WP_CLI::warning( "Failed to fetch image for episode #{$ep_num}: " . $response->get_error_message() );
		continue;
	}

	$http_code = wp_remote_retrieve_response_code( $response );
	if ( $http_code !== 200 ) {
		WP_CLI::warning( "Non-200 response ({$http_code}) for episode #{$ep_num} — skipping." );
		continue;
	}

	$image_data    = wp_remote_retrieve_body( $response );
	$content_type  = wp_remote_retrieve_header( $response, 'content-type' );

	// Derive extension from content-type.
	$ext_map = [
		'image/jpeg' => 'jpg',
		'image/png'  => 'png',
		'image/webp' => 'webp',
		'image/gif'  => 'gif',
	];
	$base_type = strtok( $content_type, ';' );
	$ext       = $ext_map[ trim( $base_type ) ] ?? 'jpg';
	$filename  = "ep0{$ep_num}-art.{$ext}";

	// Save to uploads.
	$file_path = trailingslashit( $upload_dir['path'] ) . $filename;

	if ( file_put_contents( $file_path, $image_data ) === false ) {
		WP_CLI::warning( "Could not write image file for episode #{$ep_num}." );
		continue;
	}

	// Create the attachment.
	$attachment = [
		'post_title'     => "Episode #{$ep_num} Art",
		'post_mime_type' => trim( $base_type ),
		'post_status'    => 'inherit',
		'post_parent'    => $post_id,
	];

	$attach_id = wp_insert_attachment( $attachment, $file_path, $post_id );

	if ( is_wp_error( $attach_id ) ) {
		WP_CLI::warning( "Failed to insert attachment for episode #{$ep_num}: " . $attach_id->get_error_message() );
		continue;
	}

	// Generate image metadata (thumbnails, etc).
	require_once ABSPATH . 'wp-admin/includes/image.php';
	$attach_data = wp_generate_attachment_metadata( $attach_id, $file_path );
	wp_update_attachment_metadata( $attach_id, $attach_data );

	// Set as featured image.
	set_post_thumbnail( $post_id, $attach_id );

	WP_CLI::success( "Set featured image for episode #{$ep_num} (post {$post_id}, attachment {$attach_id})" );
}

WP_CLI::success( 'Image seeding complete.' );
