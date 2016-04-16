<?php

class Migration_toa_tipos_trabajo extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		echo "Agrega tabla 'toa_tipos_trabajo'...".PHP_EOL;

		$this->dbforge->add_field(
			array(
				'id_tipo'   => array('type' => 'VARCHAR', 'constraint' => '30', 'null' => FALSE),
				'desc_tipo' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
			)
		);
		$this->dbforge->add_key('id_tipo', TRUE);
		$this->dbforge->create_table('toa_tipos_trabajo');

		echo 'OK'.PHP_EOL;
	}

	/**
	 * Rollback
	 *
	 * @return void
	 */
	public function down()
	{
		echo "Elimina tabla 'toa_tipos_trabajo'... ".PHP_EOL;

		$this->dbforge->drop_table('toa_tipos_trabajo');

		echo 'OK'.PHP_EOL;
	}

}