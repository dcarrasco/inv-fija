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

use Inventario\inventario_reporte;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Controller Reportes de Inventario
 *
 * @category CodeIgniter
 * @package  Inventario
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Inventario_reportes extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'reportes';

	/**
	 * Menu de opciones del modulo
	 *
	 * @var  array
	 */
	public $menu_opciones = [
		'hoja' => ['url'=>'{{route}}/listado/hoja', 'texto'=>'lang::inventario_menu_reporte_hoja', 'icon'=>'file-text-o'],
		'material' => ['url'=>'{{route}}/listado/material', 'texto'=>'lang::inventario_menu_reporte_mat', 'icon'=>'barcode'],
		'material_faltante' => ['url'=>'{{route}}/listado/material_faltante', 'texto'=>'lang::inventario_menu_reporte_faltante', 'icon'=>'tasks'],
		'ubicacion' => ['url'=>'{{route}}/listado/ubicacion', 'texto'=>'lang::inventario_menu_reporte_ubicacion', 'icon'=>'map-marker'],
		'tipos_ubicacion' => ['url'=>'{{route}}/listado/tipos_ubicacion', 'texto'=>'lang::inventario_menu_reporte_tip_ubic', 'icon'=>'th'],
		'ajustes' => ['url'=>'{{route}}/listado/ajustes', 'texto'=>'lang::inventario_menu_reporte_ajustes', 'icon'=>'wrench'],
	];

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'inventario';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * Define el submenu del modulo
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return none
	 */
	public function index()
	{
		$this->listado('hoja');
	}

	// --------------------------------------------------------------------

	/**
	 * Genera los diferentes listados de reportes de inventario
	 *
	 * @param  string $tipo   Tipo de reporte
	 * @param  string $param1 Parámetro variable
	 * @return none
	 */
	public function listado($tipo = 'hoja', $param1 = '')
	{
		// define reglas para usar set_value, y ejecuta validación de formulario
		form_validation(Inventario_reporte::create()->rules_reporte);

		$id_inventario = request('inv_activo', Inventario_reporte::create()->get_id_inventario_activo());
		$inventario = Inventario_reporte::create()->find_id($id_inventario);

		app_render_view('inventario/reporte', [
			'menu_modulo'       => $this->get_menu_modulo($tipo),
			'reporte'           => $inventario->reporte($tipo, $param1),
			'combo_inventarios' => $inventario->get_combo_inventarios(),
			'id_inventario'     => $id_inventario,
		]);
	}

}
// End of file Inventario_reportes.php
// Location: ./controllers/Inventario_reportes.php
