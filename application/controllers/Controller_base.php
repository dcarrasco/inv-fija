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
	public $menu_opciones = array();

	/**
	 * Errores de validación
	 *
	 * @var Collection
	 */
	public $errors = NULL;

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->errors = collect($this->session->flashdata('errors'));
	}

	// --------------------------------------------------------------------

	/**
	 * Fija arreglo para el menu modulo
	 *
	 * @param  array $menu Arreglo con el menu
	 * @return void
	 */
	public function set_menu_modulo($menu = [])
	{
		$this->menu_opciones = $menu;
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
		return array('menu' => $this->menu_opciones, 'mod_selected' => $opcion);
	}



}
/* End of file Controller_base.php */
/* Location: ./application/controllers/Controller_base.php */
