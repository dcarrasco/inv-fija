<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl extends CI_Controller {

	private $arr_menu = array(
				'usuario_model'        => array('url' => '/acl/listado/usuario_model', 'texto' => 'Usuarios'),
				'app_model'            => array('url' => '/acl/listado/app_model', 'texto' => 'Aplicaciones'),
				'rol_model'            => array('url' => '/acl/listado/rol_model', 'texto' => 'Roles'),
				'modulo_model'         => array('url' => '/acl/listado/modulo_model', 'texto' => 'Modulos'),
			);

	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('acl_config');
	}

	public function index()
	{
		$arr_keys = array_keys($this->arr_menu);
		$this->listado($arr_keys[0]);
	}


	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Configuración Control de Acceso';
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}


	public function listado($nombre_modelo = '', $filtro = '_', $pag = 0)
	{
		$filtro = ($this->input->post('filtro')) ? $this->input->post('filtro') : $filtro;

		$this->load->model($nombre_modelo);
		$this->$nombre_modelo->find('all', array('filtro' => $filtro, 'limit' => $this->$nombre_modelo->get_page_results(), 'offset' => $pag));

		//dbg($modelo);

		$data = array(
				'menu_modulo'        => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
				'modelo'             => $this->$nombre_modelo,
				'links_paginas'      => $this->$nombre_modelo->crea_links_paginas($filtro, 'acl/listado'),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'filtro'             => ($filtro == '_') ? '' : $filtro,
				'url_filtro'         => site_url('acl/listado/' . $nombre_modelo . '/'),
				'url_editar'         => site_url('acl/editar/' . $nombre_modelo . '/'),
				'url_borrar'         => site_url('acl/borrar/' . $nombre_modelo . '/'),
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
			redirect('acl/listado/' . $nombre_modelo);
		}

	}

}

/* End of file acl.php */
/* Location: ./application/controllers/acl.php */