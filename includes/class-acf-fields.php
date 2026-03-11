<?php
defined( 'ABSPATH' ) || exit;

class NS_PM_ACF_Fields {

	public static function init(): void {
		if ( ! function_exists( 'acf_add_local_field_group' ) ) {
			add_action( 'admin_notices', [ __CLASS__, 'acf_notice' ] );
			return;
		}
		add_action( 'acf/init', [ __CLASS__, 'register_fields' ] );
	}

	public static function acf_notice(): void {
		?>
		<div class="notice notice-warning">
			<p><?php esc_html_e( 'NS Podcast Manager: Advanced Custom Fields (ACF) is required for episode custom fields.', 'ns-podcast-manager' ); ?></p>
		</div>
		<?php
	}

	public static function register_fields(): void {
		$opts = ns_pm_options();

		$fields = [];
		$order  = 10;

		// Episode Number
		$fields[] = [
			'key'           => 'field_ns_pm_episode_number',
			'label'         => __( 'Episode Number', 'ns-podcast-manager' ),
			'name'          => 'episode_number',
			'type'          => 'number',
			'instructions'  => '',
			'required'      => 0,
			'min'           => 1,
			'step'          => 1,
			'wrapper'       => [ 'width' => '25' ],
			'menu_order'    => $order,
		];
		$order += 10;

		// Season (toggleable)
		if ( $opts['enable_season'] === '1' ) {
			$fields[] = [
				'key'        => 'field_ns_pm_season',
				'label'      => __( 'Season', 'ns-podcast-manager' ),
				'name'       => 'season',
				'type'       => 'number',
				'min'        => 1,
				'step'       => 1,
				'wrapper'    => [ 'width' => '25' ],
				'menu_order' => $order,
			];
			$order += 10;
		}

		// Duration
		$fields[] = [
			'key'           => 'field_ns_pm_duration',
			'label'         => __( 'Duration', 'ns-podcast-manager' ),
			'name'          => 'duration',
			'type'          => 'text',
			'instructions'  => __( 'e.g. 42:30', 'ns-podcast-manager' ),
			'placeholder'   => '00:00',
			'wrapper'       => [ 'width' => '25' ],
			'menu_order'    => $order,
		];
		$order += 10;

		// Audio Player (oEmbed)
		$fields[] = [
			'key'          => 'field_ns_pm_audio_url',
			'label'        => __( 'Audio Player', 'ns-podcast-manager' ),
			'name'         => 'audio_url',
			'type'         => 'oembed',
			'instructions' => __( 'Paste the share URL from your podcast host (Spotify, Buzzsprout, Podbean, SoundCloud, etc.) — WordPress will embed the player automatically.', 'ns-podcast-manager' ),
			'menu_order'   => $order,
		];
		$order += 10;

		// Video Player (oEmbed)
		$fields[] = [
			'key'          => 'field_ns_pm_video_embed_url',
			'label'        => __( 'Video Player', 'ns-podcast-manager' ),
			'name'         => 'video_embed_url',
			'type'         => 'oembed',
			'instructions' => __( 'Paste the YouTube, Vimeo, or other video share URL — WordPress will embed the player automatically.', 'ns-podcast-manager' ),
			'menu_order'   => $order,
		];
		$order += 10;

		// --- Episode Platform Links tab/separator ---
		$fields[] = [
			'key'        => 'field_ns_pm_tab_links',
			'label'      => __( 'Episode Links', 'ns-podcast-manager' ),
			'name'       => '',
			'type'       => 'tab',
			'placement'  => 'top',
			'menu_order' => $order,
		];
		$order += 10;

		// Spotify episode URL
		$fields[] = [
			'key'        => 'field_ns_pm_spotify_url',
			'label'      => __( 'Spotify Episode URL', 'ns-podcast-manager' ),
			'name'       => 'spotify_url',
			'type'       => 'url',
			'wrapper'    => [ 'width' => '50' ],
			'menu_order' => $order,
		];
		$order += 10;

		// Apple Podcasts episode URL
		$fields[] = [
			'key'        => 'field_ns_pm_apple_url',
			'label'      => __( 'Apple Podcasts Episode URL', 'ns-podcast-manager' ),
			'name'       => 'apple_url',
			'type'       => 'url',
			'wrapper'    => [ 'width' => '50' ],
			'menu_order' => $order,
		];
		$order += 10;

		// YouTube episode URL
		$fields[] = [
			'key'        => 'field_ns_pm_youtube_url',
			'label'      => __( 'YouTube Episode URL', 'ns-podcast-manager' ),
			'name'       => 'youtube_url',
			'type'       => 'url',
			'wrapper'    => [ 'width' => '50' ],
			'menu_order' => $order,
		];
		$order += 10;

		// Amazon Music episode URL
		$fields[] = [
			'key'        => 'field_ns_pm_amazon_url',
			'label'      => __( 'Amazon Music Episode URL', 'ns-podcast-manager' ),
			'name'       => 'amazon_url',
			'type'       => 'url',
			'wrapper'    => [ 'width' => '50' ],
			'menu_order' => $order,
		];
		$order += 10;

		// --- Show Notes tab ---
		$fields[] = [
			'key'        => 'field_ns_pm_tab_show_notes',
			'label'      => __( 'Show Notes', 'ns-podcast-manager' ),
			'name'       => '',
			'type'       => 'tab',
			'placement'  => 'top',
			'menu_order' => $order,
		];
		$order += 10;

		$fields[] = [
			'key'        => 'field_ns_pm_show_notes',
			'label'      => __( 'Show Notes', 'ns-podcast-manager' ),
			'name'       => 'show_notes',
			'type'       => 'wysiwyg',
			'tabs'       => 'all',
			'toolbar'    => 'full',
			'media_upload' => 1,
			'menu_order' => $order,
		];
		$order += 10;

		// Transcript (toggleable)
		if ( $opts['enable_transcript'] === '1' ) {
			$fields[] = [
				'key'        => 'field_ns_pm_transcript',
				'label'      => __( 'Transcript', 'ns-podcast-manager' ),
				'name'       => 'transcript',
				'type'       => 'textarea',
				'rows'       => 10,
				'menu_order' => $order,
			];
			$order += 10;
		}

		// --- Guests tab (toggleable, ACF Pro repeater) ---
		if ( $opts['enable_guests'] === '1' && function_exists( 'acf_get_field_groups' ) ) {
			$fields[] = [
				'key'        => 'field_ns_pm_tab_guests',
				'label'      => __( 'Guests', 'ns-podcast-manager' ),
				'name'       => '',
				'type'       => 'tab',
				'placement'  => 'top',
				'menu_order' => $order,
			];
			$order += 10;

			$fields[] = [
				'key'        => 'field_ns_pm_guests',
				'label'      => __( 'Guests', 'ns-podcast-manager' ),
				'name'       => 'guests',
				'type'       => 'repeater',
				'min'        => 0,
				'layout'     => 'block',
				'button_label' => __( 'Add Guest', 'ns-podcast-manager' ),
				'menu_order' => $order,
				'sub_fields' => [
					[
						'key'     => 'field_ns_pm_guest_name',
						'label'   => __( 'Name', 'ns-podcast-manager' ),
						'name'    => 'guest_name',
						'type'    => 'text',
						'wrapper' => [ 'width' => '40' ],
					],
					[
						'key'     => 'field_ns_pm_guest_title',
						'label'   => __( 'Title / Company', 'ns-podcast-manager' ),
						'name'    => 'guest_title',
						'type'    => 'text',
						'wrapper' => [ 'width' => '40' ],
					],
					[
						'key'     => 'field_ns_pm_guest_url',
						'label'   => __( 'Website', 'ns-podcast-manager' ),
						'name'    => 'guest_url',
						'type'    => 'url',
						'wrapper' => [ 'width' => '40' ],
					],
					[
						'key'     => 'field_ns_pm_guest_photo',
						'label'   => __( 'Photo', 'ns-podcast-manager' ),
						'name'    => 'guest_photo',
						'type'    => 'image',
						'return_format' => 'array',
						'preview_size'  => 'thumbnail',
						'wrapper' => [ 'width' => '20' ],
					],
					[
						'key'     => 'field_ns_pm_guest_bio',
						'label'   => __( 'Bio', 'ns-podcast-manager' ),
						'name'    => 'guest_bio',
						'type'    => 'textarea',
						'rows'    => 4,
					],
				],
			];
		}

		acf_add_local_field_group( [
			'key'                   => 'group_ns_pm_episode',
			'title'                 => sprintf( __( '%s Details', 'ns-podcast-manager' ), $opts['singular_label'] ),
			'fields'                => $fields,
			'location'              => [
				[ [ 'param' => 'post_type', 'operator' => '==', 'value' => 'podcast' ] ],
			],
			'menu_order'            => 0,
			'position'              => 'normal',
			'style'                 => 'default',
			'label_placement'       => 'top',
			'instruction_placement' => 'label',
			'active'                => true,
		] );
	}
}
