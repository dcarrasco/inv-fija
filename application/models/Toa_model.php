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
class Toa_model extends CI_Model {

	/**
	 * Movimientos validos de consumos TOA
	 *
	 * @var array
	 */
	public $movimientos_consumo = array('Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89');
	// public $movimientos_consumo = array('Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89', 'Z33');
	// Z33 devolucion de materiales: viene con un idtecnico distinto
	// public $movimientos_consumo = array('Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89', 'Z81', 'Z82', 'Z83', 'Z84');
	// Z81 CAPEX  Z82 ANULA CAPEX  / Z83 OPEX y Z84 ANULA OPEX   Regularizaciones Manuales

	/**
	 * Movimientos validos de asignaciones TOA
	 *
	 * @var array
	 */
	public $movimientos_asignaciones = array('Z31', 'Z32');

	/**
	 * Centros validos de consumos TOA
	 *
	 * @var array
	 */
	public $centros_consumo = array('CH32', 'CH33');

	/**
	 * Tipos de reporte asignaciones
	 *
	 * @var array
	 */
	public $tipos_reporte_asignaciones = array(
		'tecnicos'      => 'T&eacute;cnicos',
		'material'      => 'Materiales',
		'lote'          => 'Lotes',
		'lote-material' => 'Lotes y materiales',
		'detalle'       => 'Detalle todos los registros',
	);

	/**
	 * Dias de la semana
	 *
	 * @var array
	 */
	public $dias_de_la_semana = array(
		'0' => 'Do',
		'1' => 'Lu',
		'2' => 'Ma',
		'3' => 'Mi',
		'4' => 'Ju',
		'5' => 'Vi',
		'6' => 'Sa',
	);

	/**
	 * Combo de unidades reporte control tecnicos
	 *
	 * @var array
	 */
	public $combo_unidades_consumo = array(
		'peticiones' => 'Cantidad de peticiones',
		'unidades'   => 'Suma de unidades',
		'monto'      => 'Suma de montos',
	);

	/**
	 * Combo de unidades reporte control asignaciones
	 *
	 * @var array
	 */
	public $combo_unidades_asignacion = array(
		'asignaciones' => 'Cantidad de asignaciones',
		'unidades'     => 'Suma de unidades',
		'monto'        => 'Suma de montos',
	);

	/**
	 * Combo de unidades reporte control materiales consumidos
	 *
	 * @var array
	 */
	public $combo_unidades_materiales_consumidos = array(
		'unidades' => 'Suma de unidades',
		'monto'    => 'Suma de montos',
	);

	/**
	 * Combo de unidades reporte control materiales por tipo de trabajo
	 *
	 * @var array
	 */
	public $combo_unidades_materiales_tipo_trabajo = array(
		'unidades'     => 'Suma de unidades',
		'monto'        => 'Suma de montos',
	);

	/**
	 * Arreglo con validación formulario panel
	 *
	 * @var array
	 */
	public $panel_validation = array(
		array(
			'field' => 'empresa',
			'label' => 'Empresa',
			'rules' => 'required',
		),
		array(
			'field' => 'mes',
			'label' => 'Mes',
			'rules' => 'required',
		),
	);

	/**
	 * Arreglo con validación formulario controles consumo
	 *
	 * @var array
	 */
	public $controles_consumos_validation = array(
		array(
			'field' => 'empresa',
			'label' => 'Empresa',
			'rules' => 'required',
		),
		array(
			'field' => 'mes',
			'label' => 'Mes',
			'rules' => 'required',
		),
		array(
			'field' => 'filtro_trx',
			'label' => 'Filtro transaccion',
			'rules' => 'required',
		),
		array(
			'field' => 'dato',
			'label' => 'Dato a desplegar',
			'rules' => 'required',
		),
	);

	/**
	 * Arreglo con validación formulario controles materiales
	 *
	 * @var array
	 */
	public $controles_materiales_validation = array(
		array(
			'field' => 'empresa',
			'label' => 'Empresa',
			'rules' => 'required',
		),
		array(
			'field' => 'mes',
			'label' => 'Mes',
			'rules' => 'required',
		),
		array(
			'field' => 'tipo_trabajo',
			'label' => 'Tipos de trabajo',
			'rules' => 'required',
		),
		array(
			'field' => 'dato',
			'label' => 'Dato a desplegar',
			'rules' => 'required',
		),
	);

