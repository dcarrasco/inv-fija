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

	protected $db_query = NULL;

	protected function select_from_database()
	{
		if ($this->db_query === NULL)
		{
			$this->db_query = $this->db->from($this->tabla);
		}

		return $this;
	}

	protected function get()
	{
		$this->select_from_database();
		$this->db_query = $this->db_query->get()->result_array();

		if (count($this->db_query) === 1)
		{
			$this->fill_from_array($this->db_query[0]);
		}

		if (count($this->db_query) > 1)
		{
			$class = get_class($this);

			return collect($this->db_query)->map(function($result) use ($class) {
				return new $class($result);
			});
		}

		$this->db_query = NULL;

		return $this;
	}

	public function where($key, $value = NULL, $escape = NULL)
	{
		$this->select_from_database();
		$this->db_query = $this->db_query->where($key, $value, $escape);

		return $this;
	}

}
/* End of file model_uses_database.php */
/* Location: ./application/libraries/model_uses_database.php */
