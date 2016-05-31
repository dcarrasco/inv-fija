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
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_rol'),
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
					'onchange'       => "\$('#id_modulo').html('');\$.get('".site_url('acl_config/get_select_modulo')."'+'/'+\$('#id_id_app').val(), function(data){\$('#id_modulo').html(data);});",
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
						'conditions'    => array('id_app' => '@field_value:id_app:NULL'),
					),
					'texto_ayuda'    => 'M&oacute;dulos del rol.',
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_rol)
		{
			$this->fill($id_rol);
		}
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