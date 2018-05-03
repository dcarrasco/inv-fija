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
trait Model_uses_database {

	/**
	 * Tabla de la BD donde se almacena el modelo
	 *
	 * @var  string
	 */
	protected $db_table = '';

	/**
	 * Objeto para mantener querys
	 *
	 * @var object
	 */
	protected $db_query = NULL;

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

	// --------------------------------------------------------------------

	public function get_order_by()
	{
		return $this->order_by;
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
	 * Recupera registros por el ID
	 *
	 * @param  string  $id_modelo         Identificador del registro a recuperar
	 * @param  boolean $recupera_relation Indica si se recuperará la información de relación
	 * @return void
	 */
	public function find_id($id_modelo = '', $recupera_relation = TRUE)
	{
		$modelo = $this->where($this->array_id($id_modelo))->get_first(FALSE);

		if ( ! is_null($modelo))
		{
			$this->fill($modelo->get_values());

			if ($recupera_relation)
			{
				$this->get_relation_fields();
			}
		}

		return $this;
	}

	// --------------------------------------------------------------------

	public function get_db_table($db_table = '')
	{
		$db_table = empty($db_table) ? $this->db_table : $db_table;

		return empty($db_table)
			? str_replace('\\', '_', $this->model_nombre)
			: (substr($db_table, 0, 8) === 'config::'
				? config(substr($db_table, 8, strlen($db_table)))
				: $db_table);
	}

	// --------------------------------------------------------------------

	protected function select_from_database()
	{
		if ($this->db_query === NULL)
		{
			$this->db_query = $this->db->from($this->get_db_table());
		}

		return $this;
	}

	// --------------------------------------------------------------------

	public function get($recupera_relation = TRUE)
	{
		if (is_null($this->db_query))
		{
			$this->select_from_database()->order_by($this->order_by);
		}
		else
		{
			$this->select_from_database();
		}

		$results = $this->db_query->get()->result_array();
		$this->db_query = NULL;

		$class = get_class($this);

		return collect($results)
			->map(function($result) use ($class, $recupera_relation) {
				$obj_modelo = new $class($result);

				if ($recupera_relation)
				{
					$obj_modelo->get_relation_fields($this->relation_objects);
					$this->_add_relation_fields($obj_modelo);
				}

				return $obj_modelo;
			});
	}

	// --------------------------------------------------------------------

	public function get_array()
	{
		$this->select_from_database();
		$results = $this->db_query->get()->result_array();
		$this->db_query = NULL;

		return collect($results);
	}

	// --------------------------------------------------------------------

	public function get_dropdown_list($opcion_inicial = TRUE)
	{
		$opc_ini = collect($opcion_inicial
			? ['' => 'Seleccione '.strtolower($this->label).'...']
			: []
		);

		$opciones = collect($this->get())->map_with_keys(function ($obj_modelo) {
			return [$obj_modelo->get_id() => (string) $obj_modelo];
		});

		return $opc_ini->merge($opciones)->all();
	}

	// --------------------------------------------------------------------

	public function with($campo)
	{
		$this->select_from_database();

		if ($this->get_field($campo)->get_tipo() === Orm_field::TIPO_HAS_ONE)
		{
			$related_model = array_get($this->get_field($campo)->get_relation(), 'model');
			$related_object = new $related_model;
			$related_table = $related_object->get_db_table();
			$related_id = collect($related_object->get_campo_id())->first();

			$this->db_query = $this->db_query
				->join($related_table, $campo.'='.$related_table.'.'.$related_id, 'left');
		}

		return $this;
	}

	// --------------------------------------------------------------------

	public function get_first($recupera_relation = TRUE)
	{
		return $this->get($recupera_relation)->first();
	}

	// --------------------------------------------------------------------

	public function where($key, $value = NULL, $escape = NULL)
	{
		$this->select_from_database();
		$this->db_query = $this->db_query->where($key, $value, $escape);

		return $this;
	}

	// --------------------------------------------------------------------

	public function where_in($key = NULL, $values = NULL, $escape = NULL)
	{
		$this->select_from_database();
		$this->db_query = $this->db_query->where_in($key, $values, $escape);

		return $this;
	}

	// --------------------------------------------------------------------

	public function count()
	{
		$this->select_from_database();

		$cantidad = $this->db_query
			->select('count(*) as cantidad', FALSE)
			->get()->row()
			->cantidad;

		$this->db_query = NULL;

		return $cantidad;
	}

	// --------------------------------------------------------------------

	public function limit($value, $offset = 0)
	{
		$this->select_from_database();
		$this->db_query = $this->db_query->limit($value, $offset);

		return $this;
	}

	// --------------------------------------------------------------------

	public function offset($offset)
	{
		$this->select_from_database();
		$this->db_query = $this->db_query->offset($offset);

		return $this;
	}

	// --------------------------------------------------------------------

	public function order_by($orderby, $direction = '', $escape = NULL)
	{
		$this->select_from_database();
		$this->db_query = $this->db_query->order_by($orderby, $direction, $escape);

		return $this;
	}

	// --------------------------------------------------------------------

	protected function filter_by_array($filtro = '')
	{
		return collect($this->fields)
			->filter(function ($field) {
				return $field->get_tipo() === Orm_field::TIPO_CHAR;
			})
			->map(function($elem) use ($filtro) {
				return $filtro;
			});
	}

	// --------------------------------------------------------------------

	public function filter_by($filtro = '')
	{
		if ( ! empty($filtro))
		{
			$filtros = $this->filter_by_array($filtro);

			if ($filtros->count() > 0)
			{
				$this->select_from_database();
				$this->db_query = $this->db_query->or_like($filtros->all(), $filtro, 'both');
			}
		}

		return $this;
	}

}
/* End of file model_uses_database.php */
/* Location: ./application/libraries/model_uses_database.php */
