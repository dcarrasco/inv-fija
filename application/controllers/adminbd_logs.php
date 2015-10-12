<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adminbd_logs extends CI_Controller {

	public $llave_modulo = 'j8hdajhJKHHu12';


	public function __construct()
	{
		parent::__construct();
		$this->lang->load('adminbd');

		$this->arr_menu = array(
			'borra_log' => array(
				'url'   => $this->router->class . '/borra_log',
				'texto' => $this->lang->line('adminbd_log_menu_borra'),
			),
			'indices' => array(
				'url'   => $this->router->class . '/indices',
				'texto' => $this->lang->line('adminbd_log_menu_indices'),
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @param  none
	 * @return none
	 */
	public function index()
	{
		$this->borra_log();
	}


	// --------------------------------------------------------------------

	/**
	 * Permite mostrar log de borrado de logs sql
	 *
	 * @param  none
	 * @return none
	 */
	public function borra_log()
	{
		$this->_show_file('c:\util\mantencion\log_borra_log.log', 'borra_log');
	}

	// --------------------------------------------------------------------

	/**
	 * Permite mostrar log de borrado de logs sql
	 *
	 * @param  none
	 * @return none
	 */
	public function indices()
	{
		$this->_show_file('c:\util\mantencion\log_indices_bd_logistica.log', 'indices');
	}

	// --------------------------------------------------------------------

	/**
	 * Permite mostrar un archivo de texto
	 *
	 * @param  string $archivo Ruta y nombre del archivo a mostrar
	 * @return none
	 */
	private function _show_file($archivo, $menu)
	{
		$log_file = array();

		if (file_exists($archivo))
		{
			$log_file = file($archivo);
		}
		else
		{
			$log_file = array('--- Archivo no encontrado ---');
		}

		$datos = array(
			'menu_modulo' => array('menu' => $this->arr_menu, 'mod_selected' => $menu),
			'log_file' => $log_file,
		);

		app_render_view('admindb/logs', $datos, $this->arr_menu);
	}



}
/* End of file Adminbd_queries.php */
/* Location: ./application/controllers/Adminbd_queries.php */
