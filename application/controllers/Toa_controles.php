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
class Toa_controles extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'oknsk82js7s';

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

		$this->set_menu_modulo([
			'consumos' => [
				'url'   => $this->router->class . '/consumos',
				'texto' => $this->lang->line('toa_controles_tecnicos'),
				'icon'  => 'user'
			],
			'materiales_consumidos' => [
				'url'   => $this->router->class . '/materiales_consumidos',
				'texto' => $this->lang->line('toa_controles_materiales_consumidos'),
				'icon'  => 'tv'
			],
			'materiales' => [
				'url'   => $this->router->class . '/materiales',
				'texto' => $this->lang->line('toa_controles_materiales'),
				'icon'  => 'file-text-o'
			],
			'asignaciones' => [
				'url'   => $this->router->class . '/asignaciones',
				'texto' => $this->lang->line('toa_controles_asignaciones'),
				'icon'  => 'archive'
			],
			'stock' => [
				'url'   => $this->router->class . '/stock',
				'texto' => $this->lang->line('toa_controles_stock'),
				'icon'  => 'signal'
			],
			'stock_tecnicos' => [
				'url'   => $this->router->class . '/stock_tecnicos',
				'texto' => $this->lang->line('toa_controles_stock_tecnicos'),
				'icon'  => 'truck'
			],
			'nuevos_tecnicos' => [
				'url'   => $this->router->class . '/nuevos_tecnicos',
				'texto' => $this->lang->line('toa_controles_nuevos_tecnicos'),
				'icon'  => 'users'
			],
			'clientes' => [
				'url'   => $this->router->class . '/clientes',
				'texto' => $this->lang->line('toa_controles_clientes'),
				'icon'  => 'users'
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
		return $this->consumos();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle de las actuaciones de los técnicos
	 *
	 * @return void
	 */
	public function consumos()
	{
		$this->form_validation
			->set_data(request())
			->set_rules($this->toa_model->controles_consumos_validation)
			->run();

		$empresa_toa = new Empresa_toa();
		$control = $this->toa_model->control_tecnicos(request('empresa'), request('mes'), request('filtro_trx'), request('dato'));
		$reporte = $this->reporte_mes->genera_reporte($control);

		app_render_view('toa/controles', [
			'menu_modulo'          => $this->get_menu_modulo('consumos'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_filtro_trx'     => $this->toa_model->get_combo_movimientos_consumo(),
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_consumo,
			'url_detalle_dia'      => 'toa_consumos/ver_peticiones/tecnicos',
			'anomes'               => request('mes'),
			'reporte'              => $reporte,
		]);

	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle de las asignaciones de material a los técnicos
	 *
	 * @return void
	 */
	public function asignaciones()
	{
		$this->load->model('toa_asignacion');

		$this->form_validation
			->set_data(request())
			->set_rules($this->toa_model->controles_consumos_validation)
			->run();

		$empresa_toa = new Empresa_toa();
		$control = $this->toa_asignacion->control_asignaciones(request('empresa'), request('mes'), request('filtro_trx'), request('dato'));
		$reporte = $this->reporte_mes->genera_reporte($control);

		app_render_view('toa/controles', [
			'menu_modulo'          => $this->get_menu_modulo('asignaciones'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_filtro_trx'     => $this->toa_asignacion->get_combo_movimientos_asignacion(),
			'combo_dato_desplegar' => $this->toa_asignacion->combo_unidades_asignacion,
			'url_detalle_dia'      => 'toa_asignaciones/ver_asignaciones/tecnicos',
			'anomes'               => request('mes'),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle de los materiales consumidos
	 *
	 * @return void
	 */
	public function materiales_consumidos()
	{
		$this->form_validation
			->set_data(request())
			->set_rules($this->toa_model->controles_consumos_validation)
			->run();

		$empresa_toa = new Empresa_toa();

		$control = $this->toa_model->materiales_consumidos(request('empresa'), request('mes'), request('filtro_trx'), request('dato'));
		$reporte = $this->reporte_mes->genera_reporte($control);


		app_render_view('toa/control_materiales_consumidos', [
			'menu_modulo'          => $this->get_menu_modulo('materiales_consumidos'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_filtro_trx'     => $this->toa_model->get_combo_movimientos_consumo(),
			'combo_dato_desplegar' => $this->toa_model->combo_unidades_materiales_consumidos,
			'url_detalle_dia'      => 'toa_consumos/ver_peticiones/material',
			'anomes'               => request('mes'),
			'reporte'              => $reporte,
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega stock de la contrata
	 *
	 * @return void
	 */
	public function stock()
	{
		$this->load->model('toa_stock');

		$this->form_validation
			->set_data(request())
			->set_rules($this->toa_stock->controles_stock_empresa_validation)
			->run();

		$empresa_toa = new Empresa_toa();

		app_render_view('toa/controles_stock', [
			'menu_modulo'          => $this->get_menu_modulo('stock'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_dato_desplegar' => $this->toa_stock->combo_unidades_stock,
			'url_detalle_dia'      => 'toa_controles/detalle_stock',
			'anomes'               => request('mes'),
			'stock_almacenes'      => $this->toa_stock->stock_almacenes(request('empresa'), request('mes'), request('dato')),
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega stock de los tecnicos de la contrata
	 *
	 * @return void
	 */
	public function stock_tecnicos()
	{
		$this->load->model('toa_stock');

		$this->form_validation
			->set_data(request())
			->set_rules($this->toa_stock->controles_stock_tecnicos_validation)
			->run();

		$empresa_toa = new Empresa_toa();

		app_render_view('toa/controles_stock_tecnicos', [
			'menu_modulo'          => $this->get_menu_modulo('stock_tecnicos'),
			'combo_empresas'       => $empresa_toa->find('list'),
			'combo_dato_desplegar' => $this->toa_stock->combo_unidades_stock,
			'combo_dato_mostrar'   => $this->toa_stock->combo_mostrar_stock_tecnicos,
			'url_detalle_dia'      => 'toa_controles/detalle_stock_tecnico',
			'anomes'               => request('mes'),
			'stock_tecnicos'       => $this->toa_stock->stock_tecnicos(request('empresa'), request('mes'), request('dato')),
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle stock de la contrata
	 *
	 * @param  string $fecha          Fecha del reporte de stock
	 * @param  string $centro_almacen Centro-Almacen del reporte
	 *
	 * @return void
	 */
	public function detalle_stock($fecha = NULL, $centro_almacen = NULL)
	{
		$this->load->model('toa_stock');

		app_render_view('toa/peticiones', [
			'reporte' => $this->toa_stock->detalle_stock_almacen($fecha, $centro_almacen),
			'google_maps' => '',
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle stock del tecnico de la contrata
	 *
	 * @param  string $fecha      Fecha del reporte de stock
	 * @param  string $id_tecnico Tecnico del reporte
	 *
	 * @return void
	 */
	public function detalle_stock_tecnico($fecha = NULL, $id_tecnico = NULL)
	{
		$this->load->model('toa_stock');

		app_render_view('toa/peticiones', [
			'reporte' => $this->toa_stock->detalle_stock_tecnico($fecha, $id_tecnico),
			'google_maps' => '',
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega detalle stock del tecnico de la contrata
	 *
	 * @return void
	 */
	public function materiales()
	{
		$this->form_validation
			->set_data(request())
			->set_rules($this->toa_model->controles_materiales_validation)
			->run();

		$empresa_toa = new Empresa_toa();
		$tipo_trabajo = new Tipo_trabajo_toa();


		app_render_view('toa/controles_materiales_tipo_trabajo', [
			'menu_modulo'              => $this->get_menu_modulo('materiales'),
			'combo_empresas'           => $empresa_toa->find('list'),
			'combo_tipos_trabajo'      => array_merge(['000' => 'Todos'], $tipo_trabajo->find('list', ['opc_ini' => FALSE])),
			'combo_dato_desplegar'     => $this->toa_model->combo_unidades_materiales_tipo_trabajo,
			'url_detalle_dia'          => 'toa_consumos/detalle_peticion',
			'anomes'                   => request('mes'),
			'materiales_tipos_trabajo' => $this->toa_model->materiales_tipos_trabajo(request('empresa'), request('mes'), request('tipo_trabajo'), request('dato')),
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Agrega nuevos técnicos
	 *
	 * @return void
	 */
	public function nuevos_tecnicos()
	{
		$msg_agregar = '';
		$nuevos_tecnicos = $this->toa_model->nuevos_tecnicos();

		if (request('agregar'))
		{
			$this->toa_model->agrega_nuevos_tecnicos($nuevos_tecnicos);
			$msg_agregar = print_message(sprintf($this->lang->line('toa_controles_tecnicos_agregados'), count($nuevos_tecnicos)));
		}

		app_render_view('toa/controles_nuevos_tecnicos', [
			'menu_modulo'     => $this->get_menu_modulo('nuevos_tecnicos'),
			'msg_agregar'     => $msg_agregar,
			'nuevos_tecnicos' => $nuevos_tecnicos,
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Actuaciones por clientes
	 *
	 * @return void
	 */
	public function clientes()
	{
		$this->form_validation
			->set_data(request())
			->set_rules($this->toa_model->controles_clientes_validation)
			->run();

		app_render_view('toa/controles_clientes', [
			'menu_modulo'     => $this->get_menu_modulo('clientes'),
			'url_detalle_dia' => 'toa_consumos/detalle_peticion',
			'clientes'        => $this->toa_model->clientes(request('cliente'), request('fecha_desde'), request('fecha_hasta'), request('dato')),
			'link_peticiones' => 'toa_consumos/ver_peticiones/clientes/'.str_replace('-','',request('fecha_desde')).'/'.str_replace('-','',request('fecha_hasta')).'/',
		]);
	}

}
/* End of file Toa_controles.php */
/* Location: ./application/controllers/Toa_controles.php */
