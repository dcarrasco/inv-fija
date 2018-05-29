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
class Google_map_test extends TestCase {

	public function test_initialize()
	{
		$this->assertInternalType('object', new \Google_map());
	}

	public function test_create_map_is_string()
	{
		$this->assertInternalType('string', (new \Google_map())->create_map());
		$this->assertContains("<script type=\"text/javascript\">", (new \Google_map())->create_map());
		$this->assertContains("map.fitBounds", (new \Google_map())->create_map());
	}

	public function test_add_marker()
	{
		$gmap = (new \Google_map())->add_marker(['lat' => 10, 'lng' => 20]);

		$this->assertContains("LatLng(10, 20)", $gmap->create_map());
	}

	public function test_add_peticiones_markers()
	{
		$peticiones = [['acoord_y' => 10, 'acoord_x' => 20, 'empresa' => '', 'tecnico' => '', 'referencia' => '']];
		$gmap = (new \Google_map())->add_peticiones_markers($peticiones);

		$this->assertContains("LatLng(10, 20)", $gmap->create_map());
	}

}
