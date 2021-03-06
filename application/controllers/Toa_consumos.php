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


use Toa\Consumo_toa;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Controller Reporte de consumos TOA
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_consumos extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'ij792bxksh1';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('toa');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		return $this->consumos();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle del log del gestor DTH
	 *
	 * @return void
	 */
	public function consumos()
	{
		form_validation(Consumo_toa::create()->consumos_validation);

		app_render_view('toa/consumos', [
			'combo_reportes' => Consumo_toa::create()->tipos_reporte_consumo,
			'reporte'        => Consumo_toa::create()->consumos_toa(request('sel_reporte'), request('fecha_desde'), request('fecha_hasta'), request('sort')),
		]);

	}


	// --------------------------------------------------------------------

	/**
	 * Despliega listado de peticiones de acuerdo a un criterio
	 *
	 * @param  string $tipo_reporte Tipo de reporte
	 * @param  mixed  $param1       Primer parametro de filtro
	 * @param  mixed  $param2       Segundo parametro de filtro
	 * @param  mixed  $param3       Tercer parametro de filtro
	 * @param  mixed  $param4       Cuarto parametro de filtro
	 * @return void
	 */
	public function ver_peticiones($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		$peticiones = Consumo_toa::create()->peticiones($tipo_reporte, $param1, $param2, $param3, $param4);

		$mapa = (new Google_map(['map_css' => 'height: 350px']))
			->add_peticiones_markers($peticiones)
			->create_map();

		app_render_view('toa/peticiones', [
			'reporte'      => Consumo_toa::create()->reporte_peticiones($peticiones),
			'google_maps'  => $mapa,
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle de una peticion
	 *
	 * @param  string $id_peticion Identificador de la peticion
	 * @return void
	 */
	public function detalle_peticion($id_peticion = NULL)
	{
		$id_peticion = ( ! $id_peticion) ? request('peticion') : $id_peticion;
		$peticion = Consumo_toa::create()->detalle_peticion($id_peticion);

		$mapa = (new Google_map(['map_css' => 'height: 350px']))
			->add_marker([
				'lat'   => array_get($peticion, 'peticion_toa.acoord_y'),
				'lng'   => array_get($peticion, 'peticion_toa.acoord_x'),
				'title' => array_get($peticion, 'peticion_toa.cname'),
			])
			->create_map();

		set_message(($id_peticion && ! $peticion) ? lang('toa_consumo_peticion_not_found') : '');

		app_render_view('toa/detalle_peticion', [
			'tipo_peticion' => strtoupper(substr($id_peticion, 0, 3)) === 'INC' ? 'repara' : 'instala',
			'reporte'       => $peticion,
			'google_maps'   => $mapa,
		]);
	}

}
// End of file Toa_consumos.php
// Location: ./controllers/Toa_consumos.php
