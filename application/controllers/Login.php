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

use Acl\Acl;

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
class Login extends Controller_base {

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->lang->load('login');
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
		// si el usuario ya está logueado, redireccionar a la app
		if (Acl::create()->is_user_logged())
		{
			redirect(Acl::create()->get_redirect_app());
		}

		Acl::create()->delete_old_captchas();
		Acl::create()->delete_session_data();

		$captcha_img = Acl::create()->get_captcha_img(request('usr'));

		app_render_view('ACL/login', [
			'captcha_img'  => $captcha_img,
			'extra_styles' => '<style type="text/css">body {margin-top: 80px; background-image: url("'.site_url('img/tch-background.jpg').'"); background-size: cover;}</style>',
			'vista_login'  => TRUE,
			'url_login'    => site_url("{$this->router->class}/do_login/"),
		]);
	}


	// --------------------------------------------------------------------

	public function do_login()
	{
		$rules = array_merge(
			Acl::create()->rules_login,
			Acl::create()->use_captcha(request('usr')) ? Acl::create()->rules_captcha : []
		);
		$this->form_validation->set_rules($rules);

		route_validation($this->form_validation->run());

		$usuario      = request('usr');
		$password     = request('pwd');
		$captcha_word = request('captcha');
		$remember_me  = request('remember_me');

		// si el usuario existe y no tiene fijada una clave, lo obligamos a fijarla
		if (Acl::create()->existe_usuario($usuario) AND ! Acl::create()->tiene_clave($usuario))
		{
			redirect('login/cambio_password/' . $usuario);
		}

		// si el usuario debe validar captcha
		$captcha_valido = Acl::create()->use_captcha($usuario)
			? Acl::create()->validar_captcha($captcha_word, $this->session->session_id)
			: TRUE;

		// si el usuario valida correctamente, redireccionamos a la app
		if ($captcha_valido AND Acl::create()->login($usuario, $password, $remember_me === 'remember'))
		{
			redirect(Acl::create()->get_redirect_app());
		}

		set_message($this->lang->line('login_error_usr_pwd'), 'danger');
		$this->session->set_flashdata('old_request', array_merge($this->input->post(), $this->input->get()));

		redirect('login');
	}


	// --------------------------------------------------------------------

	/**
	 * Desconecta al usuario
	 *
	 * @return void
	 */
	public function logout()
	{
		Acl::create()->delete_session_data();
		Acl::create()->delete_cookie_data();
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
		$usuario = request('usr', $usr_param);

		app_render_view('ACL/cambio_password', [
			'usr'               => $usuario,
			'tiene_clave_class' => Acl::create()->tiene_clave($usuario) ? '' : ' disabled',
			'ocultar_password'  => request('usr') ? TRUE : FALSE,
			'extra_styles'      => '<style type="text/css">body {margin-top: 80px; background-image: url("'.site_url('img/tch-background.jpg').'"); background-size: cover;}</style>',
			'arr_vistas'        => ['ACL/cambio_password'],
			'vista_login'       => TRUE,
			'url_form'          => site_url("{$this->router->class}/do_cambio_password/{$usuario}"),
		]);
	}

	// --------------------------------------------------------------------

	public function do_cambio_password($usr_param = '')
	{
		$this->form_validation->set_rules(Acl::create()->change_password_validation);

		if ( ! Acl::create()->tiene_clave(request('usr')))
		{
			$this->form_validation->set_rules('pwd_old', 'Clave Anterior', 'trim');
		}

		route_validation($this->form_validation->run());

		if ( ! Acl::create()->check_user_credentials(request('usr'), request('pwd_old')))
		{
			set_message($this->lang->line('login_error_usr_pwd'), 'danger');
		}

		if (Acl::create()->cambio_clave(request('usr'), request('pwd_old'), request('pwd_new1')))
		{
			set_message($this->lang->line('login_success_pwd_changed'));

			redirect('login');
		}

		$this->session->set_flashdata('old_request', array_merge($this->input->post(), $this->input->get()));
		redirect('cambio_password');
	}

}
/* End of file login.php */
/* Location: ./application/controllers/login.php */
