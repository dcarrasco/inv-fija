<?php

class Migration_agrega_HU extends CI_Migration {

	public function up()
	{

		// ***************************************************************************
		// TABLA FIJA_DETALLE_INVENTARIO
		// ***************************************************************************
		echo "Agregando columna a tabla 'fija_detalle_inventario'... ";
		$fields = array(
			'HU' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE, 'default' => ''),
		);
		$this->dbforge->add_column('fija_detalle_inventario', $fields);
		echo "OK<br/>";


	}


	public function down()
	{
		echo "Quitando columna a tabla 'fija_detalle_inventario'... ";
		$this->dbforge->drop_column('fija_detalle_inventario', 'HU');
		echo "OK<br/>";
	}

}