<?php

class Migration_Persistent_cookies extends CI_Migration {

	public function up()
	{

		// ***************************************************************************
		// TABLA FIJA_USUARIOS, elimina columna remember_token
		// ***************************************************************************
		echo "Elimina campo 'remember_token' en tabla 'fija_usuarios'... <br/>";

		$this->dbforge->drop_column('fija_usuarios', 'remember_token');


		// ***************************************************************************
		// TABLA FIJA_PCOOKIES
		// ***************************************************************************
		echo "Agrega tabla 'fija_pcookies'... <br/>";

		$this->dbforge->add_field(
			array(
				'user_id'   => array('type' => 'VARCHAR', 'constraint' => 30, 'null' => FALSE),
				'cookie_id' => array('type' => 'VARCHAR', 'constraint' => 128, 'null' => FALSE),
				'expiry'    => array('type' => 'DATETIME'),
				'salt'      => array('type' => 'VARCHAR', 'constraint' => 128, 'null' => FALSE),
			)
		);

		$this->dbforge->add_key('user_id', TRUE);
		$this->dbforge->add_key('cookie_id', TRUE);

		$this->dbforge->create_table('fija_pcookies');

		echo "OK<br/>";
	}


	public function down()
	{
		// ***************************************************************************
		// TABLA FIJA_USUARIOS, agrega remember_token
		// ***************************************************************************
		echo "Agrega campo 'remember_token' en tabla 'fija_usuarios'... <br/>";

		$this->dbforge->add_column('fija_usuarios',
			array(
				'remember_token' => array('type' => 'VARCHAR', 'constraint' => 128, 'null' => TRUE,),
			)
		);

		// ***************************************************************************
		// TABLA FIJA_PCOOKIES
		// ***************************************************************************
		echo "Elimina tabla 'fija_pcookies'... ";

		$this->dbforge->drop_table('fija_pcookies');

		echo "OK<br/>";
	}

}