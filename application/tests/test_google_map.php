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

		$this->test(new \Google_map(), $expected);
	}

	public function test_create_map_is_string()
	{
		$expected = 'is_string';

		$this->test((new \Google_map())->create_map(), $expected);
	}

}
