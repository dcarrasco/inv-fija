<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
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
		$this->delete_session_data();

		if ($this->check_user_credentials($usr, $pwd))
		{

			// escribe auditoria del login
			$this->write_login_audit($usr);

			// crea session con los datos del usuario
			$this->_set_session_data($usr, $remember);

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

		$rs = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $user))
			->row_array();

		return is_array($rs) ? $rs['id'] : '';
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el usuario (leido desde la session)
	 * @param  none
	 * @return string Usuario
	 */
	public function get_user()
	{
		return (string) $this->session->userdata('user');
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el usuario (leido desde la session)
	 * @param  none
	 * @return string Usuario
	 */
	public function get_user_firstname()
	{
		$nombre = $this->db
			->where('usr', $this->get_user())
			->get($this->config->item('bd_usuarios'))
			->row()
			->nombre;

		return strpos($nombre, ' ') ? substr($nombre, 0, strpos($nombre, ' ')) : $nombre;
	}


	// --------------------------------------------------------------------

	/**
	 * Chequea si el usuario esta logueado en el sistema
	 * @return boolean TRUE/FALSE dependiendo si el usuario está logueado o no
	 */
	public function is_user_logged()
	{
		if ($this->session->userdata('user') AND $this->session->userdata('modulos'))
		{
			return TRUE;
		}
		else
		{
			if ($this->is_user_remembered())
			{
				return $this->login_rememberme_cookie();
			}
			else
			{
				return FALSE;
			}
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Chequea si el usuario esta logueado en el sistema
	 * @return boolean TRUE/FALSE dependiendo si el usuario está logueado o no
	 */
	public function is_user_remembered()
	{
		return ($this->input->cookie('login_user') AND  $this->input->cookie('login_token')) ? TRUE : FALSE;
	}


	// --------------------------------------------------------------------

	/**
	 * Genera el hash de una password
	 * @param  string $pwd password
	 * @return string Hash de la password
	 */
	private function _hash_password($pwd = '')
	{
		return crypt($pwd, '$2a$10$'.$this->config->item('encryption_key'));
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
				'pwd'    => $this->_hash_password($pwd),
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
	 * Devuelve el menu del usuario desde la session. Si no existe session, la crea
	 * @param  none
	 * @return string Menu del usuario en formato JSON
	 */
	public function get_user_menu()
	{
		if ( ! $this->session->userdata('menu_app'))
		{
			$this->_set_session_data($this->get_user());
		}

		return json_decode($this->session->userdata('menu_app'), TRUE);
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

		return array_column($arr_rs, 'llave_modulo');
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
			->where('usr', (string) $usr)
			->order_by('a.orden, m.orden')
			->get()
			->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Crea las session de menu para un usuario
	 * @param string $usr Usuario
	 */
	private function _set_session_data($usr, $remember = FALSE)
	{
		$this->session->set_userdata(array(
			'user'        => $usr,
			'modulos'     => json_encode($this->get_llaves_modulos($usr)),
			'menu_app'    => json_encode($this->acl_model->get_menu_usuario($usr)),
		));
	}


	// --------------------------------------------------------------------

	/**
	 * Borra las session de login
	 * @return none
	 */
	public function delete_session_data()
	{
		$this->session->unset_userdata('user');
		$this->session->unset_userdata('remember_me');
		$this->session->unset_userdata('modulos');
		$this->session->unset_userdata('menu_app');
	}


	// --------------------------------------------------------------------

	/**
	 * Borra las cookies de login
	 * @return none
	 */
	public function delete_cookie_data()
	{
		$this->input->set_cookie(array(
			'name'  => 'login_token',
			'value' => '',
			'expire' => '',
		));

		$this->input->set_cookie(array(
			'name'  => 'login_user',
			'value' => '',
			'expire' => '',
		));
	}


	// --------------------------------------------------------------------

	/**
	 * Genera cookie con token y usuario para login con remember-me
	 * @param  string $usr Usuario
	 * @return none
	 */
	public function set_rememberme_cookie($usr = '')
	{
		$token = substr(base64_encode(mcrypt_create_iv(32)), 0, 32);

		$expire = 60 * 60 * 24 * 31;   // fija la expiración en un mes

		$this->input->set_cookie(array(
			'name'  => 'login_token',
			'value' => $token,
			'expire' => $expire,
		));

		$this->input->set_cookie(array(
			'name'  => 'login_user',
			'value' => $usr,
			'expire' => $expire,
		));

		$this->db
			->where('usr', $usr)
			->update(
				$this->config->item('bd_usuarios'),
				array('remember_token' => $this->_hash_password($token))
			);
	}


	// --------------------------------------------------------------------

	/**
	 * Loguea en el sistema cuando el usuario presenta cookie con token
	 * @param  none
	 * @return boolean               TRUE o FALSE, dependiendo si loguea correctamente
	 */
	public function login_rememberme_cookie()
	{
		$stored_login_token = $this->get_stored_login_token($this->input->cookie('login_user'));

		if ($stored_login_token == $this->_hash_password($this->input->cookie('login_token')))
		{
			// regenera token
			$this->set_rememberme_cookie($this->input->cookie('login_user'));

			// escribe auditoria del login
			$this->write_login_audit($this->input->cookie('login_user'));

			// crea session con los datos del usuario
			$this->_set_session_data($this->input->cookie('login_user'));

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera token de la base de datos
	 * @param  string   $usr    Usuario a recuperar
	 * @return string           Token del usuario
	 */
	public function get_stored_login_token($usr = '')
	{
		$rs = $this->db
			->where(array(
				'usr'    => $usr,
				'activo' => '1'
			))
			->get($this->config->item('bd_usuarios'))
			->row_array();

		return (array_key_exists('remember_token', $rs)) ? $rs['remember_token'] : NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Autentica el uso de un módulo de la aplicacion
	 * @param  string $mod Modulo a autenticar
	 * @return none
	 */
	public function autentica($mod = '')
	{
		if ( ! $this->session->userdata('user') OR  ! $this->session->userdata('modulos'))
		{
			if ( ! $this->input->cookie('login_token') OR  ! $this->input->cookie('login_user'))
			{
				redirect('login');
			}
			else
			{
				return $this->login_rememberme_cookie();
			}
		}
		else
		{
			$arr_modulos = json_decode($this->session->userdata('modulos'));

			if ( ! in_array($mod, $arr_modulos))
			{
				redirect('login');
			}
			else
			{
				return TRUE;
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
			return (is_null($arr_rs['pwd']) OR trim($arr_rs['pwd']) == '') ? FALSE : TRUE;
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
		$regs = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $usr))
			->num_rows();

		return ($regs > 0) ? TRUE : FALSE;
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
		$chk_usr = ( ! $this->tiene_clave($usr)) ? TRUE : $this->check_user_credentials($usr, $clave_old);

		if ($chk_usr)
		{
			$this->db
				->where('usr', $usr)
				->update($this->config->item('bd_usuarios'), array('pwd' => $this->_hash_password($clave_new)));

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