<?php

namespace StellarWP\Helix\Utils;

class Log {
	const DEBUG   = 'debug';
	const DISABLE = 'disable';
	const ERROR   = 'error';
	const SUCCESS = 'success';
	const WARNING = 'warning';

	public static function log( $level, $source, $data ) {
		/**
		 * Provide hook for watching logging info.
		 * 
		 * @since 1.0.0
		 * 
		 * @param string $level Log level.
		 * @param string $source Log source.
		 * @param mixed  $data Log data.
		 */
		do_action(
			'helix_log',
			$level,
			$source,
			$data
		);
	}
}