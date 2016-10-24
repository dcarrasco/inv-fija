<?php
/**
 *
 * Author: J. A. Cuttilan
 * Last modified: 02/12/2010
 * ADDS HTML5 FORM ELEMENTS AND INPUT TYPES
 * New attributes (e.g. placeholder, required, autofocus) can be included as part of the $extra string
 *
 * @link      localhost:1520
 */

if ( ! defined('BASEPATH')) exit('No direct script access allowed');



if ( ! function_exists('form_email'))
{
	/**
	 * HTML5 Email Field
	 *
	 * Identical to the input function but adds the "email" type
	 * Falls back to a standard input field of type "text"
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_email($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'email';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_url'))
{
	/**
	 * HTML5 URL Field
	 *
	 * Identical to the input function but adds the "url" type
	 * Falls back to a standard input field of type "text"
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_url($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'url';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_search'))
{
	/**
	 * HTML5 Search Field
	 *
	 * Identical to the input function but adds the "search" type
	 * Falls back to a standard input field of type "text"
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_search($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'search';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_number'))
{
	/**
	 * HTML5 Number Field
	 *
	 * Identical to the input function but adds the "number" type
	 * Falls back to a standard input field of type "text"
	 * Define min, max & step in $extra
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_number($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'number';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_range'))
{
	/**
	 * HTML5 Range Field
	 *
	 * Identical to the input function but adds the "range" type
	 * Falls back to a standard input field of type "text"
	 * Define min, max & step in $extra
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_range($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'range';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_date_browser'))
{
	/**
	 * HTML5 Date Field
	 *
	 * Identical to the input function but adds the "date" type
	 * Only supported in Opera 9+ at time of writing
	 * Falls back to a standard input field of type "text"
	 *
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_date_browser($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'date';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_month_browser'))
{
	/**
	 * HTML5 Month Field
	 *
	 * Identical to the input function but adds the "month" type
	 * Only supported in Opera 9+ at time of writing
	 * Falls back to a standard input field of type "text"
	 *
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_month_browser($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'month';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_week'))
{
	/**
	 * HTML5 Week Field
	 *
	 * Identical to the input function but adds the "week" type
	 * Only supported in Opera 9+ at time of writing
	 * Falls back to a standard input field of type "text"
	 *
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_week($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'week';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_time'))
{
	/**
	 * HTML5 Time Field
	 *
	 * Identical to the input function but adds the "time" type
	 * Only supported in Opera 9+ at time of writing
	 * Falls back to a standard input field of type "text"
	 *
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_time($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'time';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_datetime'))
{
	/**
	 * HTML5 DateTime Field
	 *
	 * Identical to the input function but adds the "datetime" type
	 * Only supported in Opera 9+ at time of writing
	 * Falls back to a standard input field of type "text"
	 *
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_datetime($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'datetime';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_datetime_local'))
{
	/**
	 * HTML5 DateTime-Local Field
	 *
	 * Identical to the input function but adds the "datetime-local" type
	 * Only supported in Opera 9+ at time of writing
	 * Falls back to a standard input field of type "text"
	 *
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_datetime_local($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'datetime-local';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_color'))
{
	/**
	 * HTML5 Color Field
	 *
	 * Identical to the input function but adds the "datetime" type
	 * Unsupported in browsers at time of writing
	 * Falls back to a standard input field of type "text"
	 *
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_collor($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'color';
		return form_input($data, $value, $extra);
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_picture'))
{
	/**
	 * HTML5 Picture Field
	 *
	 * Identical to the input function but adds the "search" type
	 * Falls back to a standard input field of type "text"
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_picture($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$data['type'] = 'file';
		$data['accept'] = 'image/*;';
		$data['capture'] = 'camera';
		return form_input($data, $value, $extra);
	}
}


// ------------------------------------------------------------------------

if ( ! function_exists('form_date'))
{
	/**
	 * Bootstrap Date Field
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_date($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		return '<div class="input-group date" data-provide="datepicker" data-date-today-highlight="true" data-date-language="es" data-date-autoclose="true">'.form_input($data, $value, $extra).'<div class="input-group-addon"><i class="fa fa-calendar"></i></div></div>';

	}
}


// ------------------------------------------------------------------------

if ( ! function_exists('form_month'))
{
	/**
	 * Bootstrap Month Field
	 *
	 * @access	public
	 * @param	mixed  $data  Nombre del elemento
	 * @param	string $value Valor del elemento
	 * @param	string $extra Parametros adicionales
	 * @return	string
	 */
	function form_month($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		$extra .= ' placeholder="Seleccione mes"';

		return '<div class="input-group date" data-provide="datepicker" data-date-min-view-mode="1" data-date-language="es" data-date-autoclose="true" data-date-format="yyyymm">'.form_input($data, $value, $extra).'<div class="input-group-addon"><i class="fa fa-calendar"></i></div></div>';

	}
}


