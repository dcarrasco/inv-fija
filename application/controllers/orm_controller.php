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
class ORM_Controller extends CI_Controller {

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

	/**
	 * Titulo del modulo
	 *
	 * @var string
	 */
	public $titulo_modulo = '';

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
		$this->listado($arr_keys[0]);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega listado de los elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @param  string $filtro        Permite filtrar los registros a desplegar
	 * @param  string $pagina        Numero de la pagina a desplegar
	 * @return void
	 */
	public function listado($nombre_modelo = '', $filtro = '_', $pagina = 0)
	{
		$modelo = new $nombre_modelo;

		$filtro = $this->input->post('filtro') ? $this->input->post('filtro') : $filtro;
		$modelo->set_model_filtro($filtro);

		$modelo->list_paginated($pagina);

		$data = array(
			'menu_modulo' => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
			'modelo'      => $modelo,
			'url_filtro'  => site_url($this->router->class . '/listado/' . $nombre_modelo . '/'),
			'url_editar'  => site_url($this->router->class . '/editar/'  . $nombre_modelo . '/'),
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
	public function editar($nombre_modelo = '' , $id_modelo = NULL)
	{
		$modelo = new $nombre_modelo($id_modelo);

		if ( ! $modelo->valida_form())
		{
			$data = array(
				'menu_modulo'   => array('menu' => $this->arr_menu, 'mod_selected' => $nombre_modelo),
				'modelo'        => $modelo,
				'link_cancelar' => site_url($this->router->class . '/listado/' . $nombre_modelo),
			);

			app_render_view('ORM/orm_editar', $data);
		}
		else
		{
			$modelo->recuperar_post();

			if ($this->input->post('grabar'))
			{
				$modelo->grabar();
				$mensaje = sprintf($this->lang->line('orm_msg_save_ok'), $modelo->get_model_label(), $modelo);
			}
			else if ($this->input->post('borrar'))
			{
				$modelo->borrar();
				$mensaje = sprintf($this->lang->line('orm_msg_delete_ok'), $modelo->get_model_label(), $modelo);
			}

			set_message($mensaje);
			redirect($this->router->class . '/listado/' . $nombre_modelo);
		}
	}



}
/* End of file orm_controller.php */
/* Location: ./application/controllers/orm_controller.php */