<?php

namespace StellarWP\Helix\Utils;

/**
 * Array utilities
 */
class Array_Utils {

	/**
	 * Set key/value within an array, can set a key nested inside of a multidimensional array.
	 *
	 * Example: set( $a, [ 0, 1, 2 ], 'hi' ) sets $a[0][1][2] = 'hi' and returns $a.
	 * 
	 * @since 1.0.0
	 *
	 * @param mixed		$array The array containing the key this sets.
	 * @param string|array $key To set a key nested multiple levels deep pass an array
	 *							 specifying each key in order as a value.
		*							 Example: array( 'lvl1', 'lvl2', 'lvl3' );
		* @param mixed		$value The value.
		*
		* @return array Full array with the key set to the specified value.
		*/
	public static function set( array $array, $key, $value ) {
		// Convert strings and such to array.
		$key = (array) $key;

		// Setup a pointer that we can point to the key specified.
		$key_pointer = &$array;

		// Iterate through every key, setting the pointer one level deeper each time.
		foreach ( $key as $i ) {

			// Ensure current array depth can have children set.
			if ( ! is_array( $key_pointer ) ) {
				// $key_pointer is set but is not an array. Converting it to an array
				// would likely lead to unexpected problems for whatever first set it.
				$error = sprintf(
					'Attempted to set $array[%1$s] but %2$s is already set and is not an array.',
					implode( '][', $key ),
					$i
				);

				_doing_it_wrong( __FUNCTION__, esc_html( $error ), '4.3');
				break;
			} elseif ( ! isset( $key_pointer[ $i ] ) ) {
				$key_pointer[ $i ] = [];
			}

			// Dive one level deeper into the nested array.
			$key_pointer = &$key_pointer[ $i ];
		}

		// Set the value for the specified key
		$key_pointer = $value;

		return $array;
	}

	/**
	 * Find a value inside of an array or object, including one nested a few levels deep.
	 *
	 * Example: get( $a, [ 0, 1, 2 ] ) returns the value of $a[0][1][2] or the default.
	 * 
	 * @since 1.0.0
	 *
	 * @param array		$variable Array or object to search within.
	 * @param array|string $indexes Specify each nested index in order.
	 *								Example: array( 'lvl1', 'lvl2' );
		* @param mixed		$default Default value if the search finds nothing.
		*
		* @return mixed The value of the specified index or the default if not found.
		*/
	public static function get( $variable, $indexes, $default = null ) {
		if ( is_object( $variable ) ) {
			$variable = (array) $variable;
		}

		if ( ! is_array( $variable ) ) {
			return $default;
		}

		foreach ( (array) $indexes as $index ) {
			if ( ! is_array( $variable ) || ! isset( $variable[ $index ] ) ) {
				$variable = $default;
				break;
			}

			$variable = $variable[ $index ];
		}

		return $variable;
	}

	/**
	 * Find a value inside a list of array or objects, including one nested a few levels deep.
	 *
	 * @since 1.0.0
	 *
	 * Example: get( [$a, $b, $c], [ 0, 1, 2 ] ) returns the value of $a[0][1][2] found in $a, $b or $c
	 * or the default.
	 *
	 * @param array		$variables Array of arrays or objects to search within.
	 * @param array|string $indexes Specify each nested index in order.
	 *								 Example: array( 'lvl1', 'lvl2' );
		* @param mixed		$default Default value if the search finds nothing.
		*
		* @return mixed The value of the specified index or the default if not found.
		*/
	public static function get_in_any( array $variables, $indexes, $default = null ) {
		foreach ( $variables as $variable ) {
			$found = self::get( $variable, $indexes, '__not_found__' );
			if ( '__not_found__' !== $found ) {
				return $found;
			}
		}

		return $default;
	}

	/**
	 * Behaves exactly like the native strpos(), but accepts an array of needles.
	 * 
	 * @since 1.0.0
	 *
	 * @param string	   $haystack String to search in.
	 * @param array|string $needles Strings to search for.
	 * @param int		  $offset Starting position of search.
	 *
	 * @return false|int Integer position of first needle occurrence.
	 * @see strpos()
	 *
	 */
	public static function strpos( $haystack, $needles, $offset = 0 ) {
		$needles = (array) $needles;

		foreach ( $needles as $i ) {
			$search = strpos( $haystack, $i, $offset );

			if ( false !== $search ) {
				return $search;
			}
		}

		return false;
	}

