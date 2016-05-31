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
class Adminbd_exportartablas extends CI_Controller {

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
		$this->load->model('adminbd_model');
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

		// reglas de validacion formulario
		$this->form_validation->set_rules($this->adminbd_model->validation_exportar_tablas);

		if ($this->form_validation->run() === TRUE)
		{
			if ($this->db->table_exists($this->input->post('tabla')))
			{
				if ($this->input->post('campo') AND $this->input->post('filtro'))
				{
					$this->db->where($this->input->post('campo'), $this->input->post('filtro'));
				}

				$result = $this->db->get($this->input->post('tabla'));
				$result_string = $this->dbutil->csv_from_result($result);
			}
		}

		$data = array(
			'combo_tablas' => $this->adminbd_model->get_table_list(),
			'combo_campos' => $this->input->post('tabla')
								? $this->adminbd_model->get_fields_list($this->input->post('tabla'))
								: array(),
			'result_string' => $result_string,
		);

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