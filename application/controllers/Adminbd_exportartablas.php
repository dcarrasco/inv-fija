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
 * Clase Controller Exportar tablas
 * *
 * @category CodeIgniter
 * @package  AdministradorBD
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Adminbd_exportartablas extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = '9js7j6809pt43j01';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * Define el submenu del modulo
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('adminbd');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->exportar();
	}


	// --------------------------------------------------------------------

	/**
	 * Exportar una tabla del sistema en formato CVS
	 *
	 * @return void
	 */
	public function exportar()
	{
		$this->load->dbutil();
		$result_string = '';

		$base_datos = '';
		$tabla = request('tabla');

		if (strpos($tabla, '..') !== FALSE)
		{
			list($base_datos, $tabla) = explode('..', $tabla);
			$this->db->query('use '.$base_datos);
		}

		// reglas de validacion formulario
		$is_form_valid = form_validation(Adminbd::create()->rules_exportar_tablas);

		if ($is_form_valid AND $this->db->table_exists($tabla))
		{
			if (request('campo') AND request('filtro'))
			{
				$this->db->where(request('campo'), request('filtro'));
			}

			$result_string = $this->dbutil->csv_from_result($this->db->get($tabla));
		}

		$data = [
			'combo_tablas' => Adminbd::create()->get_table_list(),
			'combo_campos' => $tabla ? Adminbd::create()->get_fields_list($tabla) : [],
			'result_string' => $result_string,
		];

		$this->db->query('use '.$this->db->database);

		app_render_view('admindb/exportar_tablas', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Recuperar listado de campos de una tabla
	 *
	 * @param  string $tabla Nombre de la tabla
	 * @return void
	 */
	public function ajax_campos($tabla = '')
	{
		$this->output
			->set_content_type('text')
			->set_output(form_print_options($this->adminbd_model->get_fields_list($tabla)));
	}


}
/* End of file Adminbd_exportartablas.php */
/* Location: ./application/controllers/Adminbd_exportartablas.php */
