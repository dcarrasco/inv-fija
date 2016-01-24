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
 * MY_Paser Class
 *
 * Extends Parser library
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class MY_Parser extends CI_Parser {

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
	 * Parse a template
	 *
	 * Parses pseudo-variables contained in the specified template view,
	 * replacing them with the data in the second param
	 *
	 * @param	string
	 * @param	array
	 * @param	bool
	 * @return	string
	 */
	public function parse($template, $data, $return = FALSE)
	{
		$template = $this->CI->load->view($template, $data, TRUE);

		$template = $this->_parse_lang($template);

		return $this->_parse($template, $data, $return);
	}

	// --------------------------------------------------------------------

	/**
	 * Parse un template y reemplaza las variables definidas en archivo lang
	 *
	 * Las variables a reemplazar deben estar entre "_". Ejemplo:
	 * {_variable_}
	 *
	 * @param  string $template Template a reemplazar las variables
	 * @return string           Template con variables reemplazadas
	 */
	public function _parse_lang($template)
	{
		$lang_delim = '_';

		$ci =& get_instance();

		$l_delim = $this->l_delim.$lang_delim;
		$r_delim = $lang_delim.$this->r_delim;

		$reg_ex = '/'.preg_quote($l_delim).'([a-zA-Z0-9_\/]*)'.preg_quote($r_delim).'/';
		preg_match_all($reg_ex, $template, $matches);

		$arr_replace = array();
		foreach ($matches[1] as $value)
		{
			if ($ci->lang->line($value) !== FALSE)
			{
				$arr_replace[$l_delim.$value.$r_delim] = $ci->lang->line($value);
			}
		}

		$template = strtr($template, $arr_replace);

		return $template;
	}



}
/* End of file MY_parser.php */
/* Location: ./application/libraries/MY_parser.php */