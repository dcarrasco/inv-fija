<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Clase que modela una collección
 *
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class Collection implements IteratorAggregate {

	/**
	 * Arreglo que contiene los datos de la collección
	 *
	 * @var array
	 */
	private $_items = [];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param array $items Arreglo inicial para poblar la colección
	 * @return  void
	 **/
	public function __construct($items = NULL)
	{
		$this->make($items);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del objeto
	 * @return string
	 */
	public function __toString()
	{
		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Inicializa la colección con un arreglo
	 *
	 * @param array $items Arreglo inicial para poblar la colección
	 * @return  void
	 **/
	public function make($items = [])
	{
		if (is_null($items))
		{
			$this->_items = [];
		}
		else if ($items instanceof Collection)
		{
			$this->_items = $items->all();
		}
		else if (is_array($items))
		{
			$this->_items = $items;
		}
		else
		{
			$this->_items = [$items];
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el iterador de array para recorrer los campos del modelo
	 * como si fuera un arreglo
	 *
	 * @return ArrayIterator Iterador de arreglo
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->_items);
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega un item a la colección
	 *
	 * @param  mixed $item  Item a agregar
	 * @param  mixed $llave Llave de acceso del item
	 * @return void
	 */
	public function add_item($item = NULL, $llave = NULL)
	{
		if ($llave === NULL)
		{
			array_push($this->_items, $item);
		}
		else
		{

			$this->_items[$llave] = $item;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Borra un item
	 *
	 * @param  mixed $llave ID o llave del item a borrar
	 * @return void
	 */
	public function delete_item($llave = NULL)
	{
		if ($this->key_exists($llave))
		{
			unset($this->_items[$llave]);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve un item de la colección
	 *
	 * @param  mixed $llave ID o llave para recuperar el item
	 * @return mixed        Item de la coleccion
	 */
	public function item($llave)
	{
		if ($this->key_exists($llave))
		{
			return $this->_items[$llave];
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el conjunto de ID o llaves de la coleccion
	 *
	 * @return array IDs o llaves de la coleccion
	 */
	public function keys()
	{
		return new static(array_keys($this->_items));
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la cantidad de itemes de la coleccion
	 *
	 * @return integer Cantidad de itemes
	 */
	public function length()
	{
		return count($this->_items);
	}

	// --------------------------------------------------------------------

	/**
	 * Indica si existe o no un ID o llave en la colección
	 *
	 * @param  mixed $llave ID o llave a buscar
	 * @return boolean        Indica si existe o no el ID o llave
	 */
	public function key_exists($llave)
	{
		return isset($this->_items[$llave]);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los elementos de la coleccion como un arreglo
	 *
	 * @return array Elementos de la coleccion
	 */
	public function all()
	{
		return $this->_items;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve un elementos de la coleccion por la llave
	 *
	 * @param  mixed $id_elem Llave el elemento a buscar
	 * @param  mixed $default Valor a devolver en caso de no encontrar la llave
	 * @return array Elementos de la coleccion
	 */
	public function get($id_elem, $default = NULL)
	{
		return isset($this->_items[$id_elem]) ? $this->_items[$id_elem] : $default;
	}

	// --------------------------------------------------------------------

	/**
	 * Ejecuta una funcion sobre cada uno de los elementos de la coleccion
	 *
	 * @param  callable $callback_function Funcion a ejecutar en cada elemento
	 * @return Collection                  Colección con el resultado
	 */
	public function map($callback_function)
	{
		$arr_keys  = array_keys($this->_items);
		$arr_items = array_map($callback_function, $this->_items, $arr_keys);

		return new static(array_combine($arr_keys, $arr_items));
	}

	// --------------------------------------------------------------------

	/**
	 * Determina si la colleción está vacía
	 *
	 * @return boolean Indica si la collección está vacía
	 */
	public function is_empty()
	{
		return ! $this->_items OR count($this->_items) === 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Determina si un indice existe en los elementos de la colección
	 *
	 * @param  string $indice Indice a buscar
	 * @return boolean        Indicador de existencia del indice
	 */
	public function has($indice = '')
	{
		if (is_array($indice))
		{
			$has_indices = FALSE;
			foreach ($indice as $indice_string)
			{
				if (array_key_exists($indice_string, $this->_items))
				{
					return TRUE;
				}
			}
			return FALSE;
		}

		return array_key_exists($indice, $this->_items);
	}

	// --------------------------------------------------------------------

	/**
	 * Transforma una colección en un string
	 *
	 * @param  string $glue String para concatenar los elementos de la coleccion
	 * @return string       String concatenado
	 */
	function implode($glue = '')
	{
		return implode($glue, $this->_items);
	}

	// --------------------------------------------------------------------

	/**
	* Ejecuta un filtro en cada uno de los valores de la coleccion.
	*
	* @param  callable $callback Funcion de filtrado
	* @return static
	*/
	public function filter($callback = NULL)
	{
		if ($callback)
		{
			return new static(array_filter($this->_items, $callback));
		}

		return new static(array_filter($this->_items));
	}

	// --------------------------------------------------------------------

	/**
	* Reduce la coleccion a un unico valor.
	*
	* @param  callable $callback Funcion de reduccion
	* @param  mixed    $initial  Valor inicial
	* @return mixed
	*/
	public function reduce($callback, $initial = NULL)
	{
		return array_reduce($this->_items, $callback, $initial);
	}

	// --------------------------------------------------------------------

	/**
	* Suma la coleccion.
	*
	* @param  string  $campo         Campo a sumar
	* @param  integer $valor_inicial Valor inicial
	* @return mixed
	*/
	public function sum($campo = NULL, $valor_inicial = 0)
	{
		return $this->reduce(function($total, $elem) use ($campo) {
			return $total + (is_null($campo) ? $elem : $elem[$campo]);
		}, $valor_inicial);
	}

	// --------------------------------------------------------------------

	public function concat($callback)
	{
		if (is_string($callback))
		{
			$callback = function($elem) {return $elem[$callback];};
		}

		if (is_callable($callback))
		{
			return $this->reduce(function($carry, $elem) use ($callback) {
				return $carry.$callback($elem);
			}, '');
		}

	return;
	}

	// --------------------------------------------------------------------

	/**
	* Ordena la coleccion.
	*
	* @param  callable $callback Funcion de ordenamiento
	* @return mixed
	*/
	public function sort($callback = null)
	{
		$callback ? uasort($this->_items, $callback) : asort($this->_items);

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	* Flatten la coleccion a sólo un nivel
	*
	* @param  integer $profundidad Indica cuantos niveles se hara flatten
	* @return mixed
	*/
	public function flatten($profundidad = INF)
	{
		return collect(array_reduce($this->_items, function($result, $item) use ($profundidad) {
			$item = $item instanceof Collection ? $item->all() : $item;

			if ( ! is_array($item))
			{
				return array_merge($result, [$item]);
			}
			elseif ($profundidad === 1)
			{
				return array_merge($result, array_values($item));
			}
			else
			{
				return array_merge($result, collect($item)->flatten($profundidad - 1)->all());
			}
		}, []));
	}

	// --------------------------------------------------------------------

	/**
	* Junta elementos de la coleccion con nuevos items
	*
	* @param  mixed $items Items a juntar a la coleccion actual
	* @return mixed
	*/
	public function merge($items)
	{
		$items = $items instanceof Collection ? $items->all() : $items;
		$items = is_array($items) ? $items : [$items];

		reset($items);
		// si la primera llave del arreglo es 0, usamos arrat merge
		if (key($items) === 0)
		{
			$this->_items = array_merge($this->_items, $items);
			return $this;
		}
		else
		{
			$orig = new Collection($this->_items);
			collect($items)->each(function ($value, $index) use (&$orig) {
					$orig->add_item($value, $index);
				});

			return $orig;
		}
	}

	// --------------------------------------------------------------------

	/**
	* Junta elementos de la coleccion con nuevos items
	*
	* @param  mixed $items Items a juntar a la coleccion actual
	* @return mixed
	*/
	public function only($items = [])
	{
		return new static(array_intersect_key($this->_items, array_flip((array) $items)));
	}

	// --------------------------------------------------------------------

	/**
	 * Ejecuta un callback sobre cada item
	 *
	 * @param  callable  $callback
	 * @return $this
	 */
	public function each(callable $callback)
	{
		foreach ($this->_items as $key => $item)
		{
			if ($callback($item, $key) === false)
			{
				break;
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Combina la llave de los elementos de la colección
	 * con los valores de un arreglo
	 *
	 * @param  array $valores Arreglo de valores
	 * @return static
	 */
	public function combine($valores = [])
	{
		return new static(array_combine($this->_items, $valores));
	}

	// --------------------------------------------------------------------

	/**
	 * Run an associative map over each of the items.
	 *
	 * The callback should return an associative array with a single key/value pair.
	 *
	 * @param  callable  $callback
	 * @return static
	 */
	public function map_with_keys(callable $callback)
	{
		$result = [];

		foreach ($this->_items as $key => $value)
		{
			$assoc = $callback($value, $key);

			foreach ($assoc as $mapKey => $mapValue)
			{
				$result[$mapKey] = $mapValue;
			}
		}

		return new static($result);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve elementos unicos de la colección
	 * @return static
	 */
	public function unique()
	{
		return new static(array_unique($this->_items, SORT_REGULAR));
	}

	// --------------------------------------------------------------------

	/**
	* Return the first element in an array passing a given truth test.
	*
	* @param  array  $array
	* @param  callable|null  $callback
	* @param  mixed  $default
	* @return mixed
	*/
	public function first(callable $callback = null, $default = null)
	{
		if (is_null($callback))
		{
			if (empty($this->_items))
			{
				return $default;
			}

			foreach ($this->_items as $item)
			{
				return $item;
			}
		}

		foreach ($this->_items as $key => $value)
		{
			if (call_user_func($callback, $value, $key))
			{
				return $value;
			}
		}

		return $default;
	}

}
/* End of file collection.php */
/* Location: ./application/libraries/collection.php */
