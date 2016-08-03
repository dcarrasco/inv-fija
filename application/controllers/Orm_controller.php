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
 * Clase Controller ORM
 *
 * @category CodeIgniter
 * @package  ORM
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Orm_controller extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = '';

	/**
	 * Menu de opciones
	 *
	 * @var  array
	 */
	protected $arr_menu = array();

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('orm');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$arr_keys = array_keys($this->arr_menu);
		redirect($arr_keys[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega listado de los elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @param  string $pagina        Numero de la pagina a desplegar
	 * @return void
	 */
	public function listar($nombre_modelo = '', $pagina = 0)
	{
		$modelo = new $nombre_modelo;

		$filtro = $this->input->get('filtro');
		$filtro = urldecode($filtro);

		$modelo->set_model_filtro($filtro);

		$querystring = $this->input->get() ? '?'.http_build_query($this->input->get()) : '';

		$data = array(
			'menu_modulo' => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
			'modelo'      => $modelo,
			'modelos'     => $modelo->list_paginated($pagina),
			'orm_filtro'  => $filtro,
			'orm_pagina'  => $pagina,
			'url_filtro'  => site_url($this->uri->segment(1)),
			'url_nuevo'   => site_url($this->uri->segment(1).'/nuevo'.$querystring),
			'url_editar'  => site_url($this->uri->segment(1).'/%id%'.$querystring),
		);

		app_render_view('ORM/orm_listado', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Edita un elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @param  string $id_modelo     Identificador del modelo
	 * @return void
	 */
	public function mostrar($nombre_modelo = '', $id_modelo = NULL)
	{
		$modelo = new $nombre_modelo($id_modelo);
		$querystring = $this->input->get() ? '?'.http_build_query($this->input->get()) : '';

		$data = array(
			'menu_modulo'   => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
			'modelo'        => $modelo,
			'link_cancelar' => site_url($this->uri->segment(1).$querystring),
			'form_url'      => site_url($this->uri->segment(1).($id_modelo ? '/'.$id_modelo : '').$querystring),
		);

		app_render_view('ORM/orm_editar', $data);
	}

	// --------------------------------------------------------------------

	/**
	 * Edita un elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @param  string $id_modelo     Identificador del modelo
	 * @return void
	 */
	public function grabar($nombre_modelo = '', $id_modelo = NULL)
	{
		$modelo = new $nombre_modelo($id_modelo);
		$querystring = $this->input->get() ? '?'.http_build_query($this->input->get()) : '';

		if ( ! $modelo->valida_form())
		{
			return $this->mostrar($nombre_modelo, $id_modelo);
		}
		else
		{
			$modelo->recuperar_post();

			if ($this->input->post('grabar'))
			{
				$modelo->grabar();
				set_message(sprintf($this->lang->line('orm_msg_save_ok'), $modelo->get_model_label(), $modelo));
			}
			else if ($this->input->post('borrar'))
			{
				$modelo->borrar();
				set_message(sprintf($this->lang->line('orm_msg_delete_ok'), $modelo->get_model_label(), $modelo));
			}

			redirect($this->uri->segment(1).$querystring);
		}
	}


}
/* End of file orm_controller.php */
/* Location: ./application/controllers/orm_controller.php */