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

use Toa\Asignacion_toa;

class test_model_asignacion_toa extends test_case {

	public function __construct()
	{
	    parent::__construct();
	}

	public function test_asignaciones_toa_sin_parametros()
	{
		$expected = '';
		$asignacion = new Asignacion_toa;

		$this->test($asignacion->asignaciones_toa(), $expected);
	}

	public function test_asignaciones_toa()
	{
		$expected = 'is_string';
		$asignacion = new Asignacion_toa;

		$this->test($asignacion->asignaciones_toa('tecnicos', '20170901', '20170901'), $expected);
	}

}
