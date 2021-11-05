<?php

namespace StellarWP\Helix\Utils;

class Regex {
	/**
	 * Checks whether a candidate string is a valid regular expression or not.
	 *
	 * @since 1.0.0
	 *
	 * @param string $candidate The candidate string to check, it must include the
	 *                          regular expression opening and closing tags to validate.
	 *
	 * @return bool Whether a candidate string is a valid regular expression or not.
	 */
	public static function is_regex( $candidate ) {
		if ( ! is_string( $candidate ) ) {
			return false;
		}

		// We need to have the Try/Catch for Warnings too
		try {
			return ! ( @preg_match( $candidate, null ) === false );
		} catch ( \Exception $e ) {
			return false;
		}
	}

	/**
	 * Removes fence characters and modifiers from a regular expression string.
	 *
	 * Use this to go from a PCRE-format regex (PHP) to one SQL can understand.
	 *
	 * @since 1.0.0
	 *
	 * @param string $regex The input regular expression string.
	 *
	 * @return string The un-fenced regular expression string.
	 */
	public static function unfenced_regex( $regex ) {
		if ( ! is_string( $regex ) ) {
			return $regex;
		}

		$str_fence = $regex[0];
		// Let's pick a fence char the string itself is not using.
		$fence_char = '~' === $str_fence ? '#' : '~';
		$pattern    = $fence_char
			. preg_quote( $str_fence, $fence_char ) // the opening fence
			. '(.*)' // keep anything after the opening fence, group 1
			. preg_quote( $str_fence, $fence_char ) // the closing fence
			. '.*' // any modifier after the closing fence
			. $fence_char;

		return preg_replace( $pattern, '$1', $regex );
	}
}
