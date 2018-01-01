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

if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Clase Controller Revision de facturas despachos a retail
 * *
 * @category CodeIgniter
 * @package  DespachosRetail
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Despachos_retail extends Controller_base {

	/**
	 * Llave de identificación del módulo
	 *
	 * @var  string
	 */
	public $llave_modulo = 'iojms9128_$$21';

	/**
	 * Lenguajes a cargar
	 *
	 * @var  array|string
	 */
	public $lang_controller = 'despachos';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
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
		$this->retail();
	}

	// --------------------------------------------------------------------

	/**
	 * Muestra listado de los despachos a un retails
	 *
	 * @param  integer $rut_retail Numero de la RUT del retail a desplegar
	 * @return void
	 */
	public function retail($rut_retail = NULL)
	{
		$arr_facturas  = [];
		$despachos = new Despachos;

		if (form_validation($despachos->rules))
		{
			$despachos->limite_facturas = request('max_facturas', $despachos->limite_facturas);
			$arr_facturas = $despachos->get_listado_ultimas_facturas(request('rut_retail'), request('modelos'));
		}

		app_render_view('despachos/retail', [
			'combo_retail'       => $despachos->get_combo_rut_retail(),
			'combo_max_facturas' => $despachos->get_combo_cantidad_facturas(),
			'facturas'           => $arr_facturas,
			'limite_facturas'    => $despachos->limite_facturas,
		]);
	}

}
// End of file Despachos_retail.php
// Location: ./controllers/Despachos_retail.php