	/**
	 * Converts a list to an array filtering out empty string elements.
	 * 
	 * @since 1.0.0
	 *
	 * @param mixed  $value A string representing a list of values separated by the specified separator
	 *               or an array. If the list is a string (e.g. a CSV list) then it will urldecoded
	 *               before processing.
	 * @param string $sep The char(s) separating the list elements; will be ignored if the list is an array.
	 *
	 * @return array An array of list elements.
	 */
	public static function list_to_array( $value, $sep = ',' ) {
		// since we might receive URL encoded strings for CSV lists let's URL decode them first
		$value = is_array( $value ) ? $value : urldecode( $value );

		$sep = is_string( $sep ) ? $sep : ',';

		if ( $value === null || $value === '' ) {
			return [];
		}

		if ( ! is_array( $value ) ) {
			$value = preg_split( '/\\s*' . preg_quote( $sep ) . '\\s*/', $value );
		}

		$filtered = [];
		foreach ( $value as $v ) {
			if ( '' === $v ) {
				continue;
			}
			$filtered[] = is_numeric( $v ) ? $v + 0 : $v;
		}

		return $filtered;
	}

	/**
	 * Returns a list separated by the specified separator.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed  $list
	 * @param string $sep
	 *
	 * @return string The list separated by the specified separator or the original list if the list is empty.
	 */
	public static function to_list( $list, $sep = ',' ) {
		if ( empty( $list ) ) {
			return $list;
		}

		if ( is_array( $list ) ) {
			return implode( $sep, $list );
		}

		return $list;
	}

	/**
	 * Sanitize a multidimensional array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $data The array to sanitize.
	 *
	 * @return array The sanitized array
	 *
	 * @link https://gist.github.com/esthezia/5804445
	 */
	public static function escape_multidimensional_array( $data = [] ) {
		if ( ! is_array( $data ) || ! count( $data ) ) {
			return [];
		}

		foreach ( $data as $key => $value ) {
			if ( ! is_array( $value ) && ! is_object( $value ) ) {
				$data[ $key ] = esc_attr( trim( $value ) );
			}
			if ( is_array( $value ) ) {
				$data[ $key ] = self::escape_multidimensional_array( $value );
			}
		}

		return $data;
	}

	/**
	 * Duplicates any key prefixed with '_' creating an un-prefixed duplicate one.
	 *
	 * The un-prefixing and duplication is recursive.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $array The array whose keys should be duplicated.
	 * @param bool  $recursive Whether the un-prefixing and duplication should be
	 *						 recursive or shallow.
		*
		* @return array The array with the duplicate, unprefixed, keys or the
		*			   original input if not an array.
		*/
	public static function add_unprefixed_keys_to( $array, $recursive = false ) {
		if ( ! is_array( $array ) ) {
			return $array;
		}

		$unprefixed = [];
		foreach ( $array as $key => $value ) {
			if ( $recursive && is_array( $value ) ) {
				$value = self::add_unprefixed_keys_to( $value, true );
				// And also add it to the original array.
				$array[ $key ] = array_merge( $array[ $key ], $value );
			}

			if ( 0 !== strpos( $key, '_' ) ) {
				continue;
			}
			$unprefixed[ substr( $key, 1 ) ] = $value;
		}

		return array_merge( $array, $unprefixed );
	}

	/**
	 * Duplicates any key not prefixed with '_' creating a prefixed duplicate one.
	 *
	 * The prefixing and duplication is recursive.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $array The array whose keys should be duplicated.
	 * @param bool  $recursive Whether the prefixing and duplication should be
	 *						 recursive or shallow.
		*
		* @return array The array with the duplicate, prefixed, keys or the
		*			   original input if not an array.
		*/
	public static function add_prefixed_keys_to( $array, $recursive = false ) {
		if ( ! is_array( $array ) ) {
			return $array;
		}

		$prefixed = [];
		foreach ( $array as $key => $value ) {
			if ( $recursive && is_array( $value ) ) {
				$value = self::add_prefixed_keys_to( $value, true );
				// And also add it to the original array.
				$array[ $key ] = array_merge( $array[ $key ], $value );
			}

			if ( 0 === strpos( $key, '_' ) ) {
				continue;
			}

			$prefixed[ '_' . $key ] = $value;
		}

		return array_merge( $array, $prefixed );
	}

