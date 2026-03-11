<?php
defined( 'ABSPATH' ) || exit;

class NS_PM_Settings {

	const OPTION_KEY = 'ns_podcast_manager_options';

	public static function init(): void {
		add_action( 'admin_menu', [ __CLASS__, 'add_page' ] );
		add_action( 'admin_init', [ __CLASS__, 'register_settings' ] );
		add_action( 'update_option_' . self::OPTION_KEY, [ __CLASS__, 'maybe_schedule_flush' ], 10, 2 );
		add_action( 'admin_enqueue_scripts', [ __CLASS__, 'enqueue_assets' ] );
		add_action( 'admin_notices', [ __CLASS__, 'flush_notice' ] );
		NS_PM_Rewrites::init();
	}

	public static function add_page(): void {
		add_options_page(
			__( 'Podcast Manager', 'ns-podcast-manager' ),
			__( 'Podcast Manager', 'ns-podcast-manager' ),
			'manage_options',
			'ns-podcast-manager',
			[ __CLASS__, 'render_page' ]
		);
	}

	public static function enqueue_assets( string $hook ): void {
		if ( $hook !== 'settings_page_ns-podcast-manager' ) {
			return;
		}
		wp_enqueue_style(
			'ns-pm-admin',
			NS_PM_URL . 'assets/admin.css',
			[],
			NS_PM_VERSION
		);
	}

	public static function register_settings(): void {
		register_setting(
			'ns_pm_settings_group',
			self::OPTION_KEY,
			[ 'sanitize_callback' => [ __CLASS__, 'sanitize' ] ]
		);

		// --- General ---
		add_settings_section( 'ns_pm_general', __( 'General', 'ns-podcast-manager' ), '__return_false', 'ns-podcast-manager' );

		self::add_field( 'show_name', __( 'Show Name', 'ns-podcast-manager' ), 'text', 'ns_pm_general',
			__( 'Shown in the admin menu and used for labeling.', 'ns-podcast-manager' ) );
		self::add_field( 'singular_label', __( 'Singular Label', 'ns-podcast-manager' ), 'text', 'ns_pm_general',
			__( 'e.g. Episode', 'ns-podcast-manager' ) );
		self::add_field( 'plural_label', __( 'Plural Label', 'ns-podcast-manager' ), 'text', 'ns_pm_general',
			__( 'e.g. Episodes', 'ns-podcast-manager' ) );
		self::add_field( 'cpt_slug', __( 'URL Slug', 'ns-podcast-manager' ), 'text', 'ns_pm_general',
			__( 'Base slug for episode URLs (e.g. <code>podcasts</code> → <code>/podcasts/episode-title/</code>). Changing this will flush rewrite rules.', 'ns-podcast-manager' ) );
		self::add_field( 'menu_icon', __( 'Menu Icon', 'ns-podcast-manager' ), 'text', 'ns_pm_general',
			__( 'Dashicon slug, e.g. <code>dashicons-microphone</code>.', 'ns-podcast-manager' ) );
		self::add_field( 'enable_archive', __( 'Enable Archive', 'ns-podcast-manager' ), 'checkbox', 'ns_pm_general',
			__( 'Enable the episode archive page.', 'ns-podcast-manager' ) );
		self::add_field( 'enable_rest', __( 'Enable REST API', 'ns-podcast-manager' ), 'checkbox', 'ns_pm_general',
			__( 'Expose episodes in the WP REST API.', 'ns-podcast-manager' ) );

		// --- Episode Fields ---
		add_settings_section( 'ns_pm_fields', __( 'Episode Fields', 'ns-podcast-manager' ), function () {
			echo '<p>' . esc_html__( 'Toggle optional ACF fields on or off for this install.', 'ns-podcast-manager' ) . '</p>';
		}, 'ns-podcast-manager' );

		self::add_field( 'enable_season', __( 'Season Field', 'ns-podcast-manager' ), 'checkbox', 'ns_pm_fields',
			__( 'Show a Season number field on episodes.', 'ns-podcast-manager' ) );
		self::add_field( 'enable_transcript', __( 'Transcript Field', 'ns-podcast-manager' ), 'checkbox', 'ns_pm_fields',
			__( 'Show a Transcript textarea on episodes.', 'ns-podcast-manager' ) );
		self::add_field( 'enable_guests', __( 'Guest Repeater', 'ns-podcast-manager' ), 'checkbox', 'ns_pm_fields',
			__( 'Show a Guest Name/Bio repeater on episodes. Requires ACF Pro.', 'ns-podcast-manager' ) );

		// --- Show-Level Platform URLs ---
		add_settings_section( 'ns_pm_show_urls', __( 'Show URLs (Platform Links)', 'ns-podcast-manager' ), function () {
			echo '<p>' . esc_html__( 'Links to your podcast\'s main page on each platform. Use these in footers, sidebars, and "Listen On" badges.', 'ns-podcast-manager' ) . '</p>';
		}, 'ns-podcast-manager' );

		self::add_field( 'show_url_spotify',     __( 'Spotify', 'ns-podcast-manager' ),       'url', 'ns_pm_show_urls' );
		self::add_field( 'show_url_apple',       __( 'Apple Podcasts', 'ns-podcast-manager' ), 'url', 'ns_pm_show_urls' );
		self::add_field( 'show_url_youtube',     __( 'YouTube', 'ns-podcast-manager' ),        'url', 'ns_pm_show_urls' );
		self::add_field( 'show_url_amazon',      __( 'Amazon Music', 'ns-podcast-manager' ),   'url', 'ns_pm_show_urls' );
		self::add_field( 'show_url_iheart',      __( 'iHeart Radio', 'ns-podcast-manager' ),   'url', 'ns_pm_show_urls' );
		self::add_field( 'show_url_rss',         __( 'RSS Feed URL', 'ns-podcast-manager' ),   'url', 'ns_pm_show_urls',
			__( 'Auto-generated or a manual override (e.g. your Feedburner/Podbean feed).', 'ns-podcast-manager' ) );
		self::add_field( 'show_url_other_label', __( 'Other Platform Label', 'ns-podcast-manager' ), 'text', 'ns_pm_show_urls',
			__( 'Label for the extra platform link below (e.g. Stitcher).', 'ns-podcast-manager' ) );
		self::add_field( 'show_url_other',       __( 'Other Platform URL', 'ns-podcast-manager' ),   'url', 'ns_pm_show_urls' );
	}

