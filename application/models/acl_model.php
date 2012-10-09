<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}




	public function get_total_app() 
	{
		return $this->db->count_all('acl_app');
	}

	public function get_total_modulo() 
	{
		return $this->db->count_all('acl_modulo');
	}

	public function get_total_rol() 
	{
		return $this->db->count_all('acl_rol');
	}



	public function get_all_app($limit = 0, $offset = 0)
	{
		$this->db->order_by('id ASC');
		return $this->db->get('acl_app', $limit, $offset)->result_array();
	}

	public function get_all_modulo($limit = 0, $offset = 0)
	{
		$this->db->order_by('id_app ASC, modulo ASC');
		return $this->db->get('acl_modulo', $limit, $offset)->result_array();
	}

	public function get_all_rol($limit = 0, $offset = 0)
	{
		$this->db->order_by('id_app ASC, rol ASC');
		return $this->db->get('acl_rol', $limit, $offset)->result_array();
	}

	public function get_modulos_rol($id_rol = 0)
	{
		$arr_rs = $this->db->get_where('acl_rol_modulo', array('id_rol' => $id_rol))->result_array();

		$arr_modulos = array();
		foreach($arr_rs as $reg)
		{
			array_push($arr_modulos, $reg['id_modulo']);
		}

		return $arr_modulos;
	}


	public function get_combo_app()
	{
		$arr_rs = $this->db->order_by('app ASC')->get('acl_app')->result_array();
		
		$arr_combo = array();
		$arr_combo[''] = 'Seleccione una aplicacion...';

		foreach($arr_rs as $reg)
		{
			$arr_combo[$reg['id']] = $reg['app'];
		}

		return $arr_combo;
	}

	public function get_combo_modulo($id_app = 0)
	{
		$arr_rs = $this->db->order_by('modulo ASC')->get_where('acl_modulo', array('id_app' => $id_app))->result_array();
		
		$arr_combo = array();
		//$arr_combo[''] = 'Seleccione una aplicacion...';

		foreach($arr_rs as $reg)
		{
			$arr_combo[$reg['id']] = $reg['modulo'];
		}

		return $arr_combo;
	}


	public function get_cant_registros_app($id = 0)
	{
		return $this->db->get_where('acl_app', array('id'=>$id))->num_rows();
	}

	public function get_cant_registros_modulo($id = 0)
	{
		return $this->db->get_where('acl_rol_modulo', array('id_modulo'=>$id))->num_rows();
	}

	public function get_cant_registros_rol($id = 0)
	{
		return $this->db->get_where('acl_rol_modulo', array('id_rol'=>$id))->num_rows() + $this->db->get_where('acl_usuario_rol', array('id_rol'=>$id))->num_rows();
	}


	public function guardar_app($id = 0, $app = '', $descripcion = '', $url = '')
	{
		$arr_datos = array('app' => $app, 'descripcion' => $descripcion, 'url' => $url);
		if ($id == 0)
		{
			$this->db->insert('acl_app', $arr_datos);
		}
		else
		{
			$this->db->where('id', $id)->update('acl_app', $arr_datos);					
		}
	}

	public function guardar_modulo($id = 0, $id_app = 0, $modulo = '', $descripcion = '', $llave_modulo = '')
	{
		$arr_datos = array('id_app' => $id_app, 'modulo' => $modulo, 'descripcion' => $descripcion, 'llave_modulo' => $llave_modulo);
		if ($id == 0)
		{
			$this->db->insert('acl_modulo', $arr_datos);
		}
		else
		{
			$this->db->where('id', $id)->update('acl_modulo', $arr_datos);					
		}
	}

	public function guardar_rol($id = 0, $id_app = 0, $rol = '', $descripcion = '')
	{
		$arr_datos = array('id_app' => $id_app, 'rol' => $rol, 'descripcion' => $descripcion);
		if ($id == 0)
		{
			$this->db->insert('acl_rol', $arr_datos);
		}
		else
		{
			$this->db->where('id', $id)->update('acl_rol', $arr_datos);					
		}
	}

	public function guardar_rol_modulo($id_rol = 0, $id_modulos = array())
	{
		var_dump($id_modulos);
		$this->db->delete('acl_rol_modulo', array('id_rol' => $id_rol));
		foreach ($id_modulos as $id_modulo) 
		{
			$this->db->insert('acl_rol_modulo', array('id_rol' => $id_rol, 'id_modulo' => $id_modulo));
		}
	}


	public function borrar_app($id = 0)
	{
		$this->db->delete('acl_app', array('id' => $id));
	}

	public function borrar_modulo($id = 0)
	{
		$this->db->delete('acl_modulo', array('id' => $id));
	}

	public function borrar_rol($id = 0)
	{
		$this->db->delete('acl_rol', array('id' => $id));
	}


}

/* End of file acl_model.php */
/* Location: ./application/models/acl_model.php */