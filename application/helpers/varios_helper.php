<?php

if ( ! function_exists('dbg'))
{
	function dbg($item)
	{
		//echo '<pre style="font-family: courier, font-size: 8px">';
		//$dump = print_r($item, TRUE);
		//echo gettype($item) . ' : ' . $dump;
		//echo '</pre><hr>';
		var_dump($item);
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('dbg_die'))
{
	function dbg_die($item)
	{
		//echo '<pre style="font-family: courier, font-size: 8px">';
		//$dump = print_r($item, TRUE);
		//echo gettype($item) . ' : ' . $dump;
		//echo '</pre><hr>';
		var_dump($item);
		die();
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('form_array_format'))
{
	function form_array_format($arr = array(), $msg_ini = null)
	{
		$arr_combo = array();

		if ($msg_ini)
		{
			$arr_combo[''] = $msg_ini;
		}

		foreach($arr as $reg)
		{
			$arr_combo[$reg['llave']] = $reg['valor'];
		}

		return $arr_combo;
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('form_has_error'))
{
	function form_has_error($form_field = '')
	{
		return (form_error($form_field) != '') ? 'has-error' : '';
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('fmt_cantidad'))
{
	function fmt_cantidad($valor = 0, $decimales = 0, $mostrar_cero = false)
	{
		$cero = $mostrar_cero ? '0' : ' ';

		return ($valor == 0) ? $cero : number_format($valor, $decimales, ',', '.');
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('fmt_monto'))
{
	function fmt_monto($monto = 0, $unidad = 'UN', $signo_moneda = '$', $decimales = 0)
	{
		if ($monto == 0)
		{
			return ' ';
		}
		else
		{
			if (strtoupper($unidad) == 'UN')
			{
				return $signo_moneda . '&nbsp;' . number_format($monto, $decimales, ',', '.');
			}
			elseif (strtoupper($unidad) == 'MM')
			{
				return 'MM' . $signo_moneda . '&nbsp;' . number_format($monto/1000000, ($monto > 10000000) ? 0 : 1, ',', '.');
			}
		}
	}

}
/* helpers varios_helper.php */
/* Location: ./application/helpers/varios_helper.php */