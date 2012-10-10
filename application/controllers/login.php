<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends CI_Controller {

	public function __construct()
	{
		parent::__construct();
		//$this->output->enable_profiler(TRUE);
	}

	public function index() 
	{
		$this->login();
	}

	public function login()
	{
		$this->load->model('acl_model');

		$this->form_validation->set_rules('usr', 'Usuario', 'trim|required');
		$this->form_validation->set_rules('pwd', 'Password', 'trim|required');

		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');

		if ($this->form_validation->run() == FALSE)
		{
			$data = array('msg_alerta' => '');
			$this->load->view('login', $data);
		}
		else
		{
			// si el usuario existe
			if ($this->acl_model->existe_usuario(set_value('usr')))
			{
				// si el usuario no tiene fijada una clave, lo obligamos a fijarla
				if (!$this->acl_model->tiene_clave(set_value('usr')))
				{
					redirect('login/cambio_password/' . set_value('usr'));
				}
				else
				{
					$login_ok = $this->acl_model->login(set_value('usr'), set_value('pwd'));
					if (!$login_ok) 
					{
						$data = array('msg_alerta' => 'Error en el nombre de usuario y/o clave');
						$this->load->view('login', $data);
					}
					else
					{
						$arr_paginas = json_decode($this->input->cookie('movistar_menu'));
						redirect($arr_paginas[0]);
					}
				}
			}
			else
			{
				$data = array('msg_alerta' => 'Error en el nombre de usuario y/o clave');
				$this->load->view('login', $data);
			}
		}
	}

	public function cambio_password($usr = '')
	{
		$this->load->model('acl_model');

		if ($this->acl_model->tiene_clave($usr))
		{
			$this->form_validation->set_rules('pwd_old', 'Clave Anterior', 'trim|required');
		}
		$this->form_validation->set_rules('pwd_new1', 'Clave Nueva', 'trim|required');
		$this->form_validation->set_rules('pwd_new2', 'Clave Nueva (reingreso)', 'trim|required|matches[pwd_new1]');

		$this->form_validation->set_error_delimiters('<div class="error round">', '</div>');
		$this->form_validation->set_message('required', 'Ingrese un valor para %s');
		$this->form_validation->set_message('matches', 'Nueva clave (reingreso) no coincide');

		if ($this->form_validation->run() == FALSE)
		{
			$data = array(
							'msg_alerta' => '',
							'usr'        => $usr,
							'nombre_usuario' => $this->acl_model->get_nombre_usuario($usr),
							'tiene_clave'    => $this->acl_model->tiene_clave($usr),
						);
			$this->load->view('cambio_password', $data);
		}
		else
		{
			$res = $this->acl_model->cambio_clave($usr, set_value('pwd_old'), set_value('pwd_new1'));
			if ($res[0])
			{
				redirect('login');				
			}
			else
			{
				$data = array('msg_alerta' => $res[1]);
				$this->load->view('cambio_password', $data);
			}
		}
	}

}

/* End of file login.php */
/* Location: ./application/controllers/login.php */
