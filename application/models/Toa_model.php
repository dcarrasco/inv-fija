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
	public $movimientos_consumo = array('Z35', 'Z45', 'Z39', 'Z41');

	/**
	 * Combo movimientos validos de consumo TOA
	 *
	 * @var array
	 */
	public $combo_movimientos_consumo = array(
		''    => 'Todos los movimientos',
		'Z35' => 'Z35 Consumo CAPEX TOA',
		'Z45' => 'Z45 Consumo OPEX TOA',
		'Z39' => 'Z39 Consumo Promedio CAPEX TOA',
		'Z41' => 'Z45 Consumo Promedio OPEX TOA'
	);

	/**
	 * Movimientos validos de asignaciones TOA
	 *
	 * @var array
	 */
	public $movimientos_asignaciones = array('Z31', 'Z32');

	/**
	 * Combo movimientos validos de asignacion TOA
	 *
	 * @var array
	 */
	public $combo_movimientos_asignacion = array(
		''    => 'Todos los movimientos',
		'Z31' => 'Z31 Asig stock tecnico TOA',
		'Z32' => 'Z32 Anula asig stock tecnico TOA',
	);

	/**
	 * Centros validos de consumos TOA
	 *
	 * @var array
	 */
	public $centros_consumo = array('CH32', 'CH33');

	/**
	 * Tipos de reporte consumo
	 *
	 * @var array
	 */
	public $tipos_reporte_consumo = array(
		'peticion'      => 'N&uacute;mero petici&oacute;n',
		'material'      => 'Materiales',
		'lote'          => 'Lotes',
		'lote-material' => 'Lotes y materiales',
		'pep'           => 'PEP',
		'tecnicos'      => 'T&eacute;cnicos',
		'tipo-trabajo'  => 'Tipo de trabajo',
		'detalle'       => 'Detalle todos los registros',
	);

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
	 * Combo de unidades reporte control stock
	 *
	 * @var array
	 */
	public $combo_unidades_stock = array(
		'monto'        => 'Suma de montos',
		'unidades'     => 'Suma de unidades',
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
	 * Devuelve los consumos realizados en TOA
	 *
	 * @param  string $tipo_reporte Tipo de reporte a desplegar
	 * @param  string $fecha        Fecha de los datos del reporte
	 * @param  string $orden_campo  Campo para ordenar el resultado
	 * @param  string $orden_tipo   Orden del resultado (ascendente o descendente)
	 * @return string               Reporte
	 */
	public function consumos_toa($tipo_reporte = 'detalle', $fecha = NULL, $orden_campo = '', $orden_tipo = 'ASC')
	{
		if ( ! $tipo_reporte OR ! $fecha)
		{
			return '';
		}

		$arr_data = array();

		$this->db
			->where('fecha_contabilizacion', $fecha)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo);

		$orden_tipo = ($orden_tipo === '') ? 'ASC' : $orden_tipo;

		if ($tipo_reporte === 'detalle')
		{
			$orden_campo = ($orden_campo === '') ? 'referencia' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
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
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']              = array('titulo' => 'Fecha', 'tipo' => 'texto');
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
		else if ($tipo_reporte === 'peticion')
		{
			$orden_campo = ($orden_campo === '') ? 'referencia' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
				->select('a.referencia')
				->select('a.carta_porte')
				->select('c.empresa')
				->select('a.cliente')
				->select("'ver detalle' as texto_link")
				->select('b.tecnico')
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
				->group_by('a.referencia')
				->group_by('a.vale_acomp')
				->group_by('a.carta_porte')
				->group_by('c.empresa')
				->group_by('a.cliente')
				->group_by('b.tecnico')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'a.vale_acomp collate Latin1_General_CI_AS = c.id_empresa collate Latin1_General_CI_AS', 'left', FALSE)
				->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']      = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['referencia'] = array('titulo' => 'Numero peticion', 'tipo' => 'texto');
			$arr_campos['carta_porte'] = array('titulo' => 'Tipo trabajo', 'tipo' => 'texto');
			$arr_campos['empresa']    = array('titulo' => 'Empresa', 'tipo' => 'texto');
			$arr_campos['cliente']    = array('titulo' => 'Cod Tecnico', 'tipo' => 'texto');
			$arr_campos['tecnico']    = array('titulo' => 'Nombre Tecnico', 'tipo' => 'texto');
			$arr_campos['cant']       = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']       = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/detalle_peticion', 'href_registros' => array('fecha','referencia'));
			$this->reporte->set_order_campos($arr_campos, 'referencia');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		else if ($tipo_reporte === 'material')
		{
			$orden_campo = ($orden_campo === '') ? 'material' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), fecha_contabilizacion, 102) as fecha')
				->select('material')
				->select('texto_material')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('convert(varchar(20), fecha_contabilizacion, 102)')
				->group_by('material')
				->group_by('texto_material')
				->order_by($orden_campo, $orden_tipo)
				->get($this->config->item('bd_movimientos_sap_fija'))
				->result_array();

			$arr_campos = array();
			$arr_campos['fecha']          = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['material']       = array('titulo' => 'Cod material', 'tipo' => 'texto');
			$arr_campos['texto_material'] = array('titulo' => 'Desc material', 'tipo' => 'texto');
			$arr_campos['cant']           = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']          = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/material', 'href_registros' => array('fecha','material'));
			$this->reporte->set_order_campos($arr_campos, 'material');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		else if ($tipo_reporte === 'lote')
		{
			$orden_campo = ($orden_campo === '') ? 'valor' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), fecha_contabilizacion, 102) as fecha')
				->select('valor')
				->select('lote')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('convert(varchar(20), fecha_contabilizacion, 102)')
				->group_by('valor')
				->group_by('lote')
				->order_by($orden_campo, $orden_tipo)
				->get($this->config->item('bd_movimientos_sap_fija'))
				->result_array();

			$arr_campos = array();
			$arr_campos['fecha'] = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['valor'] = array('titulo' => 'Valor', 'tipo' => 'texto');
			$arr_campos['lote']  = array('titulo' => 'Lote', 'tipo' => 'texto');
			$arr_campos['cant']  = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto'] = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/lote', 'href_registros' => array('fecha','lote'));
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		else if ($tipo_reporte === 'lote-material')
		{
			$orden_campo = ($orden_campo === '') ? 'valor' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), fecha_contabilizacion, 102) as fecha')
				->select('valor')
				->select('lote')
				->select('material')
				->select('texto_material')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('convert(varchar(20), fecha_contabilizacion, 102)')
				->group_by('valor')
				->group_by('lote')
				->group_by('material')
				->group_by('texto_material')
				->order_by($orden_campo, $orden_tipo)
				->get($this->config->item('bd_movimientos_sap_fija'))
				->result_array();

			$arr_campos = array();
			$arr_campos['fecha']          = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['valor']          = array('titulo' => 'Valor', 'tipo' => 'texto');
			$arr_campos['lote']           = array('titulo' => 'Lote', 'tipo' => 'texto');
			$arr_campos['material']       = array('titulo' => 'Cod material', 'tipo' => 'texto');
			$arr_campos['texto_material'] = array('titulo' => 'Desc material', 'tipo' => 'texto');
			$arr_campos['cant']           = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']          = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/lote-material', 'href_registros' => array('fecha','lote','material'));
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		else if ($tipo_reporte === 'pep')
		{
			$orden_campo = ($orden_campo === '') ? 'codigo_movimiento' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), fecha_contabilizacion, 102) as fecha')
				->select('codigo_movimiento')
				->select('texto_movimiento')
				->select('elemento_pep')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('convert(varchar(20), fecha_contabilizacion, 102)')
				->group_by('codigo_movimiento')
				->group_by('texto_movimiento')
				->group_by('elemento_pep')
				->order_by($orden_campo, $orden_tipo)
				->get($this->config->item('bd_movimientos_sap_fija'))
				->result_array();

			$arr_campos = array();
			$arr_campos['fecha']             = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['codigo_movimiento'] = array('titulo' => 'CodMov', 'tipo' => 'texto');
			$arr_campos['texto_movimiento']  = array('titulo' => 'Desc Movimiento', 'tipo' => 'texto');
			$arr_campos['elemento_pep']      = array('titulo' => 'PEP', 'tipo' => 'texto');
			$arr_campos['cant']              = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']             = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/pep', 'href_registros' => array('fecha','codigo_movimiento', 'elemento_pep'));
			$this->reporte->set_order_campos($arr_campos, 'codigo_movimiento');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		else if ($tipo_reporte === 'tecnicos')
		{
			$orden_campo = ($orden_campo === '') ? 'empresa' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
				->select('c.empresa')
				->select('a.cliente')
				->select('b.tecnico')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
				->group_by('c.empresa')
				->group_by('a.cliente')
				->group_by('b.tecnico')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']   = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['empresa'] = array('titulo' => 'Empresa', 'tipo' => 'texto');
			$arr_campos['cliente'] = array('titulo' => 'Cod Tecnico', 'tipo' => 'texto');
			$arr_campos['tecnico'] = array('titulo' => 'Nombre Tecnico', 'tipo' => 'texto');
			$arr_campos['cant']    = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']   = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/tecnicos', 'href_registros' => array('fecha','cliente'));
			$this->reporte->set_order_campos($arr_campos, 'empresa');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		else if ($tipo_reporte === 'tipo-trabajo')
		{
			$orden_campo = ($orden_campo === '') ? 'carta_porte' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
				->select('a.carta_porte')
				->select("'ver peticiones' as texto_link")
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
				->group_by('a.carta_porte')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']       = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['carta_porte'] = array('titulo' => 'Tipo de trabajo', 'tipo' => 'texto');
			$arr_campos['cant']        = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']       = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']  = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/ver_peticiones/tipo_trabajo', 'href_registros' => array('fecha','carta_porte'));
			$this->reporte->set_order_campos($arr_campos, 'carta_porte');

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
	 * @return string               Reporte con las peticiones encontradas
	 */
	public function peticiones_toa($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL)
	{
		if ( ! $tipo_reporte)
		{
			return '';
		}

		$arr_data = array();

		$this->db
			->where('fecha_contabilizacion', $param1)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo);

		if ($tipo_reporte === 'material')
		{
			$this->db->where('material', $param2);
		}
		elseif ($tipo_reporte === 'lote')
		{
			$this->db->where('lote', $param2);
		}
		elseif ($tipo_reporte === 'lote-material')
		{
			$this->db->where('lote', $param2);
			$this->db->where('material', $param3);
		}
		elseif ($tipo_reporte === 'pep')
		{
			$this->db->where('codigo_movimiento', $param2);
			$this->db->where('elemento_pep', $param3);
		}
		elseif ($tipo_reporte === 'tecnicos')
		{
			$this->db->where('cliente', $param2);
		}

		$arr_data = $this->db
			->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
			->select('a.referencia')
			->select('a.carta_porte')
			->select('c.empresa')
			->select('a.cliente')
			->select('b.tecnico')
			->select("'ver detalle' as texto_link")
			->select_sum('(-a.cantidad_en_um)', 'cant')
			->select_sum('(-a.importe_ml)', 'monto')
			->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
			->group_by('a.referencia')
			->group_by('a.carta_porte')
			->group_by('c.empresa')
			->group_by('a.cliente')
			->group_by('b.tecnico')
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'a.vale_acomp collate Latin1_General_CI_AS = c.id_empresa collate Latin1_General_CI_AS', 'left', FALSE)
			->order_by('a.referencia', 'ASC')
			->get()->result_array();

		$arr_campos = array();
		$arr_campos['fecha']      = array('titulo' => 'Fecha', 'tipo' => 'texto');
		$arr_campos['referencia'] = array('titulo' => 'Numero peticion', 'tipo' => 'texto');
		$arr_campos['carta_porte'] = array('titulo' => 'Tipo trabajo', 'tipo' => 'texto');
		$arr_campos['empresa']    = array('titulo' => 'Empresa', 'tipo' => 'texto');
		$arr_campos['cliente']    = array('titulo' => 'Cod Tecnico', 'tipo' => 'texto');
		$arr_campos['tecnico']    = array('titulo' => 'Nombre Tecnico', 'tipo' => 'texto');
		$arr_campos['cant']       = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
		$arr_campos['monto']       = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
		$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/detalle_peticion', 'href_registros' => array('fecha','referencia'));
		$this->reporte->set_order_campos($arr_campos, 'referencia');

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
	public function detalle_peticion_toa($fecha = NULL, $peticion = NULL)
	{
		if ( ! $fecha OR ! $peticion)
		{
			return array();
		}

		return $this->db
			->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
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
			->select('(-a.cantidad_en_um) as cant', FALSE)
			->select('(-a.importe_ml) as monto', FALSE)
			->select('a.usuario')
			->select('a.vale_acomp')
			->select('a.carta_porte')
			->select('a.usuario')
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'a.vale_acomp collate Latin1_General_CI_AS = c.id_empresa collate Latin1_General_CI_AS', 'left', FALSE)
			//->order_by($orden_campo, $orden_tipo)
			//->where('fecha_contabilizacion', $fecha)
			->where('referencia', $peticion)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			->order_by('a.codigo_movimiento, a.material')
			->get()->result_array();

	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve las asignaciones realizados en TOA
	 *
	 * @param  string $tipo_reporte Tipo de reporte a desplegar
	 * @param  string $fecha        Fecha de los datos del reporte
	 * @param  string $orden_campo  Campo para ordenar el resultado
	 * @param  string $orden_tipo   Orden del resultado (ascendente o descendente)
	 * @return string               Reporte
	 */
	public function asignaciones_toa($tipo_reporte = 'tecnicos', $fecha = NULL, $orden_campo = '', $orden_tipo = 'ASC')
	{
		if ( ! $tipo_reporte OR ! $fecha)
		{
			return '';
		}

		$arr_data = array();

		$this->db
			->where('a.fecha_contabilizacion', $fecha)
			->where_in('a.codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('a.centro', $this->centros_consumo)
			->where('a.signo','NEG');

		$orden_tipo = ($orden_tipo === '') ? 'ASC' : $orden_tipo;

		if ($tipo_reporte === 'detalle')
		{
			$orden_campo = ($orden_campo === '') ? 'referencia' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
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
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				//->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']              = array('titulo' => 'Fecha', 'tipo' => 'texto');
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
		else if ($tipo_reporte === 'material')
		{
			$orden_campo = ($orden_campo === '') ? 'material' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), fecha_contabilizacion, 102) as fecha')
				->select('material')
				->select('texto_material')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('convert(varchar(20), fecha_contabilizacion, 102)')
				->group_by('material')
				->group_by('texto_material')
				//->order_by($orden_campo, $orden_tipo)
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']          = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['material']       = array('titulo' => 'Cod material', 'tipo' => 'texto');
			$arr_campos['texto_material'] = array('titulo' => 'Desc material', 'tipo' => 'texto');
			$arr_campos['cant']           = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']          = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/material', 'href_registros' => array('fecha','material'));
			$this->reporte->set_order_campos($arr_campos, 'material');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		else if ($tipo_reporte === 'lote')
		{
			$orden_campo = ($orden_campo === '') ? 'valor' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), fecha_contabilizacion, 102) as fecha')
				->select('valor')
				->select('lote')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('convert(varchar(20), fecha_contabilizacion, 102)')
				->group_by('valor')
				->group_by('lote')
				//->order_by($orden_campo, $orden_tipo)
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha'] = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['valor'] = array('titulo' => 'Valor', 'tipo' => 'texto');
			$arr_campos['lote']  = array('titulo' => 'Lote', 'tipo' => 'texto');
			$arr_campos['cant']  = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto'] = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/lote', 'href_registros' => array('fecha','lote'));
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		else if ($tipo_reporte === 'lote-material')
		{
			$orden_campo = ($orden_campo === '') ? 'valor' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), fecha_contabilizacion, 102) as fecha')
				->select('valor')
				->select('lote')
				->select('material')
				->select('texto_material')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('convert(varchar(20), fecha_contabilizacion, 102)')
				->group_by('valor')
				->group_by('lote')
				->group_by('material')
				->group_by('texto_material')
				//->order_by($orden_campo, $orden_tipo)
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']          = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['valor']          = array('titulo' => 'Valor', 'tipo' => 'texto');
			$arr_campos['lote']           = array('titulo' => 'Lote', 'tipo' => 'texto');
			$arr_campos['material']       = array('titulo' => 'Cod material', 'tipo' => 'texto');
			$arr_campos['texto_material'] = array('titulo' => 'Desc material', 'tipo' => 'texto');
			$arr_campos['cant']           = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']          = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/lote-material', 'href_registros' => array('fecha','lote','material'));
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		else if ($tipo_reporte === 'tecnicos')
		{
			$orden_campo = ($orden_campo === '') ? 'empresa' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
				->select('c.empresa')
				->select('a.cliente')
				->select('b.tecnico')
				->select('a.centro')
				->select('a.almacen')
				->select('d.des_almacen')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
				->group_by('c.empresa')
				->group_by('a.cliente')
				->group_by('b.tecnico')
				->group_by('a.centro')
				->group_by('a.almacen')
				->group_by('d.des_almacen')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->join($this->config->item('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left')
				//->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']   = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['empresa'] = array('titulo' => 'Empresa', 'tipo' => 'texto');
			$arr_campos['cliente'] = array('titulo' => 'Cod Tecnico', 'tipo' => 'texto');
			$arr_campos['tecnico'] = array('titulo' => 'Nombre Tecnico', 'tipo' => 'texto');
			$arr_campos['centro']  = array('titulo' => 'Centro', 'tipo' => 'texto');
			$arr_campos['almacen'] = array('titulo' => 'Almac&eacute;n', 'tipo' => 'texto');
			$arr_campos['des_almacen'] = array('titulo' => 'Desc Almac&eacute;n', 'tipo' => 'texto');
			$arr_campos['cant']    = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
			$arr_campos['monto']   = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
			$arr_campos['texto_link']     = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/tecnicos', 'href_registros' => array('fecha','cliente'));
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
	 * @return string               Reporte con las peticiones encontradas
	 */
	public function documentos_asignaciones_toa($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL)
	{
		if ( ! $tipo_reporte)
		{
			return '';
		}

		$arr_data = array();

		$this->db
			->where('a.fecha_contabilizacion', $param1)
			->where_in('a.codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('a.centro', $this->centros_consumo)
			->where('a.signo', 'NEG');

		if ($tipo_reporte === 'material')
		{
			$this->db->where('a.material', $param2);
		}
		elseif ($tipo_reporte === 'lote')
		{
			$this->db->where('a.lote', $param2);
		}
		elseif ($tipo_reporte === 'lote-material')
		{
			$this->db->where('a.lote', $param2);
			$this->db->where('a.material', $param3);
		}
		elseif ($tipo_reporte === 'pep')
		{
			$this->db->where('a.codigo_movimiento', $param2);
			$this->db->where('a.elemento_pep', $param3);
		}
		elseif ($tipo_reporte === 'tecnicos')
		{
			$this->db->where('a.cliente', $param2);
		}

		$arr_data = $this->db
			->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
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
			->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
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
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->join($this->config->item('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left')
			->order_by('a.documento_material', 'ASC')
			->get()->result_array();

		$arr_campos = array();
		$arr_campos['fecha']              = array('titulo' => 'Fecha', 'tipo' => 'texto');
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
			->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
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
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
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

	private function _get_arr_dias($anomes = NULL)
	{
		$mes = (int) substr($anomes, 4, 2);
		$ano = (int) substr($anomes, 0, 4);

		$arr_dias = array();

		for($i = 1; $i <= days_in_month($mes, $ano); $i++)
		{
			$indice_dia = (strlen($i) === 1) ? '0'.$i : $i;
			$arr_dias[$indice_dia] = NULL;
		}

		return $arr_dias;
	}


	// --------------------------------------------------------------------

	private function _get_fecha_hasta($anomes = NULL)
	{
		$mes = (int) substr($anomes, 4, 2);
		$ano = (int) substr($anomes, 0, 4);

		return (string) (($mes == 12) ? ($ano+1)*10000+(1)*100+1 : $ano*10000+($mes+1)*100+1);

	}


	// --------------------------------------------------------------------

	public function get_combo_meses()
	{
		for ($anno = 2016; $anno <= 2030; $anno++)
		{
			for ($mes = 1; $mes <= 12; $mes++)
			{
				$id_annomes = (string) $anno*100+$mes;
				$annomes    = substr($id_annomes, 0, 4).'-'.substr($id_annomes, 4, 2);
				$combo_meses[$id_annomes] = $annomes;
			}
		}

		return $combo_meses;
	}


	// --------------------------------------------------------------------

	public function control_tecnicos($empresa = NULL, $anomes = NULL, $filtro_trx = NULL, $dato_desplegar = 'peticiones')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$arr_dias = $this->_get_arr_dias($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		if ($filtro_trx OR $filtro_trx !== '')
		{
			$this->db->where('codigo_movimiento', $filtro_trx);
		}

		$query = $this->db
			->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
			->select('a.cliente')
			->select('a.referencia')
			->select_sum('(-a.cantidad_en_um)', 'cant')
			->select_sum('(-a.importe_ml)', 'monto')
			->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
			->group_by('a.cliente')
			->group_by('a.referencia')
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<', $fecha_hasta)
			->where('b.id_empresa', $empresa)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
			->get_compiled_select();

		$arr_dato_desplegar = array(
			'peticiones' => 'count(q1.referencia)',
			'unidades'   => 'sum(q1.cant)',
			'monto'      => 'sum(q1.monto)'
		);

		$query = 'select q1.fecha, q1.cliente, '.$arr_dato_desplegar[$dato_desplegar].' as dato from ('.$query.') q1 group by q1.fecha, q1.cliente order by q1.cliente, q1.fecha';

		$datos = $this->db->query($query)->result_array();

		$tecnicos = new Tecnico_toa();
		$matriz = $tecnicos->find('list', array('conditions' => array('id_empresa' => $empresa)));

		foreach ($matriz as $id_tecnico => $nombre)
		{
			$matriz[$id_tecnico] = array('nombre' => $nombre, 'actuaciones' => $arr_dias);
		}

		foreach ($datos as $registro)
		{
			$matriz[$registro['cliente']]['actuaciones'][substr($registro['fecha'], 8, 2)] = $registro['dato'];
		}

		return $matriz;
	}


	// --------------------------------------------------------------------

	public function control_asignaciones($empresa = NULL, $anomes = NULL, $filtro_trx = NULL, $dato_desplegar = 'asignaciones')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$arr_dias = $this->_get_arr_dias($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		if ($filtro_trx OR $filtro_trx !== '')
		{
			$this->db->where('codigo_movimiento', $filtro_trx);
		}

		$this->db
			->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
			->select('a.cliente')
			->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
			->group_by('a.cliente')
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<', $fecha_hasta)
			->where('b.id_empresa', $empresa)
			->where_in('codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('centro', $this->centros_consumo)
			->where('almacen', NULL)
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE);

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
		$matriz = $tecnicos->find('list', array('conditions' => array('id_empresa' => $empresa)));

		foreach ($matriz as $id_tecnico => $nombre)
		{
			$matriz[$id_tecnico] = array('nombre' => $nombre, 'actuaciones' => $arr_dias);
		}

		foreach ($datos as $registro)
		{
			$matriz[$registro['cliente']]['actuaciones'][substr($registro['fecha'], 8, 2)] = $registro['dato'];
		}

		return $matriz;
	}


	// --------------------------------------------------------------------

	public function stock_almacenes($empresa = NULL, $anomes = NULL, $dato_desplegar = 'monto')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$arr_dias = $this->_get_arr_dias($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		$almacenes = $this->db
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

		$this->db
			->select('convert(varchar(20), a.fecha_stock, 102) as fecha', FALSE)
			->select('d.tipo')
			->select('a.centro')
			->select('a.almacen')
			->select('b.des_almacen')
			->from($this->config->item('bd_stock_fija').' a')
			->join($this->config->item('bd_almacenes_sap').' b', 'a.centro=b.centro and a.almacen=b.cod_almacen', 'left')
			->join($this->config->item('bd_tipoalmacen_sap').' c', 'a.centro=c.centro and a.almacen=c.cod_almacen', 'left')
			->join($this->config->item('bd_tiposalm_sap').' d', 'c.id_tipo=d.id_tipo', 'left')
			->join($this->config->item('bd_empresas_toa_tiposalm').' e', 'd.id_tipo=e.id_tipo', 'left')
			->where('a.fecha_stock>=', $fecha_desde)
			->where('a.fecha_stock<', $fecha_hasta)
			->where('e.id_empresa', $empresa)
			->group_by('convert(varchar(20), a.fecha_stock, 102)')
			->group_by('d.tipo')
			->group_by('a.centro')
			->group_by('a.almacen')
			->group_by('b.des_almacen');

		if ($dato_desplegar === 'unidades')
		{
			$this->db->select_sum('a.cantidad', 'dato');
		}
		else
		{
			$this->db->select_sum('a.valor', 'dato');
		}

		$stock = $this->db->get()->result_array();

		$matriz = array();

		foreach ($almacenes as $almacen)
		{
			$matriz[$almacen['centro'].$almacen['cod_almacen']] = array(
				'tipo'        => $almacen['tipo'],
				'centro'      => $almacen['centro'],
				'cod_almacen' => $almacen['cod_almacen'],
				'des_almacen' => $almacen['des_almacen'],
				'actuaciones' => $arr_dias,
			);
		}

		foreach ($stock as $registro)
		{
			$matriz[$registro['centro'].$registro['almacen']]['actuaciones'][substr($registro['fecha'], 8, 2)] = $registro['dato'];
		}

		return $matriz;
	}


	// --------------------------------------------------------------------

	public function detalle_stock_almacen($fecha = NULL, $centro_almacen = NULL)
	{
		if ( ! $fecha OR ! $centro_almacen)
		{
			return NULL;
		}

		list($centro, $almacen) = explode('-', $centro_almacen);

		$arr_data = $this->db
			->select('convert(varchar(20), a.fecha_stock, 102) as fecha', FALSE)
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
			->join($this->config->item('bd_catalogos').' e', 'a.material collate Latin1_General_CI_AS = e.catalogo collate Latin1_General_CI_AS', 'left', FALSE)
			->where('a.fecha_stock=', $fecha)
			->where('a.centro', $centro)
			->where('a.almacen', $almacen)
			->where('d.es_sumable', 1)
			->get()->result_array();

		$arr_campos = array();
		$arr_campos['fecha']       = array('titulo' => 'Fecha', 'tipo' => 'texto');
		$arr_campos['tipo']        = array('titulo' => 'Tipo Almac&eacute;n', 'tipo' => 'texto');
		$arr_campos['centro']      = array('titulo' => 'Centro', 'tipo' => 'texto');
		$arr_campos['almacen']     = array('titulo' => 'Almac&eacute;n', 'tipo' => 'texto');
		$arr_campos['des_almacen'] = array('titulo' => 'Desc Almac&eacute;n', 'tipo' => 'texto');
		$arr_campos['material']    = array('titulo' => 'Material', 'tipo' => 'texto');
		$arr_campos['descripcion'] = array('titulo' => 'Desc Material', 'tipo' => 'texto');
		$arr_campos['lote']        = array('titulo' => 'Lote', 'tipo' => 'texto');
		$arr_campos['umb']         = array('titulo' => 'Unidad', 'tipo' => 'texto');
		$arr_campos['estado']      = array('titulo' => 'Estado', 'tipo' => 'texto');
		$arr_campos['cantidad']    = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
		$arr_campos['valor']       = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
		$this->reporte->set_order_campos($arr_campos, 'material');

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
	}


	// --------------------------------------------------------------------

	public function stock_tecnicos($empresa = NULL, $anomes = NULL, $dato_desplegar = 'monto')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$arr_dias = $this->_get_arr_dias($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = $this->_get_fecha_hasta($anomes);

		$tecnicos = new Tecnico_toa();
		$matriz = $tecnicos->find('list', array('conditions' => array('id_empresa' => $empresa)));

		$arr_tecnicos = array();
		foreach ($matriz as $id_tecnico => $tecnico)
		{
			array_push($arr_tecnicos, (string) $id_tecnico);
		}

		$this->db
			->select('convert(varchar(20), a.fecha_stock, 102) as fecha', FALSE)
			->select('a.acreedor')
			->from($this->config->item('bd_stock_fija').' a')
			->where('a.fecha_stock>=', $fecha_desde)
			->where('a.fecha_stock<', $fecha_hasta)
			->where_in('a.acreedor', $arr_tecnicos)
			->group_by('convert(varchar(20), a.fecha_stock, 102)')
			->group_by('a.acreedor');

		if ($dato_desplegar === 'unidades')
		{
			$this->db->select_sum('a.cantidad', 'dato');
		}
		else
		{
			$this->db->select_sum('a.valor', 'dato');
		}

		$stock = $this->db->get()->result_array();

		foreach ($matriz as $id_tecnico => $tecnico)
		{
			$matriz[$id_tecnico] = array(
				'tecnico'     => $tecnico,
				'actuaciones' => $arr_dias,
			);
		}

		foreach ($stock as $registro)
		{
			$matriz[$registro['acreedor']]['actuaciones'][substr($registro['fecha'], 8, 2)] = $registro['dato'];
		}

		return $matriz;
	}


	// --------------------------------------------------------------------

	public function detalle_stock_tecnico($fecha = NULL, $id_tecnico = NULL)
	{
		if ( ! $fecha OR ! $id_tecnico)
		{
			return NULL;
		}

		$arr_data = $this->db
			->select('convert(varchar(20), a.fecha_stock, 102) as fecha', FALSE)
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
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.acreedor collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
			->join($this->config->item('bd_catalogos').' c', 'a.material collate Latin1_General_CI_AS = c.catalogo collate Latin1_General_CI_AS', 'left', FALSE)
			->where('a.fecha_stock=', $fecha)
			->where('a.acreedor', $id_tecnico)
			->order_by('material, lote')
			->get()->result_array();

		$arr_campos = array();
		$arr_campos['fecha']       = array('titulo' => 'Fecha', 'tipo' => 'texto');
		$arr_campos['centro']      = array('titulo' => 'Centro', 'tipo' => 'texto');
		$arr_campos['acreedor']    = array('titulo' => 'Cod T&eacute;cnico', 'tipo' => 'texto');
		$arr_campos['tecnico']     = array('titulo' => 'Nombre T&eacute;cnico', 'tipo' => 'texto');
		$arr_campos['material']    = array('titulo' => 'Material', 'tipo' => 'texto');
		$arr_campos['descripcion'] = array('titulo' => 'Desc Material', 'tipo' => 'texto');
		$arr_campos['lote']        = array('titulo' => 'Lote', 'tipo' => 'texto');
		$arr_campos['umb']         = array('titulo' => 'Unidad', 'tipo' => 'texto');
		$arr_campos['estado']      = array('titulo' => 'Estado', 'tipo' => 'texto');
		$arr_campos['cantidad']    = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
		$arr_campos['valor']       = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
		$this->reporte->set_order_campos($arr_campos, 'material');

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
	}


}
/* End of file Toa_model.php */
/* Location: ./application/models/Toa_model.php */
