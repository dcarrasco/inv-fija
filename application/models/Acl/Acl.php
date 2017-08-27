<?php

namespace Acl;

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

use \Orm_Model;
use \Orm_Field;
use \CaptchaWords;

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
class Acl extends Orm_Model {

	/**
	 * Reglas para formulario login
	 *
	 * @var array
	 */
	public $rules_login = [
		[
			'field' => 'usr',
			'label' => 'Usuario',
			'rules' => 'trim|required'
		],
		[
			'field' => 'pwd',
			'label' => 'Password',
			'rules' => 'trim|required'
		],
		[
			'field' => 'remember_me',
			'label' => 'Recordarme',
			'rules' => 'trim'
		],
	];

	/**
	 * Reglas para formulario login, campo captcha
	 *
	 * @var array
	 */
	public $rules_captcha = [[
		'field' => 'captcha',
		'label' => 'Palabra distorsionada',
		'rules' => 'trim|required'
	]];

	/**
	 * Reglas para formulario cambiar password
	 *
	 * @var array
	 */
	public $change_password_validation = [
		[
			'field' => 'usr',
			'label' => 'Usuario',
			'rules' => 'trim|required'
		],
		[
			'field' => 'pwd_old',
			'label' => 'Clave Anterior',
			'rules' => 'trim|required'
		],
		[
			'field' => 'pwd_new1',
			'label' => 'Clave Nueva',
			'rules' => 'trim|required|min_length[8]|password_validation'
		],
		[
			'field' => 'pwd_new2',
			'label' => 'Clave Nueva (reingreso)',
			'rules' => 'trim|required|matches[pwd_new1]'
		],
	];

	/**
	 * Cantidad de veces que el usuario puede loguearse erroneamente,
	 * antes de requerir un captcha
	 *
	 * @var integer
	 */
	public $login_failed_attempts = 2;

	/**
	 * Tiempo de duración de un captcha
	 * (10 minutos = 60 * 10 = 600)
	 *
	 * @var integer
	 */
	public $captcha_duration = 600;

	/**
	 * Tiempo de duración de la cookie remember_me
	 * (1 mes = 60*60*24*31 = 2.678.400)
	 *
	 * @var integer
	 */
	public $rememberme_cookie_duration = 2678400;

	/**
	 * Algoritmo y rondas para realizar hash de passwords
	 * Valor por defecto $2a$10$ ($2a$ --> blowfish; 10 --> rondas)
	 * Otros algoritmos
	 *   $2a$, $2x$, $2y$
	 *
	 * @var string
	 */
	private $_crypt_algo = '$2a$10$';

	// --------------------------------------------------------------------

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
			$this->_write_login_audit($usuario);

			// resetea intentos fallidos de login
			$this->_reset_login_errors($usuario);

			// borra los captchas del usuario
			$this->_delete_captchas_session($this->session->userdata('session_id'));

			// crea session con los datos del usuario
			$this->_set_session_data($usuario, $remember);

			// fijamos el token
			if ($remember)
			{
				$this->set_rememberme_cookie($usuario);
			}

