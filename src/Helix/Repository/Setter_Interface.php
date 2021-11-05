<?php
namespace StellarWP\Helix\Repository;

/**
 * Interface Setter_Interface
 *
 * @since 1.0.0
 */
interface Setter_Interface {

	/**
	 * Sets a key on the posts to update using a value or a callback.
	 *
	 * The callback method will be passed the post ID, the `$key` and
	 * the Update repository instance.
	 * The update will check, in order, if the key is a post table field,
	 * a taxonomy and will, finally, set on a custom field.
	 * Updates to the same key will not stack.
	 *
	 * @since 1.0.0
	 *
	 * @param string         $key
	 * @param mixed|callable $value
	 *
	 * @return Update_Interface
	 * @throws Usage_Error If $key is not a string
	 */
	public function set( $key, $value );

	/**
	 * Sets updates in bulk using a map.
	 *
	 * Updates to the same key will not stack.
	 *
	 * @since 1.0.0
	 *
	 * @param array $update_map A map relating update keys to values.
	 *
	 * @return Update_Interface
	 * @throws Usage_Error If not all keys are strings.
	 *
	 * @see   the `set` method
	 */
	public function set_args( array $update_map );
}
