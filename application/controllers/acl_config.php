<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl_config extends CI_Controller {

	private $arr_menu = array();

	public function __construct()
	{
		parent::__construct();
		$this->acl_model->autentica('acl_config');

		if (ENVIRONMENT != 'production')
		{
			$this->output->enable_profiler(TRUE);
		}

		$this->arr_menu = array(
			'usuario'        => array('url' => $this->uri->segment(1) . '/listado/usuario', 'texto' => 'Usuarios'),
			'app'            => array('url' => $this->uri->segment(1) . '/listado/app', 'texto' => 'Aplicaciones'),
			'rol'            => array('url' => $this->uri->segment(1) . '/listado/rol', 'texto' => 'Roles'),
			'modulo'         => array('url' => $this->uri->segment(1) . '/listado/modulo', 'texto' => 'Modulos'),
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
		$arr_keys = array_keys($this->arr_menu);
		$this->listado($arr_keys[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * Genera las vistas para los métodos de este controlador
	 *
	 * @param  string $vista Nombre de la vista a desplegar
	 * @param  array  $data  Arreglo con las variables a pasar a la vista
	 * @return none
	 */
	private function _render_view($vista = '', $data = array())
	{
		$data['titulo_modulo'] = 'Configuración Control de Acceso';
		$this->load->view('app_header', $data);
		$this->load->view($vista, $data);
		$this->load->view('app_footer', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega listado de los elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @param  string $filtro        Permite filtrar los registros a desplegar
	 * @param  string $pag           Numero de la pagina a desplegar
	 * @return none
	 */
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

	/**
	 * Edita un elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @param  string $id            Identificador del modelo
	 * @return none
	 */
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
				'link_cancelar'      => site_url($this->uri->segment(1) . '/listado/' . $nombre_modelo),
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



}
/* End of file acl_config.php */
/* Location: ./application/controllers/acl_config.php */