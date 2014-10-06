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

if ( ! function_exists('form_array_format'))
{
	function form_array_format($arr = array())
	{
		$arr_combo = array();

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
	function fmt_cantidad($valor = 0, $decimales = 0)
	{
		return ($valor == 0) ? '' : number_format($valor, $decimales, ',', '.');
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('fmt_monto'))
{
	function fmt_monto($monto = 0, $unidad = 'UN', $signo_moneda = '$')
	{
		if ($monto == 0)
		{
			return '';
		}
		else
		{
			if (strtoupper($unidad) == 'UN')
			{
				return $signo_moneda . ' ' . number_format($monto, 0, ',', '.');
			}
			elseif (strtoupper($unidad) == 'MM')
			{
				return 'MM' . $signo_moneda . ' ' . number_format($monto/1000000, ($monto > 10000000) ? 0 : 1, ',', '.');
			}
		}
	}

}
/* helpers varios_helper.php */
/* Location: ./application/helpers/varios_helper.php */