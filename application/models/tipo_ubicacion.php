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
 * Clase Modelo Tipo de Ubicacion
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
class Tipo_ubicacion extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  string $id_tipo_ubicacion Identificador del modulo
	 * @return void
	 */
	public function __construct($id_tipo_ubicacion = NULL)
	{
		parent::__construct();

		$arr_config = array(
			'modelo' => array(
				'model_tabla'        => $this->config->item('bd_tipo_ubicacion'),
				'model_label'        => 'Tipo de ubicaci&oacute;n',
				'model_label_plural' => 'Tipos de ubicaci&oacute;n',
				'model_order_by'     => 'tipo_inventario, tipo_ubicacion',
			),
			'campos' => array(
				'id' => array(
					'tipo'   => Orm_field::TIPO_ID,
				),
				'tipo_inventario' => array(
					'tipo'           =>  Orm_field::TIPO_HAS_ONE,
					'relation'       => array(
						'model' => 'tipo_inventario'
					),
					'texto_ayuda'    => 'Seleccione el tipo de inventario.',
					'es_obligatorio' => TRUE,
				),
				'tipo_ubicacion' => array(
					'label'          => 'Tipo de ubicaci&oacute;n',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 30,
					'texto_ayuda'    => 'M&aacute;ximo 30 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				),
			),
		);

		$this->config_model($arr_config);

		if ($id_tipo_ubicacion)
		{
			$this->fill($id_tipo_ubicacion);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del rol
	 *
	 * @return string Tipo de Ubicacion
	 */
	public function __toString()
	{
		return (string) $this->tipo_ubicacion;
	}


}
/* End of file tipo_ubicacion.php */
/* Location: ./application/models/tipo_ubicacion.php */
