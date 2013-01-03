<?php

class Migration_ini_acl extends CI_Migration {

	public function up()
	{

		// ***************************************************************************
		// TABLA ACL_APP
		// ***************************************************************************
		$fields = array(
			'id' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'auto_increment' => TRUE,
				),
			'app' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
					'default'    => '',
				),
			'descripcion' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
					'default'    => '',
				),
			'orden' => array(
					'type'       => 'INT',
					//'constraint' => '50',
					'null'       => FALSE,
					'default'    => 0,
				),
			'url' => array(
					'type'       => 'VARCHAR',
					'constraint' => '100',
					'null'       => FALSE,
					'default'    => '',
				),
			'icono' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
					'default'    => '',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('acl_app');


		// ***************************************************************************
		// TABLA ACL_MODULO
		// ***************************************************************************
		$fields = array(
			'id' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'auto_increment' => TRUE,
				),
			'id_app' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'default'        => 0,
				),
			'modulo' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
					'default'    => '',
				),
			'descripcion' => array(
					'type'       => 'VARCHAR',
					'constraint' => '100',
					'null'       => FALSE,
					'default'    => '',
				),
			'orden' => array(
					'type'       => 'INT',
					//'constraint' => '50',
					'null'       => FALSE,
					'default'    => 0,
				),
			'url' => array(
					'type'       => 'VARCHAR',
					'constraint' => '100',
					'null'       => FALSE,
					'default'    => '',
				),
			'icono' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
					'default'    => '',
				),
			'llave_modulo' => array(
					'type'       => 'VARCHAR',
					'constraint' => '20',
					'null'       => FALSE,
					'default'    => '',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('acl_modulo');


		// ***************************************************************************
		// TABLA ACL_ROL
		// ***************************************************************************
		$fields = array(
			'id' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'auto_increment' => TRUE,
				),
			'id_app' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'default'        => 0,
				),
			'rol' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
					'default'    => '',
				),
			'descripcion' => array(
					'type'       => 'VARCHAR',
					'constraint' => '100',
					'null'       => FALSE,
					'default'    => '',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('acl_rol');


		// ***************************************************************************
		// TABLA ACL_ROL_MODULO
		// ***************************************************************************
		$fields = array(
			'id_rol' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'default'        => 0,
				),
			'id_modulo' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'default'        => 0,
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id_rol', TRUE);
		$this->dbforge->add_key('id_modulo', TRUE);
		$this->dbforge->create_table('acl_rol_modulo');


		// ***************************************************************************
		// TABLA ACL_USUARIO_ROL
		// ***************************************************************************
		$fields = array(
			'id_usuario' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'default'        => 0,
				),
			'id_rol' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'default'        => 0,
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id_usuario', TRUE);
		$this->dbforge->add_key('id_rol', TRUE);
		$this->dbforge->create_table('acl_usuario_rol');


		// ***************************************************************************
		// TABLA FIJA_USUARIOS
		// ***************************************************************************
		$fields = array(
			'id' => array(
					'type'           => 'BIGINT',
					//'constraint'     => '10',
					'null'           => FALSE,
					'auto_increment' => TRUE,
				),
			'nombre' => array(
					'type'       => 'VARCHAR',
					'constraint' => '50',
					'null'       => FALSE,
					'default'    => '',
				),
			'tipo' => array(
					'type'       => 'VARCHAR',
					'constraint' => '10',
					'null'       => FALSE,
					'default'    => '',
				),
			'activo' => array(
					'type'       => 'TINYINT',
					//'constraint' => '1',
					'null'       => FALSE,
					'default'    => 0,
				),
			'usr' => array(
					'type'       => 'VARCHAR',
					'constraint' => '30',
					'null'       => FALSE,
					'default'    => '',
				),
			'pwd' => array(
					'type'       => 'VARCHAR',
					'constraint' => '40',
					'null'       => FALSE,
					'default'    => '',
				),
			'correo' => array(
					'type'       => 'VARCHAR',
					'constraint' => '40',
					'null'       => FALSE,
					'default'    => '',
				),
		);
		$this->dbforge->add_field($fields);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('fija_usuarios');


		// ***************************************************************************
		// INICIALIZA ACL
		// ***************************************************************************
		$insert_data = array(
				'nombre' => 'acl',
				'activo' => 1,
				'usr'    => 'acl',
			);
		$this->db->insert('fija_usuarios', $insert_data);

		$insert_data = array(
				'app'   => 'acl',
				'url'   => '/acl',
				'orden' => 0,
			);
		$this->db->insert('acl_app', $insert_data);

		$insert_data = array(
				'id_app' => 1,
				'modulo' => 'config acl',
				'llave_modulo' => 'acl_config',
				'url' => '/acl',
				'orden' => 0,
			);
		$this->db->insert('acl_modulo', $insert_data);

		$insert_data = array(
				'id_app' => 1,
				'rol' => 'config acl',
			);
		$this->db->insert('acl_rol', $insert_data);

		$insert_data = array(
				'id_rol'    => 1,
				'id_modulo' => 1,
			);
		$this->db->insert('acl_rol_modulo', $insert_data);

		$insert_data = array(
				'id_usuario' => 1,
				'id_rol'    => 1,
			);
		$this->db->insert('acl_usuario_rol', $insert_data);
	}


	public function down()
	{
		$this->dbforge->drop_table('acl_app');
		$this->dbforge->drop_table('acl_modulo');
		$this->dbforge->drop_table('acl_rol');
		$this->dbforge->drop_table('acl_rol_modulo');
		$this->dbforge->drop_table('acl_usuario_rol');
		$this->dbforge->drop_table('fija_usuarios');
	}

}