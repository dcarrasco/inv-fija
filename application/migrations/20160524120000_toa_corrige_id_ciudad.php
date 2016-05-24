<?php

class Migration_toa_corrige_id_ciudad extends CI_Migration {

	/**
	 * Actualiza la BD
	 *
	 * @return void
	 */
	public function up()
	{
		// --------------------------------------------------------------------

/*
		echo "Corrige tabla 'toa_ciudades'...".PHP_EOL;
		$this->dbforge->drop_table('bd_toa..toa_ciudades', TRUE);
		$this->dbforge->add_field(array(
			'id_ciudad' => array('type' => 'VARCHAR', 'constraint' => 10, 'null' => FALSE),
			'ciudad'    => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE),
			'orden'     => array('type' => 'BIGINT', 'null' => FALSE),
		));
		$this->dbforge->add_key('id_ciudad', TRUE);
		$this->dbforge->create_table('bd_toa..toa_ciudades');
*/

		// --------------------------------------------------------------------

/*
		echo "Corrige tabla 'toa_tecnicos'...".PHP_EOL;
		$this->dbforge->drop_column('bd_toa..toa_tecnicos', 'id_ciudad');
		$this->dbforge->add_column('bd_toa..toa_tecnicos', array(
			'id_ciudad' => array('type' => 'VARCHAR', 'constraint' => 10, 'null' => TRUE),
		));
*/
		// --------------------------------------------------------------------

/*
		echo "Corrige tabla 'toa_empresas_ciudades'...".PHP_EOL;
		$this->dbforge->drop_table('bd_toa..toa_empresas_ciudades', TRUE);
		$this->dbforge->add_field(array(
			'id_ciudad'  => array('type' => 'VARCHAR', 'constraint' => '10', 'null' => FALSE),
			'id_empresa' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
		));
		$this->dbforge->add_key('id_ciudad', TRUE);
		$this->dbforge->add_key('id_empresa', TRUE);
		$this->dbforge->create_table('bd_toa..toa_empresas_ciudades');
*/

		// --------------------------------------------------------------------

/*
		echo "Corrige tabla 'toa_empresas_ciudades_almacenes'...".PHP_EOL;
		$this->dbforge->drop_table('bd_toa..toa_empresas_ciudades_almacenes', TRUE);
		$this->dbforge->add_field(array(
			'id_ciudad'   => array('type' => 'VARCHAR', 'constraint' => '10', 'null' => FALSE),
			'id_empresa'  => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
			'centro'      => array('type' => 'VARCHAR', 'constraint' => '10', 'null' => FALSE),
			'cod_almacen' => array('type' => 'VARCHAR', 'constraint' => '10', 'null' => FALSE),
		));
		$this->dbforge->add_key('id_ciudad', TRUE);
		$this->dbforge->add_key('id_empresa', TRUE);
		$this->dbforge->add_key('centro', TRUE);
		$this->dbforge->add_key('cod_almacen', TRUE);
		$this->dbforge->create_table('bd_toa..toa_empresas_ciudades_almacenes');
*/

		// --------------------------------------------------------------------

		echo "Agrega data en tabla 'toa_ciudades'...".PHP_EOL;
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'STGO', 'ciudad' => 'Santiago',     'orden' => 1));
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'ARCA', 'ciudad' => 'Arica',        'orden' => 10));
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'IQQE', 'ciudad' => 'Iquique',      'orden' => 11));
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'ANTF', 'ciudad' => 'Antofagasta',  'orden' => 20));
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'CLMA', 'ciudad' => 'Calama',       'orden' => 21));
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'CPPO', 'ciudad' => 'Copiapo',      'orden' => 30));
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'LSRN', 'ciudad' => 'La Serena',    'orden' => 40));
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'VLPO', 'ciudad' => 'Valparaiso',   'orden' => 50));
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'SFDO', 'ciudad' => 'San Fernando', 'orden' => 70));
		$this->db->insert('bd_toa..toa_ciudades', array('id_ciudad' => 'PARE', 'ciudad' => 'Punta Arenas', 'orden' => 120));

		echo "Modifica data en tabla 'toa_tecnicos'...".PHP_EOL;
		$this->db->where('id_ciudad', 4)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'STGO'));
		$this->db->where('id_ciudad', 6)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'ARCA'));
		$this->db->where('id_ciudad', 7)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'IQQE'));
		$this->db->where('id_ciudad', 1)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'ANTF'));
		$this->db->where('id_ciudad', 2)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'CLMA'));
		$this->db->where('id_ciudad', 3)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'CPPO'));
		$this->db->where('id_ciudad', 8)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'LSRN'));
		$this->db->where('id_ciudad', 5)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'VLPO'));
		$this->db->where('id_ciudad', 9)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'SFDO'));
		$this->db->where('id_ciudad', 10)->update('bd_toa..toa_tecnicos', array('id_ciudad' => 'PARE'));

		echo "Agrega data en tabla 'toa_empresas_ciudades'...".PHP_EOL;
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'ANTF', 'id_empresa' => 'LAR'));
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'CLMA', 'id_empresa' => 'LAR'));
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'CPPO', 'id_empresa' => 'LAR'));
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'STGO', 'id_empresa' => 'CBA'));
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'STGO', 'id_empresa' => 'LAR'));
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'ARCA', 'id_empresa' => 'CBA'));
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'IQQE', 'id_empresa' => 'CBA'));
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'LSRN', 'id_empresa' => 'CBA'));
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'SFDO', 'id_empresa' => 'CBA'));
		$this->db->insert('bd_toa..toa_empresas_ciudades', array('id_ciudad' => 'PARE', 'id_empresa' => 'CBA'));

		echo "Agrega data en tabla 'toa_empresas_ciudades_almacenes'...".PHP_EOL;
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'ANTF', 'id_empresa' => 'LAR', 'centro' => 'CH32', 'cod_almacen' => 'BN99'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'ANTF', 'id_empresa' => 'LAR', 'centro' => 'CH33', 'cod_almacen' => 'BNB7'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'CLMA', 'id_empresa' => 'LAR', 'centro' => 'CH32', 'cod_almacen' => 'BNA2'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'CLMA', 'id_empresa' => 'LAR', 'centro' => 'CH33', 'cod_almacen' => 'BNC0'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'CPPO', 'id_empresa' => 'LAR', 'centro' => 'CH32', 'cod_almacen' => 'BNA5'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'CPPO', 'id_empresa' => 'LAR', 'centro' => 'CH33', 'cod_almacen' => 'BNC3'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'STGO', 'id_empresa' => 'CBA', 'centro' => 'CH32', 'cod_almacen' => 'BM34'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'STGO', 'id_empresa' => 'CBA', 'centro' => 'CH33', 'cod_almacen' => 'BM31'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'STGO', 'id_empresa' => 'LAR', 'centro' => 'CH32', 'cod_almacen' => 'BM77'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'STGO', 'id_empresa' => 'LAR', 'centro' => 'CH33', 'cod_almacen' => 'BM70'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'ARCA', 'id_empresa' => 'CBA', 'centro' => 'CH32', 'cod_almacen' => 'BN28'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'ARCA', 'id_empresa' => 'CBA', 'centro' => 'CH33', 'cod_almacen' => 'BN29'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'IQQE', 'id_empresa' => 'CBA', 'centro' => 'CH32', 'cod_almacen' => 'BN32'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'IQQE', 'id_empresa' => 'CBA', 'centro' => 'CH33', 'cod_almacen' => 'BN33'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'LSRN', 'id_empresa' => 'CBA', 'centro' => 'CH32', 'cod_almacen' => 'BN45'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'LSRN', 'id_empresa' => 'CBA', 'centro' => 'CH33', 'cod_almacen' => 'BN46'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'SFDO', 'id_empresa' => 'CBA', 'centro' => 'CH32', 'cod_almacen' => 'BC27'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'SFDO', 'id_empresa' => 'CBA', 'centro' => 'CH33', 'cod_almacen' => 'BC26'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'PARE', 'id_empresa' => 'CBA', 'centro' => 'CH32', 'cod_almacen' => 'BS59'));
		$this->db->insert('bd_toa..toa_empresas_ciudades_almacenes', array('id_ciudad' => 'PARE', 'id_empresa' => 'CBA', 'centro' => 'CH33', 'cod_almacen' => 'BS53'));


		echo 'OK'.PHP_EOL;
	}

	/**
	 * Rollback
	 *
	 * @return void
	 */
	public function down()
	{
		// --------------------------------------------------------------------

		echo "Des-corrige tabla 'toa_ciudades'... ".PHP_EOL;
		$this->dbforge->drop_table('toa_ciudades', TRUE);
		$this->dbforge->add_field(
			array(
				'id'     => array('type' => 'BIGINT', 'null' => FALSE, 'auto_increment' => TRUE),
				'ciudad' => array('type' => 'VARCHAR', 'constraint' => 50, 'null' => FALSE),
				'orden'  => array('type' => 'BIGINT', 'null' => FALSE),
			)
		);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('toa_ciudades');

		// --------------------------------------------------------------------

		echo "Des-corrige tabla 'toa_tecnicos'...".PHP_EOL;
		$this->dbforge->drop_column('toa_tecnicos', 'id_ciudad');
		$this->dbforge->add_column('toa_tecnicos', array(
			'id_ciudad'   => array('type' => 'BIGINT', 'null' => TRUE),
		));

		// --------------------------------------------------------------------

		echo "Des-corrige tabla 'toa_empresas_ciudades'...".PHP_EOL;
		$this->dbforge->drop_table('toa_empresas_ciudades', TRUE);
		$this->dbforge->add_field(
			array(
				'id_ciudad'  => array('type' => 'BIGINT', 'null' => FALSE),
				'id_empresa' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
			)
		);
		$this->dbforge->add_key('id_ciudad', TRUE);
		$this->dbforge->add_key('id_empresa', TRUE);
		$this->dbforge->create_table('toa_empresas_ciudades');

		// --------------------------------------------------------------------

		echo "Des-corrige tabla 'toa_empresas_ciudades_almacenes'...".PHP_EOL;
		$this->dbforge->drop_table('toa_empresas_ciudades_almacenes', TRUE);
		$this->dbforge->add_field(
			array(
				'id_ciudad'   => array('type' => 'BIGINT', 'null' => FALSE),
				'id_empresa'  => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE),
				'centro'      => array('type' => 'VARCHAR', 'constraint' => '10', 'null' => FALSE),
				'cod_almacen' => array('type' => 'VARCHAR', 'constraint' => '10', 'null' => FALSE),
			)
		);
		$this->dbforge->add_key('id_ciudad', TRUE);
		$this->dbforge->add_key('id_empresa', TRUE);
		$this->dbforge->add_key('centro', TRUE);
		$this->dbforge->add_key('cod_almacen', TRUE);
		$this->dbforge->create_table('toa_empresas_ciudades_almacenes');

		echo 'OK'.PHP_EOL;
	}

}