	private static function add_field( string $key, string $label, string $type, string $section, string $description = '' ): void {
		add_settings_field(
			'ns_pm_' . $key,
			$label,
			[ __CLASS__, 'render_field' ],
			'ns-podcast-manager',
			$section,
			[
				'key'         => $key,
				'type'        => $type,
				'description' => $description,
				'label_for'   => 'ns_pm_' . $key,
			]
		);
	}

	public static function render_field( array $args ): void {
		$opts  = ns_pm_options();
		$key   = $args['key'];
		$type  = $args['type'];
		$value = $opts[ $key ] ?? '';
		$id    = 'ns_pm_' . $key;
		$name  = self::OPTION_KEY . '[' . $key . ']';

		if ( $type === 'checkbox' ) {
			printf(
				'<label><input type="checkbox" id="%s" name="%s" value="1" %s> %s</label>',
				esc_attr( $id ),
				esc_attr( $name ),
				checked( $value, '1', false ),
				esc_html( $args['description'] )
			);
		} else {
			printf(
				'<input type="%s" id="%s" name="%s" value="%s" class="regular-text">',
				esc_attr( $type ),
				esc_attr( $id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			if ( $args['description'] ) {
				printf( '<p class="description">%s</p>', wp_kses( $args['description'], [ 'code' => [] ] ) );
			}
		}
	}

	public static function sanitize( array $input ): array {
		$clean = [];

		$text_fields = [
			'show_name', 'singular_label', 'plural_label', 'cpt_slug',
			'menu_icon', 'show_url_other_label',
		];
		$url_fields = [
			'show_url_spotify', 'show_url_apple', 'show_url_youtube',
			'show_url_amazon', 'show_url_iheart', 'show_url_rss', 'show_url_other',
		];
		$checkbox_fields = [
			'enable_archive', 'enable_rest', 'enable_season',
			'enable_transcript', 'enable_guests',
		];

		foreach ( $text_fields as $f ) {
			$clean[ $f ] = sanitize_text_field( $input[ $f ] ?? '' );
		}
		foreach ( $url_fields as $f ) {
			$clean[ $f ] = esc_url_raw( $input[ $f ] ?? '' );
		}
		foreach ( $checkbox_fields as $f ) {
			$clean[ $f ] = isset( $input[ $f ] ) && $input[ $f ] === '1' ? '1' : '0';
		}

		// Ensure slug is URL-safe.
		if ( $clean['cpt_slug'] ) {
			$clean['cpt_slug'] = sanitize_title( $clean['cpt_slug'] );
		}

		return $clean;
	}

	public static function maybe_schedule_flush( $old, $new ): void {
		if ( ( $old['cpt_slug'] ?? '' ) !== ( $new['cpt_slug'] ?? '' ) ) {
			// Don't flush here — init has already run with the old slug.
			// Schedule a flush for the next request, after the CPT registers with the new slug.
			NS_PM_Rewrites::schedule();
			set_transient( 'ns_pm_slug_changed', '1', 30 );
		}
	}

	public static function flush_notice(): void {
		if ( ! get_transient( 'ns_pm_slug_changed' ) ) {
			return;
		}
		delete_transient( 'ns_pm_slug_changed' );
		?>
		<div class="notice notice-success is-dismissible">
			<p><?php esc_html_e( 'Podcast Manager: URL slug updated. Permalinks have been flushed automatically.', 'ns-podcast-manager' ); ?></p>
		</div>
		<?php
	}

	public static function render_page(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}
		?>
		<div class="wrap ns-pm-settings">
			<h1><?php esc_html_e( 'Podcast Manager Settings', 'ns-podcast-manager' ); ?></h1>
			<form method="post" action="options.php">
				<?php
				settings_fields( 'ns_pm_settings_group' );
				do_settings_sections( 'ns-podcast-manager' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}
}
