<?php

class Migration_toa_ubicaciones extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		echo "Agrega tabla 'toa_ciudades'...".PHP_EOL;
		$this->dbforge->add_field(
			array(
				'id'     => array('type' => 'BIGINT', 'null' => FALSE, 'auto_increment' => TRUE),
				'ciudad' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE),
				'orden'  => array('type' => 'BIGINT', 'null' => FALSE),
			)
		);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('toa_ciudades');

		echo "Agrega tabla 'toa_empresas_ciudades'...".PHP_EOL;
		$this->dbforge->add_field(
			array(
				'id_ciudad'  => array('type' => 'BIGINT', 'null' => FALSE),
				'id_empresa' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
			)
		);
		$this->dbforge->add_key('id_ciudad', TRUE);
		$this->dbforge->add_key('id_empresa', TRUE);
		$this->dbforge->create_table('toa_empresas_ciudades');

		echo 'OK'.PHP_EOL;
	}

	/**
	 * Rollback
	 *
	 * @return void
	 */
	public function down()
	{
		echo "Elimina tabla 'toa_ciudades', 'toa_empresas_ciudades'... ".PHP_EOL;

		$this->dbforge->drop_table('toa_ciudades');
		$this->dbforge->drop_table('toa_empresas_ciudades');

		echo 'OK'.PHP_EOL;
	}

}