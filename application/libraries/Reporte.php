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
		$arr_formatos = array(
			'texto'     => function($valor) {return $valor;},
			'fecha'     => function($valor) {return fmt_fecha($valor);},
			'link'      => function($valor, $arr_param_campo) {return anchor($arr_param_campo['href'] . $valor, $valor);},
			'numero'    => function($valor) {return fmt_cantidad($valor);},
			'valor'     => function($valor) {return fmt_monto($valor);},
			'valor_pmp' => function($valor) {return fmt_monto($valor);},
			'numero_dif' => function($valor) {return '<strong>' . (($valor > 0) ? '<span class="text-success">+' : (($valor < 0) ? '<span class="text-danger">' : '')) . fmt_cantidad($valor) . (($valor !== '0') ? '</span>' : '') .'</strong>';},
			'valor_dif' => function($valor) {return '<strong>' .(($valor > 0) ? '<span class="text-success">+' : (($valor < 0) ? '<span class="text-danger">' : '')) .fmt_monto($valor) . (($valor !== 0) ? '</span>' : '') .'</strong>';},
			'link_registro' => function($valor, $arr_param_campo, $registro)
				{
					$arr_link = array();
					foreach ($arr_param_campo['href_registros'] as $campos_link)
					{
						$valor_registro = ($campos_link === 'fecha') ? fmt_fecha_db($registro[$campos_link]) : $registro[$campos_link];
						array_push($arr_link, $valor_registro);
					}

					return anchor($arr_param_campo['href'].'/'.implode('/', $arr_link), $valor);
				},
			'link_detalle_series' => function($valor, $arr_param_campo, $registro, $campo)
				{
					$registro['permanencia'] = $campo;
					$arr_indices = array('id_tipo', 'centro', 'almacen', 'lote', 'estado_stock', 'material', 'tipo_material', 'permanencia');
					$valor_desplegar = fmt_cantidad($valor);

					return anchor(
						$arr_param_campo['href'].'?'.http_build_query(array_intersect_key($registro, array_flip($arr_indices))),
						($valor_desplegar === '') ? ' ' : $valor_desplegar
					);
				},
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

		$sort_by = $ci->input->post_get('sort');

		if ($sort_by === NULL OR $sort_by === '')
		{
			$sort_by_field = $campo_default;
			$sort_by_order = '+';
		}
		else
		{
			if ( ! in_array(substr($sort_by, 0, 1), array('+', '-')))
			{
				$sort_by = '+'.$sort_by;
			}
			$sort_by_field = substr($sort_by, 1, strlen($sort_by));
			$sort_by_order = substr($sort_by, 0, 1);
		}

		$new_orden_tipo  = ($sort_by_order === '+') ? '-' : '+';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['sort'] = (($campo === $sort_by_field) ? $new_orden_tipo : '+').$campo;

			$arr_tipo_icono_numero = array('numero', 'numero_dif', 'valor', 'valor_dif', 'valor_pmp');
			$arr_tipo_icono_texto  = array('link', 'texto');
			$tipo_icono = 'amount';
			$tipo_icono = in_array($arr_campos[$campo]['tipo'], $arr_tipo_icono_numero) ? 'numeric' : $tipo_icono;
			$tipo_icono = in_array($arr_campos[$campo]['tipo'], $arr_tipo_icono_texto) ? 'alpha' : $tipo_icono;

			$order_icon = (substr($arr_campos[$campo]['sort'], 0, 1) === '+') ? "sort-{$tipo_icono}-desc" : "sort-{$tipo_icono}-asc";

			$arr_campos[$campo]['img_orden'] = ($campo === $sort_by_field) ? " <span class=\"fa fa-{$order_icon}\" ></span>" : '';
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve string de ordenamiento
	 * @param  string $sort_by Orden en formato: +campo1, +campo2, -campo3
	 * @return string          Orden en formato: campo1 ASC, campo2 ASC, campo3 DESC
	 */
	public function get_order_by($sort_by)
	{
		return implode(array_map(array($this, '_order_by'), explode(',', $sort_by)), ', ');
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve un string con formato "[+/-]campo" a formato "campo [ASC/DESC]"
	 *
	 * @param  string $sort_by Texto a cambiar el formato
	 * @return string          Texto con formato final
	 */
	private function _order_by($sort_by)
	{
		$sort_by = ( ! in_array(substr(trim($sort_by), 0, 1), array('+', '-'))) ? '+'.trim($sort_by) : trim($sort_by);
		$sort = substr($sort_by, 1, strlen($sort_by));
		$sort .= (substr($sort_by, 0, 1) === '+') ? ' ASC' : ' DESC';

		return $sort;
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
				'data' => "<span data-sort=\"{$arr_param_campo['sort']}\" data-toggle=\"tooltip\" title=\"Ordenar por campo {$arr_param_campo['titulo']}\">{$arr_param_campo['titulo']}</span>{$arr_param_campo['img_orden']}",
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
			$data = '';
			if ($nombre_campo === $this->_get_campo_subtotal($arr_campos))
			{
				$data = strtoupper($tipo).' '.$nombre_subtotal;
			}
			elseif (in_array($arr_param_campo['tipo'], $arr_totales['campos']))
			{
				$data = $this->formato_reporte($arr_totales[$tipo][$nombre_campo], $arr_param_campo);
			}

			array_push($arr_linea_totales, array(
				'data' => "<strong>{$data}</strong>",
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