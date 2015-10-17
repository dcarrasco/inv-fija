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
 * Clase Modelo Unidad de medida
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
class Unidad_medida extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_unidad_medida Identificador del modulo
	 * @return void
	 */
	public function __construct($id_unidad_medida = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->CI->config->item('bd_unidades'),
				'model_label'        => 'Unidad de medida',
				'model_label_plural' => 'Unidades de medida',
				'model_order_by'     => 'unidad',
			),
			'campos' => array(
				'unidad' => array(
					'label'          => 'Unidad',
					'tipo'           => 'char',
					'largo'          => 10,
					'texto_ayuda'    => 'Unidad de medida. M&aacute;ximo 10 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'desc_unidad' => array(
					'label'          => 'Descripci&oacute;n unidad de medida',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n de la unidad de medida. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_unidad_medida)
		{
			$this->fill($id_unidad_medida);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string de la unidad de medida
	 *
	 * @return string Unidad de medida
	 */
	public function __toString()
	{
		return (string) $this->desc_unidad;
	}

}

/* End of file unidad_medida.php */
/* Location: ./application/models/unidad_medida.php */