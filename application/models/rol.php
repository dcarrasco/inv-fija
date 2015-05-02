<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Rol extends ORM_Model {

	public function __construct($id = null)
	{
		parent::__construct();

		$cfg = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_rol'),
				'model_label'        => 'Rol',
				'model_label_plural' => 'Roles',
				'model_order_by'     => 'rol',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => 'id',
				),
				'id_app' => array(
					'tipo'           => 'has_one',
					'relation'       => array(
						'model' => 'app',
					),
					'texto_ayuda'    => 'Aplicaci&oacute;n a la que pertenece el rol.',
				),
				'rol' => array(
					'label'          => 'Rol',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del rol. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'descripcion' => array(
					'label'          => 'Descripci&oacute;n del rol',
					'tipo'           => 'char',
					'largo'          => 100,
					'texto_ayuda'    => 'Descripci&oacute;n del rol. M&aacute;ximo 100 caracteres.',
				),
				'modulo' => array(
					'tipo'           => 'has_many',
					'relation'       => array(
						'model'         => 'modulo',
						'join_table'    => 'acl_rol_modulo',
						'id_one_table'  => array('id_rol'),
						'id_many_table' => array('id_modulo'),
						'conditions'    => array('id_app' => '@field_value:id_app'),
					),
					'texto_ayuda'    => 'M&oacute;dulos del rol.',
				),
			),
		);

		$this->config_model($cfg);

		if ($id)
		{
			$this->fill($id);
		}
	}


	// --------------------------------------------------------------------

	public function __toString()
	{
		return (string) $this->rol;
	}

}
/* End of file rol.php */
/* Location: ./application/models/rol.php */