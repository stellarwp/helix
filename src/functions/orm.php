<?php

namespace StellarWP\Helix;

use StellarWP\Helix\Utils\Array_Utils as Arr;

if ( ! function_exists( __NAMESPACE__ . '\helix' ) ) {
	/**
	 * Get a helix instance.
	 * 
	 * @since 1.0.0
	 *
	 * @param string $repository Repository slug.
	 * 
	 * @return Repository
	 */
	function helix( $repository ) {
		$map = [
			'default' => 'post',
		];

		$args = func_num_args() > 1 ? array_slice( func_get_args(), 1 ) : [];

		/**
		 * Filters the map relating event repository slugs to service container bindings.
		 *
		 * @since 1.0.0
		 *
		 * @param array  $map        A map in the shape [ <repository_slug> => <service_name> ]
		 * @param string $repository The currently requested implementation.
		 * @param array $args        An array of additional call arguments used to call the function beside the
		 *                           repository slug.
		 */
		$map = apply_filters( 'helix_repository_map', $map, $repository, $args );

		$repository = Arr::get( $map, $repository, $map['default'] );
		$container  = Container::init();

		if ( null === $repository ) {
			return $container;
		}

		return $container->make( $repository );
	}
}

if ( ! function_exists( __NAMESPACE__ . '\helix_singleton' ) ) {
	/**
	 * Registers a class as a singleton.
	 *
	 * Each call to obtain an instance of this class made using the `tribe( $slug )` function
	 * will return the same instance; the instances are built just in time (if not passing an
	 * object instance or callback function) and on the first request.
	 * The container will call the class `__construct` method on the class (if not passing an object
	 * or a callback function) and will try to automagically resolve dependencies.
	 *
	 * Example use:
	 *
	 *      helix_singleton( My_Class::class, My_Class::class );
	 *
	 *      // some code later...
	 *
	 *      // class is built here
	 *      helix( My_Class::class )->doSomething();
	 *
	 * Need the class built immediately? Build it and register it:
	 *
	 *      helix_singleton( My_Class::class, new My_Class() );
	 *
	 *      // some code later...
	 *
	 *      helix( My_Class::class )->doSomething();
	 *
	 * Need a very custom way to build the class? Register a callback:
	 *
	 *      helix_singleton( My_Class::class, array( My_Class__Factory, 'make' ) );
	 *
	 *      // some code later...
	 *
	 *      helix( My_Class::class )->doSomething();
	 *
	 * Or register the methods that should be called on the object after its construction:
	 *
	 *      helix_singleton( My_Class::class, My_Class::class, array( 'hook', 'register' ) );
	 *
	 *      // some code later...
	 *
	 *      // the `hook` and `register` methods will be called on the built instance.
	 *      helix( My_Class::class )->doSomething();
	 *
	 * The class will be built only once (if passing the class name or a callback function), stored
	 * and the same instance will be returned from that moment on.
	 *
	 * @param string                 $slug                The human-readable and catchy name of the class.
	 * @param string|object|callable $class               The full class name or an instance of the class
	 *                                                    or a callback that will return the instance of the class.
	 * @param array                  $after_build_methods An array of methods that should be called on
	 *                                                    the built object after the `__construct` method; the methods
	 *                                                    will be called only once after the singleton instance
	 *                                                    construction.
	 */
	function helix_singleton( $slug, $class, array $after_build_methods = null ) {
		Container::init()->singleton( $slug, $class, $after_build_methods );
	}
}

if ( ! function_exists( __NAMESPACE__ . '\helix_register' ) ) {
	/**
	 * Registers a class.
	 *
	 * Each call to obtain an instance of this class made using the `tribe( $slug )` function
	 * will return a new instance; the instances are built just in time (if not passing an
	 * object instance, in that case it will work as a singleton) and on the first request.
	 * The container will call the class `__construct` method on the class (if not passing an object
	 * or a callback function) and will try to automagically resolve dependencies.
	 *
	 * Example use:
	 *
	 *      helix_register( My_Class::class, My_Class::class );
	 *
	 *      // some code later...
	 *
	 *      // class is built here
	 *      $some_one = helix( My_Class::class )->doSomething();
	 *
	 *      // $some_two !== $some_one
	 *      $some_two = helix( My_Class::class )->doSomething();
	 *
	 * Need the class built immediately? Build it and register it:
	 *
	 *      helix_register( My_Class::class, new My_Class() );
	 *
	 *      // some code later...
	 *
	 *      // $some_two === $some_one
	 *      // acts like a singleton
	 *      $some_one = helix( My_Class::class )->doSomething();
	 *      $some_two = helix( My_Class::class )->doSomething();
	 *
	 * Need a very custom way to build the class? Register a callback:
	 *
	 *      helix_register( My_Class::class, array( Helix__Some__Factory, 'make' ) );
	 *
	 *      // some code later...
	 *
	 *      // $some_two !== $some_one
	 *      $some_one = helix( My_Class::class )->doSomething();
	 *      $some_two = helix( My_Class::class )->doSomething();
	 *
	 * Or register the methods that should be called on the object after its construction:
	 *
	 *      helix_singleton( Helix__Admin__Class::class, 'Helix__Admin__Class', array( 'hook', 'register' ) );
	 *
	 *      // some code later...
	 *
	 *      // the `hook` and `register` methods will be called on the built instance.
	 *      helix( Helix__Admin__Class::class )->doSomething();
	 *
	 * @param string                 $slug                The human-readable and catchy name of the class.
	 * @param string|object|callable $class               The full class name or an instance of the class
	 *                                                    or a callback that will return the instance of the class.
	 * @param array                  $after_build_methods An array of methods that should be called on
	 *                                                    the built object after the `__construct` method; the methods
	 *                                                    will be called each time after the instance contstruction.
	 */
	function helix_register( $slug, $class, array $after_build_methods = null ) {
		Container::init()->bind( $slug, $class, $after_build_methods );
	}
}