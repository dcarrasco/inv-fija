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
 * Clase Modelo Administrador de la base de datos
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Adminbd_model extends CI_Model {

	/**
	 * Objeto de base de datos con perfil administrador
	 *
	 * @var object
	 */
	private $_db_object;

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera arreglo con las queries en ejecución
	 *
	 * @return array Queries en ejecución
	 */
	public function get_running_queries()
	{
		$this->_db_object = $this->load->database('adminbd', TRUE);

		$arr_final = array();
		$arr_users = array();
		$arr_users_tmp = $this->_db_object
			->query('sp_who2 "active"')
			->result_array();

		foreach ($arr_users_tmp as $registro)
		{
			if (trim($registro['Login']) !== 'sa' AND trim($registro['Login']) !== '')
			{
				if (trim($registro['Login']) !== 'patripio' OR trim($registro['ProgramName']) !== 'Apache HTTP Server')
				{
					$arr_users[trim($registro['SPID'])] = $registro;
				}
			}
		}

		$arr_queries = array();
		$arr_queries_tmp = $this->_db_object
			->query('SELECT  * FROM sys.dm_exec_requests CROSS APPLY sys.dm_exec_sql_text(sql_handle)')
			->result_array();

		foreach ($arr_queries_tmp as $registro)
		{
			$arr_queries[trim($registro['session_id'])] = $registro;
		}

		foreach($arr_users as $llave => $valor)
		{
			$arr_final[$llave] = array_merge($arr_users[$llave], $arr_queries[$llave]);
		}

		return $arr_final;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera listado de tablas del sistema para exportar
	 *
	 * @return array Listado de tablas
	 */
	public function get_table_list()
	{
		$blacklist = array(
			'bd_usuarios',
			'bd_app',
			'bd_modulos',
			'bd_rol',
			'bd_usuario_rol',
			'bd_rol_modulo',
			'bd_captcha',
			'bd_pcookies',
		);

		$arr_list = array();
		foreach($this->config->config as $index => $value)
		{
			if (strpos($index, 'bd_') !== FALSE AND ! in_array($index, $blacklist))
			{
				$arr_list[urlencode($value)] = $index;
			}
		}
		asort($arr_list);

		return $arr_list;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera listado de campos de una tabla
	 *
	 * @param  string $tabla Nombre de la tabla
	 * @return array         Listado de campos
	 */
	public function get_fields_list($tabla = '')
	{
		if ( ! $this->db->table_exists($tabla))
		{
			return array();
		}

		$arr_list = array();
		foreach ($this->db->list_fields($tabla) as $campo)
		{
			$arr_list[$campo] = $campo;
		}

		return $arr_list;
	}


}
/* End of file adminbd_model.php */
/* Location: ./application/models/adminbd_model.php */