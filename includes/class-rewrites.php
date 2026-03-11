<?php
defined( 'ABSPATH' ) || exit;

class NS_PM_Rewrites {

	const FLUSH_FLAG = 'ns_pm_flush_rewrites';

	public static function init(): void {
		// Run late on init so the CPT is already registered before we flush.
		add_action( 'init', [ __CLASS__, 'maybe_flush' ], 99 );
	}

	/**
	 * Schedule a flush on the next request by setting a flag.
	 * Call this instead of flush_rewrite_rules() directly during a settings save,
	 * because init (and therefore CPT registration) has already run by that point.
	 */
	public static function schedule(): void {
		update_option( self::FLUSH_FLAG, '1', false );
	}

	/**
	 * If the flush flag is set, flush now (CPT is registered at this point)
	 * and clear the flag.
	 */
	public static function maybe_flush(): void {
		if ( get_option( self::FLUSH_FLAG ) ) {
			flush_rewrite_rules();
			delete_option( self::FLUSH_FLAG );
		}
	}

	/**
	 * Hard flush — used on deactivation.
	 */
	public static function flush(): void {
		flush_rewrite_rules();
	}
}
