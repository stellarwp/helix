<?php
/**
 * Date utility functions used throughout TEC + Addons
 */

namespace StellarWP\Helix\Utils;

use DateTime;
use DateTimeImmutable;
use DateTimeZone;

class Date_Utils {
	// Default formats, they are overridden by WP options or by arguments to date methods
	const DATEONLYFORMAT        = 'F j, Y';
	const TIMEFORMAT            = 'g:i A';
	const HOURFORMAT            = 'g';
	const MINUTEFORMAT          = 'i';
	const MERIDIANFORMAT        = 'A';
	const DBDATEFORMAT          = 'Y-m-d';
	const DBDATETIMEFORMAT      = 'Y-m-d H:i:s';
	const DBTZDATETIMEFORMAT    = 'Y-m-d H:i:s O';
	const DBTIMEFORMAT          = 'H:i:s';
	const DBYEARMONTHTIMEFORMAT = 'Y-m';

	/**
	 * check if a given string is a timestamp
	 *
	 * @param $timestamp
	 *
	 * @return bool
	 */
	public static function is_timestamp( $timestamp ) {
		if ( is_numeric( $timestamp ) && (int) $timestamp == $timestamp && date( 'U', $timestamp ) == $timestamp ) {
			return true;
		}

		return false;
	}

	/**
	 * Builds a date object from a given datetime and timezone.
	 *
	 * @since 4.9.5
	 *
	 * @param string|DateTime|int      $datetime      A `strtotime` parse-able string, a DateTime object or
	 *                                                a timestamp; defaults to `now`.
	 * @param string|DateTimeZone|null $timezone      A timezone string, UTC offset or DateTimeZone object;
	 *                                                defaults to the site timezone; this parameter is ignored
	 *                                                if the `$datetime` parameter is a DatTime object.
	 * @param bool                     $with_fallback Whether to return a DateTime object even when the date data is
	 *                                                invalid or not; defaults to `true`.
	 *
	 * @return DateTime|false A DateTime object built using the specified date, time and timezone; if `$with_fallback`
	 *                        is set to `false` then `false` will be returned if a DateTime object could not be built.
	 */
	public static function build_date_object( $datetime = 'now', $timezone = null, $with_fallback = true ) {
		if ( $datetime instanceof DateTime ) {
			return clone $datetime;
		}

		if ( class_exists( 'DateTimeImmutable' ) && $datetime instanceof DateTimeImmutable ) {
			// Return the mutable version of the date.
			return Date_I18n::createFromImmutable( $datetime );
		}

		$timezone_object = null;
		$datetime = empty( $datetime ) ? 'now' : $datetime;

		try {
			// PHP 5.2 will not throw an exception but will generate an error.
			$utc = new DateTimeZone( 'UTC' );
			$timezone_object = Timezones::build_timezone_object( $timezone );

			if ( self::is_timestamp( $datetime ) ) {
				$timestamp_timezone = $timezone ? $timezone_object : $utc;

				return new Date_I18n( '@' . $datetime, $timestamp_timezone );
			}

			set_error_handler( 'tribe_catch_and_throw' );
			$date = new Date_I18n( $datetime, $timezone_object );
			restore_error_handler();
		} catch ( \Exception $e ) {
			// If we encounter an error, we need to restore after catching.
			restore_error_handler();

			if ( $timezone_object === null ) {
				$timezone_object = Timezones::build_timezone_object( $timezone );
			}

			return $with_fallback
				? new Date_I18n( 'now', $timezone_object )
				: false;
		}

		return $date;
	}

	/**
	 * Builds the immutable version of a date from a string, integer (timestamp) or \DateTime object.
	 *
	 * It's the immutable version of the `Date_Utils::build_date_object` method.
	 *
	 * @since 4.10.2
	 *
	 * @param string|DateTime|int      $datetime      A `strtotime` parse-able string, a DateTime object or
	 *                                                a timestamp; defaults to `now`.
	 * @param string|DateTimeZone|null $timezone      A timezone string, UTC offset or DateTimeZone object;
	 *                                                defaults to the site timezone; this parameter is ignored
	 *                                                if the `$datetime` parameter is a DatTime object.
	 * @param bool                     $with_fallback Whether to return a DateTime object even when the date data is
	 *                                                invalid or not; defaults to `true`.
	 *
	 * @return DateTimeImmutable|false A DateTime object built using the specified date, time and timezone; if
	 *                                 `$with_fallback` is set to `false` then `false` will be returned if a
	 *                                 DateTime object could not be built.
	 */
	static function immutable( $datetime = 'now', $timezone = null, $with_fallback = true ) {
		static $cache = [];

		if ( $datetime instanceof DateTimeImmutable ) {
			return $datetime;
		}

		if ( $datetime instanceof DateTime ) {
			return Date_I18n_Immutable::createFromMutable( $datetime );
		}

		$mutable = static::build_date_object( $datetime, $timezone, $with_fallback );

		if ( false === $mutable ) {
			return false;
		}

		$cache_key = md5( ( __METHOD__ . $mutable->getTimezone()->getName() . $mutable->getTimestamp() ) );

		if ( isset( $cache[ $cache_key] ) && false !== $cached = $cache[ $cache_key ] ) {
			return $cached;
		}

		$immutable = Date_I18n_Immutable::createFromMutable( $mutable );

		$cache[ $cache_key ] = $immutable;

		return $immutable;
	}

	/**
	 * Builds a date object from a given datetime and timezone.
	 *
	 * An alias of the `Date_Utils::build_date_object` function.
	 *
	 * @since 4.10.2
	 *
	 * @param string|DateTime|int      $datetime      A `strtotime` parse-able string, a DateTime object or
	 *                                                a timestamp; defaults to `now`.
	 * @param string|DateTimeZone|null $timezone      A timezone string, UTC offset or DateTimeZone object;
	 *                                                defaults to the site timezone; this parameter is ignored
	 *                                                if the `$datetime` parameter is a DatTime object.
	 * @param bool                     $with_fallback Whether to return a DateTime object even when the date data is
	 *                                                invalid or not; defaults to `true`.
	 *
	 * @return DateTime|false A DateTime object built using the specified date, time and timezone; if `$with_fallback`
	 *                        is set to `false` then `false` will be returned if a DateTime object could not be built.
	 */
	public static function mutable( $datetime = 'now', $timezone = null, $with_fallback = true ) {
		return static::build_date_object( $datetime, $timezone, $with_fallback );
	}
}
