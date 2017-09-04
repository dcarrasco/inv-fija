<?php
namespace inventario;
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
 * Clase Modelo Auditor
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
class Auditor extends ORM_Model {

	/**
	 * Constructor de la clase
	 *
	 * @param  integer $id_auditor Identificador del auditor
	 * @return void
	 */
	public function __construct($id_auditor = NULL)
	{
		$this->model_config = [
			'modelo' => [
				'tabla'        => config('bd_auditores'),
				'label'        => 'Auditor',
				'label_plural' => 'Auditores',
				'order_by'     => 'nombre',
			],
			'campos' => [
				'id'     => ['tipo' => Orm_field::TIPO_ID],
				'nombre' => [
					'label'          => 'Nombre del auditor',
					'tipo'           => Orm_field::TIPO_CHAR,
					'largo'          => 50,
					'texto_ayuda'    => 'M&aacute;ximo 50 caracteres.',
					'es_obligatorio' => TRUE,
					'es_unico'       => TRUE
				],
				'activo' => [
					'label'          => 'Activo',
					'tipo'           =>  Orm_field::TIPO_BOOLEAN,
					'texto_ayuda'    => 'Indica se el auditor est&aacute; activo dentro del sistema.',
					'es_obligatorio' => TRUE,
					'default'        => 1
				],
			],
		];

		parent::__construct($id_auditor);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Auditor
	 */
	public function __toString()
	{
		return (string) $this->nombre;
	}


}
/* End of file auditor.php */
/* Location: ./application/models/Inventario/auditor.php */
