<?php
defined( 'ABSPATH' ) || exit;

use Breakdance\DynamicData\StringField;
use Breakdance\DynamicData\StringData;

/**
 * Registers each show-level platform URL as a Breakdance dynamic data field.
 */
class NS_PM_Dynamic_Data {

	public static function init(): void {
		if (
			! function_exists( '\Breakdance\DynamicData\registerField' ) ||
			! class_exists( '\Breakdance\DynamicData\Field' )
		) {
			return;
		}

		$urls = [
			'show_url_spotify' => 'Podcast: Spotify URL',
			'show_url_apple'   => 'Podcast: Apple Podcasts URL',
			'show_url_youtube' => 'Podcast: YouTube URL',
			'show_url_amazon'  => 'Podcast: Amazon Music URL',
			'show_url_iheart'  => 'Podcast: iHeart Radio URL',
			'show_url_rss'     => 'Podcast: RSS Feed URL',
			'show_url_other'   => 'Podcast: Other Platform URL',
		];

		foreach ( $urls as $option_key => $label ) {
			\Breakdance\DynamicData\registerField(
				new NS_PM_Show_URL_Field( $option_key, $label )
			);
		}
	}
}

/**
 * A single reusable StringField that reads one show URL option key.
 */
class NS_PM_Show_URL_Field extends StringField {

	private string $option_key;
	private string $field_label;

	public function __construct( string $option_key, string $label ) {
		$this->option_key  = $option_key;
		$this->field_label = $label;
	}

	public function label(): string {
		return $this->field_label;
	}

	public function category(): string {
		return 'Podcast';
	}

	public function slug(): string {
		return 'ns_pm_' . $this->option_key;
	}

	public function returnTypes(): array {
		return [ 'url', 'string' ];
	}

	public function handler( $attributes ): StringData {
		$opts = ns_pm_options();
		return StringData::fromString( $opts[ $this->option_key ] ?? '' );
	}
}
