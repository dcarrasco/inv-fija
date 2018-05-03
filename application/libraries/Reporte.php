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
trait Reporte {

	/**
	 * Arreglo con datos del reporte
	 *
	 * @var array
	 */
	protected $datos_reporte;

	/**
	 * Arreglo con definicion de campos del reporte
	 *
	 * @var array
	 */
	protected $campos_reporte;

	/**
	 * Configuración de la tabla del reporte
	 *
	 * @var array
	 */
	protected $table_template = [
		'table_open' => '<table class="table table-striped table-hover table-condensed reporte table-fixed-header">',
		'thead_open' => '<thead class="header">',
	];

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo con los formatos válidos para los reportes
	 *
	 * @return array
	 */
	protected function formatos()
	{
		return [
			'texto'         => function($valor) {return $valor;},
			'fecha'         => function($valor) {return fmt_fecha($valor);},
			'numero'        => function($valor) {return fmt_cantidad($valor, 0, TRUE);},
			'valor'         => function($valor) {return fmt_monto($valor, 'UN', '$', 0, TRUE);},
			'valor_pmp'     => function($valor) {return fmt_monto($valor, 'UN', '$', 0, TRUE);},
			'numero_dif'    => function($valor) {return fmt_cantidad($valor, 0, TRUE, TRUE);},
			'valor_dif'     => function($valor) {return fmt_monto($valor, 'UN', '$', 0, TRUE, TRUE);},
			'link'          => function($valor, $param) {return anchor($param['href'] . $valor, $valor);},
			'link_registro' => function($valor, $param, $registro) {
				return anchor(
					$param['href'].'/'.collect($param['href_registros'])
						->map(function($elem) use($registro) {return $registro[$elem];})
						->implode('/'),
					$valor);
			},
			'link_detalle_series' => function($valor, $arr_param_campo, $registro, $campo) {
				$registro['permanencia'] = $campo;
				$arr_indices = ['id_tipo', 'centro', 'almacen', 'lote', 'estado_stock', 'material', 'tipo_material', 'permanencia'];
				$valor_desplegar = fmt_cantidad($valor);

				return anchor(
					$arr_param_campo['href'].url_params(array_intersect_key($registro, array_flip($arr_indices))),
					($valor_desplegar === '') ? ' ' : $valor_desplegar
				);
			},
			'doi' => function($valor) {
				$color = is_null($valor)
					? 'danger'
					: ($valor <= 65
						? 'success'
						: ($valor <= 90
							? 'warning'
							: 'danger'));

				return fmt_cantidad($valor, $valor < 10 ? 1 : 0, TRUE)
					." <i class=\"fa fa-circle text-{$color}\"></i>";
			},
		];
	}


