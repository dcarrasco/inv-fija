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
 * Clase Modelo Despachos
 * *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Despachos extends ORM_Model {

	/**
	 * Limite de facturas a recuperar
	 *
	 * @var integer
	 */
	public $limite_facturas = 5;

	public $rules = [
		[
			'field' => 'rut_retail',
			'label' => 'RUT Retail',
			'rules' => 'trim|required'
		],
		[
			'field' => 'modelos',
			'label' => 'Modelos de equipos',
			'rules' => 'trim|required'
		],
		[
			'field' => 'max_facturas',
			'label' => 'Maximo de facturas',
			'rules' => 'trim|required'
		],
	];

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera RUTs retail para poblar combo box
	 *
	 * @return array Arreglo con los ruts de los retail
	 */
	public function get_combo_rut_retail()
	{
		$arr_result = $this->db
			->select('rut as llave')
			->select_max("des_bodega + ' (' + rut + ')'", 'valor')
			->group_by('rut')
			->order_by('valor')
			->from(config('bd_despachos_pack'))
			->get()
			->result_array();

		return form_array_format($arr_result);
	}



	// --------------------------------------------------------------------

	/**
	 * Recupera cantidad de facturas para poblar combo box
	 *
	 * @return array Arreglo con las cantidades de facturas permitidas
	 */
	public function get_combo_cantidad_facturas()
	{
		return [
			'1' => 1,
			'3' => 3,
			'5' => 5,
			'10' => 10,
			'20' => 20,
			'30' => 30,
			'50' => 50,
		];
	}



	// --------------------------------------------------------------------

	/**
	 * Recupera listado de las ultimas facturas de un RUT y modelos de materiales
	 *
	 * @param string $rut     RUT del retail
	 * @param string $modelos Modelo de materiales a consultar
	 * @return array          Arreglo con los tipos de inventiaro
	 */
	public function get_listado_ultimas_facturas($rut = NULL, $modelos = NULL)
	{
		if ($rut AND $modelos)
		{
			return collect(explode("\r\n", $modelos))
				->map_with_keys(function($modelo) use ($rut) {
					return [$modelo => $this->get_ultimas_facturas($rut, $modelo)];
				});
		}
	}



	// --------------------------------------------------------------------

	/**
	 *
	 * @return array Arreglo con los tipos de inventiaro
	 */
	public function get_ultimas_facturas($rut = NULL, $modelo = NULL)
	{
		if ( ! $rut OR ! $modelo)
		{
			return;
		}

		$arr_campos_enc = ['operador', 'operador_c', 'rut', 'des_bodega', 'cod_cliente'];
		$arr_campos_det = ['alm', 'cmv', 'n_doc', 'referencia', 'cod_sap', 'texto_breve_material', 'lote', 'fecha', 'cant'];

		$facturas = collect(
			$this->db
				->limit($this->limite_facturas)
				->where('rut', $rut)
				->like('texto_breve_material', $modelo)
				->from(config('bd_despachos_pack'))
				->order_by('fecha', 'desc')
				->get()->result_array()
		);

		$facturas_enc = collect($facturas->first())->only($arr_campos_enc)->all();

		$facturas_det = $facturas->map(function($factura) use($arr_campos_det) {
			return collect($factura)->only($arr_campos_det)->all();
		})->all();

		return ['datos' => $facturas_enc, 'facturas' => $facturas_det];
	}

}
/* End of file Despachos.php */
/* Location: ./application/models/Despachos.php */
