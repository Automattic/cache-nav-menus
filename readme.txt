=== Cache Nav Menus ===
Contributors: batmoo, mdawaffe, mdbitz, nbachiyski, nickdaugherty
Tags: cache, caching, menu, nav, navigation
Requires at least: 3.2
Tested up to: 4.8.2
Requires PHP: 5.6
Stable tag: 4.8.2
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
 
Improves nav menu queries and provides a replacement for core's `wp_nav_menu()` that make nav menus more performant.
 
== Description ==
 
This plugin improves the performance of navigation menus within WordPress via two methods.

First, it intercepts the nav menu queries and ensures they are cached by WP_Query.

Secondly, it provides a replacement for `wp_nav_menu()` that wraps the core function, caching the output efficiently to reduce the number of queries performed on a site.
 
== Installation ==
 
Install as with any other plugin for an immediate benefit.

To get the full benefit, replace occurences of `wp_nav_menu()` in any custom code with `wpcom_vip_cached_nav_menu()`.

