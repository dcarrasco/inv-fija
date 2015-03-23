<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ubicacion_model extends CI_Model {



	public function __construct()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	public function total_ubicacion_tipo_ubicacion()
	{
		return $this->db->count_all($this->config->item('bd_ubic_tipoubic'));
	}


	// --------------------------------------------------------------------

	public function get_tipos_ubicacion($limit = 0, $offset =0)
	{
		return $this->db
			->order_by('tipo_inventario ASC, tipo_ubicacion ASC')
			->get($this->config->item('bd_tipo_ubicacion'), $limit, $offset)
			->result_array();
	}

	public function get_ubicacion_tipo_ubicacion($limit = 0, $offset =0)
	{
		return $this->db
			->order_by('tipo_inventario ASC, id_tipo_ubicacion ASC, ubicacion ASC')
			->get($this->config->item('bd_ubic_tipoubic'), $limit, $offset)
			->result_array();
	}


	// --------------------------------------------------------------------

	public function get_combo_tipos_ubicacion($tipo_inventario = '')
	{
		$arr_rs = $this->db
			->order_by('tipo_ubicacion')
			->where('tipo_inventario', $tipo_inventario)
			->get($this->config->item('bd_tipo_ubicacion'))
			->result_array();

		$arr_combo = array();
		$arr_combo[''] = 'Seleccione tipo de ubicacion...';

		foreach($arr_rs as $val)
		{
			$arr_combo[$val['id']] = $val['tipo_ubicacion'];
		}

		return $arr_combo;

	}


	// --------------------------------------------------------------------

	public function get_ubicaciones_libres($tipo_inventario = '')
	{
		$arr_rs = $this->db
			->distinct()
			->select('d.ubicacion')
			->from($this->config->item('bd_detalle_inventario') . ' d')
			->join($this->config->item('bd_inventarios') . ' i','d.id_inventario=i.id')
			->join($this->config->item('bd_ubic_tipoubic') . ' u','d.ubicacion=u.ubicacion and i.tipo_inventario=u.tipo_inventario', 'left')
			->where('i.tipo_inventario', $tipo_inventario)
			->where('u.id_tipo_ubicacion is null')
			->order_by('d.ubicacion')
			->get()->result_array();

		$arr_ubicaciones = array();

		foreach($arr_rs as $val)
		{
			$arr_ubicaciones[$val['ubicacion']] = $val['ubicacion'];
		}

		return $arr_ubicaciones;
	}


	// --------------------------------------------------------------------

	public function guardar_tipo_ubicacion($id = 0, $tipo_inventario = '', $tipo_ubicacion = '')
	{
		if ($id == 0)
		{
			$this->db->insert(
				$this->config->item('bd_tipo_ubicacion'),
				array(
					'tipo_inventario' => $tipo_inventario,
					'tipo_ubicacion' => $tipo_ubicacion,
				)
			);
		}
		else
		{
			$this->db
				->where('id', $id)
				->update(
					$this->config->item('bd_tipo_ubicacion'),
					array(
						'tipo_inventario' => $tipo_inventario,
						'tipo_ubicacion' => $tipo_ubicacion,
					)
				);
		}
	}


	// --------------------------------------------------------------------

	public function guardar_ubicacion_tipo_ubicacion($id = 0, $tipo_inventario = '', $ubicacion = '', $id_tipo_ubicacion = 0)
	{
		if ($id == 0)
		{
			$this->db
				->insert(
					$this->config->item('bd_ubic_tipoubic'),
					array(
						'tipo_inventario'   => $tipo_inventario,
						'ubicacion'         => $ubicacion,
						'id_tipo_ubicacion' => $id_tipo_ubicacion,
					)
				);
		}
		else
		{
			$this->db
				->where('id', $id)
				->update(
					$this->config->item('bd_ubic_tipoubic'),
					array(
						'tipo_inventario'   => $tipo_inventario,
						'ubicacion'         => $ubicacion,
						'id_tipo_ubicacion' => $id_tipo_ubicacion,
					)
				);
		}
	}


	// --------------------------------------------------------------------

	public function borrar_tipo_ubicacion($id = 0)
	{
		$this->db->delete($this->config->item('bd_tipo_ubicacion'), array('id' => $id));
	}


	// --------------------------------------------------------------------

	public function borrar_ubicacion_tipo_ubicacion($id = 0)
	{
		$this->db->delete($this->config->item('bd_ubic_tipoubic'), array('id' => $id));
	}


	// --------------------------------------------------------------------

	public function get_cant_registros_tipo_ubicacion($id = 0)
	{
		return $this->db
			->get_where($this->config->item('bd_ubic_tipoubic'), array('id_tipo_ubicacion' => $id))
			->num_rows();
	}


}
/* End of file ubicacion_model.php */
/* Location: ./application/models/ubicacion_model.php */