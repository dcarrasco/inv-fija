<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Usuario_model extends MY_Model {

	protected $tabla_bd     = 'fija_usuarios';
	protected $label        = 'Usuario';
	protected $label_plural = 'Usuarios';
	protected $orden        = 'nombre';

	protected $field_info = array(
		'id' => array(
			'tipo'   => 'ID',
		),
		'nombre' => array(
			'label'          => 'Nombre de usuario',
			'tipo'           => 'CHAR',
			'largo'          => 45,
			'texto_ayuda'    => 'Nombre del usuario. Maximo 45 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		),
		/*
		'tipo' => array(
			'label'          => 'Descripcion de la Aplicacion',
			'tipo'           =>  'CHAR',
			'largo'          => 50,
			'texto_ayuda'    => 'Maximo 50 caracteres.',
			'es_obligatorio' => TRUE,
		),
		*/
		'activo' => array(
			'label'          => 'Activo',
			'tipo'           =>  'BOOLEAN',
			'texto_ayuda'    => 'Indica se el usuario esta activo dentro del sistema.',
			'es_obligatorio' => TRUE,
		),
		'usr' => array(
			'label'          => 'Username',
			'tipo'           =>  'CHAR',
			'largo'          => 30,
			'texto_ayuda'    => 'Username para el ingreso al sistema. Maximo 30 caracteres.',
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		),
		'pwd' => array(
			'label'          => 'Password',
			'tipo'           =>  'CHAR',
			'largo'          => 40,
			'texto_ayuda'    => 'Password para el ingreso al sistema. Maximo 40 caracteres.',
			'mostrar_lista'  => FALSE,
		),
		'correo' => array(
			'label'          => 'Correo',
			'tipo'           =>  'CHAR',
			'largo'          => 40,
			'texto_ayuda'    => 'Correo del usuario. Maximo 40 caracteres.',
			'mostrar_lista'  => FALSE,
		),
		'rol' => array(
			'tipo'           => 'HAS_MANY',
			'relation'       => array(
				'model'         => 'rol_model',
				'join_table'    => 'acl_usuario_rol',
				'id_one_table'  => array('id_usuario'),
				'id_many_table' => array('id_rol'),
			),
			'texto_ayuda'    => 'Roles asociados al usuario.',
		),
	);

	public function __construct()
	{
		parent::__construct();
	}

	public function __toString()
	{
		return $this->valores['nombre'];
	}

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



	public function ___get_usuarios($limit = 0, $offset = 0)
	{
		$this->db->order_by('tipo ASC, nombre ASC');

		return $this->db->get('fija_usuarios', $limit, $offset)->result_array();
	}



	public function ___total_usuarios()
	{
		return $this->db->count_all('fija_usuarios');
	}


	public function ___get_cant_registros_usuario($id = 0)
	{
		return ($this->db->get_where('fija_detalle_inventario', array('digitador' => $id))->num_rows() +
				$this->db->get_where('fija_detalle_inventario', array('auditor' => $id))->num_rows());
	}



}

/* End of file usuario_model.php */
/* Location: ./application/models/usuario_model.php */