<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario extends ORM_Model {

	public function __construct()
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_usuarios'),
				'model_label'        => 'Usuario',
				'model_label_plural' => 'Usuarios',
				'model_order_by'     => 'nombre',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => 'id',
				),
				'nombre' => array(
					'label'          => 'Nombre de usuario',
					'tipo'           => 'char',
					'largo'          => 45,
					'texto_ayuda'    => 'Nombre del usuario. Maximo 45 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
/*
				'tipo' => array(
					'label'          => 'Descripcion de la Aplicacion',
					'tipo'           =>  'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Maximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
*/
				'activo' => array(
					'label'          => 'Activo',
					'tipo'           =>  'boolean',
					'texto_ayuda'    => 'Indica se el usuario esta activo dentro del sistema.',
					'es_obligatorio' => TRUE,
				),
				'usr' => array(
					'label'          => 'Username',
					'tipo'           =>  'char',
					'largo'          => 30,
					'texto_ayuda'    => 'Username para el ingreso al sistema. Maximo 30 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'pwd' => array(
					'label'          => 'Password',
					'tipo'           =>  'char',
					'largo'          => 40,
					'texto_ayuda'    => 'Password para el ingreso al sistema. Maximo 40 caracteres.',
					'mostrar_lista'  => FALSE,
				),
				'correo' => array(
					'label'          => 'Correo',
					'tipo'           =>  'char',
					'largo'          => 40,
					'texto_ayuda'    => 'Correo del usuario. Maximo 40 caracteres.',
					'mostrar_lista'  => FALSE,
				),
				'fecha_login' => array(
					'label'          => 'Fecha ultimo login',
					'tipo'           =>  'datetime',
					'largo'          => 40,
					'texto_ayuda'    => 'Fecha de la ultima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				),
				'ip_login' => array(
					'label'          => 'Dirección IP',
					'tipo'           =>  'char',
					'largo'          => 30,
					'texto_ayuda'    => 'Dirección IP de la ultima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				),
				'agente_login' => array(
					'label'          => 'Agente',
					'tipo'           =>  'char',
					'largo'          => 200,
					'texto_ayuda'    => 'Agente web de la ultima entrada al sistema.',
					'mostrar_lista'  => FALSE,
				),
				'rol' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'rol',
						'join_table'    => $this->CI->config->item('bd_usuario_rol'),
						'id_one_table'  => array('id_usuario'),
						'id_many_table' => array('id_rol'),
					),
					'texto_ayuda'    => 'Roles asociados al usuario.',
				),
			),
		);

		$this->config_model($cfg);
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return $this->nombre;
	}


	// --------------------------------------------------------------------

	public function ___get_combo_usuarios($tipo = 'AUD')
	{
		$arr_result = array();
		$arr_combo = array();

		$arr_combo[''] = 'Seleccione un ';
		$arr_combo[''] .= ($tipo == 'AUD') ? 'auditor ...' : 'digitador ...';

		$this->db->order_by('nombre');
		$this->db->where('tipo', $tipo);
		$this->db->where('activo','1');
		$arr_result = $this->db->get('fija_usuarios')->result_array();

		foreach($arr_result as $reg)
		{
			$arr_combo[$reg['id']] = $reg['nombre'];
		}

		return $arr_combo;
	}


	// --------------------------------------------------------------------

	public function ___get_usuarios($limit = 0, $offset = 0)
	{
		$this->db->order_by('tipo ASC, nombre ASC');

		return $this->db->get('fija_usuarios', $limit, $offset)->result_array();
	}


	// --------------------------------------------------------------------

	public function ___total_usuarios()
	{
		return $this->db->count_all('fija_usuarios');
	}

	// --------------------------------------------------------------------

	public function ___get_cant_registros_usuario($id = 0)
	{
		return ($this->db->get_where('fija_detalle_inventario', array('digitador' => $id))->num_rows() +
				$this->db->get_where('fija_detalle_inventario', array('auditor' => $id))->num_rows());
	}



}
/* End of file usuario.php */
/* Location: ./application/models/usuario.php */