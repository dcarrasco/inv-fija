<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */

namespace Stock;

use Model\Orm_model;
use Model\Orm_field;

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

	protected $db_table = 'config::bd_usuarios_sap';
	protected $label = 'Usuario SAP';
	protected $label_plural = 'Usuarios SAP';
	protected $order_by = 'usuario';

	protected $fields = [
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
	];

	/**
	 * Constructor de la clase
	 *
	 * @param  array $atributos Valores para inicializar el modelo
	 * @return void
	 */
	public function __construct($atributos = [])
	{
		parent::__construct($atributos);
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
// End of file usuario_sap.php
// Location: ./models/Stock/usuario_sap.php
