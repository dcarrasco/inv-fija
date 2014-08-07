<?php

class Migration_auditoria_login extends CI_Migration {

	public function up()
	{

		// ***************************************************************************
		// TABLA FIJA_ALMACENES
		// ***************************************************************************
		echo "Agrega campos auditoria tabla 'fija_usuarios'... ";

		$this->dbforge->add_column('fija_usuarios',
			array(
				'fecha_login' => array('type' => 'DATETIME', 'null' => TRUE),
				'ip_login'    => array('type' => 'VARCHAR', 'constraint' => '30', 'null' => TRUE),
				'agent_login' => array('type' => 'VARCHAR', 'constraint' => '200', 'null' => TRUE),
			)
		);

		echo "OK<br/>";
	}


	public function down()
	{
		echo "Elimina campos auditoria tabla 'fija_usuarios'... ";

		$this->dbforge->drop_column('fija_usuarios', 'fecha_login');
		$this->dbforge->drop_column('fija_usuarios', 'ip_login');
		$this->dbforge->drop_column('fija_usuarios', 'agente_login');

		echo "OK<br/>";
	}

}