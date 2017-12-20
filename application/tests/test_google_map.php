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
		$this->assert_is_object(new \Google_map());
	}

	public function test_create_map_is_string()
	{
		$this->assert_is_string((new \Google_map())->create_map());
		$this->assert_contains((new \Google_map())->create_map(), "<script type=\"text/javascript\">");
		$this->assert_contains((new \Google_map())->create_map(), "map.fitBounds");
	}

	public function test_add_marker()
	{
		$gmap = (new \Google_map())->add_marker(['lat' => 10, 'lng' => 20]);

		$this->assert_contains($gmap->create_map(), "LatLng(10, 20)");
	}

	public function test_add_peticiones_markers()
	{
		$peticiones = [['acoord_y' => 10, 'acoord_x' => 20, 'empresa' => '', 'tecnico' => '', 'referencia' => '']];
		$gmap = (new \Google_map())->add_peticiones_markers($peticiones);

		$this->assert_contains($gmap->create_map(), "LatLng(10, 20)");
	}

}
