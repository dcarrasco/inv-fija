<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
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
 */
class MY_Form_validation extends CI_Form_validation {

	function __construct()
	{
	    parent::__construct();
	}

	// --------------------------------------------------------------------

	function edit_unique($value, $params)
	{
		$CI =& get_instance();
		$CI->form_validation->set_message('edit_unique', 'El valor %s ya está en uso.');

		list($table, $column, $id_field, $current_id) = explode('.', $params);

		$query = $CI->db->select()->from($table)->where($column, $value)->limit(1)->get();

		if ($query->row() && $query->row()->{$id_field} != $current_id)
		{
			return FALSE;
		}
		else
		{
			return TRUE;
		}
	}

}