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
class Movs_fija_model extends CI_Model {

	/**
	 * Movimientos validos de consumos TOA
	 *
	 * @var array
	 */
	public $movimientos_consumo = array('Z35', 'Z45');

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
	 * Tipos de reporte consumo
	 *
	 * @var array
	 */
	public $tipos_reporte_consumo = array(
		'orden_at'      => 'N&uacute;mero petici&oacute;n',
		'material'      => 'Materiales',
		'lote'          => 'Lotes',
		'lote-material' => 'Lotes y materiales',
		'pep'           => 'PEP',
		'tecnicos'      => 'T&eacute;cnicos',
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
		else if ($tipo_reporte === 'orden_at')
		{
			$orden_campo = ($orden_campo === '') ? 'referencia' : $orden_campo;

			$arr_data = $this->db
				->select('convert(varchar(20), a.fecha_contabilizacion, 102) as fecha', FALSE)
				->select('a.referencia')
				->select('c.empresa')
				->select('a.cliente')
				->select("'ver detalle' as texto_link")
				->select('b.tecnico')
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
				->group_by('a.referencia')
				->group_by('c.empresa')
				->group_by('a.cliente')
				->group_by('b.tecnico')
				->from($this->config->item('bd_movimientos_sap_fija').' a')
				->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
				->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				//->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = array();
			$arr_campos['fecha']      = array('titulo' => 'Fecha', 'tipo' => 'texto');
			$arr_campos['referencia'] = array('titulo' => 'Numero peticion', 'tipo' => 'texto');
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
				//->order_by($orden_campo, $orden_tipo)
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
				//->order_by($orden_campo, $orden_tipo)
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
				//->order_by($orden_campo, $orden_tipo)
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
				//->order_by($orden_campo, $orden_tipo)
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
				//->order_by($orden_campo, $orden_tipo)
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
			->select('c.empresa')
			->select('a.cliente')
			->select('b.tecnico')
			->select("'ver detalle' as texto_link")
			->select_sum('(-a.cantidad_en_um)', 'cant')
			->select_sum('(-a.importe_ml)', 'monto')
			->group_by('convert(varchar(20), a.fecha_contabilizacion, 102)')
			->group_by('a.referencia')
			->group_by('c.empresa')
			->group_by('a.cliente')
			->group_by('b.tecnico')
			->from($this->config->item('bd_movimientos_sap_fija').' a')
			->join($this->config->item('bd_tecnicos_toa').' b', 'a.cliente collate Latin1_General_CI_AS = b.id_tecnico collate Latin1_General_CI_AS', 'left', FALSE)
			->join($this->config->item('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->order_by('a.referencia', 'ASC')
			->get()->result_array();

		$arr_campos = array();
		$arr_campos['fecha']      = array('titulo' => 'Fecha', 'tipo' => 'texto');
		$arr_campos['referencia'] = array('titulo' => 'Numero peticion', 'tipo' => 'texto');
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
			->where('fecha_contabilizacion', $fecha)
			->where('referencia', $peticion)
			->where_in('codigo_movimiento', $this->movimientos_consumo)
			->where_in('centro', $this->centros_consumo)
			->order_by('material')
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
		$arr_campos['cant']               = array('titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right');
		$arr_campos['monto']              = array('titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right');
		$arr_campos['texto_link']         = array('titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_consumos/detalle_peticion', 'href_registros' => array('fecha','documento_material'));
		$this->reporte->set_order_campos($arr_campos, 'documento_material');

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
	}





}
/* End of file Movs_fija_model.php */
/* Location: ./application/models/Movs_fija_model.php */
