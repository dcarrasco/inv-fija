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
trait has_assertions {

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
		$result_type   = gettype($result);
		$string_test   = $this->to_string($test);
		$string_result = $this->to_string($result);

		switch ($result_type)
		{
			case 'array':
			case 'object':
			case 'string':
				$error_message = "Failed asserting that two {$result_type}s are equal.\n"
					."  Test    : {$string_test}\n"
					."  Expected: {$string_result}\n";
				break;

			case 'integer':
				$error_message = "Failed asserting that {$string_test} matches expected {$string_result}.\n";
				break;

			case 'boolean':
			case 'NULL':
				$error_message = "Failed asserting that {$string_test} is {$string_result}.\n";
				break;
		}

		return $this->test($test, $result, 'assert_equals', compact('error_message'));
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
		return $this->test($test, 'is_string', 'assert_is_string', [
			'error_message' => 'Failed asserting that test type '.gettype($test)." equals result type string\n",
		]);
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
		return $this->test($test, 'is_object', 'assert_is_object', [
			'error_message' => 'Failed asserting that test type '.gettype($test)." equals result type object\n",
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es arreglo
	 *
	 * @param  mixed $test Test a ejecutar
	 * @return mixed
	 */
	public function assert_is_array($test)
	{
		return $this->test($test, 'is_array', 'assert_is_array', [
			'error_message' => 'Failed asserting that test type '.gettype($test)." equals result type array\n",
		]);
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
		return $this->test($test, 'is_int', 'assert_is_int', [
			'error_message' => 'Failed asserting that test type '.gettype($test)." equals result type integer\n",
		]);
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
		return $this->test($test, TRUE, 'assert_true', [
			'error_message' => 'Failed asserting that '.$this->to_string($test)." is true.\n"
		]);
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
		return $this->test($test, FALSE, 'assert_false', [
			'error_message' => 'Failed asserting that '.$this->to_string($test)." is false.\n"
		]);
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
		return $this->test($test, NULL, 'assert_null', [
			'error_message' => 'Failed asserting that '.$this->to_string($test)." is NULL.\n"
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es vacio
	 *
	 * @param  mixed $test   Test a ejecutar
	 * @return mixed
	 */
	public function assert_empty($test)
	{
		return $this->test(empty($test), TRUE, 'assert_empty', [
			'test' => $test,
			'error_message' => 'Failed asserting that '.gettype($test)." '".$this->to_string($test)."' is empty.\n"
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor no es vacio
	 *
	 * @param  mixed $test Test a ejecutar
	 * @return mixed
	 */
	public function assert_not_empty($test)
	{
		return $this->test(empty($test), FALSE, 'assert_not_empty', [
			'test' => $test,
			'error_message' => 'Failed asserting that '.gettype($test)." '".$this->to_string($test)."' is not empty.\n"
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Testea si un valor es vacio
	 *
	 * @param  mixed $item    Test a ejecutar
	 * @param  mixed $result  Cantidad de elementos
	 * @return mixed
	 */
	public function assert_count($item, $result)
	{
		return $this->test(count($item), $result, 'assert_count', [
			'test' => $item,
			'error_message' => 'Failed asserting that '.gettype($item).' size '.count($item)." matches expected size {$result}.\n",
		]);
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

		return $this->test($test, TRUE, 'assert_contains', [
			'contains_haystack' => $haystack,
			'contains_needle'   => $needle,
			'error_message'     => "Failed asserting that '".$this->to_string($haystack)."' contains '".$this->to_string($needle)."'.\n",
		]);
	}

	// --------------------------------------------------------------------

	public function to_string($value)
	{
		$type = gettype($value);

		switch ($type)
		{
			case 'integer':
				return $value;
				break;

			case 'string':
				return str_replace("\n", "\\n", $value);
				break;

			case 'array':
			case 'object':
				return str_replace('\\u0000', '', json_encode($value));
				break;

			case 'boolean':
				return $value ? 'true' : 'false';
				break;

			case 'NULL':
				return 'NULL';
				break;
		}
	}


}
