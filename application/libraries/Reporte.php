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
		switch ($arr_param_campo['tipo'])
		{
			case 'texto':
							return $valor;
							break;
			case 'fecha':
							return fmt_fecha($valor);
							break;
			case 'link':
							return anchor($arr_param_campo['href'] . $valor, $valor);
							break;
			case 'link_registro':
							$arr_link = array();
							foreach ($arr_param_campo['href_registros'] as $campos_link)
							{
								$valor_registro = $campos_link === 'fecha'
									? fmt_fecha_db($registro[$campos_link])
									: $registro[$campos_link];
								array_push($arr_link, $valor_registro);
							}

							return anchor($arr_param_campo['href'].implode('/', $arr_link), $valor);
							break;
			case 'link_get_query':
							$arr_parameters = array();
							foreach ($arr_param_campo['link_parameters'] as $campos_link => $valor_link)
							{
								if ( ! is_array($valor_link))
								{
									$valor_link = array('nombre' => $campos_link, 'valor' => $valor_link);
								}

								if ($valor_link['valor'] !== NULL)
								{
									$arr_parameters[$valor_link['nombre']] = $valor_link['valor'];
								}
								else
								{
									$arr_parameters[$valor_link['nombre']] = ($campos_link === 'fecha')
										? fmt_fecha_db($registro[$campos_link])
										: $registro[$campos_link];
								}
							}

							return anchor($arr_param_campo['href'].http_build_query($arr_parameters), $valor);
							break;
			case 'link_detalle_series':
							$id_tipo        = array_key_exists('id_tipo', $registro) ? $registro['id_tipo'] : '';
							$centro         = array_key_exists('centro', $registro) ? $registro['centro'] : '';
							$almacen        = array_key_exists('almacen', $registro) ? $registro['almacen'] : '';
							$lote           = array_key_exists('lote', $registro) ? $registro['lote'] : '';
							$estado_stock   = array_key_exists('estado_stock', $registro) ? $registro['estado_stock'] : '';
							$material       = array_key_exists('material', $registro) ? $registro['material'] : '';
							$tipo_material  = array_key_exists('tipo_material', $registro) ? $registro['tipo_material']: '';
							$permanencia    = $campo;

							$href_param  = '?id_tipo='.$id_tipo;
							$href_param .= '&centro='.$centro;
							$href_param .= '&almacen='.$almacen;
							$href_param .= '&lote='.$lote;
							$href_param .= '&estado_stock='.$estado_stock;
							$href_param .= '&material='.$material;
							$href_param .= '&tipo_material='.$tipo_material;
							$href_param .= '&permanencia='.$permanencia;

							$valor_desplegar = fmt_cantidad($valor);
							return anchor($arr_param_campo['href'] . $href_param, $valor_desplegar === '' ? ' ' : $valor_desplegar);
							break;
			case 'numero':
							return fmt_cantidad($valor);
							break;
			case 'valor':
							return fmt_monto($valor);
							break;
			case 'valor_pmp':
							return fmt_monto($valor);
							break;
			case 'numero_dif':
							return '<strong>' .
								(($valor > 0)
									? '<span class="text-success">+'
									: (($valor < 0) ? '<span class="text-danger">' : '')) .
								fmt_cantidad($valor) . (($valor !== '0') ? '</span>' : '') .
								'</strong>';
							break;
			case 'valor_dif':
							return '<strong>' .
								(($valor > 0)
									? '<span class="text-success">+'
									: (($valor < 0) ? '<span class="text-danger">' : '')) .
								fmt_monto($valor) . (($valor !== 0) ? '</span>' : '') .
								'</strong>';
							break;
			default:
							return $valor;
							break;
		}

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
		$CI =& get_instance();

		$order_sort = $CI->input->post_get('order_sort');
		if ($order_sort === NULL)
		{
			$order_sort = 'ASC';
		}

		$order_by = $CI->input->post_get('order_by');
		if ($order_by === NULL)
		{
			$order_by = $campo_default;
		}

		$new_orden_tipo  = ($order_sort === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = ($campo === $order_by) ? $new_orden_tipo : 'ASC';

			$arr_tipo_icono_numero = array('numero', 'numero_dif', 'valor', 'valor_dif', 'valor_pmp');
			$arr_tipo_icono_texto  = array('link', 'texto');
			$tipo_icono = 'amount';
			$tipo_icono = in_array($arr_campos[$campo]['tipo'], $arr_tipo_icono_numero) ? 'numeric' : $tipo_icono;
			$tipo_icono = in_array($arr_campos[$campo]['tipo'], $arr_tipo_icono_texto) ? 'alpha' : $tipo_icono;

			$order_icon = ($arr_campos[$campo]['order_by'] === 'ASC') ? 'sort-'.$tipo_icono.'-desc' : 'sort-'.$tipo_icono.'-asc';

			$arr_campos[$campo]['img_orden'] = ($campo === $order_by) ? ' <span class="fa fa-'.$order_icon.'" ></span>' : '';
		}
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
		$num_linea    = 0;
		$subtotal_ant = '***init***';
		$arr_totales  = array(
			'campos'   => array('numero', 'valor', 'numero_dif', 'valor_dif', 'link_detalle_series'),
			'total'    => array(),
			'subtotal' => array(),
		);

		// --- ENCABEZADO REPORTE ---
		$ci->table->set_heading($this->_reporte_linea_encabezado($arr_campos, $arr_totales));

		// --- CUERPO REPORTE ---
		foreach ($arr_datos as $linea)
		{
			$num_linea += 1;

			if ($this->_get_campo_subtotal($arr_campos) !== NULL AND $this->_es_nuevo_subtotal($linea, $arr_campos, $subtotal_ant))
			{
				$this->_reporte_linea_subtotal($linea, $arr_campos, $arr_totales, $subtotal_ant);
			}

			$ci->table->add_row($this->_reporte_linea_datos($linea, $arr_campos, $arr_totales, $num_linea));
		}

		// --- ULTIMO SUBTOTAL ---
		if ($this->_get_campo_subtotal($arr_campos) !== NULL)
		{
			$ci->table->add_row($this->_reporte_linea_totales('subtotal', $arr_campos, $arr_totales, $subtotal_ant));
			$ci->table->add_row(array_fill(0, count($arr_campos) + 1, ''));
		}

		// --- TOTALES ---
		$ci->table->add_row($this->_reporte_linea_totales('total', $arr_campos, $arr_totales));

		return $ci->table->generate().' '.$script;
	}


	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con datos de encabezado del reporte
	 *
	 * @param  array $arr_campos  Arreglo con la descripcion de los campos del reporte
	 * @param  array $arr_totales Arreglo con los totales y subtotales de los campos del reporte
	 * @return array               Arreglo con los campos del encabezado
	 */
	private function _reporte_linea_encabezado($arr_campos = array(), &$arr_totales = array())
	{
		$arr_linea_encabezado = array();

		// primera celda, es en blanco
		array_push($arr_linea_encabezado, '');

		// recorre cada uno de los campos generando la celda de encabezado
		foreach ($arr_campos as $nombre_campo => $arr_param_campo)
		{
			if ( ! array_key_exists('class', $arr_param_campo))
			{
				$arr_param_campo['class'] = '';
			}

			$arr_celda = array(
				'data' =>
					'<span '.
						'order_by="'.$nombre_campo.'" '.
						'order_sort="'.$arr_param_campo['order_by'].'" '.
						'data-toggle="tooltip" '.
						'title="Ordenar por campo '.$arr_param_campo['titulo'].'">'.
						$arr_param_campo['titulo'].
					'</span>'.
					$arr_param_campo['img_orden'],
				'class' => $arr_param_campo === '' ? '' : $arr_param_campo['class'],
			);
			array_push($arr_linea_encabezado, $arr_celda);

			if (in_array($arr_param_campo['tipo'], $arr_totales['campos']))
			{
				$arr_totales['total'][$nombre_campo]    = 0;
				$arr_totales['subtotal'][$nombre_campo] = 0;
			}
		}

		return $arr_linea_encabezado;
	}

	// --------------------------------------------------------------------

	/**
	 * Genera arreglo con los datos de una linea del reporte
	 *
	 * @param  array   $arr_linea   Arreglo con los valores de la linea
	 * @param  array   $arr_campos  Arreglo con la descripcion de los campos del reporte
	 * @param  array   $arr_totales Arreglo con los totales y subtotales de los campos del reporte
	 * @param  integer $num_linea   Numero de linea actual
	 * @return array                 Arreglo con los campos de una linea
	 */
	private function _reporte_linea_datos($arr_linea = array(), $arr_campos = array(), &$arr_totales = array(), $num_linea = 0)
	{
		$arr_linea_datos = array();


		// primera celda, muestra el numero de la linea
		array_push($arr_linea_datos, array(
			'data'  => $num_linea,
			'class' => 'text-muted'
		));

		// recorre cada uno de los campos y formatea la celda
		foreach ($arr_campos as $nombre_campo => $arr_param_campo)
		{
			array_push($arr_linea_datos, array(
				'data'  => $this->formato_reporte($arr_linea[$nombre_campo], $arr_param_campo, $arr_linea, $nombre_campo),
				'class' => array_key_exists('class', $arr_param_campo) ? $arr_param_campo['class'] : ''
			));

			if (in_array($arr_param_campo['tipo'], $arr_totales['campos']))
			{
				$arr_totales['total'][$nombre_campo]    += $arr_linea[$nombre_campo];
				$arr_totales['subtotal'][$nombre_campo] += $arr_linea[$nombre_campo];
			}
		}

		return $arr_linea_datos;
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
		$arr_linea_totales = array();

		// primera celda, muestra el numero de la linea
		array_push($arr_linea_totales, '');

		// recorre cada uno de los campos y formatea la celda
		foreach ($arr_campos as $nombre_campo => $arr_param_campo)
		{
			if ($nombre_campo === $this->_get_campo_subtotal($arr_campos))
			{
				$data = strtoupper($tipo).' '.$nombre_subtotal;
			}
			else if (in_array($arr_param_campo['tipo'], $arr_totales['campos']))
			{
				$data = $this->formato_reporte($arr_totales[$tipo][$nombre_campo], $arr_param_campo);
			}
			else
			{
				$data = '';
			}

			array_push($arr_linea_totales, array(
				'data' => '<strong>'.$data.'</strong>',
				'class' => array_key_exists('class', $arr_param_campo) ? $arr_param_campo['class'] : ''
			));
		}

		return $arr_linea_totales;
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