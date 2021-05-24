<?php
namespace StellarWP\Helix\Repository;

/**
 * Interface Formatter_Interface
 *
 * This interface is usually implemented by repository decorators that
 * need not only to modify the filtering criteria but the return format
 * of the items as well.
 *
 * @since 4.7.19
 */
interface Formatter_Interface {
	/**
	 * Formats an item handled by a repository to the expected
	 * format.
	 *
	 * @since 4.7.19
	 *
	 * @param int|WP_Post $id
	 *
	 * @return mixed
	 */
	public function format_item( $id );
}
