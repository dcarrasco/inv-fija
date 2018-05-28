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

use Toa\Tecnico_toa;
use Toa\Consumo_toa;
use Model\Orm_model;
use Stock\Clase_movimiento;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Modelo Movimientos fija
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Panel extends ORM_Model {

	/**
	 * Arreglo con validación formulario panel
	 *
	 * @var array
	 */
	public $rules = [
		['field' => 'empresa', 'label' => 'Empresa', 'rules' => 'required'],
		['field' => 'mes',     'label' => 'Mes',     'rules' => 'required'],
	];

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
		$this->load->helper('date');
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve los datos del resumen panel
	 *
	 * @param  string $indicador Indicador a devolver
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes de datos a devolver (formato YYYYMM)
	 *
	 * @return array            Arreglo con los datos
	 */
	public function get_resumen_panel($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		if ( ! $indicador OR ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$fecha_desde = $anomes.'01';
		$fecha_hasta = get_fecha_hasta($anomes);

		$this->db
			->select('tipo')
			->select('fecha')
			->select('substring(convert(varchar(20), fecha, 103),1,2) as dia', FALSE)
			->from(config('bd_resumen_panel_toa'))
			->where('tipo', $indicador)
			->where('fecha>=', $fecha_desde)
			->where('fecha<', $fecha_hasta);

		if ($empresa === '***')
		{
			$this->db
				->select_sum('valor', 'valor')
				->group_by('tipo')
				->group_by('fecha')
				->group_by('substring(convert(varchar(20), fecha, 103),1,2)', FALSE)
				->order_by('tipo, fecha');
		}
		else
		{
			$this->db
				->select('empresa')
				->select('valor')
				->where('empresa', $empresa)
				->order_by('tipo, empresa, fecha');
		}

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos para poblar panel
	 *
	 * @param  mixed  $indicador Nombre del indicador o arreglo con nombre de indicadores
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes a recuperar (formato YYYYMM)
	 * @return string            Arreglo en formato javascript
	 */
	public function get_resumen_panel_gchart($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		$indicador = ( ! is_array($indicador)) ? ['Data' => $indicador] : $indicador;
		$num_mes   = (int) substr($anomes, 4, 2);
		$num_ano   = (int) substr($anomes, 0, 4);

		$datos = collect($indicador)->map(function($indicador, $indicador_key) use ($empresa, $anomes) {
			return $this->get_resumen_panel($indicador, $empresa, $anomes);
		})->map(function($dato) {
			return collect($dato)->map_with_keys(function($dato) {
				return [(int) $dato['dia'] => $dato['valor']];
			})->all();
		});

		$arr_peticiones = collect(array_fill(1, days_in_month($num_mes, $num_ano), 0))
			->map(function($item, $item_key) use ($datos, $indicador, $num_mes, $num_ano) {
				return collect($indicador)->map(function($indicador, $indicador_key) use ($item_key, $datos) {
					return array_get($datos->get($indicador_key), $item_key, 0);
				})->all();
			})->all();

		return $this->gchart_data($arr_peticiones);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera la suma mensual de un indicador
	 *
	 * @param  string $indicador Indicador a recuperar
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes a recuperar (formato YYYYMM)
	 * @return array            Arreglo con los datos de la suma del indicador
	 */
	public function get_resumen_usage($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		if ( ! $indicador OR ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$fecha_desde = $anomes.'01';
		$fecha_hasta = get_fecha_hasta($anomes);

		if ($empresa !== '***')
		{
			$this->db->where('empresa', $empresa);
		}

		$result = $this->db
			->select_sum('valor')
			->from(config('bd_resumen_panel_toa'))
			->where('tipo', $indicador)
			->where('fecha>=', $fecha_desde)
			->where('fecha<', $fecha_hasta)
			->get()->row();

		return $result->valor;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve datos de uso para poblar el panel
	 *
	 * @param  string $indicador1 Indicador a recuperar (numerador)
	 * @param  string $indicador2 Indicador a recuperar (denominador)
	 * @param  string $empresa    ID de la empresa
	 * @param  string $anomes     Mes a recuperar (formato YYYYMM)
	 * @return string             Arreglo en formato javascript
	 */
	public function get_resumen_panel_usage($indicador1 = NULL, $indicador2 = NULL, $empresa = NULL, $anomes = NULL)
	{
		$sum_indicador1 = $this->get_resumen_usage($indicador1, $empresa, $anomes);
		$sum_indicador2 = $this->get_resumen_usage($indicador2, $empresa, $anomes);
		$usage = ($sum_indicador2 === 0 OR is_null($sum_indicador2)) ? 0 : 100*($sum_indicador1/$sum_indicador2);
		$usage = $usage > 100 ? 100 : (int) $usage;

		return ($this->gchart_data(['usa' => $usage, 'no usa' => 100 - $usage]));
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos de uso para poblar el panel
	 *
	 * @param  string $indicador Indicador a recuperar (numerador)
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes a recuperar (formato YYYYMM)
	 * @return string            Arreglo en formato javascript
	 */
	public function get_resumen_panel_porcentaje_mes($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		if ( ! $indicador OR ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$dias_mes = days_in_month((int) substr($anomes, 4, 2), (int) substr($anomes, 0, 4));

		$fecha_desde = $anomes.'01';
		$fecha_hasta = get_fecha_hasta($anomes);

		if ($empresa !== '***')
		{
			$this->db->where('empresa', $empresa);
		}

		$result = $this->db
			->select_max('fecha')
			->from(config('bd_resumen_panel_toa'))
			->where('tipo', $indicador)
			->where('fecha>=', $fecha_desde)
			->where('fecha<', $fecha_hasta)
			->get()->row();

		$dia_actual = (int) fmt_fecha($result->fecha, 'd');

		return $dia_actual/$dias_mes;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve datos de uso para poblar el panel
	 *
	 * @param  string $indicador Indicador a recuperar (numerador)
	 * @param  string $empresa   ID de la empresa
	 * @param  string $anomes    Mes a recuperar (formato YYYYMM)
	 * @return string            Arreglo en formato javascript
	 */
	public function get_resumen_panel_proyeccion($indicador = NULL, $empresa = NULL, $anomes = NULL)
	{
		if ( ! $indicador OR ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$valor_indicador = $this->get_resumen_usage($indicador, $empresa, $anomes);
		$porc_mes = $this->get_resumen_panel_porcentaje_mes($indicador, $empresa, $anomes);

		return $this->gchart_data([
			'actual' => $valor_indicador,
			'proy'   => $porc_mes === 0 ? 0 : (int) ($valor_indicador/$porc_mes) - $valor_indicador
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Formatea un arreglo para ser usado por Google Charts
	 *
	 * @param  array $datos Arreglo a formatear
	 * @return string
	 */
	public function gchart_data($datos = [])
	{
		if ( ! $datos)
		{
			return NULL;
		}

		$agrega_comillas = function($elem) {
			return "'{$elem}'";
		};

		$nulos_a_cero = function($elem) {
			return is_null($elem) ? 0 : $elem;
		};

		$datos = collect($datos)->map(function($dato, $dato_key) {
			return ! is_array($dato) ? ['Data' => $dato] : $dato;
		});

		return "[['Dia',"
			.collect($datos->first())->keys()->map($agrega_comillas)->implode(',')
			.'],'
			.collect($datos)->map(function($dato, $dato_key) use ($nulos_a_cero) {
				return "['{$dato_key}',".collect($dato)->map($nulos_a_cero)->implode(',').']';
			})->implode(',')
			.']';
	}

	// --------------------------------------------------------------------

	/**
	 * Genera nueva data del panel para un mes
	 *
	 * @param  string $mes_data Mes a generar
	 * @return void
	 */
	public function genera_data_panel($mes_data)
	{
		$fecha_desde = $mes_data.'01';
		$fecha_hasta = get_fecha_hasta($mes_data);

		// Borra los datos del panel
		$this->db
			->where('fecha>=', $fecha_desde)
			->where('fecha<', $fecha_hasta)
			->delete(config('bd_resumen_panel_toa'));

		// Genera Q peticiones SAP
		$select = $this->db->from(config('bd_movimientos_sap_fija'))
			->select("fecha_contabilizacion as fecha", FALSE)->group_by('fecha_contabilizacion')
			->select('vale_acomp as empresa')->group_by('vale_acomp')
			->select('referencia')->group_by('referencia')
			->select('count(*) as Q', FALSE)
			->where('fecha_contabilizacion>=', $fecha_desde)
			->where('fecha_contabilizacion<', $fecha_hasta)
			->where_in('centro', ['CH32', 'CH33'])
			->where_in('codigo_movimiento', ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89'])
			->get_compiled_select();
		$this->db->query('insert into '.config('bd_resumen_panel_toa'). " select 'SAP_Q_PET' as tipo, empresa, fecha, count(*) as valor from ({$select}) as SQ1 where empresa is not null group by empresa, fecha");

		// Genera Q peticiones instala SAP
		$select = $this->db->from(config('bd_movimientos_sap_fija'))
			->select("fecha_contabilizacion as fecha", FALSE)->group_by('fecha_contabilizacion')
			->select('vale_acomp as empresa')->group_by('vale_acomp')
			->select('referencia')->group_by('referencia')
			->select('count(*) as Q', FALSE)
			->where('fecha_contabilizacion>=', $fecha_desde)
			->where('fecha_contabilizacion<', $fecha_hasta)
			->where_in('centro', ['CH32', 'CH33'])
			->where_in('codigo_movimiento', ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89'])
			->where('substring(referencia, 1, 3)<>', "'INC'", FALSE)
			->get_compiled_select();
		$this->db->query('insert into '.config('bd_resumen_panel_toa'). " select 'SAP_Q_PET_INSTALA' as tipo, empresa, fecha, count(*) as valor from ({$select}) as SQ1 where empresa is not null group by empresa, fecha");

		// Genera Q peticiones repara SAP
		$select = $this->db->from(config('bd_movimientos_sap_fija'))
			->select("fecha_contabilizacion as fecha", FALSE)->group_by('fecha_contabilizacion')
			->select('vale_acomp as empresa')->group_by('vale_acomp')
			->select('referencia')->group_by('referencia')
			->select('count(*) as Q', FALSE)
			->where('fecha_contabilizacion>=', $fecha_desde)
			->where('fecha_contabilizacion<', $fecha_hasta)
			->where_in('centro', ['CH32', 'CH33'])
			->where_in('codigo_movimiento', ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89'])
			->where('substring(referencia, 1, 3)=', "'INC'", FALSE)
			->get_compiled_select();
		$this->db->query('insert into '.config('bd_resumen_panel_toa'). " select 'SAP_Q_PET_REPARA' as tipo, empresa, fecha, count(*) as valor from ({$select}) as SQ1 where empresa is not null group by empresa, fecha");

		// Genera Q peticiones TOA
		$select = $this->db->from(config('bd_peticiones_toa').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.resource_external_id=b.rut', 'left')
			->select("'TOA_Q_PET' as tipo", FALSE)
			->select('b.id_empresa as empresa')->group_by('b.id_empresa')
			->select('convert(datetime, a.date, 102) as fecha', FALSE)->group_by('convert(datetime, a.date, 102)', FALSE)
			->select('count(*) as referencia')
			->where('a.date>=', fmt_fecha($fecha_desde))
			->where('a.date<', fmt_fecha($fecha_hasta))
			->where('a.astatus', 'complete')
			->where('a.appt_number is NOT NULL', NULL, FALSE)
			->where('b.id_empresa is NOT NULL', NULL, FALSE)
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_resumen_panel_toa').' '.$select);

		// Genera Q peticiones instala TOA
		$select = $this->db->from(config('bd_peticiones_toa').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.resource_external_id=b.rut', 'left')
			->select("'TOA_Q_PET_INSTALA' as tipo", FALSE)
			->select('b.id_empresa as empresa')->group_by('b.id_empresa')
			->select('convert(datetime, a.date, 102) as fecha', FALSE)->group_by('convert(datetime, a.date, 102)', FALSE)
			->select('count(*) as referencia')
			->where('a.date>=', fmt_fecha($fecha_desde))
			->where('a.date<', fmt_fecha($fecha_hasta))
			->where('a.astatus', 'complete')
			->where('a.appt_number is NOT NULL', NULL, FALSE)
			->where('b.id_empresa is NOT NULL', NULL, FALSE)
			->where('substring(a.appt_number, 1, 3)<>', "'INC'", FALSE)
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_resumen_panel_toa').' '.$select);

		// Genera Q peticiones repara TOA
		$select = $this->db->from(config('bd_peticiones_toa').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.resource_external_id=b.rut', 'left')
			->select("'TOA_Q_PET_REPARA' as tipo", FALSE)
			->select('b.id_empresa as empresa')->group_by('b.id_empresa')
			->select('convert(datetime, a.date, 102) as fecha', FALSE)->group_by('convert(datetime, a.date, 102)', FALSE)
			->select('count(*) as referencia')
			->where('a.date>=', fmt_fecha($fecha_desde))
			->where('a.date<', fmt_fecha($fecha_hasta))
			->where('a.astatus', 'complete')
			->where('a.appt_number is NOT NULL', NULL, FALSE)
			->where('b.id_empresa is NOT NULL', NULL, FALSE)
			->where('substring(a.appt_number, 1, 3)=', "'INC'", FALSE)
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_resumen_panel_toa').' '.$select);

		// Genera monto peticiones SAP
		$select = $this->db->from(config('bd_movimientos_sap_fija'))
			->select("'SAP_MONTO_PET' as tipo", FALSE)
			->select('vale_acomp as empresa')->group_by('vale_acomp')
			->select("fecha_contabilizacion as fecha", FALSE)->group_by('fecha_contabilizacion')
			->select('sum(-importe_ml) as valor', FALSE)
			->where('fecha_contabilizacion>=', $fecha_desde)
			->where('fecha_contabilizacion<', $fecha_hasta)
			->where_in('centro', ['CH32', 'CH33'])
			->where_in('codigo_movimiento', ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89'])
			->where('vale_acomp is NOT NULL', NULL, FALSE)
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_resumen_panel_toa').' '.$select);

		// Genera Q tecnicos SAP
		$select = $this->db->from(config('bd_movimientos_sap_fija'))
			->select('fecha_contabilizacion as fecha', FALSE)->group_by('fecha_contabilizacion')
			->select('vale_acomp as empresa')->group_by('vale_acomp')
			->select('cliente')->group_by('cliente')
			->select('count(*) as Q', FALSE)
			->where('fecha_contabilizacion>=', $fecha_desde)
			->where('fecha_contabilizacion<', $fecha_hasta)
			->where_in('centro', ['CH32', 'CH33'])
			->where_in('codigo_movimiento', ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89'])
			->where('substring(referencia, 1, 3)=', "'INC'", FALSE)
			->get_compiled_select();
		$this->db->query('insert into '.config('bd_resumen_panel_toa'). " select 'SAP_Q_TECNICOS' as tipo, empresa, fecha, count(*) as valor from ({$select}) as SQ1 where empresa is not null group by empresa, fecha");

		// Genera Q tecnicos TOA
		$select = $this->db->from(config('bd_peticiones_toa').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.resource_external_id=b.rut', 'left')
			->select('convert(datetime, a.date, 102) as fecha', FALSE)->group_by('convert(datetime, a.date, 102)', FALSE)
			->select('b.id_empresa as empresa')->group_by('b.id_empresa')
			->select('a.resource_external_id as tecnico')->group_by('a.resource_external_id')
			->select('count(*) as Q', FALSE)
			->where('a.date>=', fmt_fecha($fecha_desde))
			->where('a.date<', fmt_fecha($fecha_hasta))
			->where('a.astatus', 'complete')
			->where('a.appt_number is NOT NULL', NULL, FALSE)
			->get_compiled_select();
		$this->db->query('insert into '.config('bd_resumen_panel_toa'). " select 'TOA_Q_TECNICOS' as tipo, empresa, fecha, count(*) as valor from ({$select}) as SQ1 WHERE empresa IS NOT NULL GROUP BY empresa, fecha");

		// Genera monto stock almacenes SAP
		$select = $this->db->from(config('bd_stock_fija').' a')
			->join(config('bd_tipoalmacen_sap').' b', 'a.centro=b.centro and a.almacen=b.cod_almacen', 'left')
			->join(config('bd_empresas_toa_tiposalm').' c', 'b.id_tipo=c.id_tipo', 'left')
			->select("'SAP_MONTO_STOCK_ALM' as tipo", FALSE)
			->select('c.id_empresa as empresa')->group_by('c.id_empresa')
			->select("a.fecha_stock as fecha", FALSE)->group_by('a.fecha_stock')
			->select('sum(a.valor) as valor', FALSE)
			->where('a.fecha_stock>=', $fecha_desde)
			->where('a.fecha_stock<', $fecha_hasta)
			->where('c.id_empresa is NOT NULL', NULL, FALSE)
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_resumen_panel_toa').' '.$select);

		// Genera monto stock tecnicos
		$select = $this->db->from(config('bd_stock_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.acreedor=b.id_tecnico', 'left')
			->select("'SAP_MONTO_STOCK_TEC' as tipo", FALSE)
			->select('b.id_empresa as empresa')->group_by('b.id_empresa')
			->select("a.fecha_stock as fecha", FALSE)->group_by('a.fecha_stock')
			->select('sum(a.valor) as valor', FALSE)
			->where('a.fecha_stock>=', $fecha_desde)
			->where('a.fecha_stock<', $fecha_hasta)
			->where('a.almacen', NULL)
			->where_in('centro', ['CH32', 'CH33'])
			->where('b.id_empresa is NOT NULL', NULL, FALSE)
			->get_compiled_select();
		$this->db->query('INSERT into '.config('bd_resumen_panel_toa').' '.$select);

	}
}

// End of file Panel.php
// Location: ./models/Toa/Panel.php
