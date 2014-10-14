<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
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

		$this->acl_model->delete_session_cookies();

		$this->form_validation->set_rules('usr', 'Usuario', 'trim|required');
		$this->form_validation->set_rules('pwd', 'Password', 'trim|required');
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
					if (!$this->acl_model->login($usr, $pwd))
					{
						$data = array('msg_alerta' => 'Error en el nombre de usuario y/o clave');
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
				$data = array('msg_alerta' => 'Error en el nombre de usuario y/o clave');
				$this->load->view('ACL/login', $data);
			}
		}
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
		$this->form_validation->set_rules('pwd_new1', 'Clave Nueva', 'trim|required');
		$this->form_validation->set_rules('pwd_new2', 'Clave Nueva (reingreso)', 'trim|required|matches[pwd_new1]');

		$usr = ($this->input->post('usr')) ? $this->input->post('usr') : '';

		if ($this->acl_model->tiene_clave($usr))
		{
			$this->form_validation->set_rules('pwd_old', 'Clave Anterior', 'trim|required');
		}

		$this->app_common->form_validation_config();

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
							'msg_alerta' => '',
							'usr'        => $usr,
							'nombre_usuario' => $this->acl_model->get_nombre_usuario($usr),
							'tiene_clave'    => $this->acl_model->tiene_clave($usr),
							'ocultar_password' => (($this->input->post('usr')) ? TRUE : FALSE),
						);
			$this->load->view('ACL/cambio_password', $data);
		}
		else
		{
			$res = $this->acl_model->cambio_clave($usr, set_value('pwd_old'), set_value('pwd_new1'));
			if ($res[0])
			{
				$this->session->set_flashdata('msg_alerta', 'La clave se cambió con exito. Vuelva a ingresar al sistema.');
				redirect('login');
			}
			else
			{
				$data = array(
					'msg_alerta' => $res[1],
					'usr'        => $usr,
					'ocultar_password' => (($this->input->post('usr')) ? TRUE : FALSE),
					'tiene_clave'    => $this->acl_model->tiene_clave($usr),
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
