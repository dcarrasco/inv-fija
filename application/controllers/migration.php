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
	}

	// --------------------------------------------------------------------

	/**
	 * Migra la BD a la ultima version
	 *
	 * @return  void
	 */
	public function migrate()
	{
		$this->load->library('migration');

		if ( ! $this->migration->current())
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
		$this->load->library('migration');

		if ($version > -1)
		{
			$this->migration->version($version);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Ejecuta varia acciones de migracion
	 *
	 * @param  string $command Comando a ejecutar
	 * @param  mixed  $param1  Parametro para el comando
	 * @return void
	 */
	public function commands($command = '', $param1 = NULL)
	{
		$this->load->library('migration');

		if ($command === 'find')
		{
			dbg($this->migration->find_migrations());
		}
		else if ($command === 'current')
		{
			dbg($this->migration->current());
		}
		else if ($command === 'latest')
		{
			dbg($this->migration->latest());
		}
		else if ($command === 'version')
		{
			dbg($this->migration->version($param1));
		}

		dbg($this->migration->error_string());
	}


}

/* End of file migration.php */
/* Location: ./application/controllers/migration.php */
