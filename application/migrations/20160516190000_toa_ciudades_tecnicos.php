<?php

class Migration_toa_ciudades_tecnicos extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		echo "Agrega campo ciudad a 'toa_tecnicos'...".PHP_EOL;

		$this->dbforge->add_column('bd_toa..toa_tecnicos', array(
				'id_ciudad'   => array('type' => 'BIGINT', 'null' => TRUE),
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
		echo "Elimina campo ciudad tabla 'toa_tecnicos'... ".PHP_EOL;

		$this->dbforge->drop_column('bd_toa..toa_tecnicos', 'id_ciudad');

		echo 'OK'.PHP_EOL;
	}

}