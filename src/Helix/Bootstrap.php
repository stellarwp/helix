<?php

namespace StellarWP\Helix;

class Boostrap {
	public static function init() {
		require_once dirname( dirname( dirname( __FILE__ ) ) ) . '/vendor/autoload.php';
		require_once dirname( __DIR__ ) . '/functions/orm.php';
	}
}