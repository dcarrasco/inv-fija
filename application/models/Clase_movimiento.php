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
 * Clase Modelo Clase de Movimiento
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
class Clase_movimiento extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipoclasif Identificador del modulo
	 * @return void
	 */
	public function __construct($cmv = NULL)
	{
		$this->_model_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_cmv_sap'),
				'model_label'        => 'Clase de movimiento',
				'model_label_plural' => 'Clases de movimiento',
				'model_order_by'     => 'cmv',
			),
			'campos' => array(
				'cmv' => array(
					'label'          => 'C&oacute;digo movimiento',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'C&oacute;digo del movimiento. M&aacute;ximo 10 caracteres',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'des_cmv' => array(
					'label'          => 'Descripci&oacute;n del movimiento',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n del movimiento. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					//'es_unico'       => TRUE
				),
			),
		);

		parent::__construct($cmv);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return (string) $this->des_cmv;
	}



}
/* End of file Clase_movimiento.php */
/* Location: ./application/models/Clase_movimiento.php */
