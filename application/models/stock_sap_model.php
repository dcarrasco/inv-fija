<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class stock_sap_model extends CI_Model {

	public function __construct()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	public function get_stock($tipo_op = '', $mostrar = array(), $filtrar = array())
	{
		if ($tipo_op == 'MOVIL')
		{
			return $this->_get_stock_movil($mostrar, $filtrar);
		}
		else
		{
			return $this->_get_stock_fijo($mostrar, $filtrar);
		}
	}


	// --------------------------------------------------------------------

	public function _get_stock_movil($mostrar = array(), $filtrar = array())
	{
		$arr_result = array();

		// fecha stock
		if (in_array('fecha', $mostrar))
		{
			$this->db->select('s.fecha_stock');
			$this->db->group_by('s.fecha_stock');
			$this->db->order_by('fecha_stock');
		}

		// tipo de almacen
		if ($filtrar['sel_tiposalm'] == 'sel_tiposalm')
		{
			if (in_array('tipo_alm', $mostrar))
			{
				$this->db->select('t.tipo as tipo_almacen');
				$this->db->group_by('t.tipo');
				$this->db->order_by('t.tipo');
			}

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
				$this->db->select_sum('s.otros','OT');

				$this->db->select_sum('(s.VAL_LU)', 'VAL_LU');
				$this->db->select_sum('(s.VAL_BQ)', 'VAL_BQ');
				$this->db->select_sum('(s.VAL_CQ)', 'VAL_CC');
				$this->db->select_sum('(s.VAL_TT)', 'VAL_TT');
				$this->db->select_sum('(s.VAL_OT)','VAL_OT');
			}
			else
			{
				$this->db->select_sum('s.LU','LU');
				$this->db->select_sum('s.BQ','BQ');
				$this->db->select_sum('s.CC','CC');
				$this->db->select_sum('s.TT','TT');
				$this->db->select_sum('s.OT','OT');

				$this->db->select_sum('s.V_LU','VAL_LU');
				$this->db->select_sum('s.V_BQ','VAL_BQ');
				$this->db->select_sum('s.V_CC','VAL_CC');
				$this->db->select_sum('s.V_TT','VAL_TT');
				$this->db->select_sum('s.V_OT','VAL_OT');
			}
		}

		if (in_array('material', $mostrar))
		{
			$this->db->select_sum('(s.libre_utilizacion + s.bloqueado + s.contro_calidad + s.transito_traslado + s.otros)','total');
			$this->db->select_sum('(s.VAL_LU + s.VAL_BQ + s.VAL_CQ + s.VAL_TT + s.VAL_OT)','VAL_total');
		}
		else
		{
			$this->db->select_sum('s.cant','total');
			$this->db->select_sum('s.monto','VAL_total');
		}

		// tablas
		if ($filtrar['sel_tiposalm'] == 'sel_tiposalm')
		{
			$this->db->from($this->config->item('bd_tiposalm_sap') . ' t');
			$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'ta.id_tipo = t.id_tipo');
			$this->db->join($this->config->item('bd_almacenes_sap') . ' a', 'a.centro = ta.centro and a.cod_almacen=ta.cod_almacen', 'left');

			if (in_array('material', $mostrar))
			{
				$this->db->join($this->config->item('bd_stock_movil') . ' s', 's.centro = ta.centro and s.cod_bodega=ta.cod_almacen');
				//$this->db->join($this->config->item('bd_pmp') . ' p', "p.centro = s.centro and p.material=s.cod_articulo and p.lote=s.lote and p.estado_stock='01'",'left');
			}
			else
			{
				$this->db->join($this->config->item('bd_stock_movil_res01') . ' s', 's.centro = ta.centro and s.cod_bodega=ta.cod_almacen');
			}
		}
		else
		{
			if (in_array('material', $mostrar))
			{
				$this->db->from($this->config->item('bd_stock_movil') . ' s');
				$this->db->join($this->config->item('bd_pmp') . ' p', "p.centro = s.centro and p.material=s.cod_articulo and p.lote=s.lote and p.estado_stock='01'",'left');
				$this->db->join($this->config->item('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.cod_bodega=a.cod_almacen', 'left');
			}
			else
			{
				$this->db->from($this->config->item('bd_stock_movil_res01') . ' s');
				$this->db->join($this->config->item('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.cod_bodega=a.cod_almacen', 'left');
			}


		}


		// condiciones
		// fechas
		if (array_key_exists('fecha', $filtrar))
		{
			$this->db->where_in('s.fecha_stock', $filtrar['fecha']);
		}

		// tipos de almacen
		if ($filtrar['sel_tiposalm'] == 'sel_tiposalm')
		{
			if (array_key_exists('tipo_alm', $filtrar))
			{
				$this->db->where_in('t.id_tipo', $filtrar['tipo_alm']);
			}
		}
		else if($filtrar['sel_tiposalm'] == 'sel_almacenes')
		{
			$this->db->where_in("s.centro+'-'+s.cod_bodega", $filtrar['almacenes']);
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
			$arr_filtro_tipo_stock = array();

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

		//dbg($arr_result);

		$arr_stock_tmp01 = array();
		// si tenemos tipos de articulo y no tenemos el detalle de estados de stock,
		// entonces hacemos columnas con los totales de tipos_articulo (equipos, simcards y otros)
		if (in_array('tipo_articulo', $mostrar) and !(in_array('tipo_stock', $mostrar)))
		{
			foreach($arr_result as $reg)
			{
				$arr_reg = array();
				$llave_total = '';
				$valor_total = 0;
				$valor_monto = 0;
				foreach ($reg as $campo_key => $campo_val)
				{
					if ($campo_key == 'tipo_articulo')
					{
						$llave_total = $campo_val;
					}
					else if ($campo_key == 'total')
					{
						$valor_total = $campo_val;
					}
					else if ($campo_key == 'VAL_total')
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


			$arr_stock = array();
			$llave_ant = '';
			$i = 0;
			foreach($arr_stock_tmp01 as $reg)
			{
				$i += 1;
				$llave_act = '';
				$llave_total = '';
				$llave_monto = '';
				$valor_total = 0;
				$valor_monto = 0;

				// determina la llave actual y el valor del campo total
				$arr_tmp = array();
				foreach ($reg as $campo_key => $campo_val)
				{
					if ($campo_key == 'EQUIPOS' || $campo_key == 'OTROS' || $campo_key == 'SIMCARD')
					{
						$llave_total = $campo_key;
						$valor_total = $campo_val;
					}
					else if ($campo_key == 'VAL_EQUIPOS' || $campo_key == 'VAL_OTROS' || $campo_key == 'VAL_SIMCARD')
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
				if ($llave_act != $llave_ant)
				{
					if ($i != 1)
					{
						//echo "i: $i llave_act: $llave_act llave_ant: $llave_ant<br>";
						array_push($arr_stock, $arr_ant);
					}

					$arr_ant = array();
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

		//var_dump($arr_result);

		return $arr_result;
	}


	// --------------------------------------------------------------------

	public function _get_stock_fijo($mostrar = array(), $filtrar = array())
	{
		$arr_result = array();

		// fecha stock
		if (in_array('fecha', $mostrar))
		{
			$this->db->select('convert(varchar(20), s.fecha_stock, 102) as fecha_stock', FALSE);
			$this->db->group_by('convert(varchar(20), s.fecha_stock, 102)', FALSE);
			$this->db->order_by('fecha_stock');
		}

		// tipo de almacen
		if ($filtrar['sel_tiposalm'] == 'sel_tiposalm')
		{
			if (in_array('tipo_alm', $mostrar))
			{
				$this->db->select('t.tipo as tipo_almacen');
				$this->db->group_by('t.tipo');
				$this->db->order_by('t.tipo');
			}

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
			$this->db->join($this->config->item('bd_catalogos') . ' m', '(s.material collate Latin1_General_CI_AS = m.catalogo collate Latin1_General_CI_AS)', 'left', FALSE);
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
		if ($filtrar['sel_tiposalm'] == 'sel_tiposalm')
		{
			$this->db->from($this->config->item('bd_stock_fija') . ' s');
			$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 's.almacen=ta.cod_almacen and s.centro=ta.centro');
			$this->db->join($this->config->item('bd_tiposalm_sap') . ' t', 't.id_tipo=ta.id_tipo');
			$this->db->join($this->config->item('bd_almacenes_sap') . ' a', 'a.centro=ta.centro and a.cod_almacen=ta.cod_almacen', 'left');
		}
		else
		{
			$this->db->from($this->config->item('bd_stock_fija') . ' s');
			$this->db->join($this->config->item('bd_almacenes_sap') . ' a', 'a.centro=s.centro and a.cod_almacen=s.almacen', 'left');
		}

		// condiciones
		// fechas
		if (array_key_exists('fecha', $filtrar))
		{
			$this->db->where_in('s.fecha_stock', $filtrar['fecha']);
		}

		// tipos de almacen
		if ($filtrar['sel_tiposalm'] == 'sel_tiposalm')
		{
			if (array_key_exists('tipo_alm', $filtrar))
			{
				$this->db->where_in('t.id_tipo', $filtrar['tipo_alm']);
			}
		}
		else
		{
			$this->db->where_in("s.centro+'-'+s.almacen", $filtrar['almacenes']);
		}

		// tipos de articulo
		/*
		if (array_key_exists('tipo_articulo', $filtrar))
		{
			$this->db->where_in('tipo_articulo', $filtrar['tipo_articulo']);
		}
		*/

		$arr_result = $this->db->get()->result_array();
		//print_r($arr_result);


		return $arr_result;
	}


	// --------------------------------------------------------------------

	public function get_stock_transito($tipo_op = '', $mostrar = array(), $filtrar = array())
	{
		if ($tipo_op == 'MOVIL')
		{
			return $this->_get_stock_transito_movil($mostrar, $filtrar);
		}
		else
		{
			return $this->_get_stock_transito_fijo($mostrar, $filtrar);
		}
	}


	// --------------------------------------------------------------------

	public function _get_stock_transito_fijo($mostrar = array(), $filtrar = array())
	{
		$arr_result = array();

		if (array_key_exists('fecha', $filtrar) AND count($filtrar['fecha'])>0)
		{
			// fecha stock
			if (in_array('fecha', $mostrar))
			{
				$this->db->select('convert(varchar(20), s.fecha_stock, 102) as fecha_stock', FALSE);
				$this->db->group_by('convert(varchar(20), s.fecha_stock, 102)', FALSE);
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
				$this->db->select('s.estado, s.acreedor, p.des_proveedor');
				$this->db->join($this->config->item('bd_proveedores') . ' p', 'p.cod_proveedor=s.acreedor', 'left');
				$this->db->group_by('s.estado, s.acreedor, p.des_proveedor');
				$this->db->order_by('s.estado, s.acreedor');
			}

			// materiales
			if (in_array('material', $mostrar))
			{
				$this->db->select('s.material, m.desc_material, s.umb');
				$this->db->join($this->config->item('bd_catalogos') . ' m', 's.material=m.catalogo', 'left');
				$this->db->group_by('s.material, m.desc_material, s.umb');
				$this->db->order_by('s.material, m.desc_material, s.umb');
			}

			// lotes
			if (in_array('lote', $mostrar))
			{
				$this->db->select('s.lote');
				$this->db->group_by('s.lote');
			}

			$this->db->select('sum(s.cantidad) as cantidad, sum(s.valor) as VAL_cantidad', FALSE);

			// tablas
			$this->db->from($this->config->item('bd_stock_fija') . ' s');

			// condiciones
			// fechas
			if (array_key_exists('fecha', $filtrar))
			{
				$this->db->where_in('s.fecha_stock', $filtrar['fecha']);
			}

			$this->db->where('s.almacen is null');


			$arr_result = $this->db->get()->result_array();
			//print_r($arr_result);
		}

		return $arr_result;
	}


	// --------------------------------------------------------------------

	public function get_combo_fechas($tipo_op = '')
	{
		$arr_fecha_tmp = ($tipo_op == 'MOVIL') ? $this->_get_combo_fechas_movil() : $this->_get_combo_fechas_fija();
		$arr_fecha = array('ultimodia' => array(), 'todas' => $arr_fecha_tmp);

		$año_mes_ant = '';
		foreach($arr_fecha_tmp as $key => $val)
		{
			if ($año_mes_ant != substr($key, 0, 6))
			{
				$arr_fecha['ultimodia'][$key] = $val;
			}

			$año_mes_ant = substr($key, 0, 6);
		}

		return $arr_fecha;
	}


	// --------------------------------------------------------------------

	private function _get_combo_fechas_movil()
	{
		$arr_result = $this->db
			->select('convert(varchar(20), fecha_stock, 112) as llave', FALSE)
			->select('convert(varchar(20), fecha_stock, 102) as valor', FALSE)
			->order_by('llave','desc')
			->get($this->config->item('bd_stock_movil_fechas'))
			->result_array();

		return form_array_format($arr_result);
	}


	// --------------------------------------------------------------------

	private function _get_combo_fechas_fija()
	{
		$arr_result = $this->db
			->select('convert(varchar(20), fecha_stock, 112) as llave', FALSE)
			->select('convert(varchar(20), fecha_stock, 102) as valor', FALSE)
			->order_by('llave','desc')
			->get($this->config->item('bd_stock_fija_fechas'))
			->result_array();

		return form_array_format($arr_result);
	}


	// --------------------------------------------------------------------

	public function get_detalle_series($centro = '', $almacen = '', $material = '', $lote = '')
	{
		if ($almacen != '' && $almacen != '_')
		{
			if ($centro   != '' && $centro   != '_') $this->db->where('s.centro', $centro);
			if ($almacen  != '' && $almacen  != '_') $this->db->where('s.almacen', $almacen);
			if ($material != '' && $material != '_') $this->db->where('s.material', $material);
			if ($lote     != '' && $lote     != '_') $this->db->where('s.lote', $lote);

			$this->db->select('convert(varchar(20), s.fecha_stock, 103) as fecha_stock, s.centro, s.almacen, a.des_almacen, s.material, s.des_material, s.lote, s.serie, s.estado_stock, convert(varchar(20), s.modificado_el, 103) as fecha_modificacion, s.modificado_por, u.nom_usuario, p.pmp');

			$this->db->from($this->config->item('bd_stock_seriado_sap') . ' as s');
			$this->db->join($this->config->item('bd_almacenes_sap') . ' as a', 'a.centro=s.centro and a.cod_almacen=s.almacen', 'left');
			$this->db->join($this->config->item('bd_usuarios_sap') . ' as u', 'u.usuario=s.modificado_por', 'left');
			$this->db->join($this->config->item('bd_pmp') . ' p', "p.centro = s.centro and p.material=s.material and p.lote=s.lote and p.estado_stock=s.estado_stock",'left');
			$this->db->order_by('material, lote, serie');

			return $this->db->get()->result_array();
		}
		else
		{
			return array();
		}
	}


	public function reporte_clasificacion($tipo_op = 'MOVIL', $fechas = null, $borrar_datos = FALSE)
	{

		$arr_result = array();

		if ($fechas)
		{
			foreach($fechas as $fecha)
			{
				if ($borrar_datos)
				{
					$this->db
						->where('tipo_op', $tipo_op)
						->where('fecha_stock', $fecha)
						->delete($this->config->item('bd_reporte_clasif'));
				}

				$result = $this->db
					->select('tipo_op, convert(varchar(20), fecha_stock, 102) fecha_stock, orden, clasificacion, tipo, color, cantidad, monto', FALSE)
					->from($this->config->item('bd_reporte_clasif'))
					->where('tipo_op', $tipo_op)
					->where('fecha_stock', $fecha)
					->order_by('tipo_op, fecha_stock, orden')
					->get()->result_array();

				if (count($result) == 0)
				{
					$this->_genera_reporte_clasificacion($tipo_op, $fecha);

					array_push($arr_result, $this->reporte_clasificacion($tipo_op, $fecha));
				}
				else
				{
					array_push($arr_result, $result);
				}
			}
		}

		// agrega registros en arreglo final
		$arr_final = array();
		$arr_fechas = array();
		foreach($arr_result as $result_fecha)
		{
			foreach ($result_fecha as $reg)
			{
				if (! in_array($reg['fecha_stock'], $arr_fechas))
				{
					array_push($arr_fechas, $reg['fecha_stock']);
				}

				if (! array_key_exists($reg['orden'], $arr_final))
				{
					$arr_final[$reg['orden']] = array();
				}

				$arr_final[$reg['orden']]['tipo_op'] = $reg['tipo_op'];
				$arr_final[$reg['orden']]['orden'] = $reg['orden'];
				$arr_final[$reg['orden']]['clasificacion'] = $reg['clasificacion'];
				$arr_final[$reg['orden']]['tipo'] = $reg['tipo'];
				$arr_final[$reg['orden']]['color'] = $reg['color'];
				$arr_final[$reg['orden']][$reg['fecha_stock']] = $reg['monto'];

			}
		}

		// corrige arreglo final, agregando valores =0 en aquellas llaves de fecha que no existan
		foreach($arr_final as $key => $reg)
		{
			foreach($arr_fechas as $fec)
			{
				if (! array_key_exists($fec, $reg))
				{
					$arr_final[$key][$fec] = 0;
				}
			}
		}

		asort($arr_fechas);

		return array('datos' => $arr_final, 'fechas' => $arr_fechas);
	}


	private function _genera_reporte_clasificacion($tipo_op = null, $fecha = null)
	{
		if ($tipo_op == "FIJA")
		{
			$sql = "INSERT INTO " . $this->config->item('bd_reporte_clasif') .
" SELECT
A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
SUM(E.CANTIDAD) AS CANTIDAD, SUM(E.VALOR) AS MONTO
FROM " . $this->config->item('bd_clasifalm_sap') . " A
JOIN " . $this->config->item('bd_clasif_tipoalm_sap') . " B ON A.ID_CLASIF=B.ID_CLASIF
JOIN " . $this->config->item('bd_tiposalm_sap')       . " C ON B.ID_TIPO=C.ID_TIPO
JOIN " . $this->config->item('bd_tipoalmacen_sap')    . " D ON C.ID_TIPO=D.ID_TIPO
JOIN " . $this->config->item('bd_stock_fija')         . " E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.ALMACEN
JOIN " . $this->config->item('bd_tipo_clasifalm_sap') . " F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
WHERE A.TIPO_OP = ?
AND E.FECHA_STOCK = ?
GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR
ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN";
		}
		else if ($tipo_op == "MOVIL")
		{
			$sql = "INSERT INTO " . $this->config->item('bd_reporte_clasif') .
" SELECT
A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
SUM(E.LIBRE_UTILIZACION + E.BLOQUEADO + E.CONTRO_CALIDAD + E.TRANSITO_TRASLADO + E.OTROS) AS CANTIDAD,
SUM(E.VAL_LU + E.VAL_BQ + E.VAL_CQ + E.VAL_TT + E.VAL_OT) AS MONTO
FROM " . $this->config->item('bd_clasifalm_sap')      . " A
JOIN " . $this->config->item('bd_clasif_tipoalm_sap') . " B ON A.ID_CLASIF=B.ID_CLASIF
JOIN " . $this->config->item('bd_tiposalm_sap')       . " C ON B.ID_TIPO=C.ID_TIPO
JOIN " . $this->config->item('bd_tipoalmacen_sap')    . " D ON C.ID_TIPO=D.ID_TIPO
JOIN " . $this->config->item('bd_stock_movil')        . " E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.COD_BODEGA
JOIN " . $this->config->item('bd_tipo_clasifalm_sap') . " F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
WHERE A.TIPO_OP= ?
AND E.FECHA_STOCK = ?
GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION, F.TIPO, F.COLOR
ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION";
		}

		return $this->db->query($sql, array($tipo_op, $fecha));

	}

/*
actualiza PMP
=================================
UPDATE S
SET
	S.VAL_LU = P.PMP_01*S.LIBRE_UTILIZACION,
	S.VAL_BQ = P.PMP_07*S.BLOQUEADO,
	S.VAL_CQ = P.PMP_02*S.CONTRO_CALIDAD,
	S.VAL_TT = P.PMP_06*S.TRANSITO_TRASLADO,
	S.VAL_OT = 0
FROM
STOCK_SCL S
JOIN CP_PMP P ON S.CENTRO=P.CENTRO AND S.COD_ARTICULO=P.MATERIAL AND S.LOTE=P.LOTE
WHERE S.FECHA_STOCK='20150331'




SELECT *
FROM
STOCK_SCL S
JOIN CP_PMP P ON S.CENTRO=P.CENTRO AND S.COD_ARTICULO=P.MATERIAL AND S.LOTE=P.LOTE
WHERE S.FECHA_STOCK='20140930'




REPORTE CLASIFICACION MOVIL
=========================================
SELECT
A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
SUM(E.LIBRE_UTILIZACION + E.BLOQUEADO + E.CONTRO_CALIDAD + E.TRANSITO_TRASLADO + E.OTROS) AS CANTIDAD,
SUM(E.VAL_LU + E.VAL_BQ + E.VAL_CQ + E.VAL_TT + E.VAL_OT) AS MONTO
FROM CP_CLASIFALM A
JOIN CP_CLASIF_TIPOALM B ON A.ID_CLASIF=B.ID_CLASIF
JOIN CP_TIPOSALM C ON B.ID_TIPO=C.ID_TIPO
JOIN CP_TIPOS_ALMACENES D ON C.ID_TIPO=D.ID_TIPO
JOIN STOCK_SCL E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.COD_BODEGA
JOIN CP_TIPO_CLASIFALM F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
WHERE A.TIPO_OP= ?
AND E.FECHA_STOCK = ?
GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION, F.TIPO, F.COLOR
ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION



SERIES ESTADO 01
=========================================
SELECT
A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
SUM(E.LIBRE_UTILIZACION + E.BLOQUEADO + E.CONTRO_CALIDAD + E.TRANSITO_TRASLADO + E.OTROS) AS CANTIDAD,
SUM(E.VAL_LU + E.VAL_BQ + E.VAL_CQ + E.VAL_TT + E.VAL_OT) AS MONTO,
SUM(E.LIBRE_UTILIZACION) AS CANTIDAD_01,
SUM(E.VAL_LU) AS MONTO_01
FROM CP_CLASIFALM A
JOIN CP_CLASIF_TIPOALM B ON A.ID_CLASIF=B.ID_CLASIF
JOIN CP_TIPOSALM C ON B.ID_TIPO=C.ID_TIPO
JOIN CP_TIPOS_ALMACENES D ON C.ID_TIPO=D.ID_TIPO
JOIN STOCK_SCL E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.COD_BODEGA
JOIN CP_TIPO_CLASIFALM F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
WHERE A.TIPO_OP= 'MOVIL'
AND E.FECHA_STOCK IN ('20150429','20150331','20150228','20150131',
'20141231','20141130','20141031','20140930','20140831','20140731','20140630','20140531','20140430','20140331','20140228','20140130')
AND A.CLASIFICACION IN ('SUC', 'GMC APM WEB', 'CANALES REMOTOS', 'FQ')
GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION, F.TIPO, F.COLOR
ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION

*/


}
/* End of file stock_sap_model.php */
/* Location: ./application/models/stock_sap_model.php */