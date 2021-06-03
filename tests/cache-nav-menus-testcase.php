<?php

/**
 * Base unit test class for Cache Nav Menus
 */
class CacheNavMenus_TestCase extends WP_UnitTestCase {
	public function setUp() {
		parent::setUp();

		global $cache_nav_menus;
		$this->_toc = $cache_nav_menus;
	}
}
