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

use Stock\Reporte_stock;
use Stock\Tipoalmacen_sap;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Controller Reportes de stock
 *
 * Utiliza ORM_Controller
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock_reporte extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'odk9@i2_23';

	/**
	 * Menu de opciones del modulo
	 *
	 * @var  array
	 */
	public $menu_opciones = [
		'permanencia' => ['url'=>'{{route}}/listado/permanencia', 'texto'=>'lang::stock_perm_menu_perm', 'icon'=>'clock-o'],
		'mapastock' => ['url'=>'{{route}}/mapastock', 'texto'=>'lang::stock_perm_menu_mapa', 'icon'=>'globe'],
		'movhist' => ['url'=>'{{route}}/movhist', 'texto'=>'lang::stock_perm_menu_movs', 'icon'=>'calendar'],
		'est03' => ['url'=>'{{route}}/est03', 'texto'=>'lang::stock_perm_menu_est03', 'icon'=>'refresh'],
	];

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'stock';

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
	 * @return void
	 */
	public function index()
	{
		$this->listado('permanencia');
	}

	// --------------------------------------------------------------------

	/**
	 * Permite desplegar reportes de stock SAP
	 *
	 * @param  string $tipo   Tipo del reporte
	 * @param  string $param1 [description]
	 * @return void
	 */
	public function listado($tipo = 'permanencia', $param1 = '')
	{
		$arr_campos = [];
		$datos_hoja = [];
		$view = 'listado';

		$reporte = new Reporte_stock;

		if (form_validation($reporte->rules_permanencia) && $tipo === 'permanencia')
		{
			$reporte->get_campos_reporte_permanencia();
			$reporte->get_reporte_permanencia($reporte->get_config_reporte_permanencia());
		}

		app_render_view('stock_sap/reporte', [
			'menu_modulo'      => $this->get_menu_modulo($tipo),
			'reporte'          => $reporte->genera_reporte(),
			'tipo_reporte'     => $view,
			'nombre_reporte'   => $tipo,
			'filtro_dif'       => '',
			'arr_campos'       => $arr_campos,
			'fecha_reporte'    => $reporte->get_fecha_reporte(),
			'combo_tipo_alm'   => Tipoalmacen_sap::create()->get_combo_tiposalm(request('tipo_op', 'MOVIL')),
			'combo_estado_sap' => $reporte->combo_estado_sap(),
			'combo_tipo_mat'   => $reporte->combo_tipo_mat(),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega el detalle del stock
	 *
	 * @return void
	 */
	public function detalle()
	{
		$reporte = new Reporte_stock;
		$reporte->get_campos_reporte_detalle_series();
		$reporte->get_detalle_series(request()->all());

		app_render_view('stock_sap/detalle_series', [
			'menu_modulo'        => $this->get_menu_modulo('permanencia'),
			'menu_configuracion' => '',
			'detalle_series'     => $reporte->genera_reporte(),
		]);

	}


	// --------------------------------------------------------------------

	/**
	 * Despliega mapa de monto y cantidades de materiales en bodegas
	 *
	 * @return void
	 */
	public function mapastock()
	{
		$centro = request('sel_centro', 'CL03');

		$arr_treemap = Reporte_stock::create()->get_treemap_permanencia($centro);
		$arr_treemap_cantidad = Reporte_stock::create()->arr_query2treemap('cantidad', $arr_treemap, ['tipo', 'alm', 'marca', 'modelo'], 'cant', 'perm');
		$arr_treemap_valor = Reporte_stock::create()->arr_query2treemap('valor', $arr_treemap, ['tipo', 'alm', 'marca', 'modelo'], 'valor', 'perm');

		app_render_view('stock_sap/treemap', [
			'menu_modulo'           => $this->get_menu_modulo('mapastock'),
			'treemap_data_cantidad' => $arr_treemap_cantidad,
			'treemap_data_valor'    => $arr_treemap_valor,
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega reporte de movimientos
	 *
	 * @return void
	 */
	public function movhist()
	{
		$reporte = new Reporte_stock;

		form_validation($reporte->rules_movhist);

		$param_tipo_fecha     = request('tipo_fecha', 'ANNO');
		$param_tipo_alm       = request('tipo_alm', 'MOVIL-TIPOALM');
		$param_tipo_mat       = request('tipo_mat', 'TIPO');
		$param_tipo_cruce_alm = request('tipo_cruce_alm', 'alm');
		$param_sort           = request('sort', '+fecha');

		$reporte->get_campos_reporte_movhist();
		$reporte->get_reporte_movhist([
			'filtros' => [
				'tipo_fecha'     => $param_tipo_fecha,
				'fechas'         => request('fechas'),
				'cmv'            => request('cmv'),
				'tipo_alm'       => $param_tipo_alm,
				'almacenes'      => request('almacenes'),
				'tipo_cruce_alm' => $param_tipo_cruce_alm,
				'tipo_mat'       => $param_tipo_mat,
				'materiales'     => request('materiales'),
			],
			'orden' => $param_sort,
		]);

		app_render_view('stock_sap/movhist', [
			'menu_modulo'      => $this->get_menu_modulo('movhist'),
			'combo_tipo_fecha' => $reporte->get_combo_tipo_fecha(),
			'combo_fechas'     => $reporte->get_combo_fechas($param_tipo_fecha),
			'combo_cmv'        => $reporte->get_combo_cmv(),
			'combo_tipo_alm'   => $reporte->get_combo_tipo_alm(),
			'combo_almacenes'  => $reporte->get_combo_almacenes($param_tipo_alm),
			'combo_tipo_mat'   => $reporte->get_combo_tipo_mat(),
			'combo_materiales' => $reporte->get_combo_materiales($param_tipo_mat),
			'reporte'          => $reporte->genera_reporte(),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo tipo fecha
	 *
	 * @param  string $tipo Tipo de fechas a devolver
	 * @return string       Tipos de fechas formato json
	 */
	public function movhist_ajax_tipo_fecha($tipo = 'ANNO')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(Reporte_stock::create()->get_combo_tipo_fecha($tipo)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo fecha
	 *
	 * @param  string $tipo   Tipo de fechas a devolver
	 * @param  string $filtro Filtro de las fechas
	 * @return string         Tipos de fechas formato json
	 */
	public function movhist_ajax_fechas($tipo = 'ANNO', $filtro = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(Reporte_stock::create()->get_combo_fechas($tipo, $filtro)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo tipos de almacen
	 *
	 * @param  string $tipo Tipo de almacen
	 * @return string       Tipos de almacen
	 */
	public function movhist_ajax_tipo_alm($tipo = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(Reporte_stock::create()->get_combo_tipo_alm($tipo)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo almacenes
	 *
	 * @param  string $tipo   Tipo de almacen
	 * @param  string $filtro Filtro de las fechas
	 * @return string         Tipos de almacen
	 */
	public function movhist_ajax_almacenes($tipo = 'MOVIL-TIPOALM', $filtro = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(Reporte_stock::create()->get_combo_almacenes($tipo, $filtro)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo tipo de material
	 *
	 * @param  string $tipo Tipo de material
	 * @return string       Tipos de material
	 */
	public function movhist_ajax_tipo_mat($tipo = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(Reporte_stock::create()->get_combo_tipo_mat($tipo)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos json para poblar combo materiales
	 *
	 * @param  string $tipo   Tipo de material
	 * @param  string $filtro Filtro de los materiales
	 * @return string         Materiales
	 */
	public function movhist_ajax_materiales($tipo = 'TIPO', $filtro = '')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(Reporte_stock::create()->get_combo_materiales($tipo, $filtro)));
	}



	// --------------------------------------------------------------------

	/**
	 * Despliega el detalle del stock
	 *
	 * @return void
	 */
	public function est03()
	{
		app_render_view('stock_sap/est03', [
			'menu_modulo' => $this->get_menu_modulo('est03'),
			'almacenes'   => Reporte_stock::create()->get_almacenes_est03(),
			'marcas'      => Reporte_stock::create()->get_marcas_est03(),
			'reporte'     => Reporte_stock::create()->get_stock_est03(),
		]);
	}

}
// End of file Stock_reporte.php
// Location: ./controllers/Stock_reporte.php
