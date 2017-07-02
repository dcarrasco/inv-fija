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

	public $header = NULL;
	public $rows   = NULL;
	public $footer = NULL;

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

	public function set_header($anomes = NULL)
	{
		$this->header = collect(get_arr_dias_mes($anomes))
			->map(function($val, $dia) use ($anomes) {
				return array_get(
					['0'=>'Do', '1'=>'Lu', '2'=>'Ma', '3'=>'Mi', '4'=>'Ju', '5'=>'Vi', '6'=>'Sa'],
					date('w', strtotime($anomes.$dia))
				)." {$dia}";
			});

		return $this;
	}

	// --------------------------------------------------------------------

	public function set_rows($matriz = NULL)
	{
		$this->rows = $matriz;

		return $this;
	}

	// --------------------------------------------------------------------

	public function set_footer($matriz = NULL)
	{
		if ( ! $matriz)
		{
			$rows = $this->rows;

			$matriz = [];
			$matriz['data'] = $this->header
				->map(function($elem, $dia) use ($rows) {
					$rows_dia = $rows->map(function($elem) use ($dia) {
						return $elem['data']->get($dia);
					});

					return [
						'total' => $rows_dia->sum(),
						//'uso'   => $rows_dia->filter()->count()/$rows_dia->count(),
					];
				})
				->all();
			$matriz['total'] = collect($matriz['data'])->sum('total');
		}

		$this->footer = $matriz;

		return $this;
	}

	// --------------------------------------------------------------------

	/**
	 * Imprime reporte
	 *
	 * @param  array $arr_campos Arreglo con los campos del reporte
	 * @param  array $arr_datos  Arreglo con los datos del reporte
	 * @return string            Reporte
	 */
	public function genera_reporte()
	{
		if ( ! $this->header OR ! $this->rows)
		{
			return;
		}

		$ci =& get_instance();
		$ci->load->library('table');
		$template = [
			'table_open' => '<table class="table table-bordered table-hover table-condensed reporte">',
			'heading_row_start' => '<tr class="active">',
			'footer_row_start' => '<tr class="active">',
		];
		$ci->table->set_template($template);

		// --- ENCABEZADO REPORTE ---
		$ci->table->set_heading($this->_reporte_linea_encabezado());

		// --- CUERPO REPORTE ---
		$num_linea = 0;
		$this->rows->each(function($row_data) use (&$num_linea, $ci){
			$num_linea += 1;
			$ci->table->add_row($this->_reporte_linea_datos($row_data, $num_linea));
		});

		// --- TOTALES ---
		$ci->table->add_row($this->_reporte_linea_totales());
		// $ci->table->add_row($this->_reporte_linea_uso_totales($matriz));

		return $ci->table->generate();
	}


	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con datos de encabezado del reporte
	 *
	 * @return array              Arreglo con los campos del encabezado
	 */
	private function _reporte_linea_encabezado()
	{
		return collect(['num' => ''])
			->merge(collect(array_get($this->rows->first(), 'header'))
				->keys()
				->map_with_keys(function($campo) {
					return [$campo => ucwords(str_replace('_', ' ', $campo))];
				})
			)
			->merge($this->header->map(function($val_dia) {
				return ['data' => $val_dia, 'class' => 'text-center'];
			}))
			->merge([['data'=>'Tot Mes', 'class'=>'text-center']])
			->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con los datos de una linea del reporte
	 *
	 * @param  array   $row_data  Arreglo con la descripcion de los campos del reporte
	 * @param  integer $num_linea Numero de linea actual
	 * @return array                 Arreglo con los campos de una linea
	 */
	private function _reporte_linea_datos($row_data = [], $num_linea = 0)
	{
		return collect(['ini' => $num_linea])
			->merge(collect(array_get($row_data, 'header'))->map(function ($valor) {
				return [
					'data' => $valor,
					'style' => 'white-space: nowrap;',
				];
			}))
			->merge(collect(array_get($row_data, 'data'))->map(function($valor) {
				return [
					'data' => $valor,
					'class' => $valor ? 'text-center info' : '',
				];
			}))
			->merge(['total' => [
				'data'  => '<strong>'.array_get($row_data, 'total').'</strong>',
				'class' => 'text-center active'
			]])
			->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con los datos de una linea de total o subtotal del reporte
	 *
	 * @return array                   Arreglo con los campos de una linea de total o subtotal
	 */
	private function _reporte_linea_totales()
	{
		return collect(['ini' => ['data'=>'', 'class'=>'active']])
			->merge(collect(array_get($this->rows->first(), 'header'))->map(function ($valor) {
				return ['data'=>'', 'class'=>'active'];
			}))
			->merge(collect($this->footer['data'])->map(function($valor) {
				return [
					'data' => "<strong>{$valor['total']}</strong>",
					'class' => 'text-center active',
				];
			}))
			->merge([[
				'data' => "<strong>{$this->footer['total']}</strong>",
				'class' => 'text-center active',
			]])
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
