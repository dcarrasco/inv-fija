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
	protected $items = [];

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
			$this->items = [];
		}
		else if ($items instanceof Collection)
		{
			$this->items = $items->all();
		}
		else if (is_array($items))
		{
			$this->items = $items;
		}
		else
		{
			$this->items = [$items];
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
		return new ArrayIterator($this->items);
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
			array_push($this->items, $item);
		}
		else
		{

			$this->items[$llave] = $item;
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
			unset($this->items[$llave]);
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
			return $this->items[$llave];
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
		return new static(array_keys($this->items));
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la cantidad de itemes de la coleccion
	 *
	 * @return integer Cantidad de itemes
	 */
	public function length()
	{
		return $this->count();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la cantidad de itemes de la coleccion
	 *
	 * @return integer Cantidad de itemes
	 */
	public function count()
	{
		return count($this->items);
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
		return isset($this->items[$llave]);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los elementos de la coleccion como un arreglo
	 *
	 * @return array Elementos de la coleccion
	 */
	public function all()
	{
		return $this->items;
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
		return isset($this->items[$id_elem]) ? $this->items[$id_elem] : $default;
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
		$arr_keys  = array_keys($this->items);
		$arr_items = array_map($callback_function, $this->items, $arr_keys);

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
		return ! $this->items OR count($this->items) === 0;
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
				if (array_key_exists($indice_string, $this->items))
				{
					return TRUE;
				}
			}
			return FALSE;
		}

		return array_key_exists($indice, $this->items);
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
		return implode($glue, $this->items);
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
			return new static(array_filter($this->items, $callback));
		}

		return new static(array_filter($this->items));
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
		return array_reduce($this->items, $callback, $initial);
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
			if (is_null($campo))
			{
				$valor_campo = $elem;
			}
			else
			{
				$elem = is_array($elem) ? $elem : (array)$elem;
				$valor_campo = array_get($elem, $campo);
			}

			return $total + $valor_campo;
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
		$callback ? uasort($this->items, $callback) : asort($this->items);

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
		return collect(array_reduce($this->items, function($result, $item) use ($profundidad) {
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
			$this->items = array_merge($this->items, $items);
			return $this;
		}
		else
		{
			$orig = new Collection($this->items);
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
		return new static(array_intersect_key($this->items, array_flip((array) $items)));
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
		foreach ($this->items as $key => $item)
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
		return new static(array_combine($this->items, $valores));
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

		foreach ($this->items as $key => $value)
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
		return new static(array_unique($this->items, SORT_REGULAR));
	}

	// --------------------------------------------------------------------

	public function pluck($value, $key = NULL)
	{
		$results = [];

		foreach ($this->items as $item)
		{
			$itemValue = array_get($item, $value);

			// If the key is "null", we will just append the value to the array and keep
			// looping. Otherwise we will key the array using the value of the key we
			// received from the developer. Then we'll return the final array form.
			if (is_null($key))
			{
				$results[] = $itemValue;
			}
			else
			{
				$itemKey = array_get($item, $key);

				$results[$itemKey] = $itemValue;
			}
		}

		return new static($results);
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
			if (empty($this->items))
			{
				return $default;
			}

			foreach ($this->items as $item)
			{
				return $item;
			}
		}

		foreach ($this->items as $key => $value)
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
