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
 * Clase Controller Reportes de stock
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock_reportes_trazabilidad extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'j*&on238=B';

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

		$this->set_menu_modulo([
			'perm_consumo' => [
				'url' => $this->router->class . '/listado/perm_consumo',
				'texto' => 'Permanencia Consumo'
			],
			'det_consumo' => [
				'url' => $this->router->class . '/listado/det_consumo',
				'texto' => 'Detalle Series Consumo'
			],
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->listado('perm_consumo');
	}

	// --------------------------------------------------------------------

	/**
	 * Permite desplegar listados de trazabilidad de series
	 *
	 * @param  string $tipo   Tipo de reporte
	 * @param  string $param1 [description]
	 * @return void
	 */
	public function listado($tipo = 'perm_consumo', $param1 = '')
	{
		$this->load->model('reportestock_model');

		// define reglas para usar request
		$this->form_validation
			->set_rules('tipo_alm')
			->set_rules('estado_sap')
			->set_rules('incl_almacen', '', 'trim')
			->set_rules('incl_lote', '', 'trim')
			->set_rules('incl_estado', '', 'trim')
			->set_rules('incl_modelos', '', 'trim')
			->set_rules('order_by', '', 'trim')
			->set_rules('order_sort', '', 'trim')
			->run();

		$orden_campo   = request('order_by');
		$orden_tipo    = request('order_sort');
		$tipo_alm      = request('tipo_alm');
		$estado_sap    = request('estado_sap');
		$incl_almacen  = request('incl_almacen');
		$incl_lote     = request('incl_lote');
		$incl_estado   = request('incl_estado');
		$incl_modelos  = request('incl_modelos');

		$new_orden_tipo  = ($orden_tipo === 'ASC') ? 'DESC' : 'ASC';
		$view = 'listado';

		if ($tipo === 'perm_consumo')
		{
			$orden_campo = empty($orden_campo) ? 'tipo' : $orden_campo;
			$datos_hoja = $this->reportestock_model->get_reporte_perm_consumo($orden_campo, $orden_tipo, $tipo_alm, $estado_sap, $incl_almacen, $incl_lote, $incl_estado, $incl_modelos);

			$arr_campos = [];

			$arr_campos['tipo'] = ['titulo' => 'Tipo Almacen'];

			if ($incl_almacen === '1')
			{
				$arr_campos['centro'] = ['titulo' => 'Centro'];
				$arr_campos['almacen'] = ['titulo' => 'CodAlm'];
				$arr_campos['des_almacen'] = ['titulo' => 'Almacen'];
			}

			if ($incl_estado === '1')
			{
				$arr_campos['estado_sap'] = ['titulo' => 'Estado SAP'];
			}

			if ($incl_modelos === '1')
			{
				$arr_campos['material'] = ['titulo' => 'CodMat'];
				$arr_campos['des_material'] = ['titulo' => 'Material'];
			}

			if ($incl_lote === '1')
			{
				$arr_campos['lote'] = ['titulo' => 'Lote'];
			}

			$arr_campos['m030']   = ['titulo' => '000-030', 'class' => 'text-center', 'tipo' => 'numero'];
			$arr_campos['m060']   = ['titulo' => '031-060', 'class' => 'text-center', 'tipo' => 'numero'];
			$arr_campos['m090']   = ['titulo' => '061-090', 'class' => 'text-center', 'tipo' => 'numero'];
			$arr_campos['m180']   = ['titulo' => '091-180', 'class' => 'text-center', 'tipo' => 'numero'];
			$arr_campos['mas180'] = ['titulo' => '+181', 'class' => 'text-center', 'tipo' => 'numero'];
			$arr_campos['total']  = ['titulo' => 'Total', 'class' => 'text-center', 'tipo' => 'numero'];
		}
		elseif ($tipo === 'det_consumo')
		{
			$orden_campo = empty($orden_campo) ? 'tipo' : $orden_campo;
			$datos_hoja = $this->reportestock_model->get_detalle_series_consumo($tipo_alm);

			$arr_campos = [];

			$arr_campos['tipo']         = ['titulo' => 'Tipo Almacen'];
			$arr_campos['centro']       = ['titulo' => 'Centro'];
			$arr_campos['almacen']      = ['titulo' => 'Cod Alm'];
			$arr_campos['des_almacen']  = ['titulo' => 'Almacen'];
			$arr_campos['material']     = ['titulo' => 'Cod Mat'];
			$arr_campos['des_material'] = ['titulo' => 'Material'];
			$arr_campos['lote']         = ['titulo' => 'Lote'];
			$arr_campos['dias']         = ['titulo' => 'Permanencia'];
			$arr_campos['serie']        = ['titulo' => 'Serie'];

		}

		$arr_link_campos = [];
		$arr_link_sort   = [];
		$arr_img_orden   = [];
		foreach ($arr_campos as $campo => $valor)
		{
			$arr_link_campos[$campo] = $campo;
			$arr_link_sort[$campo] = $campo === $orden_campo ? $new_orden_tipo : 'ASC';
			$arr_img_orden[$campo] = ($campo === $orden_campo)
				? '<span class="glyphicon ' . (($orden_tipo === 'ASC') ? 'glyphicon-circle-arrow-up' : 'glyphicon-circle-arrow-down')
					. '"></span>'
				: '';
		}

		app_render_view('stock_sap/reporte_trazabilidad', [
			'menu_modulo'     => $this->get_menu_modulo($tipo),
			'datos_hoja'      => $datos_hoja,
			'tipo_reporte'    => $view,
			'nombre_reporte'  => $tipo,
			'filtro_dif'      => '',
			'arr_campos'      => $arr_campos,
			'arr_link_campos' => $arr_link_campos,
			'arr_link_sort'   => $arr_link_sort,
			'arr_img_orden'   => $arr_img_orden,
			'combo_tipo_alm'  => $this->reportestock_model->combo_tipo_alm_consumo(),
			//'combo_estado_sap' => $this->reportestock_model->combo_estado_sap(),
		]);
	}

}
/* End of file stock_reportes_trazabilidad.php */
/* Location: ./application/controllers/stock_reportes_trazabilidad.php */
