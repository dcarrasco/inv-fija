<?php

class Migration_update_ci_sessions extends CI_Migration {

	public function up()
	{

		// ***************************************************************************
		// TABLA FIJA_USUARIOS, elimina columna remember_token
		// ***************************************************************************
		echo "Cambia campo 'id' en tabla 'ci_sessions'... <br/>";

		$this->dbforge->drop_column('ci_sessions', 'id');
		$this->dbforge->add_column('ci_sessions', array(
			'id' => array('type' => 'VARCHAR', 'constraint' => 128, 'null' => FALSE)
		));

		echo "OK<br/>";
	}


	public function down()
	{
		// ***************************************************************************
		// TABLA FIJA_USUARIOS, agrega remember_token
		// ***************************************************************************
		echo "Agrega campo 'remember_token' en tabla 'fija_usuarios'... <br/>";

		$this->dbforge->drop_column('ci_sessions', 'id');
		$this->dbforge->add_column('ci_sessions', array(
			'id' => array('type' => 'VARCHAR', 'constraint' => 40, 'null' => FALSE)
		));

		echo "OK<br/>";
	}

}