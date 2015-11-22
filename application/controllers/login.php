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
 * Clase Controller Login ACL
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Login extends CI_Controller {

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->lang->load('login');

		$this->load->model('acl_model');
		$this->load->helper('cookie');

	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @return void
	 */
	public function index()
	{
		$this->login();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega pagina para ingresar al sistema
	 *
	 * @return void
	 */
	public function login()
	{
		$msg_alerta = $this->session->flashdata('msg_alerta');

		$this->acl_model->delete_old_captchas();

		// si el usuario ya está logueado, redireccionar a la app
		if ($this->acl_model->is_user_logged())
		{
			redirect($this->acl_model->get_redirect_app());
		}

		$this->acl_model->delete_session_data();

		$this->form_validation->set_rules($this->acl_model->login_validation);

		if ($this->form_validation->run() === TRUE)
		{
			$usuario      = set_value('usr');
			$password     = set_value('pwd');
			$captcha_word = set_value('captcha');
			$remember_me  = set_value('remember_me');

			// si el usuario existe y no tiene fijada una clave, lo obligamos a fijarla
			if ($this->acl_model->existe_usuario($usuario) AND ! $this->acl_model->tiene_clave($usuario))
			{
				redirect('login/cambio_password/' . $usuario);
			}

			// si el usuario debe validar captcha
			$captcha_valido = TRUE;
			if ($this->acl_model->use_captcha($usuario))
			{
				$captcha_valido = $this->acl_model->validar_captcha($captcha_word, $this->session->session_id);
			}

			// si el usuario valida correctamente, redireccionamos a la app
			if ($captcha_valido AND $this->acl_model->login($usuario, $password, $remember_me === 'remember'))
			{
				if ($remember_me === 'remember')
				{
					$this->acl_model->set_rememberme_cookie($usuario);
				}
				redirect($this->acl_model->get_redirect_app());
			}

			$msg_alerta = print_message($this->lang->line('login_error_usr_pwd'), 'danger');
		}

		$captcha_img  = $this->acl_model->get_captcha_img(set_value('usr'));

		$data = array(
			'msg_alerta'   => $msg_alerta,
			'usar_captcha' => $captcha_img !== '',
			'captcha_img'  => $captcha_img,
			'extra_styles' => '<style type="text/css">body {margin-top: 40px;}</style>',
			'arr_vistas'   => array('ACL/login'),
			'vista_login'  => TRUE,
		);

		$this->load->view('common/app_layout', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Desconecta al usuario
	 *
	 * @return void
	 */
	public function logout()
	{
		$this->acl_model->delete_session_data();
		$this->acl_model->delete_cookie_data();
		redirect('login/');
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega pagina para cambiar la password de un usuario
	 *
	 * @param  string $usr_param Usuario
	 * @return void
	 */
	public function cambio_password($usr_param = '')
	{
		$this->form_validation->set_rules($this->acl_model->change_password_validation);

		$msg_alerta = '';

		if ( ! $this->acl_model->tiene_clave($this->input->post('usr')))
		{
			$this->form_validation->set_rules('pwd_old', 'Clave Anterior', 'trim');
		}

		if ($this->form_validation->run() === TRUE)
		{
			if ( ! $this->acl_model->check_user_credentials(set_value('usr'), set_value('pwd_old')))
			{
				$msg_alerta = print_message($this->lang->line('login_error_usr_pwd'), 'danger');
			}

			if ($this->acl_model->cambio_clave(set_value('usr'), set_value('pwd_old'), set_value('pwd_new1')))
			{
				set_message($this->lang->line('login_success_pwd_changed'));

				redirect('login');
			}
		}

		$usuario = set_value('usr', $usr_param);

		$data = array(
			'msg_alerta'        => $msg_alerta,
			'usr'               => $usuario,
			'tiene_clave_class' => $this->acl_model->tiene_clave($usuario) ? '' : ' disabled',
			'ocultar_password'  => $this->input->post('usr') ? TRUE : FALSE,
			'extra_styles' => '<style type="text/css">body {margin-top: 40px;}</style>',
			'arr_vistas'   => array('ACL/cambio_password'),
			'vista_login'  => TRUE,
		);

		$this->load->view('common/app_layout', $data);
	}



}
/* End of file login.php */
/* Location: ./application/controllers/login.php */