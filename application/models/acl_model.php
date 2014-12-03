<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();

		$this->load->helper('cookie');
		$this->load->library('encrypt');
	}


	// --------------------------------------------------------------------

	/**
	 * Loguea un usuario en el sistema
	 * @param  string $usr usuario
	 * @param  string $pwd clave
	 * @return boolean     TRUE si se loguea al usuario, FALSE si no
	 */
	public function login($usr = '', $pwd = '', $remember = FALSE)
	{
		$this->delete_session_cookies();

		if ($this->check_user_credentials($usr, $pwd))
		{

			// escribe auditoria del login
			$this->write_login_audit($usr);
			// crea cookie con la sesion del usuario
			$this->_set_session_cookies($usr, $remember);
			$this->_set_menu_cookies($usr, $remember);

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el id del usuario, dado el nombre de usuario
	 * @param  string $usr Usuario
	 * @return string      ID del usuario
	 */
	public function get_id_usr($usr = '')
	{
		$user = ($usr == '') ? $this->get_user() : $usr;
		$rs = $this->db->get_where($this->config->item('bd_usuarios'), array('usr' => $user))->row_array();
		return (is_array($rs) ? $rs['id'] : '');
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el usuario (leido desde cookie)
	 * @param  none
	 * @return string Usuario
	 */
	public function get_user()
	{
		return $this->encrypt->decode($this->input->cookie('user'));
	}


	// --------------------------------------------------------------------

	/**
	 * Chequea si el usuario esta logueado en el sistema
	 * @return boolean TRUE/FALSE dependiendo si el usuario está logueado o no
	 */
	public function is_user_logged()
	{
		return (($this->input->cookie('user') and $this->input->cookie('modulos')) ? TRUE : FALSE);
	}


	// --------------------------------------------------------------------

	/**
	 * Chequea si el usuario esta logueado en el sistema
	 * @return boolean TRUE/FALSE dependiendo si el usuario está logueado o no
	 */
	public function is_user_remembered()
	{
		return ($this->input->cookie('remember_me') == 'TRUE' ? TRUE : FALSE);
	}


	// --------------------------------------------------------------------

	/**
	 * Chequea si las credenciales del usuario son validas
	 * @return boolean TRUE/FALSE
	 */
	public function check_user_credentials($usr = '', $pwd = '')
	{
		return $this->db
			->where(array(
				'usr'    => $usr,
				'pwd'    => sha1($pwd),
				'activo' => '1'
			))
			->get($this->config->item('bd_usuarios'))
			->num_rows() > 0;
	}


	// --------------------------------------------------------------------

	/**
	 * Escribe datos de auditoria al loguear al usuario
	 * @return none
	 */
	public function write_login_audit($usr = '')
	{
		$this->db
			->where('usr', $usr)
			->update($this->config->item('bd_usuarios'), array(
				'fecha_login'  => date('Ymd H:i:s'),
				'ip_login'     => $this->input->ip_address(),
				'agente_login' => $this->input->user_agent(),
			));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el menu del usuario desde la cookie. Si no existe cookie, la crea
	 * @param  none
	 * @return string Menu del usuario en formato JSON
	 */
	public function get_user_menu()
	{
		if (!$this->input->cookie('menu_app'))
		{
			$this->_set_menu_cookies($this->get_user());
		}

		return json_decode($this->encrypt->decode($this->input->cookie('menu_app')), TRUE);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo con todos los modulos para un usuario
	 * @param  string $usr Usuario
	 * @return array       Arreglo con los módulos del usuario
	 */
	public function get_llaves_modulos($usr = '')
	{
		$arr_rs = $this->db->distinct()
			->select('llave_modulo')
			->from($this->config->item('bd_usuarios') . ' u')
			->join($this->config->item('bd_usuario_rol') . ' ur', 'ur.id_usuario = u.id')
			->join($this->config->item('bd_rol_modulo') . ' rm', 'rm.id_rol = ur.id_rol')
			->join('acl_modulo m', 'm.id = rm.id_modulo')
			->where('usr', $usr)
			->get()
			->result_array();

		$arr_modulos = array();
		foreach($arr_rs as $rs)
		{
			array_push($arr_modulos, $rs['llave_modulo']);
		}

		return $arr_modulos;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el menu de módulos del usuario
	 * @param  string $usr Usuario
	 * @return array       Modulos asociados al usuario
	 */
	public function get_menu_usuario($usr = '')
	{
		return $this->db->distinct()
			->select('a.app, a.icono as app_icono, m.modulo, m.url, m.llave_modulo, m.icono as modulo_icono, a.orden, m.orden')
			->from($this->config->item('bd_usuarios') . ' u')
			->join($this->config->item('bd_usuario_rol') . ' ur', 'ur.id_usuario = u.id')
			->join($this->config->item('bd_rol_modulo') . ' rm', 'rm.id_rol = ur.id_rol')
			->join($this->config->item('bd_modulos') . ' m', 'm.id = rm.id_modulo')
			->join($this->config->item('bd_app') . ' a', 'a.id = m.id_app')
			->where('usr', (string)$usr)
			->order_by('a.orden, m.orden')
			->get()
			->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Crea las cookies de login para un usuario
	 * @param string $usr Usuario
	 */
	private function _set_session_cookies($usr = '', $remember = FALSE)
	{
		$expire = $remember ? '0' : '1200';

		$this->input->set_cookie(array(
			'name'   => 'user',
			'value'  => $this->encrypt->encode($usr),
			'expire' => $expire,
		));
	}


	// --------------------------------------------------------------------

	/**
	 * Crea las cookies de menu para un usuario
	 * @param string $usr Usuario
	 */
	private function _set_menu_cookies($usr, $remember = FALSE)
	{
		$this->input->set_cookie(array(
			'name'   => 'modulos',
			'value'  => $this->encrypt->encode(json_encode($this->get_llaves_modulos($usr))),
			'expire' => '0'
		));

		$this->input->set_cookie(array(
			'name'   => 'menu_app',
			'value'  => $this->encrypt->encode(json_encode($this->acl_model->get_menu_usuario($usr))),
			'expire' => '0'
		));

		$this->input->set_cookie(array(
			'name'   => 'remember_me',
			'value'  => $remember ? 'TRUE' : 'FALSE',
			'expire' => '0',
		));
	}


	// --------------------------------------------------------------------

	/**
	 * Borra las cookies de login
	 * @return none
	 */
	public function delete_session_cookies()
	{
		delete_cookie('user');
		delete_cookie('remember_me');
		delete_cookie('modulos');
		delete_cookie('menu_app');
	}


	// --------------------------------------------------------------------

	/**
	 * Autentica el uso de un módulo de la aplicacion
	 * @param  string $mod Modulo a autenticar
	 * @return none
	 */
	public function autentica($mod = '')
	{
		if (!$this->input->cookie('user') or !$this->input->cookie('modulos'))
		{
			redirect('login');
		}
		else
		{
			$arr_modulos = json_decode($this->encrypt->decode($this->input->cookie('modulos')));

			if (!in_array($mod, $arr_modulos))
			{
				redirect('login');
			}
			else
			{
				// renueva la cookie de usuario (por timeout)
				$this->_set_session_cookies($this->get_user(), $this->is_user_remembered());
			}
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el nombre de un usuario
	 * @param  string $usr Usuario
	 * @return string      Nombre del usuario
	 */
	public function get_nombre_usuario($usr = '')
	{
		$arr_rs = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $usr))
			->row_array();

		return (count($arr_rs) > 0) ? $arr_rs['nombre'] : '';
	}


	// --------------------------------------------------------------------

	/**
	 * Indica si un usuario tiene o no una clave
	 * @param  string $usr Usuario
	 * @return boolean     Indica si el usuario tiene o no clave
	 */
	public function tiene_clave($usr = '')
	{
		$arr_rs = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $usr))
			->row_array();

		if (count($arr_rs) > 0)
		{
			return ((is_null($arr_rs['pwd']) OR trim($arr_rs['pwd']) == '') ? FALSE : TRUE);
		}
		else
		{
			return FALSE;
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Indica si un usuario existe o no en el sistema
	 * @param  string $usr Usuario
	 * @return boolean     Indica si el usuario existe o no
	 */
	public function existe_usuario($usr = '')
	{
		$regs = $this->db->get_where($this->config->item('bd_usuarios'), array('usr' => $usr))->num_rows();

		return (($regs > 0) ? TRUE : FALSE);
	}


	// --------------------------------------------------------------------

	/**
	 * Cambia la clave de un usuario
	 * @param  string $usr       Usuario
	 * @param  string $clave_old Clave anterior
	 * @param  string $clave_new Nueva clave
	 * @return array             Estado de cambio de clave
	 */
	public function cambio_clave($usr = '', $clave_old = '', $clave_new = '')
	{
		$chk_usr = (!$this->tiene_clave($usr)) ? TRUE : $this->check_user_credentials($usr, $clave_old);

		if ($chk_usr)
		{
			$this->db
				->where('usr', $usr)
				->update($this->config->item('bd_usuarios'), array('pwd' => sha1($clave_new)));

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el correo de un usuario
	 * @param  string $usr Usuario
	 * @return string      Correo del usuario
	 */
	public function get_correo($usr = '')
	{
		$arr_rs = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $usr))
			->row_array();

		return (count($arr_rs) > 0) ? $arr_rs['correo'] : '';
	}


}
/* End of file acl_model.php */
/* Location: ./application/models/acl_model.php */