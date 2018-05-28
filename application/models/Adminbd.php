<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */

use Model\Orm_model;

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
class Adminbd extends ORM_Model {

	/**
	 * Objeto de base de datos con perfil administrador
	 *
	 * @var object
	 */
	protected $dbo_admin;

	/**
	 * Reglas de validación formulario exportar tablas
	 *
	 * @var array
	 */
	public $rules_exportar_tablas = [
		[
			'field' => 'tabla',
			'label' => 'Tabla',
			'rules' => 'required',
		],
	];

	/**
	 * Tablas que no seran recuperadas para exportar
	 *
	 * @var array
	 */
	protected $table_blacklist = [
			'bd_usuarios',
			'bd_app',
			'bd_modulos',
			'bd_rol',
			'bd_usuario_rol',
			'bd_rol_modulo',
			'bd_captcha',
			'bd_pcookies',
		];

	/**
	 * Bases de datos para recuperar el espacio utilizado
	 *
	 * @var array
	 */
	protected $bases_datos = [
		'BD_Controles',
		'BD_Inventario',
		'BD_Logistica',
		'BD_Planificacion',
		'BD_Sucursales',
		'BD_TOA',
	];

	/**
	 * Arreglo con los tamaños de las tablas de todas las BD
	 *
	 * @var array
	 */
	protected $tables_sizes = [];

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @param  array $atributos Valores para inicializar el modelo
	 * @return  void
	 */
	public function __construct($atributos = [])
	{
		parent::__construct($atributos);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera arreglo con las queries en ejecución
	 *
	 * @return array Queries en ejecución
	 */
	public function get_running_queries()
	{
		$this->dbo = $this->load->database('adminbd', TRUE);

		$users_result = $this->dbo
			->query('sp_who2 "active"')
			->result_array();

		$process_result = $this->dbo
			->query('SELECT  * FROM sys.dm_exec_requests CROSS APPLY sys.dm_exec_sql_text(sql_handle)')
			->result_array();

		$users = collect($users_result)
			->filter(function($registro) {
				return trim($registro['Login']) !== 'sa'
					&& trim($registro['Login']) !== ''
					&& ! (trim($registro['Login']) === 'patripio' && trim($registro['ProgramName']) === 'PHP freetds');
			})->map_with_keys(function($registro) {
				return [trim($registro['SPID']) => $registro];
			})->all();

		return collect($process_result)
			->map_with_keys(function($process) {
				return [trim($process['session_id']) => $process];
			})->map(function($process, $process_id) use ($users) {
				return array_merge(array_get($users, $process_id, []), $process);
			})->filter(function($process) {
				return array_key_exists('SPID', $process);
			})->all();
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera listado de tablas del sistema para exportar
	 *
	 * @return array Listado de tablas
	 */
	public function table_list()
	{
		return collect($this->config->config)
			->map(function($item, $index) {
				return ['item' => $item, 'index' => $index];
			})
			->filter(function($item) {
				return strpos($item['index'], 'bd_') !== FALSE
					&& ! in_array($item['index'], $this->table_blacklist);
			})
			->map_with_keys(function($item, $index) {
				return [$item['item'] => $item['index']];
			})
			->sort()
			->all();
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera listado de campos de una tabla
	 *
	 * @param  string $tabla Nombre de la tabla
	 * @return array         Listado de campos
	 */
	public function fields_list($tabla = '')
	{
		if (strpos($tabla, '..') !== FALSE)
		{
			list($base_datos, $tabla) = explode('..', $tabla);
			$this->db->query('use '.$base_datos);
		}

		$fields = $this->db->list_fields($tabla);
		$this->db->query('use '.$this->db->database);

		return collect($fields)->map_with_keys(function($campo) {
			return [$campo => $campo];
		})->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el contenido de una tabla como texto CSV
	 *
	 * @param  string $tabla Tabla a extraer
	 * @return string
	 */
	public function table_to_csv($tabla = '')
	{
		if ( ! $this->table_exists($tabla))
		{
			return '--- Tabla no existe ---';
		}

		if (request('campo') && request('filtro'))
		{
			$this->db->where(request('campo'), request('filtro'));
		}

		$this->load->dbutil();
		$table_to_csv = $this->dbutil->csv_from_result($this->db->get($tabla));

		return $table_to_csv;
	}

	// --------------------------------------------------------------------

	/**
	 * Indica si una tabla existe
	 *
	 * @param  string $tabla Nombre de la tabla
	 * @return boolean
	 */
	protected function table_exists($tabla)
	{
		if (empty($tabla))
		{
			return FALSE;
		}

		if (strpos($tabla, '..') !== FALSE)
		{
			list($base_datos, $tabla) = explode('..', $tabla);
			$this->db->query('use '.$base_datos);
		}

		$exists = $this->db->table_exists($tabla);
		$this->db->query('use '.$this->db->database);

		return $exists;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el tamaño de las tablas de todas las BD
	 *
	 * @return Collection
	 */
	public function get_tables_sizes()
	{
		if ( ! empty($this->tables_sizes))
		{
			return $this->tables_sizes;
		}

		return $this->tables_sizes = collect($this->bases_datos)
			->map(function($base) {
				return $this->database_tables_sizes($base);
			})
			->flatten(1)
			->sort(function($elem_a, $elem_b) {
				return $elem_a['TotalSpaceKB'] < $elem_b['TotalSpaceKB'] ? 1 : -1;
			})->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Suma los campos de un arreglo de tamaños de tablas
	 *
	 * @param  array $tables_sizes Arreglo con los tamaños de las tablas
	 * @return array
	 */
	public function get_tables_sizes_sums($tables_sizes = [])
	{
		if (empty($this->tables_sizes))
		{
			$this->get_tables_sizes();
		}

		$campos_sumables = ['RowCounts', 'TotalSpaceKB', 'UsedSpaceKB', 'UnusedSpaceKB'];

		return collect($campos_sumables)->map_with_keys(function($campo) use ($tables_sizes) {
			return [$campo => collect($tables_sizes)->sum($campo)];
		})->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el tamaño de las tablas de una BD
	 *
	 * @param  string $base_datos Nombre de la base de datos
	 * @return array              Listado de tablas y tamaños
	 */
	public function database_tables_sizes($base_datos = 'bd_logistica')
	{
		$this->dbo = $this->load->database('adminbd', TRUE);

		$this->dbo->query('use '.$base_datos);
		$sql_tables_sizes = $this->table_size_query($base_datos);
		$result = $this->dbo->query($sql_tables_sizes)->result_array();
		$this->db->query('use '.$this->db->database);

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve query para recuperar el tamaño de las tablas de una base de datos
	 *
	 * @param  string $base_datos Base de datos
	 * @return string
	 */
	protected function table_size_query($base_datos = '')
	{
		return "SELECT '{$base_datos}' AS DataBaseName,
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
    TotalSpaceKB desc";


	}


}
// End of file Adminbd.php
// Location: ./models/Adminbd.php
