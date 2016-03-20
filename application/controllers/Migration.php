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
 * Clase Controller Migracion
 *
 * @category CodeIgniter
 * @package  Migracion
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Migration extends CI_Controller {

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->library('migration');
		$this->output->enable_profiler(FALSE);
	}

	// --------------------------------------------------------------------

	/**
	 * Migra la BD a la ultima version
	 *
	 * @return  void
	 */
	public function index()
	{
		$this->_print_help();
		//$this->find();
	}

	// --------------------------------------------------------------------

	private function _print_help()
	{
		$tab = '    ';
		echo PHP_EOL;
		echo 'Modo de uso'.PHP_EOL;
		echo $tab.'php index.php migration _comando_ [param1]'.PHP_EOL;
		echo PHP_EOL;
		echo $tab.'Comandos'.PHP_EOL;
		echo $tab.'find          '.$tab.'Despliega listado de migraciones disponibles'.PHP_EOL;
		echo $tab.'latest        '.$tab.'Actualiza a la última versión disponibles'.PHP_EOL;
		echo $tab.'migrate_config'.$tab.'Actualiza a la version definida en config/migration.php -> migration_version'.PHP_EOL;
		echo $tab.'version [ver] '.$tab.'Actualiza a la version especificada en [ver]'.PHP_EOL;
		echo PHP_EOL;
	}

	// --------------------------------------------------------------------

	/**
	 * Migra la BD a la ultima version
	 *
	 * @return  void
	 */
	public function migrate_config()
	{
		if ( config_item('migration_version')!==0 AND ! $this->migration->current())
		{
			show_error($this->migration->error_string());
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza la BD a una version especifica
	 *
	 * @param   integer $version Numero de la version
	 * @return  void
	 */
	public function version($version = -1)
	{
		if ($version > -1)
		{
			$this->migration->version($version);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Lista migraciones
	 *
	 * @return void
	 */
	public function find()
	{
		dbg($this->migration->find_migrations());
	}

	// --------------------------------------------------------------------

	/**
	 * Ejecuta migracions hasta la última
	 *
	 * @return void
	 */
	public function latest()
	{
		dbg($this->migration->latest());
		dbg($this->migration->error_string());
	}


}
/* End of file migration.php */
/* Location: ./application/controllers/migration.php */