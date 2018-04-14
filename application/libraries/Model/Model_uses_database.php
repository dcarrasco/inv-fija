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

	protected $db_query = NULL;

	// --------------------------------------------------------------------

	protected function get_db_table($db_table = '')
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

	protected function get($recupera_relation = TRUE)
	{
		$this->select_from_database();
		$results = $this->db_query->get()->result_array();
		$this->db_query = NULL;

		if (count($results) === 1)
		{
			$this->fill_from_array($results[0]);

			if ($recupera_relation)
			{
				$this->get_relation_fields();
			}
		}

		if (count($results) > 1)
		{
			$class = get_class($this);

			return collect($results)->map(function($result) use ($class, $recupera_relation) {
				$obj_modelo = new $class($result);

				if ($recupera_relation)
				{
					$obj_modelo->get_relation_fields($this->relation_objects);
					$this->_add_relation_fields($obj_modelo);
				}

				return $obj_modelo;
			});
		}

		return $this;
	}

	// --------------------------------------------------------------------

	public function get_first($recupera_relation = TRUE)
	{
		return $this->limit(1)->get($recupera_relation);
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
