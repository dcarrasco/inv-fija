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

if ( ! function_exists('dbg'))
{
	/**
	 * Debug de varianbles
	 *
	 * @return void
	 */
	function dbg()
	{
		return call_user_func_array('\Formatter::print_debug', func_get_args());
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('dump'))
{
	/**
	 * Debug de variables
	 *
	 * @return void
	 */
	function dump()
	{
		return call_user_func_array('dbg', func_get_args());
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('dd'))
{
	/**
	 * Debug de variables y para la ejecución del programa
	 *
	 * @return void
	 */
	function dd()
	{
		call_user_func_array('dump', func_get_args());
		die();
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('menu_app'))
{
	/**
	 * Devuelve arreglo con el menu de las aplicaciones del sistema
	 *
	 * @return string    Texto con el menu (<ul>) de las aplicaciones
	 */
	function menu_app()
	{
		return \Acl\Acl::create()->menu_app();
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('titulo_modulo'))
{
	/**
	 * Devuelve el titulo del modulo
	 *
	 * @return string Titulo del modulo
	 */
	function titulo_modulo()
	{
		return \Acl\Acl::create()->titulo_modulo();
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('menu_modulo'))
{
	/**
	 * Reestructura arreglo menu modulo para ser usado en parser
	 *
	 * @param  array  $menu         Menu del módulo
	 * @param  string $mod_selected Módulo seleccionado
	 * @return array                Arreglo reestructurado
	 */
	function menu_modulo($menu = [], $mod_selected = '')
	{
		return \Acl\Acl::create()->menu_modulo($menu, $mod_selected);
	}
}
// --------------------------------------------------------------------

if ( ! function_exists('app_render_view'))
{
	/**
	 * Render vista
	 *
	 * @param  mixed $vista Nombre o arreglo de nombre de la(s) vista(s) a dibujar
	 * @param  array $datos Arreglo con parámetros de datos a dibujar
	 * @return void
	 */
	function app_render_view($vista = NULL, $datos = [])
	{
		if ( ! $vista)
		{
			return;
		}

		// carga objeto global CI
		$ci =& get_instance();

		if (array_key_exists('menu_modulo', $datos))
		{
			$datos['menu_modulo'] = menu_modulo($datos['menu_modulo'], is_string($vista) ? basename($vista) : '');
		}

		$vista_login = array_get($datos, 'vista_login', FALSE);

		// titulos y variables generales
		$datos['app_title']    = (ENVIRONMENT !== 'production' ? 'DEV - ' : '').$ci->config->item('app_nombre');
		$datos['base_url']     = base_url();
		$datos['js_base_url']  = empty($ci->config->item('index_page')) ? base_url() : base_url().$ci->config->item('index_page').'/';
		$datos['extra_styles'] = array_get($datos, 'extra_styles', '');

		// navegación
		$datos['is_vista_login']  = $vista_login;
		$datos['navbar_menu']     = $vista_login ? [] : menu_app();
		$datos['titulo_modulo']   = titulo_modulo();
		$datos['logout_url']      = site_url('login/logout');
		$datos['user_firstname']  = \Acl\Acl::create()->get_user_firstname();
		$datos['app_navbar']      = $ci->parser->parse('common/app_navbar', $datos, TRUE);
		$datos['app_menu_modulo'] = array_key_exists('menu_modulo', $datos) ? $ci->parser->parse('common/app_menu_modulo', $datos, TRUE) : NULL;

		// otros
		$datos['msg_alerta']        = $ci->session->flashdata('msg_alerta');
		$datos['validation_errors'] = print_validation_errors();

		// vistas
		$datos['arr_vistas'] = [];
		$vista = is_array($vista) ? $vista : [$vista];
		foreach ($vista as $item_vista)
		{
			array_push($datos['arr_vistas'], ['vista' => $ci->parser->parse($item_vista, $datos, TRUE)]);
		}

		return $ci->parser->parse('common/app_layout', $datos);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('lang'))
{
	/**
	 * Recupera una linea de texto del arreglo de lenguaje
	 * @param  string $line Linea a recuperar
	 * @return string
	 */
	function lang($line)
	{
		return get_instance()->lang->line($line);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('print_message'))
{
	/**
	 * Devuelve un mensaje de alerta o error
	 *
	 * @param  string $mensaje Mensaje a desplegar
	 * @param  string $tipo    Tipo de mensaje (warning, danger, info, success)
	 * @return string          Mensaje formateado
	 */
	function print_message($mensaje = '', $tipo = 'info')
	{
		if ( $mensaje OR $mensaje !== '')
		{
			// carga objeto global CI
			$ci =& get_instance();

			$texto_tipo = 'INFORMACI&Oacute;N';
			$img_tipo   = 'info-sign';

			if ($tipo === 'warning')
			{
				$texto_tipo = 'ALERTA';
				$img_tipo   = 'warning-sign';
			}
			elseif ($tipo === 'danger' OR $tipo === 'error')
			{
				$tipo = 'danger';
				$texto_tipo = 'ERROR';
				$img_tipo   = 'exclamation-sign';
			}
			elseif ($tipo === 'success')
			{
				$texto_tipo = '&Eacute;XITO';
				$img_tipo   = 'ok-sign';
			}

			$arr_datos_view = [
				'tipo'       => $tipo,
				'texto_tipo' => $texto_tipo,
				'img_tipo'   => $img_tipo,
				'mensaje'    => $mensaje,
			];

			return $ci->parser->parse('common/alert', $arr_datos_view, TRUE);
		}
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('set_message'))
{
	/**
	 * Devuelve un mensaje de alerta o error
	 *
	 * @param  string $mensaje Mensaje a desplegar
	 * @param  string $tipo    Tipo de mensaje (warning, danger, info, success)
	 * @return void
	 */
	function set_message($mensaje = '', $tipo = 'info')
	{
		return get_instance()->session->set_flashdata('msg_alerta', print_message($mensaje, $tipo));
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('print_validation_errors'))
{
	/**
	 * Imprime errores de validacion
	 *
	 * @return string Errores de validacion
	 */
	function print_validation_errors()
	{
		// carga objeto global CI
		$ci =& get_instance();

		if ($ci->errors->is_empty())
		{
			$ci->errors = collect($ci->form_validation->error_array());
		}

		return ($ci->errors->length() > 0)
			? print_message('<ul>'.
				$ci->errors->concat(function($error) {
					return '<li>'.$error.'</li>';
				})
				.'</ul>', 'danger')
			: '';
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('form_array_format'))
{
	/**
	 * Formatea un arreglo para que sea usado en un formuario select
	 * Espera que el arreglo tenga a lo menos las llaves "llave" y "valor"
	 *
	 * @param  array  $arreglo Arreglo a transformar
	 * @param  string $msg_ini Elemento inicial a desplegar en select
	 * @return array           Arreglo con formato a utilizar
	 */
	function form_array_format($arreglo = [], $msg_ini = '')
	{
		return collect(empty($msg_ini) ? [] : ['' => $msg_ini])
			->merge(collect($arreglo)->map_with_keys(function($item) {
				return [$item['llave'] => $item['valor']];
			}))->all();
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('form_has_error_class'))
{
	/**
	 * Indica si el elemento del formulario tiene un error de validación
	 *
	 * @param  string $form_field Nombre del elemento del formulario
	 * @return bool               Indicador de error del elemento
	 */
	function form_has_error_class($form_field = '')
	{
		$ci =& get_instance();

		if ($ci->errors->is_empty())
		{
			$ci->errors = collect($ci->form_validation->error_array());
		}

		return $ci->errors->has($form_field) ? 'has-error' : '';
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('errors'))
{
	/**
	 * Devuelve el error de un campo
	 *
	 * @param  string $form_field Campo
	 * @return string             Error del campo
	 */
	function errors($form_field = '')
	{
		$error = get_instance()->errors->get($form_field, '');

		return empty($error) ? '' : "<p class=\"text-danger\">{$error}</p>";
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('url_params'))
{
	/**
	 * Transforma un arreglo en una cadena de parametros url
	 *
	 * @param  array $params Arreglo con los parámetros
	 * @return string
	 */
	function url_params($params = [])
	{
		$url_params = http_build_query(count($params) === 0
			? get_instance()->input->get()
			: $params
		);

		return empty($url_params) ? '' : '?'.$url_params;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('fmt_cantidad'))
{
	/**
	 * Formatea cantidades numéricas con separador decimal y de miles
	 *
	 * @param  integer $valor        Valor a formatear
	 * @param  integer $decimales    Cantidad de decimales a mostrar
	 * @param  boolean $mostrar_cero Indica si muestra o no valores ceros
	 * @param  boolean $format_diff  Indica si formatea valores positivos (verde) y negativos (rojo)
	 * @return string                Valor formateado
	 */
	function fmt_cantidad($valor = 0, $decimales = 0, $mostrar_cero = FALSE, $format_diff = FALSE)
	{
		return \Formatter::cantidad($valor, $decimales, $mostrar_cero, $format_diff);
	}
}


// --------------------------------------------------------------------

if ( ! function_exists('fmt_monto'))
{
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
	function fmt_monto($monto = 0, $unidad = 'UN', $signo_moneda = '$', $decimales = 0, $mostrar_cero = FALSE, $format_diff = FALSE)
	{
		return \Formatter::monto($monto, $unidad, $signo_moneda, $decimales, $mostrar_cero, $format_diff);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('fmt_hora'))
{
	/**
	 * Devuelve una cantidad de segundos como una hora
	 *
	 * @param  integer $segundos_totales Cantidad de segundos a formatear
	 * @return string       Segundos formateados como hora
	 */
	function fmt_hora($segundos_totales = 0)
	{
		return \Formatter::hora($segundos_totales);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('fmt_fecha'))
{
	/**
	 * Devuelve una fecha de la BD formateada para desplegar
	 *
	 * @param  string $fecha   Fecha a formatear
	 * @param  string $formato Formato a devolver
	 * @return string          Fecha formateada segun formato
	 */
	function fmt_fecha($fecha = NULL, $formato = 'Y-m-d')
	{
		return \Formatter::fecha($fecha, $formato);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('fmt_fecha_db'))
{
	/**
	 * Devuelve una fecha del usuario para consultar en la BD
	 *
	 * @param  string $fecha Fecha a formatear
	 * @return string        Fecha formateada segun formato
	 */
	function fmt_fecha_db($fecha = NULL)
	{
		return fmt_fecha($fecha, 'Ymd');
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('fmt_rut'))
{
	/**
	 * Formatea un RUT
	 *
	 * @param  string $numero_rut RUT a formatear
	 * @return string
	 */
	function fmt_rut($numero_rut = NULL)
	{
		return \Formatter::rut($numero_rut);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('collect'))
{
	/**
	 * Wrapper de la función array_map
	 * @param  array $arreglo Arreglo con datos
	 * @return Collection
	 */
	function collect($arreglo = [])
	{
		return new Collection($arreglo);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('config'))
{
	/**
	 * Recupera el valor de un elemento de configuración
	 *
	 * @param  string $item Item a recuperar
	 * @return string       Valor de configuración
	 */
	function config($item = '')
	{
		return get_instance()->config->item($item);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('request'))
{
	/**
	 * Devuelve un valor de post o get
	 * @param  string $field   Campo a recuperar el valor
	 * @param  string $default Valor a devolver por defecto
	 * @return mixed           Variable post o get
	 */
	function request($field = NULL, $default = NULL)
	{
		$ci =& get_instance();

		if ( ! $ci->request)
		{
			$old_request = collect($ci->session->flashdata('old_request'));
			$ci->request = collect(array_merge($ci->input->post(), $ci->input->get()))
				->merge($old_request);
		}

		if (is_null($field))
		{
			return $ci->request;
		}

		if (is_array($field))
		{
			return $ci->request->only($field)->all();
		}

		return $ci->request->get($field, $default);
		// return array_get($ci->request->all(), $field, $default);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('array_get'))
{
	/**
	 * Devuelve el valor de un elemento de un arreglo
	 * @param  array  $arreglo Arreglo a buscar el valor
	 * @param  string $indice  Indice a recuperar
	 * @param  mixed  $default Valor por defecto en caso de no encontrar el valor
	 *
	 * @return mixed
	 */
	function array_get($arreglo = [], $indice = NULL, $default = NULL)
	{
		if ( ! is_array($arreglo))
		{
			return $default;
		}

		if (is_null($indice))
		{
			return $arreglo;
		}

		if (array_key_exists($indice, $arreglo))
		{
			return $arreglo[$indice];
		}

		foreach (explode('.', $indice) as $segmento)
		{
			if (is_array($arreglo) AND array_key_exists($segmento, $arreglo))
			{
				$arreglo = $arreglo[$segmento];
			}
			else
			{
				return $default;
			}
		}

		return $arreglo;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('form_validation'))
{
	/**
	 * Valida los datos del request contra un arreglo
	 *
	 * @param  array $rules Reglas a validar
	 * @return boolean      Indicador de exito de la validación
	 */
	function form_validation($rules = [])
	{
		$ci =& get_instance();

		return $ci->form_validation->set_data(request()->all())->set_rules($rules)->run();
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('route_validation'))
{
	/**
	 * Ejecuta validación y vuelve hacia atrás en caso que no sea válido
	 *
	 * @param  mixed $is_valid Resultado de la validación o arreglo a con reglas a validar
	 * @return void
	 */
	function route_validation($is_valid = FALSE)
	{
		$ci = &get_instance();

		$is_valid = is_array($is_valid) ? form_validation($is_valid) : $is_valid;

		if ( ! $is_valid)
		{
			$ci->session->set_flashdata('errors', $ci->form_validation->error_array());
			$ci->session->set_flashdata('old_request', array_merge($ci->input->post(), $ci->input->get()));

			redirect($ci->input->server('HTTP_REFERER'));
		}
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('cached_query'))
{
	/**
	 * Ejecuta una función de un modelo (query) o devuelve el resultado
	 * almacenado en el cache.
	 *
	 * @param  string $cache_id ID o identificador unico de la función y sus parámetros
	 * @param  mixed  $object   Objeto o modelo que contiene la función a ejecutar
	 * @param  string $method   Nombre de la función o método a ejecutar
	 * @param  array  $params   Arreglo con los parámetros de la función a ejecutar
	 * @return mixed            Resultado de la función
	 */
	function cached_query($cache_id = '', $object = NULL, $method = '', $params = [])
	{
		$ci =& get_instance();
		$ci->load->driver('cache', ['adapter' => 'file']);
		$cache_ttl = 300;

		$params = ( ! is_array($params)) ? [$params] : $params;

		log_message('debug', "cached_query: id({$cache_id}), object(".get_class($object)."), method({$method}), params(".json_encode($params).")");

		// limpia caches antiguos
		if (is_array($ci->cache->cache_info()))
		{
			foreach($ci->cache->cache_info() as $cache_ant_id => $cache_ant_data)
			{
				if ($cache_ant_data['date'] < now() - $cache_ttl AND strtolower(substr($cache_ant_data['name'], -4)) !== 'html')
				{
					$ci->cache->delete($cache_ant_id);
				}
			}
		}

		if ( ! method_exists($object, $method))
		{
			log_message('error', 'cached_query: Metodo "'.$method.'"" no existe en objeto "'.get_class($object).'".');
			return NULL;
		}

		$cache_id = hash('md5', $cache_id);

		if ( ! $result = $ci->cache->get($cache_id))
		{
			$result = call_user_func_array([$object, $method], $params);
			$ci->cache->save($cache_id, $result, $cache_ttl);
		}

		return $result;
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_arr_dias_mes'))
{
	/**
	 * Devuelve arreglo con dias del mes
	 *
	 * @param  string $anomes Mes y año a consultar (formato YYYYMM)
	 * @return array          Arreglo con dias del mes (llaves en formato DD)
	 */
	function get_arr_dias_mes($anomes = NULL)
	{
		$mes = (int) substr($anomes, 4, 2);
		$ano = (int) substr($anomes, 0, 4);

		return collect(array_fill(1, days_in_month($mes, $ano), NULL))
			->map_with_keys(function($valor, $indice) {
				return [str_pad($indice, 2, '0', STR_PAD_LEFT) => $valor];
			})->all();
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('get_fecha_hasta'))
{
	/**
	 * Devuelve la fecha más un mes
	 *
	 * @param  string $anomes Mes y año a consultar (formato YYYYMM)
	 * @return string         Fecha más un mes (formato YYYYMMDD)
	 */
	function get_fecha_hasta($anomes = NULL)
	{
		$mes = (int) substr($anomes, 4, 2);
		$ano = (int) substr($anomes, 0, 4);

		return (string) (($mes === 12) ? ($ano+1)*10000+(1)*100+1 : $ano*10000+($mes+1)*100+1);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('dias_de_la_semana'))
{
	/**
	 * Devuelve arreglo con nombre los días de la semana
	 *
	 * @return array
	 */
	function dias_de_la_semana($dia)
	{
		return array_get([
			'0' => 'Do',
			'1' => 'Lu',
			'2' => 'Ma',
			'3' => 'Mi',
			'4' => 'Ju',
			'5' => 'Vi',
			'6' => 'Sa',
		], $dia);
	}
}

// --------------------------------------------------------------------

if ( ! function_exists('clase_cumplimiento_consumos'))
{
	/**
	 * Devuelve la clase pintar el cumplimiento diario
	 *
	 * @param  integer $porcentaje_cumplimiento % de cumplimiento
	 * @return string                           Clase
	 */
	function clase_cumplimiento_consumos($porcentaje_cumplimiento = 0)
	{
		return $porcentaje_cumplimiento >= 0.9
			? 'success'
			: ($porcentaje_cumplimiento >= 0.6
				? 'warning'
				: 'danger');
	}
}


/* helpers varios_helper.php */
/* Location: ./application/helpers/varios_helper.php */
