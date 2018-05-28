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

use Inventario\catalogo;
use Inventario\Inventario;
use Inventario\detalle_inventario;
use Inventario\inventario_reporte;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Controller Análisis de inventarios
 * *
 * @category CodeIgniter
 * @package  Inventario
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Inventario_analisis extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'analisis';

	/**
	 * Menu de opciones del modulo
	 *
	 * @var  array
	 */
	public $menu_opciones = [
		'ajustes' => ['url'=>'{{route}}/ajustes', 'texto'=>'lang::inventario_menu_ajustes', 'icon'=>'wrench'],
		'sube_stock' => ['url'=>'{{route}}/sube_stock', 'texto'=>'lang::inventario_menu_upload', 'icon' =>'cloud-upload'],
		'imprime_inventario' => ['url'=>'{{route}}/imprime_inventario', 'texto'=>'lang::inventario_menu_print', 'icon' =>'print'],
		'actualiza_precios' => ['url'=>'{{route}}/actualiza_precios', 'texto'=>'lang::inventario_menu_act_precios', 'icon' =>'usd'],
	];

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'inventario';

	/**
	 * Identificador del inventario
	 *
	 * @var  integer
	 */
	private $_id_inventario = 0;

	/**
	 * Nombre del inventario
	 *
	 * @var  string
	 */
	private $_nombre_inventario = '';

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
		$this->ajustes();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los datos del inventario activo
	 *
	 * @return nada
	 */
	private function _get_datos_inventario()
	{
		$this->_id_inventario     = Inventario::create()->get_id_inventario_activo();
		$this->_nombre_inventario = Inventario::create()->find_id($this->_id_inventario)->nombre;
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega la página de ajustes de inventarios
	 *
	 * @return void
	 */
	public function ajustes()
	{
		$this->_get_datos_inventario();

		$pagina = request('page', 1);
		$ocultar_reg = request('ocultar_reg') ? 1 : 0;

		$detalle_inventario = Detalle_inventario::create();

		app_render_view('inventario/ajustes', [
			'menu_modulo'     => $this->get_menu_modulo('ajustes'),
			'inventario'      => $this->_id_inventario.' - '.$this->_nombre_inventario,
			'detalle_ajustes' => $detalle_inventario->get_ajustes($this->_id_inventario, $ocultar_reg),
			'links_paginas'   => $detalle_inventario->get_pagination_links(),
			'ocultar_reg'     => $ocultar_reg,
			'url_form'        => site_url("{$this->router->class}/update_ajustes".url_params()),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega la página de ajustes de inventarios
	 *
	 * @return void
	 */
	public function update_ajustes()
	{
		// recupera el detalle de registros con diferencias
		$this->_get_datos_inventario();
		$pagina      = request('page', 1);
		$ocultar_reg = request('ocultar_reg') ? 1 : 0;
		$detalles    = Detalle_inventario::create()->get_ajustes($this->_id_inventario, $ocultar_reg, $pagina);

		route_validation(Detalle_inventario::create()->rules_ajustes($detalles));

		$cant_modif = Detalle_inventario::create()->update_ajustes($detalles);
		set_message(($cant_modif > 0) ? sprintf(lang('inventario_adjust_msg_save'), $cant_modif)
			: '');

		redirect("{$this->router->class}/ajustes".url_params());
	}

	// --------------------------------------------------------------------

	/**
	 * Permite subir un archivo con el stock a cargar a un inventario
	 *
	 * @return void
	 */
	public function sube_stock()
	{
		$this->_get_datos_inventario();

		app_render_view('inventario/sube_stock_form', [
			'menu_modulo'       => $this->get_menu_modulo('sube_stock'),
			'inventario_id'     => $this->_id_inventario,
			'inventario_nombre' => $this->_nombre_inventario,
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Realiza la subida de un archivo de stock
	 *
	 * @return void
	 */
	public function upload()
	{
		route_validation(Detalle_inventario::create()->rules_upload());

		$this->_get_datos_inventario();
		$upload_path = '../upload/';
		$upload_file = 'sube_stock.txt';

		unlink($upload_path.$upload_file);
		$this->load->library('upload', [
			'upload_path'   => $upload_path,
			'file_name'     => $upload_file,
			'allowed_types' => 'txt|cvs',
			'max_size'      => '2000',
			'overwrite'     => TRUE,
		]);

		if ( ! $this->upload->do_upload('upload_file'))
		{
			$this->session->set_flashdata('errors', [
				'upload_file' => $this->upload->display_errors(),
			]);

			redirect($this->input->server('HTTP_REFERER'));
		}

		$upload_data = $this->upload->data();
		$archivo_cargado = $upload_data['full_path'];

		$inventario = new Inventario($this->_id_inventario);
		$inventario->borrar_detalle_inventario();
		$res_procesa_archivo = $inventario->cargar_datos_archivo($archivo_cargado);

		$script_carga = $res_procesa_archivo['script'];
		$regs_ok      = $res_procesa_archivo['regs_ok'];
		$regs_error   = $res_procesa_archivo['regs_error'];
		$msj_error    = ($regs_error > 0)
			? '<br><div class="error round">'.$res_procesa_archivo['msj_termino'].'</div>'
			: '';

		app_render_view('inventario/sube_stock_do_upload', [
			'menu_modulo'       => $this->get_menu_modulo('sube_stock'),
			'inventario_id'     => $this->_id_inventario,
			'inventario_nombre' => $this->_nombre_inventario,
			'script_carga'      => $script_carga,
			'show_script_carga' => TRUE,
			'regs_ok'           => $regs_ok,
			'regs_error'        => $regs_error,
			'msj_error'         => $msj_error,
		]);

	}

	// --------------------------------------------------------------------

	/**
	 * Recibe linea de detalle de inventario por post, y lo graba
	 *
	 * @return void
	 */
	public function inserta_linea_archivo()
	{
		return Detalle_inventario::create(request())->grabar();
	}

	// --------------------------------------------------------------------

	/**
	 * Permite imprimir el detalle del inventario
	 *
	 * @return void
	 */
	public function imprime_inventario()
	{
		$this->_get_datos_inventario();
		$inventario = (new Inventario)->find_id($this->_id_inventario);

		app_render_view('inventario/imprime_inventario', [
			'menu_modulo'       => $this->get_menu_modulo('imprime_inventario'),
			'inventario_id'     => $this->_id_inventario,
			'inventario_nombre' => $this->_nombre_inventario,
			'max_hoja'          => $inventario->get_max_hoja_inventario(),
			'url_form'          => site_url("{$this->router->class}/imprime_inventario_validate"),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega hojas del inventario
	 *
	 * @return void
	 */
	public function imprime_inventario_validate()
	{
		route_validation(Inventario_reporte::create()->rules_imprime_inventario());

		redirect($this->router->class.'/imprime_hojas/'
			.request('pag_desde').'/'
			.request('pag_hasta').'/'
			.((request('oculta_stock_sap') === 'oculta_stock_sap') ? 1 : 0).'/'
			.time()
		);


	}
	// --------------------------------------------------------------------

	/**
	 * Genera las hojas a imprimir
	 *
	 * @param  integer $hoja_desde       Hoja inicial a imprimir
	 * @param  integer $hoja_hasta       Hoja final a imprimir
	 * @param  integer $oculta_stock_sap Permite ocultar la columna con el stock de SAP
	 * @return void
	 */
	public function imprime_hojas($hoja_desde = 1, $hoja_hasta = 1, $oculta_stock_sap = 0)
	{
		$this->_get_datos_inventario();
		$nombre_inventario = $this->_nombre_inventario;

		$hoja_desde = ($hoja_desde < 1) ? 1 : $hoja_desde;
		$hoja_hasta = ($hoja_hasta < $hoja_desde) ? $hoja_desde : $hoja_hasta;

		$this->load->view('inventario/inventario_print_head');

		collect(range($hoja_desde, $hoja_hasta))
			->each(function($hoja) use ($oculta_stock_sap, $nombre_inventario) {
				$this->load->view('inventario/inventario_print_body', [
					'datos_hoja' => Detalle_inventario::create()->get_hoja_inventario($this->_id_inventario, $hoja),
					'hoja' => $hoja,
					'oculta_stock_sap' => $oculta_stock_sap,
					'nombre_inventario' => $nombre_inventario,
				]);
			});

		$this->load->view('inventario/inventario_print_footer');
	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza los precios del catálogo
	 *
	 * @return void
	 */
	public function actualiza_precios()
	{
		app_render_view('inventario/actualiza_precios', [
			'menu_modulo'      => $this->get_menu_modulo('actualiza_precios'),
			'update_status'    => request('actualizar') ? ' disabled' : '',
			'cant_actualizada' => request('actualizar')	? Catalogo::create()->actualiza_precios() : 0,
		]);
	}

}
// End of file Inventario_analisis.php
// Location: ./controllers/Inventario_analisis.php
