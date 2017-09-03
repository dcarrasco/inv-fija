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


use Toa\Uso;
use Toa\Empresa_toa;

/**
 * Clase Controller Uso TOA
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_uso extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'j8snpo92bpwt';

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
			'reporte' => [
				'url'   => "{$this->router->class}/uso",
				'texto' => lang('uso_menu_reporte'),
				'icon'  => 'bar-chart'
			],
			'genera' => [
				'url'   => "{$this->router->class}/genera",
				'texto' => lang('uso_menu_genera'),
				'icon'  => 'list'
			],
			'evolucion_tecnico' => [
				'url'   => "{$this->router->class}/evolucion_tecnico",
				'texto' => lang('uso_menu_evolucion_tecnico'),
				'icon'  => 'user'
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
		return $this->uso();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega detalle del log del gestor DTH
	 *
	 * @return void
	 */
	public function uso()
	{
		form_validation(Uso::create()->rules_uso);

		app_render_view('toa/uso', [
			'menu_modulo'    => $this->get_menu_modulo('reporte'),
			'data_uso'       => Uso::create()->get(request('mes'), request('mostrar')),
			'combo_mostrar'  => Uso::create()->combo_mostrar,
			'combo_tipo_pet' => Uso::create()->tipos_pet,
			'combo_empresa'  => Empresa_toa::create()->find('list'),
			'combo_agencia'  => Uso::create()->combo_agencia(request('mes'), request('empresa')),
			'combo_tecnico'  => Uso::create()->combo_tecnico(request('mes'), request('empresa'), request('agencia')),
			'tipo_chart'     => Uso::create()->tipo_chart(request('mostrar')) === Uso::BUBBLE_CHART ? 'BubbleChart' : 'ScatterChart',
			'mes'            => request('mes'),
		]);

	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza combobox agencias
	 *
	 * @param  string $empresa Empresa a buscar
	 * @param  string $mes     Mes a buscar
	 * @return void
	 */
	public function ajax_combo_agencia($mes = NULL, $empresa = NULL)
	{
		return $this->output
			->set_content_type('text')
			->set_output(form_print_options(Uso::create()->combo_agencia($mes, $empresa)));

	}

	// --------------------------------------------------------------------

	/**
	 * Actualiza combobox tecnicos
	 *
	 * @param  string $empresa Empresa a buscar
	 * @param  string $mes     Mes a buscar
	 * @return void
	 */
	public function ajax_combo_tecnico($mes = NULL, $empresa = NULL)
	{
		return $this->output
			->set_content_type('text')
			->set_output(form_print_options(Uso::create()->combo_tecnico($mes, $empresa)));

	}


	// --------------------------------------------------------------------

	/**
	 * Formulario genera reporte de uso TOA
	 *
	 * @return void
	 */
	public function genera()
	{
		app_render_view('toa/genera_uso', [
			'menu_modulo' => $this->get_menu_modulo('genera'),
			'url_form'    => site_url("{$this->router->class}/do_genera"),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Genera reporte de uso TOA
	 *
	 * @return void
	 */
	public function do_genera()
	{
		route_validation(Uso::create()->rules_genera);

		$mensaje = Uso::create()->genera_reporte(request('mes'));
		set_message($mensaje);

		redirect("{$this->router->class}/genera");
	}

	// --------------------------------------------------------------------

	public function evolucion_tecnico()
	{
		form_validation(Uso::create()->rules_evolucion_uso_tecnico);

		app_render_view('toa/evolucion_uso_tecnico', [
			'menu_modulo'    => $this->get_menu_modulo('evolucion_tecnico'),
			'combo_tipo_pet' => Uso::create()->tipos_pet,
			'data_uso'       => Uso::create()->evolucion_tecnico(request('tecnico')),
		]);

	}

}
/* End of file Toa_uso.php */
/* Location: ./application/controllers/Toa_uso.php */
