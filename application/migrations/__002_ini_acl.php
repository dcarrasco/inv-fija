<?php

class Migration_ini_acl extends CI_Migration {

	public function up()
	{

		// ***************************************************************************
		// TABLA ACL_APP
		// ***************************************************************************
		echo "Creando tabla 'acl_app'... ";
		$this->dbforge->add_field(
			array(
				'id'          => array('type' => 'BIGINT', 'null' => FALSE, 'auto_increment' => TRUE),
				'app'         => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => FALSE, 'default' => ''),
				'descripcion' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => FALSE, 'default' => ''),
				'orden'       => array('type' => 'INT', 'null' => FALSE, 'default' => 0),
				'url'         => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE, 'default' => ''),
				'icono'       => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => FALSE, 'default' => ''),
			)
		);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('acl_app');

		echo "OK<br/>";


		// ***************************************************************************
		// TABLA ACL_MODULO
		// ***************************************************************************
		echo "Creando tabla 'acl_modulo'... ";
		$this->dbforge->add_field(
			array(
				'id'           => array('type' => 'BIGINT', 'null' => FALSE, 'auto_increment' => TRUE),
				'id_app'       => array('type' => 'BIGINT', 'null' => FALSE, 'default' => 0),
				'modulo'       => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => FALSE, 'default'  => ''),
				'descripcion'  => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE, 'default'  => ''),
				'orden'        => array('type' => 'INT', 'null' => FALSE, 'default'  => 0),
				'url'          => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE, 'default'  => ''),
				'icono'        => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => FALSE, 'default'  => ''),
				'llave_modulo' => array('type' => 'VARCHAR', 'constraint' => '20', 'null' => FALSE, 'default'  => ''),
			)
		);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('acl_modulo');

		echo "OK<br/>";


		// ***************************************************************************
		// TABLA ACL_ROL
		// ***************************************************************************
		echo "Creando tabla 'acl_rol'... ";

		$this->dbforge->add_field(
			array(
				'id'          => array('type' => 'BIGINT', 'null' => FALSE, 'auto_increment' => TRUE),
				'id_app'      => array('type' => 'BIGINT', 'null' => FALSE, 'default' => 0),
				'rol'         => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => FALSE, 'default' => ''),
				'descripcion' => array('type' => 'VARCHAR', 'constraint' => '100', 'null' => FALSE, 'default' => ''),
			)
		);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('acl_rol');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA ACL_ROL_MODULO
		// ***************************************************************************
		echo "Creando tabla 'acl_rol_modulo'... ";

		$this->dbforge->add_field(
			array(
				'id_rol'    => array('type' => 'BIGINT', 'null' => FALSE, 'default' => 0,),
				'id_modulo' => array('type' => 'BIGINT', 'null' => FALSE, 'default' => 0,),
			)
		);

		$this->dbforge->add_key('id_rol', TRUE);
		$this->dbforge->add_key('id_modulo', TRUE);
		$this->dbforge->create_table('acl_rol_modulo');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA ACL_USUARIO_ROL
		// ***************************************************************************
		echo "Creando tabla 'acl_usuario_rol'... ";

		$this->dbforge->add_field(
			array(
				'id_usuario' => array('type' => 'BIGINT', 'null' => FALSE, 'default' => 0),
				'id_rol'     => array('type' => 'BIGINT', 'null' => FALSE, 'default' => 0,),
			)
		);

		$this->dbforge->add_key('id_usuario', TRUE);
		$this->dbforge->add_key('id_rol', TRUE);
		$this->dbforge->create_table('acl_usuario_rol');
		echo "OK<br/>";


		// ***************************************************************************
		// TABLA FIJA_USUARIOS
		// ***************************************************************************
		echo "Creando tabla 'fija_usuarios'... ";

		$this->dbforge->add_field(
			array(
				'id'     => array('type' => 'BIGINT', 'null' => FALSE, 'auto_increment' => TRUE),
				'nombre' => array('type' => 'VARCHAR', 'constraint' => '50', 'null' => FALSE, 'default' => ''),
				'tipo'   => array('type' => 'VARCHAR', 'constraint' => '10', 'null' => FALSE, 'default' => ''),
				'activo' => array('type' => 'TINYINT', 'null' => FALSE, 'default' => 0),
				'usr'    => array('type' => 'VARCHAR', 'constraint' => '30', 'null' => FALSE, 'default' => ''),
				'pwd'    => array('type' => 'VARCHAR', 'constraint' => '40', 'null' => FALSE, 'default' => ''),
				'correo' => array('type' => 'VARCHAR', 'constraint' => '40', 'null' => FALSE, 'default' => ''),
			)
		);

		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('fija_usuarios');
		echo "OK<br/>";


		// ***************************************************************************
		// INICIALIZA ACL
		// ***************************************************************************
		echo "Insertando datos iniciales en  'fija_usuarios'... ";
		$insert_data = array(
				'nombre' => 'acl',
				'activo' => 1,
				'usr'    => 'acl',
			);
		$this->db->insert('fija_usuarios', $insert_data);
		echo "OK<br/>";


		echo "Insertando datos iniciales en  'acl_app'... ";
		$insert_data = array(
				'app'   => 'acl',
				'url'   => '/acl',
				'orden' => 0,
			);
		$this->db->insert('acl_app', $insert_data);
		echo "OK<br/>";

		echo "Insertando datos iniciales en  'acl_modulo'... ";
		$insert_data = array(
				'id_app' => 1,
				'modulo' => 'config acl',
				'llave_modulo' => 'acl_config',
				'url' => '/acl',
				'orden' => 0,
			);
		$this->db->insert('acl_modulo', $insert_data);
		echo "OK<br/>";

		echo "Insertando datos iniciales en  'acl_rol'... ";
		$insert_data = array(
				'id_app' => 1,
				'rol' => 'config acl',
			);
		$this->db->insert('acl_rol', $insert_data);
		echo "OK<br/>";

		echo "Insertando datos iniciales en  'acl_rol_modulo'... ";
		$insert_data = array(
				'id_rol'    => 1,
				'id_modulo' => 1,
			);
		$this->db->insert('acl_rol_modulo', $insert_data);
		echo "OK<br/>";

		echo "Insertando datos iniciales en  'acl_usuario_rol'... ";
		$insert_data = array(
				'id_usuario' => 1,
				'id_rol'    => 1,
			);
		$this->db->insert('acl_usuario_rol', $insert_data);
		echo "OK<br/>";
	}


	public function down()
	{
		echo "Borrando tabla 'acl_app'... ";
		$this->dbforge->drop_table('acl_app');
		echo "OK<br/>";

		echo "Borrando tabla 'acl_modulo'... ";
		$this->dbforge->drop_table('acl_modulo');
		echo "OK<br/>";

		echo "Borrando tabla 'acl_rol'... ";
		$this->dbforge->drop_table('acl_rol');
		echo "OK<br/>";

		echo "Borrando tabla 'acl_rol_modulo'... ";
		$this->dbforge->drop_table('acl_rol_modulo');
		echo "OK<br/>";

		echo "Borrando tabla 'acl_usuario_rol'... ";
		$this->dbforge->drop_table('acl_usuario_rol');
		echo "OK<br/>";

		echo "Borrando tabla 'fija_usuarios'... ";
		$this->dbforge->drop_table('fija_usuarios');
		echo "OK<br/>";
	}

}