	/**
	 * Recursively key-sort an array.
	 *
	 * @since 1.0.0
	 *
	 * @param array $array The array to sort, modified by reference.
	 *
	 * @return bool The sorting result.
	 */
	public static function recursive_ksort( array &$array ) {
		foreach ( $array as &$value ) {
			if ( is_array( $value ) ) {
				static::recursive_ksort( $value );
			}
		}

		return ksort( $array );
	}

	/**
	 * Stringifies the numeric keys of an array.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int|string,mixed> $input  The input array whose keys should be stringified.
	 * @param string|null			 $prefix The prefix that should be use to stringify the keys, if not provided
	 *										then it will be generated.
		*
		* @return array<string,mixed> The input array with each numeric key stringified.
		*/
	public static function stringify_keys( array $input, $prefix = null ) {
		$prefix  = null === $prefix ? uniqid( 'sk_', true ) : $prefix;
		$visitor = static function ( $key, $value ) use ( $prefix ) {
			$string_key = is_numeric( $key ) ? $prefix . $key : $key;

			return [ $string_key, $value ];
		};

		return static::array_visit_recursive( $input, $visitor );
	}

	/**
	 * The inverse of the `stringify_keys` method, it will restore numeric keys for previously
	 * stringified keys.
	 *
	 * @since 1.0.0
	 *
	 * @param array<int|string,mixed> $input  The input array whose stringified keys should be
	 *										destringified.
		* @param string				  $prefix The prefix that should be used to target only specific string keys.
		*
		* @return array<int|string,mixed> The input array, its stringified keys destringified.
		*/
	public static function destringify_keys( array $input, $prefix = 'sk_' ) {
		$visitor = static function ( $key, $value ) use ( $prefix ) {
			$destringified_key = 0 === self::strpos( $key, $prefix ) ? null : $key;

			return [ $destringified_key, $value ];
		};

		return static::array_visit_recursive( $input, $visitor );
	}

	/**
	 * Recursively visits all elements of an array applying the specified callback to each element
	 * key and value.
	 *
	 * @since 1.0.0
	 *
	 * @param		 array $input The input array whose nodes should be visited.
	 * @param callable $visitor A callback function that will be called on each array item; the callback will
	 *						  receive the item key and value as input and should return an array that contains
		*						  the update key and value in the shape `[ <key>, <value> ]`. Returning a `null`
		*						  key will cause the element to be removed from the array.
		*/
	public static function array_visit_recursive( $input, callable $visitor ) {
		if ( ! is_array( $input ) ) {
			return $input;
		}

		$return = [];

		foreach ( $input as $key => &$value ) {
			if ( is_array( $value ) ) {
				$value = static::array_visit_recursive( $value, $visitor );
			}
			// Ensure visitors can quickly return `null` to remove an element.
			list( $updated_key, $update_value ) = array_replace( [ $key, $value ], (array) $visitor( $key, $value ) );
			if ( false === $updated_key ) {
				// Visitor will be able to remove an element by returning a `false` key for it.
				continue;
			}
			if ( null === $updated_key ) {
				// Automatically assign the first available numeric index to the element.
				$return[] = $update_value;
			} else {
				$return[ $updated_key ] = $update_value;
			}
		}

		return $return;
	}

	/**
	 * Merges two or more arrays in the nested format used by WP_Query arguments preserving and merging them correctly.
	 *
	 * The method will recursively replace named keys and merge numeric keys. The method takes its name from its intended
	 * primary use, but it's not limited to query arguments only.
	 *
	 * @since 1.0.0
	 *
	 * @param array<string|int,mixed> ...$arrays A set of arrays to merge.
	 *
	 * @return array<string|int,mixed> The recursively merged array.
	 */
	public static function merge_recursive_query_vars( array ...$arrays ) {
		if ( ! count( $arrays ) ) {
			return [];
		}

		// Temporarily transform numeric keys to string keys generated with time-related randomness.
		$stringified = array_map( [ static::class, 'stringify_keys' ], $arrays );
		// Replace recursive will recursively replace any entry that has the same string key, stringified keys will never match due to randomness.
		$merged = array_replace_recursive( ...$stringified );

		// Finally destringify the keys to return something that will resemble, in shape, the original arrays.
		return static::destringify_keys( $merged );
	}
}