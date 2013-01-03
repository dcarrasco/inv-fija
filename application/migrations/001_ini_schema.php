<?php

class Migration_ini_schema extends CI_Migration {

	public function up()
	{

		// ***************************************************************************
		// TABLA FIJA_ALMACENES
		// ***************************************************************************
		echo "Creando tabla 'fija_almacenes'... ";
		$fields = array(
			'almacen' => array(
					'type'       => 'VARCHAR',
					'constraint' => '10',
					'null'       => FALSE,
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('almacen', TRUE);
		$this->dbforge->create_table('fija_almacenes');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_AUDITORES
		// ***************************************************************************
		echo "Creando tabla 'fija_auditores'... ";
		$fields = array(
			'id' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'auto_increment' => TRUE,
				),
			'nombre' => array(
					'type'       => 'VARCHAR',
					'constraint' => '45',
					'null'       => FALSE,
				),
			'activo' => array(
					'type'       => 'TINYINT',
					//'constraint' => 1,
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('fija_auditores');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_CATALOGO
		// ***************************************************************************
		echo "Creando tabla 'fija_catalogo'... ";
		$fields = array(
			'catalogo' => array(
					'type'       => 'VARCHAR',
					'constraint' => '45',
					'null'       => FALSE,
				),
			'descripcion' => array(
					'type'       => 'VARCHAR',
					'constraint' => '100',
					'null'       => FALSE,
				),
			'pmp' => array(
					'type'       => 'DECIMAL',
					'constraint' => '10,2',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('catalogo', TRUE);
		$this->dbforge->create_table('fija_catalogo');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_CATALOGO_FAMILIAS
		// ***************************************************************************
		echo "Creando tabla 'fija_catalogo_familias'... ";
		$fields = array(
			'codigo' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
				),
			'tipo' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
				),
			'nombre' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('codigo', TRUE);
		$this->dbforge->create_table('fija_catalogo_familias');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_CENTROS
		// ***************************************************************************
		echo "Creando tabla 'fija_centros'... ";
		$fields = array(
			'centro' => array(
					'type'       => 'VARCHAR',
					'constraint' => '10',
					'null'       => FALSE,
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('centro', TRUE);
		$this->dbforge->create_table('fija_centros');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_DETALLE_INVENTARIO
		// ***************************************************************************
		echo "Creando tabla 'fija_detalle_inventario'... ";
		$fields = array(
			'id' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'auto_increment' => TRUE,
				),
			'id_inventario' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
				),
			'ubicacion' => array(
					'type'       => 'VARCHAR',
					'constraint' => '45',
					'null'       => FALSE,
					'default'    => '',
				),
			'catalogo' => array(
					'type'       => 'VARCHAR',
					'constraint' => '45',
					'null'       => FALSE,
					'default'    => '',
				),
			'descripcion' => array(
					'type'       => 'VARCHAR',
					'constraint' => '100',
					'null'       => FALSE,
					'default'    => '',
				),
			'lote' => array(
					'type'       => 'VARCHAR',
					'constraint' => '45',
					'null'       => FALSE,
					'default'    => '',
				),
			'centro' => array(
					'type'       => 'VARCHAR',
					'constraint' => '10',
					'null'       => FALSE,
					'default'    => '',
				),
			'almacen' => array(
					'type'       => 'VARCHAR',
					'constraint' => '10',
					'null'       => FALSE,
					'default'    => '',
				),
			'um' => array(
					'type'       => 'VARCHAR',
					'constraint' => '10',
					'null'       => FALSE,
					'default'    => '',
				),
			'stock_sap' => array(
					'type'       => 'INT',
					//'constraint' => '10',
					'null'       => FALSE,
					'default'    => 0,
				),
			'stock_fisico' => array(
					'type'       => 'INT',
					//'constraint' => '10',
					'null'       => FALSE,
					'default'    => 0,
				),
			'digitador' => array(
					'type'       => 'BIGINT',
					//'constraint' => '10',
				),
			'auditor' => array(
					'type'       => 'BIGINT',
					//'constraint' => '10',
				),
			'hoja' => array(
					'type'       => 'INT',
					//'constraint' => '10',
					'null'       => FALSE,
					'default'    => 0,
				),
			'reg_nuevo' => array(
					'type'       => 'CHAR',
					'constraint' => '1',
					'null'       => FALSE,
					'default'    => '',
				),
			'observacion' => array(
					'type'       => 'VARCHAR',
					'constraint' => '100',
					'null'       => FALSE,
					'default'    => '',
				),
			'fecha_modificacion' => array(
					'type'       => 'DATETIME',
				),
			'stock_ajuste' => array(
					'type'       => 'INT',
					//'constraint' => '10',
					'null'       => FALSE,
					'default'    => 0,
				),
			'glosa_ajuste' => array(
					'type'       => 'VARCHAR',
					'constraint' => '200',
					'null'       => FALSE,
					'default'    => '',
				),
			'fecha_ajuste' => array(
					'type'       => 'DATETIME',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('fija_detalle_inventario');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_INVENTARIO2
		// ***************************************************************************
		echo "Creando tabla 'fija_inventario2'... ";
		$fields = array(
			'id' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'auto_increment' => TRUE,
				),
			'nombre' => array(
					'type'       => 'VARCHAR',
					'constraint' => '100',
					'null'       => FALSE,
					'default'    => '',
				),
			'activo' => array(
					'type'       => 'TINYINT',
					//'constraint' => '1',
					'null'       => FALSE,
					'default'    => 0,
				),
			'tipo_inventario' => array(
					'type'       => 'VARCHAR',
					'constraint' => '30',
					'null'       => FALSE,
					'default'    => '',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('fija_inventario2');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_TIPO_UBICACION
		// ***************************************************************************
		echo "Creando tabla 'fija_tipo_ubicacion'... ";
		$fields = array(
			'id' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'auto_increment' => TRUE,
				),
			'tipo_inventario' => array(
					'type'       => 'VARCHAR',
					'constraint' => '30',
					'null'       => FALSE,
					'default'    => '',
				),
			'tipo_ubicacion' => array(
					'type'       => 'VARCHAR',
					'constraint' => '30',
					'null'       => FALSE,
					'default'    => '',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('fija_tipo_ubicacion');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_TIPOS_INVENTARIO
		// ***************************************************************************
		echo "Creando tabla 'fija_tipos_inventario'... ";
		$fields = array(
			'id_tipo_inventario' => array(
					'type'           => 'VARCHAR',
					'constraint'     => '10',
					'null'           => FALSE,
					'default'        => '',
				),
			'desc_tipo_inventario' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
					'default'    => '',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id_tipo_inventario', TRUE);
		$this->dbforge->create_table('fija_tipos_inventario');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_UBICACION_TIPO_UBICACION
		// ***************************************************************************
		echo "Creando tabla 'fija_ubicacion_tipo_ubicacion'... ";
		$fields = array(
			'id' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'auto_increment' => TRUE,
				),
			'tipo_inventario' => array(
					'type'       => 'VARCHAR',
					'constraint' => '30',
					'null'       => FALSE,
					'default'    => '',
				),
			'ubicacion' => array(
					'type'       => 'VARCHAR',
					'constraint' => '45',
					'null'       => FALSE,
					'default'    => '',
				),
			'id_tipo_ubicacion' => array(
					'type'       => 'BIGINT',
					//'constraint' => '10',
					'null'       => FALSE,
					'default'    => 0,
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('fija_ubicacion_tipo_ubicacion');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_UNIDADES
		// ***************************************************************************
		echo "Creando tabla 'fija_unidades'... ";
		$fields = array(
			'unidad' => array(
					'type'       => 'VARCHAR',
					'constraint' => '10',
					'null'       => FALSE,
					'default'    => '',
				),
			'desc_unidad' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
					'default'    => '',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('unidad', TRUE);
		$this->dbforge->create_table('fija_unidades');
		echo "OK<br/>";


	}


	public function down()
	{
		echo "Borrando tabla 'fija_almacenes'... ";
		$this->dbforge->drop_table('fija_almacenes');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_auditores'... ";
		$this->dbforge->drop_table('fija_auditores');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_catalogo'... ";
		$this->dbforge->drop_table('fija_catalogo');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_catalogo_familias'... ";
		$this->dbforge->drop_table('fija_catalogo_familias');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_centros'... ";
		$this->dbforge->drop_table('fija_centros');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_detalle_inventario'... ";
		$this->dbforge->drop_table('fija_detalle_inventario');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_inventario2'... ";
		$this->dbforge->drop_table('fija_inventario2');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_tipo_ubicacion'... ";
		$this->dbforge->drop_table('fija_tipo_ubicacion');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_tipos_inventario'... ";
		$this->dbforge->drop_table('fija_tipos_inventario');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_ubicacion_tipo_ubicacion'... ";
		$this->dbforge->drop_table('fija_ubicacion_tipo_ubicacion');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_unidades'... ";
		$this->dbforge->drop_table('fija_unidades');
		echo "OK<br/>";

	}

}