<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Movil_reporte extends CI_Controller {

	private $arr_menu = array(
				'consulta_inventario'     => array('url' => '/movil_reporte/consulta_inventario', 'texto' => 'Consulta Inventario'),
			);

	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('yhsibsdyqw2oe');
	}

	public function index()
	{
		$arr_keys = array_keys($this->arr_menu);
		$this->{$arr_keys[0]}();
	}


	private function _menu_configuracion($modelo)
	{

		$menu = '<ul>';
		foreach($this->arr_menu as $key => $val)
		{
			$selected = ($key == $modelo) ? ' class="selected"' : '';
			$menu .= '<li' . $selected . '>' . anchor($val['url'], $val['texto']) . '</li>';
		}
		$menu .= '</ul>';
		return $menu;
	}


	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Configuracion';
		$data['menu_app'] = $this->acl_model->menu_app();
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}


	public function consulta_inventario()
	{
		$data = array();
		$this->_render_view('inventario_movil/reporte', $data);
	}

	public function listado($nombre_modelo = '', $filtro = '_', $pag = 0)
	{
		$filtro = ($this->input->post('filtro')) ? $this->input->post('filtro') : $filtro;

		$modelo = new $nombre_modelo;
		$modelo->find('all', array('filtro' => $filtro, 'limit' => $modelo->get_model_page_results(), 'offset' => $pag));

		//dbg($modelo);

		$data = array(
				'menu_configuracion' => $this->_menu_configuracion($nombre_modelo),
				'modelo'             => $modelo,
				'links_paginas'      => $modelo->crea_links_paginas($filtro, 'movil_reporte/listado'),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'filtro'             => ($filtro == '_') ? '' : $filtro,
				'url_filtro'         => site_url('movil_reporte/listado/' . $nombre_modelo . '/'),
				'url_editar'         => site_url('movil_reporte/editar/' . $nombre_modelo . '/'),
				'url_borrar'         => site_url('movil_reporte/borrar/' . $nombre_modelo . '/'),
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
				'menu_configuracion' => $this->_menu_configuracion($nombre_modelo),
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
			redirect('movil_reporte/listado/' . $nombre_modelo);
		}

	}

}

/* End of file movil_reporte.php */
/* Location: ./application/controllers/movil_reporte.php */