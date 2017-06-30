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
 * Clase con functionalidades comunes de la aplicacion
 * *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Reporte_mes {

	public $matriz = NULL;

	public $dias_de_la_semana = [
		'0' => 'Do',
		'1' => 'Lu',
		'2' => 'Ma',
		'3' => 'Mi',
		'4' => 'Ju',
		'5' => 'Vi',
		'6' => 'Sa',
	];

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		$this->matriz = new Collection();
	}

	// --------------------------------------------------------------------

	/**
	 * Imprime reporte
	 *
	 * @param  array $arr_campos Arreglo con los campos del reporte
	 * @param  array $arr_datos  Arreglo con los datos del reporte
	 * @return string            Reporte
	 */
	public function genera_reporte($matriz = NULL)
	{
		if ( ! $matriz)
		{
			return;
		}

		$this->matriz = $matriz;

		$ci =& get_instance();
		$ci->load->library('table');
		$template = [
			'table_open' => '<table class="table table-bordered table-hover table-condensed reporte">',
			'heading_row_start' => '<tr class="active">',
			'footer_row_start' => '<tr class="active">',
		];
		$ci->table->set_template($template);

		// --- ENCABEZADO REPORTE ---
		$ci->table->set_heading($this->_reporte_linea_encabezado($matriz));


		// --- CUERPO REPORTE ---
		$num_linea = 0;
		$matriz->each(function($data) use (&$num_linea, $ci){
			$num_linea += 1;
			$ci->table->add_row($this->_reporte_linea_datos($data, $num_linea));
		});

		// --- TOTALES ---
		$ci->table->add_row($this->_reporte_linea_totales($matriz));
		$ci->table->add_row($this->_reporte_linea_uso_totales($matriz));

		return $ci->table->generate();
	}


	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con datos de encabezado del reporte
	 *
	 * @param  array $arr_campos  Arreglo con la descripcion de los campos del reporte
	 * @param  array $arr_totales Arreglo con los totales y subtotales de los campos del reporte
	 * @return array              Arreglo con los campos del encabezado
	 */
	private function _reporte_linea_encabezado($matriz = NULL)
	{
		$anomes = $matriz->first()->anomes;

		return collect('')
			->merge(collect($matriz->first()->header)->keys()->map(function($campo) {
				return ucwords(str_replace('_', ' ', $campo));
			}))
			->merge(collect($matriz->first()->data)->keys()->map(function($dia) use ($anomes) {
				return [
					'data' => $this->dias_de_la_semana[date('w', strtotime($anomes.$dia))].'<br>'.$dia,
					'class' => 'text-center',
				];
			}))
			->merge([['data'=>'Tot Mes', 'class'=>'text-center']])
			->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con los datos de una linea del reporte
	 *
	 * @param  array   $arr_linea  Arreglo con los valores de la linea
	 * @param  array   $arr_campos Arreglo con la descripcion de los campos del reporte
	 * @param  integer $num_linea  Numero de linea actual
	 * @return array                 Arreglo con los campos de una linea
	 */
	private function _reporte_linea_datos($data = NULL, $num_linea = 0	)
	{
		return collect(['ini' => $num_linea])
			->merge(collect($data->header)->map(function ($val) {
				return [
					'data' => $val,
					'style' => 'white-space: nowrap;',
				];
			}))
			->merge($data->data->map(function($val, $dia) use ($data) {
				return [
					'data' => $val ? array_get($val, 'formated_value') : '',
					'class' => $val ? 'text-center info' : '',
				];
			}))
			->merge([['data'=>fmt_cantidad($data->total), 'class'=>'text-center']])
			->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con los datos de una linea de total o subtotal del reporte
	 *
	 * @param  string $tipo            Tipo de linea a imprimir (total o subtotal)
	 * @param  array  $arr_campos      Arreglo con la descripcion de los campos del reporte
	 * @param  array  $arr_totales     Arreglo con los totales y subtotales de los campos del reporte
	 * @param  string $nombre_subtotal Texto con el nombre del subtotal
	 * @return array                   Arreglo con los campos de una linea de total o subtotal
	 */
	private function _reporte_linea_totales($matriz)
	{
		$totales = $matriz->first()->data->keys()
			->map_with_keys(function($dia) use ($matriz) {
				$total_dia = $matriz->map(function($item) use ($dia) {
					return $item->data->get($dia);
				})->sum('value');
				return [$dia => $total_dia];
			});

		return collect([['class'=>'active']])
			->merge(collect($matriz->first()->header)->map(function ($elem) {return ['data'=>'', 'class'=>'active'];}))
			->merge($totales->map(function($total) {
				return ['data' => $total ? '<strong>'.fmt_cantidad($total).'</strong>' : '', 'class' => 'text-center active '];
			}))
			->merge([['data'=>'<strong>'.fmt_cantidad($totales->sum()).'</strong>', 'class'=>'text-center active']])
			->all();
	}
	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con los datos de una linea de total o subtotal del reporte
	 *
	 * @param  string $tipo            Tipo de linea a imprimir (total o subtotal)
	 * @param  array  $arr_campos      Arreglo con la descripcion de los campos del reporte
	 * @param  array  $arr_totales     Arreglo con los totales y subtotales de los campos del reporte
	 * @param  string $nombre_subtotal Texto con el nombre del subtotal
	 * @return array                   Arreglo con los campos de una linea de total o subtotal
	 */
	private function _reporte_linea_uso_totales($matriz)
	{
		$cant_dia = $matriz->count();
		$uso = $matriz->first()->data->keys()
			->map_with_keys(function($dia) use ($matriz, $cant_dia) {
				$uso_dia = $matriz->map(function($item) use ($dia) {
					return $item->data->get($dia) ? 1 : 0;
				})->sum();

				return [$dia => $uso_dia/$cant_dia];
			});

		return collect([['class'=>'active']])
			->merge(collect($matriz->first()->header)->map(function ($elem) {return ['class'=>'active'];}))
			->merge($uso->map(function($uso_dia) {
				return [
					'data' => '<strong>'.fmt_cantidad(100*$uso_dia,0,TRUE).'%</strong>',
					'class' => 'text-center '.$this->clase_cumplimiento_consumos($uso_dia),
				];
			}))
			->merge([['data'=>'<strong>100%</strong>', 'class'=>'text-center '.$this->clase_cumplimiento_consumos(1)]])
			->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la clase pintar el cumplimiento diario
	 *
	 * @param  integer $porcentaje_cumplimiento % de cumplimiento
	 * @return string                           Clase
	 */
	public function clase_cumplimiento_consumos($porcentaje_cumplimiento = 0)
	{
		if ($porcentaje_cumplimiento >= 0.9)
		{
			return 'success';
		}
		elseif ($porcentaje_cumplimiento >= 0.6)
		{
			return 'warning';
		}

		return 'danger';
	}


}
/* libraries reporte_mes.php */
/* Location: ./application/libraries/reporte_mes.php */
