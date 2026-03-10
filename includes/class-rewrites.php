<?php
defined( 'ABSPATH' ) || exit;

class NS_PM_Rewrites {

	/**
	 * Flush rewrite rules safely (schedules a flush on next load
	 * rather than flushing in the middle of a settings save).
	 */
	public static function flush(): void {
		flush_rewrite_rules();
	}
}
