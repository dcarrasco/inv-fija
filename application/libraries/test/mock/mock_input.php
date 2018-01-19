<?php

namespace test\mock;

/**
 * Clase de testeo
 *
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class mock_input {

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
	}

	// --------------------------------------------------------------------

	public function mock_set_input_variable($index, $data)
	{
		$this->return_data[$index] = $data;
		return $this;
	}

	// --------------------------------------------------------------------

	public function cookie($index)
	{
		return array_get($this->return_data, $index, NULL);
	}

	// --------------------------------------------------------------------

	public function set_cookie($index)
	{
		return;
	}

	// --------------------------------------------------------------------

	public function ip_address()
	{
		return '';
	}

	// --------------------------------------------------------------------

	public function user_agent()
	{
		return '';
	}

	// --------------------------------------------------------------------

	public function method()
	{
		return '';
	}

}
