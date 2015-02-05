<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportestock_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos para poblar combobox de tipos de inventario
	 * @return array Arreglo con los tipos de inventiaro
	 */
	public function get_fecha_reporte()
	{
		$this->db->distinct();
		$this->db->select('convert(varchar(20), fecha_stock, 102) as fecha_stock', FALSE);
		$arr_result = $this->db->get($this->config->item('bd_permanencia') . ' as p')->row_array();
		if (count($arr_result) > 0)
		{
			return $arr_result['fecha_stock'];
		}
		else
		{
			return '';
		}
	}


	// --------------------------------------------------------------------

	public function combo_tipo_alm($tipo_op = '')
	{
		$arr_rs = $this->db
			->distinct()
			->select('t.id_tipo, t.tipo')
			->from($this->config->item('bd_tiposalm_sap') . ' t')
			->where('t.tipo_op', $tipo_op)
			->order_by('t.tipo')
			->get()
			->result_array();

		$arr_combo = array();
		foreach($arr_rs as $reg)
		{
			$arr_combo[$reg['id_tipo']] = $reg['tipo'];
		}

		return $arr_combo;
	}


	// --------------------------------------------------------------------

	public function combo_estado_sap()
	{
		return array(
			'01' => '01 Libre Utilizacion',
			'02' => '02 Control de Calidad',
			'03' => '03 Devolucion Cliente',
			'06' => '06 Transito',
			'07' => '07 Bloqueado',
		);
	}


	// --------------------------------------------------------------------

	public function combo_tipo_alm_consumo()
	{
		$arr_rs = $this->db
			->distinct()
			->select('t.id_tipo, t.tipo')
			->from($this->config->item('bd_tipoalmacen_sap') . ' ta')
			->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left')
			->like('t.tipo', '(CONSUMO)')
			->order_by('t.tipo')
			->get()
			->result_array();

		$arr_combo = array();
		foreach($arr_rs as $reg)
		{
			$arr_combo[$reg['id_tipo']] = $reg['tipo'];
		}

		return $arr_combo;
	}


	// --------------------------------------------------------------------

	public function combo_tipo_mat()
	{
		return array(
				'EQUIPO'  => 'Equipos',
				'SIMCARD' => 'Simcard',
				'FIJA'    => 'Fija',
				'OTRO'    => 'Otros',
			);
	}

	// =====================================================================================================
	// REPORTES
	// =====================================================================================================


	/**
	 * Devuelve reporte por hojas
	 * @param  array $config Arreglo de configuración
	 * @return array         Arreglo con el detalle del reporte
	 */
	public function get_reporte_permanencia($config = array())
	{
		//dbg($config);
		$arr_reporte = array();
		$param_ok = $config['filtros']['tipo_alm'];

		if ($param_ok)
		{
			$this->db
				->select('t.tipo, t.id_tipo')
				->select('sum(m030) as m030')
				->select('sum(m060) as m060')
				->select('sum(m090) as m090')
				->select('sum(m120) as m120')
				->select('sum(m180) as m180')
				->select('sum(m360) as m360')
				->select('sum(m720) as m720')
				->select('sum(mas720) as mas720')
				->select('sum(otro) as otro')
				->select('sum(total) as \'total\'')
				->from($this->config->item('bd_permanencia') . ' as p')
				->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'ta.centro=p.centro and ta.cod_almacen=p.almacen', 'left')
				->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left')
				->group_by('t.tipo, t.id_tipo')
				->order_by($config['orden']['campo'], $config['orden']['tipo']);


			// FILTROS
			if (count($config['filtros']['tipo_alm']) > 0)
			{
				$this->db->where_in('t.id_tipo', $config['filtros']['tipo_alm']);
			}

			if (count($config['filtros']['estado_sap']) > 0 and is_array($config['filtros']['estado_sap']))
			{
				$this->db
					->where_in('p.estado_stock', $config['filtros']['estado_sap'])
					->select('case p.estado_stock when \'01\' then \'01 Libre util\' when \'02\' then \'02 Control Calidad\' when \'03\' then \'03 Devol cliente\' when \'07\' then \'07 Bloqueado\' when \'06\' then \'06 Transito\' else p.estado_stock end as estado_sap, p.estado_stock')
					->group_by('p.estado_stock');
			}

			if (count($config['filtros']['tipo_mat']) > 0 and is_array($config['filtros']['tipo_mat']))
			{
				$this->db
					->where_in('tipo_material', $config['filtros']['tipo_mat'])
					->select('tipo_material')
					->group_by('tipo_material');
			}

			// INFORMACION ADICIONAL
			if ($config['mostrar']['almacen'] == '1')
			{
				$this->db
					->select('p.centro, p.almacen, p.des_almacen')
					->group_by('p.centro, p.almacen, p.des_almacen');
			}

			if ($config['mostrar']['lote'] == '1')
			{
				$this->db
					->select('p.lote')
					->group_by('p.lote');
			}

			if ($config['mostrar']['modelos'] == '1')
			{
				$this->db
					->select('p.material')
					->select('p.material + \' \' + p.des_material as modelo')
					->group_by('p.material + \' \' + p.des_material')
					->group_by('p.material');
			}

			$arr_reporte = $this->db->get()->result_array();
		}

		return $arr_reporte;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por hojas
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_perm_consumo($orden_campo = 't.tipo', $orden_tipo = 'ASC', $tipo_alm = array(), $estado_sap = array(), $incl_almacen = '0', $incl_lote = '0', $incl_estado = '0', $incl_modelos = '0')
	{

		$this->db->select('t.tipo');
		$this->db->select('count(case when s.dias <= 30                 then 1 else null end) as m030');
		$this->db->select('count(case when s.dias > 30 and s.dias<= 60  then 1 else null end) as m060');
		$this->db->select('count(case when s.dias > 60 and s.dias<= 90  then 1 else null end) as m090');
		$this->db->select('count(case when s.dias > 90 and s.dias<= 180 then 1 else null end) as m180');
		$this->db->select('count(case when s.dias > 180 then 1 else null end) as mas180');
		$this->db->select('count(1) as \'total\'');
		$this->db->from($this->config->item('bd_permanencia_fija') . ' s');
		$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'ta.centro=s.centro and ta.cod_almacen=s.almacen', 'left');
		$this->db->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left');
		$this->db->group_by('t.tipo');
		$this->db->order_by($orden_campo, $orden_tipo);

		if (count($tipo_alm) > 0)
		{
			$this->db->where_in('t.id_tipo', $tipo_alm);
		}
		else
		{
			$this->db->where('t.id_tipo', -1);
		}

		if ($incl_almacen == '1')
		{
			$this->db->select('s.centro, s.almacen, a.des_almacen');
			$this->db->join($this->config->item('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.almacen=a.cod_almacen', 'left');
			$this->db->group_by('s.centro, s.almacen, a.des_almacen');
			$this->db->order_by('s.centro, s.almacen, a.des_almacen');
		}

		if ($incl_lote == '1')
		{
			$this->db->select('s.lote');
			$this->db->group_by('s.lote');
			$this->db->order_by('s.lote');
		}

		if ($incl_modelos == '1')
		{
			$this->db->select('s.material');
			$this->db->select('s.des_material');
			$this->db->group_by('s.material');
			$this->db->group_by('s.des_material');
			$this->db->order_by('s.material');
		}

		return $this->db->get()->result_array();
	}

	public function get_detalle_series($tipo_alm = '', $centro = '', $almacen = '', $estado_stock = '', $lote = '', $material = '', $tipo_material = '', $permanencia = '')
	{
		$this->db->select('t.tipo');
		$this->db->select('s.fecha_stock');
		$this->db->select('s.centro');
		$this->db->select('s.almacen');
		$this->db->select('a.des_almacen');
		$this->db->select('s.material');
		$this->db->select('s.des_material');
		$this->db->select('s.lote');
		$this->db->select('s.estado_stock');
		$this->db->select('s.modificado_el');
		$this->db->select('s.serie');
		$this->db->select('0 as pmp');
		$this->db->select('s.modificado_el as fecha_modificacion');
		$this->db->select('s.modificado_por');
		$this->db->select('u.nom_usuario');

		$this->db->from($this->config->item('bd_stock_seriado_sap') . ' s');
		$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'ta.centro=s.centro and ta.cod_almacen=s.almacen', 'left');
		$this->db->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left');
		$this->db->join($this->config->item('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.almacen=a.cod_almacen', 'left');
		$this->db->join($this->config->item('bd_usuarios_sap') . ' u', 'u.usuario=s.modificado_por', 'left');
		$this->db->order_by('t.tipo');
		$this->db->order_by('s.centro');
		$this->db->order_by('s.almacen');
		$this->db->order_by('s.material');

		if ($tipo_alm != '')
		{
			$this->db->where('t.id_tipo', $tipo_alm);
		}

		if ($centro != '')
		{
			$this->db->where('s.centro', $centro);
		}

		if ($almacen != '')
		{
			$this->db->where('s.almacen', $almacen);
		}

		if ($estado_stock != '')
		{
			$this->db->where('s.estado_stock', $estado_stock);
		}

		if ($lote != '')
		{
			$this->db->where('s.lote', $lote);
		}

		if ($material != '')
		{
			$this->db->where('s.material', $material);
		}

		if ($tipo_material == 'EQUIPO')
		{
			$this->db->where_in('substring(s.material,1,2)', array('TM', 'TC', 'TO', 'PK', 'PO'));
			$this->db->where('substring(s.material,1,8) <>', 'PKGCLOTK');
		}
		elseif ($tipo_material == 'SIMCARD')
		{
			$this->db->where('(substring(s.material,1,2) = \'TS\' OR substring(s.material,1,8)=\'PKGCLOTK\')');
		}
		elseif ($tipo_material == 'FIJA')
		{
			$this->db->where('substring(s.material,1,3)', '103');
		}

		if ($permanencia == 'm030')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 30);
		}
		elseif ($permanencia == 'm060')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 31);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 60);
		}
		elseif ($permanencia == 'm090')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 61);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 90);
		}
		elseif ($permanencia == 'm120')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 91);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 120);
		}
		elseif ($permanencia == 'm180')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 121);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 180);
		}
		elseif ($permanencia == 'm360')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 181);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 360);
		}
		elseif ($permanencia == 'm720')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 361);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 720);
		}
		elseif ($permanencia == 'mas720')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 721);
		}
		elseif ($permanencia == 'otro')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) IS NULL');
		}
		elseif ($permanencia == 'total')
		{
		}

		return $this->db->get()->result_array();

	}


	// --------------------------------------------------------------------

	public function get_treemap_permanencia($centro = 'CL03', $tipo_material = 'EQUIPO')
	{
		return $this->db
			->select('t.tipo')
			->select('s.almacen + \' - \' + s.des_almacen as alm', FALSE)
			->select('substring(s.material,6,2) + \' (\' + s.almacen + \')\' as marca', FALSE)
			->select('substring(s.material,6,7) + \' (\' + s.almacen + \')\' as modelo', FALSE)
			->select_sum('s.total', 'cant')
			->select_sum('convert(FLOAT, s.total)*(case when v.pmp IS NULL then 0 else v.pmp END)/1000000', 'valor', FALSE)
			->select_sum('s.m030*15+s.m060*45+s.m090*75+s.m120*115+s.m180*150+s.m360*270+s.m720*540+s.mas720*720', 'perm', FALSE)
			->from($this->config->item('bd_permanencia') . ' as s')
			->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'ta.centro=s.centro and ta.cod_almacen=s.almacen', 'left')
			->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left')
			->join($this->config->item('bd_pmp') . ' v', 's.centro=v.centro and s.material=v.material and s.lote=v.lote and s.estado_stock=v.estado_stock', 'left')
			->where('s.centro', $centro)
			->where('s.almacen is not NULL')
			->where('s.tipo_material', $tipo_material)
			->group_by('t.tipo')
			->group_by('s.almacen + \' - \' + s.des_almacen')
			->group_by('substring(s.material,6,2) + \' (\' + s.almacen + \')\'')
			->group_by('substring(s.material,6,7) + \' (\' + s.almacen + \')\'')
			->order_by('t.tipo')
			->order_by('s.almacen + \' - \' + s.des_almacen')
			->order_by('substring(s.material,6,2) + \' (\' + s.almacen + \')\'')
			->order_by('substring(s.material,6,7) + \' (\' + s.almacen + \')\'')
			->get()->result_array();
	}


	// --------------------------------------------------------------------

	public function arr_query2treemap($tipo = 'cantidad', $arr = array(), $arr_nodos = array(), $campo_size = '', $campo_color = '')
	{
		$arr_final = array();

		$tipo = ($tipo == 'cantidad') ? 'cantidad' : 'valor';

		if(count($arr) > 0 AND count($arr_nodos) > 0)
		{
			$campo_item = array_pop($arr_nodos);
			$campo_padre = array_pop($arr_nodos);

			foreach($arr as $v)
			{
				array_push($arr_final, array("'".$v[$campo_item]."'", "'".$v[$campo_padre]."'", (int) $v[$campo_size], $v[$campo_color]));
			}

			$campo_item = $campo_padre;

			while (count($arr_nodos) > 0)
			{
				$campo_padre = array_pop($arr_nodos);
				$arr_tmp = array();
				$arr_tmp2 = $arr_final;

				foreach($arr as $v)
				{
					$arr_v = array("'".$v[$campo_item]."'", "'".$v[$campo_padre]."'", 0, 0);

					if(!in_array($arr_v, $arr_tmp))
					{
						array_push($arr_tmp, $arr_v);
					}
				}

				$arr_final = array();
				$arr_final = array_merge($arr_tmp, $arr_tmp2);
				$campo_item = $campo_padre;
			}

			$campo_padre = "'TOTAL'";
			$arr_tmp = array();
			$arr_tmp2 = $arr_final;
			foreach($arr as $v)
			{
				$arr_v = array("'".$v[$campo_item]."'", $campo_padre, 0, 0);
				if(!in_array($arr_v, $arr_tmp))
				{
					array_push($arr_tmp, $arr_v);
				}
			}
			$arr_final = array();
			$arr_tmp3 = array(array($campo_padre, 'null', 0, 0));
			$arr_final = array_merge($arr_tmp3, $arr_tmp, $arr_tmp2);

			$salida = "['Item','Padre','$tipo','Antiguedad'],\n";
			foreach($arr_final as $v)
			{
				$salida .= "[" . implode($v, ',') . "],\n";
			}

			return "[".$salida."]";

		}
	}


	// --------------------------------------------------------------------

	public function get_combo_tipo_fecha($tipo_fecha = '')
	{
		$arr = array(
			'ANNO'      => 'Año',
			'TRIMESTRE' => 'Trimestre',
			'MES'       => 'Meses',
			'DIA'       => 'Dias'
		);

		if ($tipo_fecha == 'ANNO')
		{
			unset($arr['ANNO']);
		}
		else if ($tipo_fecha == 'TRIMESTRE')
		{
			unset($arr['ANNO']);
			unset($arr['TRIMESTRE']);
		}
		else if ($tipo_fecha == 'MES')
		{
			unset($arr['ANNO']);
			unset($arr['TRIMESTRE']);
			unset($arr['MES']);
		}

		return $arr;
	}


	// --------------------------------------------------------------------

	public function get_combo_fechas($tipo_fecha = 'ANNO', $filtro = '')
	{
		$arr_filtro = explode('~', urldecode($filtro));

		if ($tipo_fecha == 'ANNO')
		{
			$this->db->distinct()
				->select('anno as llave')
				->select('anno as valor')
				->from($this->config->item('bd_fechas_sap'))
				->order_by('anno ASC');
		}
		else if ($tipo_fecha == 'TRIMESTRE')
		{
			$this->db->distinct()
				->select('cast(anno as varchar(10)) + \'-\' + trimestre as llave')
				->select('cast(anno as varchar(10)) + \'-\' + trimestre as valor')
				->from($this->config->item('bd_fechas_sap'))
				->order_by('cast(anno as varchar(10)) + \'-\' + trimestre ASC');

			if ($filtro != '')
			{
				$this->db->where_in('anno', $arr_filtro);
			}
		}
		else if ($tipo_fecha == 'MES')
		{
			$this->db->distinct()
				->select('anomes as llave')
				->select('anomes as valor')
				->from($this->config->item('bd_fechas_sap'))
				->order_by('anomes ASC');

			if ($filtro != '')
			{
				$this->db->where_in('cast(anno as varchar(10)) + \'-\' + trimestre', $arr_filtro);
			}
		}
		else if ($tipo_fecha == 'DIA')
		{
			$this->db->distinct()
				->select('convert(varchar(8), fecha, 112) as llave')
				->select('convert(varchar(8), fecha, 112) as valor')
				->from($this->config->item('bd_fechas_sap'))
				->order_by('convert(varchar(8), fecha, 112) ASC');

			if ($filtro != '')
			{
				$this->db->where_in('anomes', $arr_filtro);
			}
		}

		$arr_combo = $this->db->get()->result_array();

		return form_array_format($arr_combo);
	}


	// --------------------------------------------------------------------

	public function get_combo_cmv()
	{
		$arr_combo = $this->db
			->select('cmv as llave')
			->select('cmv + \' \' + des_cmv as valor')
			->from($this->config->item('bd_cmv_sap'))
			->order_by('cmv ASC')
			->get()->result_array();

		return form_array_format($arr_combo);
	}


	// --------------------------------------------------------------------

	public function get_combo_tipo_alm($tipo_alm = '')
	{
		$arr = array(
			'MOVIL-TIPOALM' => 'Móvil - Tipos Almacén',
			'MOVIL-ALM'     => 'Móvil - Almacenes',
			'FIJA-TIPOALM'  => 'Fija - Tipos Almacén',
			'FIJA-ALM'      => 'Fija - Almacenes',
		);

		if ($tipo_alm == 'MOVIL-TIPOALM')
		{
			unset($arr['MOVIL-TIPOALM']);
			unset($arr['FIJA-TIPOALM']);
			unset($arr['FIJA-ALM']);
		}
		else if ($tipo_alm == 'FIJA-TIPOALM')
		{
			unset($arr['MOVIL-TIPOALM']);
			unset($arr['MOVIL-ALM']);
			unset($arr['FIJA-TIPOALM']);
		}

		return $arr;
	}

	// --------------------------------------------------------------------

	public function get_combo_almacenes($tipo = 'MOVIL-TIPOALM', $filtro = '')
	{
		$param = explode('-', $tipo);

		$almacen_sap = new Almacen_sap;
		$tipoalmacen_sap = new Tipoalmacen_sap;

		return ($param[1] == 'TIPOALM') ?
			$tipoalmacen_sap->get_combo_tiposalm($param[0]) :
			$almacen_sap->get_combo_almacenes($param[0], $filtro);
	}


	// --------------------------------------------------------------------

	public function get_combo_tipo_mat($tipo_mat = '')
	{
		$arr = array(
			'TIPO'     => 'Tipo',
			'MARCA'    => 'Marca',
			'MODELO'   => 'Modelo',
			'MATERIAL' => 'Material',
		);

		if ($tipo_mat == 'TIPO')
		{
			unset($arr['TIPO']);
		}
		else if ($tipo_mat == 'MARCA')
		{
			unset($arr['TIPO']);
			unset($arr['MARCA']);
		}
		else if ($tipo_mat == 'MODELO')
		{
			unset($arr['TIPO']);
			unset($arr['MARCA']);
			unset($arr['MODELO']);
		}

		return $arr;
	}


	// --------------------------------------------------------------------

	public function get_combo_materiales($tipo = 'TIPO', $filtro = '')
	{
		$arr_filtro = explode('~', urldecode($filtro));

		if ($tipo == 'TIPO')
		{
			$this->db
				->select('tipo1 as llave')
				->select('tipo1 as valor')
				->from($this->config->item('bd_materiales2_sap'))
				->order_by('tipo1 ASC');
		}
		else if ($tipo == 'MARCA')
		{
			$this->db
				->select('sub_marca as llave')
				->select('sub_marca + \' \'+ des_marca as valor')
				->from($this->config->item('bd_materiales2_sap'))
				->order_by('des_marca ASC');

			if ($filtro != '')
			{
				$this->db->where_in('tipo1', $arr_filtro);
			}
		}
		else if ($tipo == 'MODELO')
		{
			$this->db
				->select('sub_marcamodelo as llave')
				->select('sub_marcamodelo as valor')
				->from($this->config->item('bd_materiales2_sap'))
				->order_by('sub_marcamodelo ASC');

			if ($filtro != '')
			{
				$this->db->where_in('sub_marca', $arr_filtro);
			}
		}
		else if ($tipo == 'MATERIAL')
		{
			$arr_combo = $this->db
				->select('codigo_sap as llave')
				->select('codigo_sap + \' \' + descripcion as valor')
				->from($this->config->item('bd_materiales2_sap'))
				->order_by('codigo_sap ASC');

			if ($filtro != '')
			{
				$this->db->where_in('sub_marcamodelo', $arr_filtro);
			}
		}

		$arr_combo = $this->db->get()->result_array();

		return form_array_format($arr_combo);
	}


	// --------------------------------------------------------------------

	public function get_reporte_movhist($config = array())
	{
		$param_ok = $config['fechas'] AND $config['cmv'] AND $config['almacenes'] AND $config['materiales'];

		$arr_reporte = array();

		$arr_filtro_fechas = array(
			'ANNO'      => 'f.anno',
			'TRIMESTRE' => 'cast(f.anno as varchar(10)) + \'/\' + trimestre',
			'MES'       => 'f.anomes',
			'DIA'       => 'f.fecha',
		);

		if ($config['tipo_cruce_alm'] == 'alm')
		{
			$arr_filtro_almacenes = array(
				'MOVIL-TIPOALM' => 't.id_tipo',
				'MOVIL-ALM'     => 'm.ce+\'-\'+m.alm',
				'FIJA-TIPOALM'  => 't.id_tipo',
				'FIJA-ALM'      => 'm.ce+\'-\'+m.alm',
			);
		}
		else
		{
			$arr_filtro_almacenes = array(
				'MOVIL-TIPOALM' => 't.id_tipo',
				'MOVIL-ALM'     => 'm.ce+\'-\'+m.rec',
				'FIJA-TIPOALM'  => 't.id_tipo',
				'FIJA-ALM'      => 'm.ce+\'-\'+m.rec',
			);
		}

		$arr_filtro_materiales = array(
			'TIPO'     => 'mat.tipo1',
			'MARCA'    => 'mat.sub_marca',
			'MODELO'   => 'mat.sub_marcamodelo',
			'MATERIAL' => 'm.codigo_sap',
		);

		if ($param_ok)
		{
			$this->db->limit(1000);

			$this->db->from($this->config->item('bd_resmovimientos_sap') . ' as m');
			$this->db->join($this->config->item('bd_fechas_sap') . ' as f', 'm.fecha=f.fecha');
			$this->db->join($this->config->item('bd_cmv_sap') . ' as c', 'm.cmv=c.cmv');
			$this->db->join($this->config->item('bd_materiales2_sap') . ' as mat', 'm.codigo_sap=mat.codigo_sap');

			if ($config['tipo_cruce_alm'] == 'alm')
			{
				$this->db->join($this->config->item('bd_almacenes_sap') . ' as a', 'm.ce=a.centro and m.alm=a.cod_almacen');
				$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' as t', 'm.ce=t.centro and m.alm=t.cod_almacen');
			}
			else
			{
				$this->db->join($this->config->item('bd_almacenes_sap') . ' as a', 'm.ce=a.centro and m.rec=a.cod_almacen');
				$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' as t', 'm.ce=t.centro and m.rec=t.cod_almacen');
			}


			$this->db->where_in($arr_filtro_fechas[$config['tipo_fecha']], $config['fechas']);
			$this->db->where_in('m.cmv', $config['cmv']);
			$this->db->where_in($arr_filtro_almacenes[$config['tipo_alm']], $config['almacenes']);
			$this->db->where_in($arr_filtro_materiales[$config['tipo_mat']], $config['materiales']);

			$arr_reporte = $this->db->get()->result_array();
		}

		return $arr_reporte;

	}


}

/* End of file reportestock_model.php */
/* Location: ./application/models/reportestock_model.php */