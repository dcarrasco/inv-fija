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
 * Clase Modelo Tipo de Clasificacion Almacen
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
class Tipo_clasifalm extends ORM_Model {

	protected $db_table = 'config::bd_tipo_clasifalm_sap';
	protected $label = 'Tipo Clasificaci&oacute;n Almac&eacute;n';
	protected $label_plural = 'Tipos Clasificaci&oacute;n Almac&eacute;n';
	protected $order_by = 'tipo';

	protected $fields = [
		'id_tipoclasif' => [
			'label'            => 'id',
			'tipo'             => Orm_field::TIPO_INT,
			'largo'            => 10,
			'texto_ayuda'      => '',
			'es_id'            => TRUE,
			'es_obligatorio'   => TRUE,
			'es_unico'         => TRUE,
			'es_autoincrement' => TRUE,
		],
		'tipo' => [
			'label'          => 'Tipo Clasificaci&oacute;n de Almac&eacute;n',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Tipo Clasificaci&oacute;n del almac&eacute;n. M&aacute;ximo 50 caracteres.',
			'es_obligatorio' => TRUE,
		],
		'color' => [
			'label'          => 'Color del tipo',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Color del tipo para graficar. M&aacute;ximo 20 caracteres.',
			'es_obligatorio' => FALSE,
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
	 * Devuelve representación string del rol
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return (string) $this->tipo;
	}

}
// End of file tipo_clasifalm.php
// Location: ./models/Stock/tipo_clasifalm.php
