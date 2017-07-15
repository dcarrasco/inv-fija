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
class Stock_sap_movil_model extends Stock_sap_model {

	/**
	 * Indica el tipo de operación del stock (fijo o movil)
	 * @var string
	 */
	public $tipo_op = 'MOVIL';

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
	 * Recupera stock operación movil
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
			if (in_array('material', $mostrar))
			{
				$this->db->select('s.fecha_stock');
				$this->db->group_by('s.fecha_stock');
				$this->db->order_by('fecha_stock');
			}
			else
			{
				$this->db->select('s.fecha_stock');
				$this->db->group_by('s.fecha_stock');
				$this->db->order_by('fecha_stock');
			}
		}

		// tipo de almacen
		if ($filtrar['sel_tiposalm'] === 'sel_tiposalm')
		{
			$this->db->select('t.tipo as tipo_almacen');
			$this->db->group_by('t.tipo');
			$this->db->order_by('t.tipo');

			// almacenes
			if (in_array('almacen', $mostrar))
			{
				$this->db->select('ta.centro, ta.cod_almacen, a.des_almacen');
				$this->db->group_by('ta.centro, ta.cod_almacen, a.des_almacen');
				$this->db->order_by('ta.centro, ta.cod_almacen, a.des_almacen');
			}
		}
		else
		{
			$this->db->select('a.centro, s.cod_bodega as cod_almacen, a.des_almacen');
			$this->db->group_by('a.centro, s.cod_bodega, a.des_almacen');
			$this->db->order_by('a.centro, s.cod_bodega, a.des_almacen');
		}

		// lotes
		if (in_array('lote', $mostrar))
		{
			$this->db->select('s.lote');
			$this->db->group_by('s.lote');
			$this->db->order_by('lote');
		}

		// tipos de articulos
		if (in_array('tipo_articulo', $mostrar))
		{
			if (in_array('material', $mostrar))
			{
				$this->db->select("CASE WHEN (substring(cod_articulo,1,8)='PKGCLOTK' OR substring(cod_articulo,1,2)='TS') THEN 'SIMCARD' WHEN substring(cod_articulo, 1,2) in ('TM','TO','TC','PK','PO') THEN 'EQUIPOS' ELSE 'OTROS' END as tipo_articulo", FALSE);
				$this->db->group_by("CASE WHEN (substring(cod_articulo,1,8)='PKGCLOTK' OR substring(cod_articulo,1,2)='TS') THEN 'SIMCARD' WHEN substring(cod_articulo, 1,2) in ('TM','TO','TC','PK','PO') THEN 'EQUIPOS' ELSE 'OTROS' END", FALSE);
			}
			else
			{
				$this->db->select('s.tipo_articulo');
				$this->db->group_by('s.tipo_articulo');
				$this->db->order_by('tipo_articulo');
			}
		}

		// materiales
		if (in_array('material', $mostrar))
		{
			$this->db->select('s.cod_articulo');
			$this->db->group_by('s.cod_articulo');
			$this->db->order_by('s.cod_articulo');
		}


		// cantidades y tipos de stock
		if (in_array('tipo_stock', $mostrar))
		{
			if (in_array('material', $mostrar))
			{
				$this->db->select_sum('s.libre_utilizacion', 'LU');
				$this->db->select_sum('s.bloqueado', 'BQ');
				$this->db->select_sum('s.contro_calidad', 'CC');
				$this->db->select_sum('s.transito_traslado', 'TT');
				$this->db->select_sum('s.otros', 'OT');

				$this->db->select_sum('(s.VAL_LU)', 'VAL_LU');
				$this->db->select_sum('(s.VAL_BQ)', 'VAL_BQ');
				$this->db->select_sum('(s.VAL_CQ)', 'VAL_CC');
				$this->db->select_sum('(s.VAL_TT)', 'VAL_TT');
				$this->db->select_sum('(s.VAL_OT)', 'VAL_OT');
			}
			else
			{
				$this->db->select_sum('s.LU', 'LU');
				$this->db->select_sum('s.BQ', 'BQ');
				$this->db->select_sum('s.CC', 'CC');
				$this->db->select_sum('s.TT', 'TT');
				$this->db->select_sum('s.OT', 'OT');

				$this->db->select_sum('s.V_LU', 'VAL_LU');
				$this->db->select_sum('s.V_BQ', 'VAL_BQ');
				$this->db->select_sum('s.V_CC', 'VAL_CC');
				$this->db->select_sum('s.V_TT', 'VAL_TT');
				$this->db->select_sum('s.V_OT', 'VAL_OT');
			}
		}

		if (in_array('material', $mostrar))
		{
			$this->db->select_sum('(s.libre_utilizacion + s.bloqueado + s.contro_calidad + s.transito_traslado + s.otros)', 'total');
			$this->db->select_sum('(s.VAL_LU + s.VAL_BQ + s.VAL_CQ + s.VAL_TT + s.VAL_OT)', 'VAL_total');
		}
		else
		{
			$this->db->select_sum('s.cant', 'total');
			$this->db->select_sum('s.monto', 'VAL_total');
		}

		// tablas
		if ($filtrar['sel_tiposalm'] === 'sel_tiposalm')
		{
			$this->db->from(config('bd_tiposalm_sap') . ' t');
			$this->db->join(config('bd_tipoalmacen_sap') . ' ta', 'ta.id_tipo = t.id_tipo');
			$this->db->join(config('bd_almacenes_sap') . ' a', 'a.centro = ta.centro and a.cod_almacen=ta.cod_almacen', 'left');

			if (in_array('material', $mostrar))
			{
				$this->db->join(config('bd_stock_movil') . ' s', 's.centro = ta.centro and s.cod_bodega=ta.cod_almacen');
				//$this->db->join(config('bd_pmp') . ' p', "p.centro = s.centro and p.material=s.cod_articulo and p.lote=s.lote and p.estado_stock='01'",'left');
			}
			else
			{
				$this->db->join(config('bd_stock_movil_res01') . ' s', 's.centro = ta.centro and s.cod_bodega=ta.cod_almacen');
			}
		}
		else
		{
			if (in_array('material', $mostrar))
			{
				$this->db->from(config('bd_stock_movil') . ' s');
				$this->db->join(config('bd_pmp') . ' p', "p.centro = s.centro and p.material=s.cod_articulo and p.lote=s.lote and p.estado_stock='01'", 'left');
				$this->db->join(config('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.cod_bodega=a.cod_almacen', 'left');
			}
			else
			{
				$this->db->from(config('bd_stock_movil_res01') . ' s');
				$this->db->join(config('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.cod_bodega=a.cod_almacen', 'left');
			}


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
			$this->db->where_in("s.centro + '~' + s.cod_bodega", $filtrar['almacenes']);
		}

		// tipos de articulo
		/*
		if (array_key_exists('tipo_articulo', $filtrar))
		{
			$this->db->where_in('tipo_articulo', $filtrar['tipo_articulo']);
		}
		*/

		//$this->db->where('s.origen', 'SAP');

		if (in_array('tipo_stock', $mostrar))
		{
			$arr_filtro_tipo_stock = [];

			if (in_array('tipo_stock_equipos', $filtrar))
			{
				array_push($arr_filtro_tipo_stock, 'EQUIPOS');
			}

			if (in_array('tipo_stock_simcard', $filtrar))
			{
				array_push($arr_filtro_tipo_stock, 'SIMCARD');
			}

			if (in_array('tipo_stock_otros', $filtrar))
			{
				array_push($arr_filtro_tipo_stock, 'OTROS');
			}

			if (in_array('material', $mostrar))
			{
				$this->db->where_in("CASE WHEN (substring(cod_articulo,1,8)='PKGCLOTK' OR substring(cod_articulo,1,2)='TS') THEN 'SIMCARD' WHEN substring(cod_articulo, 1,2) in ('TM','TO','TC','PK','PO') THEN 'EQUIPOS' ELSE 'OTROS' END", $arr_filtro_tipo_stock);
			}
			else
			{
				$this->db->where_in('s.tipo_articulo', $arr_filtro_tipo_stock);
			}
		}

		$arr_result = $this->db->get()->result_array();

		$arr_stock_tmp01 = [];
		// si tenemos tipos de articulo y no tenemos el detalle de estados de stock,
		// entonces hacemos columnas con los totales de tipos_articulo (equipos, simcards y otros)
		if (in_array('tipo_articulo', $mostrar) AND  ! in_array('tipo_stock', $mostrar))
		{
			foreach($arr_result as $registro)
			{
				$arr_reg = [];
				$llave_total = '';
				$valor_total = 0;
				$valor_monto = 0;

				foreach ($registro as $campo_key => $campo_val)
				{
					if ($campo_key === 'tipo_articulo')
					{
						$llave_total = $campo_val;
					}
					elseif ($campo_key === 'total')
					{
						$valor_total = $campo_val;
					}
					elseif ($campo_key === 'VAL_total')
					{
						$valor_monto = $campo_val;
					}
					else
					{
						$arr_reg[$campo_key] = $campo_val;
					}
				}
				$arr_reg[$llave_total] = $valor_total;
				$arr_reg['VAL_' . $llave_total] = $valor_monto;
				array_push($arr_stock_tmp01, $arr_reg);
			}

			$arr_stock = [];
			$llave_ant = '';
			$i = 0;

			foreach($arr_stock_tmp01 as $registro)
			{
				$i += 1;
				$llave_act = '';
				$llave_total = '';
				$llave_monto = '';
				$valor_total = 0;
				$valor_monto = 0;

				// determina la llave actual y el valor del campo total
				$arr_tmp = [];
				foreach ($registro as $campo_key => $campo_val)
				{
					if ($campo_key === 'EQUIPOS' OR $campo_key === 'OTROS' OR $campo_key === 'SIMCARD')
					{
						$llave_total = $campo_key;
						$valor_total = $campo_val;
					}
					elseif ($campo_key === 'VAL_EQUIPOS' OR $campo_key === 'VAL_OTROS' OR $campo_key === 'VAL_SIMCARD')
					{
						$llave_monto = $campo_key;
						$valor_monto = $campo_val;
					}
					else
					{
						$llave_act .= $campo_val;
						$arr_tmp[$campo_key] = $campo_val;
					}
				}

				// si la llave es distinta y no es el primer registro, agregamos el arreglo anterior
				// y guardamos los nuevos valores en el arreglo anterior
				if ($llave_act !== $llave_ant)
				{
					if ($i !== 1)
					{
						//echo "i: $i llave_act: $llave_act llave_ant: $llave_ant<br>";
						array_push($arr_stock, $arr_ant);
					}

					$arr_ant = [];
					$arr_ant = $arr_tmp;
					$arr_ant['EQUIPOS']     = 0;
					$arr_ant['VAL_EQUIPOS'] = 0;
					$arr_ant['SIMCARD']     = 0;
					$arr_ant['VAL_SIMCARD'] = 0;
					$arr_ant['OTROS']       = 0;
					$arr_ant['VAL_OTROS']   = 0;
					$arr_ant[$llave_total] = $valor_total;
					$arr_ant[$llave_monto] = $valor_monto;

					$llave_ant = $llave_act;
				}
				// si la llave es la misma, solo agregamos el valor de equipos, otros o simcard
				else
				{
					$arr_ant[$llave_total] = $valor_total;
					$arr_ant[$llave_monto] = $valor_monto;
				}
			}

			// finalmente agregamos el ultimo valor del arreglo anterior
			if ($i > 0)
			{
				array_push($arr_stock, $arr_ant);
			}

			$arr_result = $arr_stock;
		}

		return $arr_result;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera stock en transito, operación fija y/o movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con filtros a aplicar
	 * @return array          Stock en transito
	 */
	public function get_stock_transito($mostrar = [], $filtrar = [])
	{
		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera fechas de la operación movil para mostrar en combobox
	 *
	 * @return array Fechas
	 */
	public function get_data_combo_fechas()
	{
		$arr_result = $this->db
			->select('fecha_stock')
			->order_by('fecha_stock', 'desc')
			->get(config('bd_stock_movil_fechas'))
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
	 * Recupera el detalle de las series, dado un centro, almacén material y lote
	 *
	 * @param  string $centro   Centro series a recuperar
	 * @param  string $almacen  Almacén series a recuperar
	 * @param  string $material Material series a recuperar
	 * @param  string $lote     Lote series a recuperar
	 * @return string           Texto tabla formateada
	 */
	public function get_detalle_series($centro = '', $almacen = '', $material = '', $lote = '')
	{
		if ($almacen === '' OR $almacen === '_')
		{
			return '';
		}

		if ($centro !== '' AND $centro !== '_')
		{
			$this->db->where('s.centro', $centro);
		}

		if ($almacen !== '' AND $almacen !== '_')
		{
			$this->db->where('s.almacen', $almacen);
		}

		if ($material !== '' AND $material !== '_')
		{
			$this->db->where('s.material', $material);
		}

		if ($lote !== '' AND $lote !== '_')
		{
			$this->db->where('s.lote', $lote);
		}

		$this->db
			->select('row_number() over (order by s.material, s.lote, s.serie) as lin', FALSE)
			->select('convert(varchar(20), s.fecha_stock, 103) as fecha_stock')
			->select('s.centro')
			->select('s.almacen')
			->select('a.des_almacen')
			->select('s.material')
			->select('s.des_material')
			->select('s.lote')
			->select('s.serie')
			->select('s.estado_stock')
			->select('p.pmp')
			->select('convert(varchar(20), s.modificado_el, 103) as fecha_modificacion')
			->select('s.modificado_por')
			->select('u.nom_usuario')
			->from(config('bd_stock_seriado_sap') . ' as s')
			->join(config('bd_almacenes_sap') . ' as a', 'a.centro=s.centro and a.cod_almacen=s.almacen', 'left')
			->join(config('bd_usuarios_sap') . ' as u', 'u.usuario=s.modificado_por', 'left')
			->join(config('bd_pmp') . ' p', 'p.centro = s.centro and p.material=s.material and p.lote=s.lote and p.estado_stock=s.estado_stock', 'left')
			->order_by('s.material, s.lote, s.serie');

		return $this->table->generate($this->db->get());
	}

	// --------------------------------------------------------------------

	/**
	 * Inserta registros del reporte de stock por clasificacion en tabla resumen
	 *
	 * @param  string $fecha   Fecha del reporte a generar
	 * @return boolean         Indicador de exito o fallo de la query
	 */
	private function _genera_reporte_clasificacion($fecha = NULL)
	{
		$sql_query = "INSERT INTO " . config('bd_reporte_clasif') .
" SELECT
A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
SUM(E.LIBRE_UTILIZACION + E.BLOQUEADO + E.CONTRO_CALIDAD + E.TRANSITO_TRASLADO + E.OTROS) AS CANTIDAD,
SUM(E.VAL_LU + E.VAL_BQ + E.VAL_CQ + E.VAL_TT + E.VAL_OT) AS MONTO
FROM " . config('bd_clasifalm_sap')      . " A
JOIN " . config('bd_clasif_tipoalm_sap') . " B ON A.ID_CLASIF=B.ID_CLASIF
JOIN " . config('bd_tiposalm_sap')       . " C ON B.ID_TIPO=C.ID_TIPO
JOIN " . config('bd_tipoalmacen_sap')    . " D ON C.ID_TIPO=D.ID_TIPO
JOIN " . config('bd_stock_movil')        . " E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.COD_BODEGA
JOIN " . config('bd_tipo_clasifalm_sap') . " F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
WHERE A.TIPO_OP= ?
AND E.FECHA_STOCK = ?
GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION, F.TIPO, F.COLOR
ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION";

		return $this->db->query($sql_query, [$tipo_op, $fecha]);

	}


}
/* End of file stock_sap_movil_model.php */
/* Location: ./application/models/stock_sap_movil_model.php */
