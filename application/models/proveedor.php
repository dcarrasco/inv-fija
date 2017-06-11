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
 * Clase Modelo Proveedor
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
class Proveedor extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_proveedor Identificador del modulo
	 * @return void
	 */
	public function __construct($id_proveedor = NULL)
	{
		$this->_model_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_proveedores'),
				'model_label'        => 'Proveedor',
				'model_label_plural' => 'Proveedores',
				'model_order_by'     => 'des_proveedor',
			),
			'campos' => array(
				'cod_proveedor' => array(
					'label'          => 'C&oacute;digo del proveedor',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'M&aacute;ximo 10 caracteres.',
					'es_id' => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				),
				'des_proveedor' => array(
					'label'          => 'Nombre del proveedor',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => FALSE,
				),
			),
		);

		parent::__construct($id_proveedor);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Proveedor
	 */
	public function __toString()
	{
		return (string) $this->des_proveedor;
	}


}
/* End of file proveedor.php */
/* Location: ./application/models/proveedor.php */
