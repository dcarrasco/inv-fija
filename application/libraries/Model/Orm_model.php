<?php

namespace Model;

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

use Collection;
use ArrayIterator;
use IteratorAggregate;

/**
 * Extension de clase CI_Model para usar con administracion de modelo
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Orm_model implements IteratorAggregate {

	use Model_has_form;
	use Model_uses_database;
	use Model_has_attributes;
	use Model_has_pagination;
	use Model_has_persistance;
	use Model_has_relationships;

	/**
	 * Nombre del modelo
	 *
	 * @var  string
	 */
	protected $model_nombre = '';

	/**
	 * Clase del modelo
	 *
	 * @var  string
	 */
	protected $model_class = '';

	/**
	 * Nombre (etiqueta) del modelo
	 *
	 * @var string
	 */
	protected $label = '';

	/**
	 * Nombre (etiqueta) del modelo (plural)
	 *
	 * @var string
	 */
	protected $label_plural = '';

	/**
	 * Campos para ordenar el modelo cuando se recupera de la BD
	 *
	 * @var string
	 */
	protected $order_by = '';

	/**
	 * Filtro para buscar resultados
	 *
	 * @var string
	 */
	protected $filtro = '';

	/**
	 * Caracter separador de los campos cuando la llave tiene más de un campo
	 *
	 * @var string
	 */
	protected $separador_campos = '~';

	/**
	 * Arreglo con los campos del modelo
	 *
	 * @var array
	 */
	protected $fields = [];

	/**
	 * Arreglo con la configuración del modelo
	 *
	 * @var array
	 */
	protected $model_config = [];


	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 * @param   mixed $id_modelo Identificador o arreglo de valores para inicializar el modelo
	 * @return  void
	 **/
	public function __construct($id_modelo = NULL)
	{
		$this->lang->load('orm');

		$this->model_class      = get_class($this);
		$this->model_nombre     = strtolower($this->model_class);

		$this->label = empty($this->label) ? $this->model_nombre : $this->label;
		$this->label_plural = empty($this->label_plural) ? $this->label .'s' : $this->label_plural;

		$this->fields = $this->config_fields($this->fields);

		$this->relation_objects = new Collection();

		$this->campo_id = $this->_determina_campo_id();

		if ($id_modelo)
		{
			$this->fill($id_modelo);
		}
	}

	// --------------------------------------------------------------------

	public static function __callStatic($metodo, $argumentos)
	{
		if ($metodo === 'new')
		{
			return new static;
		}
		else
		{
			throw new \LogicException("Unknown method '{$metodo}'");
		}
	}

	// --------------------------------------------------------------------
	// Iterator methods
	// --------------------------------------------------------------------

	/**
	 * Devuelve el iterador de array para recorrer los campos del modelo
	 * como si fuera un arreglo
	 *
	 * @return ArrayIterator Iterador de arreglo
	 */
	public function getIterator()
	{
		return new ArrayIterator($this->values);
	}

	// --------------------------------------------------------------------

	/**
	 * Crea una instancia del modelo y la devuelve
	 *
	 * @return static
	 */
	public static function create()
	{
		return new static;
	}

	// --------------------------------------------------------------------

	/**
	 * Configura las propiedades de los campos del modelo
	 *
	 * @param  array $arr_config arreglo con la configuración de los campos del modelo
	 * @return nada
	 */
	protected function config_fields($arr_fields = [])
	{
		return collect($arr_fields)->map(function($config_field, $nombre_campo) {
			return is_array($config_field)
				? new Orm_field(
					$nombre_campo,
					array_merge($config_field, ['tabla_bd' => $this->get_db_table()])
				)
				: $config_field;
		})->all();
	}




	// =======================================================================
	// SETTERS Y GETTERS
	// =======================================================================

	/**
	 * Recupera el nombre del modelo
	 *
	 * @param  boolean $full_name Indica si devuelve el nombre completo (con namespace)
	 * @return string             Nombre del modelo
	 */
	public function get_model_nombre($full_name = TRUE)
	{
		if ($full_name)
		{
			return $this->model_nombre;
		}

		$arr_model_nombre = explode('\\', $this->model_nombre);

		return $arr_model_nombre[count($arr_model_nombre) - 1];
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la etiqueta del modelo
	 *
	 * @return string Etiqueta del modelo
	 */
	public function get_label()
	{
		return $this->label;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera la etiqueta del modelo en plural
	 *
	 * @return string Etiqueta del modelo en plural
	 */
	public function get_label_plural()
	{
		return $this->label_plural;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los campos del modelo
	 *
	 * @return array Arreglo con los campos del modelo
	 */
	public function get_fields($filtrar_listado = FALSE)
	{
		if ($filtrar_listado)
		{
			return collect($this->fields)->filter(function($campo) {
					return $campo->get_mostrar_lista();
				})->all();
		}

		return $this->fields;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el valor del filtro a utilizar para recuperar datos del modelo
	 *
	 * @return string Filtro a usar
	 */
	public function get_filtro()
	{
		return ($this->filtro === '_') ? '' : $this->filtro;
	}

	// --------------------------------------------------------------------

	/**
	 * Fija el valor del filtro a utilizar para recuperar datos del modelo
	 *
	 * @param  string $filtro Filtro a usar
	 * @return void
	 */
	public function set_filtro($filtro = '')
	{
		$this->filtro = $filtro;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve indicador si el campo se mostrará o no en listado
	 *
	 * @param string $campo Nombre del campo
	 * @return boolean Indicador mostrar o no en la lista
	 */
	public function get_mostrar_lista($campo = '')
	{
		return (isset($this->fields[$campo])) ? $this->fields[$campo]->get_mostrar_lista() : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Puebla el modelo con datos de la base de datos o de un arreglo entregado
	 *
	 * @param  mixed $id_modelo ID del registro a recuperar o arreglo con datos
	 * @return void
	 */
	public function fill($id_modelo = NULL)
	{
		return is_array($id_modelo) ? $this->fill_from_array($id_modelo) : $this->find_id($id_modelo);
	}

}
/* End of file orm_model.php */
/* Location: ./application/libraries/orm_model.php */
