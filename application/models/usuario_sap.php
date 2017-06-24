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
 * Clase Modelo Usuario SAP
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Usuario_sap extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_usuario_sap Identificador del modulo
	 * @return void
	 */
	public function __construct($id_usuario_sap = NULL)
	{
		$this->_model_config = [
			'modelo' => [
				'model_tabla'        => $this->config->item('bd_usuarios_sap'),
				'model_label'        => 'Usuario SAP',
				'model_label_plural' => 'Usuarios SAP',
				'model_order_by'     => 'usuario',
			],
			'campos' => [
				'usuario' => [
					'label'          => 'Codigo Usuario',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'C&oacute;digo del usuario SAP. M&aacute;ximo 10 caracteres',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'nom_usuario' => [
					'label'          => 'Nombre de usuario',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre del usuario. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => FALSE,
				],
			],
		];

		parent::__construct($id_usuario_sap);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del Usuario SAP
	 *
	 * @return string Usuario SAP
	 */
	public function __toString()
	{
		return (string) $this->nom_usuario;
	}

}
/* End of file usuario_sap.php */
/* Location: ./application/models/usuario_sap.php */
