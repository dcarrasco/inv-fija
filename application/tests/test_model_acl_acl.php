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

	protected $ci_object;

	public function __construct()
	{
		parent::__construct();

		$this->ci_object = [
			'session' => new mock_session,
			'db'      => new mock_db(),
			'input'   => new mock_input(),
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
		$acl = Acl\Acl::create($this->ci_object);

		$this->ci_object['session']->mock_set_userdata('user', 'session_user');
		$this->assert_equals($acl->get_user('test'), 'session_user');
	}

	public function test_get_id_usr()
	{
		$acl = Acl\Acl::create($this->ci_object);

		$this->ci_object['db']->mock_set_return_result(NULL);
		$this->assert_is_string($acl->get_id_usr());
		$this->assert_empty($acl->get_id_usr());

		$this->ci_object['db']->mock_set_return_result(['id' => '1']);
		$this->assert_equals($acl->get_id_usr(), '1');
		$this->assert_equals($acl->get_id_usr('test'), '1');
	}

	public function test_get_user_firstname()
	{
		$acl = Acl\Acl::create($this->ci_object);

		$this->ci_object['db']->mock_set_return_result(['nombre' => 'Nombre1 Apellido1 Apellido2']);
		$this->assert_equals($acl->get_user_firstname(), 'Nombre1');

		$this->ci_object['db']->mock_set_return_result(['nombre' => 'Nombre1Apellido1Apellido2']);
		$this->assert_equals($acl->get_user_firstname(), 'Nombre1Apellido1Apellido2');

		$this->ci_object['session']->mock_set_userdata('user_firstname', 'first_name_from_session');
		$this->assert_equals($acl->get_user_firstname(), 'first_name_from_session');
	}

	public function test_is_user_logged()
	{
		$acl = Acl\Acl::create($this->ci_object);

		$this->assert_false($acl->is_user_logged());

		$this->ci_object['input']->mock_set_input_variable('login_token', json_encode(['usr' => 'usuario', 'token' => 'token']));
		$this->ci_object['db']->mock_set_return_result([[
			'user_id'   => 'user_id',
			'cookie_id' => crypt('token', '$2a$10$1g7RmPUspQEh5FNNJJsJn2'),
		]]);
		$this->assert_true($acl->is_user_logged());

		$this->ci_object['session']
			->mock_set_userdata('user', 'session_user')
			->mock_set_userdata('modulos', 'session_modulos');
		$this->assert_true($acl->is_user_logged());
	}

	public function test_check_user_credentials()
	{
		$acl = Acl\Acl::create($this->ci_object);

		$this->ci_object['db']->mock_set_return_result([
			'password' => crypt('password', '$2a$10$1g7RmPUspQEh5FNNJJsJn2'),
		]);

		$this->assert_true($acl->check_user_credentials('usuario', 'password'));
	}

	public function test_use_captcha()
	{
		$acl = Acl\Acl::create($this->ci_object);

		$this->ci_object['db']->mock_set_return_result(['login_errors' => 10]);
		$this->assert_true($acl->use_captcha('usuario'));

		$this->ci_object['db']->mock_set_return_result(['login_errors' => 1]);
		$this->assert_false($acl->use_captcha('usuario'));
	}
}



