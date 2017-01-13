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
	private $_items = array();

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * @param array $items Arreglo inicial para poblar la colección
	 * @return  void
	 **/
	public function __construct($items = NULL)
	{
		$this->get_array($items);
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
	public function get_array($items = array())
	{
		if (is_array($items))
		{
			$this->_items = $items;
		}
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
			if ( ! $this->key_exists($llave))
			{
				$this->_items[$llave] = $item;
			}
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
		return array_keys($this->_items);
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
	 * Ejecuta una funcion sobre cada uno de los elementos de la coleccion
	 *
	 * @param  callable $f Funcion a ejecutar en cada elemento
	 * @return Collection  Colección con el resultado
	 */
	function map($f)
	{
		return new static(array_map($f, $this->_items));
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



}
/* End of file collection.php */
/* Location: ./application/libraries/collection.php */