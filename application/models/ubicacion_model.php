<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ubicacion_model extends CI_Model {


/*
CREATE TABLE bd_inventario.dbo.fija_detalle_inventario
(
id int identity(1,1) not null,
id_inventario int,
ubicacion   varchar(45),
catalogo    varchar(45),
descripcion varchar(100),
lote        varchar(45),
centro      varchar(10),
almacen     varchar(10),
um          varchar(10),
stock_sap   int,
stock_fisico int,
digitador   int,
auditor     int,
hoja        int,
reg_nuevo   char(1),
observacion varchar(100),
fecha_modificacion datetime,
primary key(id)
)
*/


	public function __construct()
	{
		parent::__construct();
	}



	public function total_ubicacion_tipo_ubicacion() 
	{
		return $this->db->count_all('fija_ubicacion_tipo_ubicacion');
	}



	public function get_tipos_ubicacion($limit = 0, $offset =0)
	{
		$this->db->order_by('tipo_inventario ASC, tipo_ubicacion ASC');
		return $this->db->get('fija_tipo_ubicacion', $limit, $offset)->result_array();
	}

	public function get_ubicacion_tipo_ubicacion($limit = 0, $offset =0)
	{
		$this->db->order_by('tipo_inventario ASC, id_tipo_ubicacion ASC, ubicacion ASC');
		return $this->db->get('fija_ubicacion_tipo_ubicacion', $limit, $offset)->result_array();
	}




	public function get_combo_tipos_ubicacion($tipo_inventario = '')
	{
		$arr_rs = $this->db->order_by('tipo_ubicacion')->get_where('fija_tipo_ubicacion', array('tipo_inventario'=>$tipo_inventario))->result_array();

		$arr_combo = array();
		$arr_combo[''] = 'Seleccione tipo de ubicacion...';
		foreach($arr_rs as $val)
		{
			$arr_combo[$val['id']] = $val['tipo_ubicacion'];
		}

		return $arr_combo;

	}



	public function get_ubicaciones_libres($tipo_inventario = '')
	{
		$this->db->distinct();
		$this->db->select('d.ubicacion');
		$this->db->from('fija_detalle_inventario d');
		$this->db->join('fija_inventario2 i','d.id_inventario=i.id');
		$this->db->join('fija_ubicacion_tipo_ubicacion u','d.ubicacion=u.ubicacion and i.tipo_inventario=u.tipo_inventario', 'left');
		$this->db->where('i.tipo_inventario', $tipo_inventario);
		$this->db->where('u.id_tipo_ubicacion is null');
		$this->db->order_by('d.ubicacion');
		$arr_rs = $this->db->get()->result_array();

		$arr_ubicaciones = array();
		foreach($arr_rs as $val)
		{
			$arr_ubicaciones[$val['ubicacion']] = $val['ubicacion'];
		}
		return ($arr_ubicaciones);
	}




	public function guardar_tipo_ubicacion($id = 0, $tipo_inventario = '', $tipo_ubicacion = '')
	{
		if ($id == 0)
		{
			$this->db->insert('fija_tipo_ubicacion', array('tipo_inventario' => $tipo_inventario, 'tipo_ubicacion' => $tipo_ubicacion));
		}
		else
		{
			$this->db->where('id', $id);
			$this->db->update('fija_tipo_ubicacion', array('tipo_inventario' => $tipo_inventario, 'tipo_ubicacion' => $tipo_ubicacion));
		}
	}

	public function guardar_ubicacion_tipo_ubicacion($id = 0, $tipo_inventario = '', $ubicacion = '', $id_tipo_ubicacion = 0)
	{
		if ($id == 0)
		{
			$this->db->insert('fija_ubicacion_tipo_ubicacion', array('tipo_inventario' => $tipo_inventario, 'ubicacion' => $ubicacion, 'id_tipo_ubicacion' => $id_tipo_ubicacion));
		}
		else
		{
			$this->db->where('id', $id);
			$this->db->update('fija_ubicacion_tipo_ubicacion', array('tipo_inventario' => $tipo_inventario, 'ubicacion' => $ubicacion, 'id_tipo_ubicacion' => $id_tipo_ubicacion));
		}
	}



	public function borrar_tipo_ubicacion($id = 0)
	{
		$this->db->delete('fija_tipo_ubicacion', array('id' => $id));
	}

	public function borrar_ubicacion_tipo_ubicacion($id = 0)
	{
		$this->db->delete('fija_ubicacion_tipo_ubicacion', array('id' => $id));
	}

	public function get_cant_registros_tipo_ubicacion($id = 0)
	{
		return $this->db->get_where('fija_ubicacion_tipo_ubicacion', array('id_tipo_ubicacion' => $id))->num_rows();
	}




}

/* End of file ubicacion_model.php */
/* Location: ./application/models/ubicacion_model.php */