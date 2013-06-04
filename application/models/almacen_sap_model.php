<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Almacen_sap_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}


	public function get_combo_centros()
	{
		$arr_result = array();
		$arr_combo = array();
		$arr_combo[''] = 'Seleccionar centro ...';

		$this->db->distinct()->select('centro')->order_by('centro');
		$arr_result = $this->db->get('bd_logistica..cp_almacenes')->result_array();

		foreach($arr_result as $reg)
		{
			$arr_combo[$reg['centro']] = $reg['centro'];
		}

		return $arr_combo;
	}


	public function get_combo_almacenes($tipo_op = '')
	{
		$arr_result = array();
		$arr_combo = array();
		//$arr_combo[''] = 'Seleccionar almacenes ...';

		$this->db->order_by('centro, cod_almacen');
		//$arr_result = $this->db->get_where('bd_logistica..cp_almacenes', array('tipo_op'=>$tipo_op))->result_array();
		$arr_result = $this->db->get_where('cp_almacenes', array('tipo_op'=>$tipo_op))->result_array();

		foreach($arr_result as $reg)
		{
			$arr_combo[$reg['centro'] . '-' . $reg['cod_almacen']] = $reg['centro'] . ' - ' . $reg['cod_almacen'] . ' - ' . $reg['des_almacen'];
		}

		return $arr_combo;
	}

	public function get_combo_tiposalm($tipo_op = '')
	{
		$arr_result = array();
		$arr_combo = array();
		$arr_combo[''] = 'Seleccionar tipo almacen ...';

		$this->db->order_by('tipo');
		//$arr_result = $this->db->get_where('bd_logistica..cp_tiposalm', array('tipo_op' => $tipo_op))->result_array();
		$arr_result = $this->db->get_where('cp_tiposalm', array('tipo_op' => $tipo_op))->result_array();

		foreach($arr_result as $reg)
		{
			$arr_combo[$reg['id_tipo']] = $reg['tipo'];
		}

		return $arr_combo;
	}

	public function get_tiposalm($tipo_op = '', $grupo = 0)
	{
		if ($grupo == 0)
		{
			return $this->db->order_by('tipo')->get_where('bd_logistica..cp_tiposalm', array('tipo_op' => $tipo_op))->result_array();
		}
		else
		{
			return $this->db->where('id_tipo', $grupo)->get('bd_logistica..cp_tiposalm')->row_array();
		}

	}

	public function get_tipos_almacenes($grupo = 0)
	{
		$arr_result = array();
		$arr_tmp = $this->db->where('id_tipo', $grupo)->get('bd_logistica..cp_tipos_almacenes')->result_array();
		foreach($arr_tmp as $reg)
		{
			array_push($arr_result, $reg['centro'].'-'.$reg['cod_almacen']);
		}
		return $arr_result;
	}

	public function get_detalle_almacenes($grupo = 0)
	{
		if ($grupo != 0)
		{
			$this->db->where('id_tipo',$grupo);
		}
		$this->db->select('ta.id_tipo, ta.centro, ta.cod_almacen, a.des_almacen');
		$this->db->from('bd_logistica..cp_tipos_almacenes ta');
		$this->db->join('bd_logistica..cp_almacenes a','a.centro=ta.centro and a.cod_almacen=ta.cod_almacen','left');
		$arr_datos = $this->db->get()->result_array();

		$arr_result = array();
		foreach($arr_datos as $reg)
		{
			if (!array_key_exists($reg['id_tipo'], $arr_result))
			{
				$arr_result[$reg['id_tipo']] = array();
			}
			array_push($arr_result[$reg['id_tipo']], $reg);
		}
		return $arr_result;
	}

	public function get_grupos_almacenes($centro = '', $alm = '')
	{
		if ($centro != '' && $alm != '')
		{
			$this->db->where(array('centro' => $centro, 'cod_almacen' => $alm));
		}
		$this->db->select('ta.centro, ta.cod_almacen, ta.id_tipo, t.tipo');
		$this->db->from('bd_logistica..cp_tipos_almacenes ta');
		$this->db->join('bd_logistica..cp_tiposalm t', 't.id_tipo=ta.id_tipo');
		$arr_datos = $this->db->get()->result_array();

		$arr_result = array();
		foreach($arr_datos as $reg)
		{
			if (!array_key_exists($reg['centro'] . $reg['cod_almacen'], $arr_result))
			{
				$arr_result[$reg['centro'] . $reg['cod_almacen']] = array();
			}
			array_push($arr_result[$reg['centro'] . $reg['cod_almacen']], $reg);
		}
		return $arr_result;
	}


	public function grabar_tiposalm($tipo_op = '', $id_tipo = 0, $tipoalm = '')
	{
		if ($id_tipo == 0)
		{
			$this->db->insert('cp_tiposalm', array('tipo_op'=>$tipo_op, 'tipo' => $tipoalm));
		}
		else
		{
			$this->db->where(array('id_tipo' => $id_tipo));
			$this->db->update('bd_logistica..cp_tiposalm', array('tipo' => $tipoalm));
		}
	}

	public function grabar_tiposalmacenes($id_tipo = 0, $arr_alm = array())
	{
		if ($id_tipo != 0)
		{
			$this->db->delete('cp_tipos_almacenes', array('id_tipo' => $id_tipo));
			foreach($arr_alm as $reg)
			{
				$arr_datos = explode('-', $reg);
				$this->db->insert('bd_logistica..cp_tipos_almacenes', array('id_tipo' => $id_tipo, 'centro'=>$arr_datos[0], 'cod_almacen'=>$arr_datos[1]));
			}
		}
	}

	public function borrar_tiposalm($id_tipo = 0)
	{
		$this->db->delete('bd_logistica..cp_tipos_almacenes', array('id_tipo' => $id_tipo));
		$this->db->delete('bd_logistica..cp_tiposalm', array('id_tipo' => $id_tipo));
	}

	public function get_almacenes($tipo_op = '', $centro = '', $cod_alm = '')
	{
		if ($centro == '' && $cod_alm == '')
		{
			$this->db->order_by('centro, cod_almacen');
			return $this->db->get_where('bd_logistica..cp_almacenes',array('tipo_op'=>$tipo_op))->result_array();
		}
		else
		{
			return $this->db->get_where('bd_logistica..cp_almacenes', array('centro'=>$centro, 'cod_almacen' => $cod_alm))->row_array();
		}
	}

	public function grabar_almacenes($centro = '', $cod_almacen = '', $des_almacen = '', $uso_almacen = '', $responsable = '', $grupos = array(), $tipo_op)
	{
		// si el registro existe, lo modifica
		if ($this->db->where(array('centro'=>$centro, 'cod_almacen'=>$cod_almacen))->from('cp_almacenes')->count_all_results() == 1)
		{
			$this->db->where(array('centro'=>$centro, 'cod_almacen'=>$cod_almacen))->update('cp_almacenes', array('des_almacen'=>$des_almacen, 'uso_almacen' => $uso_almacen, 'responsable' => $responsable, 'tipo_op' => $tipo_op));
		}
		// si no existe, lo crea
		else
		{
			$this->db->insert('bd_logistica..cp_almacenes', array('centro' => $centro, 'cod_almacen' => $cod_almacen, 'des_almacen' => $des_almacen, 'uso_almacen' => $uso_almacen, 'responsable' => $responsable, 'tipo_op' => $tipo_op));
		}

		// modifica asociacion almacen -> tipos
		$this->db->delete('bd_logistica..cp_tipos_almacenes', array('centro' => $centro, 'cod_almacen' => $cod_almacen));
		foreach($grupos as $key => $val)
		{
			$this->db->insert('bd_logistica..cp_tipos_almacenes', array('id_tipo' => $val, 'centro'=>$centro, 'cod_almacen'=>$cod_almacen));
		}
	}


}

/* End of file almacen_sap_model.php */
/* Location: ./application/models/almacen_sap_model.php */