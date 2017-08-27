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
 * Clase con functionalidades de formateo de valores
 * *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Formatter {

	/**
	 * Formatea cantidades numéricas con separador decimal y de miles
	 *
	 * @param  integer $valor        Valor a formatear
	 * @param  integer $decimales    Cantidad de decimales a mostrar
	 * @param  boolean $mostrar_cero Indica si muestra o no valores ceros
	 * @param  boolean $format_diff  Indica si formatea valores positivos (verde) y negativos (rojo)
	 * @return string                Valor formateado
	 */
	static public function cantidad($valor = 0, $decimales = 0, $mostrar_cero = FALSE, $format_diff = FALSE)
	{
		if ( ! is_numeric($valor))
		{
			return NULL;
		}

		$cero = $mostrar_cero ? '0' : '';

		$valor_formateado = $valor === 0
			? $cero
			: number_format($valor, $decimales, ',', '.');

		return $format_diff ? static::format_diff($valor_formateado, $valor) : $valor_formateado;
	}

	// --------------------------------------------------------------------

	/**
	 * Formatea un valor como diferencias
	 *
	 * @param  string $valor_formateado Valor ya formateado
	 * @param  string $valor            Valor a formatear
	 * @return string
	 */
	static protected function format_diff($valor_formateado = '', $valor = 0)
	{
		$format_start = $valor > 0
			? '<strong><span class="text-success">+'
			: ($valor < 0
				? '<strong><span class="text-danger">'
				: '');
		$format_end = $valor === 0 ? '' : '</span></strong>';

		return $format_start.$valor_formateado.$format_end;
	}

	// --------------------------------------------------------------------

	/**
	 * Formatea cantidades numéricas como un monto
	 *
	 * @param  integer $monto        Valor a formatear
	 * @param  string  $unidad       Unidad a desplegar
	 * @param  string  $signo_moneda Simbolo monetario
	 * @param  integer $decimales    Cantidad de decimales a mostrar
	 * @param  boolean $mostrar_cero Indica si muestra o no valores ceros
	 * @param  boolean $format_diff  Indica si formatea valores positivos (verde) y negativos (rojo)
	 * @return string                Monto formateado
	 */
	static public function monto($monto = 0, $unidad = 'UN', $signo_moneda = '$', $decimales = 0, $mostrar_cero = FALSE, $format_diff = FALSE)
	{
		if ( ! is_numeric($monto))
		{
			return NULL;
		}

		if ($monto === 0 AND ! $mostrar_cero)
		{
			return '';
		}

		$signo_moneda = $signo_moneda.'&nbsp;';

		if (strtoupper($unidad) === 'UN')
		{
			$valor_formateado = $signo_moneda.static::cantidad($monto, $decimales);
		}
		elseif (strtoupper($unidad) === 'MM')
		{
			$valor_formateado = 'MM'.$signo_moneda.static::cantidad($monto/1000000, $monto > 10000000 ? 0 : 1);
		}

		return $format_diff ? static::format_diff($valor_formateado, $monto) : $valor_formateado;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve una cantidad de segundos como una hora
	 *
	 * @param  integer $segundos_totales Cantidad de segundos a formatear
	 * @return string       Segundos formateados como hora
	 */
	static public function hora($segundos_totales = 0)
	{
		$separador = ':';
		$hora      = str_pad((int) ($segundos_totales/3600), 2, '0', STR_PAD_LEFT);
		$minutos   = str_pad((int) (($segundos_totales - ((int) $hora) *3600)/60), 2, '0', STR_PAD_LEFT);
		$segundos  = str_pad((int) ($segundos_totales - ($hora*3600 + $minutos*60)), 2, '0', STR_PAD_LEFT);

		return $hora.$separador.$minutos.$separador.$segundos;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve una fecha de la BD formateada para desplegar
	 *
	 * @param  string $fecha   Fecha a formatear
	 * @param  string $formato Formato a devolver
	 * @return string          Fecha formateada segun formato
	 */
	static public function fecha($fecha = NULL, $formato = 'Y-m-d')
	{
		if ( ! $fecha)
		{
			return;
		}

		if (strlen($fecha) === 8)
		{
			$fecha = substr($fecha, 0, 4).'/'.substr($fecha, 4, 2).'/'.substr($fecha, 6, 2);
		}

		return date_create($fecha)->format($formato);
	}

	// --------------------------------------------------------------------

	/**
	 * Formatea un RUT
	 *
	 * @param  string $numero_rut RUT a formatear
	 * @return string
	 */
	static public function rut($numero_rut = NULL)
	{
		if ( ! $numero_rut)
		{
			return NULL;
		}

		if (strpos($numero_rut, '-') === FALSE)
		{
			$dv_rut     = substr($numero_rut, strlen($numero_rut) - 1, 1);
			$numero_rut = substr($numero_rut, 0, strlen($numero_rut) - 1);
		}
		else
		{
			list($numero_rut, $dv_rut) = explode('-', $numero_rut);
		}

		return fmt_cantidad($numero_rut).'-'.strtoupper($dv_rut);
	}

	// --------------------------------------------------------------------

	/**
	 * Debug de varianbles
	 *
	 * @return void
	 */
	static public function print_debug()
	{
		$colores_texto = [
			'llave_array' => 'tomato',
			'numero'      => 'blue',
			'texto'       => 'limegreen',
			'boolean'     => 'rebeccapurple',
			'array'       => 'red',
		];

		foreach (func_get_args() as $item)
		{
			ob_start();
			var_dump($item);
			$dump = ob_get_contents();
			ob_end_clean();
			$dump = preg_replace(
				[
					'/=>\n[ ]+/i',
					'/\n/',
					'/ /i',
					'/\n/i',
					'/\["([\w:\(\)\/_\-. ]*)"\]/i',
					'/\[(\d*)\]/i',
					'/(int|float)\(([\d\.]*)\)/i',
					'/bool\((\w*)\)/i',
					'/string\((\w*)\)&nbsp;\"([\w\.\\,\-~\/%:+><\&\$#@\*{}\[\]=;?\' \(\)\|]*)\"/i',
					'/array\(([\d\.]*)\)/i',
				],
				[
					' => ',
					'<br/>',
					'&nbsp;',
					'<br/>',
					'<span style="color:'.$colores_texto['llave_array'].'">["$1"]</span>',
					'<span style="color:'.$colores_texto['llave_array'].'">[$1]</span>',
					'<span style="color:'.$colores_texto['numero'].'"><i>$1</i>($2)</span>',
					'<span style="color:'.$colores_texto['boolean'].'"><i>bool</i>($1)</span>',
					'<span style="color:'.$colores_texto['texto'].'"><i>string</i>($1)&nbsp;"$2"</span>',
					'<span style="color:'.$colores_texto['array'].'"><i>array</i>($1)</span>',
				],
				$dump
			);

			echo get_instance()->parser->parse('common/dbg.php', ['dump'=>$dump], TRUE);
		}
	}


}
/* libraries Formatter.php */
/* Location: ./application/libraries/Formatter.php */
