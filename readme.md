[![Run PHPUnit and PHPCS](https://github.com/Automattic/cache-nav-menus/actions/workflows/integrate.yml/badge.svg)](https://github.com/Automattic/cache-nav-menus/actions/workflows/integrate.yml)

# Cache Nav Menus
 
Improves nav menu queries and provides a replacement for core's `wp_nav_menu()` that make nav menus more performant.
 
This plugin improves the performance of navigation menus within WordPress via two methods.

First, it intercepts the nav menu queries and ensures they are cached by WP_Query.

Secondly, it provides a replacement for `wp_nav_menu()` that wraps the core function, caching the output efficiently to reduce the number of queries performed on a site.
 
## Installation
 
Install as with any other plugin for an immediate benefit.

To get the full benefit, replace occurences of `wp_nav_menu()` in any custom code with `wpcom_vip_cached_nav_menu()`.

