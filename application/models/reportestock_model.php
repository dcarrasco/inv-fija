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
	public function get_fecha_reporte()
	{
		$this->db->distinct();
		$this->db->select('convert(varchar(20), fecha_stock, 102) as fecha_stock');
		$arr_result = $this->db->get('bd_controles..ctrl00_equipos_cl15_permanencia as p')->row_array();
		if (count($arr_result) > 0)
		{
			return $arr_result['fecha_stock'];
		}
		else
		{
			return '';
		}
	}

	public function combo_tipo_alm()
	{
		$this->db->distinct();
		$this->db->select('t.id_tipo, t.tipo');
		$this->db->from('bd_logistica..cp_tipos_almacenes ta');
		$this->db->join('bd_logistica..cp_tiposalm t', 'ta.id_tipo=t.id_tipo', 'left');
		$this->db->where('ta.centro', 'CL15');
		$this->db->order_by('t.tipo');
		$arr_rs = $this->db->get()->result_array();

		$arr_combo = array();
		foreach($arr_rs as $reg)
		{
			$arr_combo[$reg['id_tipo']] = $reg['tipo'];
		}
		return $arr_combo;
	}


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
	public function get_reporte_permanencia($orden_campo = 't.tipo', $orden_tipo = 'ASC', $tipo_alm = array(), $estado_sap = array(),
										$incl_almacen = '0', $incl_lote = '0', $incl_estado = '0', $incl_modelos = '0')
	{

		$this->db->select('t.tipo');
		$this->db->select('count(case when p.dias<= 120 then 1 else null end) as m120');
		$this->db->select('count(case when p.dias > 120 and p.dias<= 150 then 1 else null end) as m150');
		$this->db->select('count(case when p.dias > 150 and p.dias<= 180 then 1 else null end) as m180');
		$this->db->select('count(case when p.dias > 180 and p.dias<= 360 then 1 else null end) as m360');
		$this->db->select('count(case when p.dias > 360 and p.dias<= 540 then 1 else null end) as m540');
		$this->db->select('count(case when p.dias > 540 and p.dias<= 720 then 1 else null end) as m720');
		$this->db->select('count(case when p.dias > 720 then 1 else null end) as mas720');
		$this->db->select('count(1) as \'total\'');
		$this->db->from('bd_controles..ctrl00_equipos_cl15_permanencia as p');
		$this->db->join('bd_logistica..cp_tipos_almacenes ta', 'ta.centro=p.centro and ta.cod_almacen=p.cod_almacen', 'left');
		$this->db->join('bd_logistica..cp_tiposalm t', 'ta.id_tipo=t.id_tipo', 'left');
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

		if (count($estado_sap) > 0 and is_array($estado_sap))
		{
			$this->db->where_in('p.estado_sap', $estado_sap);
		}

		if ($incl_almacen == '1')
		{
			$this->db->select('p.centro, p.cod_almacen, a.des_almacen');
			$this->db->join('bd_logistica..cp_almacenes a', 'p.centro=a.centro and p.cod_almacen=a.cod_almacen', 'left');
			$this->db->group_by('p.centro, p.cod_almacen, a.des_almacen');
			$this->db->order_by('p.centro, p.cod_almacen, a.des_almacen');
		}

		if ($incl_estado == '1')
		{
			$this->db->select('case p.estado_sap when \'01\' then \'01 Libre util\' when \'02\' then \'02 Control Calidad\' when \'03\' then \'03 Devol cliente\' when \'07\' then \'07 Bloqueado\' when \'06\' then \'06 Transito\' else p.estado_sap end as estado_sap');
			$this->db->group_by('p.estado_sap');
			$this->db->order_by('p.estado_sap');
		}

		if ($incl_lote == '1')
		{
			$this->db->select('p.lote');
			$this->db->group_by('p.lote');
			$this->db->order_by('p.lote');
		}

		if ($incl_modelos == '1')
		{
			$this->db->select('substring(p.cod_material_sap, 6, 2) + \' \' + substring(p.cod_material_sap, 8, 5) as modelo');
			$this->db->group_by('substring(p.cod_material_sap, 6, 2) + \' \' + substring(p.cod_material_sap, 8, 5)');
			$this->db->order_by('modelo');
		}

			return $this->db->get()->result_array();
	}




}

/* End of file reportestock_model.php */
/* Location: ./application/models/reportestock_model.php */