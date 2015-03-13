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

		if ($this->acl_model->is_user_logged())
		{
			$arr_menu = $this->acl_model->get_menu_usuario($this->acl_model->get_user());
			redirect($arr_menu[0]['url']);
		}
		else
		{
			$this->acl_model->delete_session_data();

			$this->form_validation->set_rules('usr', 'Usuario', 'trim|required');
			$this->form_validation->set_rules('pwd', 'Password', 'trim|required');
			$this->form_validation->set_rules('remember_me', 'Recordarme', '');
			$this->app_common->form_validation_config();

			if ($this->form_validation->run() == FALSE)
			{
				$data = array('msg_alerta' => $this->session->flashdata('msg_alerta'));
				$this->load->view('ACL/login', $data);
			}
			else
			{
				$usr = set_value('usr');
				$pwd = set_value('pwd');
				$rem = set_value('remember_me');

				// si el usuario existe
				if ($this->acl_model->existe_usuario($usr))
				{
					// si el usuario no tiene fijada una clave, lo obligamos a fijarla
					if (!$this->acl_model->tiene_clave($usr))
					{
						redirect('login/cambio_password/' . $usr);
					}
					else
					{
						if (!$this->acl_model->login($usr, $pwd, $rem == 'remember'))
						{
							$data = array('msg_alerta' => '<div class="alert alert-danger">Error en el nombre de usuario y/o clave</div>');
							$this->load->view('ACL/login', $data);
						}
						else
						{
							$arr_menu = $this->acl_model->get_menu_usuario($usr);
							redirect($arr_menu[0]['url']);
						}
					}
				}
				else
				{
					$data = array('msg_alerta' => '<div class="alert alert-danger">Error en el nombre de usuario y/o clave</div>');
					$this->load->view('ACL/login', $data);
				}
			}
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Desconecta al usuario
	 * @return none
	 */
	public function logout()
	{
		$this->acl_model->delete_session_data();
		redirect('login/');
	}


	// --------------------------------------------------------------------

	/**
	 * Despliega pagina para cambiar la password de un usuario
	 *
	 * @param  none
	 * @return none
	 */
	public function cambio_password()
	{
		$this->load->model('acl_model');

		$this->form_validation->set_rules('usr', 'Usuario', 'trim|required');
		$this->form_validation->set_rules('pwd_old', 'Clave Anterior', 'trim|required');
		$this->form_validation->set_rules('pwd_new1', 'Clave Nueva', "trim|required|min_length[6]");
		$this->form_validation->set_rules('pwd_new2', 'Clave Nueva (reingreso)', 'trim|required|matches[pwd_new1]');
		$this->app_common->form_validation_config();

		if (!$this->acl_model->tiene_clave($this->input->post('usr')))
		{
			$this->form_validation->set_rules('pwd_old', 'Clave Anterior', 'trim');
		}


		if ($this->form_validation->run() == FALSE)
		{
			$usr = set_value('usr');

			$data = array(
				'msg_alerta'       => '',
				'usr'              => $usr,
				'tiene_clave_class' => $this->acl_model->tiene_clave($usr) ? '' : ' disabled',
				'ocultar_password' => (($this->input->post('usr')) ? TRUE : FALSE),
			);
			$this->load->view('ACL/cambio_password', $data);
		}
		else
		{
			$usr = set_value('usr');
			$msg_alerta = '';
			if (!$this->acl_model->check_user_credentials(set_value('usr'), set_value('pwd_old')))
			{
				$msg_alerta = 'Error en el nombre de usuario y/o clave';
			}

			if ($this->acl_model->cambio_clave(set_value('usr'), set_value('pwd_old'), set_value('pwd_new1')))
			{
				$this->session->set_flashdata('msg_alerta', '<div class="alert alert-success">La clave se cambió con exito. Vuelva a ingresar al sistema.</div>');
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
