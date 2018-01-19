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
class mock_session extends mock_object {

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
	}

	// --------------------------------------------------------------------

	public function mock_set_userdata($index, $data)
	{
		$this->return_data[$index] = $data;
		return $this;
	}

	// --------------------------------------------------------------------

	public function userdata($index)
	{
		return array_get($this->return_data, $index, NULL);
	}

	// --------------------------------------------------------------------

	public function set_userdata($data)
	{
		return $this;
	}

}
