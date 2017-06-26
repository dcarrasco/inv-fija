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
 * Clase Modelo Movimientos fija
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_consumo extends CI_Model {

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
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'a.vale_acomp = c.id_empresa', 'left', FALSE)
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
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'a.vale_acomp = c.id_empresa', 'left', FALSE)
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
			'id_tip_material_trabajo',
		];

		$datos = $this->db->select(array_merge($this->_select_base, $select), FALSE)
			->group_by($select)
			->order_by($this->reporte->get_order_by($orden_campo))
			->join($this->config->item('bd_catalogo_tip_material_toa').' b', 'a.material = b.id_catalogo', 'left', FALSE)
			->join($this->config->item('bd_tip_material_trabajo_toa').' c', 'b.id_tip_material_trabajo = c.id', 'left', FALSE)
			->get()->result_array();

		$campos = array_merge([
				'desc_tip_material' => ['titulo' => 'Tipo material'],
				'ume'               => ['titulo' => 'Unidad'],
			],
			$this->_texto_link($fecha_desde, $fecha_hasta, 'tip_material', ['id_tip_material_trabajo'])
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
			->join($this->config->item('bd_catalogo_tip_material_toa').' b', 'a.material = b.id_catalogo', 'left', FALSE)
			->join($this->config->item('bd_tip_material_trabajo_toa').' c', 'b.id_tip_material_trabajo = c.id', 'left', FALSE)
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
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
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
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->join($this->config->item('bd_ciudades_toa').' d', 'b.id_ciudad = d.id_ciudad', 'left')
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
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
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
			->from($this->config->item('bd_movimientos_sap_fija').' a')
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

}

/* End of file Toa_model.php */
/* Location: ./application/models/Toa_model.php */