// ------------------------------------------------------------------------

if ( ! function_exists('form_date_range'))
{
	/**
	 * Bootstrap Date Field
	 *
	 * @access	public
	 * @param	mixed  $data_desde  Nombre del elemento desde
	 * @param	string $value_desde Valor del elemento desde
	 * @param	mixed  $data_hasta  Nombre del elemento desde
	 * @param	string $value_hasta Valor del elemento desde
	 * @param	string $extra       Parametros adicionales
	 * @return	string
	 */
	function form_date_range($data_desde = '', $value_desde = '', $data_hasta = '', $value_hasta = '', $extra = '')
	{
		$js_script = '<script>$(document).ready(function() {$(\'input[name="'.$data_desde.'"]\').on(\'changeDate\', function(e) {var fecha_desde = $(\'input[name="'.$data_desde.'"]\').val();$(\'input[name="'.$data_hasta.'"]\').val(fecha_desde);$(\'input[name="'.$data_hasta.'"]\').datepicker(\'setStartDate\',fecha_desde);})});</script>';

		$str_desde = 'Desde';
		$str_hasta = 'Hasta';

		if ( ! is_array($data_desde))
		{
			$data_desde = array('name' => $data_desde);
		}

		if ( ! is_array($data_hasta))
		{
			$data_hasta = array('name' => $data_hasta);
		}

		$extra .= ' data-provide="datepicker" data-date-today-highlight="true" data-date-language="es" data-date-autoclose="true"';

		$extra_desde = $extra.' placeholder="Fecha desde"';
		$extra_hasta = $extra.' placeholder="Fecha hasta"';

		$form_item = '<div class="input-group input-daterange">';
		$form_item .= '<span class="input-group-addon">'.$str_desde.'</span>';
		$form_item .= form_input($data_desde, $value_desde, $extra_desde);
		$form_item .= '<span class="input-group-addon">'.$str_hasta.'</span>';
		$form_item .= form_input($data_hasta, $value_hasta, $extra_hasta);
		$form_item .= '</div>'.$js_script;

		return $form_item;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_print_options'))
{
	/**
	 * Devuelve conjunto de <option> para ser usado en un <select>
	 *
	 * @access	public
	 * @param	array $options  Arreglo de opciones
	 * @param	array $selected Arreglo de elementos seleccioandos
	 * @return	string
	 */
	function form_print_options($options = array(), $selected = array())
	{
		if ( ! $options)
		{
			return;
		}

		is_array($options) OR $options = array($options);
		is_array($selected) OR $selected = array($selected);

		$form = '';

		foreach ($options as $options_key => $options_val)
		{
			$options_key = (string) $options_key;

			if (is_array($options_val))
			{
				if (empty($options_val))
				{
					continue;
				}

				$form .= '<optgroup label="'.$options_key."\">\n";

				foreach ($options_val as $optgroup_key => $optgroup_val)
				{
					$form .= '<option value="'.html_escape($optgroup_key).'"'
						.(in_array($optgroup_key, $selected) ? ' selected="selected"' : '').'>'
						.(string) $optgroup_val."</option>\n";
				}

				$form .= "</optgroup>\n";
			}
			else
			{
				$form .= '<option value="'.html_escape($options_key).'"'
					.(in_array($options_key, $selected) ? ' selected="selected"' : '').'>'
					.(string) $options_val."</option>\n";
			}
		}

		return $form;
	}
}

// ------------------------------------------------------------------------

if ( ! function_exists('form_onchange'))
{
	/**
	 * Devuelve js para onchange
	 *
	 * @access	public
	 * @param	string $elem_orig  Elemento de origen
	 * @param	string $elem_dest  Elemento de destino
	 * @param	string $controller Controlador
	 * @return	string
	 */
	function form_onchange($elem_orig = '', $elem_dest = '', $controller = '')
	{
		$form_elem_prefix = '#id_';

		$elem_orig = $form_elem_prefix.$elem_orig;
		$elem_dest = $form_elem_prefix.$elem_dest;

		$script_js = '';
		if ($elem_orig)
		{
			// limpia elem destino
			$script_js .= "\$('{$elem_dest}').html('');";

			// recupera opciones
			$script_js .= "\$.get('".site_url($controller)."'+'/'+\$('{$elem_orig}').val(), function(data){\$('{$elem_dest}').html(data);});";
		}

		return $script_js;
	}

}
/* helpers MY_form_helper.php */
/* Location: ./application/helpers/MY_form_helper.php */