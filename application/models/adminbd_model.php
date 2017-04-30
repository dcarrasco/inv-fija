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

	/**
	 * Reglas de validación formulario exportar tablas
	 *
	 * @var array
	 */
	public $validation_exportar_tablas = array(
		array(
			'field' => 'tabla',
			'label' => 'Tabla',
			'rules' => 'required',
		),
	);

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
				if ( ! (trim($registro['Login']) === 'patripio' AND trim($registro['ProgramName']) === 'PHP freetds'))
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
		$base_datos = '';

		if (strpos($tabla, '..') !== FALSE)
		{
			list($base_datos, $tabla) = explode('..', $tabla);
			$this->db->query('use '.$base_datos);
		}

		if ( ! $this->db->table_exists($tabla))
		{
			return array();
		}

		$arr_list = array();
		foreach ($this->db->list_fields($tabla) as $campo)
		{
			$arr_list[$campo] = $campo;
		}

		$this->db->query('use '.$this->db->database);

		return $arr_list;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el tamaño de las tablas de una BD
	 *
	 * @param  string $tabla Nombre de la base de datos
	 * @return array         Listado de tablas y tamaños
	 */
	public function get_tables_sizes($base_datos = 'bd_logistica')
	{
		$this->_db_object = $this->load->database('adminbd', TRUE);

		$this->_db_object->query('use '.$base_datos);

		$sql_tables_sizes = "SELECT
    t.NAME AS TableName,
    s.Name AS SchemaName,
    p.rows AS RowCounts,
    SUM(a.total_pages) * 8 AS TotalSpaceKB,
    SUM(a.used_pages) * 8 AS UsedSpaceKB,
    (SUM(a.total_pages) - SUM(a.used_pages)) * 8 AS UnusedSpaceKB
FROM
    sys.tables t
INNER JOIN
    sys.indexes i ON t.OBJECT_ID = i.object_id
INNER JOIN
    sys.partitions p ON i.object_id = p.OBJECT_ID AND i.index_id = p.index_id
INNER JOIN
    sys.allocation_units a ON p.partition_id = a.container_id
LEFT OUTER JOIN
    sys.schemas s ON t.schema_id = s.schema_id
WHERE
    t.NAME NOT LIKE 'dt%'
    AND t.is_ms_shipped = 0
    AND i.OBJECT_ID > 255
GROUP BY
    t.Name, s.Name, p.Rows
ORDER BY
    t.Name";

	$result = $this->_db_object->query($sql_tables_sizes)->result_array();

	$this->db->query('use '.$this->db->database);

	return $result;
	}


}
/* End of file adminbd_model.php */
/* Location: ./application/models/adminbd_model.php */
