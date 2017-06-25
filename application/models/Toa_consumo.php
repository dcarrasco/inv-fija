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
		'lote-material' => 'Lotes y materiales',
		'pep'           => 'PEP',
		'tipo-trabajo'  => 'Tipo de trabajo',
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

		$arr_data = [];

		$this->db
			->where('fecha_contabilizacion>=', $fecha_desde)
			->where('fecha_contabilizacion<=', $fecha_hasta)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo);

		if ($tipo_reporte === 'detalle')
		{
			$orden_campo = empty($orden_campo) ? '+referencia' : $orden_campo;

			$arr_data = $this->db
				->select('a.fecha_contabilizacion as fecha')
				->select('a.referencia')
				->select('a.carta_porte')
				->select('c.empresa')
				->select('a.cliente')
				->select('b.tecnico')
				->select('a.codigo_movimiento')
				->select('a.texto_movimiento')
				->select('a.elemento_pep')
				->select('a.documento_material')
				->select('a.centro')
				->select('a.material')
				->select('a.texto_material')
				->select('a.lote')
				->select('a.valor')
				->select('a.umb')
				->select('(-a.cantidad_en_um) as cant', FALSE)
				->select('(-a.importe_ml) as monto', FALSE)
				->select('a.usuario')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'a.vale_acomp = c.id_empresa', 'left', FALSE)
				->order_by($this->reporte->get_order_by($orden_campo))
				->get()->result_array();

			$arr_campos = [];
			$arr_campos['fecha']              = ['titulo' => 'Fecha', 'tipo' => 'fecha'];
			$arr_campos['referencia']         = ['titulo' => 'Numero Peticion'];
			$arr_campos['carta_porte']        = ['titulo' => 'Tipo trabajo'];
			$arr_campos['empresa']            = ['titulo' => 'Empresa'];
			$arr_campos['cliente']            = ['titulo' => 'Cod Tecnico'];
			$arr_campos['tecnico']            = ['titulo' => 'Nombre Tecnico'];
			$arr_campos['codigo_movimiento']  = ['titulo' => 'Cod Movimiento'];
			$arr_campos['texto_movimiento']   = ['titulo' => 'Desc Movimiento'];
			$arr_campos['elemento_pep']       = ['titulo' => 'PEP'];
			$arr_campos['documento_material'] = ['titulo' => 'Documento SAP'];
			$arr_campos['centro']             = ['titulo' => 'Centro'];
			$arr_campos['material']           = ['titulo' => 'Cod material'];
			$arr_campos['texto_material']     = ['titulo' => 'Desc material'];
			$arr_campos['lote']               = ['titulo' => 'Lote'];
			$arr_campos['valor']              = ['titulo' => 'Valor'];
			$arr_campos['umb']                = ['titulo' => 'Unidad'];
			$arr_campos['cant']               = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']              = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['usuario']            = ['titulo' => 'Usuario SAP'];
			$this->reporte->set_order_campos($arr_campos, 'referencia');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'peticion')
		{
			$orden_campo = empty($orden_campo) ? '+referencia' : $orden_campo;

			$arr_data = $this->db
				->select('a.referencia')
				->select('a.carta_porte')
				->select('c.empresa')
				->select('a.cliente')
				->select("'ver detalle' as texto_link")
				->select('b.tecnico')
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('a.referencia')
				->group_by('a.vale_acomp')
				->group_by('a.carta_porte')
				->group_by('c.empresa')
				->group_by('a.cliente')
				->group_by('b.tecnico')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'a.vale_acomp = c.id_empresa', 'left', FALSE)
				->order_by($this->reporte->get_order_by($orden_campo))
				->get()->result_array();

			$arr_campos = [];
			$arr_campos['referencia'] = ['titulo' => 'Numero peticion', 'tipo' => 'link', 'href' => 'toa_consumos/detalle_peticion/'];
			$arr_campos['carta_porte'] = ['titulo' => 'Tipo trabajo'];
			$arr_campos['empresa']    = ['titulo' => 'Empresa'];
			$arr_campos['cliente']    = ['titulo' => 'Cod Tecnico'];
			$arr_campos['tecnico']    = ['titulo' => 'Nombre Tecnico'];
			$arr_campos['cant']       = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']      = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$this->reporte->set_order_campos($arr_campos, 'referencia');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'tip_material')
		{
			$orden_campo = empty($orden_campo) ? '+desc_tip_material' : $orden_campo;

			$arr_data = $this->db
				->select('desc_tip_material')
				->select('ume')
				->select('id_tip_material_trabajo')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('desc_tip_material')
				->group_by('ume')
				->group_by('id_tip_material_trabajo')
				->order_by($this->reporte->get_order_by($orden_campo))
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_catalogo_tip_material_toa').' b', 'a.material = b.id_catalogo', 'left', FALSE)
				->join($this->config->item('bd_tip_material_trabajo_toa').' c', 'b.id_tip_material_trabajo = c.id', 'left', FALSE)
				->get()->result_array();

			$arr_campos = [];
			$arr_campos['desc_tip_material'] = ['titulo' => 'Tipo material'];
			$arr_campos['ume'] = ['titulo' => 'Unidad'];
			$arr_campos['cant'] = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto'] = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link'] = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/tip_material/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['id_tip_material_trabajo']];
			$this->reporte->set_order_campos($arr_campos, 'material');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'material')
		{
			$orden_campo = empty($orden_campo) ? '+desc_tip_material, +material' : $orden_campo;

			$arr_data = $this->db
				->select('desc_tip_material')
				->select('material')
				->select('texto_material')
				->select('ume')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('desc_tip_material')
				->group_by('material')
				->group_by('texto_material')
				->group_by('ume')
				->order_by($this->reporte->get_order_by($orden_campo))
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_catalogo_tip_material_toa').' b', 'a.material = b.id_catalogo', 'left', FALSE)
				->join($this->config->item('bd_tip_material_trabajo_toa').' c', 'b.id_tip_material_trabajo = c.id', 'left', FALSE)
				->get()->result_array();

			$arr_campos = [];
			$arr_campos['desc_tip_material'] = ['titulo' => 'Tipo material'];
			$arr_campos['material']       = ['titulo' => 'Cod material'];
			$arr_campos['texto_material'] = ['titulo' => 'Desc material'];
			$arr_campos['ume']            = ['titulo' => 'Unidad'];
			$arr_campos['cant']           = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']          = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link']     = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/material/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['material']];
			$this->reporte->set_order_campos($arr_campos, 'material');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'lote')
		{
			$orden_campo = empty($orden_campo) ? '+valor, +lote' : $orden_campo;

			$arr_data = $this->db
				->select('valor')
				->select('lote')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('valor')
				->group_by('lote')
				->order_by($this->reporte->get_order_by($orden_campo))
				->get($this->config->item('bd_movimientos_sap_fija'))
				->result_array();

			$arr_campos = [];
			$arr_campos['valor'] = ['titulo' => 'Valor'];
			$arr_campos['lote']  = ['titulo' => 'Lote'];
			$arr_campos['cant']  = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto'] = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link'] = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/lote/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['lote']];
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'lote-material')
		{
			$orden_campo = empty($orden_campo) ? '+valor' : $orden_campo;

			$arr_data = $this->db
				->select('valor')
				->select('lote')
				->select('material')
				->select('texto_material')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('valor')
				->group_by('lote')
				->group_by('material')
				->group_by('texto_material')
				->order_by($this->reporte->get_order_by($orden_campo))
				->get($this->config->item('bd_movimientos_sap_fija'))
				->result_array();

			$arr_campos = [];
			$arr_campos['valor']          = ['titulo' => 'Valor'];
			$arr_campos['lote']           = ['titulo' => 'Lote'];
			$arr_campos['material']       = ['titulo' => 'Cod material'];
			$arr_campos['texto_material'] = ['titulo' => 'Desc material'];
			$arr_campos['cant']           = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']          = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link']     = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/lote-material/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['lote','material']];
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'pep')
		{
			$orden_campo = empty($orden_campo) ? '+codigo_movimiento' : $orden_campo;

			$arr_data = $this->db
				->select('codigo_movimiento')
				->select('texto_movimiento')
				->select('elemento_pep')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('codigo_movimiento')
				->group_by('texto_movimiento')
				->group_by('elemento_pep')
				->order_by($this->reporte->get_order_by($orden_campo))
				->get($this->config->item('bd_movimientos_sap_fija'))
				->result_array();

			$arr_campos = [];
			$arr_campos['codigo_movimiento'] = ['titulo' => 'CodMov'];
			$arr_campos['texto_movimiento']  = ['titulo' => 'Desc Movimiento'];
			$arr_campos['elemento_pep']      = ['titulo' => 'PEP'];
			$arr_campos['cant']              = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']             = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link']        = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/pep/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['codigo_movimiento', 'elemento_pep']];
			$this->reporte->set_order_campos($arr_campos, 'codigo_movimiento');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'tecnicos')
		{
			$orden_campo = empty($orden_campo) ? '+empresa, +tecnico' : $orden_campo;

			$query = $this->db
				->select('c.empresa')
				->select('a.cliente')
				->select('b.tecnico')
				->select('a.referencia')
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('c.empresa')
				->group_by('a.cliente')
				->group_by('b.tecnico')
				->group_by('a.referencia')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->get_compiled_select();

			$query = 'select q1.empresa, q1.cliente, q1.tecnico, \'ver peticiones\' as texto_link, count(referencia) as referencia, sum(cant) as cant, sum(monto) as monto from ('.$query.') q1 group by q1.empresa, q1.cliente, q1.tecnico order by '.$this->reporte->get_order_by($orden_campo);

			$arr_data = $this->db->query($query)->result_array();

			$arr_campos = [];
			$arr_campos['empresa'] = ['titulo' => 'Empresa'];
			$arr_campos['cliente'] = ['titulo' => 'Cod Tecnico'];
			$arr_campos['tecnico'] = ['titulo' => 'Nombre Tecnico'];
			$arr_campos['referencia'] = ['titulo' => 'Peticiones', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['cant']    = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']   = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link'] = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/tecnicos/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['cliente']];
			$this->reporte->set_order_campos($arr_campos, 'empresa');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'ciudades')
		{
			$orden_campo = empty($orden_campo) ? '+orden' : $orden_campo;

			$query = $this->db
				->select('b.id_ciudad')
				->select('d.ciudad')
				->select('d.orden')
				->select('a.referencia')
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('b.id_ciudad')
				->group_by('d.ciudad')
				->group_by('d.orden')
				->group_by('a.referencia')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->join($this->config->item('bd_ciudades_toa').' d', 'b.id_ciudad = d.id_ciudad', 'left')
				->get_compiled_select();

			$query = 'select q1.id_ciudad, q1.ciudad, q1.orden, \'ver peticiones\' as texto_link, count(referencia) as referencia, sum(cant) as cant, sum(monto) as monto from ('.$query.') q1 group by q1.id_ciudad, q1.ciudad, q1.orden order by '.$this->reporte->get_order_by($orden_campo);

			$arr_data = $this->db->query($query)->result_array();

			$arr_campos = [];
			// $arr_campos['empresa'] = ['titulo' => 'Empresa'];
			$arr_campos['ciudad'] = ['titulo' => 'Ciudad'];
			$arr_campos['referencia'] = ['titulo' => 'Peticiones', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['cant']    = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']   = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link'] = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/ciudades/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['id_ciudad']];
			$this->reporte->set_order_campos($arr_campos, 'empresa');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'empresas')
		{
			$orden_campo = empty($orden_campo) ? '+empresa' : $orden_campo;

			$query = $this->db
				->select('c.empresa')
				->select('c.id_empresa')
				->select('a.referencia')
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('c.empresa')
				->group_by('c.id_empresa')
				->group_by('a.referencia')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente = b.id_tecnico', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->get_compiled_select();

			$query = 'select q1.empresa, q1.id_empresa, \'ver peticiones\' as texto_link, count(referencia) as referencia, sum(cant) as cant, sum(monto) as monto from ('.$query.') q1 group by q1.empresa, q1.id_empresa order by '.$this->reporte->get_order_by($orden_campo);

			$arr_data = $this->db->query($query)->result_array();

			$arr_campos = [];
			$arr_campos['empresa'] = ['titulo' => 'Empresa'];
			$arr_campos['referencia'] = ['titulo' => 'Peticiones', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['cant']    = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']   = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link'] = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/empresas/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['id_empresa']];
			$this->reporte->set_order_campos($arr_campos, 'empresa');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'tipo-trabajo')
		{
			$orden_campo = empty($orden_campo) ? '+carta_porte' : $orden_campo;

			$query = $this->db
				->select('a.carta_porte')
				->select('a.referencia')
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('a.carta_porte')
				->group_by('a.referencia')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->get_compiled_select();

			$query = 'select q1.carta_porte, \'ver peticiones\' as texto_link, count(referencia) as referencia, sum(cant) as cant, sum(monto) as monto from ('.$query.') q1 group by q1.carta_porte order by '.$this->reporte->get_order_by($orden_campo);

			$arr_data = $this->db->query($query)->result_array();

			$arr_campos = [];
			$arr_campos['carta_porte'] = ['titulo' => 'Tipo de trabajo'];
			$arr_campos['referencia']  = ['titulo' => 'Peticiones', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['cant']        = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']       = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link']  = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/tipo_trabajo/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['carta_porte']];
			$this->reporte->set_order_campos($arr_campos, 'carta_porte');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
	}





}

/* End of file Toa_model.php */
/* Location: ./application/models/Toa_model.php */
