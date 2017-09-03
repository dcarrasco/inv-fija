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

use Inventario\inventario_reporte;

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
		$this->lang->load('inventario');

		$this->set_menu_modulo([
			'hoja' => [
				'url'   => "{$this->router->class}/listado/hoja",
				'texto' => lang('inventario_menu_reporte_hoja'),
				'icon'  => 'file-text-o'
			],
			'material' => [
				'url'   => "{$this->router->class}/listado/material",
				'texto' => lang('inventario_menu_reporte_mat'),
				'icon'  => 'barcode'
			],
			'material_faltante' => [
				'url'   => "{$this->router->class}/listado/material_faltante",
				'texto' => lang('inventario_menu_reporte_faltante'),
				'icon'  => 'tasks'
			],
			'ubicacion' => [
				'url'   => "{$this->router->class}/listado/ubicacion'",
				'texto' => lang('inventario_menu_reporte_ubicacion'),
				'icon'  => 'map-marker'
			],
			'tipos_ubicacion' => [
				'url'   => "{$this->router->class}/listado/tipos_ubicacion",
				'texto' => lang('inventario_menu_reporte_tip_ubic'),
				'icon'  => 'th'
			],
			'ajustes' => [
				'url'   => "{$this->router->class}/listado/ajustes",
				'texto' => lang('inventario_menu_reporte_ajustes'),
				'icon'  => 'wrench'
			],
		]);
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
		$inventario = new Inventario_reporte($id_inventario);

		app_render_view('inventario/reporte', [
			'menu_modulo'       => $this->get_menu_modulo($tipo),
			'reporte'           => $inventario->reporte($tipo, $param1),
			'combo_inventarios' => $inventario->get_combo_inventarios(),
			'id_inventario'     => $id_inventario,
		]);
	}

}
/* End of file inventario_reportes.php */
/* Location: ./application/controllers/inventario_reportes.php */
