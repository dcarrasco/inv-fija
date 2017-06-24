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
 * Clase Modelo Centro
 *
 * Basada en modelo ORM
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Centro extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_centro Identificador del catalogo
	 * @return void
	 */
	public function __construct($id_centro = NULL)
	{
		$this->_model_config = [
			'modelo' => [
				'model_tabla'        => $this->config->item('bd_centros'),
				'model_label'        => 'Centro',
				'model_label_plural' => 'Centros',
				'model_order_by'     => 'centro',
			],
			'campos' => [
				'centro' => [
					'label'          => 'Centro',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 10,
					'texto_ayuda'    => 'Nombre del centro. M&aacute;ximo 10 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
			],
		];

		parent::__construct($id_centro);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Centro
	 */
	public function __toString()
	{
		return (string) $this->centro;
	}

}
/* End of file centro.php */
/* Location: ./application/models/centro.php */
