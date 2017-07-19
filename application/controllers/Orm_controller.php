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
class Orm_controller extends Controller_base {

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
		$this->listado(collect($this->menu_opciones)->keys()->first());
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega listado de los elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @return void
	 */
	public function listado($nombre_modelo = '')
	{
		$filtro = request('filtro');
		$page   = request('page');

		$nombre_modelo_full = $this->model_namespace.$nombre_modelo;
		$modelo = new $nombre_modelo_full;
		$modelo->set_model_filtro($filtro);

		app_render_view('ORM/orm_listado', [
			'menu_modulo' => $this->get_menu_modulo($nombre_modelo),
			'modelo'      => $modelo,
			'modelos'     => $modelo->list_paginated($page),
			'orm_filtro'  => $filtro,
			'url_editar'  => site_url("{$this->router->class}/editar/{$nombre_modelo}/"),
			'url_params'  => url_params(),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Edita un elementos de un modelo
	 *
	 * @param  string $nombre_modelo Modelo o entidad a desplegar
	 * @param  string $id_modelo     Identificador del modelo
	 * @return void
	 */
	public function editar($nombre_modelo = '', $id_modelo = NULL)
	{
		$nombre_modelo_full = $this->model_namespace.$nombre_modelo;
		$modelo = new $nombre_modelo_full($id_modelo);
		$url_params = url_params();

		app_render_view('ORM/orm_editar', [
			'menu_modulo'   => $this->get_menu_modulo($nombre_modelo),
			'modelo'        => $modelo,
			'url_form'      => site_url("{$this->router->class}/update/{$nombre_modelo}/{$id_modelo}{$url_params}"),
			'link_cancelar' => site_url("{$this->router->class}/listado/{$nombre_modelo}{$url_params}"),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza los datos de una entidad
	 *
	 * @param  string $nombre_modelo Nombre del modelo a actualizar o borrar
	 * @param  string $id_modelo     Identificador del modelo
	 * @return void
	 */
	public function update($nombre_modelo = '', $id_modelo = NULL)
	{
		$nombre_modelo_full = $this->model_namespace.$nombre_modelo;
		$modelo = new $nombre_modelo_full($id_modelo);
		route_validation($modelo->valida_form());
		$modelo->recuperar_post();

		if (request('grabar'))
		{
			$modelo->grabar();
			set_message(sprintf($this->lang->line('orm_msg_save_ok'), $modelo->get_model_label(), $modelo));
		}
		elseif (request('borrar'))
		{
			$modelo->borrar();
			set_message(sprintf($this->lang->line('orm_msg_delete_ok'), $modelo->get_model_label(), $modelo));
		}

		$url_params = url_params();
		redirect("{$this->router->class}/listado/{$nombre_modelo}{$url_params}");
	}

}
/* End of file orm_controller.php */
/* Location: ./application/controllers/orm_controller.php */