			return TRUE;
		}
		else
		{
			// incrementa intentos fallidos de login
			$this->_inc_login_errors($usuario);

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
		$user = empty($usuario) ? $this->get_user() : $usuario;

		$registro = $this->db
			->get_where(config('bd_usuarios'), ['username' => $user])
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
		if ( ! $this->session->userdata('user_firstname'))
		{
			$usuario = $this->db
				->where('username', $this->get_user())
				->get(config('bd_usuarios'))
				->row();

			$nombre = $usuario ? $usuario->nombre : '';
			$user_firstname = strpos($nombre, ' ') ? substr($nombre, 0, strpos($nombre, ' ')) : $nombre;

			$this->session->set_userdata(compact('user_firstname'));
		}
		else
		{
			$user_firstname = $this->session->userdata('user_firstname');
		}

		return $user_firstname;
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

		if ($this->_is_user_remembered())
		{
			return $this->_login_rememberme_cookie();
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Chequea si el usuario esta logueado en el sistema
	 *
	 * @return boolean TRUE/FALSE dependiendo si el usuario está logueado o no
	 */
	private function _is_user_remembered()
	{
		return $this->input->cookie('login_token') ? TRUE : FALSE;
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
		$reg_usuario = $this->db
			->where('username', $usuario)
			->where('activo', 1)
			->get(config('bd_usuarios'))
			->row();

		if ($reg_usuario)
		{
			return $this->_valida_password($password, $reg_usuario->password);
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Escribe datos de auditoria al loguear al usuario
	 *
	 * @param  string $usuario Usuario a validar
	 * @return void
	 */
	private function _write_login_audit($usuario = '')
	{
		$this->db
			->where('username', $usuario)
			->update(config('bd_usuarios'), [
				'fecha_login'  => date('Y-m-d H:i:s'),
				'ip_login'     => $this->input->ip_address(),
				'agente_login' => $this->input->user_agent(),
			]);
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

		$captcha_config = [
			'img_path'   => './img/captcha/',
			'img_url'    => base_url().'img/captcha/',
			'word'       => CaptchaWords::word(),
			'img_width'  => 260,
			'img_height' => 50,
			'font_size'  => 30,
		];

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
			->get_where(config('bd_usuarios'), [
				'username' => (string) $usuario,
			])->row();

		$login_attempts = isset($row_user) ? (int) $row_user->login_errors : 0;

		return (boolean) ($login_attempts > $this->login_failed_attempts);
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
		$this->_delete_captchas_session($session_id);

		$this->db
			->insert(config('bd_captcha'), [
				'captcha_time' => (int) $time,
				'session_id'   => (string) $session_id,
				'word'         => (string) $word,
			]);
	}

	// --------------------------------------------------------------------

	/**
	 * Borra captchas de un usuario
	 *
	 * @param string $session_id Identificador de la sesion del usuario
	 * @return void
	 */
	private function _delete_captchas_session($session_id = '')
	{
		$this->db
			->where('session_id', $session_id)
			->delete(config('bd_captcha'));
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
			->where('captcha_time < ', time() - $this->captcha_duration)
			->delete(config('bd_captcha'));
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
			->get_where(config('bd_captcha'), [
				'session_id' => $session_id,
				'word'       => $captcha,
				'captcha_time >' => time() - $this->captcha_duration,
			])->row();

		return isset($row_captcha);
	}

	// --------------------------------------------------------------------

	/**
	 * Resetea cantidad de intentos fallidos de login
	 *
	 * @param  string $usuario Usuario a resetear
	 * @return void
	 */
	private function _reset_login_errors($usuario = '')
	{
		$this->db
			->where('username', $usuario)
			->update(config('bd_usuarios'), [
				'login_errors'  => 0,
			]);
	}

	// --------------------------------------------------------------------

	/**
	 * Incrementa cantidad de intentos fallidos de login
	 *
	 * @param  string $usuario Usuario a incrementar
	 * @return void
	 */
	private function _inc_login_errors($usuario = '')
	{
		$this->db
			->set('login_errors', 'login_errors + 1', FALSE)
			->where('username', $usuario)
			->update(config('bd_usuarios'));
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
	 * Devuelve el menu de módulos del usuario
	 *
	 * @param  string $usuario Usuario
	 * @return array       Modulos asociados al usuario
	 */
	private function _get_menu_usuario($usuario = '')
	{
		return $this->db->distinct()
			->select('a.app, a.icono as app_icono, m.modulo, m.url, m.llave_modulo, m.icono as modulo_icono, a.orden, m.orden')
			->from(config('bd_usuarios').' u')
			->join(config('bd_usuario_rol').' ur', 'ur.id_usuario = u.id')
			->join(config('bd_rol_modulo').' rm', 'rm.id_rol = ur.id_rol')
			->join(config('bd_modulos').' m', 'm.id = rm.id_modulo')
			->join(config('bd_app').' a', 'a.id = m.id_app')
			->where('username', (string) $usuario)
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
		$this->session->set_userdata([
			'user'     => $usuario,
			'modulos'  => json_encode($this->_get_llaves_modulos($usuario)),
			'menu_app' => json_encode($this->_get_menu_usuario($usuario)),
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo con todos los modulos para un usuario
	 *
	 * @param  string $usuario Usuario
	 * @return array           Arreglo con los módulos del usuario
	 */
	private function _get_llaves_modulos($usuario = '')
	{
		$arr_rs = $this->db->distinct()
			->select('llave_modulo')
			->from(config('bd_usuarios').' u')
			->join(config('bd_usuario_rol').' ur', 'ur.id_usuario = u.id')
			->join(config('bd_rol_modulo').' rm', 'rm.id_rol = ur.id_rol')
			->join('acl_modulo m', 'm.id = rm.id_modulo')
			->where('username', $usuario)
			->get()
			->result_array();

		return array_column($arr_rs, 'llave_modulo');
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
		$this->session->unset_userdata('user_firstname');
	}

	// --------------------------------------------------------------------

	/**
	 * Borra las cookies de login
	 *
	 * @return void
	 */
	public function delete_cookie_data()
	{
		$this->input->set_cookie([
			'name'   => 'login_token',
			'value'  => '',
			'expire' => '',
		]);
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
		$token = substr(base64_encode(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)), 0, 32);
		$salt  = $this->_create_salt();

		// Graba la cookie que recuerda la sesion
		$this->input->set_cookie([
			'name'   => 'login_token',
			'value'  => json_encode(['usr' => $usuario, 'token' => $token]),
			'expire' => $this->rememberme_cookie_duration,
		]);

		// Graba el registro en la BD
		$this->db->insert(config('bd_pcookies'), [
			'user_id'   => $usuario,
			'cookie_id' => $this->_hash_password($token, $salt),
			'expiry'    => date('Y-m-d H:i:s', time() + $this->rememberme_cookie_duration),
			'salt'      => $salt,
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Crea una cadena de texto para ser usada como salt
	 *
	 * @param  integer $largo Largo de la cadena a devolver
	 * @return string         Cadena de texto salt
	 */
	private function _create_salt($largo = 22)
	{
		$salt = '';
		$caracteres_validos = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		for ($i = 0; $i < $largo; $i++)
		{
			$salt .= $caracteres_validos[rand(0, strlen($caracteres_validos) - 1)];
		}

		return $this->_crypt_algo.$salt;
	}

	// --------------------------------------------------------------------

	/**
	 * Valida una password contra un texto hash
	 *
	 * @param  string $password Password a validar
	 * @param  string $hash     Texto hash a comparar con la password
	 * @return boolean          Indica si la password valida contra el hash
	 */
	private function _valida_password($password = '', $hash = '')
	{
		return $this->_hash_password($password, $hash) === $hash;
	}

	// --------------------------------------------------------------------

	/**
	 * Genera el hash de una password
	 *
	 * @param  string $password password
	 * @param  string $salt     salt para mayor seguridad
	 * @return string           Hash de la password
	 */
	private function _hash_password($password = '', $salt = '')
	{
		$salt = (empty($salt) ? $this->_create_salt() : $salt).config('encryption_key');

		return crypt($password, $salt);
	}

	// --------------------------------------------------------------------

	/**
	 * Reemplaza las cookie remember_me y el registro en la BD
	 * con un nuevo token
	 *
	 * @return void
	 */
	private function _delete_rememberme_cookie()
	{
		$token_object = json_decode($this->input->cookie('login_token'));

		if ($token_object)
		{
			// recupera cookies almacenadas
			$reg_pcookies = $this->db
				->get_where(config('bd_pcookies'), [
					'user_id' => $token_object->usr,
				])->result_array();

			$where = collect($reg_pcookies)
				->map(function($cookie) {
					return collect($cookie);
				})->first(function($pcookie) use ($token_object) {
					return $this->_valida_password($token_object->token, $pcookie->get('cookie_id'));
				})->only(['user_id', 'cookie_id'])
				->all();

			// eliminamos la cookie utilizada en la BD
			$this->db->where($where)
				->delete(config('bd_pcookies'));

			// borramos la cookie del browser
			$this->delete_cookie_data();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Loguea en el sistema cuando el usuario presenta cookie con token
	 *
	 * @return boolean TRUE o FALSE, dependiendo si loguea correctamente
	 */
	private function _login_rememberme_cookie()
	{
		$user = $this->_get_user_login_token();

		if ($user)
		{
			// borra token anterior
			$this->_delete_rememberme_cookie($user);

			// volvemos a definir nuevo token
			$this->set_rememberme_cookie($user);

			// escribe auditoria del login
			$this->_write_login_audit($user);

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
	 * @return string               ID usuario token
	 */
	private function _get_user_login_token()
	{
		$token_object = json_decode($this->input->cookie('login_token'));

		if ( ! $token_object)
		{
			return NULL;
		}

		// elimina cookies antiguas
		$this->db
			// ->where('user_id', $token_object->usr)
			->where('expiry<', date('Y-m-d H:i:s'))
			->delete(config('bd_pcookies'));

		// recupera cookies almacenadas
		$reg_pcookies = $this->db
			->get_where(config('bd_pcookies'), [
				'user_id' => $token_object->usr,
			])->result_array();

		return collect($reg_pcookies)
			->map(function($cookie) {
				return collect($cookie);
			})->first(function($pcookie) use ($token_object) {
				return $this->_valida_password($token_object->token, $pcookie->get('cookie_id'));
			}, collect())->get('user_id');
	}

	// --------------------------------------------------------------------

	/**
	 * Autentica el uso de un módulo de la aplicacion
	 *
	 * @param  string $modulo Modulo a autenticar
	 * @return boolean
	 */
	public function autentica_modulo($modulo = '')
	{
		if ( ! $this->session->userdata('user') OR  ! $this->session->userdata('modulos'))
		{
			return $this->input->cookie('login_token') ? $this->_login_rememberme_cookie() : FALSE;
		}
		else
		{
			return in_array($modulo, json_decode($this->session->userdata('modulos')));
		}
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
			->get_where(config('bd_usuarios'), ['username' => $usuario])
			->row_array();

		if (count($arr_rs) > 0)
		{
			return (is_null($arr_rs['password']) OR trim($arr_rs['password']) === '') ? FALSE : TRUE;
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
			->get_where(config('bd_usuarios'), ['username' => $usuario])
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
				->where('username', $usuario)
				->update(config('bd_usuarios'), [
					'password' => $this->_hash_password($clave_new)
				]);

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
	 * @return string
	 */
	public function get_redirect_app()
	{
		$arr_menu = $this->_get_menu_usuario($this->get_user());

		return array_get(collect($arr_menu)->first(), 'url', 'login/');
	}


}
/* End of file Acl.php */
/* Location: ./application/models/Acl.php */