	/**
	 * Arreglo con validación formulario controles clientes
	 *
	 * @var array
	 */
	public $controles_clientes_validation = array(
		array(
			'field' => 'cliente',
			'label' => 'Cliente',
			'rules' => 'trim',
		),
		array(
			'field' => 'fecha_desde',
			'label' => 'Fecha desde',
			'rules' => 'required',
		),
		array(
			'field' => 'fecha_hasta',
			'label' => 'Fecha hasta',
			'rules' => 'required',
		),
	);


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
	 * Devuelve combo con las clases de movimiento de consumo
	 *
	 * @return array Clases de movimiento de consumo
	 */
	public function get_combo_movimientos_consumo()
	{
		$cmv = new Clase_movimiento;

		return array_merge(
			array('000' => 'Todos los movimientos'),
			$cmv->find('list', array(
				'conditions' => array('cmv' => $this->movimientos_consumo),
				'order_by'   => 'des_cmv',
				'opc_ini'    => FALSE,
			))
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve combo con las clases de movimiento de asignacion
	 *
	 * @return array Clases de movimiento de asignacion
	 */
	public function get_combo_movimientos_asignacion()
	{
		$cmv = new Clase_movimiento;

		return array_merge(
			array('000' => 'Todos los movimientos'),
			$cmv->find('list', array(
				'conditions' => array('cmv' => $this->movimientos_asignaciones),
				'opc_ini' => FALSE,
			))
		);
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
	public function peticiones_toa($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		if ( ! $tipo_reporte)
		{
			return '';
		}

		$arr_data = array();

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
			$this->db->where('id_tip_material_trabajo', $param3);
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
				->from($this->config->item('bd_peticiones_toa').' p')
				->join($this->config->item('bd_movimientos_sap_fija').' m', 'm.referencia=p.appt_number', 'left')
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
				->select("sum(-m.cantidad_en_um) as cant", FALSE)
				->select("sum(-m.importe_ml) as monto", FALSE)
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

		return $this->db
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
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'a.vale_acomp=c.id_empresa', 'left', FALSE)
			->join($this->config->item('bd_peticiones_toa').' d', 'a.referencia=d.appt_number and d.astatus=\'complete\'', 'left', FALSE)
			->join($this->config->item('bd_catalogo_tip_material_toa').' e', 'a.material = e.id_catalogo', 'left', FALSE)
			->join($this->config->item('bd_tip_material_trabajo_toa').' f', 'e.id_tip_material_trabajo = f.id', 'left', FALSE)
			->order_by('a.referencia', 'ASC')
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera reporte de peticiones
	 *
	 * @param  array  $arr_data Arreglo con datos a desplegar
	 * @return string           Reporte con las peticiones encontradas
	 */
	public function reporte_peticiones_toa($arr_data = NULL)
	{
		if ( ! $arr_data)
		{
			return NULL;
		}

		$arr_campos = array();
		$arr_campos['referencia'] = array('titulo' => 'Numero peticion', 'tipo' => 'link', 'href' => 'toa_consumos/detalle_peticion/');
		$arr_campos['fecha']      = array('titulo' => 'Fecha', 'tipo' => 'fecha');
		$arr_campos['carta_porte'] = array('titulo' => 'Tipo trabajo', 'tipo' => 'texto');
		$arr_campos['empresa']    = array('titulo' => 'Empresa', 'tipo' => 'texto');
		$arr_campos['cliente']    = array('titulo' => 'Cod Tecnico', 'tipo' => 'texto');
		$arr_campos['tecnico']    = array('titulo' => 'Nombre Tecnico', 'tipo' => 'texto');
		$arr_campos['cant']       = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
		$arr_campos['monto']      = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
		$this->reporte->set_order_campos($arr_campos, 'referencia');

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera datos de detalle de una peticion
	 *
	 * @param  string $peticion ID de la peticion
	 * @return array            Detalle de la peticion
	 */
	public function detalle_peticion_toa($peticion = NULL)
	{
		if ( ! $peticion)
		{
			return array();
		}

		$arr_peticion_toa = $this->db
			->from($this->config->item('bd_peticiones_toa').' d')
			->join($this->config->item('bd_empresas_toa').' c', 'd.contractor_company=c.id_empresa', 'left', FALSE)
			->where('appt_number', $peticion)
			->where('astatus', 'complete')
			->get()->row_array();

		$arr_materiales_sap = $this->db
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
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'a.vale_acomp=c.id_empresa', 'left', FALSE)
			->where('referencia', $peticion)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			->order_by('a.material, a.codigo_movimiento')
			->get()->result_array();

		$arr_materiales_toa = $this->db
			->where('aid', $arr_peticion_toa['aid'])
			->order_by('XI_SAP_CODE')
			->get($this->config->item('bd_materiales_peticiones_toa'))
			->result_array();

		$arr_materiales_vpi = $this->db
			->where('appt_number', $arr_peticion_toa['appt_number'])
			->order_by('ps_id')
			->get($this->config->item('bd_peticiones_vpi'))
			->result_array();

		$arr_peticion_repara = $this->db->from($this->config->item('bd_peticiones_toa'))
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
			->join($this->config->item('bd_claves_cierre_toa').' a', 'a_complete_reason_rep_minor_stb=a.clave', 'left')
			->join($this->config->item('bd_claves_cierre_toa').' b', 'a_complete_reason_rep_minor_ba=b.clave', 'left')
			->join($this->config->item('bd_claves_cierre_toa').' c', 'a_complete_reason_rep_minor_tv=c.clave', 'left')
			->where('appt_number', $peticion)
			->get()->result_array();

		return array(
			'arr_peticion_toa'   => $arr_peticion_toa,
			'arr_materiales_sap' => $arr_materiales_sap,
			'arr_materiales_toa' => $arr_materiales_toa,
			'arr_materiales_vpi' => $arr_materiales_vpi,
			'arr_peticion_repara' => $arr_peticion_repara,
		);
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

		$arr_data = array();

		$this->db
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<=', $fecha_hasta)
			->where_in('a.codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('a.centro', $this->centros_consumo)
			->where('a.signo','NEG');

		$orden_tipo = ($orden_tipo === '') ? 'ASC' : $orden_tipo;

		if ($tipo_reporte === 'detalle')
		{
			$orden_campo = empty($orden_campo) ? 'referencia' : $orden_campo;

			$arr_data = $this->db
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
				->select('a.material')
				->select('a.texto_material')
				->select('a.lote')
				->select('a.valor')
				->select('a.umb')
				->select('(-a.cantidad_en_um) as cant', FALSE)
				->select('(-a.importe_ml) as monto', FALSE)
				->select('a.usuario')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				//->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']              = array('titulo' => 'Fecha', 'tipo' => 'fecha');
			$arr_campos['referencia']         = array('titulo' => 'Numero Peticion', 'tipo' => 'texto');
			$arr_campos['empresa']            = array('titulo' => 'Empresa', 'tipo' => 'texto');
			$arr_campos['cliente']            = array('titulo' => 'Cod Tecnico', 'tipo' => 'texto');
			$arr_campos['tecnico']            = array('titulo' => 'Nombre Tecnico', 'tipo' => 'texto');
			$arr_campos['codigo_movimiento']  = array('titulo' => 'Cod Movimiento', 'tipo' => 'texto');
			$arr_campos['texto_movimiento']   = array('titulo' => 'Desc Movimiento', 'tipo' => 'texto');
			$arr_campos['elemento_pep']       = array('titulo' => 'PEP', 'tipo' => 'texto');
			$arr_campos['documento_material'] = array('titulo' => 'Documento SAP', 'tipo' => 'texto');
			$arr_campos['centro']             = array('titulo' => 'Centro', 'tipo' => 'texto');
			$arr_campos['material']           = array('titulo' => 'Cod material', 'tipo' => 'texto');
			$arr_campos['texto_material']     = array('titulo' => 'Desc material', 'tipo' => 'texto');
			$arr_campos['lote']               = array('titulo' => 'Lote', 'tipo' => 'texto');
			$arr_campos['valor']              = array('titulo' => 'Valor', 'tipo' => 'texto');
			$arr_campos['umb']                = array('titulo' => 'Unidad', 'tipo' => 'texto');
			$arr_campos['cant']               = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']              = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['usuario']            = array('titulo' => 'Usuario SAP', 'tipo' => 'texto');
			$this->reporte->set_order_campos($arr_campos, 'referencia');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'material')
		{
			$orden_campo = empty($orden_campo) ? 'material' : $orden_campo;

			$arr_data = $this->db
				->select('material')
				->select('texto_material')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('material')
				->group_by('texto_material')
				//->order_by($orden_campo, $orden_tipo)
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['material']       = array('titulo' => 'Cod material', 'tipo' => 'texto');
			$arr_campos['texto_material'] = array('titulo' => 'Desc material', 'tipo' => 'texto');
			$arr_campos['cant']           = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']          = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/material/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => array('material'));
			$this->reporte->set_order_campos($arr_campos, 'material');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'lote')
		{
			$orden_campo = empty($orden_campo) ? 'valor' : $orden_campo;

			$arr_data = $this->db
				->select('valor')
				->select('lote')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('valor')
				->group_by('lote')
				//->order_by($orden_campo, $orden_tipo)
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['valor'] = array('titulo' => 'Valor', 'tipo' => 'texto');
			$arr_campos['lote']  = array('titulo' => 'Lote', 'tipo' => 'texto');
			$arr_campos['cant']  = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto'] = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link'] = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/lote/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => array('lote'));
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'lote-material')
		{
			$orden_campo = empty($orden_campo) ? 'valor' : $orden_campo;

			$arr_data = $this->db
				->select('valor')
				->select('lote')
				->select('material')
				->select('texto_material')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('valor')
				->group_by('lote')
				->group_by('material')
				->group_by('texto_material')
				//->order_by($orden_campo, $orden_tipo)
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['valor']          = array('titulo' => 'Valor', 'tipo' => 'texto');
			$arr_campos['lote']           = array('titulo' => 'Lote', 'tipo' => 'texto');
			$arr_campos['material']       = array('titulo' => 'Cod material', 'tipo' => 'texto');
			$arr_campos['texto_material'] = array('titulo' => 'Desc material', 'tipo' => 'texto');
			$arr_campos['cant']           = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']          = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/lote-material/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => array('lote','material'));
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'tecnicos')
		{
			$orden_campo = empty($orden_campo) ? 'empresa' : $orden_campo;

			$arr_data = $this->db
				->select('c.empresa')
				->select('a.cliente')
				->select('b.tecnico')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('c.empresa')
				->group_by('a.cliente')
				->group_by('b.tecnico')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->join($this->config->item('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left')
				//->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['empresa'] = array('titulo' => 'Empresa', 'tipo' => 'texto');
			$arr_campos['cliente'] = array('titulo' => 'Cod Tecnico', 'tipo' => 'texto');
			$arr_campos['tecnico'] = array('titulo' => 'Nombre Tecnico', 'tipo' => 'texto');
			$arr_campos['cant']    = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']   = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link'] = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/tecnicos/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => array('cliente'));
			$this->reporte->set_order_campos($arr_campos, 'empresa');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
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

		$arr_data = array();

		$this->db
			->where('a.fecha_contabilizacion>=', $param1)
			->where('a.fecha_contabilizacion<=', $param2)
			->where_in('a.codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('a.centro', $this->centros_consumo)
			->where('a.signo', 'NEG');

		if ($tipo_reporte === 'material')
		{
			$this->db->where('a.material', $param3);
		}
		elseif ($tipo_reporte === 'lote')
		{
			$this->db->where('a.lote', $param3);
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
		elseif ($tipo_reporte === 'tecnicos')
		{
			$this->db->where('a.cliente', $param3);
		}

		$arr_data = $this->db
			->select('a.fecha_contabilizacion as fecha')
			->select('a.documento_material')
			->select('c.empresa')
			->select('a.cliente')
			->select('b.tecnico')
			->select('a.centro')
			->select('a.almacen')
			->select('d.des_almacen')
			->select('a.material')
			->select('a.texto_material')
			->select('a.valor')
			->select('a.lote')
			->select("'ver detalle' as texto_link")
			->select_sum('(-a.cantidad_en_um)', 'cant')
			->select_sum('(-a.importe_ml)', 'monto')
			->group_by('a.fecha_contabilizacion')
			->group_by('a.documento_material')
			->group_by('c.empresa')
			->group_by('a.cliente')
			->group_by('b.tecnico')
			->group_by('a.centro')
			->group_by('a.almacen')
			->group_by('d.des_almacen')
			->group_by('a.material')
			->group_by('a.texto_material')
			->group_by('a.valor')
			->group_by('a.lote')
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->join($this->config->item('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left')
			->order_by('a.documento_material', 'ASC')
			->get()->result_array();

		$arr_campos = array();
		$arr_campos['fecha']              = array('titulo' => 'Fecha', 'tipo' => 'fecha');
		$arr_campos['documento_material'] = array('titulo' => 'Documento SAP', 'tipo' => 'texto');
		$arr_campos['empresa']            = array('titulo' => 'Empresa', 'tipo' => 'texto');
		$arr_campos['cliente']            = array('titulo' => 'Cod Tecnico', 'tipo' => 'texto');
		$arr_campos['tecnico']            = array('titulo' => 'Nombre Tecnico', 'tipo' => 'texto');
		$arr_campos['centro']             = array('titulo' => 'Centro', 'tipo' => 'texto');
		$arr_campos['almacen']            = array('titulo' => 'Almac&eacute;n', 'tipo' => 'texto');
		$arr_campos['des_almacen']        = array('titulo' => 'Desc Almac&eacute;n', 'tipo' => 'texto');
		$arr_campos['material']           = array('titulo' => 'Material', 'tipo' => 'texto');
		$arr_campos['texto_material']     = array('titulo' => 'Desc Material', 'tipo' => 'texto');
		$arr_campos['lote']               = array('titulo' => 'Lote', 'tipo' => 'texto');
		$arr_campos['valor']              = array('titulo' => 'Valor', 'tipo' => 'texto');
		$arr_campos['cant']               = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
		$arr_campos['monto']              = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
		$arr_campos['texto_link']         = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/detalle_asignacion', 'href_registros' => array('fecha','documento_material'));
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
			return array();
		}

		return $this->db
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
			->select('a.lote')
			->select('a.valor')
			->select('a.umb')
			->select('(a.cantidad_en_um) as cant', FALSE)
			->select('(a.importe_ml) as monto', FALSE)
			->select('a.usuario')
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			//->order_by($orden_campo, $orden_tipo)
			->where('fecha_contabilizacion', $fecha)
			->where('documento_material', $peticion)
			->where_in('codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('centro', $this->centros_consumo)
			->order_by('material, cantidad_en_um')
			->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo con dias del mes
	 *
	 * @param  string $anomes Mes y año a consultar (formato YYYYMM)
	 * @return array          Arreglo con dias del mes (llaves en formato DD)
	 */
	private function _get_arr_dias($anomes = NULL)
	{
		$mes = (int) substr($anomes, 4, 2);
		$ano = (int) substr($anomes, 0, 4);

		$arr_dias = array();

		for($i = 1; $i <= days_in_month($mes, $ano); $i++)
		{
			$indice_dia = (strlen($i) === 1) ? '0'.$i : ''.$i;
			$arr_dias[$indice_dia] = NULL;
		}

		return $arr_dias;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve la fecha más un mes
	 *
	 * @param  string $anomes Mes y año a consultar (formato YYYYMM)
	 * @return string         Fecha más un mes (formato YYYYMMDD)
	 */
	private function _get_fecha_hasta($anomes = NULL)
	{
		$mes = (int) substr($anomes, 4, 2);
		$ano = (int) substr($anomes, 0, 4);

		return (string) (($mes === 12) ? ($ano+1)*10000+(1)*100+1 : $ano*10000+($mes+1)*100+1);

	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve matriz de consumos de técnicos por dia
	 *
	 * @param  string $empresa        Empresa a consultar
	 * @param  string $anomes         Mes a consultar, formato YYYYMM
	 * @param  string $filtro_trx     Filtro para revisar un codigo movimiento
	 * @param  string $dato_desplegar Tipo de dato a desplegar
	 * @return array                  Arreglo con los datos del reporte
	 */
	public function control_tecnicos($empresa = NULL, $anomes = NULL, $filtro_trx = NULL, $dato_desplegar = 'peticiones')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$arr_dias = $this->_get_arr_dias($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		if ($dato_desplegar === 'unidades')
		{
			$this->db->select_sum('cant', 'dato');
		}
		elseif ($dato_desplegar === 'monto')
		{
			$this->db->select_sum('monto', 'dato');
		}
		else
		{
			$this->db->select_sum('1', 'dato');
		}

		$datos = $this->db
			->select('a.fecha')
			->select('a.tecnico')
			->group_by('a.fecha')
			->group_by('a.tecnico')
			->where('a.fecha>=', $fecha_desde)
			->where('a.fecha<', $fecha_hasta)
			->where('b.id_empresa', $empresa)
			// ->where_in('codigo_movimiento', $this->movimientos_consumo)
			// ->where_in('centro', $this->centros_consumo)
			->from($this->config->item('bd_peticiones_sap').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.tecnico = b.id_tecnico', 'left', FALSE)
			->get()->result_array();

		if ($filtro_trx AND $filtro_trx !== '000')
		{
			$query = $this->db
				->select('a.fecha_contabilizacion as fecha')
				->select('a.cliente as tecnico')
				->select('a.referencia')
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('a.fecha_contabilizacion')
				->group_by('a.cliente')
				->group_by('a.referencia')
				->where('a.fecha_contabilizacion>=', $fecha_desde)
				->where('a.fecha_contabilizacion<', $fecha_hasta)
				->where('b.id_empresa', $empresa)
				->where('codigo_movimiento', $filtro_trx)
				->where_in('centro', $this->centros_consumo)
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->get_compiled_select();

			$arr_dato_desplegar = array(
				'peticiones' => 'count(q1.referencia)',
				'unidades'   => 'sum(q1.cant)',
				'monto'      => 'sum(q1.monto)'
			);

			$query = 'select q1.fecha, q1.tecnico, '.$arr_dato_desplegar[$dato_desplegar].' as dato from ('.$query.') q1 group by q1.fecha, q1.tecnico order by q1.tecnico, q1.fecha';

			$datos = $this->db->query($query)->result_array();
		}



		$tecnicos = new Tecnico_toa();
		$arr_tecnicos = $tecnicos->find('all', array('conditions' => array('id_empresa' => $empresa)));

		$matriz = array();
		foreach ($arr_tecnicos as $tecnico)
		{
			$matriz[$tecnico->id_tecnico] = array(
				'nombre'       => $tecnico->tecnico,
				'rut'          => $tecnico->rut,
				'ciudad'       => (string) $tecnico->get_relation_object('id_ciudad'),
				'orden_ciudad' => (int) $tecnico->get_relation_object('id_ciudad')->orden,
				'actuaciones'  => $arr_dias
			);
		}
		uasort($matriz, array($this, '_sort_matriz_control_tecnicos'));

		$arr_tecnicos_con_datos = array();
		foreach ($datos as $registro)
		{
			$matriz[$registro['tecnico']]['actuaciones'][substr(fmt_fecha($registro['fecha']), 8, 2)] = $registro['dato'];

			if ( ! in_array($registro['tecnico'], $arr_tecnicos_con_datos))
			{
				array_push($arr_tecnicos_con_datos, $registro['tecnico']);
			}
		}

		foreach ($matriz as $tecnico => $datos_tecnico)
		{
			if ( ! in_array($tecnico, $arr_tecnicos_con_datos))
			{
				unset($matriz[$tecnico]);
			}
		}

		return $matriz;
	}


	// --------------------------------------------------------------------

	/**
	 * Criterio para ordenar matriz de técnicos por ciudad
	 *
	 * @param  array $a Primer elemento a comparar
	 * @param  array $b Segundo elemento a comparar
	 * @return int      Comparación
	 */
	private function _sort_matriz_control_tecnicos($a, $b)
	{
		return $a['orden_ciudad'] > $b['orden_ciudad'];
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

		$arr_dias = $this->_get_arr_dias($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		if ($filtro_trx AND $filtro_trx !== '000')
		{
			$this->db->where('codigo_movimiento', $filtro_trx);
		}

		$this->db
			->select('a.fecha_contabilizacion as fecha')
			->select('a.cliente')
			->group_by('a.fecha_contabilizacion')
			->group_by('a.cliente')
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<', $fecha_hasta)
			->where('b.id_empresa', $empresa)
			->where_in('codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('centro', $this->centros_consumo)
			->where('almacen', NULL)
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE);

		if ($dato_desplegar === 'unidades')
		{
			$this->db->select_sum('a.cantidad_en_um', 'dato');
		}
		elseif ($dato_desplegar === 'monto')
		{
			$this->db->select_sum('a.importe_ml', 'dato');
		}
		else
		{
			$this->db->select('count(*) as dato', FALSE);
		}

		$datos = $this->db->get()->result_array();

		$tecnicos = new Tecnico_toa();
		$arr_tecnicos = $tecnicos->find('all', array('conditions' => array('id_empresa' => $empresa)));

		$matriz = array();
		foreach ($arr_tecnicos as $tecnico)
		{
			$matriz[$tecnico->id_tecnico] = array(
				'nombre'       => $tecnico->tecnico,
				'rut'          => $tecnico->rut,
				'ciudad'       => (string) $tecnico->get_relation_object('id_ciudad'),
				'orden_ciudad' => (int) $tecnico->get_relation_object('id_ciudad')->orden,
				'actuaciones'  => $arr_dias
			);
		}
		uasort($matriz, array($this, '_sort_matriz_control_tecnicos'));

		$arr_tecnicos_con_datos = array();
		foreach ($datos as $registro)
		{
			$matriz[$registro['cliente']]['actuaciones'][substr(fmt_fecha($registro['fecha']), 8, 2)] = $registro['dato'];

			if ( ! in_array($registro['cliente'], $arr_tecnicos_con_datos))
			{
				array_push($arr_tecnicos_con_datos, $registro['cliente']);
			}
		}

		foreach ($matriz as $tecnico => $datos_tecnico)
		{
			if ( ! in_array($tecnico, $arr_tecnicos_con_datos))
			{
				unset($matriz[$tecnico]);
			}
		}

		return $matriz;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve matriz de materiales consumidos por dia
	 *
	 * @param  string $empresa        Empresa a consultar
	 * @param  string $anomes         Mes a consultar, formato YYYYMM
	 * @param  string $filtro_trx     Filtro para revisar un codigo movimiento
	 * @param  string $dato_desplegar Tipo de dato a desplegar
	 * @return array                  Arreglo con los datos del reporte
	 */
	public function materiales_consumidos($empresa = NULL, $anomes = NULL, $filtro_trx = NULL, $dato_desplegar = 'unidades')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$arr_dias = get_arr_dias($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		if ($filtro_trx AND $filtro_trx !== '000')
		{
			$this->db->where('codigo_movimiento', $filtro_trx);
		}

		$datos = $this->db
			->select('a.fecha_contabilizacion as fecha')
			->select('a.material')
			->select('c.descripcion')
			->select('a.ume')
			->select('e.desc_tip_material')
			->select('e.color')
			->select_sum(($dato_desplegar === 'monto') ? '(-a.importe_ml)' : '(-a.cantidad_en_um)', 'dato')
			->group_by('a.fecha_contabilizacion')
			->group_by('a.material')
			->group_by('c.descripcion')
			->group_by('a.ume')
			->group_by('e.desc_tip_material')
			->group_by('e.color')
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<', $fecha_hasta)
			->where('b.id_empresa', $empresa)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			// ->where('almacen', NULL)
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_catalogos').' c', 'a.material=c.catalogo', 'left', FALSE)
			->join($this->config->item('bd_catalogo_tip_material_toa').' d', 'a.material=d.id_catalogo', 'left', FALSE)
			->join($this->config->item('bd_tip_material_trabajo_toa').' e', 'd.id_tip_material_trabajo = e.id', 'left')
			->get()->result_array();

		$matriz = collect($datos)
			->map_with_keys(function ($material) use ($arr_dias) {
				return array(
					$material['desc_tip_material'].$material['material'] => array(
						'material'     => $material['material'],
						'descripcion'  => $material['descripcion'],
						'unidad'       => $material['ume'],
						'tip_material' => $material['desc_tip_material'],
						'color'        => $material['color'],
						'actuaciones'  => $arr_dias,
					)
				);
			})->all();

		ksort($matriz);

		return collect($matriz)->map(function($material) use ($datos) {
			$consumo_material = collect($datos)
				->filter(function($dato) use ($material) {
					return $dato['material'] === $material['material'];
				})->map_with_keys(function($dato) {
					return array(
						substr(fmt_fecha($dato['fecha']), 8, 2) => $dato['dato']
					);
				});

			$material['actuaciones'] = collect($material['actuaciones'])
				->map(function($valor, $indice) use ($consumo_material) {
					return $valor + $consumo_material->get($indice, 0);
				})->all();

			return $material;
		});
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve la clase pintar el cumplimiento diario
	 *
	 * @param  integer $porcentaje_cumplimiento % de cumplimiento
	 * @return string                           Clase
	 */
	public function clase_cumplimiento_consumos($porcentaje_cumplimiento = 0)
	{
		if ($porcentaje_cumplimiento >= 0.9)
		{
			return 'success';
		}
		elseif ($porcentaje_cumplimiento >= 0.6)
		{
			return 'warning';
		}

		return 'danger';
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve matriz de materiales por tipos de trabajo
	 *
	 * @param  string $empresa        Empresa a consultar
	 * @param  string $anomes         Mes a consultar, formato YYYYMM
	 * @param  string $tipo_trabajo   Tipo de trabajo a consultar
	 * @param  string $dato_desplegar Tipo de dato a desplegar
	 * @return array                  Arreglo con los datos del reporte
	 */
	public function materiales_tipos_trabajo($empresa = NULL, $anomes = NULL, $tipo_trabajo = NULL, $dato_desplegar = 'unidad')
	{
		if ( ! $empresa OR ! $anomes OR ! $tipo_trabajo)
		{
			return NULL;
		}
		$arr_dias = $this->_get_arr_dias($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		if ($tipo_trabajo !== '000')
		{
			$this->db->where('a.carta_porte', $tipo_trabajo);
		}

		$arr_referencias = $this->db
			->distinct()
			->select('a.referencia')
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<', $fecha_hasta)
			->where('a.vale_acomp', $empresa)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->order_by('referencia')
			->get()->result_array();


		if ($tipo_trabajo !== '000')
		{
			$this->db->where('a.carta_porte', $tipo_trabajo);
		}

		$arr_materiales = $this->db
			->distinct()
			->select('a.material')
			->select('a.texto_material')
			->select('c.desc_tip_material')
			->select('c.color')
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<', $fecha_hasta)
			->where('a.vale_acomp', $empresa)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_catalogo_tip_material_toa').' b', 'a.material=b.id_catalogo', 'left', FALSE)
			->join($this->config->item('bd_tip_material_trabajo_toa').' c', 'b.id_tip_material_trabajo=c.id', 'left')
			->order_by('c.desc_tip_material, a.material')
			->get()->result_array();

		if ($tipo_trabajo !== '000')
		{
			$this->db->where('a.carta_porte', $tipo_trabajo);
		}

		$this->db
			->select('a.referencia')
			->select('a.material')
			->select('a.fecha_contabilizacion as fecha')
			->group_by('a.referencia')
			->group_by('a.material')
			->group_by('a.fecha_contabilizacion')
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<', $fecha_hasta)
			->where('a.vale_acomp', $empresa)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->order_by('referencia, material');

			if ($dato_desplegar === 'monto')
			{
				$this->db->select_sum('(-a.importe_ml)', 'dato');
			}
			else
			{
				$this->db->select_sum('(-a.cantidad_en_um)', 'dato');
			}

			$arr_data = $this->db->get()->result_array();
			$matriz = array();

			foreach($arr_referencias as $referencia)
			{
				$matriz[$referencia['referencia']] = array();

				foreach($arr_materiales as $material)
				{
					$matriz[$referencia['referencia']][$material['material']] = array(
						'texto_material'    => $material['texto_material'],
						'desc_tip_material' => $material['desc_tip_material'],
						'color'             => $material['color'],
						'fecha'             => NULL,
						'dato'              => NULL,
					);
				}
			}

			foreach($arr_data as $registro)
			{
				$matriz[$registro['referencia']][$registro['material']]['dato'] = $registro['dato'];
				$matriz[$registro['referencia']][$registro['material']]['fecha'] = fmt_fecha($registro['fecha']);
			}
		return $matriz;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve los datos del resumen panel
	 *
	 * @param  string $indicador Indicador a devolver
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes de datos a devolver (formato YYYYMM)
	 *
	 * @return array            Arreglo con los datos
	 */
	public function get_resumen_panel($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		if ( ! $indicador OR ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		$this->db
			->select('tipo')
			->select('fecha')
			->select('substring(convert(varchar(20), fecha, 103),1,2) as dia', FALSE)
			->from($this->config->item('bd_resumen_panel_toa'))
			->where('tipo', $indicador)
			->where('fecha>=', $fecha_desde)
			->where('fecha<', $fecha_hasta);

		if ($empresa === '***')
		{
			$this->db
				->select_sum('valor', 'valor')
				->group_by('tipo')
				->group_by('fecha')
				->group_by('substring(convert(varchar(20), fecha, 103),1,2)', FALSE)
				->order_by('tipo, fecha');
		}
		else
		{
			$this->db
				->select('empresa')
				->select('valor')
				->where('empresa', $empresa)
				->order_by('tipo, empresa, fecha');
		}

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos para poblar panel
	 *
	 * @param  mixed  $indicador Nombre del indicador o arreglo con nombre de indicadores
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes a recuperar (formato YYYYMM)
	 * @return string            Arreglo en formato javascript
	 */
	public function get_resumen_panel_gchart($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		$arr_indicador = ( ! is_array($indicador)) ? array('Data' => $indicador) : $indicador;

		$arr_datos = array();
		foreach ($arr_indicador as $llave_indicador => $valor_indicador)
		{
			$arr_datos[$llave_indicador] = $this->get_resumen_panel($valor_indicador, $empresa, $anomes);
		}

		$num_mes = (int) substr($anomes, 4, 2);
		$num_ano = (int) substr($anomes, 0, 4);
		$arr_indicador_vacio = array_fill_keys(array_keys($arr_indicador), 0);
		$arr_peticiones = array_fill(1, days_in_month($num_mes, $num_ano), $arr_indicador_vacio);

		foreach ($arr_indicador as $llave_indicador => $valor_indicador)
		{
			foreach($arr_datos[$llave_indicador] as $registro)
			{
				$arr_peticiones[(int) $registro['dia']][$llave_indicador] = $registro['valor'];
			}
		}

		return $this->gchart_data($arr_peticiones);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera la suma mensual de un indicador
	 *
	 * @param  string $indicador Indicador a recuperar
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes a recuperar (formato YYYYMM)
	 * @return array            Arreglo con los datos de la suma del indicador
	 */
	public function get_resumen_usage($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		if ( ! $indicador OR ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		if ($empresa !== '***')
		{
			$this->db->where('empresa', $empresa);
		}

		$result = $this->db
			->select_sum('valor')
			->from($this->config->item('bd_resumen_panel_toa'))
			->where('tipo', $indicador)
			->where('fecha>=', $fecha_desde)
			->where('fecha<', $fecha_hasta)
			->get()->row();

		return $result->valor;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve datos de uso para poblar el panel
	 *
	 * @param  string $indicador1 Indicador a recuperar (numerador)
	 * @param  string $indicador2 Indicador a recuperar (denominador)
	 * @param  string $empresa    ID de la empresa
	 * @param  string $anomes     Mes a recuperar (formato YYYYMM)
	 * @return string             Arreglo en formato javascript
	 */
	public function get_resumen_panel_usage($indicador1 = NULL, $indicador2 = NULL, $empresa = NULL, $anomes = NULL)
	{
		$sum_indicador1 = $this->get_resumen_usage($indicador1, $empresa, $anomes);
		$sum_indicador2 = $this->get_resumen_usage($indicador2, $empresa, $anomes);
		$usage = ($sum_indicador2 === 0) ? 0 : 100*($sum_indicador1/$sum_indicador2);
		$usage = $usage > 100 ? 100 : (int) $usage;

		return ($this->gchart_data(array('usa' => $usage, 'no usa' => 100 - $usage)));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos de uso para poblar el panel
	 *
	 * @param  string $indicador Indicador a recuperar (numerador)
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes a recuperar (formato YYYYMM)
	 * @return string            Arreglo en formato javascript
	 */
	public function get_resumen_panel_porcentaje_mes($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		if ( ! $indicador OR ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$dias_mes = days_in_month((int) substr($anomes, 4, 2), (int) substr($anomes, 0, 4));

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		if ($empresa !== '***')
		{
			$this->db->where('empresa', $empresa);
		}

		$result = $this->db
			->select_max('fecha')
			->from($this->config->item('bd_resumen_panel_toa'))
			->where('tipo', $indicador)
			->where('fecha>=', $fecha_desde)
			->where('fecha<', $fecha_hasta)
			->get()->row();

		$dia_actual = (int) fmt_fecha($result->fecha, 'd');

		return $dia_actual/$dias_mes;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos de uso para poblar el panel
	 *
	 * @param  string $indicador Indicador a recuperar (numerador)
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes a recuperar (formato YYYYMM)
	 * @return string            Arreglo en formato javascript
	 */
	public function get_resumen_panel_proyeccion($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		if ( ! $indicador OR ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$valor_indicador = $this->get_resumen_usage($indicador, $empresa, $anomes);
		$porc_mes = $this->get_resumen_panel_porcentaje_mes($indicador, $empresa, $anomes);

		return ($this->gchart_data(
			array(
				'actual' => $valor_indicador,
				'proy'   => (int) ($valor_indicador/$porc_mes) - $valor_indicador
			)
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Formatea un arreglo para ser usado por Google Charts
	 *
	 * @param  array $arr_orig Arreglo a formatear
	 * @return string
	 */
	public function gchart_data($arr_orig = array())
	{

		if ( ! $arr_orig)
		{
			return NULL;
		}

		$func_agrega_comillas = function($elem) {return "'".$elem."'";};
		$func_nulos_a_cero    = function($elem) {return is_null($elem) ? 0 : $elem;};
		$num_registro = 0;

		foreach ($arr_orig as $num_dia => $valor)
		{
			if ( ! is_array($valor))
			{
				$valor = array('Data' => $valor);
			}

			// titulo
			if ($num_registro === 0)
			{
				$arrjs = "[['Dia', ".collect(array_keys($valor))->map($func_agrega_comillas)->implode(',').']';
			}

			// agregamos los datos del día
			$arrjs .= ", ['{$num_dia}',".collect($valor)->map($func_nulos_a_cero)->implode(',').']';
			$num_registro += 1;
		}

		$arrjs .= ']';

		return $arrjs;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera nuevos técnicos a partir de los cierres
	 *
	 * @return array Técnicos nuevos
	 */
	public function nuevos_tecnicos()
	{
		return $this->db
			->distinct()
			->select('a.cliente as id_tecnico')
			->select('d.resource_name as tecnico')
			->select('a.vale_acomp as id_empresa')
			->select('d.resource_external_id as rut')
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join($this->config->item('bd_peticiones_toa').' d', 'a.referencia=d.appt_number and d.astatus=\'complete\'', 'left', FALSE)
			->where('a.fecha_contabilizacion >= dateadd(day, -20, convert(date, getdate()))')
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			->where('b.id_tecnico is NULL')
			->where('d.resource_name is not NULL')
			->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Agrega nuevos técnicos a partir de un arreglo
	 *
	 * @param  array  $arr_nuevos_tecnicos Arreglo a de tecnicos a agregar
	 * @return void
	 */
	public function agrega_nuevos_tecnicos($arr_nuevos_tecnicos)
	{
		foreach($arr_nuevos_tecnicos as $nuevo_tecnico)
		{
			$this->db->insert($this->config->item('bd_tecnicos_toa'), $nuevo_tecnico);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Control de clientes
	 *
	 * @param  string $cliente     Cliente a buscar
	 * @param  string $fecha_desde Fecha incial para buscar
	 * @param  string $fecha_hasta Fecha final para buscar
	 * @return void
	 */
	public function clientes($cliente = NULL, $fecha_desde = NULL, $fecha_hasta = NULL)
	{
		if (! $fecha_desde OR ! $fecha_hasta)
		{
			return NULL;
		}

		$this->db
			->limit(100)
			->select('customer_number')
			->select_max('cname')
			->select('count(*) as cantidad', FALSE)
			->where('date>=', $fecha_desde)
			->where('date<=', $fecha_hasta)
			->where('astatus', 'complete')
			->where('customer_number IS NOT NULL')
			->group_by('customer_number')
			->order_by('cantidad', 'desc');

		if ($cliente)
		{
			$this->db->like('cname', strtoupper($cliente));
		}

		return $this->db
			->get($this->config->item('bd_peticiones_toa'))
			->result_array();
	}


}

/* End of file Toa_model.php */
/* Location: ./application/models/Toa_model.php */
