<?php

use test\test_case;
use test\mock\mock_db;
use test\mock\mock_input;
use test\mock\mock_session;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_model_acl_acl extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	protected function new_ci_object()
	{
		return [
			'session' => new mock_session,
			'db'      => new mock_db,
			'input'   => new mock_input,
		];
	}

	public function test_new()
	{
		$this->assert_is_object(Acl\Acl::create());
	}

	public function test_rules_login()
	{
		$this->assert_not_empty(Acl\Acl::create()->rules_login);
	}

	public function test_rules_captcha()
	{
		$this->assert_not_empty(Acl\Acl::create()->rules_captcha);
	}

	public function test_rules_change_password_validation()
	{
		$this->assert_not_empty(Acl\Acl::create()->change_password_validation);
	}

	public function test_get_user()
	{
		$acl = Acl\Acl::create($this->new_ci_object());

		$acl->session->mock_set_userdata('user', 'session_user');
		$this->assert_equals($acl->get_user('test'), 'session_user');
	}

	public function test_get_id_usr()
	{
		$acl = Acl\Acl::create($this->new_ci_object());

		$acl->db->mock_set_return_result(NULL);
		$this->assert_is_string($acl->get_id_usr());
		$this->assert_empty($acl->get_id_usr());

		$acl->db->mock_set_return_result(['id' => '1']);
		$this->assert_equals($acl->get_id_usr(), '1');
		$this->assert_equals($acl->get_id_usr('test'), '1');
	}

	public function test_get_user_firstname()
	{
		$acl = Acl\Acl::create($this->new_ci_object());

		$acl->db->mock_set_return_result(['nombre' => 'Nombre1 Apellido1 Apellido2']);
		$this->assert_equals($acl->get_user_firstname(), 'Nombre1');

		$acl->db->mock_set_return_result(['nombre' => 'Nombre1Apellido1Apellido2']);
		$this->assert_equals($acl->get_user_firstname(), 'Nombre1Apellido1Apellido2');

		$acl->session->mock_set_userdata('user_firstname', 'first_name_from_session');
		$this->assert_equals($acl->get_user_firstname(), 'first_name_from_session');
	}

	public function test_is_user_logged()
	{
		$acl = Acl\Acl::create($this->new_ci_object());

		$this->assert_false($acl->is_user_logged());

		$acl->input->mock_set_input_variable('login_token', json_encode(['usr' => 'usuario', 'token' => 'token']));
		$acl->db->mock_set_return_result([[
			'user_id'   => 'user_id',
			'cookie_id' => crypt('token', '$2a$10$1g7RmPUspQEh5FNNJJsJn2'),
		]]);
		$this->assert_true($acl->is_user_logged());

		$acl->session
			->mock_set_userdata('user', 'session_user')
			->mock_set_userdata('modulos', 'session_modulos');
		$this->assert_true($acl->is_user_logged());
	}

	public function test_check_user_credentials()
	{
		$acl = Acl\Acl::create($this->new_ci_object());

		$acl->db->mock_set_return_result([
			'password' => crypt('password', '$2a$10$1g7RmPUspQEh5FNNJJsJn2'),
		]);

		$this->assert_true($acl->check_user_credentials('usuario', 'password'));
	}

	public function test_use_captcha()
	{
		$acl = Acl\Acl::create($this->new_ci_object());

		$acl->db->mock_set_return_result(['login_errors' => 10]);
		$this->assert_true($acl->use_captcha('usuario'));

		$acl->db->mock_set_return_result(['login_errors' => 1]);
		$this->assert_false($acl->use_captcha('usuario'));
	}

	public function test_get_user_menu()
	{
		$acl = Acl\Acl::create($this->new_ci_object());
		$arr_menu_app = [
			['app'=>'app', 'app_icono'=>'app_icono1', 'modulo'=>'modulo1', 'url'=>'url1', 'llave_modulo'=>'llave_modulo1', 'modulo_icono'=>'modulo_icono1', 'orden'=>'orden'],
			['app'=>'app', 'app_icono'=>'app_icono2', 'modulo'=>'modulo2', 'url'=>'url2', 'llave_modulo'=>'llave_modulo2', 'modulo_icono'=>'modulo_icono2', 'orden'=>'orden'],
		];

		$acl->session->mock_set_userdata('menu_app', json_encode($arr_menu_app));
		$this->assert_equals((array) $acl->get_user_menu('usuario'), (array) collect($arr_menu_app));
	}

	public function test_autentica_modulo()
	{
		$acl = Acl\Acl::create($this->new_ci_object());
		$arr_modulos = ['llave_modulo1', 'llave_modulo2', 'llave_modulo3', 'llave_modulo4'];

		$acl->session
			->mock_set_userdata('user', 'user')
			->mock_set_userdata('modulos', json_encode($arr_modulos));

		$this->assert_true($acl->autentica_modulo('llave_modulo1'));
		$this->assert_false($acl->autentica_modulo('llave_modulo100'));
	}

	public function test_tiene_clave()
	{
		$acl = Acl\Acl::create($this->new_ci_object());

		$acl->db->mock_set_return_result(NULL);
		$this->assert_false($acl->tiene_clave('usuario'));

		$acl->db->mock_set_return_result([]);
		$this->assert_false($acl->tiene_clave('usuario'));

		$acl->db->mock_set_return_result(['password' => '']);
		$this->assert_false($acl->tiene_clave('usuario'));

		$acl->db->mock_set_return_result(['password' => 'password']);
		$this->assert_true($acl->tiene_clave('usuario'));
	}

	public function test_existe_usuario()
	{
		$acl = Acl\Acl::create($this->new_ci_object());

		$acl->db->mock_set_return_result(NULL);
		$this->assert_false($acl->existe_usuario('usuario'));

		$acl->db->mock_set_return_result([]);
		$this->assert_false($acl->existe_usuario('usuario'));

		$acl->db->mock_set_return_result(['user' => 'user']);
		$this->assert_true($acl->existe_usuario('usuario'));
	}



}



