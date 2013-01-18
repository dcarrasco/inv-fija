<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
		$this->load->helper('cookie');
	}


	/**
	 * Loguea un usuario en el sistema
	 * @param  string $usr usuario
	 * @param  string $pwd clave
	 * @return boolean     TRUE si se loguea al usuario, FALSE si no
	 */
	public function login($usr = '', $pwd = '')
	{
		if ($this->db->get_where('fija_usuarios', array('usr' => $usr, 'pwd' => sha1($pwd), 'activo' => '1'))->num_rows() > 0)
		{
			// crea cookies con la sesion del usuario
			$this->_set_session_cookies($usr);
			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Devuelve el menu de las aplicaciones del sistema
	 * @return string    Texto con el menu (<ul>) de las aplicaciones
	 */
	public function menu_app()
	{

		$arr_modulos = $this->get_menu_usuario($this->input->cookie('movistar_usr'));

		$app_ant = '';
		$menu = '<ul>';
		foreach ($arr_modulos as $modulo)
		{
			if ($modulo['app'] != $app_ant)
			{
				if ($app_ant != '')
				{
					$menu .= '</ul></li>';
				}
				$menu .= '<li><span class="button ' . $modulo['app_icono'] .'">' . $modulo['app'] . '</span>';
				$menu .= '<ul>';
			}
			$menu .= '<li>' . anchor($modulo['url'], $modulo['modulo'], 'class="button ' . $modulo['modulo_icono'] .'"');
			$app_ant = $modulo['app'];
		}
		$menu .= '</ul></li>';
		$menu .= '<li>' . anchor('login','Logout', 'class="button ic-logout"') . '</li>';
		$menu .= '</ul>';

		return $menu;
	}


	/**
	 * Devuelve el id del usuario, dado el nombre de usuario
	 * @param  string $usr Usuario
	 * @return string      ID del usuario
	 */
	public function get_id_usr($usr = '')
	{
		$user = ($usr == '') ? $this->input->cookie('movistar_usr') : $usr;
		$rs = $this->db->get_where('fija_usuarios', array('usr' => $user))->row_array();
		return (is_array($rs) ? $rs['id'] : '');
	}


	/**
	 * Devuelve arreglo con todos los modulos para un usuario
	 * @param  string $usr Usuario
	 * @return array       Arreglo con los módulos del usuario
	 */
	public function get_modulos_usuario($usr = '')
	{
		$this->db->distinct();
		$this->db->select('llave_modulo');
		$this->db->from('fija_usuarios u');
		$this->db->join('acl_usuario_rol ur', 'ur.id_usuario = u.id');
		$this->db->join('acl_rol_modulo rm',  'rm.id_rol     = ur.id_rol');
		$this->db->join('acl_modulo m',       'm.id          = rm.id_modulo');
		$this->db->where('usr', $usr);
		$arr_rs = $this->db->get()->result_array();

		$arr_modulos = array();
		foreach($arr_rs as $rs)
		{
			array_push($arr_modulos, $rs['llave_modulo']);
		}
		return $arr_modulos;
	}


	/**
	 * Devuelve el menu de módulos del usuario
	 * @param  string $usr Usuario
	 * @return array       Modulos asociados al usuario
	 */
	public function get_menu_usuario($usr = '')
	{
		$this->db->select('a.app, a.icono as app_icono, m.modulo, m.url, m.llave_modulo, m.icono as modulo_icono');
		$this->db->from('fija_usuarios u');
		$this->db->join('acl_usuario_rol ur', 'ur.id_usuario = u.id');
		$this->db->join('acl_rol_modulo rm',  'rm.id_rol     = ur.id_rol');
		$this->db->join('acl_modulo m',       'm.id          = rm.id_modulo');
		$this->db->join('acl_app a',          'a.id          = m.id_app');
		$this->db->where('usr', $usr);
		$this->db->order_by('a.orden, m.orden');
		return $this->db->get()->result_array();
	}


	/**
	 * Crea las cookies de login para un usuario
	 * @param string $usr Usuario
	 */
	private function _set_session_cookies($usr = '')
	{
		$this->input->set_cookie(array(
			'name'   => 'movistar_usr',
			'value'  => $usr,
			'expire' => '1200'
			));
	}


	/**
	 * Borra las cookies de login
	 * @return none
	 */
	public function delete_session_cookies()
	{
		delete_cookie('movistar_usr');
		delete_cookie('movistar_menu');
	}


	/**
	 * Autentica el uso de un módulo de la aplicacion
	 * @param  string $mod Modulo a autenticar
	 * @return none
	 */
	public function autentica($mod = '')
	{
		if (!$this->input->cookie('movistar_usr'))
		{
			redirect('login');
		}
		else
		{
			$arr_modulos = $this->get_modulos_usuario($this->input->cookie('movistar_usr'));
			if (!in_array($mod, $arr_modulos))
			{
				redirect('login');
			}
			else
			{
				// renueva la cookie de usuario (por timeout)
				$this->_set_session_cookies($this->input->cookie('movistar_usr'));
			}
		}
	}




/*
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

	public function get_total_usuario_rol()
	{
		return $this->db->count_all('acl_usuario_rol');
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

	public function get_all_usuario_rol($limit = 0, $offset = 0)
	{
		$this->db->order_by('u.nombre ASC, r.rol ASC');
		$this->db->join('fija_usuarios u', 'ur.id_usuario = u.id', 'left');
		$this->db->join('acl_rol r', 'ur.id_rol=r.id', 'left');
		$this->db->join('acl_app a', 'r.id_app=a.id', 'left');
		return $this->db->get('acl_usuario_rol ur', $limit, $offset)->result_array();
	}
*/


	/**
	 * Devuelve el nombre de un usuario
	 * @param  string $usr Usuario
	 * @return string      Nombre del usuario
	 */
	public function get_nombre_usuario($usr = '')
	{
		$arr_rs = $this->db->get_where('fija_usuarios', array('usr' => $usr))->row_array();
		if (count($arr_rs) > 0)
		{
			return $arr_rs['nombre'];
		}
		else
		{
			return '';
		}
	}


	/**
	 * Indica si un usuario tiene o no una clave
	 * @param  string $usr Usuario
	 * @return boolean     Indica si el usuario tiene o no clave
	 */
	public function tiene_clave($usr = '')
	{
		$arr_rs = $this->db->get_where('fija_usuarios', array('usr' => $usr))->row_array();
		if (count($arr_rs) > 0)
		{
			return ((is_null($arr_rs['pwd']) OR $arr_rs['pwd'] == ' ' OR $arr_rs['pwd'] == '') ? false : true);
		}
		else
		{
			return false;
		}
	}


	/**
	 * Indica si un usuario existe o no en el sistema
	 * @param  string $usr Usuario
	 * @return boolean     Indica si el usuario existe o no
	 */
	public function existe_usuario($usr = '')
	{
		$regs = $this->db->get_where('fija_usuarios', array('usr' => $usr))->num_rows();
		return (($regs > 0) ? true : false);
	}


	/**
	 * Cambia la clave de un usuario
	 * @param  string $usr       Usuario
	 * @param  string $clave_old Clave anterior
	 * @param  string $clave_new Nueva clave
	 * @return array             Estado de cambio de clave
	 */
	public function cambio_clave($usr = '', $clave_old = '', $clave_new = '')
	{
		$arr_rs = $this->db->get_where('fija_usuarios', array('usr' => $usr))->row_array();
		if (!$this->tiene_clave($usr))
		{
			$this->db->where('usr', $usr)->update('fija_usuarios', array('pwd' => sha1($clave_new)));
			return array(true);
		}
		else
		{
			if($this->db->get_where('fija_usuarios', array('usr' => $usr, 'pwd' => sha1($clave_old)))->num_rows() > 0)
			{
				$this->db->where('usr', $usr)->update('fija_usuarios', array('pwd' => sha1($clave_new)));
				return array(true);
			}
			else
			{
				return array(false, 'Clave anterior incorrecta');
			}
		}
	}


	/**
	 * Devuelve el correo de un usuario
	 * @param  string $usr Usuario
	 * @return string      Correo del usuario
	 */
	public function get_correo($usr = '')
	{
		$arr_rs = $this->db->get_where('fija_usuarios', array('usr' => $usr))->row_array();
		if (count($arr_rs) > 0)
		{
			return $arr_rs['correo'];
		}
		else
		{
			return '';
		}
	}


/*

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

	public function get_combo_rol()
	{
		$arr_rs = $this->db->order_by('app ASC, rol ASC')->join('acl_app a', 'r.id_app=a.id', 'left')->select('r.id, r.rol, a.app')->get('acl_rol r')->result_array();

		$arr_combo = array();
		$arr_combo[''] = 'Seleccione un rol...';

		foreach($arr_rs as $reg)
		{
			$arr_combo[$reg['id']] = $reg['app'] . ': ' . $reg['rol'];
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
		$this->db->delete('acl_rol_modulo', array('id_rol' => $id_rol));
		foreach ($id_modulos as $id_modulo)
		{
			$this->db->insert('acl_rol_modulo', array('id_rol' => $id_rol, 'id_modulo' => $id_modulo));
		}
	}

	public function guardar_usuario_rol($id_usuario = 0, $id_rol = 0)
	{
		$this->db->insert('acl_usuario_rol', array('id_usuario' => $id_usuario, 'id_rol' => $id_rol));
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

	public function borrar_usuario_rol($id_usuario = 0, $id_rol = 0)
	{
		$this->db->delete('acl_usuario_rol', array('id_usuario' => $id_usuario, 'id_rol' => $id_rol));
	}
*/


}

/* End of file acl_model.php */
/* Location: ./application/models/acl_model.php */