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
 * Clase Modelo Stock TOA
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_stock extends CI_Model {

	/**
	 * Arreglo con validación formulario controles stock empresa
	 *
	 * @var array
	 */
	public $controles_stock_empresa_validation = [
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
	public $controles_stock_tecnicos_validation = [
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

		$almacenes = $this->_get_almacenes($empresa);
		$stock = $this->_get_stock_almacenes($empresa, $anomes, $dato_desplegar);

		$arr_dias = get_arr_dias($anomes);

		return collect($almacenes)
			->map_with_keys(function($almacen) use ($arr_dias, $stock) {
				$stock_almacen = collect($stock)
					->filter(function($stock) use ($almacen) {
						return $stock['centro'] === $almacen['centro'] AND $stock['almacen'] === $almacen['cod_almacen'];
					})->map_with_keys(function($stock) {
						return [substr(fmt_fecha($stock['fecha']), 8, 2) => $stock['dato']];
					})->all();

				$actuaciones = collect($arr_dias)->map(function($valor, $indice) use ($stock_almacen) {
					return $valor + array_get($stock_almacen, $indice, 0);
				})->all();

				return [$almacen['centro'].$almacen['cod_almacen'] => [
					'tipo'        => $almacen['tipo'],
					'centro'      => $almacen['centro'],
					'cod_almacen' => $almacen['cod_almacen'],
					'des_almacen' => $almacen['des_almacen'],
					'actuaciones' => $actuaciones,
					'con_datos'   => collect($actuaciones)->sum(),
				]];
			})->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera almacenes TOA de una empresa
	 *
	 * @param  string $empresa Empresa a recuperar los almacenes
	 * @return array           Arreglo con almacenes
	 */
	private function _get_almacenes($empresa = NULL)
	{
		return $this->db
			->select('d.tipo')
			->select('a.centro')
			->select('a.cod_almacen')
			->select('a.des_almacen')
			->from($this->config->item('bd_almacenes_sap').' a')
			->join($this->config->item('bd_tipoalmacen_sap').' c', 'a.centro=c.centro and a.cod_almacen=c.cod_almacen', 'left')
			->join($this->config->item('bd_tiposalm_sap').' d', 'c.id_tipo=d.id_tipo', 'left')
			->join($this->config->item('bd_empresas_toa_tiposalm').' e', 'd.id_tipo=e.id_tipo', 'left')
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
	private function _get_stock_almacenes($empresa = NULL, $anomes = NULL, $dato_desplegar = 'monto')
	{
		$fecha_desde = $anomes.'01';
		$fecha_hasta = get_fecha_hasta($anomes);

		return $this->db
			->select('a.fecha_stock as fecha')
			->select('d.tipo')
			->select('a.centro')
			->select('a.almacen')
			->select('b.des_almacen')
			->select_sum(($dato_desplegar === 'unidades') ? 'a.cantidad' : 'a.valor', 'dato')
			->from($this->config->item('bd_stock_fija').' a')
			->join($this->config->item('bd_almacenes_sap').' b', 'a.centro=b.centro and a.almacen=b.cod_almacen', 'left')
			->join($this->config->item('bd_tipoalmacen_sap').' c', 'a.centro=c.centro and a.almacen=c.cod_almacen', 'left')
			->join($this->config->item('bd_tiposalm_sap').' d', 'c.id_tipo=d.id_tipo', 'left')
			->join($this->config->item('bd_empresas_toa_tiposalm').' e', 'd.id_tipo=e.id_tipo', 'left')
			->where('a.fecha_stock>=', $fecha_desde)
			->where('a.fecha_stock<', $fecha_hasta)
			->where('e.id_empresa', $empresa)
			->group_by('a.fecha_stock')
			->group_by('d.tipo')
			->group_by('a.centro')
			->group_by('a.almacen')
			->group_by('b.des_almacen')
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

		$tecnicos = new Tecnico_toa();
		$arr_tecnicos = $tecnicos->find('all', ['conditions' => ['id_empresa' => $empresa]]);
		$stock = $this->_get_stock_tecnicos($empresa, $anomes, $dato_desplegar);

		$arr_dias = get_arr_dias($anomes);

		return collect($arr_tecnicos)
			->map_with_keys(function($tecnico) use ($arr_dias, $stock) {
				$stock_tecnico = collect($stock)
					->filter(function($stock) use ($tecnico) {
						return $stock['acreedor'] === $tecnico->id_tecnico;
					})->map_with_keys(function($stock) {
						return [substr(fmt_fecha($stock['fecha']), 8, 2) => $stock['dato']];
					})->all();

				$actuaciones = collect($arr_dias)->map(function($valor, $indice) use ($stock_tecnico) {
					return $valor + array_get($stock_tecnico, $indice, 0);
				})->all();

				return [$tecnico->id_tecnico => [
					'tecnico'     => $tecnico->tecnico,
					'rut'         => $tecnico->rut,
					'actuaciones' => $actuaciones,
					'con_datos'   => collect($actuaciones)->sum(),
				]];
			})->all();
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
	private function _get_stock_tecnicos($empresa = NULL, $anomes = NULL, $dato_desplegar = 'monto')
	{
		$fecha_desde = $anomes.'01';
		$fecha_hasta = get_fecha_hasta($anomes);

		return $this->db
			->select('a.fecha_stock as fecha')
			->select('a.acreedor')
			->select('b.rut')
			->select_sum(($dato_desplegar === 'unidades') ? 'a.cantidad' : 'a.valor', 'dato')
			->from($this->config->item('bd_stock_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.acreedor=b.id_tecnico', 'left', FALSE)
			->where('a.fecha_stock>=', $fecha_desde)
			->where('a.fecha_stock<', $fecha_hasta)
			->where('b.id_empresa', $empresa)
			->group_by('a.fecha_stock')
			->group_by('a.acreedor')
			->group_by('b.rut')
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

		$arr_data = $this->db
			->select('a.fecha_stock as fecha')
			->select('d.tipo')
			->select('a.centro')
			->select('a.almacen')
			->select('b.des_almacen')
			->select('a.material')
			->select('e.descripcion')
			->select('a.lote')
			->select('a.umb')
			->select('a.estado')
			->select('a.cantidad')
			->select('a.valor')
			->from($this->config->item('bd_stock_fija').' a')
			->join($this->config->item('bd_almacenes_sap').' b', 'a.centro=b.centro and a.almacen=b.cod_almacen', 'left')
			->join($this->config->item('bd_tipoalmacen_sap').' c', 'a.centro=c.centro and a.almacen=c.cod_almacen', 'left')
			->join($this->config->item('bd_tiposalm_sap').' d', 'c.id_tipo=d.id_tipo', 'left')
			->join($this->config->item('bd_catalogos').' e', 'a.material=e.catalogo', 'left', FALSE)
			->where('a.fecha_stock=', $fecha)
			->where('a.centro', $centro)
			->where('a.almacen', $almacen)
			->where('d.es_sumable', 1)
			->get()->result_array();

		$arr_campos = [];
		$arr_campos['fecha']       = ['titulo' => 'Fecha', 'tipo' => 'fecha'];
		$arr_campos['tipo']        = ['titulo' => 'Tipo Almac&eacute;n'];
		$arr_campos['centro']      = ['titulo' => 'Centro'];
		$arr_campos['almacen']     = ['titulo' => 'Almac&eacute;n'];
		$arr_campos['des_almacen'] = ['titulo' => 'Desc Almac&eacute;n'];
		$arr_campos['material']    = ['titulo' => 'Material'];
		$arr_campos['descripcion'] = ['titulo' => 'Desc Material'];
		$arr_campos['lote']        = ['titulo' => 'Lote'];
		$arr_campos['umb']         = ['titulo' => 'Unidad'];
		$arr_campos['estado']      = ['titulo' => 'Estado'];
		$arr_campos['cantidad']    = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
		$arr_campos['valor']       = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
		$this->reporte->set_order_campos($arr_campos, 'material');

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
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

		$arr_data = $this->db
			->select('a.fecha_stock as fecha')
			->select('a.centro')
			->select('a.acreedor')
			->select('b.tecnico')
			->select('a.material')
			->select('c.descripcion')
			->select('a.lote')
			->select('a.umb')
			->select('a.estado')
			->select('a.cantidad')
			->select('a.valor')
			->from($this->config->item('bd_stock_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.acreedor=b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_catalogos').' c', 'a.material=c.catalogo', 'left', FALSE)
			->where('a.fecha_stock=', $fecha)
			->where('a.acreedor', $id_tecnico)
			->order_by('material, lote')
			->get()->result_array();

		$arr_campos = [];
		$arr_campos['fecha']       = ['titulo' => 'Fecha', 'tipo' => 'fecha'];
		$arr_campos['centro']      = ['titulo' => 'Centro'];
		$arr_campos['acreedor']    = ['titulo' => 'Cod T&eacute;cnico'];
		$arr_campos['tecnico']     = ['titulo' => 'Nombre T&eacute;cnico'];
		$arr_campos['material']    = ['titulo' => 'Material'];
		$arr_campos['descripcion'] = ['titulo' => 'Desc Material'];
		$arr_campos['lote']        = ['titulo' => 'Lote'];
		$arr_campos['umb']         = ['titulo' => 'Unidad'];
		$arr_campos['estado']      = ['titulo' => 'Estado'];
		$arr_campos['cantidad']    = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
		$arr_campos['valor']       = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
		$this->reporte->set_order_campos($arr_campos, 'material');

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
	}

}

/* End of file Toa_stock.php */
/* Location: ./application/models/Toa_stock.php */
