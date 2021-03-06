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
 * Clase Modelo Técnico TOA
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
class Tecnico_toa extends ORM_Model {

	protected $db_table = 'config::bd_tecnicos_toa';
	protected $label = 'T&eacute;cnico TOA';
	protected $label_plural = 'T&eacute;cnicos TOA';
	protected $order_by = 'tecnico';

	protected $fields = [
		'id_tecnico' => [
			'label'          => 'ID T&eacute;cnico',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 20,
			'texto_ayuda'    => 'ID del t&eacute;cnico. M&aacute;ximo 20 caracteres.',
			'es_id'          => TRUE,
			'es_obligatorio' => TRUE,
			'es_unico'       => TRUE
		],
		'tecnico' => [
			'label'          => 'Nombre t&eacute;cnico',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 50,
			'texto_ayuda'    => 'Nombre del t&eacute;cnico. M&aacute;ximo 50 caracteres.',
			'es_obligatorio' => TRUE,
			// 'es_unico'       => TRUE
		],
		'rut' => [
			'label'          => 'RUT del t&eacute;cnico',
			'tipo'           => Orm_field::TIPO_CHAR,
			'largo'          => 20,
			'texto_ayuda'    => 'RUT del t&eacute;cnico. Sin puntos, con guion y d&iacute;gito verificador (en min&uacute;scula). M&aacute;ximo 50 caracteres.',
			'es_obligatorio' => TRUE,
			// 'es_unico'       => TRUE
		],
		'id_empresa' => [
			'tipo'        => Orm_field::TIPO_HAS_ONE,
			'relation'    => ['model' => Empresa_toa::class],
			'texto_ayuda' => 'Empresa a la que pertenece el t&eacute;cnico.',
			'onchange'    => 'form_onchange::id_empresa:id_ciudad:toa_config/get_select_ciudad',
		],
		'id_ciudad' => [
			'tipo'        => Orm_field::TIPO_HAS_ONE,
			'relation'    => ['model' => Ciudad_toa::class],
			'texto_ayuda' => 'Ciudad a la que pertenece el t&eacute;cnico.',
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

		$this->get_ciudades_tecnico();
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve representación string del modelo
	 *
	 * @return string Técnico
	 */
	public function __toString()
	{
		return (string) $this->tecnico;
	}


	public function get_rut()
	{
		list($numero_rut, $dv_rut) = (strpos($this->get_value('rut'), '-') !== FALSE)
			? explode('-', $this->get_value('rut'))
			: [$this->get_value('rut'), ''];

		return fmt_cantidad($numero_rut).'-'.$dv_rut;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera las ciudades asociadas a un técnico
	 *
	 * @return $this
	 */
	public function get_ciudades_tecnico()
	{
		if ($this->id_empresa && $this->uri->segment(2) === 'editar')
		{
			$arr_ciudades = Empresa_ciudad_toa::create()->ciudades_por_empresa($this->id_empresa->get_id());

			$arr_config_ciudad = [
				'id_ciudad' => [
					'tipo'     => Orm_field::TIPO_HAS_ONE,
					'relation' => [
						'model'      => Ciudad_toa::class,
						'conditions' => ['id_ciudad' => $arr_ciudades],
					],
					'texto_ayuda' => 'Ciudad a la que pertenece el t&eacute;cnico.',
				],
			];

			// $this->config_campos($arr_config_ciudad);
			// $this->get_relation_fields();
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera nuevos técnicos a partir de los cierres
	 *
	 * @return array Técnicos nuevos
	 */
	public function nuevos_tecnicos()
	{
		$dias_hasta = -10;

		$data = collect($this->db
			->distinct()
			->select('a.cliente as id_tecnico')
			->select('d.resource_name as tecnico')
			->select('a.vale_acomp as id_empresa')
			->select('d.resource_external_id as rut')
			->from(config('bd_movimientos_sap_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_peticiones_toa').' d', "a.referencia=d.appt_number and d.astatus='complete'", 'left', FALSE)
			->where("a.fecha_contabilizacion >= dateadd(day, {$dias_hasta}, convert(date, getdate()))")
			->where_in('codigo_movimiento', Consumo_toa::create()->movimientos_consumo)
			->where_in('centro', Consumo_toa::create()->centros_consumo)
			->where('b.id_tecnico is NULL')
			->where('d.resource_name is not NULL')
			->get()->result_array()
		);

		return $data->map(function($result_row) {
			return static::create($result_row);
		});
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera técnicos sin definición de ciudad a partir de los cierres
	 *
	 * @return array Técnicos sin ciudad
	 */
	public function tecnicos_sin_ciudad()
	{
		$dias_hasta = -10;

		$data = $this->db
			->distinct()
			->select('c.empresa')
			->select('b.*')
			->select('d.xa_original_agency')
			->select('d.contractor_company')
			->from(config('bd_movimientos_sap_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'a.vale_acomp=c.id_empresa', 'left', FALSE)
			->join(config('bd_peticiones_toa').' d', "a.referencia=d.appt_number and d.astatus='complete'", 'left', FALSE)
			->where("a.fecha_contabilizacion >= dateadd(day, {$dias_hasta}, convert(date, getdate()))")
			->where_in('codigo_movimiento', Consumo_toa::create()->movimientos_consumo)
			->where_in('centro', Consumo_toa::create()->centros_consumo)
			->where('b.id_ciudad is NULL')
			->where('d.xa_original_agency is not NULL')
			->order_by('c.empresa, b.id_tecnico')
			->get()->result_array();

		return collect($data)->map(function($result_row) {
			return [
				'tecnico'            => new static($result_row['id_tecnico']),
				'original_agency'    => $result_row['xa_original_agency'],
				'contractor_company' => $result_row['contractor_company'],
				'empresa'            => $result_row['empresa'],
			];
		});
	}

}

// End of file Tecnico_toa.php
// Location: ./models/Toa/Tecnico_toa.php
