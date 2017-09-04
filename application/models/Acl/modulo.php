<?php
namespace Acl;

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

use \ORM_Model;
use \ORM_Field;

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
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_modulos'),
				'label'        => 'M&oacute;dulo',
				'label_plural' => 'M&oacute;dulos',
				'order_by'     => 'id_app, orden, modulo',
			],
			'campos' => [
				'id'     => ['tipo' => Orm_field::TIPO_ID],
				'id_app' => [
					'tipo'        => Orm_field::TIPO_HAS_ONE,
					'relation'    => ['model' => app::class],
					'texto_ayuda' => 'Aplicaci&oacute;n a la que pertenece el m&oacute;dulo.',
				],
				'modulo' => [
					'label'          => 'M&oacute;dulo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del m&oacute;dulo. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'descripcion' => [
					'label'          => 'Descripci&oacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 100,
					'texto_ayuda'    => 'Descripci&oacute;n del m&oacute;dulo. M&aacute;ximo 100 caracteres.',
					'mostrar_lista'  => FALSE,
				],
				'orden' => [
					'label'          => 'Orden del m&oacute;dulo',
					'tipo'           => Orm_field::TIPO_INT,
					'texto_ayuda'    => 'Orden del m&oacute;dulo en el men&uacute;.',
					'es_obligatorio' => TRUE,
				],
				'url' => [
					'label'       => 'Direccion del m&oacute;dulo',
					'tipo'        => Orm_field::TIPO_CHAR,
					'largo'       => 50,
					'texto_ayuda' => 'Dirección web (URL) del m&oacute;dulo. M&aacute;ximo 50 caracteres.',
				],
				'icono' => [
					'label'       => '&Iacute;cono del m&oacute;dulo',
					'tipo'        => Orm_field::TIPO_CHAR,
					'largo'       => 50,
					'texto_ayuda' => 'Nombre de archivo del icono del m&oacute;dulo. M&aacute;ximo 50 caracteres.',
				],
				'llave_modulo' => [
					'label'          => 'Llave del m&oacute;dulo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 20,
					'texto_ayuda'    => 'Cadena de caracteres de seguridad del m&oacute;dulo. M&aacute;ximo 20 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
					'mostrar_lista'  => FALSE,
				],
			],
		];

		parent::__construct($id_modulo);
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
/* Location: ./application/models/Acl/modulo.php */
