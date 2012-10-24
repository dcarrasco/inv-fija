<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class config2 extends CI_Controller {

	private $arr_menu = array(
				'auditor'        => array('url' => '/config2/listado/auditor', 'texto' => 'Auditores'),
				'catalogo'       => array('url' => '/config2/listado/catalogo', 'texto' => 'Materiales'),
				'inv_activo'     => array('url' => '/config2/listado/inv_activo', 'texto' => 'Inventario Activo'),
				'tipo_ubicacion' => array('url' => '/config2/listado/tipo_ubicacion', 'texto' => 'Tipos Ubicacion'),
				'ubicaciones'    => array('url' => '/config/ubicacion_tipo_ubicacion', 'texto' => 'Ubicaciones'),
				'app'            => array('url' => '/config2/listado/app', 'texto' => 'Aplicaciones'),
				'rol'            => array('url' => '/config2/listado/rol', 'texto' => 'Roles'),
				'modulo'         => array('url' => '/config2/listado/modulo', 'texto' => 'Modulos'),
				'usuario'        => array('url' => '/config2/listado/usuario', 'texto' => 'Usuarios'),
			);

	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
		$this->acl_model->autentica('config2');
	}

	public function index()
	{
		$arr_keys = array_keys($this->arr_menu);
		$this->listado($arr_keys[0]);
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


	public function listado($nombre_modelo = '', $filtro = '_', $pag = 0)
	{
		$filtro = ($this->input->post('filtro')) ? $this->input->post('filtro') : $filtro;

		$modelo = new $nombre_modelo;
		$modelo->get_all($filtro, $pag);

		$data = array(
				'menu_configuracion' => $this->_menu_configuracion($nombre_modelo),
				'modelo'             => $modelo,
				'links_paginas'      => $modelo->crea_links_paginas($filtro),
				'msg_alerta'         => $this->session->flashdata('msg_alerta'),
				'filtro'             => ($filtro == '_') ? '' : $filtro,
				'url_filtro'         => site_url('config2/listado/' . $nombre_modelo . '/'),
				'url_editar'         => site_url('config2/editar/' . $nombre_modelo . '/'),
				'url_borrar'         => site_url('config2/borrar/' . $nombre_modelo . '/'),
			);
		$this->_render_view('ORM/orm_listado', $data);
	}


	public function editar($nombre_modelo = '' , $id = NULL)
	{
		$modelo = new $nombre_modelo($id);
		$modelo->get_id($id);

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
				$this->session->set_flashdata('msg_alerta', $modelo->get_model_label() . ' grabado correctamente');
			}
			else if ($this->input->post('borrar'))
			{
				$modelo->borrar();
				$this->session->set_flashdata('msg_alerta', $modelo->get_model_label() . ' ('. $modelo->__toString() . ') borrado correctamente');
			}
			redirect('config2/listado/' . $nombre_modelo);
		}

	}




}

/* End of file config2.php */
/* Location: ./application/controllers/config2.php */