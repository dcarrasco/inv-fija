<?php

namespace Toa;

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

use \ORM_Model;
use \ORM_Field;
use Toa\Tecnico_toa;
use Stock\Clase_movimiento;

/**
 * Clase Modelo TOA Asignacion
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Asignacion_toa extends ORM_Model {

	/**
	 * Arreglo con validación formulario consumos
	 *
	 * @var array
	 */
	public $rules = [
		['field' => 'sel_reporte', 'label' => 'Reporte',     'rules' => 'required'],
		['field' => 'fecha_desde', 'label' => 'Fecha desde', 'rules' => 'required'],
		['field' => 'fecha_hasta', 'label' => 'Fecha hasta', 'rules' => 'required'],
	];

	/**
	 * Tipos de reporte asignaciones
	 *
	 * @var array
	 */
	public $tipos_reporte_asignaciones = [
		'tecnicos'      => 'T&eacute;cnicos',
		'material'      => 'Materiales',
		'lote'          => 'Lotes',
		'lote-material' => 'Lotes y materiales',
		'detalle'       => 'Detalle todos los registros',
	];

	/**
	 * Movimientos validos de asignaciones TOA
	 *
	 * @var array
	 */
	public $movimientos_asignaciones = ['Z31', 'Z32'];

	/**
	 * Centros validos de consumos TOA
	 *
	 * @var array
	 */
	public $centros_asignacion = ['CH32', 'CH33'];

	/**
	 * Combo de unidades reporte control asignaciones
	 *
	 * @var array
	 */
	public $combo_unidades_asignacion = [
		'asignaciones' => 'Cantidad de asignaciones',
		'unidades'     => 'Suma de unidades',
		'monto'        => 'Suma de montos',
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
		$this->load->helper('date');
	}

	// --------------------------------------------------------------------

	/**
	 * Filtra un asignaciones para un rango de fechas
	 *
	 * @param  string  $fecha_desde  Fecha inicio
	 * @param  string  $fecha_hasta  Fecha fin
	 * @param  boolean $filtra_signo Indica si filtramos por signo del movimiento
	 * @return void
	 */
	protected function rango_asignaciones($fecha_desde, $fecha_hasta, $filtra_signo = TRUE)
	{
		if ($filtra_signo)
		{
			$this->db->where('a.signo', 'NEG');
		}

		return $this->db
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<=', $fecha_hasta)
			->where_in('a.codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('a.centro', $this->centros_asignacion)
			->from(config('bd_movimientos_sap_fija').' a');
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve las asignaciones realizados en TOA
	 *
	 * @param  string $tipo_reporte Tipo de reporte a desplegar
	 * @param  string $fecha_desde  Fecha de los datos del reporte
	 * @param  string $fecha_hasta  Fecha de los datos del reporte
	 * @param  string $orden_campo  Campo para ordenar el resultado
	 * @param  string $orden_tipo   Orden del resultado (ascendente o descendente)
	 * @return string               Reporte
	 */
	public function asignaciones_toa($tipo_reporte = 'tecnicos', $fecha_desde = NULL, $fecha_hasta = NULL, $orden_campo = '', $orden_tipo = 'ASC')
	{
		if ( ! $tipo_reporte OR ! $fecha_desde OR ! $fecha_hasta)
		{
			return '';
		}

		$select_fields = [
			'tecnicos' => "c.empresa, a.cliente, b.tecnico, 'ver asignaciones' as texto_link, sum(-a.cantidad_en_um) as cant, sum(-a.importe_ml) as monto",
			'material' => "material, texto_material, 'ver asignaciones' as texto_link, sum(-cantidad_en_um) as cant, sum(-importe_ml) as monto",
			'lote' => "valor, lote, 'ver asignaciones' as texto_link, sum(-cantidad_en_um) as cant, sum(-importe_ml) as monto",
			'lote-material' => "valor, lote, material, texto_material, 'ver asignaciones' as texto_link, sum(-cantidad_en_um) as cant, sum(-importe_ml) as monto",
			'detalle' => 'a.fecha_contabilizacion as fecha, a.referencia, c.empresa, a.cliente, b.tecnico, a.codigo_movimiento, a.texto_movimiento, a.elemento_pep, a.documento_material, a.centro, a.material, a.texto_material, a.lote, a.valor, a.umb, (-a.cantidad_en_um) as cant, (-a.importe_ml) as monto, a.usuario',
		];

		$group_by_fields = [
			'tecnicos' => ['c.empresa', 'a.cliente', 'b.tecnico'],
			'material' => ['material', 'texto_material'],
			'lote'     => ['valor', 'lote'],
			'lote-material' => ['valor', 'lote', 'material', 'texto_material'],
			'detalle'  => [],
		];

		$campos_reporte = [
			'tecnicos' => [
				'empresa'    => ['titulo' => 'Empresa'],
				'cliente'    => ['titulo' => 'Cod Tecnico'],
				'tecnico'    => ['titulo' => 'Nombre Tecnico'],
				'cant'       => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
				'monto'      => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
				'texto_link' => [
					'titulo'         => '',
					'tipo'           => 'link_registro',
					'class'          => 'text-right',
					'href'           => "toa_asignaciones/ver_asignaciones/tecnicos/$fecha_desde/$fecha_hasta",
					'href_registros' => ['cliente']
				],
			],
			'material' => [
				'material'       => ['titulo' => 'Cod material'],
				'texto_material' => ['titulo' => 'Desc material'],
				'cant'           => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
				'monto'          => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
				'texto_link'     => [
					'titulo'         => '',
					'tipo'           => 'link_registro',
					'class'          => 'text-right',
					'href'           => "toa_asignaciones/ver_asignaciones/material/$fecha_desde/$fecha_hasta",
					'href_registros' => ['material']
				],
			],
			'lote' => [
				'valor'      => ['titulo' => 'Valor'],
				'lote'       => ['titulo' => 'Lote'],
				'cant'       => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
				'monto'      => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
				'texto_link' => [
					'titulo'         => '',
					'tipo'           => 'link_registro',
					'class'          => 'text-right',
					'href'           => "toa_asignaciones/ver_asignaciones/lote/$fecha_desde/$fecha_hasta",
					'href_registros' => ['lote']
				],
			],
			'lote-material' => [
				'valor'          => ['titulo' => 'Valor'],
				'lote'           => ['titulo' => 'Lote'],
				'material'       => ['titulo' => 'Cod material'],
				'texto_material' => ['titulo' => 'Desc material'],
				'cant'           => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
				'monto'          => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
				'texto_link'     => [
					'titulo'         => '',
					'tipo'           => 'link_registro',
					'class'          => 'text-right',
					'href'           => "toa_asignaciones/ver_asignaciones/lote-material/$fecha_desde/$fecha_hasta",
					'href_registros' => ['lote','material']
				],
			],
			'detalle' => [
				'fecha'              => ['titulo' => 'Fecha', 'tipo' => 'fecha'],
				'referencia'         => ['titulo' => 'Numero Peticion'],
				'empresa'            => ['titulo' => 'Empresa'],
				'cliente'            => ['titulo' => 'Cod Tecnico'],
				'tecnico'            => ['titulo' => 'Nombre Tecnico'],
				'codigo_movimiento'  => ['titulo' => 'Cod Movimiento'],
				'texto_movimiento'   => ['titulo' => 'Desc Movimiento'],
				'elemento_pep'       => ['titulo' => 'PEP'],
				'documento_material' => ['titulo' => 'Documento SAP'],
				'centro'             => ['titulo' => 'Centro'],
				'material'           => ['titulo' => 'Cod material'],
				'texto_material'     => ['titulo' => 'Desc material'],
				'lote'               => ['titulo' => 'Lote'],
				'valor'              => ['titulo' => 'Valor'],
				'umb'                => ['titulo' => 'Unidad'],
				'cant'               => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
				'monto'              => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
				'usuario'            => ['titulo' => 'Usuario SAP'],
			],
		];

		$order_fields = [
			'tecnicos'      => 'empresa',
			'material'      => 'material',
			'lote'          => 'valor',
			'lote-material' => 'valor',
			'detalle'       => 'referencia',
		];

		$orden_tipo = ($orden_tipo === '') ? 'ASC' : $orden_tipo;
		$orden_campo = empty($orden_campo) ? array_get($order_fields, $tipo_reporte) : $orden_campo;

		if ($tipo_reporte === 'tecnicos')
		{
			$this->db
				->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->join(config('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left');
		}
		elseif ($tipo_reporte === 'detalle')
		{
			$this->db
				->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left');
		}

		$arr_data = $this->rango_asignaciones($fecha_desde, $fecha_hasta)
			->select(array_get($select_fields, $tipo_reporte), FALSE)
			->group_by(array_get($group_by_fields, $tipo_reporte))
			->get()->result_array();

		$arr_campos = array_get($campos_reporte, $tipo_reporte);
		$this->reporte->set_order_campos($arr_campos, $orden_campo);

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera peticiones dados argumentos de busqueda
	 *
	 * @param  string $tipo_reporte Tipo de reporte a buscar
	 * @param  string $param1       Primer parametro
	 * @param  string $param2       Segundo parametro
	 * @param  string $param3       Tercer parametro
	 * @param  string $param4       Cuarto parametro
	 * @return string               Reporte con las peticiones encontradas
	 */
	public function documentos_asignaciones_toa($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		if ( ! $tipo_reporte)
		{
			return '';
		}

		$filtros = [
			'material' => 'a.material',
			'lote'     => 'a.lote',
			'tecnicos' => 'a.cliente',
		];

		if (array_key_exists($tipo_reporte, $filtros))
		{
			$this->db->where($filtros[$tipo_reporte], $param3);
		}
		elseif ($tipo_reporte === 'lote-material')
		{
			$this->db->where('a.lote', $param3);
			$this->db->where('a.material', $param4);
		}
		elseif ($tipo_reporte === 'pep')
		{
			$this->db->where('a.codigo_movimiento', $param3);
			$this->db->where('a.elemento_pep', $param4);
		}

		$arr_data = $this->rango_asignaciones($param1, $param2)
			->select("convert(varchar(20), a.fecha_contabilizacion, 112) as fecha, a.documento_material, c.empresa, a.cliente, b.tecnico, a.centro, a.almacen, d.des_almacen, a.material, a.texto_material, a.valor, a.lote, 'ver detalle' as texto_link, sum(-a.cantidad_en_um) as cant, sum(-a.importe_ml) as monto", FALSE)
			->group_by('a.fecha_contabilizacion, a.documento_material, c.empresa, a.cliente, b.tecnico, a.centro, a.almacen, d.des_almacen, a.material, a.texto_material, a.valor, a.lote')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->join(config('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left')
			->order_by('a.documento_material', 'ASC')
			->get()->result_array();

		$arr_campos = [];
		$arr_campos['fecha']              = ['titulo' => 'Fecha', 'tipo' => 'fecha'];
		$arr_campos['documento_material'] = ['titulo' => 'Documento SAP'];
		$arr_campos['empresa']            = ['titulo' => 'Empresa'];
		$arr_campos['cliente']            = ['titulo' => 'Cod Tecnico'];
		$arr_campos['tecnico']            = ['titulo' => 'Nombre Tecnico'];
		$arr_campos['centro']             = ['titulo' => 'Centro'];
		$arr_campos['almacen']            = ['titulo' => 'Almac&eacute;n'];
		$arr_campos['des_almacen']        = ['titulo' => 'Desc Almac&eacute;n'];
		$arr_campos['material']           = ['titulo' => 'Material'];
		$arr_campos['texto_material']     = ['titulo' => 'Desc Material'];
		$arr_campos['lote']               = ['titulo' => 'Lote'];
		$arr_campos['valor']              = ['titulo' => 'Valor'];
		$arr_campos['cant']               = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
		$arr_campos['monto']              = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
		$arr_campos['texto_link']         = [
			'titulo'         => '',
			'class'          => 'text-right',
			'tipo'           => 'link_registro',
			'href'           => 'toa_asignaciones/detalle_asignacion',
			'href_registros' => ['fecha','documento_material']
		];
		$this->reporte->set_order_campos($arr_campos, 'documento_material');

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera datos de detalle de una peticion
	 *
	 * @param  string $fecha    Fecha de la peticion
	 * @param  string $peticion ID de la peticion
	 * @return array            Detalle de la peticion
	 */
	public function detalle_asignacion_toa($fecha = NULL, $peticion = NULL)
	{
		if ( ! $fecha OR ! $peticion)
		{
			return [];
		}

		return $this->rango_asignaciones($fecha, $fecha, FALSE)
			->select('a.fecha_contabilizacion as fecha, a.referencia, c.empresa, a.cliente, b.tecnico, a.codigo_movimiento, a.texto_movimiento, a.elemento_pep, a.documento_material, a.centro, a.almacen, a.material, a.texto_material, a.lote, a.valor, a.umb, (a.cantidad_en_um) as cant, (a.importe_ml) as monto, a.usuario', FALSE)
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			//->order_by($orden_campo, $orden_tipo)
			->where('documento_material', $peticion)
			->order_by('material, cantidad_en_um')
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve matriz de asignaciones a técnicos por dia
	 *
	 * @param  string $empresa        Empresa a consultar
	 * @param  string $anomes         Mes a consultar, formato YYYYMM
	 * @param  string $filtro_trx     Filtro para revisar un codigo movimiento
	 * @param  string $dato_desplegar Tipo de dato a desplegar
	 * @return array                  Arreglo con los datos del reporte
	 */
	public function control_asignaciones($empresa = NULL, $anomes = NULL, $filtro_trx = NULL, $dato_desplegar = 'asignaciones')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$arr_dias = get_arr_dias_mes($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = get_fecha_hasta($anomes);

		if ($filtro_trx AND $filtro_trx !== '000')
		{
			$this->db->where('codigo_movimiento', $filtro_trx);
		}

		if (in_array($dato_desplegar, ['unidades', 'monto']))
		{
			$this->db->select_sum($dato_desplegar === 'unidades' ? 'a.cantidad_en_um' : 'a.importe_ml', 'dato');
		}
		else
		{
			$this->db->select('count(*) as dato', FALSE);
		}

		$datos = $this->rango_asignaciones($fecha_desde, $fecha_hasta, FALSE)
			->select('a.fecha_contabilizacion as fecha')
			->select('a.cliente')
			->group_by('a.fecha_contabilizacion')
			->group_by('a.cliente')
			->where('b.id_empresa', $empresa)
			->where('almacen', NULL)
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->get()->result_array();

		$tecnicos = Tecnico_toa::create()->find('all', ['conditions' => ['id_empresa' => $empresa]]);

		return $tecnicos
			->map_with_keys(function($tecnico) use ($arr_dias, $datos) {
				$datos_tecnico = collect($datos)
					->filter(function($dato) use ($tecnico) {
						return $dato['cliente'] === $tecnico->id_tecnico;
					})
					->map_with_keys(function($dato) {
						return [substr($dato['fecha'], 8, 2) => $dato['dato']];
					});

				return [$tecnico->id_tecnico => [
					'nombre'       => $tecnico->tecnico,
					'rut'          => $tecnico->rut,
					'ciudad'       => (string) $tecnico->get_relation_object('id_ciudad'),
					'orden_ciudad' => (int) $tecnico->get_relation_object('id_ciudad')->orden,
					'actuaciones'  => collect($arr_dias)->merge($datos_tecnico)->all(),
				]];
			})
			->filter(function($tecnico) {
				return collect($tecnico['actuaciones'])->sum() !== 0;
			})
			->sort(function($tecnico1, $tecnico2) {
				return $tecnico1['orden_ciudad'] > $tecnico2['orden_ciudad'];
			});
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve combo con las clases de movimiento de asignacion
	 *
	 * @return array Clases de movimiento de asignacion
	 */
	public function get_combo_movimientos_asignacion()
	{
		return array_merge(
			['000' => 'Todos los movimientos'],
			Clase_movimiento::create()->find('list', [
				'conditions' => ['cmv' => $this->movimientos_asignaciones],
				'opc_ini'    => FALSE,
			])
		);
	}

}

/* End of file Asignacion_toa.php */
/* Location: ./application/models/Toa/Asignacion_toa.php */
