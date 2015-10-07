<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('login');
	}

	// --------------------------------------------------------------------

	/**
	 * Pagina index, ejecuta por defecto al no recibir parámetros
	 *
	 * @param  none
	 * @return none
	 */
	public function index()
	{
		$this->login();
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega pagina para ingresar al sistema
	 *
	 * @param  none
	 * @return none
	 */
	public function login()
	{
		$this->load->model('acl_model');
		$this->load->helper('cookie');
		$this->load->helper('captcha');

		$msg_alerta = '';

		$this->acl_model->delete_old_captchas();

		// si el usuario ya está logueado, redireccionar a la app
		if ($this->acl_model->is_user_logged())
		{
			$arr_menu = $this->acl_model->get_menu_usuario($this->acl_model->get_user());
			redirect($arr_menu[0]['url']);
		}

		$this->acl_model->delete_session_data();


		$this->form_validation->set_rules('usr', 'Usuario', 'trim|required');
		$this->form_validation->set_rules('pwd', 'Password', 'trim|required');
		$this->form_validation->set_rules('remember_me', 'Recordarme', 'trim');
		$this->app_common->form_validation_config();

		if ($this->form_validation->run() === TRUE)
		{
			$usr          = set_value('usr');
			$pwd          = set_value('pwd');
			$captcha_word = set_value('captcha');
			$rem          = set_value('remember_me');

			// si el usuario existe y no tiene fijada una clave, lo obligamos a fijarla
			if ($this->acl_model->existe_usuario($usr) AND !$this->acl_model->tiene_clave($usr))
			{
				redirect('login/cambio_password/' . $usr);
			}

			// si el usuario debe validar captcha
			$captcha_valido = TRUE;
			if ($this->acl_model->use_captcha($usr))
			{
				$captcha_valido = $this->acl_model->validar_captcha($captcha_word, $this->session->userdata('session_id'));
			}

			// si el usuario valida correctamente, redireccionamos a la app
			if ($captcha_valido AND $this->acl_model->login($usr, $pwd, $rem == 'remember'))
			{
				if ($rem == 'remember')
				{
					$this->acl_model->set_rememberme_cookie($usr);
				}
				$arr_menu = $this->acl_model->get_menu_usuario($usr);

				redirect($arr_menu[0]['url']);
			}

			$msg_alerta = '<div class="alert alert-danger">' . $this->lang->line('login_error_usr_pwd') . '</div>';
		}

		$captcha_img  = '';
		$usar_captcha = $this->acl_model->use_captcha($this->input->post('usr'));

		if ($usar_captcha)
		{
			$captcha_config = array(
				'img_path'      => './img/captcha/',
				'img_url'       => base_url() . 'img/captcha/',
				'word'          => genera_captcha_word(),
				'img_width'     => 260,
				'img_height'    => 50,
				'font_size'     => 30,
			);

			$captcha     = create_captcha($captcha_config);
			$captcha_img = $captcha['image'];

			$this->acl_model->write_captcha(
				(string) $captcha['time'],
				(string) $this->session->userdata('session_id'),
				(string) $captcha['word']
			);

			$this->form_validation->set_rules('captcha', 'Palabra distorsionada', 'trim|required');
		}

		// error en la validación de usuario/pwd
		$msg_alerta = (count($this->form_validation->error_array()) > 0)
			? $this->session->flashdata('msg_alerta')
			: $msg_alerta;

		$data = array(
			'msg_alerta'   => $msg_alerta,
			'usar_captcha' => $usar_captcha,
			'captcha_img'  => $captcha_img,
		);

		$this->load->view('ACL/login', $data);
	}


	// --------------------------------------------------------------------

	/**
	 * Desconecta al usuario
	 * @return none
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
	 * @param  none
	 * @return none
	 */
	public function cambio_password($usr_param = '')
	{
		$this->load->model('acl_model');

		$this->form_validation->set_rules('usr', 'Usuario', 'trim|required');
		$this->form_validation->set_rules('pwd_old', 'Clave Anterior', 'trim|required');
		$this->form_validation->set_rules('pwd_new1', 'Clave Nueva', "trim|required|min_length[8]|callback_password_validation");
		$this->form_validation->set_rules('pwd_new2', 'Clave Nueva (reingreso)', 'trim|required|matches[pwd_new1]');
		$this->app_common->form_validation_config();

		if (!$this->acl_model->tiene_clave($this->input->post('usr')))
		{
			$this->form_validation->set_rules('pwd_old', 'Clave Anterior', 'trim');
		}


		if ($this->form_validation->run() == FALSE)
		{
			$usr = set_value('usr', $usr_param);

			$data = array(
				'msg_alerta'        => '',
				'usr'               => $usr,
				'tiene_clave_class' => $this->acl_model->tiene_clave($usr) ? '' : ' disabled',
				'ocultar_password'  => (($this->input->post('usr')) ? TRUE : FALSE),
			);
			$this->load->view('ACL/cambio_password', $data);
		}
		else
		{
			$usr = set_value('usr');
			$msg_alerta = '';
			if (!$this->acl_model->check_user_credentials(set_value('usr'), set_value('pwd_old')))
			{
				$msg_alerta = $this->lang->line('login_error_usr_pwd');
			}

			if ($this->acl_model->cambio_clave(set_value('usr'), set_value('pwd_old'), set_value('pwd_new1')))
			{
				$this->session->set_flashdata('msg_alerta', '<div class="alert alert-success">' . $this->lang->line('login_success_pwd_changed') . '</div>');
				redirect('login');
			}
			else
			{
				$data = array(
					'usr'              => $usr,
					'msg_alerta'       => $msg_alerta,
					'ocultar_password' => (($this->input->post('usr')) ? TRUE : FALSE),
					'tiene_clave'      => $this->acl_model->tiene_clave($usr),
				);
				$this->load->view('ACL/cambio_password', $data);
			}
		}
	}

	// --------------------------------------------------------------------

	public function password_validation($str)
	{
		if (preg_match('#[0-9]#', $str) AND preg_match('#[a-z]#', $str) AND preg_match('#[A-Z]#', $str))
		{
     		return TRUE;
   		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * PRUEBA ****** Enviar correo con nueva password
	 *
	 * @param  none
	 * @return none
	 */
	public function enviar_correo($usr = '')
	{
		$this->load->model('acl_model');
		$this->load->helper('string');
		$this->load->library('email');

		$correo = $this->acl_model->get_correo($usr);
		if ($correo != '')
		{
			$clave = random_string('alnum', 10);

			$this->email->from('your@example.com', 'Your Name');
			$this->email->to($correo);
			$this->email->subject('Cambio de clave');
			$this->email->message('Su nueva clave es: ' . $clave);
			$this->email->send();
		}

	}



}
/* End of file login.php */
/* Location: ./application/controllers/login.php */
