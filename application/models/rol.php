<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Clase Modelo Rol
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Rol extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_rol Identificador del modulo
	 * @return void
	 */
	public function __construct($id_rol = NULL)
	{
		$this->_model_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_rol'),
				'model_label'        => 'Rol',
				'model_label_plural' => 'Roles',
				'model_order_by'     => 'rol',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => Orm_field::TIPO_ID,
				),
				'id_app' => array(
					'tipo'           => Orm_field::TIPO_HAS_ONE,
					'relation'       => array(
						'model' => 'app',
					),
					'texto_ayuda'    => 'Aplicaci&oacute;n a la que pertenece el rol.',
					'onchange'       => form_onchange('id_app', 'modulo', 'acl_config/get_select_modulo'),
				),
				'rol' => array(
					'label'          => 'Rol',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del rol. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'descripcion' => array(
					'label'          => 'Descripci&oacute;n del rol',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 100,
					'texto_ayuda'    => 'Descripci&oacute;n del rol. M&aacute;ximo 100 caracteres.',
				),
				'modulo' => array(
					'tipo'           => Orm_field::TIPO_HAS_MANY,
					'relation'       => array(
						'model'         => 'modulo',
						'join_table'    => 'acl_rol_modulo',
						'id_one_table'  => array('id_rol'),
						'id_many_table' => array('id_modulo'),
						'conditions'    => array('id_app' => '@field_value:id_app:NULL'),
					),
					'texto_ayuda'    => 'M&oacute;dulos del rol.',
				),
			),
		);

		parent::__construct($id_rol);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return (string) $this->rol;
	}

}
/* End of file rol.php */
/* Location: ./application/models/rol.php */
