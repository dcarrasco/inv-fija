<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuarios_model extends CI_Model {

/*
drop table fija_usuarios;
CREATE TABLE bd_inventario.dbo.fija_usuarios
(
id int identity(1,1) not null,
nombre      varchar(45),
tipo        varchar(10),
PRIMARY KEY(id)
);
*/


	public function __construct()
	{
		parent::__construct();
	}



	public function get_combo_usuarios($tipo = 'AUD')
	{
		$arr_result = array();
		$arr_combo = array();
	
		$arr_combo[''] = 'Seleccione un ';
		$arr_combo[''] .= ($tipo == 'AUD') ? 'auditor ...' : 'digitador ...';

		$this->db->order_by('nombre');
		$this->db->where('tipo', $tipo);
		$this->db->where('activo','1');
		$arr_result = $this->db->get('fija_usuarios')->result_array();

		foreach($arr_result as $reg)
		{
			$arr_combo[$reg['id']] = $reg['nombre'];
		}

		return $arr_combo;
	}



	public function get_usuarios($limit = 0, $offset = 0)
	{
		$this->db->order_by('tipo ASC, nombre ASC');

		return $this->db->get('fija_usuarios', $limit, $offset)->result_array();
	}



	public function guardar($id = 0, $tipo = '', $nombre = '', $activo = 0)
	{
		$activo = ($activo == null) ? 0 : $activo;
		
		if ($id == 0)
		{
			$this->db->insert('fija_usuarios', array('tipo' => $tipo, 'nombre' => $nombre, 'activo' => $activo ));

		}
		else
		{
			$this->db->where('id', $id);
			$this->db->update('fija_usuarios', array('tipo' => $tipo, 'nombre' => $nombre, 'activo' => $activo ));					
		}
	}



	public function total_usuarios() 
	{
		return $this->db->count_all('fija_usuarios');
	}


	public function borrar($id = 0)
	{
		$this->db->delete('fija_usuarios', array('id' => $id));
	}

	public function get_cant_registros_usuario($id = 0)
	{
		return ($this->db->get_where('fija_detalle_inventario', array('digitador' => $id))->num_rows() + 
				$this->db->get_where('fija_detalle_inventario', array('auditor' => $id))->num_rows());
	}



}

/* End of file usuarios_model.php */
/* Location: ./application/models/usuarios_model.php */