<?php

class Migration_toa_tecnicos extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		echo "Agrega campo RUT a 'toa_tecnicos'...".PHP_EOL;

		$this->dbforge->add_column('toa_tecnicos', array(
				'rut' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => TRUE),
		));

		echo 'OK'.PHP_EOL;
	}

	/**
	 * Rollback
	 *
	 * @return void
	 */
	public function down()
	{
		echo "Elimina campo RUT tabla 'toa_tecnicos'... ".PHP_EOL;

		$this->dbforge->drop_column('fija_catalogos', 'rut');

		echo 'OK'.PHP_EOL;
	}

}