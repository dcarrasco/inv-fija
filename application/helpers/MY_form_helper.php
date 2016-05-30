<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
*
* Author: J. A. Cuttilan
* Last modified: 02/12/2010
* ADDS HTML5 FORM ELEMENTS AND INPUT TYPES
* New attributes (e.g. placeholder, required, autofocus) can be included as part of the $extra string
*
*/




/**
 * HTML5 Email Field
 *
 * Identical to the input function but adds the "email" type
 * Falls back to a standard input field of type "text"
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_email'))
{
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

/**
 * HTML5 URL Field
 *
 * Identical to the input function but adds the "url" type
 * Falls back to a standard input field of type "text"
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_url'))
{
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

/**
 * HTML5 Search Field
 *
 * Identical to the input function but adds the "search" type
 * Falls back to a standard input field of type "text"
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_search'))
{
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

/**
 * HTML5 Number Field
 *
 * Identical to the input function but adds the "number" type
 * Falls back to a standard input field of type "text"
 * Define min, max & step in $extra
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_number'))
{
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

/**
 * HTML5 Range Field
 *
 * Identical to the input function but adds the "range" type
 * Falls back to a standard input field of type "text"
 * Define min, max & step in $extra
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_range'))
{
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

/**
 * HTML5 Date Field
 *
 * Identical to the input function but adds the "date" type
 * Only supported in Opera 9+ at time of writing
 * Falls back to a standard input field of type "text"
 *
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_date_browser'))
{
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

/**
 * HTML5 Month Field
 *
 * Identical to the input function but adds the "month" type
 * Only supported in Opera 9+ at time of writing
 * Falls back to a standard input field of type "text"
 *
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_month_browser'))
{
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

/**
 * HTML5 Week Field
 *
 * Identical to the input function but adds the "week" type
 * Only supported in Opera 9+ at time of writing
 * Falls back to a standard input field of type "text"
 *
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_week'))
{
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

/**
 * HTML5 Time Field
 *
 * Identical to the input function but adds the "time" type
 * Only supported in Opera 9+ at time of writing
 * Falls back to a standard input field of type "text"
 *
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_time'))
{
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

/**
 * HTML5 DateTime Field
 *
 * Identical to the input function but adds the "datetime" type
 * Only supported in Opera 9+ at time of writing
 * Falls back to a standard input field of type "text"
 *
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_datetime'))
{
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

/**
 * HTML5 DateTime-Local Field
 *
 * Identical to the input function but adds the "datetime-local" type
 * Only supported in Opera 9+ at time of writing
 * Falls back to a standard input field of type "text"
 *
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_datetime_local'))
{
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

/**
 * HTML5 Color Field
 *
 * Identical to the input function but adds the "datetime" type
 * Unsupported in browsers at time of writing
 * Falls back to a standard input field of type "text"
 *
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_color'))
{
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

/**
 * HTML5 Picture Field
 *
 * Identical to the input function but adds the "search" type
 * Falls back to a standard input field of type "text"
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_picture'))
{
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

/**
 * Bootstrap Date Field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_date'))
{
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

/**
 * Bootstrap Month Field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_month'))
{
	function form_month($data = '', $value = '', $extra = '')
	{
		if ( ! is_array($data))
		{
			$data = array('name' => $data);
		}

		return '<div class="input-group date" data-provide="datepicker" data-date-min-view-mode="1" data-date-language="es" data-date-autoclose="true" data-date-format="yyyymm">'.form_input($data, $value, $extra).'<div class="input-group-addon"><i class="fa fa-calendar"></i></div></div>';

	}
}


// ------------------------------------------------------------------------

/**
 * Bootstrap Date Field
 *
 * @access	public
 * @param	mixed
 * @param	string
 * @param	string
 * @return	string
 */
if ( ! function_exists('form_date_range'))
{
	function form_date_range($data_desde = '', $value_desde = '', $data_hasta = '', $value_hasta = '', $extra = '')
	{
		$js = '<script>$(document).ready(function() {$(\'input[name="'.$data_desde.'"]\').on(\'changeDate\', function(e) {var fecha_desde = $(\'input[name="'.$data_desde.'"]\').val();$(\'input[name="'.$data_hasta.'"]\').val(fecha_desde).datepicker({startDate:fecha_desde});})	});</script>';
		$str_desde = "Desde";
		$str_hasta = "Hasta";

		if ( ! is_array($data_desde))
		{
			$data_desde = array('name' => $data_desde);
		}

		if ( ! is_array($data_hasta))
		{
			$data_hasta = array('name' => $data_hasta);
		}

		$extra .= ' data-provide="datepicker" data-date-today-highlight="true" data-date-language="es" data-date-autoclose="true"';

		$form_item = '<div class="input-group input-daterange">';
		$form_item .= '<span class="input-group-addon">'.$str_desde.'</span>';
		$form_item .= form_input($data_desde, $value_desde, $extra);
		$form_item .= '<span class="input-group-addon">'.$str_hasta.'</span>';
		$form_item .= form_input($data_hasta, $value_hasta, $extra);
		$form_item .= '</div>'.$js;

		return $form_item;
	}
}

// ------------------------------------------------------------------------

/**
 * Devuelve conjunto de <option> para ser usado en un <select>
 *
 * @access	public
 * @param	mixed
 * @param	mixed
 * @return	string
 */
if ( ! function_exists('form_print_options'))
{
	function form_print_options($options = array(), $selected = array())
	{
		if ( ! $options)
		{
			return;
		}


		is_array($options) OR $options = array($options);
		is_array($selected) OR $selected = array($selected);

		$form = '';

		foreach ($options as $key => $val)
		{
			$key = (string) $key;

			if (is_array($val))
			{
				if (empty($val))
				{
					continue;
				}

				$form .= '<optgroup label="'.$key."\">\n";

				foreach ($val as $optgroup_key => $optgroup_val)
				{
					$sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
					$form .= '<option value="'.html_escape($optgroup_key).'"'.$sel.'>'
						.(string) $optgroup_val."</option>\n";
				}

				$form .= "</optgroup>\n";
			}
			else
			{
				$form .= '<option value="'.html_escape($key).'"'
					.(in_array($key, $selected) ? ' selected="selected"' : '').'>'
					.(string) $val."</option>\n";
			}
		}

		return $form;
	}
}

