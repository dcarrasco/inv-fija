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
	 * Tabla de la BD donde se almacena el modelo
	 *
	 * @var  string
	 */
	protected $tabla = '';

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

	/**
	 * Arreglo con la clases CI para overload
	 *
	 * @var array
	 */
	protected $ci_object = [];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 * @param   mixed $id_modelo Identificador o arreglo de valores para inicializar el modelo
	 * @return  void
	 **/
	public function __construct($id_modelo = NULL, $ci_object = [])
	{
		$this->lang->load('orm');

		$this->model_class      = get_class($this);
		$this->model_nombre     = strtolower($this->model_class);
		$this->tabla            = $this->model_nombre;
		$this->label            = $this->model_nombre;
		$this->label_plural     = $this->label . 's';
		$this->relation_objects = new Collection();

		$this->config_model($this->model_config);

		$this->ci_object = $ci_object;

		if ($id_modelo)
		{
			$this->fill($id_modelo);
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
	public static function create($ci_object = [])
	{
		return new static(NULL, $ci_object);
	}

	// --------------------------------------------------------------------

	/**
	 * Configuración del modelo
	 * Define las propiedades basicas de un nuevo modelo
	 *
	 * @param   array $param Arreglo con los parametros a configurar
	 * @return  void
	 **/
	public function config_model($param = [])
	{
		$this->config_modelo(array_get($param, 'modelo', []));
		$this->config_campos(array_get($param, 'campos', []));

		$this->campo_id = $this->_determina_campo_id();
		//$this->get_relation_fields();
	}

	// --------------------------------------------------------------------

	/**
	 * Configura las propiedades del modelo
	 *
	 * @param  array $arr_config arreglo con la configuración del modelo
	 * @return nada
	 */
	protected function config_modelo($arr_config = [])
	{
		collect($arr_config)->each(function($value, $index) {
			if (isset($this->{$index}))
			{
				$this->{$index} = $value;
			}
		});
	}

	// --------------------------------------------------------------------

	/**
	 * Configura las propiedades de los campos del modelo
	 *
	 * @param  array $arr_config arreglo con la configuración de los campos del modelo
	 * @return nada
	 */
	protected function config_campos($arr_config = [])
	{
		collect($arr_config)->each(function($config_value, $campo) {
			$config_value['tabla_bd'] = $this->tabla;

			$this->fields[$campo] = new Orm_field($campo, $config_value);
			$this->values[$campo] = NULL;
		});
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
	 * Recupera el nombre de la tabla del modelo en la base de datos
	 *
	 * @return string Nombre de la tabla
	 */
	public function get_tabla()
	{
		return $this->tabla;
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
