<?php

class Migration_toa extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		echo "Agrega tabla 'toa_tecnicos'...".PHP_EOL;
		$this->dbforge->add_field(
			array(
				'id_tecnico' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
				'tecnico'    => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
				'id_empresa' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => TRUE),
			)
		);
		$this->dbforge->add_key('id_tecnico', TRUE);
		$this->dbforge->create_table('toa_tecnicos');

		echo "Agrega tabla 'toa_empresas'...".PHP_EOL;
		$this->dbforge->add_field(
			array(
				'id_empresa' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
				'empresa'    => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
			)
		);
		$this->dbforge->add_key('id_empresa', TRUE);
		$this->dbforge->create_table('toa_empresas');

		echo 'OK'.PHP_EOL;
	}

	/**
	 * Rollback
	 *
	 * @return void
	 */
	public function down()
	{
		echo "Elimina tabla 'toa_tecnicos'... ".PHP_EOL;
		$this->dbforge->drop_table('toa_tecnicos');

		echo "Elimina tabla 'toa_empresas'... ".PHP_EOL;
		$this->dbforge->drop_table('toa_empresas');
		echo 'OK'.PHP_EOL;
	}

}