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
 * Clase Modelo Modulo Aplicaciones
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
class Modulo extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_modulo Identificador del modulo
	 * @return void
	 */
	public function __construct($id_modulo = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_modulos'),
				'model_label'        => 'M&oacute;dulo',
				'model_label_plural' => 'M&oacute;dulos',
				'model_order_by'     => 'id_app, orden, modulo',
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
					'texto_ayuda'    => 'Aplicaci&oacute;n a la que pertenece el m&oacute;dulo.',
				),
				'modulo' => array(
					'label'          => 'M&oacute;dulo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del m&oacute;dulo. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'descripcion' => array(
					'label'          => 'Descripci&oacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 100,
					'texto_ayuda'    => 'Descripci&oacute;n del m&oacute;dulo. M&aacute;ximo 100 caracteres.',
					'mostrar_lista'  => FALSE,
				),
				'orden' => array(
					'label'          => 'Orden del m&oacute;dulo',
					'tipo'           => Orm_field::TIPO_INT,
					'texto_ayuda'    => 'Orden del m&oacute;dulo en el men&uacute;.',
					'es_obligatorio' => TRUE,
				),
				'url' => array(
					'label'          => 'Direccion del m&oacute;dulo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Dirección web (URL) del m&oacute;dulo. M&aacute;ximo 50 caracteres.',
				),
				'icono' => array(
					'label'          => '&Iacute;cono del m&oacute;dulo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre de archivo del icono del m&oacute;dulo. M&aacute;ximo 50 caracteres.',
				),
				'llave_modulo' => array(
					'label'          => 'Llave del m&oacute;dulo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 20,
					'texto_ayuda'    => 'Cadena de caracteres de seguridad del m&oacute;dulo. M&aacute;ximo 20 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
					'mostrar_lista'  => FALSE,
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_modulo)
		{
			$this->fill($id_modulo);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Modulo
	 */
	public function __toString()
	{
		return (string) $this->modulo;
	}

}
/* End of file modulo.php */
/* Location: ./application/models/modulo.php */