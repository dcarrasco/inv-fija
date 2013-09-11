<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class config_stock extends CI_Controller {

	private $arr_menu = array(
				'almacen_sap'        => array('url' => '/config_stock/listado/almacen_sap', 'texto' => 'Almacenes SAP'),
				'tipoalmacen_sap'    => array('url' => '/config_stock/listado/tipoalmacen_sap', 'texto' => 'Tipo Almacenes SAP'),
				'proveedor'          => array('url' => '/config_stock/listado/proveedor', 'texto' => 'Proveedores'),
				'almacenes_no_ingresados' => array('url' => '/config_stock/almacenes_no_ingresados', 'texto' => 'Almacenes no ingresados'),
			);

	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('config_stock');
	}

	public function index()
	{
		$arr_keys = array_keys($this->arr_menu);
		$this->listado($arr_keys[0]);
	}


	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Configuracion Stock';
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}


	public function listado($nombre_modelo = '', $filtro = '_', $pag = 0)
	{
		$filtro = ($this->input->post('filtro')) ? $this->input->post('filtro') : $filtro;

		$modelo = new $nombre_modelo;
		$modelo->find('all', array('filtro' => $filtro, 'limit' => $modelo->get_model_page_results(), 'offset' => $pag));

		//dbg($modelo);

		$data = array(
				'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
				'modelo'             => $modelo,
				'links_paginas'      => $modelo->crea_links_paginas($filtro, 'config_stock/listado'),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'filtro'             => ($filtro == '_') ? '' : $filtro,
				'url_filtro'         => site_url('config_stock/listado/' . $nombre_modelo . '/'),
				'url_editar'         => site_url('config_stock/editar/' . $nombre_modelo . '/'),
				'url_borrar'         => site_url('config_stock/borrar/' . $nombre_modelo . '/'),
			);
		$this->_render_view('ORM/orm_listado', $data);
	}


	public function editar($nombre_modelo = '' , $id = NULL)
	{
		$modelo = new $nombre_modelo($id);
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
			redirect('config_stock/listado/' . $nombre_modelo);
		}

	}



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