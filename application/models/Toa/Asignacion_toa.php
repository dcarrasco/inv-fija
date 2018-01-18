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
use Model\Orm_model;
use Model\Orm_field;
use Toa\Tecnico_toa;
use Stock\Clase_movimiento;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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

	use Reporte;

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

	/**
	 * Campos select del reporte
	 *
	 * @var array
	 */
	protected $select_fields = [
		'tecnicos'      => "c.empresa, a.cliente, b.tecnico, 'ver asignaciones' as texto_link, sum(-a.cantidad_en_um) as cant, sum(-a.importe_ml) as monto",
		'material'      => "material, texto_material, 'ver asignaciones' as texto_link, sum(-cantidad_en_um) as cant, sum(-importe_ml) as monto",
		'lote'          => "valor, lote, 'ver asignaciones' as texto_link, sum(-cantidad_en_um) as cant, sum(-importe_ml) as monto",
		'lote-material' => "valor, lote, material, texto_material, 'ver asignaciones' as texto_link, sum(-cantidad_en_um) as cant, sum(-importe_ml) as monto",
		'detalle'       => 'a.fecha_contabilizacion as fecha, a.referencia, c.empresa, a.cliente, b.tecnico, a.codigo_movimiento, a.texto_movimiento, a.elemento_pep, a.documento_material, a.centro, a.material, a.texto_material, a.lote, a.valor, a.umb, (-a.cantidad_en_um) as cant, (-a.importe_ml) as monto, a.usuario',
	];

	/**
	 * Campos para agrupar reporte
	 *
	 * @var array
	 */
	protected $group_by_fields = [
		'tecnicos'      => ['c.empresa', 'a.cliente', 'b.tecnico'],
		'material'      => ['material', 'texto_material'],
		'lote'          => ['valor', 'lote'],
		'lote-material' => ['valor', 'lote', 'material', 'texto_material'],
		'detalle'       => [],
	];

	/**
	 * Campos para ordenar reporte
	 *
	 * @var array
	 */
	protected $order_fields = [
		'tecnicos'      => 'empresa',
		'material'      => 'material',
		'lote'          => 'valor',
		'lote-material' => 'valor',
		'detalle'       => 'referencia',
	];

	/**
	 * Campos de reporte
	 *
	 * @var array
	 */
	protected $configuracion_campos = [
		'empresa'            => ['titulo' => 'Empresa'],
		'cliente'            => ['titulo' => 'Cod Tecnico'],
		'tecnico'            => ['titulo' => 'Nombre Tecnico'],
		'material'           => ['titulo' => 'Cod material'],
		'texto_material'     => ['titulo' => 'Desc material'],
		'lote'               => ['titulo' => 'Lote'],
		'valor'              => ['titulo' => 'Valor'],
		'fecha'              => ['titulo' => 'Fecha', 'tipo' => 'fecha'],
		'referencia'         => ['titulo' => 'Numero Peticion'],
		'codigo_movimiento'  => ['titulo' => 'Cod Movimiento'],
		'texto_movimiento'   => ['titulo' => 'Desc Movimiento'],
		'elemento_pep'       => ['titulo' => 'PEP'],
		'documento_material' => ['titulo' => 'Documento SAP'],
		'centro'             => ['titulo' => 'Centro'],
		'umb'                => ['titulo' => 'Unidad'],
		'usuario'            => ['titulo' => 'Usuario SAP'],
		'cant'               => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
		'monto'              => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
	];

	/**
	 * Configuración de los campos de los reportes
	 * @var array
	 */
	protected $config_campos_reportes = [
		'tecnicos'      => ['empresa', 'cliente', 'tecnico', 'cant', 'monto'],
		'material'      => ['material', 'texto_material', 'cant', 'monto'],
		'lote'          => ['valor', 'lote', 'cant', 'monto'],
		'lote-material' => ['valor', 'lote', 'material', 'texto_material', 'cant', 'monto'],
		'detalle'       => ['fecha', 'referencia', 'empresa', 'cliente', 'tecnico', 'codigo_movimiento', 'texto_movimiento', 'elemento_pep', 'documento_material', 'centro', 'material', 'texto_material', 'lote', 'valor', 'umb', 'cant', 'monto', 'usuario'],
	];


	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct($id = NULL, $ci_object = [])
	{
		parent::__construct($id, $ci_object);
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
	 * Recupera los datos de las asignaciones
	 *
	 * @param  string  $reporte      Tipo de reporte
	 * @param  string  $fecha_desde  Fecha inicial
	 * @param  atring  $fecha_hasta  Fecha final
	 * @param  boolean $filtra_signo Indicador si se filtra el signo
	 * @return array
	 */
	protected function get_datos_asignaciones($reporte, $fecha_desde, $fecha_hasta, $filtra_signo = TRUE)
	{
		if ($reporte === 'tecnicos')
		{
			$this->db
				->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->join(config('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left');
		}
		elseif ($reporte === 'detalle')
		{
			$this->db
				->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left');
		}

		return $this->rango_asignaciones($fecha_desde, $fecha_hasta, $filtra_signo)
			->select(array_get($this->select_fields, $reporte), FALSE)
			->group_by(array_get($this->group_by_fields, $reporte))
			->order_by(array_get($this->order_fields, $reporte))
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los parametros para un link del reporte
	 *
	 * @param  string $reporte     Tipo de reporte
	 * @param  string $fecha_desde Fecha de inicio
	 * @param  string $fecha_hasta Fecha de fin
	 * @return array
	 */
	protected function texto_link($reporte, $fecha_desde, $fecha_hasta)
	{
		$href = [
			'tecnicos'      => ['cliente'],
			'material'      => ['material'],
			'lote'          => ['lote'],
			'lote-material' => ['lote', 'material'],
		];

		return ['texto_link' => [
			'titulo'         => '',
			'tipo'           => 'link_registro',
			'class'          => 'text-right',
			'href'           => "toa_asignaciones/ver_asignaciones/{$reporte}/{$fecha_desde}/{$fecha_hasta}",
			'href_registros' => array_get($href, $reporte, []),
		]];
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos a mostrar en reporte de asignación
	 * @param  string $reporte     Tipo de reporte a desplegar
	 * @param  string $orden_campo Campo para ordenar el resultado
	 * @param  string $orden_tipo  Orden del resultado (ascendente o descendente)
	 * @return array
	 */
	protected function get_campos_reporte_asignaciones($reporte = 'tecnicos', $fecha_desde = NULL, $fecha_hasta = NULL, $orden_campo = '', $orden_tipo = 'ASC')
	{
		$orden_tipo = ($orden_tipo === '') ? 'ASC' : $orden_tipo;
		$orden_campo = empty($orden_campo) ? array_get($this->order_fields, $reporte) : $orden_campo;

		$arr_campos = collect($this->configuracion_campos)
			->only(array_get($this->config_campos_reportes, $reporte))
			->merge($this->texto_link($reporte, $fecha_desde, $fecha_hasta));

		return $this->set_order_campos($arr_campos, $orden_campo);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve las asignaciones realizados en TOA
	 *
	 * @param  string $reporte     Tipo de reporte a desplegar
	 * @param  string $fecha_desde Fecha de los datos del reporte
	 * @param  string $fecha_hasta Fecha de los datos del reporte
	 * @param  string $orden_campo Campo para ordenar el resultado
	 * @param  string $orden_tipo  Orden del resultado (ascendente o descendente)
	 * @return string
	 */
	public function asignaciones_toa($reporte = 'tecnicos', $fecha_desde = NULL, $fecha_hasta = NULL, $orden_campo = '', $orden_tipo = 'ASC')
	{
		if ( ! $reporte OR ! $fecha_desde OR ! $fecha_hasta)
		{
			return '';
		}

		$this->datos_reporte = $this->get_datos_asignaciones($reporte, $fecha_desde, $fecha_hasta);
		$this->campos_reporte = $this->get_campos_reporte_asignaciones($reporte, $fecha_desde, $fecha_hasta, $orden_campo, $orden_tipo);

		return $this->genera_reporte();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera datos peticiones dados argumentos de busqueda
	 *
	 * @param  string $tipo_reporte Tipo de reporte a buscar
	 * @param  string $param1       Primer parametro
	 * @param  string $param2       Segundo parametro
	 * @param  string $param3       Tercer parametro
	 * @param  string $param4       Cuarto parametro
	 * @return array
	 */
	protected function datos_documentos_asignaciones_toa($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
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

		return $this->rango_asignaciones($param1, $param2)
			->select("convert(varchar(20), a.fecha_contabilizacion, 112) as fecha, a.documento_material, c.empresa, a.cliente, b.tecnico, a.centro, a.almacen, d.des_almacen, a.material, a.texto_material, a.valor, a.lote, 'ver detalle' as texto_link, sum(-a.cantidad_en_um) as cant, sum(-a.importe_ml) as monto", FALSE)
			->group_by('a.fecha_contabilizacion, a.documento_material, c.empresa, a.cliente, b.tecnico, a.centro, a.almacen, d.des_almacen, a.material, a.texto_material, a.valor, a.lote')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->join(config('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left')
			->order_by('a.documento_material', 'ASC')
			->get()->result_array();

	}

	// --------------------------------------------------------------------

	/**
	 * Recupera campos peticiones dados argumentos de busqueda
	 *
	 * @return array
	 */
	protected function campos_documentos_asignaciones_toa($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		$campos_reporte = [
			'fecha'              => ['titulo' => 'Fecha', 'tipo' => 'fecha'],
			'documento_material' => ['titulo' => 'Documento SAP'],
			'empresa'            => ['titulo' => 'Empresa'],
			'cliente'            => ['titulo' => 'Cod Tecnico'],
			'tecnico'            => ['titulo' => 'Nombre Tecnico'],
			'centro'             => ['titulo' => 'Centro'],
			'almacen'            => ['titulo' => 'Almac&eacute;n'],
			'des_almacen'        => ['titulo' => 'Desc Almac&eacute;n'],
			'material'           => ['titulo' => 'Material'],
			'texto_material'     => ['titulo' => 'Desc Material'],
			'lote'               => ['titulo' => 'Lote'],
			'valor'              => ['titulo' => 'Valor'],
			'cant'               => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
			'monto'              => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
			'texto_link'         => [
				'titulo'         => '',
				'class'          => 'text-right',
				'tipo'           => 'link_registro',
				'href'           => 'toa_asignaciones/detalle_asignacion',
				'href_registros' => ['fecha','documento_material']
			],
		];

		return $this->set_order_campos($campos_reporte, 'documento_material');
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

		$this->datos_reporte = $this->datos_documentos_asignaciones_toa($tipo_reporte, $param1, $param2, $param3, $param4);
		$this->campos_reporte = $this->campos_documentos_asignaciones_toa();

		return $this->genera_reporte();
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
	 * @param  Collection $params Parametros del reporte
	 * @return Collection
	 */
	public function control_asignaciones($params = NULL)
	{
		if ( ! $params)
		{
			return collect();
		}

		$datos = $this->result_to_month_table($this->control_asignaciones_datos($params));

		return Tecnico_toa::create($this->ci_object)
			->find('all', ['conditions' => ['id_empresa' => $params->get('empresa')]])
			->map_with_keys(function($tecnico) use ($datos) {
				return [$tecnico->id_tecnico => [
					'nombre'       => $tecnico->tecnico,
					'rut'          => $tecnico->rut,
					'ciudad'       => (string) $tecnico->get_relation_object('id_ciudad'),
					'orden_ciudad' => (int) $tecnico->get_relation_object('id_ciudad')->orden,
					'actuaciones'  => $datos->get($tecnico->id_tecnico),
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
	 * Devuelve datos de asignaciones a técnicos por dia
	 *
	 * @param  Collection $params Parametros del reporte
	 * @return array
	 */
	public function control_asignaciones_datos($params)
	{
		$fecha_desde = $params->get('mes', '').'01';
		$fecha_hasta = get_fecha_hasta($params->get('mes', ''));
		$dato_desplegar = $params->get('dato_desplegar', 'asignaciones');
		$empresa = $params->get('empresa', '');

		if ($params->get('filtro_trx', '000') !== '000')
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

		return $this->rango_asignaciones($fecha_desde, $fecha_hasta, FALSE)
			->select('a.fecha_contabilizacion as fecha')
			->select('a.cliente as llave')
			->group_by('a.fecha_contabilizacion')
			->group_by('a.cliente')
			->where('b.id_empresa', $empresa)
			->where('almacen', NULL)
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->get()->result_array();
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

// End of file Asignacion_toa.php
// Location: ./models/Toa/Asignacion_toa.php
