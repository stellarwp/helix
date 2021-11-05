<?php

namespace StellarWP\Helix\Utils;

class Log {
	const DEBUG   = 'debug';
	const DISABLE = 'disable';
	const ERROR   = 'error';
	const SUCCESS = 'success';
	const WARNING = 'warning';

	public static function log( $message, $level, $details ) {
		/**
		 * Provide hook for watching logging info.
		 * 
		 * @since 1.0.0
		 * 
		 * @param string $message Log message.
		 * @param string $level Log level.
		 * @param string $details Log details.
		 */
		do_action(
			'stellarwp_helix_log',
			$message,
			$level,
			$details
		);
	}
}