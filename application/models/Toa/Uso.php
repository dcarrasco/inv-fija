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

/**
 * Clase Modelo Uso Toa
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Uso extends ORM_Model {

	/**
	 * Validacion reporte uso
	 *
	 * @var array
	 */
	public $rules_uso = [
		['field'=>'mes', 'label'=>'Mes', 'rules' => 'trim|required'],
		['field'=>'mostrar', 'label'=>'Mostrar', 'rules' => 'trim|required'],
	];

	/**
	 * Validacion reporte evolucion uso
	 *
	 * @var array
	 */
	public $rules_evolucion_uso_tecnico = [
		['field'=>'tecnico', 'label'=>'Tecnico', 'rules' => 'trim|required'],
	];

	/**
	 * Validación genera reporte de uso
	 *
	 * @var array
	 */
	public $rules_genera = [
		['field'=>'mes', 'label'=>'Mes', 'rules' => 'trim|required'],
	];

	/**
	 * Combo mostrar reportes de uso
	 *
	 * @var array
	 */
	public $combo_mostrar = [
		'tipo_pet' => 'Tipos Peticion',
		'empresa'  => 'Empresas',
		'agencia'  => 'Agencias',
		'tecnico'  => 'Tecnicos',
	];

	/**
	 * Tipos de peticion
	 *
	 * @var array
	 */
	public $tipos_pet = [
		''        => 'Todas',
		'INSTALA' => 'Intalación',
		'REPARA'  => 'Reparacion',
	];

	const BUBBLE_CHART  = 'bubble';
	const SCATTER_CHART = 'scatter';

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve las asignaciones realizados en TOA
	 *
	 * @param  string $tipo_reporte Tipo de reporte a desplegar
	 * @param  string $fecha_desde  Fecha de los datos del reporte
	 * @param  string $fecha_hasta  Fecha de los datos del reporte
	 * @param  string $orden_campo  Campo para ordenar el resultado
	 * @param  string $orden_tipo   Orden del resultado (ascendente o descendente)
	 * @return string               Reporte
	 */
	public function get_data($mes = NULL, $mostrar = NULL)
	{
		if (empty($mes) OR empty($mostrar))
		{
			return;
		}

		$mes = substr($mes, 0, 4).'-'.substr($mes, 4, 2);

		$this->db
			->select('mes')->group_by('mes')
			->select('uso')->group_by('uso')
			->select_sum('cant_appt')
			->where('mes', $mes)
			->where('fecha', NULL);

		$this->db->select($mostrar)
			->group_by($mostrar)
			->where($mostrar.' is not null')
			->order_by("mes, {$mostrar}, uso");

		$filtros = ['tipo_pet', 'empresa', 'agencia', 'tecnico'];
		foreach($filtros as $filtro)
		{
			if ( ! empty(request($filtro)))
			{
				$this->db->where($filtro, request($filtro));
			}
		}

		return collect($this->db->get(config('bd_uso_toa'))->result_array());
	}

	// --------------------------------------------------------------------

	/**
	 * Genera reporte
	 *
	 * @param  string $mes     Mes a desplegar (yyyy-mm)
	 * @param  string $mostrar Tipo de reporte a mostrar
	 * @return string          Reporte
	 */
	public function get($mes = NULL, $mostrar = NULL)
	{
		if (empty($mes) OR empty($mostrar))
		{
			return;
		}

		$data = $this->get_data($mes, $mostrar);
		$id_col = request('mostrar');

		$id_collection = $data->pluck($id_col)->unique()->sort();

		$report_data = $id_collection->map_with_keys(function($id) use ($data, $id_col) {
			$data_id = $data->filter(function($data_row) use ($id_col, $id) {
				return $data_row[$id_col] === $id;
			})->map_with_keys(function($data_row) {
				return [$data_row['uso'] => $data_row['cant_appt']];
			})->all();

			return [$id => $data_id];
		})->map(function($data_row) {
			$ok      = array_get($data_row, 'OK', 0);
			$nok     = array_get($data_row, 'NOK', 0);
			$total   = $ok + $nok;
			$uso     = 100* $ok / $total;
			$ranking = ($uso >= 80) ? (($uso >= 90) ? 'OK' : 'CASI') : 'NO OK';

			return ['cant_peticiones'=>$total, 'uso'=>$uso, 'ranking'=>$ranking, 'peticiones'=>$total];
		})->all();

		$tipo_reporte = $this->tipo_chart($mostrar);

		return $this->get_formatted_data($tipo_reporte, $report_data);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve datos formateados como array de js para usar en Google Chart
	 *
	 * @param  string $tipo_reporte Tipo de reporte a desplegar
	 * @param  array  $report_data  Datos del reporte
	 * @return string               Reporte
	 */
	protected function get_formatted_data($tipo_reporte, $report_data)
	{
		$col_headers = $tipo_reporte === Uso::BUBBLE_CHART
			? "['ID', 'Cant Peticiones', '% Uso', 'Ranking', 'Peticiones']"
			: "['Cant Peticiones', '% Uso']";

		$report = $tipo_reporte === Uso::BUBBLE_CHART
			? collect($report_data)->map(function($row, $row_id) {
					return "['$row_id', {$row['cant_peticiones']}, {$row['uso']}, '{$row['ranking']}', {$row['peticiones']}]";
				})->implode(', ')
			: collect($report_data)->map(function($row, $row_id) {
					return "[{$row['cant_peticiones']}, {$row['uso']}]";
				})->implode(', ');


		return "[{$col_headers},{$report}]";
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve resultado de agencias para un mes y empresa
	 *
	 * @param  string $empresa Empresa a buscar
	 * @param  string $mes     Mes a buscar
	 * @return array
	 */
	public function combo_agencia($mes = NULL, $empresa = NULL)
	{
		$cabecera = ['' => 'Todas'];

		if (empty($mes))
		{
			return $cabecera;
		}

		$mes = substr($mes, 0, 4).'-'.substr($mes, 4, 2);

		if ( ! empty($empresa))
		{
			$this->db->where('empresa',$empresa);
		}

		$agencias = $this->db
			->distinct()
			->select('agencia')
			->where('mes', $mes)
			->order_by('agencia')
			->get(config('bd_uso_toa'))
			->result_array();

		$agencias = collect($agencias)->pluck('agencia')
			->map_with_keys(function($agencia) {
				return [$agencia => $agencia];
			})->all();

		return array_merge($cabecera, $agencias);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve resultado de agencias para un mes y empresa
	 *
	 * @param  string $empresa Empresa a buscar
	 * @param  string $mes     Mes a buscar
	 * @return array
	 */
	public function combo_tecnico($mes = NULL, $empresa = NULL, $agencia = NULL)
	{
		$cabecera = ['' => 'Todos'];

		if (empty($empresa) OR empty($mes))
		{
			return $cabecera;
		}

		$mes = substr($mes, 0, 4).'-'.substr($mes, 4, 2);

		if ( ! empty($agencia))
		{
			$this->db->where('agencia',$agencia);
		}

		$tecnicos = $this->db
			->distinct()
			->select('a.tecnico, b.tecnico as nombre', FALSE)
			->where('a.empresa',$empresa)
			->where('a.mes', $mes)
			->order_by('a.tecnico')
			->from(config('bd_uso_toa').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.tecnico=b.rut', 'left')
			->get()->result_array();

		$tecnicos = collect($tecnicos)
			->map_with_keys(function($tecnico) {
				return [$tecnico['tecnico'] => fmt_rut($tecnico['tecnico']).' '.$tecnico['nombre']];
			})->all();

		return array_merge($cabecera, $tecnicos);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve el tipo de grafico a mostrar, dependiendo del tipo de reporte
	 *
	 * @param  string $mostrar Tipo de reporte a mostrar
	 * @return string          Tipo de grafico
	 */
	public function tipo_chart($mostrar)
	{
		return Uso::BUBBLE_CHART;
		return $mostrar === 'tecnico' ? Uso::SCATTER_CHART : Uso::BUBBLE_CHART;
	}

	// --------------------------------------------------------------------

	public function evolucion_tecnico($tecnico = NULL)
	{
		if (empty($tecnico))
		{
			return;
		}

		$filtros = ['tipo_pet'];
		foreach($filtros as $filtro)
		{
			if ( ! empty(request($filtro)))
			{
				$this->db->where($filtro, request($filtro));
			}
		}

		$data = collect($this->db
			->select('mes')->group_by('mes')
			->select('uso')->group_by('uso')
			->select_sum('cant_appt')
			->where('tecnico',strtoupper($tecnico))
			->where('fecha', NULL)
			->order_by('mes')
			->get(config('bd_uso_toa'))
			->result_array()
		);

		$reporte = $data->pluck('mes')->unique()->sort()
			->map_with_keys(function($mes) use ($data) {
				return [$mes =>
					$data->filter(function($row) use ($mes) {
						return $row['mes'] === $mes;
					})->map_with_keys(function ($row) {
					return [$row['uso'] => $row['cant_appt']];
					})->all()
				];
			})->map(function($row, $mes) {
				$ok  = array_get($row, 'OK');
				$nok = array_get($row, 'NOK');

				return "['{$mes}'," . (100 * $ok / ($ok + $nok)) . "]";
			})->implode(', ');

		return "[['Mes', 'Uso'], {$reporte}]";
	}


	// --------------------------------------------------------------------

	/**
	 * Genera data de los reportes
	 *
	 * @param  mes $mes Mes a generar
	 * @return string   Mensaje resultado
	 */
	public function genera_reporte($mes = NULL)
	{
		if (empty($mes))
		{
			return "ERROR!!!";
		}

		$mes = substr($mes, 0, 4).'-'.substr($mes, 4, 2);

		// Borra mes del reporte
		$this->db->where('mes', $mes)->delete(config('bd_uso_toa'));

		// Genera reporte
		$this->db->query("insert into ".config('bd_uso_toa')."
select distinct
	substring(p.date,1,7) as mes,
	p.date as fecha,
	case substring(p.appt_number,1,3) when 'INC' then 'REPARA' else 'INSTALA' end as tipo_pet,
	p.appt_number,
	p.xa_original_agency as agencia,
	p.resource_external_id as tecnico,
	p.contractor_company as empresa,
	d.aid,
	NULL as tipo_mat,
	'OK' as uso,
	1 as cant_appt
from ".config('bd_peticiones_toa')." p
left join ".config('bd_materiales_peticiones_toa')." d on p.aid=d.aid
where p.date like '{$mes}%'
	and p.astatus='complete'"
			);

		// Actualiza uso
		$this->db->set('uso', 'NOK')
			->where('aid', NULL)
			->where('mes',$mes)
			->update(config('bd_uso_toa'));

		// Resumen del mes
		$this->db->query("insert into ".config('bd_uso_toa')."
select
	mes, NULL, tipo_pet, NULL, agencia, tecnico, empresa, NULL, NULL, uso, sum(cant_appt)
from ".config('bd_uso_toa')."
where mes='{$mes}'
  and fecha is not null
group by mes, tipo_pet, agencia, tecnico, empresa, uso"
		);

		$cantidad = $this->db->where('mes', $mes)
			->from(config('bd_uso_toa'))
			->count_all_results();
		$cantidad = fmt_cantidad($cantidad);

		return "Generado reporte de uso del mes {$mes} con {$cantidad} registros.";
	}


}

/* End of file Uso.php */
/* Location: ./application/models/Toa/Uso.php */
