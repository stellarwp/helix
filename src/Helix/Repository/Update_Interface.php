<?php
namespace StellarWP\Helix\Repository;

/**
 * Interface Update_Interface
 *
 * @since 1.0.0
 */
interface Update_Interface extends Setter_Interface {

	/**
	 * Commits the updates to the selected post IDs to the database.
	 *
	 * @since 1.0.0
	 */
	public function save();

	/**
	 * Adds an alias for an update/save field.
	 *
	 * @since 1.0.0
	 *
	 * @param string $alias The alias to add.
	 * @param string $field_name The field name this alias should resolve to, this
	 *                           can be posts table field, a taxonomy name or a custom
	 *                           field.
	 */
	public function add_update_field_alias( $alias, $field_name );

	/**
	 * Returns the update fields aliases for the repository.
	 *
	 * @since 1.0.0
	 *
	 * @return array This repository update fields aliases map.
	 */
	public function get_update_fields_aliases();

	/**
	 * Replaces the update fields aliases for this repository.
	 *
	 * @since 1.0.0
	 *
	 * @param array $update_fields_aliases The new update fields aliases
	 *                                     map for this repository.
	 */
	public function set_update_fields_aliases( array $update_fields_aliases );

	/**
	 * Filters the post array before updates.
	 *
	 * Extending classes that need to perform some logic checks during updates
	 * should extend this method.
	 *
	 * @since 1.0.0
	 *
	 * @param array    $postarr The post array that will be sent to the update callback.
	 * @param int|null $post_id The ID  of the post that will be updated.
	 *
	 * @return array|false The filtered post array or `false` to indicate the
	 *                     update should not happen.
	 */
	public function filter_postarr_for_update( array $postarr, $post_id );

	/**
	 * Creates a post of the type managed by the repository with the fields
	 * provided using the `set` or `set_args` methods.
	 *
	 * @since 1.0.0
	 *
	 * @return WP_Post|false The created post object or `false` if the creation
	 *                       fails for logic or runtime issues.
	 */
	public function create();

	/**
	 * Builds the post array that should be used to update or create a post of
	 * the type managed by the repository.
	 *
	 * @since 1.0.0
	 *
	 * @param int|null $id The post ID that's being updated or `null` to get the
	 *                     post array for a new post.
	 *
	 * @return array The post array ready to be passed to the `wp_update_post` or
	 *               `wp_insert_post` functions.
	 *
	 * @throws Usage_Error If running an update and trying to update
	 *                                        a blocked field.
	 */
	public function build_postarr( $id = null );

	/**
	 * Filters the post array before creation.
	 *
	 * Extending classes that need to perform some logic checks during creations
	 * should extend this method.
	 *
	 * @since 1.0.0
	 *
	 * @param array $postarr The post array that will be sent to the creation callback.
	 *
	 * @return array|false The filtered post array or false to indicate creation should not
	 *                     proceed.
	 */
	public function filter_postarr_for_create( array $postarr );

	/**
	 * Sets the create args the repository will use to create posts.
	 *
	 * @since 1.0.0
	 *
	 * @param array $create_args The create args the repository will use to create posts.
	 */
	public function set_create_args( array $create_args );

	/**
	 * Returns the create args the repository will use to create posts.
	 *
	 * @since 1.0.0
	 *
	 * @return array The create args the repository will use to create posts.
	 */
	public function get_create_args();
}
