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
class Toa_consumos extends CI_Controller {

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
		$this->load->model('toa_model');
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
		$this->form_validation->set_data($this->input->get());
		$this->form_validation->set_rules($this->toa_model->consumos_validation);
		$this->form_validation->run();

		$datos = array(
			'combo_reportes' => $this->toa_model->tipos_reporte_consumo,
			'reporte'        => $this->toa_model->consumos_toa($this->input->get('sel_reporte'), $this->input->get('fecha_desde'), $this->input->get('fecha_hasta'), $this->input->get('sort')),
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
	 * @return void
	 */
	public function ver_peticiones($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		$datos_peticiones = $this->toa_model->peticiones_toa($tipo_reporte, $param1, $param2, $param3, $param4);

		$this->load->library('googlemaps');
		$this->googlemaps->initialize(array(
			'map_css' => 'height: 350px',
		));

		if (count($datos_peticiones))
		{
			foreach ($datos_peticiones as $peticion)
			{
				$this->googlemaps->add_marker(array(
					'lat'   => $peticion['acoord_y'],
					'lng'   => $peticion['acoord_x'],
					'title' => $peticion['referencia'],
			));
			}
		}

		$datos = array(
			'reporte'      => $this->toa_model->reporte_peticiones_toa($datos_peticiones),
			'google_maps'  => $this->googlemaps->create_map(),
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
	public function detalle_peticion($peticion = NULL)
	{
		$this->load->library('googlemaps');
		$this->googlemaps->initialize(array(
			'map_css' => 'height: 350px',
		));

		$arr_peticiones = $this->toa_model->detalle_peticion_toa($peticion);

		if (count($arr_peticiones))
		{
			$this->googlemaps->add_marker(array(
				'lat'   => $arr_peticiones[0]['acoord_y'],
				'lng'   => $arr_peticiones[0]['acoord_x'],
				'title' => $arr_peticiones[0]['cname'],
			));
		}

		$datos = array(
			'reporte'     => $arr_peticiones,
			'google_maps' => $this->googlemaps->create_map(),
		);

		app_render_view('toa/detalle_peticion', $datos);
	}


}
/* End of file Toa_consumos.php */
/* Location: ./application/controllers/Toa_consumos.php */