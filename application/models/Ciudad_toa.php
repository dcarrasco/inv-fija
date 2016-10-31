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
 * Clase Modelo Ciudad TOA
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
class Ciudad_toa extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_empresa Identificador de la ciudad
	 * @return void
	 */
	public function __construct($id = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_ciudades_toa'),
				'model_label'        => 'Ciudad TOA',
				'model_label_plural' => 'Ciudades TOA',
				'model_order_by'     => 'orden',
			),
			'campos' => array(
				'id_ciudad' => array(
					'label'          => 'ID de la ciudad',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 5,
					'texto_ayuda'    => 'ID de la ciudad. M&aacute;ximo 50 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE,
				),
				'ciudad' => array(
					'label'          => 'Nombre de la ciudad',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Nombre de la ciudad. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
				),
				'orden' => array(
					'label'          => 'Orden de la ciudad',
					'tipo'           => Orm_field::TIPO_INT,
					'texto_ayuda'    => 'Orden de despliegue de la ciudad.',
					'es_obligatorio' => TRUE,
				),
			),
		);

		$this->config_model($arr_config);

		if ($id)
		{
			$this->fill($id);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Ciudad
	 */
	public function __toString()
	{
		return (string) $this->ciudad;
	}

}
/* End of file Ciudad_toa.php */
/* Location: ./application/models/Ciudad_toa.php */