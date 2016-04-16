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
 * Clase Modelo Tipo de trabajo TOA
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
class Tipo_trabajo_toa extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipo Identificador del tipo de trabajo
	 * @return void
	 */
	public function __construct($id_tipo = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_tipos_trabajo_toa'),
				'model_label'        => 'Tipo de trabajo TOA',
				'model_label_plural' => 'Tipos de trabajo TOA',
				'model_order_by'     => 'id_tipo',
			),
			'campos' => array(
				'id_tipo' => array(
					'label'          => 'Tipo de rabajo',
					'tipo'           => 'char',
					'largo'          => 30,
					'texto_ayuda'    => 'Tipo de trabajo. M&aacute;ximo 30 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
				'desc_tipo' => array(
					'label'          => 'Descripci&oacute;n tipo de trabajo',
					'tipo'           => 'char',
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n del tipo de trabajo. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_tipo)
		{
			$this->fill($id_tipo);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Tipo de trabajo
	 */
	public function __toString()
	{
		return (string) $this->id_tipo;
	}

}
/* End of file Tipo_trabajo_toa.php */
/* Location: ./application/models/Tipo_trabajo_toa.php */