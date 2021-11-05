<?php
/**
 * Extends DateTime and includes translation capabilities.
 *
 * @package StellarWP\Helix\Utils
 * @since   1.0.0
 */
namespace StellarWP\Helix\Utils;

use DateTime;

/**
 * Class Date i18n
 *
 * @package StellarWP\Helix\Utils
 * @since   1.0.0
 */
class Date_I18n extends DateTime {
	/**
	 * {@inheritDoc}
	 *
	 * @return Date_I18n Localizable variation of DateTime.
	 */
	public static function createFromImmutable( $datetime ) {
		$date_object = new self;
		$date_object->setTimestamp( $datetime->getTimestamp() );
		$date_object->setTimezone( $datetime->getTimezone() );
		return $date_object;
	}

	/**
	 * Returns a translated string using the params from this DateTime instance.
	 *
	 * @since  1.0.0
	 *
	 * @param  string $date_format Format to be used in the translation.
	 *
	 * @return string         Translated date.
	 */
	public function format_i18n( $date_format ) {
		$unix_with_tz = $this->getTimestamp() + $this->getOffset();
		$translated   = date_i18n( $date_format, $unix_with_tz );

		return $translated;
	}
}
