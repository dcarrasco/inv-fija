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

use Stock\Log_gestor;
use Stock\Analisis_series;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Controller Analisis series
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock_analisis_series extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'analisis_series';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('stock');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->historia();
	}

	// --------------------------------------------------------------------

	/**
	 * Permite desplegar la varios reportes asociados a una o varias series
	 *
	 * @return void
	 */
	public function historia()
	{
		$datos = [
			'datos_show_mov'       => '',
			'datos_show_despachos' => '',
			'datos_show_stock_sap' => '',
			'datos_show_stock_scl' => '',
			'datos_show_trafico'   => '',
			'datos_show_gdth'      => '',
		];


		if (form_validation(Analisis_series::create()->rules))
		{
			$series = request('series');

			$analisis_model = new Analisis_series;
			$gestor_model   = new Log_gestor;

			$arr_reportes = [
				'show_mov' => [
					'class'  => $analisis_model, 'method' => 'get_historia', 'params' => [$series],
				],
				'show_despachos' => [
					'class'  => $analisis_model, 'method' => 'get_despacho', 'params' => [$series],
				],
				'show_stock_sap' => [
					'class'  => $analisis_model, 'method' => 'get_stock_sap', 'params' => [$series],
				],
				'show_stock_scl' => [
					'class'  => $analisis_model, 'method' => 'get_stock_scl', 'params' => [$series],
				],
				'show_trafico' => [
					'class'  => $analisis_model, 'method' => 'get_trafico', 'params' => [$series],
				],
				'show_gdth' => [
					'class'  => $gestor_model, 'method' => 'get_log', 'params' => [$series, 'serie_deco', 'log', "'ALTA', 'BAJA'", FALSE],
				],
			];

			$datos = collect($arr_reportes)
				->only(request()->keys())
				->map_with_keys(function($elem, $indice) {
					return [
						"datos_{$indice}" => call_user_func_array([$elem['class'], $elem['method']], $elem['params'])
					];
				})->all();
		}

		app_render_view('stock_sap/analisis_series_view', $datos);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega el tráfico por mes de un o varias series
	 *
	 * @return void
	 */
	public function trafico_por_mes()
	{
		app_render_view('stock_sap/analisis_series_trafico_view', [
			'combo_mes'     => Analisis_series::create()->get_meses_trafico(),
			'datos_trafico' => Analisis_series::create()->get_trafico_mes(request('series'), request('meses'), request('sel_tipo')),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve datos en formato json del trafico por mes de una o varias series
	 *
	 * @return void
	 */

	/**
	 * Devuelve datos en formato json del trafico por mes de una o varias series
	 *
	 * @param  string $serie Numero de serie
	 * @param  string $meses Meses a recuperar
	 * @param  string $tipo  Tipo de registro a comparar (imei o numero de celular)
	 * @return void
	 */
	public function ajax_trafico_mes($serie = '', $meses = '', $tipo = 'imei')
	{
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode(Analisis_series::create()->get_trafico_mes($serie, $meses, $tipo)));
	}


}
// End of file Stock_analisis_series.php
// Location: ./controllers/Stock_analisis_series.php
