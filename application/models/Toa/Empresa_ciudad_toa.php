<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */

namespace Toa;

use Model\Orm_model;
use Model\Orm_field;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Modelo Empresa Ciudad TOA
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
class Empresa_ciudad_toa extends ORM_Model {

	protected $db_table = 'config::bd_empresas_ciudades_toa';
	protected $label = 'Empresa ciudad TOA';
	protected $label_plural = 'Empresas ciudades TOA';
	protected $order_by = 'id_empresa, id_ciudad';

	protected $fields = [
		'id_empresa' => [
			'tipo'           => Orm_field::TIPO_HAS_ONE,
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'relation'       => ['model' => empresa_toa::class],
			'texto_ayuda'    => 'Seleccione una empresa TOA.',
		],
		'id_ciudad' => [
			'tipo'           => Orm_field::TIPO_HAS_ONE,
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'relation'       => ['model' => ciudad_toa::class],
			'texto_ayuda'    => 'Seleccione una Ciudad TOA.',
		],
		'almacenes' => [
			'tipo'     => Orm_field::TIPO_HAS_MANY,
			'relation' => [
				'model'         => \Stock\almacen_sap::class,
				'join_table'    => 'config::bd_empresas_ciudades_almacenes_toa',
				'id_one_table'  => ['id_empresa', 'id_ciudad'],
				'id_many_table' => ['centro', 'cod_almacen'],
				'conditions'    => ['centro' => ['CH32','CH33']],
			],
			'texto_ayuda' => 'Almacenes asociados a la empresa - ciudad.',
		],
	];

	/**
	 * Constructor de la clase
	 *
	 * @param  array $atributos Valores para inicializar el modelo
	 * @return void
	 */
	public function __construct($atributos = [])
	{
		parent::__construct($atributos);
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Empresa
	 */
	public function __toString()
	{
		return (string) $this->id_empresa;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera las ciudades de una empresa
	 *
	 * @param  string $id_empresa Empresa
	 * @return array
	 */
	public function ciudades_por_empresa($id_empresa = NULL)
	{
		return $this->where('id_empresa', $id_empresa)->get()
			->map(function ($ciudad_empresa) {
				return $ciudad_empresa->id_ciudad;
			})
			->all();
	}


}
// End of file Empresa_ciudad_toa.php
// Location: ./models/Toa/Empresa_ciudad_toa.php
