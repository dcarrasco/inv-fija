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

use Toa\Stock;
use Toa\Ciudad_toa;
use Toa\Consumo_toa;
use Toa\Empresa_toa;
use Toa\Tecnico_toa;
use Toa\Asignacion_toa;
use Toa\Tipo_trabajo_toa;
use Toa\Empresa_ciudad_toa;

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
		$this->lang->load('toa');

		$this->set_menu_modulo([
			'consumos' => [
				'url'   => "{$this->router->class}/consumos",
				'texto' => lang('toa_controles_tecnicos'),
				'icon'  => 'user'
			],
			'materiales_consumidos' => [
				'url'   => "{$this->router->class}/materiales_consumidos",
				'texto' => lang('toa_controles_materiales_consumidos'),
				'icon'  => 'tv'
			],
			'materiales' => [
				'url'   => "{$this->router->class}/materiales",
				'texto' => lang('toa_controles_materiales'),
				'icon'  => 'file-text-o'
			],
			'asignaciones' => [
				'url'   => "{$this->router->class}/asignaciones",
				'texto' => lang('toa_controles_asignaciones'),
				'icon'  => 'archive'
			],
			'stock' => [
				'url'   => "{$this->router->class}/stock",
				'texto' => lang('toa_controles_stock'),
				'icon'  => 'signal'
			],
			'stock_tecnicos' => [
				'url'   => "{$this->router->class}/stock_tecnicos",
				'texto' => lang('toa_controles_stock_tecnicos'),
				'icon'  => 'truck'
			],
			'nuevos_tecnicos' => [
				'url'   => "{$this->router->class}/nuevos_tecnicos",
				'texto' => lang('toa_controles_nuevos_tecnicos'),
				'icon'  => 'users'
			],
			'tecnicos_sin_ciudad' => [
				'url'   => "{$this->router->class}/tecnicos_sin_ciudad",
				'texto' => lang('toa_controles_ciudades_tecnicos'),
				'icon'  => 'map-marker'
			],
			'clientes' => [
				'url'   => "{$this->router->class}/clientes",
				'texto' => lang('toa_controles_clientes'),
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
		form_validation(Consumo_toa::create()->rules_controles_consumos);

		app_render_view('toa/controles', [
			'menu_modulo'          => $this->get_menu_modulo('consumos'),
			'combo_empresas'       => Empresa_toa::create()->find('list'),
			'combo_filtro_trx'     => Consumo_toa::create()->combo_movimientos_consumo(),
			'combo_dato_desplegar' => Consumo_toa::create()->combo_unidades_consumo,
			'url_detalle_dia'      => 'toa_consumos/ver_peticiones/tecnicos',
			'anomes'               => request('mes'),
			'control'              => Consumo_toa::create()->control_tecnicos(request('empresa'), request('mes'), request('filtro_trx'), request('dato')),
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
		form_validation(Consumo_toa::create()->rules_controles_consumos);

		app_render_view('toa/controles', [
			'menu_modulo'          => $this->get_menu_modulo('asignaciones'),
			'combo_empresas'       => Empresa_toa::create()->find('list'),
			'combo_filtro_trx'     => Asignacion_toa::create()->get_combo_movimientos_asignacion(),
			'combo_dato_desplegar' => Asignacion_toa::create()->combo_unidades_asignacion,
			'url_detalle_dia'      => 'toa_asignaciones/ver_asignaciones/tecnicos',
			'anomes'               => request('mes'),
			'control'              => Asignacion_toa::create()->control_asignaciones(request('empresa'), request('mes'), request('filtro_trx'), request('dato')),
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
		form_validation(Consumo_toa::create()->rules_controles_consumos);

		app_render_view('toa/control_materiales_consumidos', [
			'menu_modulo'          => $this->get_menu_modulo('materiales_consumidos'),
			'combo_empresas'       => Empresa_toa::create()->find('list'),
			'combo_filtro_trx'     => Consumo_toa::create()->combo_movimientos_consumo(),
			'combo_dato_desplegar' => Consumo_toa::create()->combo_unidades_consumo,
			'url_detalle_dia'      => 'toa_consumos/ver_peticiones/material',
			'anomes'               => request('mes'),
			'control'              => Consumo_toa::create()->materiales_consumidos(request('empresa'), request('mes'), request('filtro_trx'), request('dato')),
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
		form_validation(Stock::create()->rules_stock_empresa);

		app_render_view('toa/controles_stock', [
			'menu_modulo'          => $this->get_menu_modulo('stock'),
			'combo_empresas'       => Empresa_toa::create()->find('list'),
			'combo_dato_desplegar' => Stock::create()->combo_unidades_stock,
			'url_detalle_dia'      => 'toa_controles/detalle_stock',
			'anomes'               => request('mes'),
			'stock_almacenes'      => Stock::create()->stock_almacenes(request('empresa'), request('mes'), request('dato')),
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
		form_validation(Stock::create()->rules_stock_tecnicos);

		app_render_view('toa/controles_stock_tecnicos', [
			'menu_modulo'          => $this->get_menu_modulo('stock_tecnicos'),
			'combo_empresas'       => Empresa_toa::create()->find('list'),
			'combo_dato_desplegar' => Stock::create()->combo_unidades_stock,
			'combo_dato_mostrar'   => Stock::create()->combo_mostrar_stock_tecnicos,
			'url_detalle_dia'      => 'toa_controles/detalle_stock_tecnico',
			'anomes'               => request('mes'),
			'stock_tecnicos'       => Stock::create()->stock_tecnicos(request('empresa'), request('mes'), request('dato')),
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
		app_render_view('toa/peticiones', [
			'reporte' => Stock::create()->detalle_stock_almacen($fecha, $centro_almacen),
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
		app_render_view('toa/peticiones', [
			'reporte' => Stock::create()->detalle_stock_tecnico($fecha, $id_tecnico),
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
		form_validation(Consumo_toa::create()->rules_controles_materiales);

		app_render_view('toa/controles_materiales_tipo_trabajo', [
			'menu_modulo'              => $this->get_menu_modulo('materiales'),
			'combo_empresas'           => Empresa_toa::create()->find('list'),
			'combo_tipos_trabajo'      => collect(['000' => 'Todos'])->merge(Tipo_trabajo_toa::create()->find('list', ['opc_ini' => FALSE]))->all(),
			'combo_dato_desplegar'     => Consumo_toa::create()->combo_unidades_materiales_tipo_trabajo,
			'url_detalle_dia'          => 'toa_consumos/detalle_peticion',
			'anomes'                   => request('mes'),
			'materiales_tipos_trabajo' => Consumo_toa::create()->materiales_tipos_trabajo(request('empresa'), request('mes'), request('tipo_trabajo'), request('dato')),
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
		$nuevos_tecnicos = Tecnico_toa::create()->nuevos_tecnicos();

		app_render_view('toa/controles_nuevos_tecnicos', [
			'menu_modulo'     => $this->get_menu_modulo('nuevos_tecnicos'),
			'url_form'        => site_url("{$this->router->class}/add_nuevos_tecnicos"),
			'nuevos_tecnicos' => $nuevos_tecnicos,
		]);
	}


	// --------------------------------------------------------------------

	/**
	 * Agrega nuevos técnicos
	 *
	 * @return void
	 */
	public function add_nuevos_tecnicos()
	{
		$nuevos_tecnicos = Tecnico_toa::create()
			->nuevos_tecnicos()
			->each(function($tecnico) {
				$tecnico->grabar();
			})->count();

		set_message(sprintf(lang('toa_controles_tecnicos_agregados'), $nuevos_tecnicos));

		redirect("{$this->router->class}/nuevos_tecnicos");
	}


	// --------------------------------------------------------------------

	/**
	 * Actualiza ciudades para técnicos sin ciudad
	 *
	 * @return void
	 */
	public function tecnicos_sin_ciudad()
	{
		$msg_agregar = '';
		$tecnicos_sin_ciudad = Tecnico_toa::create()->tecnicos_sin_ciudad();

		$empresas = collect($tecnicos_sin_ciudad)
			->pluck('contractor_company')
			->unique()
			->map_with_keys(function($id_empresa) {
				return [$id_empresa => Ciudad_toa::create()->find('list', [
					'conditions' => ['id_ciudad' => Empresa_ciudad_toa::create()->ciudades_por_empresa($id_empresa)]
				])];
			})->all();

		app_render_view('toa/controles_tecnicos_sin_ciudad', [
			'menu_modulo' => $this->get_menu_modulo('tecnicos_sin_ciudad'),
			'msg_agregar' => $msg_agregar,
			'url_form'    => site_url("{$this->router->class}/actualiza_tecnicos_sin_ciudad"),
			'tecnicos'    => $tecnicos_sin_ciudad,
			'empresas'    => $empresas,
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza los técnicos sin ciudad
	 *
	 * @return redirect
	 */
	public function actualiza_tecnicos_sin_ciudad()
	{
		$tecnicos = collect(request()->get('tecnico'));
		$ciudades = collect(request()->get('ciudad'));
		$empresas = collect(request()->get('empresa'));

		$modificar = $tecnicos->map_with_keys(function($id_tecnico, $id_registro) use ($ciudades, $empresas) {
			return [$id_tecnico => [
				'ciudad' => $ciudades->get($id_registro),
				'empresa'=> $empresas->get($id_registro),
			]];
		})->filter(function($update_tecnico) {
			return $update_tecnico['ciudad'] !== '';
		})->each(function($update_tecnico, $id_tecnico) {
			$tecnico = new Tecnico_toa($id_tecnico);
			$tecnico->fill(['id_ciudad' => $update_tecnico['ciudad'], 'id_empresa' => $update_tecnico['empresa']]);
			$tecnico->grabar();
		})->count();

		set_message(sprintf(lang('toa_controles_ciudades_agregadas'), $modificar));

		redirect("{$this->router->class}/tecnicos_sin_ciudad");
	}

	// --------------------------------------------------------------------

	/**
	 * Actuaciones por clientes
	 *
	 * @return void
	 */
	public function clientes()
	{
+		form_validation(Consumo_toa::create()->rules_controles_clientes);

		app_render_view('toa/controles_clientes', [
			'menu_modulo'     => $this->get_menu_modulo('clientes'),
			'url_detalle_dia' => 'toa_consumos/detalle_peticion',
			'clientes'        => Consumo_toa::create()->consumo_clientes(request('cliente'), request('fecha_desde'), request('fecha_hasta'), request('dato')),
			'link_peticiones' => 'toa_consumos/ver_peticiones/clientes/'.str_replace('-','',request('fecha_desde')).'/'.str_replace('-','',request('fecha_hasta')).'/',
		]);
	}

}

/* End of file Toa_controles.php */
/* Location: ./application/controllers/Toa_controles.php */
