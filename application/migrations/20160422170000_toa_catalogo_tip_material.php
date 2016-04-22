<?php

class Migration_toa_catalogo_tip_material extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		echo "Agrega tabla 'toa_catalogo_tip_material'...".PHP_EOL;

		$this->dbforge->add_field(
			array(
				'id_catalogo'             => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
				'id_tip_material_trabajo' => array('type' => 'BIGINT', 'null' => FALSE),
			)
		);
		$this->dbforge->add_key('id_catalogo', TRUE);
		$this->dbforge->add_key('id_tip_material_trabajo', TRUE);
		$this->dbforge->create_table('toa_catalogo_tip_material');

		echo 'OK'.PHP_EOL;
	}

	/**
	 * Rollback
	 *
	 * @return void
	 */
	public function down()
	{
		echo "Elimina tabla 'toa_catalogo_tip_material'... ".PHP_EOL;

		$this->dbforge->drop_table('toa_catalogo_tip_material');

		echo 'OK'.PHP_EOL;
	}

}