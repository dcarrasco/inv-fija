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

use Toa\Tecnico_toa;
use Toa\Consumo_toa;
use Stock\Clase_movimiento;

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
class Toa_model extends CI_Model {

	/**
	 * Movimientos validos de consumos TOA
	 *
	 * @var array
	 */
	public $movimientos_consumo = ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89'];
	// public $movimientos_consumo = ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89', 'Z33'];
	// Z33 devolucion de materiales: viene con un idtecnico distinto
	// public $movimientos_consumo = ['Z35', 'Z45', 'Z39', 'Z41', 'Z87', 'Z89', 'Z81', 'Z82', 'Z83', 'Z84'];
	// Z81 CAPEX  Z82 ANULA CAPEX  / Z83 OPEX y Z84 ANULA OPEX   Regularizaciones Manuales

	/**
	 * Centros validos de consumos TOA
	 *
	 * @var array
	 */
	public $centros_consumo = ['CH32', 'CH33'];

	/**
	 * Combo de unidades reporte control tecnicos
	 *
	 * @var array
	 */
	public $combo_unidades_consumo = [
		'peticiones' => 'Cantidad de peticiones',
		'unidades'   => 'Suma de unidades',
		'monto'      => 'Suma de montos',
	];

	/**
	 * Combo de unidades reporte control materiales consumidos
	 *
	 * @var array
	 */
	public $combo_unidades_materiales_consumidos = [
		'unidades' => 'Suma de unidades',
		'monto'    => 'Suma de montos',
	];

	/**
	 * Arreglo con validación formulario panel
	 *
	 * @var array
	 */
	public $panel_validation = [
		['field' => 'empresa', 'label' => 'Empresa', 'rules' => 'required'],
		['field' => 'mes',     'label' => 'Mes',     'rules' => 'required'],
	];

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
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
		$arr_indicador = ( ! is_array($indicador)) ? ['Data' => $indicador] : $indicador;

		$arr_datos = [];
		foreach ($arr_indicador as $llave_indicador => $valor_indicador)
		{
			$arr_datos[$llave_indicador] = $this->get_resumen_panel($valor_indicador, $empresa, $anomes);
		}

		$num_mes = (int) substr($anomes, 4, 2);
		$num_ano = (int) substr($anomes, 0, 4);
		$arr_indicador_vacio = array_fill_keys(array_keys($arr_indicador), 0);
		$arr_peticiones = array_fill(1, days_in_month($num_mes, $num_ano), $arr_indicador_vacio);

		foreach ($arr_indicador as $llave_indicador => $valor_indicador)
		{
			foreach($arr_datos[$llave_indicador] as $registro)
			{
				$arr_peticiones[(int) $registro['dia']][$llave_indicador] = $registro['valor'];
			}
		}

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
		$usage = ($sum_indicador2 === 0) ? 0 : 100*($sum_indicador1/$sum_indicador2);
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
			'proy'   => (int) ($valor_indicador/$porc_mes) - $valor_indicador
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Formatea un arreglo para ser usado por Google Charts
	 *
	 * @param  array $arr_orig Arreglo a formatear
	 * @return string
	 */
	public function gchart_data($arr_orig = [])
	{

		if ( ! $arr_orig)
		{
			return NULL;
		}

		$func_agrega_comillas = function($elem) {return "'".$elem."'";};
		$func_nulos_a_cero    = function($elem) {return is_null($elem) ? 0 : $elem;};
		$num_registro = 0;

		foreach ($arr_orig as $num_dia => $valor)
		{
			if ( ! is_array($valor))
			{
				$valor = ['Data' => $valor];
			}

			// titulo
			if ($num_registro === 0)
			{
				$arrjs = "[['Dia', ".collect(array_keys($valor))->map($func_agrega_comillas)->implode(',').']';
			}

			// agregamos los datos del día
			$arrjs .= ", ['{$num_dia}',".collect($valor)->map($func_nulos_a_cero)->implode(',').']';
			$num_registro += 1;
		}

		$arrjs .= ']';

		return $arrjs;
	}



}

/* End of file Toa_model.php */
/* Location: ./application/models/Toa_model.php */
