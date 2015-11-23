<?php

class Migration_fija_catalogos_fotos extends CI_Migration {

	public function up()
	{


		// ***************************************************************************
		// TABLA FIJA_CATALOGOS_FOTOS
		// ***************************************************************************
		echo "Agrega tabla 'fija_catalogos_fotos'... <br/>";

		$this->dbforge->add_field(
			array(
				'catalogo' => array('type' => 'VARCHAR',   'constraint' => 45,  'null' => FALSE),
				'foto'     => array('type' => 'VARBINARY', 'constraint' => 'max', 'null' => FALSE),
			)
		);

		$this->dbforge->add_key('catalogo', TRUE);

		$this->dbforge->create_table('fija_catalogos_fotos');

		echo "OK<br/>";
	}


	public function down()
	{
		// ***************************************************************************
		// TABLA FIJA_CATALOGOS_FOTOS
		// ***************************************************************************
		echo "Elimina tabla 'fija_catalogos_fotos'... ";

		$this->dbforge->drop_table('fija_catalogos_fotos');

		echo "OK<br/>";
	}

}