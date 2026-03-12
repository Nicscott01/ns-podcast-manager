<?php
defined( 'ABSPATH' ) || exit;

/**
 * Registers custom oEmbed providers for hosts WordPress does not support.
 *
 * We register each provider against a local fake endpoint and intercept requests
 * to that endpoint in-memory, so no external oEmbed request is required.
 */
class NS_PM_Embeds {

	/** Fake endpoint intercepted in pre_http_request. */
	private const ENDPOINT = 'https://oembed.ns-podcast-manager.invalid/oembed';

	public static function init(): void {
		add_action( 'init', [ __CLASS__, 'register_providers' ] );
		add_filter( 'pre_http_request', [ __CLASS__, 'intercept_http' ], 10, 3 );
		add_filter( 'pre_oembed_result', [ __CLASS__, 'intercept_embed' ], 10, 3 );
		// Fallback for ACF oEmbed formatting if a host bypasses provider resolution.
		add_filter( 'acf/format_value/type=oembed', [ __CLASS__, 'format' ], 20, 3 );
	}

	/**
	 * Register URL patterns so wp_oembed_get() recognizes them as providers.
	 */
	public static function register_providers(): void {
		foreach ( self::providers() as $provider ) {
			wp_oembed_add_provider( $provider['pattern'], self::ENDPOINT, true );
		}
	}

	/**
	 * Intercept calls to ENDPOINT and return synthetic oEmbed JSON.
	 *
	 * @param false|array $preempt Existing pre-empt value.
	 * @param array       $args    HTTP args.
	 * @param string      $url     Requested URL.
	 * @return false|array
	 */
	public static function intercept_http( $preempt, array $args, string $url ) {
		if ( strpos( $url, self::ENDPOINT ) === false ) {
			return $preempt;
		}

		$query = parse_url( $url, PHP_URL_QUERY );
		parse_str( $query ?? '', $params );
		$target_url = ( isset( $params['url'] ) && is_string( $params['url'] ) ) ? $params['url'] : '';
		$match = self::match_provider( $target_url );

		if ( ! $match ) {
			return $preempt;
		}

		$provider = $match['provider'];
		$html = call_user_func( $provider['html'], $target_url, $match['matches'] );

		return [
			'headers'  => [],
			'body'     => wp_json_encode( [
				'type'    => 'rich',
				'version' => '1.0',
				'title'   => 'Podcast Player',
				'html'    => $html,
				'width'   => 600,
				'height'  => $provider['height'] ?? 200,
			] ),
			'response' => [ 'code' => 200, 'message' => 'OK' ],
			'cookies'  => [],
			'filename' => null,
		];
	}

	/**
	 * Handles [embed] shortcode path (e.g. editor preview).
	 *
	 * @param string|null $result Existing pre-result.
	 * @param string      $url    URL being embedded.
	 * @param mixed       $args   Embed args.
	 * @return string|null
	 */
	public static function intercept_embed( $result, string $url, $args ) {
		$match = self::match_provider( $url );
		if ( ! $match ) {
			return $result;
		}

		return call_user_func( $match['provider']['html'], $url, $match['matches'] );
	}

	/**
	 * Fallback for ACF oEmbed format pipeline.
	 *
	 * @param mixed $value   Existing formatted value.
	 * @param mixed $post_id Post context passed by ACF.
	 * @param array $field   ACF field config.
	 * @return mixed
	 */
	public static function format( $value, $post_id, array $field ) {
		if ( empty( $field['name'] ) || ! is_string( $field['name'] ) ) {
			return $value;
		}

		$meta_post_id = is_numeric( $post_id ) ? (int) $post_id : 0;
		if ( $meta_post_id <= 0 ) {
			return $value;
		}

		$raw_url = get_post_meta( $meta_post_id, $field['name'], true );
		if ( empty( $raw_url ) || ! is_string( $raw_url ) ) {
			return $value;
		}

		$match = self::match_provider( $raw_url );
		if ( ! $match ) {
			return $value;
		}

		return call_user_func( $match['provider']['html'], $raw_url, $match['matches'] );
	}

	/**
	 * @return array{provider: array, matches: array}|null
	 */
	private static function match_provider( string $url ): ?array {
		foreach ( self::providers() as $provider ) {
			if ( preg_match( $provider['pattern'], $url, $matches ) ) {
				return [
					'provider' => $provider,
					'matches'  => $matches,
				];
			}
		}

		return null;
	}

	/**
	 * @return array<string, array{pattern: string, html: callable, height: int}>
	 */
	private static function providers(): array {
		return [
			'captivate' => [
				// Supports player.captivate.fm, captivate.fm, and subdomain variants.
				'pattern' => '~https?://(?:[a-z0-9-]+\.)*captivate\.fm/episode/([^/?#&]+)~i',
				'height'  => 200,
				'html'    => function ( string $url, array $matches ): string {
					$episode_id = sanitize_text_field( $matches[1] ?? '' );
					if ( $episode_id === '' ) {
						return '';
					}

					$src = esc_url( 'https://player.captivate.fm/episode/' . $episode_id );

					return '<div class="ns-pm-captivate-embed" style="width:100%;max-width:100%;height:200px;overflow:hidden;border-radius:6px;">'
						. '<iframe style="width:100%;height:100%;display:block;border:0;" width="100%" height="200" frameborder="0" scrolling="no"'
						. ' loading="lazy" allow="clipboard-write" title="Captivate podcast player"'
						. ' src="' . $src . '">'
						. '</iframe></div>';
				},
			],
		];
	}
}
