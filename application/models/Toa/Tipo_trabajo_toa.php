<?php
namespace Toa;
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

use \ORM_Model;
use \ORM_Field;

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
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_tipos_trabajo_toa'),
				'label'        => 'Tipo de trabajo TOA',
				'label_plural' => 'Tipos de trabajo TOA',
				'order_by'     => 'id_tipo',
			],
			'campos' => [
				'id_tipo' => [
					'label'          => 'Tipo de rabajo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 30,
					'texto_ayuda'    => 'Tipo de trabajo. M&aacute;ximo 30 caracteres.',
					'es_id'          => TRUE,
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'desc_tipo' => [
					'label'          => 'Descripci&oacute;n tipo de trabajo',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'Descripci&oacute;n del tipo de trabajo. M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
			],
		];

		parent::__construct($id_tipo);
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

	public function mostrar_info()
	{
		$descripcion_trabajo = '';

		if (strlen($this->id_tipo) === 10 AND in_array(substr($this->id_tipo, 0, 1), ['A', '-', 'M', 'B', 'T']))
		{
			$descripcion_trabajo = '<span class="label label-default">BA</span><span class="label label-info">'.substr($this->id_tipo, 0, 2).'</span>';
			$descripcion_trabajo .= ' <span class="label label-default">STB</span><span class="label label-info">'.substr($this->id_tipo, 2, 2).'</span>';
			$descripcion_trabajo .= ' <span class="label label-default">DTH</span><span class="label label-info">'.substr($this->id_tipo, 4, 2).'</span>';
			$descripcion_trabajo .= ' <span class="label label-default">VDSL</span><span class="label label-info">'.substr($this->id_tipo, 6, 2).'</span>';
			$descripcion_trabajo .= ' <span class="label label-default">IPTV</span><span class="label label-info">'.substr($this->id_tipo, 8, 2).'</span>';
		}

		return (string) empty($descripcion_trabajo) ? $this->id_tipo : $descripcion_trabajo;
	}

}
/* End of file Tipo_trabajo_toa.php */
/* Location: ./application/models/Toa/Tipo_trabajo_toa.php */
