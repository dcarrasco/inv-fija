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
class mock_session {

	public $session_variables = [
	];

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
		$this->session_variables[$index] = $data;
		return $this;
	}

	// --------------------------------------------------------------------

	public function userdata($index)
	{
		return array_get($this->session_variables, $index, NULL);
	}

	// --------------------------------------------------------------------

	public function set_userdata($data)
	{
		return $this;
	}

}
