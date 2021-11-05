<?php
/**
 * Provides methods to detect whether a field is a post field, a taxonomy or a custom field in relation to a post type.
 *
 * Note that the trait does not include a `is_a_custom_field` method as that's implied from a field not being
 * a post field and not being a taxonomy.
 *
 * @since   1.0.0
 *
 * @package StellarWP\Helix\Traits
 */

namespace StellarWP\Helix\Traits;

/**
 * Trait With_Post_Attribute_Detection
 *
 * @since   1.0.0
 *
 * @package StellarWP\Helix\Traits
 */
trait With_Post_Attribute_Detection {

	/**
	 * Whether the key is a field of the posts table or not.
	 *
	 * @param string $key The field to check.
	 *
	 * @return bool Whether the key indicates a post field, a column in the `posts` table, or not.
	 */
	protected function is_a_post_field( $key ) {
		return in_array( $key, [
			'ID',
			'post_author',
			'post_date',
			'post_date_gmt',
			'post_content',
			'post_title',
			'post_excerpt',
			'post_status',
			'comment_status',
			'ping_status',
			'post_password',
			'post_name',
			'to_ping',
			'pinged',
			'post_modified',
			'post_modified_gmt',
			'post_content_filtered',
			'post_parent',
			'guid',
			'menu_order',
			'post_type',
			'post_mime_type',
			'comment_count',
		], true );
	}

	/**
	 * Whether the current key identifies one of the supported taxonomies or not.
	 * 
	 * @since 1.0.0
	 *
	 * @param string $key The field to check.
	 *
	 * @return bool Whether the key indicates a taxonomy of the post type or not.
	 */
	protected function is_a_taxonomy( $key ) {
		if ( ! isset( $this->taxonomies ) ) {
			// If we're here, then the developer made an error: throw an exception to bring this up as early as possible.
			throw new \RuntimeException(
				'The ' . __TRAIT__ . ' trait requires the user class to define a $taxonomies array parameter.'
			);
		}

		return in_array( $key, $this->taxonomies, true );
	}
}
