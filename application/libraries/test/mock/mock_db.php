<?php

namespace test\mock;

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
class mock_db {

	public $return_result = [];

	// --------------------------------------------------------------------

	/**
	 * Constructor
	 *
	 */
	public function __construct()
	{
	}

	// --------------------------------------------------------------------

	public function mock_set_return_result($data)
	{
		$this->return_result = $data;
		return $this;
	}

	// --------------------------------------------------------------------

	public function get_where()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function distinct()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function limit()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function select()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function select_max()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function from()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function join()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function order_by()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function delete()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function insert()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function update()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function where()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function where_in()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function like()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function get()
	{
		return $this;
	}

	// --------------------------------------------------------------------

	public function get_compiled_select()
	{
		return '';
	}

	// --------------------------------------------------------------------

	public function count_all()
	{
		return count($this->return_result);
	}

	// --------------------------------------------------------------------

	public function count_all_results()
	{
		return count($this->return_result);
	}

	// --------------------------------------------------------------------

	public function num_rows()
	{
		return count($this->return_result);
	}

	// --------------------------------------------------------------------

	public function query()
	{
		return $this->return_result;
	}

	// --------------------------------------------------------------------

	public function row()
	{
		return (object) $this->return_result;
	}

	// --------------------------------------------------------------------

	public function row_array()
	{
		return $this->return_result;
	}

	// --------------------------------------------------------------------

	public function result_array()
	{
		return $this->return_result;
	}

}
