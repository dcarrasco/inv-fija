<?php

class Migration_Add_captcha extends CI_Migration {

	public function up()
	{

		// ***************************************************************************
		// TABLA FIJA_USUARIOS
		// ***************************************************************************
		echo "Agrega campo 'login_errors' en tabla 'fija_usuarios'... <br/>";

		$this->dbforge->add_column('fija_usuarios',
			array(
				'login_errors' => array('type' => 'INT', 'null' => FALSE, 'default' => 0),
			)
		);

		// ***************************************************************************
		// TABLA CI_CAPTCHA
		// ***************************************************************************
		echo "Agrega tabla 'ci_captcha'... <br/>";

		$this->dbforge->add_field(
			array(
				'captcha_id'   => array('type' => 'INT', 'null' => FALSE, 'auto_increment' => TRUE),
				'captcha_time' => array('type' => 'INT', 'null' => FALSE, ),
				'ip_address'   => array('type' => 'VARCHAR', 'constraint' => 45, 'null' => FALSE, ),
				'word'         => array('type' => 'VARCHAR', 'constraint' => 20, 'null' => FALSE, ),
			)
		);

		$this->dbforge->add_key('captcha_id', TRUE);
		$this->dbforge->add_key('word');

		$this->dbforge->create_table('ci_captcha');

		echo "OK<br/>";
	}


	public function down()
	{
		// ***************************************************************************
		// TABLA FIJA_USUARIOS
		// ***************************************************************************
		echo "Elimina campo 'login_errors' en tabla 'fija_usuarios'... <br/>";

		$this->dbforge->drop_column('fija_usuarios', 'login_errors');

		// ***************************************************************************
		// TABLA CI_CAPTCHA
		// ***************************************************************************
		echo "Elimina tabla 'ci_captcha'... ";

		$this->dbforge->drop_table('ci_captcha');

		echo "OK<br/>";
	}

}