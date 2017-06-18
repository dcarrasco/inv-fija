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
 * Clase Controller Reporte de consumos TOA
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_asignaciones extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = '982jbngd762ks';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->model('toa_model');
		$this->load->model('toa_consumo');
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
		return $this->asignaciones();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle del log del gestor DTH
	 *
	 * @return void
	 */
	public function asignaciones()
	{
		$this->form_validation
			->set_data(request())
			->set_rules($this->toa_consumo->consumos_validation)
			->run();

		$datos = array(
			'combo_reportes' => $this->toa_model->tipos_reporte_asignaciones,
			'reporte'        => $this->toa_model->asignaciones_toa(request('sel_reporte'), request('fecha_desde'), request('fecha_hasta'), request('order_by'), request('order_sort')),
		);

		app_render_view('toa/consumos', $datos);
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
	public function ver_asignaciones($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		$datos = array(
			'reporte' => $this->toa_model->documentos_asignaciones_toa($tipo_reporte, $param1, $param2, $param3, $param4),
		);

		app_render_view('toa/peticiones', $datos);

	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle de una peticion
	 *
	 * @param  string $fecha    Fecha de la peticion
	 * @param  string $peticion Identificador de la peticion
	 * @return void
	 */
	public function detalle_asignacion($fecha = NULL, $peticion = NULL)
	{
		$datos = array(
			'reporte' => $this->toa_model->detalle_asignacion_toa($fecha, $peticion),
		);

		app_render_view('toa/detalle_asignacion', $datos);

	}


}
/* End of file Toa_consumos.php */
/* Location: ./application/controllers/Toa_consumos.php */
