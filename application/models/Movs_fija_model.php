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
	 * Movimeintos validos de consumos TOA
	 *
	 * @var array
	 */
	public $movimientos_consumo = array('Z35', 'Z45');

	/**
	 * Centros validos de consumos TOA
	 *
	 * @var array
	 */
	public $centros_consumo = array('CH32', 'CH33');


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
	 * @param  string  $usuario  usuario
	 * @param  string  $password clave
	 * @param  boolean $remember Indica si el usuario será recordado
	 * @return boolean           TRUE si se loguea al usuario, FALSE si no
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
			$this->reporte->set_order_campos($arr_campos, 'empresa');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}


	}


}
/* End of file Movs_fija_model.php */
/* Location: ./application/models/Movs_fija_model.php */
