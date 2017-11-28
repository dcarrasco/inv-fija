<?php

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_google_map extends test_case {

	public function __construct()
	{
	    parent::__construct();
	}

	public function test_initialize()
	{
		$expected = 'is_object';

		$this->assert_equals(new \Google_map(), $expected);
	}

	public function test_create_map_is_string()
	{
		$this->assert_is_string((new \Google_map())->create_map());
	}

	public function test_create_map_contains_script_javascript()
	{
		$expected = 'is_int';

		$this->assert_equals(strpos((new \Google_map())->create_map(), "<script type=\"text/javascript\">"), 'is_int');
	}

	public function test_create_map_contains_map_fitbounds()
	{
		$expected = 'is_int';

		$this->assert_equals(strpos((new \Google_map())->create_map(), "map.fitBounds"), 'is_int');
	}

	public function test_add_marker()
	{
		$expected = 'is_int';

		$gmap = (new \Google_map())->add_marker(['lat' => 10, 'lng' => 20]);
		$this->assert_equals(strpos($gmap->create_map(), "LatLng(10, 20)"), 'is_int');
	}

	public function test_add_peticiones_markers()
	{
		$expected = 'is_int';

		$peticiones = [['acoord_y' => 10, 'acoord_x' => 20, 'empresa' => '', 'tecnico' => '', 'referencia' => '']];
		$gmap = (new \Google_map())->add_peticiones_markers($peticiones);
		$this->assert_equals(strpos($gmap->create_map(), "LatLng(10, 20)"), 'is_int');
	}

}
