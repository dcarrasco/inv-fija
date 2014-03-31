<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class config_stock extends CI_Controller {

	private $arr_menu = array();

	public function __construct()
	{
		parent::__construct();
		$this->acl_model->autentica('config_stock');

		if (ENVIRONMENT != 'production')
		{
			$this->output->enable_profiler(TRUE);
		}

		$this->arr_menu = array(
			'almacen_sap'     => array('url' => $this->uri->segment(1) . '/listado/almacen_sap', 'texto' => 'Almacenes SAP'),
			'tipoalmacen_sap' => array('url' => $this->uri->segment(1) . '/listado/tipoalmacen_sap', 'texto' => 'Tipo Almacenes SAP'),
			'proveedor'       => array('url' => $this->uri->segment(1) . '/listado/proveedor', 'texto' => 'Proveedores'),
			'almacenes_no_ingresados' => array('url' => $this->uri->segment(1) . '/almacenes_no_ingresados', 'texto' => 'Almacenes no ingresados'),
		);
	}

	// --------------------------------------------------------------------

	public function index()
	{
		$arr_keys = array_keys($this->arr_menu);
		$this->listado($arr_keys[0]);
	}

	// --------------------------------------------------------------------

	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Configuracion Stock';
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}

	// --------------------------------------------------------------------

	public function listado($nombre_modelo = '', $filtro = '_', $pag = 0)
	{
		$modelo = new $nombre_modelo;

		$filtro = ($this->input->post('filtro')) ? $this->input->post('filtro') : $filtro;
		$modelo->set_model_filtro($filtro);

		$modelo->list_paginated($pag);

		$data = array(
				'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'modelo'             => $modelo,
				'url_filtro'         => site_url($this->uri->segment(1) . '/listado/' . $nombre_modelo . '/'),
				'url_editar'         => site_url($this->uri->segment(1) . '/editar/'  . $nombre_modelo . '/'),
			);
		$this->_render_view('ORM/orm_listado', $data);
	}

	// --------------------------------------------------------------------

	public function editar($nombre_modelo = '' , $id = NULL)
	{
		$modelo = new $nombre_modelo;
		$modelo->find_id($id);

		if (!$modelo->valida_form())
		{
			$data = array(
				'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'modelo'             => $modelo,
				);
			$this->_render_view('ORM/orm_editar', $data);
		}
		else
		{
			$modelo->recuperar_post();
			if ($this->input->post('grabar'))
			{
				$modelo->grabar();
				$this->session->set_flashdata('msg_alerta', $modelo->get_model_label() . ' (' . $modelo . ') grabado correctamente');
			}
			else if ($this->input->post('borrar'))
			{
				$modelo->borrar();
				$this->session->set_flashdata('msg_alerta', $modelo->get_model_label() . ' ('. $modelo . ') borrado correctamente');
			}
			redirect($this->uri->segment(1) . '/listado/' . $nombre_modelo);
		}
	}

	// --------------------------------------------------------------------

	public function almacenes_no_ingresados()
	{
		$almacen = new Almacen_sap;
			$data = array(
				'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => 'almacenes_no_ingresados'),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'almacenes'          => $almacen->almacenes_no_ingresados(),

				);
		$this->_render_view('stock_sap/almacenes_no_ingresados', $data);

	}
}

/* End of file config_stock.php */
/* Location: ./application/controllers/config_stock.php */