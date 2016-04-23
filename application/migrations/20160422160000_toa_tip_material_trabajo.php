<?php

class Migration_toa_tip_material_trabajo extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		echo "Agrega tabla 'toa_tip_material_trabajo'...".PHP_EOL;

		$this->dbforge->add_field(
			array(
				'id'                => array('type' => 'BIGINT', 'null' => FALSE, 'auto_increment' => TRUE),
				'desc_tip_material' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => TRUE),
				'color'             => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => TRUE),
			)
		);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('toa_tip_material_trabajo');

		echo 'OK'.PHP_EOL;
	}

	/**
	 * Rollback
	 *
	 * @return void
	 */
	public function down()
	{
		echo "Elimina tabla 'toa_tip_material_trabajo'... ".PHP_EOL;

		$this->dbforge->drop_table('toa_tip_material_trabajo');

		echo 'OK'.PHP_EOL;
	}

}