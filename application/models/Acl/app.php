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
 * Clase Modelo App (Aplicacion)
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
class App extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  integer $id_app Identificador de la app
	 * @return void
	 */
	public function __construct($id_app = NULL)
	{
		$this->_model_config = [
			'modelo' => [
				'model_tabla'        => config('bd_app'),
				'model_label'        => 'Aplicaci&oacute;n',
				'model_label_plural' => 'Aplicaciones',
				'model_order_by'     => 'app',
			],
			'campos' => [
				'id'  => ['tipo' => Orm_field::TIPO_ID],
				'app' => [
					'label'          => 'Aplicaci&oacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre de la aplicaci&oacute;n. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'descripcion' => [
					'label'          => 'Descripci&oacute;n de la Aplicaci&oacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Breve descripcion de la aplicaci&oacute;n. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				],
				'orden' => [
					'label'          => 'Orden de la Aplicaci&oacute;n',
					'tipo'           => Orm_field::TIPO_INT,
					'texto_ayuda'    => 'Orden de la aplicaci&oacute;n en el menu.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'url' => [
					'label'          => 'Direcci&oacute;n de la Aplicaci&oacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 100,
					'texto_ayuda'    => 'Direcci&oacute;n web (URL) de la aplicaci&oacute;n. M&aacute;ximo 100 caracteres.',
				],
				'icono' => [
					'label'          => '&Iacute;cono de la aplicaci&oacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del archivo del &iacute;cono de la aplicaci&oacute;n. M&aacute;ximo 50 caracteres.',
				],
			],
		];

		parent::__construct($id_app);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string App
	 */
	public function __toString()
	{
		return (string) $this->app;
	}

}
/* End of file app.php */
/* Location: ./application/models/app.php */
