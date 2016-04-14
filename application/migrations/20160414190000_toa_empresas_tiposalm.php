<?php

class Migration_toa_empresas_tiposalm extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		echo "Agrega tabla 'toa_empresas_tiposalm'...".PHP_EOL;

		$this->dbforge->add_field(
			array(
				'id_empresa' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
				'id_tipo'    => array('type' => 'INT', 'null' => FALSE),
			)
		);
		$this->dbforge->add_key('id_empresa', TRUE);
		$this->dbforge->add_key('id_tipo', TRUE);
		$this->dbforge->create_table('toa_empresas_tiposalm');

		echo 'OK'.PHP_EOL;
	}

	/**
	 * Rollback
	 *
	 * @return void
	 */
	public function down()
	{
		echo "Elimina tabla 'toa_empresas_tiposalm'... ".PHP_EOL;

		$this->dbforge->drop_table('toa_empresas_tiposalm');

		echo 'OK'.PHP_EOL;
	}

}