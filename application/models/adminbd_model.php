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

		$this->_db_object = $this->load->database('adminbd', TRUE);

	}

	// --------------------------------------------------------------------

	/**
	 * Recupera arreglo con las queries en ejecución
	 *
	 * @return array Queries en ejecución
	 */
	public function get_running_queries()
	{
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


}
/* End of file adminbd_model.php */
/* Location: ./application/models/adminbd_model.php */