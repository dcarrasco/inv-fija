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

/**
 * Clase Modelo Movimientos fija
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Consumo_toa extends ORM_Model {

	/**
	 * Movimientos validos de consumos TOA
	 *
	 * @var array
	 */
	public $movimientos_consumo = ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89'];
	// public $movimientos_consumo = ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89', 'Z33'];
	// Z33 devolucion de materiales: viene con un idtecnico distinto
	// public $movimientos_consumo = ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89', 'Z81', 'Z82', 'Z83', 'Z84'];
	// Z81 CAPEX  Z82 ANULA CAPEX  / Z83 OPEX y Z84 ANULA OPEX   Regularizaciones Manuales

	/**
	 * Centros validos de consumos TOA
	 *
	 * @var array
	 */
	public $centros_consumo = ['CH32', 'CH33'];

	/**
	 * Tipos de reporte consumo
	 *
	 * @var array
	 */
	public $tipos_reporte_consumo = [
		'peticion'      => 'N&uacute;mero petici&oacute;n',
		'empresas'      => 'Empresas',
		'ciudades'      => 'Ciudades',
		'tecnicos'      => 'T&eacute;cnicos',
		'tip_material'  => 'Tipos de material',
		'material'      => 'Materiales',
		'lote'          => 'Lotes',
		'lote_material' => 'Lotes y materiales',
		'pep'           => 'PEP',
		'tipo_trabajo'  => 'Tipo de trabajo',
		'detalle'       => 'Detalle todos los registros',
	];

	/**
	 * Arreglo con validación formulario consumos
	 *
	 * @var array
	 */
	public $consumos_validation = [
		['field' => 'sel_reporte', 'label' => 'Reporte',     'rules' => 'required'],
		['field' => 'fecha_desde', 'label' => 'Fecha desde', 'rules' => 'required'],
		['field' => 'fecha_hasta', 'label' => 'Fecha hasta', 'rules' => 'required'],
	];

	/**
	 * Campos comunes reporte consumo
	 *
	 * @var array
	 */
	private $_select_base = [
		"'ver peticiones' as texto_link",
		'sum(-a.cantidad_en_um) as cant',
		'sum(-a.importe_ml) as monto',
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

	private function _texto_link($fecha_desde, $fecha_hasta, $reporte, $registros_href)
	{
		return [
			'cant'       => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
			'monto'      => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
			'texto_link' => [
				'titulo' => '',
				'tipo'   => 'link_registro',
				'class'  => 'text-right',
				'href'   => "toa_consumos/ver_peticiones/{$reporte}/{$fecha_desde}/{$fecha_hasta}",
				'href_registros' => $registros_href,
			],
		];
	}


	// --------------------------------------------------------------------

	private function _get_datos_consumo_detalle($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'a.fecha_contabilizacion as fecha',
			'a.referencia',
			'a.carta_porte',
			'c.empresa',
			'a.cliente',
			'b.tecnico',
			'a.codigo_movimiento',
			'a.texto_movimiento',
			'a.elemento_pep',
			'a.documento_material',
			'a.centro',
			'a.material',
			'a.texto_material',
			'a.lote',
			'a.valor',
			'a.umb',
			'(-a.cantidad_en_um) as cant',
			'(-a.importe_ml) as monto',
			'a.usuario',
		];

		$datos = $this->db->select($select, FALSE)
			->join(config('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'a.vale_acomp = c.id_empresa', 'left', FALSE)
			->order_by($this->reporte->get_order_by($orden_campo))
			->get()->result_array();

		$campos = [
			'fecha'              => ['titulo' => 'Fecha', 'tipo' => 'fecha'],
			'referencia'         => ['titulo' => 'Numero Peticion'],
			'carta_porte'        => ['titulo' => 'Tipo trabajo'],
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
		];

		return [$datos, $campos];
	}

	private function _get_datos_consumo_peticion($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'a.referencia',
			'a.carta_porte',
			'c.empresa',
			'a.cliente',
			'b.tecnico',
		];

		$datos = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->join(config('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'a.vale_acomp = c.id_empresa', 'left', FALSE)
			->order_by($this->reporte->get_order_by($orden_campo))
			->get()->result_array();

		$campos = [
			'referencia'  => ['titulo' => 'Numero peticion', 'tipo' => 'link', 'href' => 'toa_consumos/detalle_peticion/'],
			'carta_porte' => ['titulo' => 'Tipo trabajo'],
			'empresa'     => ['titulo' => 'Empresa'],
			'cliente'     => ['titulo' => 'Cod Tecnico'],
			'tecnico'     => ['titulo' => 'Nombre Tecnico'],
			'cant'        => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
			'monto'       => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
		];

		return [$datos, $campos];
	}

	private function _get_datos_consumo_tip_material($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'desc_tip_material',
			'ume',
			'id_tip_material',
		];

		$datos = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->order_by($this->reporte->get_order_by($orden_campo))
			->join(config('bd_catalogo_tip_material_toa').' b', 'a.material = b.id_catalogo', 'left', FALSE)
			->join(config('bd_tip_material_toa').' c', 'b.id_tip_material=c.id', 'left', FALSE)
			->get()->result_array();

		$campos = array_merge([
				'desc_tip_material' => ['titulo' => 'Tipo material'],
				'ume'               => ['titulo' => 'Unidad'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'tip_material', ['id_tip_material'])
		);

		return [$datos, $campos];
	}

	private function _get_datos_consumo_material($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'desc_tip_material',
			'material',
			'texto_material',
			'ume',
		];

		$datos = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->order_by($this->reporte->get_order_by($orden_campo))
			->join(config('bd_catalogo_tip_material_toa').' b', 'a.material = b.id_catalogo', 'left', FALSE)
			->join(config('bd_tip_material_toa').' c', 'b.id_tip_material=c.id', 'left', FALSE)
			->get()->result_array();

		$campos = array_merge([
				'desc_tip_material' => ['titulo' => 'Tipo material'],
				'material'          => ['titulo' => 'Cod material'],
				'texto_material'    => ['titulo' => 'Desc material'],
				'ume'               => ['titulo' => 'Unidad'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'material', ['material'])
		);

		return [$datos, $campos];
	}

	private function _get_datos_consumo_lote($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'valor',
			'lote',
		];

		$datos = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->order_by($this->reporte->get_order_by($orden_campo))
			->get()->result_array();

		$campos = array_merge([
				'valor' => ['titulo' => 'Valor'],
				'lote'  => ['titulo' => 'Lote'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'lote', ['lote'])
		);

		return [$datos, $campos];
	}


	private function _get_datos_consumo_lote_material($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'valor',
			'lote',
			'material',
			'texto_material',
		];

		$datos = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->order_by($this->reporte->get_order_by($orden_campo))
			->get()->result_array();

		$campos = array_merge([
				'valor'          => ['titulo' => 'Valor'],
				'lote'           => ['titulo' => 'Lote'],
				'material'       => ['titulo' => 'Cod material'],
				'texto_material' => ['titulo' => 'Desc material'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'lote-material', ['lote', 'material'])
		);

		return [$datos, $campos];
	}

	private function _get_datos_consumo_pep($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'codigo_movimiento',
			'texto_movimiento',
			'elemento_pep',
		];

		$datos = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->order_by($this->reporte->get_order_by($orden_campo))
			->get()->result_array();

		$campos = array_merge([
				'codigo_movimiento' => ['titulo' => 'CodMov'],
				'texto_movimiento'  => ['titulo' => 'Desc Movimiento'],
				'elemento_pep'      => ['titulo' => 'PEP'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'pep', ['codigo_movimiento', 'elemento_pep'])
		);

		return [$datos, $campos];
	}

	private function _get_datos_consumo_tecnicos($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'c.empresa',
			'a.cliente',
			'b.tecnico',
			'a.referencia',
		];

		$query = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->join(config('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->get_compiled_select();
		$query = 'select q1.empresa, q1.cliente, q1.tecnico, \'ver peticiones\' as texto_link, count(referencia) as referencia, sum(cant) as cant, sum(monto) as monto from ('.$query.') q1 group by q1.empresa, q1.cliente, q1.tecnico order by '.$this->reporte->get_order_by($orden_campo);
		$datos = $this->db->query($query)->result_array();

		$campos = array_merge([
				'empresa'    => ['titulo' => 'Empresa'],
				'cliente'    => ['titulo' => 'Cod Tecnico'],
				'tecnico'    => ['titulo' => 'Nombre Tecnico'],
				'referencia' => ['titulo' => 'Peticiones', 'tipo' => 'numero', 'class' => 'text-right'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'tecnicos', ['cliente'])
		);

		return [$datos, $campos];
	}

	private function _get_datos_consumo_ciudades($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'b.id_ciudad',
			'd.ciudad',
			'd.orden',
			'a.referencia',
		];

		$query = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->join(config('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->join(config('bd_ciudades_toa').' d', 'b.id_ciudad = d.id_ciudad', 'left')
			->get_compiled_select();
		$query = 'select q1.id_ciudad, q1.ciudad, q1.orden, \'ver peticiones\' as texto_link, count(referencia) as referencia, sum(cant) as cant, sum(monto) as monto from ('.$query.') q1 group by q1.id_ciudad, q1.ciudad, q1.orden order by '.$this->reporte->get_order_by($orden_campo);
		$datos = $this->db->query($query)->result_array();

		$campos = array_merge([
				// 'empresa' => ['titulo' => 'Empresa'],
				'ciudad'     => ['titulo' => 'Ciudad'],
				'referencia' => ['titulo' => 'Peticiones', 'tipo' => 'numero', 'class' => 'text-right'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'ciudades', ['id_ciudad'])
		);

		return [$datos, $campos];
	}

	private function _get_datos_consumo_empresas($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'c.empresa',
			'c.id_empresa',
			'a.referencia',
		];

		$query = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->join(config('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->get_compiled_select();
		$query = 'select q1.empresa, q1.id_empresa, \'ver peticiones\' as texto_link, count(referencia) as referencia, sum(cant) as cant, sum(monto) as monto from ('.$query.') q1 group by q1.empresa, q1.id_empresa order by '.$this->reporte->get_order_by($orden_campo);
		$datos = $this->db->query($query)->result_array();

		$campos = array_merge([
				'empresa'    => ['titulo' => 'Empresa'],
				'referencia' => ['titulo' => 'Peticiones', 'tipo' => 'numero', 'class' => 'text-right'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'empresas', ['id_empresa'])
		);

		return [$datos, $campos];
	}

	private function _get_datos_consumo_tipo_trabajo($orden_campo = '', $fecha_desde = '', $fecha_hasta = '')
	{
		$select = [
			'a.carta_porte',
			'a.referencia',
		];

		$query = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->get_compiled_select();
		$query = 'select q1.carta_porte, \'ver peticiones\' as texto_link, count(referencia) as referencia, sum(cant) as cant, sum(monto) as monto from ('.$query.') q1 group by q1.carta_porte order by '.$this->reporte->get_order_by($orden_campo);
		$datos = $this->db->query($query)->result_array();

		$campos = array_merge([
				'carta_porte' => ['titulo' => 'Tipo de trabajo'],
				'referencia'  => ['titulo' => 'Peticiones', 'tipo' => 'numero', 'class' => 'text-right'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'tipo_trabajo', ['carta_porte'])
		);

		return [$datos, $campos];
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los consumos realizados en TOA
	 *
	 * @param  string $tipo_reporte Tipo de reporte a desplegar
	 * @param  string $fecha_desde  Fecha desde de los datos del reporte
	 * @param  string $fecha_hasta  Fecha hasta de los datos del reporte
	 * @param  string $orden_campo  Campo para ordenar el resultado
	 * @return string               Reporte
	 */
	public function consumos_toa($tipo_reporte = 'detalle', $fecha_desde = NULL, $fecha_hasta = NULL, $orden_campo = '')
	{
		if ( ! $tipo_reporte OR ! $fecha_desde OR ! $fecha_hasta)
		{
			return '';
		}

		$datos  = [];
		$campos = [];

		$this->db
			->from(config('bd_movimientos_sap_fija').' a')
			->where('fecha_contabilizacion>=', $fecha_desde)
			->where('fecha_contabilizacion<=', $fecha_hasta)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo);

		$orden_reporte = [
			'detalle'       => '+referencia',
			'peticion'      => '+referencia',
			'tip_material'  => '+desc_tip_material',
			'material'      => '+desc_tip_material, +material',
			'lote'          => '+valor, +lote',
			'lote_material' => '+valor',
			'pep'           => '+codigo_movimiento',
			'tecnicos'      => '+empresa, +tecnico',
			'ciudades'      => '+orden',
			'empresas'      => '+empresa',
			'tipo_trabajo'  => '+carta_porte',
		];

		$orden_campo = empty($orden_campo) ? array_get($orden_reporte, $tipo_reporte) : $orden_campo;
		list($datos, $campos) = call_user_func_array([$this, "_get_datos_consumo_{$tipo_reporte}"], [$orden_campo, $fecha_desde, $fecha_hasta]);
		$this->reporte->set_order_campos($campos, $orden_campo);

		return $this->reporte->genera_reporte($campos, $datos);
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
	 * @return array                Arreglo con las peticiones encontradas
	 */
	public function peticiones($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		if ( ! $tipo_reporte)
		{
			return '';
		}

		$arr_data = [];

		$this->db
			->where('fecha_contabilizacion>=', $param1)
			->where('fecha_contabilizacion<=', $param2)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo);

		if ($tipo_reporte === 'material')
		{
			$this->db->where('material', $param3);
		}
		elseif ($tipo_reporte === 'tip_material')
		{
			$this->db->where('id_tip_material', $param3);
		}
		elseif ($tipo_reporte === 'lote')
		{
			$this->db->where('lote', $param3);
		}
		elseif ($tipo_reporte === 'lote-material')
		{
			$this->db->where('lote', $param3);
			$this->db->where('material', $param4);
		}
		elseif ($tipo_reporte === 'pep')
		{
			$this->db->where('codigo_movimiento', $param3);
			$this->db->where('elemento_pep', $param4);
		}
		elseif ($tipo_reporte === 'tecnicos')
		{
			$this->db->where('cliente', $param3);
		}
		elseif ($tipo_reporte === 'ciudades')
		{
			$this->db->where('id_ciudad', $param3);
		}
		elseif ($tipo_reporte === 'empresas')
		{
			$this->db->where('vale_acomp', $param3);
		}
		elseif ($tipo_reporte === 'tipo_trabajo')
		{
			$this->db->where('carta_porte', $param3);
		}
		elseif ($tipo_reporte === 'clientes')
		{
			$this->db->where('d.customer_number', $param3);
		}

		elseif ($tipo_reporte === 'total_cliente')
		{
			$this->db->reset_query();
			return $this->db
				->from(config('bd_peticiones_toa').' p')
				->join(config('bd_movimientos_sap_fija').' m', 'm.referencia=p.appt_number', 'left')
				->where('customer_number', $param3)
				->select('p.date as fecha')
				->select('p.appt_number as referencia')
				->select('upper(p.xa_work_type) as carta_porte', FALSE)
				->select('p.contractor_company as empresa')
				->select('p.resource_external_id as cliente')
				->select('p.resource_name as tecnico')
				->select('p.acoord_x')
				->select('p.acoord_y')
				->select("'ver detalle' as texto_link")
				->select('sum(-m.cantidad_en_um) as cant', FALSE)
				->select('sum(-m.importe_ml) as monto', FALSE)
				->group_by('p.date')
				->group_by('p.appt_number')
				->group_by('p.xa_work_type')
				->group_by('p.contractor_company')
				->group_by('p.resource_external_id')
				->group_by('p.resource_name')
				->group_by('p.acoord_x')
				->group_by('p.acoord_y')
				->get()->result_array();
		}

		$reporte_peticiones = $this->db
			->select('min(a.fecha_contabilizacion) as fecha')
			->select('a.referencia')
			->select('a.carta_porte')
			->select('c.empresa')
			->select('a.cliente')
			->select('b.tecnico')
			->select('d.acoord_x')
			->select('d.acoord_y')
			->select("'ver detalle' as texto_link")
			->select_sum('(-a.cantidad_en_um)', 'cant')
			->select_sum('(-a.importe_ml)', 'monto')
			->group_by('a.referencia')
			->group_by('a.carta_porte')
			->group_by('c.empresa')
			->group_by('a.cliente')
			->group_by('b.tecnico')
			->group_by('d.acoord_x')
			->group_by('d.acoord_y')
			->from(config('bd_movimientos_sap_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'a.vale_acomp=c.id_empresa', 'left', FALSE)
			->join(config('bd_peticiones_toa').' d', 'a.referencia=d.appt_number and d.astatus=\'complete\'', 'left', FALSE)
			->join(config('bd_catalogo_tip_material_toa').' e', 'a.material = e.id_catalogo', 'left', FALSE)
			->join(config('bd_tip_material_toa').' f', 'e.id_tip_material=f.id', 'left', FALSE)
			->order_by('a.referencia', 'ASC')
			->get()->result_array();

		$peticiones =collect($reporte_peticiones)->pluck('referencia')->all();
		$uso = $this->uso_peticiones($peticiones);

		return collect($reporte_peticiones)->map(function($peticion) use ($uso) {
			return array_merge($peticion, ['uso' => array_get($uso, $peticion['referencia'])]);
		});
	}

	// --------------------------------------------------------------------

	public function uso_peticiones($peticiones = [])
	{
		return collect($this->db
			->select('appt_number')->group_by('appt_number')
			->select("sum(case uso when 'OK' then 1 else 0 end) as OK")
			->select('count(*) as TOTAL')
			->where_in('appt_number', $peticiones)
			->get(config('bd_uso_toa_dia'))
			->result_array()
			)->map_with_keys(function($peticion) {
				$uso = (int) (100*$peticion['OK']/$peticion['TOTAL']);
				$class = ($uso >= 90) ? 'success' : (($uso >= 80) ? 'warning' : 'danger');

				return [$peticion['appt_number'] => "<span class=\"label label-$class\">$uso%</span>" ];
			})->all();
	}


	// --------------------------------------------------------------------

	/**
	 * Genera reporte de peticiones
	 *
	 * @param  array  $arr_data Arreglo con datos a desplegar
	 * @return string           Reporte con las peticiones encontradas
	 */
	public function reporte_peticiones($arr_data = NULL)
	{
		if ( ! $arr_data)
		{
			return NULL;
		}

		$arr_campos = [];
		$arr_campos['referencia'] = ['titulo' => 'Numero peticion', 'tipo' => 'link', 'href' => 'toa_consumos/detalle_peticion/'];
		$arr_campos['fecha']      = ['titulo' => 'Fecha', 'tipo' => 'fecha'];
		$arr_campos['carta_porte'] = ['titulo' => 'Tipo trabajo'];
		$arr_campos['empresa']    = ['titulo' => 'Empresa'];
		$arr_campos['cliente']    = ['titulo' => 'Cod Tecnico'];
		$arr_campos['tecnico']    = ['titulo' => 'Nombre Tecnico'];
		$arr_campos['cant']       = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
		$arr_campos['monto']      = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
		$arr_campos['uso']        = ['titulo' => '%Uso', 'class' => 'text-center'];
		$this->reporte->set_order_campos($arr_campos, 'referencia');

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera datos de detalle de una peticion
	 *
	 * @param  string $id_peticion ID de la peticion
	 * @return array            Detalle de la peticion
	 */
	public function detalle_peticion($id_peticion = NULL)
	{
		if ( ! $id_peticion)
		{
			return [];
		}

		$peticion_toa = $this->db
			->from(config('bd_peticiones_toa').' d')
			->join(config('bd_empresas_toa').' c', 'd.contractor_company=c.id_empresa', 'left', FALSE)
			->where('appt_number', $id_peticion)
			->where('astatus', 'complete')
			->get()->row_array();

		$materiales_sap = $this->db
			->select('a.fecha_contabilizacion as fecha')
			->select('a.referencia')
			->select('c.empresa')
			->select('a.cliente')
			->select('b.tecnico')
			->select('a.codigo_movimiento')
			->select('a.texto_movimiento')
			->select('a.elemento_pep')
			->select('a.documento_material')
			->select('a.centro')
			->select('a.almacen')
			->select('a.material')
			->select('a.texto_material')
			->select('a.serie_toa')
			->select('a.lote')
			->select('a.valor')
			->select('a.umb')
			->select('(-a.cantidad_en_um) as cant', FALSE)
			->select('(-a.importe_ml) as monto', FALSE)
			->select('a.usuario')
			->select('a.vale_acomp')
			->select('a.carta_porte')
			->select('a.usuario')
			->select('e.desc_tip_material')
			->from(config('bd_movimientos_sap_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'a.vale_acomp=c.id_empresa', 'left', FALSE)
			->join(config('bd_catalogo_tip_material_toa').' d', 'a.material=d.id_catalogo', 'left', FALSE)
			->join(config('bd_tip_material_toa').' e', 'd.id_tip_material=e.id', 'left', FALSE)
			->where('referencia', $id_peticion)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			->order_by('e.desc_tip_material, a.material, a.codigo_movimiento')
			->get()->result_array();

		$materiales_toa = $this->db
			->select('a.*, c.desc_tip_material')
			->from(config('bd_materiales_peticiones_toa').' a')
			->join(config('bd_catalogo_tip_material_toa').' b', 'a.XI_SAP_CODE=b.id_catalogo', 'left', FALSE)
			->join(config('bd_tip_material_toa').' c', 'b.id_tip_material=c.id and c.uso_vpi=1', 'left', FALSE)
			->where('aid', array_get($peticion_toa, 'aid'))
			->order_by('c.desc_tip_material, XI_SAP_CODE')
			->get()->result_array();

		$materiales_vpi = $this->db
			->select('a.*, c.desc_tip_material')
			->from(config('bd_peticiones_vpi').' a')
			->join(config('bd_ps_tip_material_toa').' b', 'a.ps_id=b.ps_id', 'left', FALSE)
			->join(config('bd_tip_material_toa').' c', 'b.id_tip_material=c.id', 'left', FALSE)
			->where('appt_number', $id_peticion)
			->order_by('c.desc_tip_material, a.ps_id')
			->get()->result_array();

		$peticion_repara = $this->db->from(config('bd_peticiones_toa'))
			->select('a_complete_reason_rep_minor_stb as stb_clave')
			->select('a.rev_clave as stb_rev_clave')
			->select('a.descripcion as stb_descripcion')
			->select('a.agrupacion_cs as stb_agrupacion_cs')
			->select('a.ambito as stb_ambito')
			->select('a_complete_reason_rep_minor_ba as ba_clave')
			->select('b.rev_clave as ba_rev_clave')
			->select('b.descripcion as ba_descripcion')
			->select('b.agrupacion_cs as ba_agrupacion_cs')
			->select('b.ambito as ba_ambito')
			->select('a_complete_reason_rep_minor_tv as tv_clave')
			->select('c.rev_clave as tv_rev_clave')
			->select('c.descripcion as tv_descripcion')
			->select('c.agrupacion_cs as tv_agrupacion_cs')
			->select('c.ambito as tv_ambito')
			->join(config('bd_claves_cierre_toa').' a', 'a_complete_reason_rep_minor_stb=a.clave', 'left')
			->join(config('bd_claves_cierre_toa').' b', 'a_complete_reason_rep_minor_ba=b.clave', 'left')
			->join(config('bd_claves_cierre_toa').' c', 'a_complete_reason_rep_minor_tv=c.clave', 'left')
			->where('appt_number', $id_peticion)
			->get()->result_array();

		return [
			'peticion_toa'   => $peticion_toa,
			'materiales_sap' => $materiales_sap,
			'materiales_toa' => $materiales_toa,
			'materiales_vpi' => $materiales_vpi,
			'peticion_repara' => $peticion_repara,
		];
	}




}

/* End of file Consumo_toa.php */
/* Location: ./application/models/Toa/Consumo_toa.php */
