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


use Toa\Consumo_toa;

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
		$datos_peticiones = Consumo_toa::create()->peticiones($tipo_reporte, $param1, $param2, $param3, $param4);

		$this->load->library('googlemaps');
		$this->googlemaps->initialize([
			'map_css' => 'height: 350px',
		]);

		collect($datos_peticiones)->each(function($peticion) {
			$this->googlemaps->add_marker([
				'lat'   => $peticion['acoord_y'],
				'lng'   => $peticion['acoord_x'],
				'title' => $peticion['empresa'].' - '.$peticion['tecnico'].' - '.$peticion['referencia'],
			]);
		});

		app_render_view('toa/peticiones', [
			'reporte'      => Consumo_toa::create()->reporte_peticiones($datos_peticiones),
			'google_maps'  => $this->googlemaps->create_map(),
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
		$this->load->library('googlemaps');
		$this->googlemaps->initialize([
			'map_css' => 'height: 350px',
		]);

		$id_peticion = ( ! $id_peticion) ? request('peticion') : $id_peticion;
		$peticion = Consumo_toa::create()->detalle_peticion($id_peticion);

		$this->googlemaps->add_marker([
			'lat'   => array_get($peticion, 'peticion_toa.acoord_y'),
			'lng'   => array_get($peticion, 'peticion_toa.acoord_x'),
			'title' => array_get($peticion, 'peticion_toa.cname'),
		]);

		set_message(($id_peticion AND ! $peticion) ? $this->lang->line('toa_consumo_peticion_not_found') : '');

		app_render_view('toa/detalle_peticion', [
			'tipo_peticion' => strtoupper(substr($id_peticion, 0, 3)) === 'INC' ? 'repara' : 'instala',
			'reporte'       => $peticion,
			'google_maps'   => $this->googlemaps->create_map(),
		]);
	}

}
/* End of file Toa_consumos.php */
/* Location: ./application/controllers/Toa_consumos.php */
