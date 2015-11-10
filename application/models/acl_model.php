<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Clase Modelo ACL
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Acl_model extends CI_Model {

	/**
	 * Reglas para formulario login
	 *
	 * @var array
	 */
	public $login_validation = array(
		array(
			'field' => 'usr',
			'label' => 'Usuario',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'pwd',
			'label' => 'Password',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'remember_me',
			'label' => 'Recordarme',
			'rules' => 'trim'
		),
	);

	public $change_password_validation = array(
		array(
			'field' => 'usr',
			'label' => 'Usuario',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'pwd_old',
			'label' => 'Clave Anterior',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'pwd_new1',
			'label' => 'Clave Nueva',
			'rules' => 'trim|required|min_length[8]|password_validation'
		),
		array(
			'field' => 'pwd_new2',
			'label' => 'Clave Nueva (reingreso)',
			'rules' => 'trim|required|matches[pwd_new1]'
		),
	);

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('captcha');
	}

	// --------------------------------------------------------------------

	/**
	 * Loguea un usuario en el sistema
	 *
	 * @param  string  $usuario  usuario
	 * @param  string  $password clave
	 * @param  boolean $remember Indica si el usuario será recordado
	 * @return boolean           TRUE si se loguea al usuario, FALSE si no
	 */
	public function login($usuario = '', $password = '', $remember = FALSE)
	{
		$this->delete_session_data();

		if ($this->check_user_credentials($usuario, $password))
		{

			// escribe auditoria del login
			$this->write_login_audit($usuario);

			// resetea intentos fallidos de login
			$this->reset_login_errors($usuario);

			// borra los captchas del usuario
			$this->delete_captchas_session($this->session->userdata('session_id'));

			// crea session con los datos del usuario
			$this->_set_session_data($usuario, $remember);

			return TRUE;
		}
		else
		{
			// incrementa intentos fallidos de login
			$this->add_login_errors($usuario);

			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el id del usuario, dado el nombre de usuario
	 *
	 * @param  string $usuario Usuario
	 * @return string      ID del usuario
	 */
	public function get_id_usr($usuario = '')
	{
		$user = ($usuario === '') ? $this->get_user() : $usuario;

		$registro = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $user))
			->row_array();

		return is_array($registro) ? $registro['id'] : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el usuario (leido desde la session)
	 *
	 * @return string Usuario
	 */
	public function get_user()
	{
		return (string) $this->session->userdata('user');
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el usuario (leido desde la session)
	 *
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
	 *
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
	 *
	 * @return boolean TRUE/FALSE dependiendo si el usuario está logueado o no
	 */
	public function is_user_remembered()
	{
		return $this->input->cookie('login_token') ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Genera el hash de una password
	 *
	 * @param  string $password password
	 * @param  string $salt     salt para mayor seguridad
	 * @return string Hash de la password
	 */
	private function _hash_password($password = '', $salt = '')
	{
		return crypt($password, '$2a$10$'.$salt.$this->config->item('encryption_key'));
	}

	// --------------------------------------------------------------------

	/**
	 * Chequea si las credenciales del usuario son validas
	 *
	 * @param string $usuario  Usuario a validar
	 * @param string $password Clave del usuario
	 * @return boolean TRUE/FALSE
	 */
	public function check_user_credentials($usuario = '', $password = '')
	{
		return $this->db
			->where(array(
				'usr'    => $usuario,
				'pwd'    => $this->_hash_password($password),
				'activo' => '1'
			))
			->get($this->config->item('bd_usuarios'))
			->num_rows() > 0;
	}

	// --------------------------------------------------------------------

	/**
	 * Escribe datos de auditoria al loguear al usuario
	 *
	 * @param  string $usuario Usuario a validar
	 * @return void
	 */
	public function write_login_audit($usuario = '')
	{
		$this->db
			->where('usr', $usuario)
			->update($this->config->item('bd_usuarios'), array(
				'fecha_login'  => date('Ymd H:i:s'),
				'ip_login'     => $this->input->ip_address(),
				'agente_login' => $this->input->user_agent(),
			));
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera imagen para usuar en el captcha
	 *
	 * Valida si el usuario tiene intentos fallidos de login, para desplegar imagen captcha
	 *
	 * @param  string $usuario Usuario a loguear
	 * @return string          Imagen captcha
	 */
	public function get_captcha_img($usuario = '')
	{
		if ( ! $this->use_captcha($usuario))
		{
			return '';
		}

		$captcha_config = array(
			'img_path'      => './img/captcha/',
			'img_url'       => base_url() . 'img/captcha/',
			'word'          => genera_captcha_word(),
			'img_width'     => 260,
			'img_height'    => 50,
			'font_size'     => 30,
		);

		$captcha = create_captcha($captcha_config);

		$this->_write_captcha(
			(string) $captcha['time'],
			(string) $this->session->session_id,
			(string) $captcha['word']
		);

		$this->form_validation->set_rules('captcha', 'Palabra distorsionada', 'trim|required');

		return $captcha['image'];
	}

	// --------------------------------------------------------------------

	/**
	 * Indica si se debe usar un captcha para el login del usuario
	 *
	 * @param  string $usuario Usuario a validar
	 * @return boolean         Indicador si se debe usar o no captcha
	 */
	public function use_captcha($usuario = '')
	{
		$row_user = $this->db
			->get_where($this->config->item('bd_usuarios'), array(
				'usr' => (string) $usuario,
			))
			->row();

		$login_attempts = isset($row_user) ? $row_user->login_errors : 0;

		return (boolean) ($login_attempts > 2);
	}

	// --------------------------------------------------------------------

	/**
	 * Escribe captcha en la base de datos
	 *
	 * @param string $time       Texto con fecha hora en segundos
	 * @param string $session_id Identificador de la sesion del usuario
	 * @param string $word       Palabra del captcha
	 * @return void
	 */
	private function _write_captcha($time = '', $session_id = '', $word = '')
	{
		$this->delete_captchas_session($session_id);

		$this->db
			->insert($this->config->item('bd_captcha'), array(
				'captcha_time' => (int) $time,
				'session_id'   => (string) $session_id,
				'word'         => (string) $word,
			));
	}

	// --------------------------------------------------------------------

	/**
	 * Borra captchas de un usuario
	 *
	 * @param string $session_id Identificador de la sesion del usuario
	 * @return void
	 */
	public function delete_captchas_session($session_id = '')
	{
		$this->db
			->where('session_id', $session_id)
			->delete($this->config->item('bd_captcha'));
	}

	// --------------------------------------------------------------------

	/**
	 * Borra captchas con tiempo mayor a 10 minutos
	 *
	 * @return void
	 */
	public function delete_old_captchas()
	{
		$this->db
			->where('captcha_time < ', time() - 60*10)
			->delete($this->config->item('bd_captcha'));
	}

	// --------------------------------------------------------------------

	/**
	 * Borra captchas con tiempo mayor a 10 minutos
	 *
	 * @param string $captcha    Palabra del captcha
	 * @param string $session_id Identificador de la sesion del usuario
	 * @return void
	 */
	public function validar_captcha($captcha = '', $session_id = '')
	{
		$row_captcha = $this->db
			->get_where($this->config->item('bd_captcha'),
				array(
					'session_id' => $session_id,
					'word'       => $captcha,
					'captcha_time >' => time() - 60*10,
				))
			->row();

		return isset($row_captcha);
	}

	// --------------------------------------------------------------------

	/**
	 * Resetea cantidad de intentos fallidos de login
	 *
	 * @param  string $usuario Usuario a resetear
	 * @return void
	 */
	public function reset_login_errors($usuario = '')
	{
		$this->db
			->where('usr', $usuario)
			->update($this->config->item('bd_usuarios'), array(
				'login_errors'  => 0,
			));
	}

	// --------------------------------------------------------------------

	/**
	 * Incrementa cantidad de intentos fallidos de login
	 *
	 * @param  string $usuario Usuario a incrementar
	 * @return void
	 */
	public function add_login_errors($usuario = '')
	{
		$this->db
			->set('login_errors', 'login_errors + 1', FALSE)
			->where('usr', $usuario)
			->update($this->config->item('bd_usuarios'));
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el menu del usuario desde la session. Si no existe session, la crea
	 *
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
	 *
	 * @param  string $usuario Usuario
	 * @return array       Arreglo con los módulos del usuario
	 */
	public function get_llaves_modulos($usuario = '')
	{
		$arr_rs = $this->db->distinct()
			->select('llave_modulo')
			->from($this->config->item('bd_usuarios') . ' u')
			->join($this->config->item('bd_usuario_rol') . ' ur', 'ur.id_usuario = u.id')
			->join($this->config->item('bd_rol_modulo') . ' rm', 'rm.id_rol = ur.id_rol')
			->join('acl_modulo m', 'm.id = rm.id_modulo')
			->where('usr', $usuario)
			->get()
			->result_array();

		return array_column($arr_rs, 'llave_modulo');
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el menu de módulos del usuario
	 *
	 * @param  string $usuario Usuario
	 * @return array       Modulos asociados al usuario
	 */
	public function get_menu_usuario($usuario = '')
	{
		return $this->db->distinct()
			->select('a.app, a.icono as app_icono, m.modulo, m.url, m.llave_modulo, m.icono as modulo_icono, a.orden, m.orden')
			->from($this->config->item('bd_usuarios') . ' u')
			->join($this->config->item('bd_usuario_rol') . ' ur', 'ur.id_usuario = u.id')
			->join($this->config->item('bd_rol_modulo') . ' rm', 'rm.id_rol = ur.id_rol')
			->join($this->config->item('bd_modulos') . ' m', 'm.id = rm.id_modulo')
			->join($this->config->item('bd_app') . ' a', 'a.id = m.id_app')
			->where('usr', (string) $usuario)
			->order_by('a.orden, m.orden')
			->get()
			->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Crea las session de menu para un usuario
	 *
	 * @param  string  $usuario  Usuario
	 * @param  boolean $remember Indica si el usuario será recordado
	 * @return void
	 */
	private function _set_session_data($usuario, $remember = FALSE)
	{
		$this->session->set_userdata(array(
			'user'        => $usuario,
			'modulos'     => json_encode($this->get_llaves_modulos($usuario)),
			'menu_app'    => json_encode($this->acl_model->get_menu_usuario($usuario)),
		));
	}

	// --------------------------------------------------------------------

	/**
	 * Borra las session de login
	 *
	 * @return void
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
	 *
	 * @return void
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
	 *
	 * @param  string $usuario Usuario
	 * @return void
	 */
	public function set_rememberme_cookie($usuario = '')
	{
		$token = substr(base64_encode(mcrypt_create_iv(32)), 0, 32);
		$salt  = substr(base64_encode(mcrypt_create_iv(32)), 0, 32);

		$expire = 60 * 60 * 24 * 31;   // fija la expiración en un mes

		// Graba la cookie
		$this->input->set_cookie(array(
			'name'  => 'login_token',
			'value' => json_encode(array(
							'usr'   => $usuario,
							'token' => $token,
						)),
			'expire' => $expire,
		));

		// Graba el registro en la BD
		$this->db
			->insert(
				$this->config->item('bd_pcookies'),
				array(
					'user_id'   => $usuario,
					'cookie_id' => $this->_hash_password($token, $salt),
					'expiry'    => date('Ymd H:i:s', time() + $expire),
					'salt'      => $salt,
				)
			);
	}

	// --------------------------------------------------------------------

	/**
	 * Loguea en el sistema cuando el usuario presenta cookie con token
	 *
	 * @return boolean TRUE o FALSE, dependiendo si loguea correctamente
	 */
	public function login_rememberme_cookie()
	{
		$user = $this->get_user_login_token($this->input->cookie('login_token'));

		if ($user)
		{
			// regenera token
			$this->set_rememberme_cookie($user);

			// escribe auditoria del login
			$this->write_login_audit($user);

			// crea session con los datos del usuario
			$this->_set_session_data($user);

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
	 *
	 * @param  string $usuario Usuario a recuperar
	 * @return string           Token del usuario
	 */
	public function get_user_login_token($token_cookie = '')
	{
		$token_object = json_decode($token_cookie);

		if ($token_object)
		{
			// elimina cookies antiguas
			$this->db
				->where('user_id', $token_object->usr)
				->where('expiry<', date('Ymd H:i:s'))
				->delete($this->config->item('bd_pcookies'));

			// recupera cookies almacenadas
			$reg_pcookies = $this->db
				->get_where($this->config->item('bd_pcookies'), array(
					'user_id'   => $token_object->usr,
					'cookie_id' => $token_object->token,
				))
				->result_array();

			foreach($reg_pcookies as $registro)
			{
				if ($registro['cookie_id'] === $this->_hash_password($token_object->token, $registro['salt']))
				{
					return $registro['user_id'];
				}
			}
		}

		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Autentica el uso de un módulo de la aplicacion
	 *
	 * @param  string $modulo Modulo a autenticar
	 * @return void
	 */
	public function autentica($modulo = '')
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

			if ( ! in_array($modulo, $arr_modulos))
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
	 *
	 * @param  string $usuario Usuario
	 * @return string      Nombre del usuario
	 */
	public function get_nombre_usuario($usuario = '')
	{
		$arr_rs = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $usuario))
			->row_array();

		return (count($arr_rs) > 0) ? $arr_rs['nombre'] : '';
	}

	// --------------------------------------------------------------------

	/**
	 * Indica si un usuario tiene o no una clave
	 *
	 * @param  string $usuario Usuario
	 * @return boolean     Indica si el usuario tiene o no clave
	 */
	public function tiene_clave($usuario = '')
	{
		$arr_rs = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $usuario))
			->row_array();

		if (count($arr_rs) > 0)
		{
			return (is_null($arr_rs['pwd']) OR trim($arr_rs['pwd']) === '') ? FALSE : TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Indica si un usuario existe o no en el sistema
	 *
	 * @param  string $usuario Usuario
	 * @return boolean     Indica si el usuario existe o no
	 */
	public function existe_usuario($usuario = '')
	{
		$regs = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $usuario))
			->num_rows();

		return ($regs > 0) ? TRUE : FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Cambia la clave de un usuario
	 *
	 * @param  string $usuario   Usuario
	 * @param  string $clave_old Clave anterior
	 * @param  string $clave_new Nueva clave
	 * @return array             Estado de cambio de clave
	 */
	public function cambio_clave($usuario = '', $clave_old = '', $clave_new = '')
	{
		$chk_usr = ( ! $this->tiene_clave($usuario)) ? TRUE : $this->check_user_credentials($usuario, $clave_old);

		if ($chk_usr)
		{
			$this->db
				->where('usr', $usuario)
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
	 * Recupera la app para redireccionar al usuario
	 *
	 * @return void
	 */
	public function get_redirect_app()
	{
		$arr_menu = $this->get_menu_usuario($this->get_user());

		if (count($arr_menu))
		{
			redirect($arr_menu[0]['url']);
		}
		else
		{
			redirect('login/');
		}

	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve el correo de un usuario
	 *
	 * @param  string $usuario Usuario
	 * @return string      Correo del usuario
	 */
	public function get_correo($usuario = '')
	{
		$arr_rs = $this->db
			->get_where($this->config->item('bd_usuarios'), array('usr' => $usuario))
			->row_array();

		return (count($arr_rs) > 0) ? $arr_rs['correo'] : '';
	}


}
/* End of file acl_model.php */
/* Location: ./application/models/acl_model.php */