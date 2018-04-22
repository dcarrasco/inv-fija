<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
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
class Controller_base extends CI_Controller {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = '';

	/**
	 * Menu de opciones del modulo
	 *
	 * @var  array
	 */
	public $menu_opciones = [];

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = [];

	/**
	 * Errores de validación
	 *
	 * @var Collection
	 */
	public $errors = NULL;

	/**
	 * Collection de request
	 *
	 * @var Collection
	 */
	public $request = NULL;

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load_lang_controller();
		$this->set_menu_modulo();

		$this->errors = collect($this->session->flashdata('errors'));
	}

	// --------------------------------------------------------------------

	/**
	 * Carga lenguajes del controller
	 *
	 * @return void
	 */
	public function load_lang_controller()
	{
		collect($this->lang_controller)->each(function($lang_file) {
			$this->lang->load($lang_file);
		});

	}

	// --------------------------------------------------------------------

	/**
	 * Fija arreglo para el menu modulo
	 *
	 * @return void
	 */
	public function set_menu_modulo()
	{
		$router = $this->router;
		$model_namespace = isset($this->model_namespace) ? $this->model_namespace : '';
		$this->menu_opciones = collect($this->menu_opciones)
			->map(function($menu, $modulo) use ($router, $model_namespace) {
				$menu['url'] = array_key_exists('url', $menu)
					? $menu['url']
					: $router->class.'/listado/'.$modulo.'/';

				$module_class = $model_namespace.$modulo;
				$menu['texto'] = array_key_exists('texto', $menu)
					? $menu['texto']
					: (new $module_class())->get_label_plural();

				return $menu;
			})
			->map(function($menu) use ($router) {
				$menu['url'] = str_replace('{{route}}', $router->class, array_get($menu, 'url'));
				$menu['texto'] = substr(array_get($menu, 'texto'), 0, 6) === 'lang::'
					? lang(substr(array_get($menu, 'texto'), 6, strlen(array_get($menu, 'texto'))))
					: array_get($menu, 'texto');

				return $menu;
			})
			->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo formateado para el menu modulo
	 *
	 * @param  string $opcion Opcion seleccionada del menu
	 * @return array          Menu de opciones, con seleccion
	 */
	public function get_menu_modulo($opcion = NULL)
	{
		return [
			'menu' => $this->menu_opciones,
			'mod_selected' => $opcion
		];
	}

}
// End of file Controller_base.php
// Location: ./controllers/Controller_base.php
