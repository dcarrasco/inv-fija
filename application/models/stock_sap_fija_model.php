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
 * Clase Modelo Stock SAP
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock_sap_fija_model extends Stock_sap_model {

	/**
	 * Indica el tipo de operación del stock (fijo o movil)
	 * @var string
	 */
	public $tipo_op = 'FIJA';

	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock operación fija
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con campos a filtrar
	 * @return array          Arreglo con stock
	 */
	public function get_stock($mostrar = [], $filtrar = [])
	{
		$arr_result = [];

		// fecha stock
		if (in_array('fecha', $mostrar))
		{
			$this->db->select('s.fecha_stock');
			$this->db->group_by('s.fecha_stock');
			$this->db->order_by('fecha_stock');
		}

		// tipo de almacen
		if ($filtrar['sel_tiposalm'] === 'sel_tiposalm')
		{
			$this->db->select('t.tipo as tipo_almacen');
			$this->db->group_by('t.tipo');
			$this->db->order_by('t.tipo');

			if (in_array('almacen', $mostrar))
			{
				$this->db->select('ta.centro, ta.cod_almacen, a.des_almacen');
				$this->db->group_by('ta.centro, ta.cod_almacen, a.des_almacen');
				$this->db->order_by('ta.centro, ta.cod_almacen, a.des_almacen');
			}
		}
		else
		{
			$this->db->select('s.centro, s.almacen, a.des_almacen');
			$this->db->group_by('s.centro, s.almacen, a.des_almacen');
			$this->db->order_by('s.centro, s.almacen, a.des_almacen');
		}

		// almacenes
		// tipos de articulos
		if (in_array('tipo_articulo', $mostrar))
		{
			//$this->db->select("CASE WHEN (substring(cod_articulo,1,8)='PKGCLOTK' OR substring(cod_articulo,1,2)='TS') THEN 'SIMCARD' WHEN substring(cod_articulo, 1,2) in ('TM','TO','TC','PK','PO') THEN 'EQUIPOS' ELSE 'OTROS' END as tipo_articulo", FALSE);
			//$this->db->group_by("CASE WHEN (substring(cod_articulo,1,8)='PKGCLOTK' OR substring(cod_articulo,1,2)='TS') THEN 'SIMCARD' WHEN substring(cod_articulo, 1,2) in ('TM','TO','TC','PK','PO') THEN 'EQUIPOS' ELSE 'OTROS' END", FALSE);
			//$this->db->order_by('tipo_articulo');
		}

		// materiales
		if (in_array('material', $mostrar))
		{
			$this->db->select('s.material, m.descripcion, s.umb');
			$this->db->join(config('bd_catalogos') . ' m', 's.material=m.catalogo', 'left', FALSE);
			$this->db->group_by('s.material, m.descripcion, s.umb');
			$this->db->order_by('s.material, m.descripcion, s.umb');
		}

		// lotes
		if (in_array('lote', $mostrar))
		{
			$this->db->select('s.lote');
			$this->db->group_by('s.lote');
		}

		// cantidades y tipos de stock
		// cantidades y tipos de stock
		if (in_array('tipo_stock', $mostrar))
		{
			$this->db->select('s.estado');
			$this->db->group_by('s.estado');
			$this->db->order_by('s.estado');
		}

		$this->db->select('sum(s.cantidad) as cantidad, sum(s.valor) as monto', FALSE);

		// tablas
		if ($filtrar['sel_tiposalm'] === 'sel_tiposalm')
		{
			$this->db->from(config('bd_stock_fija') . ' s');
			$this->db->join(config('bd_tipoalmacen_sap') . ' ta', 's.almacen=ta.cod_almacen and s.centro=ta.centro');
			$this->db->join(config('bd_tiposalm_sap') . ' t', 't.id_tipo=ta.id_tipo');
			$this->db->join(config('bd_almacenes_sap') . ' a', 'a.centro=ta.centro and a.cod_almacen=ta.cod_almacen', 'left');
		}
		else
		{
			$this->db->from(config('bd_stock_fija') . ' s');
			$this->db->join(config('bd_almacenes_sap') . ' a', 'a.centro=s.centro and a.cod_almacen=s.almacen', 'left');
		}

		// condiciones
		// fechas
		if (array_key_exists('fecha', $filtrar))
		{
			$this->db->where_in('s.fecha_stock', $filtrar['fecha']);
		}

		// tipos de almacen
		if ($filtrar['sel_tiposalm'] === 'sel_tiposalm')
		{
			$this->db->where_in('t.id_tipo', $filtrar['almacenes']);
		}
		else
		{
			$this->db->where_in("s.centro + '~' + s.almacen", $filtrar['almacenes']);
		}

		// tipos de articulo
		/*
		if (array_key_exists('tipo_articulo', $filtrar))
		{
			$this->db->where_in('tipo_articulo', $filtrar['tipo_articulo']);
		}
		*/

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera stock en transito, operación fija y/o movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con filtros a aplicar
	 * @return array           Stock en transito
	 */
	public function get_stock_transito($mostrar = [], $filtrar = [])
	{
		$arr_result = [];

		if (array_key_exists('fecha', $filtrar) AND count($filtrar['fecha'])>0)
		{
			// fecha stock
			if (in_array('fecha', $mostrar))
			{
				$this->db->select('s.fecha_stock');
				$this->db->group_by('s.fecha_stock');
				$this->db->order_by('fecha_stock');
			}

			// almacenes
			if (in_array('almacen', $mostrar))
			{
				$this->db->select('s.centro');
				$this->db->group_by('s.centro');
				$this->db->order_by('s.centro');
			}

			// cantidades y tipos de stock
			if (in_array('tipo_stock', $mostrar))
			{
				$this->db->select('s.estado');
				$this->db->group_by('s.estado');
				$this->db->order_by('s.estado');
			}

			// materiales
			if (in_array('material', $mostrar))
			{
				$this->db->select('s.material, m.descripcion, s.umb');
				$this->db->join(config('bd_catalogos') . ' m', 's.material=m.catalogo', 'left');
				$this->db->group_by('s.material, m.descripcion, s.umb');
				$this->db->order_by('s.material, m.descripcion, s.umb');
			}

			// lotes
			if (in_array('lote', $mostrar))
			{
				$this->db->select('s.lote');
				$this->db->group_by('s.lote');
			}

			$this->db->select('s.acreedor, p.des_proveedor')
				->select('sum(s.cantidad) as cantidad, sum(s.valor) as monto', FALSE)
				->group_by('s.acreedor, p.des_proveedor');

			// tablas
			$this->db->from(config('bd_stock_fija').' s');
			$this->db->join(config('bd_proveedores').' p', 's.acreedor=p.cod_proveedor', 'left');

			// condiciones
			// fechas
			if (array_key_exists('fecha', $filtrar))
			{
				$this->db->where_in('s.fecha_stock', $filtrar['fecha']);
			}

			$this->db->where('s.almacen is null');
			$this->db->where("((len(s.acreedor)<>7 and s.acreedor not like '130%') or s.acreedor is null)");


			$arr_result = $this->db->get()->result_array();

			return $this->format_table_stock($arr_result, $mostrar, $filtrar);
		}

	}

	// --------------------------------------------------------------------

	/**
	 * Recupera fechas de la operación fija para mostrar en combobox
	 *
	 * @return array Fechas
	 */
	public function get_data_combo_fechas()
	{
		$arr_result = $this->db
			->select('fecha_stock')
			->order_by('fecha_stock', 'desc')
			->get(config('bd_stock_fija_fechas'))
			->result_array();

		foreach ($arr_result as $indice => $arr_fecha)
		{
			$arr_result[$indice] = [
				'llave' => fmt_fecha_db($arr_fecha['fecha_stock']),
				'valor' => fmt_fecha($arr_fecha['fecha_stock']),
			];
		}

		return form_array_format($arr_result);
	}


	// --------------------------------------------------------------------

	/**
	 * Inserta registros del reporte de stock por clasificacion en tabla resumen
	 *
	 * @param  string $fecha Fecha del reporte a generar
	 * @return boolean       Indicador de exito o fallo de la query
	 */
	private function _genera_reporte_clasificacion($fecha = NULL)
	{
		$sql_query = 'INSERT INTO ' . config('bd_reporte_clasif');
		$sql_query .= " SELECT
A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
SUM(E.CANTIDAD) AS CANTIDAD, SUM(E.VALOR) AS MONTO
FROM " . config('bd_clasifalm_sap') . " A
JOIN " . config('bd_clasif_tipoalm_sap') . " B ON A.ID_CLASIF=B.ID_CLASIF
JOIN " . config('bd_tiposalm_sap')       . " C ON B.ID_TIPO=C.ID_TIPO
JOIN " . config('bd_tipoalmacen_sap')    . " D ON C.ID_TIPO=D.ID_TIPO
JOIN " . config('bd_stock_fija')         . " E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.ALMACEN
JOIN " . config('bd_tipo_clasifalm_sap') . " F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
WHERE A.TIPO_OP = ?
AND E.FECHA_STOCK = ?
GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR
ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN";

		return $this->db->query($sql_query, [$tipo_op, $fecha]);

	}

}
/* End of file stock_sap_model.php */
/* Location: ./application/models/stock_sap_model.php */
