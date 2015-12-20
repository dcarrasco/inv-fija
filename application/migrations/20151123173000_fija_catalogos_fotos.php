<?php

class Migration_fija_catalogos_fotos extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		// ***************************************************************************
		// TABLA FIJA_CATALOGOS_FOTOS
		// ***************************************************************************
		echo "Agrega campo a tabla 'fija_catalogos'...".PHP_EOL;

		$this->dbforge->add_column('fija_catalogos', array(
				'foto' => array('type' => 'VARCHAR', 'constraint' => '256', 'null' => TRUE),
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
		// ***************************************************************************
		// TABLA FIJA_CATALOGOS_FOTOS
		// ***************************************************************************
		echo "Elimina campo de tabla 'fija_catalogos'... ".PHP_EOL;

		$this->dbforge->drop_column('fija_catalogos', 'foto');

		echo 'OK'.PHP_EOL;
	}

}