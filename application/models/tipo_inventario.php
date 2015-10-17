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
 * Clase Modelo Tipo de Inventario
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
class Tipo_inventario extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipo_inventario Identificador del modulo
	 * @return void
	 */
	public function __construct($id_tipo_inventario = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_tipos_inventario'),
				'model_label'        => 'Tipo de inventario',
				'model_label_plural' => 'Tipos de inventario',
				'model_order_by'     => 'desc_tipo_inventario',
			),
			'campos' => array(
				'id_tipo_inventario' => array(
					'label'          => 'Tipo de inventario',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'M&aacute;ximo 10 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				),
				'desc_tipo_inventario' => array(
					'label'          => 'Descripci&oacute;n tipo de inventario',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n del tipo de inventario. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_tipo_inventario)
		{
			$this->fill($id_tipo_inventario);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Tipo Inventario
	 */
	public function __toString()
	{
		return (string) $this->desc_tipo_inventario;
	}


}

/* End of file tipo_inventario.php */
/* Location: ./application/models/tipo_inventario.php */