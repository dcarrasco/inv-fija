<?php

namespace test;

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
trait test_has_assertions {

	// --------------------------------------------------------------------

	/**
	 * Testea si 2 valores son iguales
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @param  mixed $result Resultado esperado
	 * @return mixed
	 */
	public function assert_equals($test, $result)
	{
		return $this->test($test, $result, 'assert_equals');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es string
	 *
	 * @param  mixed $test Test a ejecutar
	 * @return mixed
	 */
	public function assert_is_string($test)
	{
		return $this->test($test, 'is_string', 'assert_is_string');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es objecto
	 *
	 * @param  mixed $test Test a ejecutar
	 * @return mixed
	 */
	public function assert_is_object($test)
	{
		return $this->test($test, 'is_object', 'assert_is_object');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es un entero
	 *
	 * @param  mixed $test Test a ejecutar
	 * @return mixed
	 */
	public function assert_is_int($test)
	{
		return $this->test($test, 'is_int', 'assert_is_int');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es true
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @return mixed
	 */
	public function assert_true($test)
	{
		return $this->test($test, TRUE, 'assert_true');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es false
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @return mixed
	 */
	public function assert_false($test)
	{
		return $this->test($test, FALSE, 'assert_false');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es NULL
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @return mixed
	 */
	public function assert_null($test)
	{
		return $this->test($test, NULL, 'assert_null');
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor contiene otro
	 *
	 * @param  string $haystack Texto en el cual se ejecuta la busqueda
	 * @param  string $needle   Texto a buscar
	 * @return mixed
	 */
	public function assert_contains($haystack = '', $needle = '')
	{
		$strpos = strpos($haystack, $needle);
		$test = ($strpos !== FALSE) && is_int($strpos);

		return $this->test($test, TRUE, 'assert_contains',
			['contains_haystack' => $haystack, 'contains_needle' => $needle]
		);
	}



}
