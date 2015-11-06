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
class stock_sap_model extends CI_Model {

	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('html');
		$this->load->library('table');
		$this->table->set_template(array(
			'table_open' => '<table class="table table-striped table-hover table-condensed reporte">',
		));
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock fijo o movil
	 *
	 * @param  string $tipo_op Indicador del tipo de operación
	 * @param  array  $mostrar Arreglo con campos a mostrar
	 * @param  array  $filtrar Arreglo con campos a filtrar
	 * @return array           Arreglo con stock
	 */
	public function get_stock($tipo_op = '', $mostrar = array(), $filtrar = array())
	{
		if ($tipo_op === 'MOVIL')
		{
			return $this->_get_stock_movil($mostrar, $filtrar);
		}
		else
		{
			return $this->_get_stock_fijo($mostrar, $filtrar);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock operación movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con campos a filtrar
	 * @return array          Arreglo con stock
	 */
	private function _get_stock_movil($mostrar = array(), $filtrar = array())
	{
		$arr_result = array();

		// fecha stock
		if (in_array('fecha', $mostrar))
		{
			if (in_array('material', $mostrar))
			{
				$this->db->select('convert(varchar(20), s.fecha_stock, 102) as fecha_stock', FALSE);
				$this->db->group_by('convert(varchar(20), s.fecha_stock, 102)', FALSE);
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
				$this->db->join($this->config->item('bd_pmp') . ' p', "p.centro = s.centro and p.material=s.cod_articulo and p.lote=s.lote and p.estado_stock='01'", 'left');
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

		$arr_stock_tmp01 = array();
		// si tenemos tipos de articulo y no tenemos el detalle de estados de stock,
		// entonces hacemos columnas con los totales de tipos_articulo (equipos, simcards y otros)
		if (in_array('tipo_articulo', $mostrar) AND  ! in_array('tipo_stock', $mostrar))
		{
			foreach($arr_result as $registro)
			{
				$arr_reg = array();
				$llave_total = '';
				$valor_total = 0;
				$valor_monto = 0;

				foreach ($registro as $campo_key => $campo_val)
				{
					if ($campo_key === 'tipo_articulo')
					{
						$llave_total = $campo_val;
					}
					else if ($campo_key === 'total')
					{
						$valor_total = $campo_val;
					}
					else if ($campo_key === 'VAL_total')
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

			foreach($arr_stock_tmp01 as $registro)
			{
				$i += 1;
				$llave_act = '';
				$llave_total = '';
				$llave_monto = '';
				$valor_total = 0;
				$valor_monto = 0;

				// determina la llave actual y el valor del campo total
				$arr_tmp = array();
				foreach ($registro as $campo_key => $campo_val)
				{
					if ($campo_key === 'EQUIPOS' OR $campo_key === 'OTROS' OR $campo_key === 'SIMCARD')
					{
						$llave_total = $campo_key;
						$valor_total = $campo_val;
					}
					else if ($campo_key === 'VAL_EQUIPOS' OR $campo_key === 'VAL_OTROS' OR $campo_key === 'VAL_SIMCARD')
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

		return $this->_format_table_stock($arr_result, $mostrar, $filtrar);
	}


	// --------------------------------------------------------------------

	public function _format_table_stock($arr_result = array(), $mostrar = array(), $filtrar = array())
	{
		$campos_sumables = array('LU','BQ','CC','TT','OT','total','EQUIPOS','SIMCARD','OTROS','cantidad','VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','VAL_total','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS', 'VAL_cantidad', 'monto');
		$campos_montos   = array('VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','VAL_total','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS','VAL_cantidad', 'monto');
		$totales = array();

		$num_reg = 0;

		foreach ($arr_result as $linea_stock)
		{
			if ($num_reg === 0)
			{
				$arr_linea = array();
				foreach ($linea_stock as $campo => $valor)
				{
					array_push($arr_linea, array(
						'data'  => str_replace('_', ' ', $campo),
						'class' => in_array($campo, $campos_sumables) ? 'text-right' : '',
					));

					$totales[$campo] = 0;
				}
				$this->table->set_heading($arr_linea);
			}

			$arr_linea = array();
			foreach ($linea_stock as $campo => $valor)
			{
				if (in_array($campo, $campos_sumables))
				{
					$str_url = base_url(
						'stock_sap/detalle_series/' .
						(array_key_exists('centro', $linea_stock) ? $linea_stock['centro'] : '_') . '/' .
						(array_key_exists('cod_almacen', $linea_stock) ? $linea_stock['cod_almacen'] : '_') . '/' .
						(array_key_exists('cod_articulo', $linea_stock) ? $linea_stock['cod_articulo'] : '_') . '/' .
						(array_key_exists('lote', $linea_stock) ? $linea_stock['lote'] : '_')
					);

					$data = anchor($str_url, nbs().(in_array($campo, $campos_montos) ? fmt_monto($valor) : fmt_cantidad($valor)));

					$totales[$campo] += $valor;
				}
				else
				{
					$data = $valor;
				}

				array_push($arr_linea, array(
					'data'  => $data,
					'class' => in_array($campo, $campos_sumables) ? 'text-right' : '',
				));
			}
			$this->table->add_row($arr_linea);
			$num_reg += 1;
		}

		$arr_linea = array();
		foreach($totales as $campo => $valor)
		{
			$data = '';

			if (in_array($campo, $campos_sumables))
			{
				$data = in_array($campo, $campos_montos) ? fmt_monto($valor) : fmt_cantidad($valor);
			}

			array_push($arr_linea, array(
				'data'  => '<strong>'.$data.'</strong>',
				'class' => in_array($campo, $campos_sumables) ? 'text-right' : '',
			));
		}
		$this->table->add_row($arr_linea);

		return $this->table->generate();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera stock operación fija
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con campos a filtrar
	 * @return array          Arreglo con stock
	 */
	private function _get_stock_fijo($mostrar = array(), $filtrar = array())
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
		if ($filtrar['sel_tiposalm'] === 'sel_tiposalm')
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

		$arr_result = $this->db->get()->result_array();

		return $this->_format_table_stock($arr_result, $mostrar, $filtrar);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock en transito, operación fija y/o movil
	 *
	 * @param  string $tipo_op Tipo de operación (fija o móvil)
	 * @param  array  $mostrar Arreglo con campos a mostrar
	 * @param  array  $filtrar Arreglo con filtros a aplicar
	 * @return array           Stock en transito
	 */
	public function get_stock_transito($tipo_op = '', $mostrar = array(), $filtrar = array())
	{
		if ($tipo_op === 'MOVIL')
		{
			return $this->_get_stock_transito_movil($mostrar, $filtrar);
		}
		else
		{
			return $this->_get_stock_transito_fijo($mostrar, $filtrar);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock en transito, operación fija y/o movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con filtros a aplicar
	 * @return array           Stock en transito
	 */
	private function _get_stock_transito_fijo($mostrar = array(), $filtrar = array())
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
		}

		return $arr_result;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera fechas para mostrar en combobox
	 *
	 * @param  string $tipo_op Indicador del tipo de operación
	 * @return array           Fechas
	 */
	public function get_combo_fechas($tipo_op = '')
	{
		$arr_fecha_tmp = ($tipo_op === 'MOVIL') ? $this->_get_combo_fechas_movil() : $this->_get_combo_fechas_fija();
		$arr_fecha = array('ultimodia' => array(), 'todas' => $arr_fecha_tmp);

		$anno_mes_ant = '';
		foreach($arr_fecha_tmp as $llave => $valor)
		{
			if ($anno_mes_ant !== substr($llave, 0, 6))
			{
				$arr_fecha['ultimodia'][$llave] = $valor;
			}

			$anno_mes_ant = substr($llave, 0, 6);
		}

		return $arr_fecha;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera fechas de la operación movil para mostrar en combobox
	 *
	 * @return array Fechas
	 */
	private function _get_combo_fechas_movil()
	{
		$arr_result = $this->db
			->select('convert(varchar(20), fecha_stock, 112) as llave', FALSE)
			->select('convert(varchar(20), fecha_stock, 102) as valor', FALSE)
			->order_by('llave', 'desc')
			->get($this->config->item('bd_stock_movil_fechas'))
			->result_array();

		return form_array_format($arr_result);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera fechas de la operación fija para mostrar en combobox
	 *
	 * @return array Fechas
	 */
	private function _get_combo_fechas_fija()
	{
		$arr_result = $this->db
			->select('convert(varchar(20), fecha_stock, 112) as llave', FALSE)
			->select('convert(varchar(20), fecha_stock, 102) as valor', FALSE)
			->order_by('llave', 'desc')
			->get($this->config->item('bd_stock_fija_fechas'))
			->result_array();

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
	 * @return array            Arreglo con series recuperadas
	 */
	public function get_detalle_series($centro = '', $almacen = '', $material = '', $lote = '')
	{
		if ($almacen === '' OR $almacen === '_')
		{
			return array();
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
			->from($this->config->item('bd_stock_seriado_sap') . ' as s')
			->join($this->config->item('bd_almacenes_sap') . ' as a', 'a.centro=s.centro and a.cod_almacen=s.almacen', 'left')
			->join($this->config->item('bd_usuarios_sap') . ' as u', 'u.usuario=s.modificado_por', 'left')
			->join($this->config->item('bd_pmp') . ' p', 'p.centro = s.centro and p.material=s.material and p.lote=s.lote and p.estado_stock=s.estado_stock', 'left')
			->order_by('material, lote, serie');

		return $this->table->generate($this->db->get());
	}

	// --------------------------------------------------------------------

	/**
	 * Genera reporte de stock con clasificacion de almacens
	 *
	 * @param  string  $tipo_op      Tipo de operación (fijo o movil)
	 * @param  array   $fechas       Fechas a proceosar
	 * @param  boolean $borrar_datos Indicador si el reporte se generar desde cero o se recupera
	 * @return array                 Arreglo con el reporte
	 */
	public function reporte_clasificacion($tipo_op = 'MOVIL', $fechas = NULL, $borrar_datos = FALSE)
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

				if (count($result) === 0)
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
			foreach ($result_fecha as $registro)
			{
				if (! in_array($registro['fecha_stock'], $arr_fechas))
				{
					array_push($arr_fechas, $registro['fecha_stock']);
				}

				if (! array_key_exists($registro['orden'], $arr_final))
				{
					$arr_final[$registro['orden']] = array();
				}

				$arr_final[$registro['orden']]['tipo_op'] = $registro['tipo_op'];
				$arr_final[$registro['orden']]['orden'] = $registro['orden'];
				$arr_final[$registro['orden']]['clasificacion'] = $registro['clasificacion'];
				$arr_final[$registro['orden']]['tipo'] = $registro['tipo'];
				$arr_final[$registro['orden']]['color'] = $registro['color'];
				$arr_final[$registro['orden']][$registro['fecha_stock']] = $registro['monto'];

			}
		}

		// corrige arreglo final, agregando valores =0 en aquellas llaves de fecha que no existan
		foreach($arr_final as $llave => $registro)
		{
			foreach($arr_fechas as $fecha)
			{
				if ( ! array_key_exists($fecha, $registro))
				{
					$arr_final[$llave][$fecha] = 0;
				}
			}
		}

		asort($arr_fechas);

		return array('datos' => $arr_final, 'fechas' => $arr_fechas);
	}

	// --------------------------------------------------------------------

	/**
	 * Inserta registros del reporte de stock por clasificacion en tabla resumen
	 *
	 * @param  string $tipo_op Indicador de operación (fijo o movil)
	 * @param  string $fecha   Fecha del reporte a generar
	 * @return boolean         Indicador de exito o fallo de la query
	 */
	private function _genera_reporte_clasificacion($tipo_op = NULL, $fecha = NULL)
	{
		if ($tipo_op === 'FIJA')
		{
			$sql_query = 'INSERT INTO ' . $this->config->item('bd_reporte_clasif');
			$sql_query .= " SELECT
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
		else if ($tipo_op === "MOVIL")
		{
			$sql_query = "INSERT INTO " . $this->config->item('bd_reporte_clasif') .
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

		return $this->db->query($sql_query, array($tipo_op, $fecha));

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