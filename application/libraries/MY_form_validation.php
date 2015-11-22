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
 * MY_Form_validation Class
 *
 * Extends Form_Validation library
 *
 * Adds one validation rule, "unique" and accepts a
 * parameter, the name of the table and column that
 * you are checking, specified in the forum table.column
 *
 * Note that this update should be used with the
 * form_validation library introduced in CI 1.7.0
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class MY_Form_validation extends CI_Form_validation {

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Validación de edición de registros únicos
	 *
	 * @param   string $value  Valor a validar
	 * @param   string $params Paramtros de validación
	 *
	 * @return  [type]           [description]
	 */
	function edit_unique($value, $params)
	{
		$ci =& get_instance();
		$ci->form_validation->set_message('edit_unique', 'El valor %s ya está en uso.');

		list($table, $column, $id_field, $current_id) = explode('.', $params);

		// si el id es numerico, cambia a formato numérico
		$current_id = is_numeric($current_id) ? (int) $current_id : $current_id;

		$query = $ci->db->select()->from($table)->where($column, $value)->limit(1)->get();

		if ($query->row() AND $query->row()->{$id_field} !== $current_id)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Valida que la password ingresada tenga a lo menos:
	 * un digito numerico, un caracter minuscula y un caracter mayuscula
	 *
	 * @param   string $password Password a validar
	 * @return  boolean          Indica si la password cumple el formato a validar
	 */
	public function password_validation($password = '')
	{
		if (preg_match('#[0-9]#', $password) AND preg_match('#[a-z]#', $password) AND preg_match('#[A-Z]#', $password))
		{
			return TRUE;
		}

		return FALSE;
	}


}
/* End of file MY_form_validation.php */
/* Location: ./application/libraries/MY_form_validation.php */