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
class Reporte {

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
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
	public function formato_reporte($valor = '', $arr_param_campo = array(), $registro = array(), $campo = '')
	{
		$func_format_detalle = function($valor, $arr_param_campo, $registro, $campo)
		{
			$registro['permanencia'] = $campo;
			$arr_indices = array('id_tipo', 'centro', 'almacen', 'lote', 'estado_stock', 'material', 'tipo_material', 'permanencia');
			$valor_desplegar = fmt_cantidad($valor);

			return anchor(
				$arr_param_campo['href'].'?'.http_build_query(array_intersect_key($registro, array_flip($arr_indices))),
				($valor_desplegar === '') ? ' ' : $valor_desplegar
			);
		};

		$arr_formatos = array(
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
			'link_detalle_series' => $func_format_detalle,
		);

		$tipo_dato = $arr_param_campo['tipo'];
		if ( ! array_key_exists($tipo_dato, $arr_formatos))
		{
			return $valor;
		}

		return call_user_func_array($arr_formatos[$tipo_dato], array($valor, $arr_param_campo, $registro, $campo));
	}

	// --------------------------------------------------------------------

	/**
	 * Agrega elementos relacionados al ordenamiento al arreglo de campos
	 *
	 * @param  array  $arr_campos    Arreglo de campos del reporte
	 * @param  string $campo_default Campo default en caso que no venga informado
	 * @return void
	 */
	public function set_order_campos(&$arr_campos, $campo_default = '')
	{
		$ci =& get_instance();

		$sort_by = empty($ci->input->post_get('sort')) ? $campo_default : $ci->input->post_get('sort');
		$sort_by = ( ! preg_match('/^[+\-](.*)$/', $sort_by)) ? '+'.$sort_by : $sort_by;

		$sort_by_field  = substr($sort_by, 1, strlen($sort_by));
		$sort_by_order  = substr($sort_by, 0, 1);
		$new_orden_tipo = ($sort_by_order === '+') ? '-' : '+';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['sort'] = (($campo === $sort_by_field) ? $new_orden_tipo : '+').$campo;
			$order_icon = (substr($arr_campos[$campo]['sort'], 0, 1) === '+') ? 'sort-amount-desc' : 'sort-amount-asc';
			$arr_campos[$campo]['img_orden'] = ($campo === $sort_by_field) ? " <span class=\"fa fa-{$order_icon}\" ></span>" : '';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve string de ordenamiento
	 * @param  string $sort_by Orden en formato: +campo1,+campo2,-campo3
	 * @return string          Orden en formato: campo1 ASC, campo2 ASC, campo3 DESC
	 */
	public function get_order_by($sort_by)
	{
		return collect(explode(',', $sort_by))
			->map(function($value) {
				$value = ( ! preg_match('/^[+\-](.*)$/', trim($value))) ? '+'.trim($value) : trim($value);
				return substr($value, 1, strlen($value)).((substr($value, 0, 1) === '+') ? ' ASC' : ' DESC');
			})
			->implode(', ');
	}


	// --------------------------------------------------------------------

	/**
	 * Imprime reporte
	 *
	 * @param  array $arr_campos Arreglo con los campos del reporte
	 * @param  array $arr_datos  Arreglo con los datos del reporte
	 * @return string            Reporte
	 */
	public function genera_reporte($arr_campos = array(), $arr_datos = array())
	{
		$ci =& get_instance();

		$ci->load->library('table');
		$template = array(
			'table_open' => '<table class="table table-striped table-hover table-condensed reporte table-fixed-header">',
			'thead_open' => '<thead class="header">',
		);
		$ci->table->set_template($template);

		$script       = '<script type="text/javascript" src="'.base_url().'js/reporte.js"></script>';
		$subtotal_ant = '***init***';

		$campos_totalizables = array('numero', 'valor', 'numero_dif', 'valor_dif', 'link_detalle_series');
		$init_total_subtotal = collect($arr_campos)
			->filter(function($elem) use ($campos_totalizables) {return in_array($elem['tipo'], $campos_totalizables);})
			->map(function($elem) {return 0;});

		$arr_totales  = array(
			'campos'   => $campos_totalizables,
			'total'    => $init_total_subtotal
							->map(function($elem, $llave) use($arr_datos) {
								return collect($arr_datos)
									->reduce(function($total, $elem) use($llave) {return $total + $elem[$llave];}, 0);
							})
							->all(),
			'subtotal' => $init_total_subtotal,
		);


		// --- ENCABEZADO REPORTE ---
		$ci->table->set_heading($this->_reporte_linea_encabezado($arr_campos, $arr_totales));

		// --- CUERPO REPORTE ---
		$num_linea = 0;
		collect($arr_datos)
			->map(function($linea) use ($arr_campos, &$num_linea, $ci){
				$num_linea += 1;
				$ci->table->add_row($this->_reporte_linea_datos($linea, $arr_campos, $num_linea));
			});

		// --- TOTALES ---
		$ci->table->set_footer($this->_reporte_linea_totales('total', $arr_campos, $arr_totales));

		// --- ULTIMO SUBTOTAL ---
		// if ($this->_get_campo_subtotal($arr_campos) !== NULL)
		// {
		// 	$ci->table->add_row($this->_reporte_linea_totales('subtotal', $arr_campos, $arr_totales, $subtotal_ant));
		// 	$ci->table->add_row(array_fill(0, count($arr_campos) + 1, ''));
		// }


		return $ci->table->generate().' '.$script;
	}


	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con datos de encabezado del reporte
	 *
	 * @param  array $arr_campos  Arreglo con la descripcion de los campos del reporte
	 * @param  array $arr_totales Arreglo con los totales y subtotales de los campos del reporte
	 * @return array              Arreglo con los campos del encabezado
	 */
	private function _reporte_linea_encabezado($arr_campos = array(), &$arr_totales = array())
	{
		return array_merge(
			array(''),
			collect($arr_campos)
				->map(function($elem) {
					return array(
						'data' => "<span data-sort=\"{$elem['sort']}\" data-toggle=\"tooltip\" title=\"Ordenar por campo {$elem['titulo']}\">{$elem['titulo']}</span>{$elem['img_orden']}",
						'class' => isset($elem['class']) ? $elem['class'] : '',
					);
				})
				->all()
		);
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
	private function _reporte_linea_datos($arr_linea = array(), $arr_campos = array(), $num_linea = 0)
	{
		return (array_merge(
			array(array('data' => $num_linea, 'class' => 'text-muted')),
			collect($arr_campos)->map(function($elem, $llave) use ($arr_linea) {
				return array(
					'data' => $this->formato_reporte($arr_linea[$llave], $elem, $arr_linea, $llave),
					'class' => isset($elem['class']) ? $elem['class'] : '',
				);
			})
			->all()
		));
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
	private function _reporte_linea_totales($tipo = '', $arr_campos = array(), $arr_totales = array(), $nombre_subtotal = '')
	{
		return (array_merge(
			array(''),
			collect($arr_campos)->map(function($elem, $llave) use ($tipo, $arr_totales) {
				return array(
					'data' => in_array($elem['tipo'], $arr_totales['campos'])
						? $this->formato_reporte($arr_totales[$tipo][$llave], $elem)
						: '',
					'class' => isset($elem['class']) ? $elem['class'] : '',
				);
			})
			->all()
		));
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el nombre del campo con el tipo subtotal
	 *
	 * @param  array $arr_campos Arreglo con la descripcion de los campos del reporte
	 * @return string             Nombre del campo con el tipo subtotal
	 */
	private function _get_campo_subtotal($arr_campos = array())
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
	private function _es_nuevo_subtotal($arr_linea = array(), $arr_campos = array(), $subtotal_ant = NULL)
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
	private function _reporte_linea_subtotal($arr_linea = array(), $arr_campos = array(), &$arr_totales = array(), &$subtotal_ant = NULL)
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
		$ci->table->add_row(array(
			'data' => '<span class="fa fa-minus-circle"></span> <strong>'.$arr_linea[$campo_subtotal].'</strong>',
			'colspan' => count($arr_campos) + 1,
		));

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


}
/* libraries reporte.php */
/* Location: ./application/libraries/reporte.php */