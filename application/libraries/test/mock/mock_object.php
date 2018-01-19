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
class mock_object {

	public $return_data = [];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
	}

	// --------------------------------------------------------------------

	/**
	 * Limpia el arreglo de datos
	 *
	 * @return array
	 */
	public function class_reset()
	{
		$this->return_data = [];
		return $this;
	}


}
