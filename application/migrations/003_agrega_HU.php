<?php

class Migration_agrega_HU extends CI_Migration {

	public function up()
	{

		// ***************************************************************************
		// TABLA FIJA_DETALLE_INVENTARIO
		// ***************************************************************************
		$fields = array(
			'HU' => array(
					'type'       => 'VARCHAR',
					'constraint' => '20',
					'null'       => FALSE,
					'default'    => '',
				),
		);
		$this->dbforge->add_column('fija_detalle_inventario', $fields);


	}


	public function down()
	{
		$this->dbforge->drop_column('fija_detalle_inventario', 'HU');
	}

}