	// --------------------------------------------------------------------
	/**
	 * Formatea un valor de acuerdo a los parametros de un reporte
	 *
	 * @param  string $valor           Valor a formatear
	 * @param  array  $arr_param_campo Parametros del campo
	 * @param  array  $registro        Registro del valor, para extraer valores del link
	 * @param  string $campo           [description]
	 * @return string                  Variable formateada
	 */
	public function formato_reporte($valor = '', $arr_param_campo = [], $registro = [], $campo = '')
	{
		$tipo_dato = $arr_param_campo['tipo'];

		return array_key_exists($tipo_dato, $this->formatos())
			? call_user_func_array($this->formatos()[$tipo_dato], [$valor, $arr_param_campo, $registro, $campo])
			: $valor;
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega elementos relacionados al ordenamiento al arreglo de campos
	 *
	 * @param  string $campos        Campos
	 * @param  string $campo_default Campo default en caso que no venga informado
	 * @return void
	 */
	public function set_order_campos($campos = [], $campo_default = '')
	{
		$sort_by = empty(request('sort')) ? $campo_default : request('sort');
		$sort_by = ( ! preg_match('/^[+\-](.*)$/', $sort_by)) ? '+'.$sort_by : $sort_by;

		$sort_by_field  = substr($sort_by, 1, strlen($sort_by));
		$new_orden_tipo = (substr($sort_by, 0, 1) === '+') ? '-' : '+';

		return $this->campos_reporte = collect($campos)
			->map(function($props_campo, $campo) use ($sort_by_field, $new_orden_tipo) {
				$props_campo['sort'] = (($campo === $sort_by_field) ? $new_orden_tipo : '+').$campo;
				$order_icon = (substr($props_campo['sort'], 0, 1) === '+') ? 'sort-amount-desc' : 'sort-amount-asc';
				$props_campo['img_orden'] = ($campo === $sort_by_field) ? " <span class=\"fa fa-{$order_icon}\" ></span>" : '';

				return $props_campo;
			})->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve string de ordenamiento
	 * @param  string $sort_by Orden en formato: +campo1,+campo2,-campo3
	 * @return string          Orden en formato: campo1 ASC, campo2 ASC, campo3 DESC
	 */
	public function format_order_by($sort_by)
	{
		return collect(explode(',', $sort_by))
			->map(function($value) {
				$value = trim($value);
				$value = ( ! preg_match('/^[+\-](.*)$/', $value)) ? '+'.$value : $value;

				return substr($value, 1, strlen($value)).((substr($value, 0, 1) === '+') ? ' ASC' : ' DESC');
			})
			->implode(', ');
	}


	// --------------------------------------------------------------------

	/**
	 * Imprime reporte
	 *
	 * @return string            Reporte
	 */
	public function genera_reporte()
	{
		$datos = collect($this->datos_reporte);

		$campos = collect($this->campos_reporte)->map(function ($elem) {
			$elem['tipo'] = array_get($elem, 'tipo', 'texto');
			return $elem;
		})->all();

		$ci =& get_instance();
		$ci->load->library('table');
		$ci->table->set_template($this->table_template);

		$script       = '<script type="text/javascript" src="'.base_url().'js/reporte.js"></script>';
		$subtotal_ant = '***init***';

		$campos_totalizables = ['numero', 'valor', 'numero_dif', 'valor_dif', 'link_detalle_series'];

		$arr_totales  = [
			'campos' => $campos_totalizables,
			'total'  => collect($campos)
				->filter(function($campo) use ($campos_totalizables) {
					return in_array($campo['tipo'], $campos_totalizables);
				})->map(function($elem, $campo) use ($datos) {
					return $datos->sum($campo);
				})->all(),
			'subtotal' => [],
		];

		// --- ENCABEZADO REPORTE ---
		$ci->table->set_heading($this->_reporte_linea_encabezado($campos));

		// --- CUERPO REPORTE ---
		$datos->each(function($linea, $num_linea) use ($campos, $ci){
			$ci->table->add_row($this->_reporte_linea_datos($linea, $campos, $num_linea + 1));
		});

		// --- TOTALES ---
		$ci->table->set_footer($this->_reporte_linea_totales('total', $campos, $arr_totales));

		// --- ULTIMO SUBTOTAL ---
		// if ($this->_get_campo_subtotal($campos) !== NULL)
		// {
		// 	$ci->table->add_row($this->_reporte_linea_totales('subtotal', $campos, $arr_totales, $subtotal_ant));
		// 	$ci->table->add_row(array_fill(0, count($campos) + 1, ''));
		// }

		return $ci->table->generate().' '.$script;
	}


	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con datos de encabezado del reporte
	 *
	 * @param  array $campos Arreglo con la descripcion de los campos del reporte
	 * @return array         Arreglo con los campos del encabezado
	 */
	private function _reporte_linea_encabezado($campos = [])
	{
		return collect([''])->merge(
			collect($campos)->map(function($campo) {
				return [
					'data' => "<span data-sort=\"{$campo['sort']}\" data-toggle=\"tooltip\" title=\"Ordenar por campo {$campo['titulo']}\">{$campo['titulo']}</span>{$campo['img_orden']}",
					'class' => array_get($campo, 'class', ''),
				];
			})
		)->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con los datos de una linea del reporte
	 *
	 * @param  array   $linea     Arreglo con los valores de la linea
	 * @param  array   $campos    Arreglo con la descripcion de los campos del reporte
	 * @param  integer $num_linea Numero de linea actual
	 * @return array                 Arreglo con los campos de una linea
	 */
	private function _reporte_linea_datos($linea = [], $campos = [], $num_linea = 0)
	{
		return collect([['data'=>$num_linea, 'class'=>'text-muted']])->merge(
			collect($campos)->map(function($campo, $llave) use ($linea) {
				return [
					'data' => $this->formato_reporte(array_get($linea, $llave), $campo, $linea, $llave),
					'class' => array_get($campo, 'class', ''),
				];
			})
		)->all();
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
	private function _reporte_linea_totales($tipo = '', $arr_campos = [], $arr_totales = [], $nombre_subtotal = '')
	{
		return collect([''])->merge(
			collect($arr_campos)->map(function($elem, $llave) use ($tipo, $arr_totales) {
				return [
					'data' => in_array($elem['tipo'], $arr_totales['campos'])
						? $this->formato_reporte($arr_totales[$tipo][$llave], $elem)
						: '',
					'class' => array_get($elem, 'class', ''),
				];
			})
		)->all();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el nombre del campo con el tipo subtotal
	 *
	 * @param  array $arr_campos Arreglo con la descripcion de los campos del reporte
	 * @return string             Nombre del campo con el tipo subtotal
	 */
	private function _get_campo_subtotal($arr_campos = [])
	{
		foreach($arr_campos as $nombre_campo => $parametros_campo)
		{
			if ($parametros_campo['tipo'] === 'subtotal')
			{
				return $nombre_campo;
			}
		}
		return NULL;
	}

	// --------------------------------------------------------------------

	/**
	 * Indica si la linea del reporte actual contiene un nuevo valor del campo subtotal
	 *
	 * @param  array  $arr_linea    Arreglo con los valores de la linea
	 * @param  array  $arr_campos   Arreglo con la descripcion de los campos del reporte
	 * @param  string $subtotal_ant Nombre del campo con el subtotal anterior
	 * @return boolean              Indicador si la linea actual cambio el valor del campo subtotal
	 */
	private function _es_nuevo_subtotal($arr_linea = [], $arr_campos = [], $subtotal_ant = NULL)
	{
		$campo_subtotal = $this->_get_campo_subtotal($arr_campos);

		if ($subtotal_ant === '***init***' OR $arr_linea[$campo_subtotal] !== $subtotal_ant)
		{
			return TRUE;
		}

		return FALSE;
	}

	// --------------------------------------------------------------------

	/**
	 * Genera linas de totalización del subtotal e inicio de un nuevo grupo
	 *
	 * @param  array  $arr_linea    Arreglo con los valores de la linea
	 * @param  array  $arr_campos   Arreglo con la descripcion de los campos del reporte
	 * @param  array  $arr_totales  Arreglo con los totales y subtotales de los campos del reporte
	 * @param  string $subtotal_ant Nombre del campo con el subtotal anterior
	 * @return void
	 */
	private function _reporte_linea_subtotal($arr_linea = [], $arr_campos = [], &$arr_totales = [], &$subtotal_ant = NULL)
	{
		$ci =& get_instance();

		$campo_subtotal = $this->_get_campo_subtotal($arr_campos);

		// si el subtotal anterior no es nulo, se deben imprimir linea de subtotales
		if ($subtotal_ant !== '***init***')
		{
			$ci->table->add_row($this->_reporte_linea_totales('subtotal', $arr_campos, $arr_totales, $subtotal_ant));

			// agrega linea en blanco
			$ci->table->add_row(array_fill(0, count($arr_campos) + 1, ''));
		}

		// agrega linea con titulo del subtotal
		$ci->table->add_row([
			'data' => '<span class="fa fa-minus-circle"></span> <strong>'.$arr_linea[$campo_subtotal].'</strong>',
			'colspan' => count($arr_campos) + 1,
		]);

		// nuevo subtotal, y deja en cero, la suma de subtotales
		$subtotal_ant = $arr_linea[$campo_subtotal];

		foreach ($arr_campos as $nombre_campo => $arr_param_campo)
		{
			if (in_array($arr_param_campo['tipo'], $arr_totales['campos']))
			{
				$arr_totales['subtotal'][$nombre_campo] = 0;
			}
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve arreglo result formateado para presentación por mes
	 *
	 * @param  array $data Datos del result
	 * @return Collection  Collección con el resultado
	 */
	public function result_to_month_table($data)
	{
		if ( ! $data)
		{
			return collect([]);
		}

		$data = collect($data);

		$anomes = $data->pluck('fecha')
			->map(function($fecha) {
				return fmt_fecha($fecha, 'Ym');
			})->unique()->first();

		$dias = collect(get_arr_dias_mes($anomes));

		return $data->pluck('llave')
			->sort()
			->unique()
			->map_with_keys(function($llave) use ($dias, $data) {
				$data_llave = $data
					->filter(function($dato) use ($llave) {
						return array_get($dato, 'llave') === $llave;
					})
					->map_with_keys(function($dato) {
						return [fmt_fecha(array_get($dato, 'fecha'), 'd') => array_get($dato, 'dato')];
					});

				return [$llave => $dias->merge($data_llave)->all()];
			});

	}


}
/* libraries Reporte.php */
/* Location: ./application/libraries/Reporte.php */
