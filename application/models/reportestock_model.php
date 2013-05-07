<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Reportestock_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Devuelve datos para poblar combobox de tipos de inventario
	 * @return array Arreglo con los tipos de inventiaro
	 */
	public function get_combo_tipos_inventario()
	{
		return array(
					''           => 'Seleccione tipo de inventario...',
					'FIJA'       => 'Inventario Fija (Puerto Madero)',
					'MAIMONIDES' => 'Inventario Empresas (Maimonides)',
					'CABLES'     => 'Inventario Proveedores Cables',
					);
	}


	/**
	 * Devuelve el identificador del inventario activo
	 * @return integer ID del inventario activo
	 */
	public function get_id_inventario_activo()
	{
		$this->db->where('activo', 1);
		$row = $this->db->get('fija_inventario2')->row_array();

		return ($row['id']);
	}


	/**
	 * Recupera los inventarios para desplegar en combobox
	 * @return array Arreglo con inventarios para usar en combobox
	 */
	public function get_combo_inventarios()
	{
		$arr_inv = array();
		$this->db->order_by('nombre ASC');
		$arr_rs = $this->db->get('fija_inventario2')->result_array();
		$arr_inv[''] = 'Seleccione inventario activo...';
		foreach($arr_rs as $val)
		{
			$arr_inv[$val['id']] = $val['nombre'];
		}

		return $arr_inv;
	}






	// =====================================================================================================
	// REPORTES
	// =====================================================================================================

	/**
	 * Devuelve reporte por hojas
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_permanencia($orden_campo = 'tipo', $orden_tipo = 'ASC',
										$incl_almacen = '0', $incl_lote = '0', $incl_estado = '0', $incl_modelos = '0')
	{


		$this->db->select('t.tipo');
		$this->db->select('count(case when p.dias<= 30 then 1 else null end) as \'000-030\'');
		$this->db->select('count(case when p.dias > 30 and p.dias<= 60 then 1 else null end) as \'031-060\'');
		$this->db->select('count(case when p.dias > 60 and p.dias<= 90 then 1 else null end) as \'061-090\'');
		$this->db->select('count(case when p.dias > 90 and p.dias<= 120 then 1 else null end) as \'091-120\'');
		$this->db->select('count(case when p.dias > 120 and p.dias<= 150 then 1 else null end) as \'121-150\'');
		$this->db->select('count(case when p.dias > 150 and p.dias<= 180 then 1 else null end) as \'151-180\'');
		$this->db->select('count(case when p.dias > 180 and p.dias<= 360 then 1 else null end) as \'181-360\'');
		$this->db->select('count(case when p.dias > 360 and p.dias<= 500 then 1 else null end) as \'361-500\'');
		$this->db->select('count(case when p.dias > 500 and p.dias<= 720 then 1 else null end) as \'501-720\'');
		$this->db->select('count(case when p.dias > 720 then 1 else null end) as \'+720\'');
		$this->db->select('count(1) as \'total\'');
		$this->db->from('bd_controles..ctrl00_equipos_cl15_permanencia as p');
		$this->db->join('bd_logistica..cp_tipos_almacenes ta', 'ta.centro=p.centro and ta.cod_almacen=p.cod_almacen', 'left');
		$this->db->join('bd_logistica..cp_tiposalm t', 'ta.id_tipo=t.id_tipo', 'left');
		$this->db->group_by('t.tipo');
		$this->db->order_by('t.tipo');

		if ($incl_almacen == '1')
		{
			$this->db->select('p.centro, p.cod_almacen, a.des_almacen');
			$this->db->join('bd_logistica..cp_almacenes a', 'p.centro=a.centro and p.cod_almacen=a.cod_almacen', 'left');
			$this->db->group_by('p.centro, p.cod_almacen, a.des_almacen');
			$this->db->order_by('p.centro, p.cod_almacen, a.des_almacen');
		}

		if ($incl_lote == '1')
		{
			$this->db->select('p.lote');
			$this->db->group_by('p.lote');
			$this->db->order_by('p.lote');
		}

		if ($incl_estado == '1')
		{
			$this->db->select('case p.estado_sap when \'01\' then \'01 Libre util\' when \'03\' then \'03 Devol cliente\' when \'07\' then \'07 Bloqueado\' when \'06\' then \'06 Transito\' else p.estado_sap end as estado_sap');
			$this->db->group_by('p.estado_sap');
			$this->db->order_by('p.estado_sap');
		}

		if ($incl_modelos == '1')
		{
			$this->db->select('substring(p.cod_material_sap, 6, 7) as modelo');
			$this->db->group_by('substring(p.cod_material_sap, 6, 7)');
			$this->db->order_by('modelo');
		}

			return $this->db->get()->result_array();
	}



	/**
	 * Devuelve reporte con el detalle de una hoja
	 * @param  integer $id_inventario ID del inventario con el cual se hará el reporte
	 * @param  string  $orden_campo   Nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ASC/DESC)
	 * @param  integer $hoja          numero de la hoja del inventario
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_detalle_hoja($id_inventario = 0, $orden_campo = 'ubicacion', $orden_tipo = 'ASC',
												$hoja = 0, $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$this->db->select('fija_detalle_inventario.ubicacion');
		$this->db->select('fija_detalle_inventario.catalogo');
		$this->db->select('fija_detalle_inventario.descripcion');
		$this->db->select('fija_detalle_inventario.lote');
		$this->db->select('fija_detalle_inventario.centro');
		$this->db->select('fija_detalle_inventario.almacen');
		$this->db->select('fija_detalle_inventario.um');
		$this->db->select('fija_detalle_inventario.stock_sap');
		$this->db->select('fija_detalle_inventario.stock_fisico');
		$this->db->select('fija_detalle_inventario.stock_ajuste');
		if ($incl_ajustes == '1')
		{
			$this->db->select('fija_detalle_inventario.stock_fisico - fija_detalle_inventario.stock_sap + fija_detalle_inventario.stock_ajuste as stock_diff');
		}
		else
		{
			$this->db->select('fija_detalle_inventario.stock_fisico - fija_detalle_inventario.stock_sap as stock_diff');
		}
		$this->db->select('fija_detalle_inventario.stock_sap*c.pmp as valor_sap');
		$this->db->select('fija_detalle_inventario.stock_fisico*c.pmp as valor_fisico');
		$this->db->select('fija_detalle_inventario.stock_ajuste*c.pmp as valor_ajuste');
		if ($incl_ajustes == '1')
		{
			$this->db->select('((fija_detalle_inventario.stock_fisico - fija_detalle_inventario.stock_sap + fija_detalle_inventario.stock_ajuste)*c.pmp) as valor_diff');
		}
		else
		{
			$this->db->select('((fija_detalle_inventario.stock_fisico - fija_detalle_inventario.stock_sap)*c.pmp) as valor_diff');
		}

		$this->db->from('fija_detalle_inventario');
		$this->db->join('fija_catalogo as c', "c.catalogo = fija_detalle_inventario.catalogo", 'left');

		$this->db->where('fija_detalle_inventario.id_inventario', $id_inventario);
		$this->db->where('fija_detalle_inventario.hoja', $hoja);

		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0');
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0');
			}
		}


		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}


	/**
	 * Devuelve reporte por materiales
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_material($id_inventario = 0, $orden_campo = 'catalogo', $orden_tipo = 'ASC', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$this->db->select("f.codigo + '-' + f.nombre + ' >> ' + sf.codigo + '-' + sf.nombre as nombre_fam");
		$this->db->select("f.codigo + '_' + sf.codigo as fam_subfam");
		$this->db->select('fija_detalle_inventario.catalogo, fija_detalle_inventario.descripcion, fija_detalle_inventario.um, c.pmp');
		$this->db->select_sum('stock_sap' , 'sum_stock_sap');
		$this->db->select_sum('stock_fisico' , 'sum_stock_fisico');
		if ($incl_ajustes == '1')
		{
			$this->db->select_sum('(stock_ajuste)' , 'sum_stock_ajuste');
		}
		if ($incl_ajustes == '1')
		{
			$this->db->select_sum('stock_fisico - stock_sap + stock_ajuste' , 'sum_stock_dif');
		}
		else
		{
			$this->db->select_sum('stock_fisico - stock_sap' , 'sum_stock_dif');
		}
		$this->db->select_sum('(stock_sap*c.pmp)' , 'sum_valor_sap');
		$this->db->select_sum('(stock_fisico*c.pmp)' , 'sum_valor_fisico');
		if ($incl_ajustes == '1')
		{
			$this->db->select_sum('(stock_ajuste*c.pmp)' , 'sum_valor_ajuste');
		}
		if ($incl_ajustes == '1')
		{
			$this->db->select_sum('(stock_fisico*c.pmp - stock_sap*c.pmp + stock_ajuste*pmp)' , 'sum_valor_diff');
		}
		else
		{
			$this->db->select_sum('(stock_fisico*c.pmp - stock_sap*c.pmp)' , 'sum_valor_diff');
		}
		$this->db->select_max('fecha_modificacion' , 'fecha');
		$this->db->from('fija_detalle_inventario');
		$this->db->join('fija_catalogo as c', "c.catalogo = fija_detalle_inventario.catalogo", 'left');
		$this->db->join('fija_catalogo_familias as f', "f.codigo = substring(fija_detalle_inventario.catalogo,1,5) and f.tipo='FAM'", 'left');
		$this->db->join('fija_catalogo_familias as sf', "sf.codigo = substring(fija_detalle_inventario.catalogo,1,7) and sf.tipo='SUBFAM'", 'left');

		$this->db->where('id_inventario', $id_inventario);

		$this->db->group_by("f.codigo + '-' + f.nombre + ' >> ' + sf.codigo + '-' + sf.nombre");
		$this->db->group_by("f.codigo + '_' + sf.codigo");
		$this->db->group_by('fija_detalle_inventario.catalogo, fija_detalle_inventario.descripcion, fija_detalle_inventario.um, c.pmp');
		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->having('sum(stock_fisico - stock_sap + stock_ajuste) <> 0');
			}
			else
			{
				$this->db->having('sum(stock_fisico - stock_sap) <> 0');
			}
		}
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}


	/**
	 * Devuelve reporte por materiales faltantes
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_material_faltante($id_inventario = 0, $orden_campo = 'catalogo', $orden_tipo = 'ASC', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$this->db->select('i.catalogo, i.descripcion, i.um, c.pmp');
		$this->db->select_sum('stock_fisico', 'q_fisico');
		$this->db->select_sum('stock_sap', 'q_sap');
		if ($incl_ajustes == '1')
		{
			$this->db->select('(SUM(stock_sap) - 0.5*(SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as q_faltante');
			$this->db->select('(0.5*(SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as q_coincidente');
			$this->db->select('(SUM((stock_fisico+stock_ajuste)) - 0.5*(SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as q_sobrante');
			$this->db->select('c.pmp*(SUM(stock_sap) - 0.5*(SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as v_faltante');
			$this->db->select('c.pmp*(0.5*(SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as v_coincidente');
			$this->db->select('c.pmp*(SUM((stock_fisico+stock_ajuste)) - 0.5*(SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as v_sobrante');
		}
		else
		{
			$this->db->select('(SUM(stock_sap) - 0.5*(SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as q_faltante');
			$this->db->select('(0.5*(SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as q_coincidente');
			$this->db->select('(SUM(stock_fisico) - 0.5*(SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as q_sobrante');
			$this->db->select('c.pmp*(SUM(stock_sap) - 0.5*(SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as v_faltante');
			$this->db->select('c.pmp*(0.5*(SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as v_coincidente');
			$this->db->select('c.pmp*(SUM(stock_fisico) - 0.5*(SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as v_sobrante');
		}
		$this->db->from('fija_detalle_inventario as i');
		$this->db->join('fija_catalogo as c', "c.catalogo = i.catalogo", 'left');
		$this->db->where('id_inventario', $id_inventario);

		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->having('(SUM(stock_sap) - 0.5*(SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) <> 0');
				$this->db->or_having('(SUM((stock_fisico+stock_ajuste)) - 0.5*(SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) <> 0');
			}
			else
			{
				$this->db->having('(SUM(stock_sap) - 0.5*(SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) <> 0');
				$this->db->or_having('(SUM(stock_fisico) - 0.5*(SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) <> 0');
			}
		}
		$this->db->group_by('i.catalogo, i.descripcion, i.um, c.pmp');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}


	/**
	 * Devuelve reporte por detalle de los materiales
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $catalogo      catalogo a buscar
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_detalle_material($id_inventario = 0, $orden_campo = 'ubicacion', $orden_tipo = 'ASC', $catalogo = '', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$this->db->select('i.*, c.pmp');
		$this->db->select('stock_fisico as stock_fisico');
		$this->db->select('stock_sap as stock_sap');
		$this->db->select('stock_ajuste as stock_ajuste');
		$this->db->select('stock_fisico*c.pmp as valor_fisico');
		$this->db->select('stock_sap*c.pmp as valor_sap');
		$this->db->select('stock_ajuste*c.pmp as valor_ajuste');
		if ($incl_ajustes == '1')
		{
			$this->db->select('(stock_fisico - stock_sap + stock_ajuste) as stock_diff');
			$this->db->select('(stock_fisico - stock_sap + stock_ajuste)*c.pmp as valor_diff');
			$this->db->select('(stock_sap - 0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante');
			$this->db->select('(0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente');
		}
		else
		{
			$this->db->select('(stock_fisico - stock_sap) as stock_diff');
			$this->db->select('(stock_fisico - stock_sap)*c.pmp as valor_diff');
			$this->db->select('(stock_sap - 0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante');
			$this->db->select('(0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente');
		}
		$this->db->select('(stock_sap - 0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante');
		$this->db->select('(0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente');
		$this->db->select('(stock_fisico - 0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_sobrante');
		$this->db->select('pmp*(stock_sap - 0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_faltante');
		$this->db->select('pmp*(0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_coincidente');
		$this->db->select('pmp*(stock_fisico - 0.5*((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_sobrante');
		$this->db->from('fija_detalle_inventario as i');
		$this->db->join('fija_catalogo as c', "c.catalogo = i.catalogo", 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->where('i.catalogo', $catalogo);

		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0');
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0');
			}
		}


		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}


	/**
	 * Devuelve reporte por ubicaciones
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_ubicacion($id_inventario = 0, $orden_campo = 'ubicacion', $orden_tipo = 'ASC')
	{
		$this->db->select('fija_detalle_inventario.ubicacion');
		$this->db->select_sum('stock_sap' , 'sum_stock_sap');
		$this->db->select_sum('stock_fisico' , 'sum_stock_fisico');
		$this->db->select_sum('(stock_fisico-stock_sap)' , 'sum_stock_diff');
		$this->db->select_sum('(stock_sap*c.pmp)' , 'sum_valor_sap');
		$this->db->select_sum('(stock_fisico*c.pmp)' , 'sum_valor_fisico');
		$this->db->select_sum('((stock_fisico-stock_sap)*c.pmp)' , 'sum_valor_diff');
		$this->db->select_max('fecha_modificacion' , 'fecha');
		$this->db->from('fija_detalle_inventario');
		$this->db->join('fija_catalogo as c', "c.catalogo = fija_detalle_inventario.catalogo", 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->group_by('fija_detalle_inventario.ubicacion');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}


	/**
	 * Devuelve reporte por tipos de ubicaciones
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_tipos_ubicacion($id_inventario = 0, $orden_campo = 'ubicacion', $orden_tipo = 'ASC', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$this->db->select('t.tipo_ubicacion');
		$this->db->select('d.ubicacion');
		$this->db->select_sum('stock_sap' , 'sum_stock_sap');
		$this->db->select_sum('stock_fisico' , 'sum_stock_fisico');
		if ($incl_ajustes == '0')
		{
			$this->db->select_sum('(stock_fisico - stock_sap)' , 'sum_stock_diff');
		}
		else
		{
			$this->db->select_sum('stock_ajuste' , 'sum_stock_ajuste');
			$this->db->select_sum('(stock_fisico - stock_sap + stock_ajuste)' , 'sum_stock_diff');
		}

		$this->db->select_sum('(stock_fisico-stock_sap)' , 'sum_stock_diff');
		$this->db->select_sum('(stock_sap*c.pmp)' , 'sum_valor_sap');
		$this->db->select_sum('(stock_fisico*c.pmp)' , 'sum_valor_fisico');
		if ($incl_ajustes == '0')
		{
			$this->db->select_sum('((stock_fisico-stock_sap)*c.pmp)' , 'sum_valor_diff');
		}
		else
		{
			$this->db->select_sum('(stock_ajuste*c.pmp)' , 'sum_valor_ajuste');
			$this->db->select_sum('((stock_fisico-stock_sap+stock_ajuste)*c.pmp)' , 'sum_valor_diff');
		}

		$this->db->select_max('fecha_modificacion' , 'fecha');
		$this->db->from('fija_detalle_inventario d');
		$this->db->join('fija_inventario2 i', 'd.id_inventario=i.id', 'left');
		$this->db->join('fija_ubicacion_tipo_ubicacion as ut', 'ut.tipo_inventario=i.tipo_inventario and ut.ubicacion = d.ubicacion', 'left');
		$this->db->join('fija_tipo_ubicacion t', 't.id=ut.id_tipo_ubicacion', 'left');
		$this->db->join('fija_catalogo as c', "c.catalogo = d.catalogo", 'left');
		$this->db->where('d.id_inventario', $id_inventario);

		$this->db->group_by('t.tipo_ubicacion');
		$this->db->group_by('d.ubicacion');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}


















/*


	public function get_ajustes($id_inventario = 0, $ocultar_regularizadas = 0)
	{
		$this->db->order_by('catalogo, lote, centro, almacen, ubicacion');
		$this->db->where('id_inventario', $id_inventario);
		if ($ocultar_regularizadas == 1)
		{
			$this->db->where('stock_fisico - stock_sap + stock_ajuste <> 0');
		}
		else
		{
			$this->db->where('stock_sap <> stock_fisico');
		}

		return $this->db->get('fija_detalle_inventario')->result_array();
	}


	public function guardar_ajuste($reg_id = 0, $stock_ajuste = 0, $glosa_ajuste = '', $fecha_ajuste = 0)
	{
		$this->db->where('id', $reg_id);
		$this->db->update('fija_detalle_inventario', array(
			'stock_ajuste' => $stock_ajuste,
			'glosa_ajuste' => $glosa_ajuste,
			'fecha_ajuste' => $fecha_ajuste,
			));
	}


	public function borrar_inventario($id_inventario = 0)
	{
		$this->db->delete('fija_detalle_inventario', array('id_inventario' => $id_inventario));
	}


	public function get_nombre_inventario($id_inventario = 0)
	{
		$this->db->where('id', $id_inventario);
		$row = $this->db->get('fija_inventario2')->row_array();

		return ($row['nombre']);
	}


	public function guardar($id = 0, $id_inventario = 0,
							$hoja = 0, $digitador = 0, $auditor = 0,
							$ubicacion = '', $catalogo = '', $descripcion = '', $lote = '', $centro = '', $almacen = '',
							$um = '', $stock_sap = 0, $stock_fisico = 0, $observacion = '', $fecha_modificacion = '', $reg_nuevo = '')
	{
		// insertar un nuevo registro
		if ($id == 0)
		{
			$this->db->insert('fija_detalle_inventario', array(
				'id_inventario' => $id_inventario,
				'hoja'          => $hoja,
				'digitador'     => $digitador,
				'auditor'       => $auditor,
				'ubicacion'     => $ubicacion,
				'catalogo'      => $catalogo,
				'descripcion'   => $descripcion,
				'lote'          => $lote,
				'centro'        => $centro,
				'almacen'       => $almacen,
				'um'            => $um,
				'stock_sap'     => $stock_sap,
				'stock_fisico'  => (int) $stock_fisico,
				'observacion'   => $observacion,
				'fecha_modificacion' => $fecha_modificacion,
				'reg_nuevo'     => $reg_nuevo,
				));
		}
		//modificar un registro existente
		else
		{
			$this->db->where('id', $id);
			$this->db->update('fija_detalle_inventario', array(
				'id_inventario' => $id_inventario,
				'hoja'          => $hoja,
				'digitador'     => $digitador,
				'auditor'       => $auditor,
				'ubicacion'     => $ubicacion,
				'catalogo'      => $catalogo,
				'descripcion'   => $descripcion,
				'lote'          => $lote,
				'centro'        => $centro,
				'almacen'       => $almacen,
				'um'            => $um,
				'stock_sap'     => $stock_sap,
				'stock_fisico'  => (int) $stock_fisico,
				'observacion'   => $observacion,
				'fecha_modificacion' => $fecha_modificacion,
				'reg_nuevo'     => $reg_nuevo,
				));
		}
	}


	public function get_max_hoja_inventario($id_inventario = 0)
	{
		$this->db->where('id_inventario', $id_inventario);
		$this->db->select_max('hoja');
		$row = $this->db->get('fija_detalle_inventario')->row_array();

		return ((int)$row['hoja']);
	}


	public function get_hoja($id_inventario = 0, $hoja = 0)
	{
		$this->db->order_by('ubicacion ASC, catalogo ASC, lote ASC');
		$this->db->where('hoja', $hoja);
		$this->db->where('id_inventario', $id_inventario);

		return $this->db->get('fija_detalle_inventario')->result_array();
	}



	public function total_inventarios_activos()
	{
		return $this->db->count_all('fija_inventario2');
	}



	public function get_inventarios_activos($limit = 0, $offset = 0)
	{
		$this->db->order_by('nombre ASC');
		return $this->db->get('fija_inventario2', $limit, $offset)->result_array();
	}



	public function get_auditor_hoja($id_inventario = 0, $hoja = 0)
	{
		$this->db->distinct();
		$this->db->select('a.nombre as nombre');
		$this->db->from('fija_detalle_inventario');
		$this->db->join('fija_auditores as a', 'a.id = fija_detalle_inventario.auditor', 'left');
		$this->db->where(array('fija_detalle_inventario.id_inventario' => $id_inventario, 'fija_detalle_inventario.hoja' => $hoja));
		$row = $this->db->get()->row_array();

		return (array_key_exists('nombre', $row) ? $row['nombre'] : '');
	}

	public function get_digitador_hoja($id_inventario = 0, $hoja = 0)
	{
		$this->db->distinct();
		$this->db->select('d.nombre as nombre');
		$this->db->from('fija_detalle_inventario');
		$this->db->join('fija_usuarios as d', "d.id = fija_detalle_inventario.digitador and d.tipo='DIG'", 'left');
		$this->db->where(array('fija_detalle_inventario.id_inventario' => $id_inventario, 'fija_detalle_inventario.hoja' => $hoja));
		$row = $this->db->get()->row_array();

		return (array_key_exists('nombre', $row) ? $row['nombre'] : '');
	}


	public function inserta_bulk($arr_bulk = array())
	{
		$this->db->insert_batch('fija_detalle_inventario', $arr_bulk);
	}





	public function borrar($id = 0)
	{
		$this->db->delete('fija_detalle_inventario', array('id' => $id, 'reg_nuevo' => 'S'));
	}


	public function borrar_inventario_activo($id_inventario = 0)
	{
		$this->db->delete('fija_inventario2', array('id' => $id_inventario));
	}

	public function get_cant_registros_inventario($id_inventario = 0)
	{
		return $this->db->get_where('fija_detalle_inventario', array('id_inventario' => $id_inventario))->num_rows();
	}













	public function guardar_inventario_activo($id = 0, $nombre = '', $tipo = '', $activo = 0)
	{
		if ($id == 0)
		{
			$this->db->insert('fija_inventario2', array('nombre' => $nombre, 'tipo_inventario' => $tipo, 'activo' => (int) $activo ));

		}
		else
		{
			$this->db->where('id', $id);
			$this->db->update('fija_inventario2', array('nombre' => $nombre, 'tipo_inventario' => $tipo, 'activo' => (int) $activo ));
		}
	}



	public function get_reporte_hoja2($id_inventario = 0, $hoja = 0)
	{
		$this->db->select('fija_detalle_inventario.*, c.pmp');
		$this->db->from('fija_detalle_inventario');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->where('hoja', $hoja);
		$this->db->join('fija_catalogo as c', "c.catalogo = fija_detalle_inventario.catalogo", 'left');
		$this->db->order_by('ubicacion ASC, catalogo ASC, lote ASC');

		return $this->db->get()->result_array();
	}


	public function get_reporte_material2($id_inventario = 0, $catalogo = 0)
	{
		$this->db->select('fija_detalle_inventario.*, d.nombre as digitador, a.nombre as auditor');
		$this->db->from('fija_detalle_inventario');
		$this->db->join('fija_usuarios as d', "d.id = fija_detalle_inventario.digitador and d.tipo='DIG'", 'left');
		$this->db->join('fija_usuarios as a', "a.id = fija_detalle_inventario.auditor   and a.tipo='AUD'", 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->where('catalogo', $catalogo);
		$this->db->order_by('hoja, ubicacion');

		return $this->db->get()->result_array();
	}


	public function get_reporte_ubicacion2($id_inventario = 0, $ubicacion = '')
	{
		$this->db->select('fija_detalle_inventario.*, d.nombre as digitador, a.nombre as auditor');
		$this->db->from('fija_detalle_inventario');
		$this->db->join('fija_usuarios as d', "d.id = fija_detalle_inventario.digitador and d.tipo='DIG'", 'left');
		$this->db->join('fija_usuarios as a', "a.id = fija_detalle_inventario.auditor   and a.tipo='AUD'", 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->where('ubicacion', $ubicacion);
		$this->db->order_by('ubicacion, catalogo, centro, almacen');

		return $this->db->get()->result_array();
	}

 */

}

/* End of file inventario_model.php */
/* Location: ./application/models/inventario_model.php */