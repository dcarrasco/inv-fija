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

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
		'tipo_mat' => 'Tipos Materiales',
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
	 * @param  array $atributos Valores para inicializar el modelo
	 * @return  void
	 */
	public function __construct($atributos = [])
	{
		parent::__construct($atributos);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve las asignaciones realizados en TOA
	 *
	 * @param  string $mes_uso Mes a recuperar
	 * @param  string $mostrar Campos a recuperar
	 * @return Collection
	 */
	public function get_data($mes_uso = NULL, $mostrar = NULL)
	{
		if (empty($mes_uso) OR empty($mostrar))
		{
			return;
		}

		$mes_uso = substr($mes_uso, 0, 4).'-'.substr($mes_uso, 4, 2);

		$this->db
			->select('mes')->group_by('mes')
			->select('uso')->group_by('uso')
			->select_sum('cant_appt')
			->where('mes', $mes_uso)
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
	 * @param  string $mes_uso Mes a desplegar (yyyy-mm)
	 * @param  string $mostrar Tipo de reporte a mostrar
	 * @return string          Reporte
	 */
	public function get($mes_uso = NULL, $mostrar = NULL)
	{
		if (empty($mes_uso) OR empty($mostrar))
		{
			return;
		}

		$data = $this->get_data($mes_uso, $mostrar);
		$id_col = request('mostrar');

		$id_collection = $data->pluck($id_col)->unique()->sort();

		$report_data = $id_collection->map_with_keys(function($id_registro) use ($data, $id_col) {
			$data_id = $data->filter(function($data_row) use ($id_col, $id_registro) {
				return $data_row[$id_col] === $id_registro;
			})->map_with_keys(function($data_row) {
				return [$data_row['uso'] => $data_row['cant_appt']];
			})->all();

			return [$id_registro => $data_id];
		})->map(function($data_row) {
			$total_ok  = array_get($data_row, 'OK', 0);
			$total_nok = array_get($data_row, 'NOK', 0);
			$total   = $total_ok + $total_nok;
			$uso_toa = 100* $total_ok / $total;
			$ranking = $uso_toa >= 80 ? ($uso_toa >= 90 ? 'OK' : 'CASI') : 'NO OK';

			return [
				'cant_peticiones' => $total,
				'uso'             => $uso_toa,
				'ranking'         => $ranking,
				'peticiones'      => $total
			];
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
			? collect($report_data)
				->map(function($fila, $row_id) {
					return "['{$row_id}', {$fila['cant_peticiones']}, {$fila['uso']}, '{$fila['ranking']}', {$fila['peticiones']}]";
				})->implode(', ')
			: collect($report_data)
				->map(function($fila, $row_id) {
					return "[{$fila['cant_peticiones']}, {$fila['uso']}]";
				})->implode(', ');


		return "[{$col_headers},{$report}]";
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve resultado de agencias para un mes y empresa
	 *
	 * @param  string $mes_uso Mes a buscar
	 * @param  string $empresa Empresa a buscar
	 * @return array
	 */
	public function combo_agencia($mes_uso = NULL, $empresa = NULL)
	{
		$cabecera = ['' => 'Todas'];

		if (empty($mes_uso))
		{
			return $cabecera;
		}

		$mes_uso = substr($mes_uso, 0, 4).'-'.substr($mes_uso, 4, 2);

		if ( ! empty($empresa))
		{
			$this->db->where('empresa', $empresa);
		}

		$agencias = $this->db
			->distinct()
			->select('agencia')
			->where('mes', $mes_uso)
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
	 * @param  string $mes_uso Mes a buscar
	 * @param  string $empresa Empresa a buscar
	 * @param  string $agencia Agencia a buscar
	 * @return array
	 */
	public function combo_tecnico($mes_uso = NULL, $empresa = NULL, $agencia = NULL)
	{
		$cabecera = ['' => 'Todos'];

		if (empty($empresa) OR empty($mes_uso))
		{
			return $cabecera;
		}

		$mes_uso = substr($mes_uso, 0, 4).'-'.substr($mes_uso, 4, 2);

		if ( ! empty($agencia))
		{
			$this->db->where('agencia', $agencia);
		}

		$tecnicos = $this->db
			->distinct()
			->select('a.tecnico, b.tecnico as nombre', FALSE)
			->where('a.empresa', $empresa)
			->where('a.mes', $mes_uso)
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

	/**
	 * Evolucion del uso de TOA por tecnico
	 *
	 * @param  string $tecnico ID del tecnico
	 * @return array
	 */
	public function evolucion_tecnico($tecnico = NULL)
	{
		if (empty($tecnico))
		{
			return;
		}

		$filtros = ['tipo_pet'];

		request()->only($filtros)->each(function($filtro, $id_filtro) {
			$this->db->where($id_filtro, $filtro);
		});

		$data = collect($this->db
			->select('mes')->group_by('mes')
			->select('uso')->group_by('uso')
			->select_sum('cant_appt')
			->where('tecnico', strtoupper($tecnico))
			->where('fecha', NULL)
			->order_by('mes')
			->get(config('bd_uso_toa'))
			->result_array()
		);

		$reporte = $data->pluck('mes')->unique()->sort()
			->map_with_keys(function($mes_uso) use ($data) {
				return [$mes_uso =>
					$data->filter(function($fila) use ($mes_uso) {
						return $fila['mes'] === $mes_uso;
					})->map_with_keys(function ($fila) {
					return [$fila['uso'] => $fila['cant_appt']];
					})->all()
				];
			})->map(function($fila, $mes_uso) {
				$total_ok  = array_get($fila, 'OK');
				$total_nok = array_get($fila, 'NOK');

				return "['{$mes_uso}'," . (100 * $total_ok / ($total_ok + $total_nok)) . ']';
			})->implode(', ');

		return "[['Mes', 'Uso'], {$reporte}]";
	}


	// --------------------------------------------------------------------

	/**
	 * Genera data de los reportes
	 *
	 * @param  mes $mes_uso Mes a generar
	 * @return string
	 */
	public function genera_reporte($mes_uso = NULL)
	{
		if (empty($mes_uso))
		{
			return 'ERROR!!!';
		}

		$mes_uso = substr($mes_uso, 0, 4).'-'.substr($mes_uso, 4, 2);

		$rows = [];

		// Borra mes del reporte
		$this->db->where('mes', $mes_uso)->delete(config('bd_uso_toa'));
		$rows['borra_reporte_mes'] = $this->db->affected_rows();

		$this->db->where('mes', $mes_uso)->delete(config('bd_uso_toa_dia'));
		$rows['borra_reporte_dia'] = $this->db->affected_rows();

		$this->db->truncate(config('bd_uso_toa_vpi'));
		$this->db->truncate(config('bd_uso_toa_toa'));
		$this->db->truncate(config('bd_uso_toa_repara'));

		// Peticiones VPI
		$select_stmt = $this->db
			->select('a.fecha, a.appt_number, a.xa_original_agency as agencia, a.resource_external_id as tecnico, a.contractor_company as empresa, a.ps_id, a.pspe_cantidad, a.desc_producto_servicio, a.desc_operacion_comercial, c.desc_tip_material')
			->from(config('bd_peticiones_vpi').' a')
			->join(config('bd_ps_tip_material_toa').' b', 'a.ps_id=b.ps_id', 'left')
			->join(config('bd_tip_material_toa').' c', 'b.id_tip_material=c.id', 'left')
			->like('a.fecha', $mes_uso, 'after')
			->where('a.astatus', 'complete')
			->where('c.uso_vpi', 1)
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_uso_toa_vpi'). ' '.$select_stmt);
		$rows['peticiones_vpi'] = $this->db->affected_rows();

		// Peticiones TOA
		$select_stmt = $this->db
			->select("substring(a.date,1,7) as mes, a.date as fecha, case substring(a.appt_number,1,3) when 'INC' then 'REPARA' else 'INSTALA' end as tipo_pet, a.appt_number, a.xa_original_agency as agencia, a.resource_external_id as tecnico, a.contractor_company as empresa, a.aid, d.desc_tip_material as tipo_mat, NULL as uso,  0 as cant_appt, 0 as cant_vpi, sum(cast(quantity as int)) as cant_toa, 0 as cant_sap", FALSE)
			->from(config('bd_peticiones_toa').' a')
			->join(config('bd_materiales_peticiones_toa').' b', 'a.aid=b.aid', 'left')
			->join(config('bd_catalogo_tip_material_toa').' c', 'b.xi_sap_code=c.id_catalogo', 'left')
			->join(config('bd_tip_material_toa').' d', 'c.id_tip_material=d.id', 'left')
			->like('a.date', $mes_uso, 'after')
			->where('a.astatus', 'complete')
			->where('d.uso_vpi', 1)
			->group_by('a.date, a.appt_number, a.xa_original_agency, a.resource_external_id, a.contractor_company, a.aid, d.desc_tip_material')
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_uso_toa_toa'). ' '.$select_stmt);
		$rows['peticiones_toa'] = $this->db->affected_rows();

		// Procesa peticiones por dia
		$select_stmt = $this->db
			->select("substring(fecha,1,7) as mes, fecha, case substring(appt_number,1,3) when 'INC' then 'REPARA' else 'INSTALA' end as tipo_pet, appt_number, agencia, tecnico, empresa, NULL as aid, desc_tip_material, NULL as uso, 0 as cant_appt, sum(pspe_cantidad) as cant_vpi, 0 as cant_toa, 0 as cant_sap", FALSE)
			->from(config('bd_uso_toa_vpi'))
			->group_by('fecha, appt_number, agencia, tecnico, empresa, desc_tip_material')
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_uso_toa_dia'). ' '.$select_stmt);
		$rows['peticiones_toa_dia'] = $this->db->affected_rows();

		// Actualiza peticiones con informacion de uso toa
		$this->db->query('UPDATE r SET r.cant_toa=t.cant_toa FROM '.config('bd_uso_toa_dia').' r JOIN '.config('bd_uso_toa_toa')." t on r.appt_number=t.appt_number and r.tipo_mat=t.tipo_mat WHERE r.mes='{$mes_uso}'");
		$rows['actualiza_peticiones_toa_dia'] = $this->db->affected_rows();

		// Agrega registros en TOA que no están en VPI
		$select_stmt = $this->db
			->select('t.*')
			->from(config('bd_uso_toa_toa').' t')
			->join(config('bd_uso_toa_dia').' d', 't.appt_number=d.appt_number and t.tipo_mat=d.tipo_mat', 'left')
			->where('d.appt_number', NULL)
			->where("substring(t.appt_number,1,3)<>'INC'")
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_uso_toa_dia'). ' '.$select_stmt);
		$rows['actualiza_peticiones_toa_con_vpi'] = $this->db->affected_rows();

		// Corrige uso de 2 o mas modem en VPI (Modem Speedy)
		$this->db->set('cant_vpi', 1)
			->where('mes', $mes_uso)
			->where('cant_vpi', 2)
			->where('tipo_mat', 'Modem')
			->update(config('bd_uso_toa_dia'));
		$rows['corrige_modem_x2'] = $this->db->affected_rows();

		// Agrega peticiones de REPARA
		$select_stmt = $this->db
			->select('mes, fecha, tipo_pet, appt_number, agencia, tecnico, empresa, aid, tipo_mat, uso, cant_appt, cant_vpi, cant_toa, cant_sap')
			->from(config('bd_uso_toa_toa'))
			->where('mes', $mes_uso)
			->where('tipo_pet', 'REPARA')
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_uso_toa_dia'). ' '.$select_stmt);
		$rows['peticiones_toa_repara'] = $this->db->affected_rows();

		// Peticiones repara
		$select_stmt = $this->db
			->select("substring(a.date,1,7) as mes, a.date as fecha, 'REPARA' as tipo_pet, a.appt_number, a.xa_original_agency as agencia, a.resource_external_id as tecnico, a.contractor_company as empresa, a.aid, c.desc_tip_material as tipo_mat, NULL as uso, 0 as cant_appt, 1 as cant_vpi, 0 as cant_toa, 0 as cant_sap", FALSE)
			->from(config('bd_peticiones_toa').' a')
			->join(config('bd_claves_cierre_tip_material_toa').' b', "case when a_complete_reason_rep_minor_stb is null then '' else a_complete_reason_rep_minor_stb end + case when a_complete_reason_rep_minor_ba is null then '' else a_complete_reason_rep_minor_ba end + case when a_complete_reason_rep_minor_tv is null then '' else a_complete_reason_rep_minor_tv end = b.clave", 'left', FALSE)
			->join(config('bd_tip_material_toa').' c', 'b.id_tip_material=c.id', 'left')
			->like('a.date', $mes_uso, 'after')
			->where('a.astatus', 'complete')
			->where("substring(appt_number,1,3)='INC'", NULL, FALSE)
			->where('c.uso_vpi', 1)
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_uso_toa_repara'). ' '.$select_stmt);
		$rows['peticiones_toa_repara2'] = $this->db->affected_rows();

		// Actualiza peticiones con informacion de uso REPARA
		$this->db->query('UPDATE d SET d.cant_vpi=r.cant_vpi FROM '.config('bd_uso_toa_dia').' d JOIN '.config('bd_uso_toa_repara')." r on d.appt_number=r.appt_number and d.tipo_mat=r.tipo_mat WHERE d.mes='{$mes_uso}'");
		$rows['actualiza_peticiones_repara'] = $this->db->affected_rows();

		// Agrega registros en TOA que no están en VPI
		$select_stmt = $this->db
			->select('r.*')
			->from(config('bd_uso_toa_repara').' r')
			->join(config('bd_uso_toa_dia').' d', 'r.appt_number=d.appt_number and r.tipo_mat=d.tipo_mat', 'left')
			->where('d.appt_number', NULL)
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_uso_toa_dia'). ' '.$select_stmt);
		$rows['actualiza_peticiones_repara2'] = $this->db->affected_rows();

		// Actualiza cantidades NULL a 0
		$this->db->set('cant_toa', 0)
			->where('mes', $mes_uso)
			->where('cant_toa', NULL)
			->update(config('bd_uso_toa_dia'));

		// Actualiza cantidades NULL a 0
		$this->db->set('cant_vpi', 0)
			->where('mes', $mes_uso)
			->where('cant_vpi', NULL)
			->update(config('bd_uso_toa_dia'));


		// Actualiza peticiones OK y NOK
		$this->db->query('UPDATE '.config('bd_uso_toa_dia')." SET uso=case when cant_vpi=cant_toa then 'OK' else 'NOK' end, cant_appt=1 WHERE mes='{$mes_uso}'");
		$rows['actualiza_ok_nok'] = $this->db->affected_rows();

		// Puebla tabla mes
		$select_stmt = $this->db
			->select('mes, NULL as fecha, tipo_pet, NULL as appt_number, agencia, tecnico, empresa, aid, tipo_mat, uso, sum(cant_appt) as cant_appt, sum(cant_vpi) as cant_vpi, sum(cant_toa) as cant_toa, sum(cant_sap) as cant_sap', FALSE)
			->from(config('bd_uso_toa_dia'))
			->where('mes', $mes_uso)
			->group_by('mes, tipo_pet, agencia, tecnico, empresa, aid, tipo_mat, uso')
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_uso_toa'). ' '.$select_stmt);
		$rows['agrega_registros_mes'] = $this->db->affected_rows();

		// Borra tablas temporales
		$this->db->truncate(config('bd_uso_toa_vpi'));
		$this->db->truncate(config('bd_uso_toa_toa'));
		$this->db->truncate(config('bd_uso_toa_repara'));

		$mensaje = collect($rows)->map(function($cant, $step_key) {
			return '<li>'.str_replace('_', ' ', $step_key).' ('.fmt_cantidad($cant).' registros) </li>';
		})->implode();

		return "<ul>{$mensaje}</ul>";
	}


}

// End of file Uso.php
// Location: ./models/Toa/Uso.php
