<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Inventario_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el identificador del inventario activo
	 * @return integer ID del inventario activo
	 */
	public function get_id_inventario_activo()
	{
		$this->db->where('activo', 1);
		$row = $this->db->get($this->config->item('bd_inventarios'))->row_array();

		return ($row['id']);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los inventarios para desplegar en combobox
	 * @return array Arreglo con inventarios para usar en combobox
	 */
	public function get_combo_inventarios()
	{
		$arr_inv = array();
		$this->db->order_by('nombre ASC');
		$arr_rs = $this->db->get($this->config->item('bd_inventarios'))->result_array();
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
	public function get_reporte_hoja($id_inventario = 0, $orden_campo = 'hoja', $orden_tipo = 'ASC',
										$incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$this->db->select('di.hoja, d.nombre as digitador, a.nombre as auditor');

		$this->db->select_sum('stock_sap' , 'sum_stock_sap');
		$this->db->select_sum('stock_fisico' , 'sum_stock_fisico');
		if ($incl_ajustes == '1')
		{
			$this->db->select_sum('stock_ajuste' , 'sum_stock_ajuste');
			$this->db->select_sum('(stock_fisico - stock_sap + stock_ajuste)' , 'sum_stock_diff');
		}
		else
		{
			$this->db->select_sum('(stock_fisico - stock_sap)' , 'sum_stock_diff');
		}

		$this->db->select_sum('(stock_sap * c.pmp)' , 'sum_valor_sap');
		$this->db->select_sum('(stock_fisico * c.pmp)' , 'sum_valor_fisico');
		if ($incl_ajustes == '1')
		{
			$this->db->select_sum('(stock_ajuste * c.pmp)' , 'sum_valor_ajuste');
			$this->db->select_sum('((stock_fisico - stock_sap + stock_ajuste) * c.pmp)' , 'sum_valor_diff');
		}
		else
		{
			$this->db->select_sum('((stock_fisico-stock_sap) * c.pmp)' , 'sum_valor_diff');
		}

		$this->db->select_max('fecha_modificacion' , 'fecha');
		$this->db->from($this->config->item('bd_detalle_inventario') . ' di');
		$this->db->join($this->config->item('bd_usuarios') . ' d', "d.id = di.digitador", 'left');
		$this->db->join($this->config->item('bd_auditores') . ' a', "a.id = di.auditor", 'left');
		$this->db->join($this->config->item('bd_catalogos') . ' c', "c.catalogo = di.catalogo", 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->group_by('di.hoja, d.nombre, a.nombre');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

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

		$this->db->select("case when reg_nuevo='S' THEN ubicacion + ' [*]' ELSE ubicacion END as ubicacion", FALSE);
		$this->db->select('di.catalogo');
		$this->db->select('di.descripcion');
		$this->db->select('di.lote');
		$this->db->select('di.centro');
		$this->db->select('di.almacen');
		$this->db->select('di.um');
		$this->db->select('di.stock_sap');
		$this->db->select('di.stock_fisico');
		$this->db->select('di.stock_ajuste');
		if ($incl_ajustes == '1')
		{
			$this->db->select('(di.stock_fisico - di.stock_sap + di.stock_ajuste) as stock_diff');
		}
		else
		{
			$this->db->select('(di.stock_fisico - di.stock_sap) as stock_diff');
		}
		$this->db->select('(di.stock_sap * c.pmp) as valor_sap');
		$this->db->select('(di.stock_fisico * c.pmp) as valor_fisico');
		$this->db->select('(di.stock_ajuste * c.pmp) as valor_ajuste');
		if ($incl_ajustes == '1')
		{
			$this->db->select('((di.stock_fisico - di.stock_sap + di.stock_ajuste) * c.pmp) as valor_diff');
		}
		else
		{
			$this->db->select('((di.stock_fisico - di.stock_sap) * c.pmp) as valor_diff');
		}

		$this->db->from($this->config->item('bd_detalle_inventario') . ' di');
		$this->db->join($this->config->item('bd_catalogos') . ' c', "c.catalogo = di.catalogo", 'left');

		$this->db->where('di.id_inventario', $id_inventario);
		$this->db->where('di.hoja', $hoja);

		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}


		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

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
		$this->db->select("f.codigo + '-' + f.nombre + ' >> ' + sf.codigo + '-' + sf.nombre as nombre_fam", FALSE);
		$this->db->select("f.codigo + '_' + sf.codigo as fam_subfam", FALSE);
		$this->db->select('di.catalogo, di.descripcion, di.um, c.pmp');
		$this->db->select_sum('stock_sap' , 'sum_stock_sap');
		$this->db->select_sum('stock_fisico' , 'sum_stock_fisico');
		if ($incl_ajustes == '1')
		{
			$this->db->select_sum('stock_ajuste' , 'sum_stock_ajuste');
		}
		if ($incl_ajustes == '1')
		{
			$this->db->select('SUM(stock_fisico - stock_sap + stock_ajuste) as sum_stock_dif', FALSE);
		}
		else
		{
			$this->db->select('SUM(stock_fisico - stock_sap) as sum_stock_dif', FALSE);
		}
		$this->db->select('SUM(stock_sap * c.pmp) as sum_valor_sap', FALSE);
		$this->db->select('SUM(stock_fisico * c.pmp) as sum_valor_fisico', FALSE);
		if ($incl_ajustes == '1')
		{
			$this->db->select('SUM(stock_ajuste * c.pmp) as sum_valor_ajuste', FALSE);
		}
		if ($incl_ajustes == '1')
		{
			$this->db->select('SUM(stock_fisico * c.pmp - stock_sap * c.pmp + stock_ajuste * pmp) as sum_valor_diff', FALSE);
		}
		else
		{
			$this->db->select('SUM(stock_fisico * c.pmp - stock_sap * c.pmp) as sum_valor_diff', FALSE);
		}
		$this->db->select_max('fecha_modificacion' , 'fecha');
		$this->db->from($this->config->item('bd_detalle_inventario') . ' di');
		$this->db->join($this->config->item('bd_catalogos') . ' c', "c.catalogo = di.catalogo", 'left');
		$this->db->join($this->config->item('bd_familias') . ' f', "f.codigo = substring(di.catalogo,1,5) and f.tipo='FAM'", 'left');
		$this->db->join($this->config->item('bd_familias') . ' sf', "sf.codigo = substring(di.catalogo,1,7) and sf.tipo='SUBFAM'", 'left');

		$this->db->where('id_inventario', $id_inventario);

		$this->db->group_by("f.codigo + '-' + f.nombre + ' >> ' + sf.codigo + '-' + sf.nombre");
		$this->db->group_by("f.codigo + '_' + sf.codigo");
		$this->db->group_by('di.catalogo, di.descripcion, di.um, c.pmp');
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


	// --------------------------------------------------------------------

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
			$this->db->select('(SUM(stock_sap) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as q_faltante');
			$this->db->select('(0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as q_coincidente');
			$this->db->select('(SUM((stock_fisico+stock_ajuste)) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as q_sobrante');
			$this->db->select('c.pmp * (SUM(stock_sap) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as v_faltante');
			$this->db->select('c.pmp * (0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as v_coincidente');
			$this->db->select('c.pmp * (SUM((stock_fisico+stock_ajuste)) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as v_sobrante');
		}
		else
		{
			$this->db->select('(SUM(stock_sap) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as q_faltante');
			$this->db->select('(0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as q_coincidente');
			$this->db->select('(SUM(stock_fisico) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as q_sobrante');
			$this->db->select('c.pmp * (SUM(stock_sap) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as v_faltante');
			$this->db->select('c.pmp * (0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as v_coincidente');
			$this->db->select('c.pmp * (SUM(stock_fisico) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as v_sobrante');
		}

		$this->db->from($this->config->item('bd_detalle_inventario') . ' i');
		$this->db->join($this->config->item('bd_catalogos') . ' c', "c.catalogo = i.catalogo", 'left');
		$this->db->where('id_inventario', $id_inventario);

		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->having('(SUM(stock_sap) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) <> 0');
				$this->db->or_having('(SUM((stock_fisico+stock_ajuste)) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) <> 0');
			}
			else
			{
				$this->db->having('(SUM(stock_sap) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) <> 0');
				$this->db->or_having('(SUM(stock_fisico) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) <> 0');
			}
		}

		$this->db->group_by('i.catalogo, i.descripcion, i.um, c.pmp');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

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
		$this->db->select('stock_fisico * c.pmp as valor_fisico', FALSE);
		$this->db->select('stock_sap * c.pmp as valor_sap', FALSE);
		$this->db->select('stock_ajuste * c.pmp as valor_ajuste', FALSE);

		if ($incl_ajustes == '1')
		{
			$this->db->select('(stock_fisico - stock_sap + stock_ajuste) as stock_diff');
			$this->db->select('(stock_fisico - stock_sap + stock_ajuste) * c.pmp as valor_diff');
			$this->db->select('(stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante');
			$this->db->select('(0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente');
		}
		else
		{
			$this->db->select('(stock_fisico - stock_sap) as stock_diff');
			$this->db->select('(stock_fisico - stock_sap) * c.pmp as valor_diff');
			$this->db->select('(stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante');
			$this->db->select('(0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente');
		}

		$this->db->select('(stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante');
		$this->db->select('(0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente');
		$this->db->select('(stock_fisico - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_sobrante');
		$this->db->select('pmp * (stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_faltante');
		$this->db->select('pmp * (0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_coincidente');
		$this->db->select('pmp * (stock_fisico - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_sobrante');
		$this->db->from($this->config->item('bd_detalle_inventario') . ' i');
		$this->db->join($this->config->item('bd_catalogos') . ' c', "c.catalogo = i.catalogo", 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->where('i.catalogo', $catalogo);

		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}


		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por ubicaciones
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_ubicacion($id_inventario = 0, $orden_campo = 'ubicacion', $orden_tipo = 'ASC', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$this->db->select('di.ubicacion');

		$this->db->select_sum('stock_sap' , 'sum_stock_sap');
		$this->db->select_sum('stock_fisico' , 'sum_stock_fisico');

		if ($incl_ajustes == '1')
		{
			$this->db->select_sum('stock_ajuste' , 'sum_stock_ajuste');
			$this->db->select_sum('(stock_fisico - stock_sap + stock_ajuste)' , 'sum_stock_dif');
		}
		else
		{
			$this->db->select_sum('(stock_fisico - stock_sap)' , 'sum_stock_dif');
		}

		$this->db->select_sum('(stock_sap * c.pmp)' , 'sum_valor_sap');
		$this->db->select_sum('(stock_fisico * c.pmp)' , 'sum_valor_fisico');

		if ($incl_ajustes == '1')
		{
			$this->db->select_sum('(stock_ajuste * c.pmp)' , 'sum_valor_ajuste');
			$this->db->select_sum('(stock_fisico * c.pmp - stock_sap * c.pmp + stock_ajuste * pmp)' , 'sum_valor_dif');
		}
		else
		{
			$this->db->select_sum('(stock_fisico * c.pmp - stock_sap * c.pmp)' , 'sum_valor_dif');
		}

		$this->db->select_max('fecha_modificacion' , 'fecha');
		$this->db->from($this->config->item('bd_detalle_inventario') . ' di');
		$this->db->join($this->config->item('bd_catalogos') . ' c', 'c.catalogo = di.catalogo', 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->group_by('di.ubicacion');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

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
		$this->db->select_sum('(stock_sap * c.pmp)' , 'sum_valor_sap');
		$this->db->select_sum('(stock_fisico * c.pmp)' , 'sum_valor_fisico');

		if ($incl_ajustes == '0')
		{
			$this->db->select_sum('((stock_fisico-stock_sap) * c.pmp)' , 'sum_valor_diff');
		}
		else
		{
			$this->db->select_sum('(stock_ajuste * c.pmp)' , 'sum_valor_ajuste');
			$this->db->select_sum('((stock_fisico-stock_sap+stock_ajuste) * c.pmp)' , 'sum_valor_diff');
		}

		$this->db->select_max('fecha_modificacion' , 'fecha');

		$this->db->from($this->config->item('bd_detalle_inventario') . ' d');
		$this->db->join($this->config->item('bd_inventarios') . ' i', 'd.id_inventario=i.id', 'left');
		$this->db->join($this->config->item('bd_ubic_tipoubic') . ' ut', 'ut.tipo_inventario=i.tipo_inventario and ut.ubicacion = d.ubicacion', 'left');
		$this->db->join($this->config->item('bd_tipo_ubicacion') . ' t', 't.id=ut.id_tipo_ubicacion', 'left');
		$this->db->join($this->config->item('bd_catalogos') . ' c', "c.catalogo = d.catalogo", 'left');

		$this->db->where('d.id_inventario', $id_inventario);

		$this->db->group_by('t.tipo_ubicacion');
		$this->db->group_by('d.ubicacion');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		if ($elim_sin_dif == '1')
		{
			if ($incl_ajustes == '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}
		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

	public function get_reporte_ajustes($id_inventario = 0, $orden_campo = 'catalogo', $orden_tipo = 'ASC', $elim_sin_dif = '0')
	{
		$this->db->select('catalogo');
		$this->db->select('descripcion');
		$this->db->select('lote');
		$this->db->select('centro');
		$this->db->select('almacen');
		$this->db->select('ubicacion');
		$this->db->select('hoja');
		$this->db->select('um');
		$this->db->select('stock_sap');
		$this->db->select('stock_fisico');
		$this->db->select('stock_ajuste');
		$this->db->select('stock_fisico - stock_sap + stock_ajuste as stock_dif', FALSE);
		$this->db->select('CASE WHEN (stock_fisico - stock_sap + stock_ajuste) > 0 THEN \'SOBRANTE\' WHEN (stock_fisico - stock_sap + stock_ajuste) < 0 THEN \'FALTANTE\' WHEN (stock_fisico - stock_sap + stock_ajuste) = 0 THEN \'OK\' END as tipo', FALSE);
		$this->db->select('glosa_ajuste');
		$this->db->where('id_inventario', $id_inventario);
		if ($elim_sin_dif == 1)
		{
			$this->db->where('stock_fisico - stock_sap + stock_ajuste <> 0', NULL, FALSE);
		}
		else
		{
			$this->db->where('stock_fisico - stock_sap <> 0', NULL, FALSE);
		}

		$this->db->order_by('catalogo, lote, centro, almacen, ubicacion');
		//$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get($this->config->item('bd_detalle_inventario'))->result_array();
	}



}
/* End of file inventario_model.php */
/* Location: ./application/models/inventario_model.php */