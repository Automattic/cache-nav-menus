<?php
/**
 * Cache Nav Menus
 *
 * @package WordPress
 * @subpackage cache-nav-menu
 * @author Automattic
 * @license GPL-2.0+
 *
 * @wordpress-plugin
 * Plugin Name: Cache Nav Menus
 * Plugin URI: https://github.com/Automattic/cache-nav-menus
 * Description: Allows Core Nave Menus to be cached using WP.com's Advanced Post Cache
 * Author: Automattic
 * Version: 1.0
 */

/**
 * Filters `parse_query` to add necessary caching parameters.
 *
 * @param WP_Query $query The WP_Query object to filter.
 *
 * @return void
 */
function cache_nav_menu_parse_query( &$query ) {
	if ( is_admin() || ! isset( $query->query_vars['post_type'] ) || 'nav_menu_item' !== $query->query_vars['post_type'] ) {
		return;
	}

	$query->query_vars['suppress_filters'] = false;
	$query->query_vars['cache_results']    = true;
}
add_action( 'parse_query', 'cache_nav_menu_parse_query' );

/**
 * Wrapper function around `wp_nav_menu()`.
 *
 * This function can replace core's `wp_nav_menu()` to use output buffering and
 * persistent object caching to cache the output of complex and slow menus.
 *
 * @param array   $args        Standard menu arguments.
 * @param boolean $prime_cache Should the cache be primed, default false.
 *
 * @return mixed  $nav_menu Outputs if the echo argument is enabled, otherwise returns.
 */
function wpcom_vip_cached_nav_menu( $args = array(), $prime_cache = false ) {
	global $wp_query;

	$queried_object_id = empty( $wp_query->queried_object_id ) ? 0 : (int) $wp_query->queried_object_id;

	$nav_menu_key = md5( serialize( $args ) . '-' . $queried_object_id );
	$my_args      = wp_parse_args( $args );
	$my_args      = apply_filters( 'wp_nav_menu_args', $my_args );
	$my_args      = (object) $my_args;

	if ( ( isset( $my_args->echo ) && true === $my_args->echo ) || ! isset( $my_args->echo ) ) {
		$echo = true;
	} else {
		$echo = false;
	}

	$nav_menu = wp_cache_get( $nav_menu_key, 'cache-nav-menu' );
	if ( true === $prime_cache || false === $nav_menu ) {
		if ( false === $echo ) {
			$nav_menu = wp_nav_menu( $args );
		} else {
			ob_start();
			wp_nav_menu( $args );
			$nav_menu = ob_get_clean();
		}

		wp_cache_set( $nav_menu_key, $nav_menu, 'cache-nav-menu', MINUTE_IN_SECONDS * 15 );
	}
	if ( true === $echo ) {
		// We're trusting that the cache hasn't been modified and not escaping on output.
		echo $nav_menu; // WPCS: XSS ok.
	} else {
		return $nav_menu;
	}
}


/**
 * Retrieves a cached copy of nav menu objects.
 *
 * @param boolean $use_cache Should the cache be used, default true.
 *
 * @return mixed $object_ids An array of nav menu objects.
 */
function wpcom_vip_get_nav_menu_cache_objects( $use_cache = true ) {
	$cache_key  = 'wpcom_vip_nav_menu_cache_object_ids';
	$object_ids = wp_cache_get( $cache_key, 'cache-nav-menu' );
	if ( true === $use_cache && ! empty( $object_ids ) ) {
		return $object_ids;
	}

	$object_ids = array();
	$objects    = array();

	$menus = wp_get_nav_menus();
	foreach ( $menus as $menu_maybe ) {
		$menu_items = wp_get_nav_menu_items( $menu_maybe->term_id );
		if ( $menu_items ) {
			foreach ( $menu_items as $menu_item ) {
				if ( preg_match( '#.*/category/([^/]+)/?$#', $menu_item->url, $match ) ) {
					$objects['category'][] = $match[1];
				}
				if ( preg_match( '#.*/tag/([^/]+)/?$#', $menu_item->url, $match ) ) {
					$objects['post_tag'][] = $match[1];
				}
			}
		}
	}
	if ( ! empty( $objects ) ) {
		foreach ( $objects as $taxonomy => $term_names ) {
			foreach ( $term_names as $term_name ) {
				$term = get_term_by( 'slug', $term_name, $taxonomy );
				if ( $term ) {
					$object_ids[] = $term->term_id;
				}
			}
		}
	}

	$object_ids[] = 0; // that's for the homepage.

	wp_cache_set( $cache_key, $object_ids, 'cache-nav-menu' );
	return $object_ids;
}
