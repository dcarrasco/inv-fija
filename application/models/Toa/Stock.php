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

namespace Toa;

use Reporte;
use Toa\Tecnico_toa;
use Model\Orm_model;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Modelo Stock TOA
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock extends ORM_Model {

	use Reporte;

	/**
	 * Arreglo con validación formulario controles stock empresa
	 *
	 * @var array
	 */
	public $rules_stock_empresa = [
		[
			'field' => 'empresa',
			'label' => 'Empresa',
			'rules' => 'required',
		],
		[
			'field' => 'mes',
			'label' => 'Mes',
			'rules' => 'required',
		],
		[
			'field' => 'dato',
			'label' => 'Dato a desplegar',
			'rules' => 'required',
		],
	];

	/**
	 * Arreglo con validación formulario controles stock tecnicos
	 *
	 * @var array
	 */
	public $rules_stock_tecnicos = [
		[
			'field' => 'empresa',
			'label' => 'Empresa',
			'rules' => 'required',
		],
		[
			'field' => 'mes',
			'label' => 'Mes',
			'rules' => 'required',
		],
		[
			'field' => 'dato',
			'label' => 'Dato a desplegar',
			'rules' => 'required',
		],
		[
			'field' => 'mostrar',
			'label' => 'Mostrar tecnicos',
			'rules' => 'required',
		],
	];

	/**
	 * Combo de unidades reporte control stock
	 *
	 * @var array
	 */
	public $combo_unidades_stock = [
		'monto'        => 'Suma de montos',
		'unidades'     => 'Suma de unidades',
	];

	/**
	 * Combo para mostrar todos los tecnivos o solo los con datos
	 *
	 * @var array
	 */
	public $combo_mostrar_stock_tecnicos = [
		'todos' => 'Todos los t&eacute;cnicos',
		'datos' => 'S&oacute;lo con datos',
	];

	/**
	 * Campos para reporte
	 *
	 * @var array
	 */
	protected $campos = [
		'fecha_stock' => ['titulo' => 'Fecha', 'tipo' => 'fecha'],
		'tipo'        => ['titulo' => 'Tipo Almac&eacute;n'],
		'centro'      => ['titulo' => 'Centro'],
		'almacen'     => ['titulo' => 'Almac&eacute;n'],
		'des_almacen' => ['titulo' => 'Desc Almac&eacute;n'],
		'acreedor'    => ['titulo' => 'Cod T&eacute;cnico'],
		'tecnico'     => ['titulo' => 'Nombre T&eacute;cnico'],
		'material'    => ['titulo' => 'Material'],
		'descripcion' => ['titulo' => 'Desc Material'],
		'lote'        => ['titulo' => 'Lote'],
		'umb'         => ['titulo' => 'Unidad'],
		'estado'      => ['titulo' => 'Estado'],
		'cantidad'    => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
		'valor'       => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
	];


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
	 * Devuelve matriz de stock de empresas contratistas por dia
	 *
	 * @param  string $empresa        Empresa a consultar
	 * @param  string $anomes         Mes a consultar, formato YYYYMM
	 * @param  string $dato_desplegar Tipo de dato a desplegar
	 * @return array                  Arreglo con los datos del reporte
	 */
	public function stock_almacenes($empresa = NULL, $anomes = NULL, $dato_desplegar = 'monto')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$almacenes = collect($this->get_datos_almacenes($empresa));
		$stock = $this->result_to_month_table($this->get_datos_stock_almacenes($empresa, $anomes, $dato_desplegar));

		return $almacenes->map_with_keys(function($almacen) use ($stock) {
			return [$almacen['centro'].$almacen['cod_almacen'] => [
				'tipo'        => $almacen['tipo'],
				'centro'      => $almacen['centro'],
				'cod_almacen' => $almacen['cod_almacen'],
				'des_almacen' => $almacen['des_almacen'],
				'actuaciones' => $stock->get($almacen['centro'].$almacen['cod_almacen']),
				// 'con_datos'   => collect($stock->get($almacen['centro'].$almacen['cod_almacen']))->sum(),
			]];
		});
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera almacenes TOA de una empresa
	 *
	 * @param  string $empresa Empresa a recuperar los almacenes
	 * @return array           Arreglo con almacenes
	 */
	protected function get_datos_almacenes($empresa = NULL)
	{
		return $this->db
			->select('d.tipo')
			->select('a.centro')
			->select('a.cod_almacen')
			->select('a.des_almacen')
			->from(config('bd_almacenes_sap').' a')
			->join(config('bd_tipoalmacen_sap').' c', 'a.centro=c.centro and a.cod_almacen=c.cod_almacen', 'left')
			->join(config('bd_tiposalm_sap').' d', 'c.id_tipo=d.id_tipo', 'left')
			->join(config('bd_empresas_toa_tiposalm').' e', 'd.id_tipo=e.id_tipo', 'left')
			->where_in('e.id_empresa', $empresa)
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera stock de almacenes de una empresa TOA
	 *
	 * @param  string $empresa        Empresa a recuperar stock de almaacenes
	 * @param  string $anomes         Año-mes (yyyymm) a recuperar stock
	 * @param  string $dato_desplegar Indica el tipo de datos a recuperar (montos o cantidades)
	 * @return array                  Arreglo con el stock
	 */
	protected function get_datos_stock_almacenes($empresa = NULL, $anomes = NULL, $dato_desplegar = 'monto')
	{
		$fecha_desde = $anomes.'01';
		$fecha_hasta = get_fecha_hasta($anomes);

		return $this->db
			->select('a.fecha_stock as fecha')
			->select('a.centro+a.almacen as llave', FALSE)
			->select_sum(($dato_desplegar === 'unidades') ? 'a.cantidad' : 'a.valor', 'dato')
			->from(config('bd_stock_fija').' a')
			->join(config('bd_almacenes_sap').' b', 'a.centro=b.centro and a.almacen=b.cod_almacen', 'left')
			->join(config('bd_tipoalmacen_sap').' c', 'a.centro=c.centro and a.almacen=c.cod_almacen', 'left')
			->join(config('bd_tiposalm_sap').' d', 'c.id_tipo=d.id_tipo', 'left')
			->join(config('bd_empresas_toa_tiposalm').' e', 'd.id_tipo=e.id_tipo', 'left')
			->where('a.fecha_stock>=', $fecha_desde)
			->where('a.fecha_stock<', $fecha_hasta)
			->where('e.id_empresa', $empresa)
			->group_by('a.fecha_stock')
			->group_by('a.centro+a.almacen', FALSE)
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	/**
	 * Devuelve matriz de stock de técnicos por dia
	 *
	 * @param  string $empresa        Empresa a consultar
	 * @param  string $anomes         Mes a consultar, formato YYYYMM
	 * @param  string $dato_desplegar Tipo de dato a desplegar
	 * @return array                  Arreglo con los datos del reporte
	 */
	public function stock_tecnicos($empresa = NULL, $anomes = NULL, $dato_desplegar = 'monto')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$tecnicos = Tecnico_toa::create()->find('all', ['conditions' => ['id_empresa' => $empresa]]);
		$stock = $this->result_to_month_table($this->get_datos_stock_tecnicos($empresa, $anomes, $dato_desplegar));
		$arr_dias_mes = get_arr_dias_mes($anomes);

		return $tecnicos->map_with_keys(function($tecnico) use ($stock, $arr_dias_mes) {
			return [$tecnico->id_tecnico => [
				'tecnico'     => $tecnico->tecnico,
				'rut'         => $tecnico->rut,
				'actuaciones' => $stock->get($tecnico->id_tecnico, $arr_dias_mes),
				'con_datos'   => collect($stock->get($tecnico->id_tecnico))->sum(),
			]];
		});
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el stock de los técnicos de una empresa TOA
	 *
	 * @param  string $empresa        Empresa a recuperar stock de los técnicos
	 * @param  string $anomes         Año-mes (yyyymm) a recuperar el stock
	 * @param  string $dato_desplegar Tipo de datos a recuperar (montos o cantidades)
	 * @return array                  Arreglo con el stock de los técnicos
	 */
	protected function get_datos_stock_tecnicos($empresa = NULL, $anomes = NULL, $dato_desplegar = 'monto')
	{
		$fecha_desde = $anomes.'01';
		$fecha_hasta = get_fecha_hasta($anomes);

		return $this->db
			->select('a.fecha_stock as fecha')
			->select('a.acreedor as llave')
			->select_sum(($dato_desplegar === 'unidades') ? 'a.cantidad' : 'a.valor', 'dato')
			->from(config('bd_stock_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.acreedor=b.id_tecnico', 'left', FALSE)
			->where('a.fecha_stock>=', $fecha_desde)
			->where('a.fecha_stock<', $fecha_hasta)
			->where('b.id_empresa', $empresa)
			->group_by('a.fecha_stock')
			->group_by('a.acreedor')
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte con el stock de un almacén para una fecha
	 *
	 * @param  string $fecha          Fecha a consultar, formato YYYYMMDD
	 * @param  string $centro_almacen Centro-Almacen a consultar
	 * @return string                 Reporte
	 */
	public function detalle_stock_almacen($fecha = NULL, $centro_almacen = NULL)
	{
		if ( ! $fecha OR ! $centro_almacen)
		{
			return NULL;
		}

		list($centro, $almacen) = explode('-', $centro_almacen);

		$this->datos_reporte = collect($this->db
			->from(config('bd_stock_fija').' a')
			->join(config('bd_almacenes_sap').' b', 'a.centro=b.centro and a.almacen=b.cod_almacen', 'left')
			->join(config('bd_tipoalmacen_sap').' c', 'a.centro=c.centro and a.almacen=c.cod_almacen', 'left')
			->join(config('bd_tiposalm_sap').' d', 'c.id_tipo=d.id_tipo', 'left')
			->join(config('bd_catalogos').' e', 'a.material=e.catalogo', 'left', FALSE)
			->where('a.fecha_stock=', $fecha)
			->where('a.centro', $centro)
			->where('a.almacen', $almacen)
			->where('d.es_sumable', 1)
			->get()->result_array())->result_keys_to_lower();

		$arr_campos = collect($this->campos)
			->only(['fecha_stock', 'tipo', 'centro', 'almacen', 'des_almacen', 'material', 'descripcion', 'lote', 'umb', 'estado', 'cantidad', 'valor']);

		$this->campos_reporte = $this->set_order_campos($arr_campos, 'material');

		return $this->genera_reporte();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte con el stock de un técnico para una fecha
	 *
	 * @param  string $fecha      Fecha a consultar, formato YYYYMMDD
	 * @param  string $id_tecnico ID del técnico a consultar
	 * @return string             Reporte
	 */
	public function detalle_stock_tecnico($fecha = NULL, $id_tecnico = NULL)
	{
		if ( ! $fecha OR ! $id_tecnico)
		{
			return NULL;
		}

		$this->datos_reporte = collect($this->db
			->from(config('bd_stock_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.acreedor=b.id_tecnico', 'left', FALSE)
			->join(config('bd_catalogos').' c', 'a.material=c.catalogo', 'left', FALSE)
			->where('a.fecha_stock=', $fecha)
			->where('a.acreedor', $id_tecnico)
			->order_by('material, lote')
			->get()->result_array())->result_keys_to_lower();

		$arr_campos = collect($this->campos)
			->only(['fecha_stock', 'centro', 'acreedor', 'tecnico', 'material', 'descripcion', 'lote', 'umb', 'estado', 'cantidad', 'valor']);

		$this->campos_reporte = $this->set_order_campos($arr_campos, 'material');

		return $this->genera_reporte();
	}

}

// End of file Stock.php
// Location: ./models/Toa/Stock.php
