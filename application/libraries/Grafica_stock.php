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
 * Clase con functionalidades graficas
 * *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Grafica_stock {

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
	 * Devuelve arreglos con datos en formato para graficar
	 *
	 * @param  array $arr_stock Arreglo con datos de stock
	 * @param  array $arr_param Arreglo con parametreos
	 * @return array        Datos para construir graficos
	 */
	public function datos_grafico($arr_stock = [], $arr_param = [])
	{
		$arr_param_key = ['fecha_ultimodia', 'fecha_todas'];

		foreach ($arr_param_key as $parametro)
		{
			if ( ! array_key_exists($parametro, $arr_param))
			{
				$arr_param[$parametro] = [];
			}
		}

		$graph_q_equipos  = [];
		$graph_q_simcard  = [];
		$graph_q_otros    = [];
		$graph_v_equipos  = [];
		$graph_v_simcard  = [];
		$graph_v_otros    = [];
		$arr_eje_x        = [];
		$arr_label_series = [];

		foreach($arr_stock as $registro_stock)
		{
			// si seleccionamos mas de una fecha, entonces usamos la fecha en el eje_X
			if (count($arr_param['fecha_ultimodia'])>1 OR count($arr_param['fecha_todas'])>1)
			{
				$idx_eje_x = 'fecha_stock';
				$this->_array_push_unique($arr_eje_x, "'" . $registro_stock[$idx_eje_x] . "'");

				// si seleccionamos tipos de almacen, entonces usamos el tipo de almacen como las series
				if ($arr_param['sel_tiposalm'] === 'sel_tiposalm')
				{
					// si seleccionamos desplegar almacenes, usamos también el almacén como parte de la serie
					if (array_key_exists('almacen', $arr_param))
					{
						// si hay más de un tipo de almacén, componemos la serie con tipo_alm y almacen
						if (count($arr_param['tipo_alm']) > 1)
						{
							$idx_series = 'cod_almacen';
							$this->_array_push_unique($arr_label_series, '{label:\'' . $registro_stock['tipo_almacen'] . '/'. $registro_stock[$idx_series] . '-' . $registro_stock['des_almacen'] . '\'}');
						}
						// si hay solo un tipo de almacén, usamos sólo el codigo de almacen como la serie
						else
						{
							$idx_series = 'cod_almacen';
							$this->_array_push_unique($arr_label_series, '{label:\'' . $registro_stock[$idx_series] . '-' . $registro_stock['des_almacen'] . '\'}');
						}
					}
					// si no seleccionamos desplegar almacenes, usamos el tipo de almacén como la serie
					else
					{
						$idx_series = 'tipo_almacen';
						$this->_array_push_unique($arr_label_series, '{label:\'' . $registro_stock[$idx_series] . '\'}');
					}
				}
				elseif ($arr_param['sel_tiposalm'] === 'sel_almacenes')
				{
					$idx_series = 'cod_almacen';
					$this->_array_push_unique($arr_label_series, '{label:\'' . $registro_stock['centro'] . '-' . $registro_stock[$idx_series] . ' ' . $registro_stock['des_almacen'] . '\'}');
				}
			}
			// si seleccionamos sólo una fecha
			else
			{
				$idx_series = 'fecha_stock';
				$this->_array_push_unique($arr_label_series, '{label:\'' . $registro_stock[$idx_series] . '\'}');

				// si seleccionamos tipos de almacen, entonces usamos el tipo de almacen como las series
				if ($arr_param['sel_tiposalm'] === 'sel_tiposalm')
				{
					// si seleccionamos desplegar almacenes, usamos también el almacén como parte de la serie
					if ($arr_param['almacen'])
					{
						// si hay más de un tipo de almacén, componemos la serie con tipo_alm y almacen
						if (count($arr_param['tipo_alm']) > 1)
						{
							$idx_eje_x = 'cod_almacen';
							$this->_array_push_unique($arr_eje_x, '\'' . $registro_stock['tipo_almacen'] . '/'. $registro_stock[$idx_eje_x] . '-' . $registro_stock['des_almacen'] . '\'');
						}
						// si hay solo un tipo de almacén, usamos sólo el codigo de almacen como la serie
						else
						{
							$idx_eje_x = 'cod_almacen';
							$this->_array_push_unique($arr_eje_x, '\'' . $registro_stock[$idx_eje_x] . '-' . $registro_stock['des_almacen'] . '\'');
						}
					}
					// si no seleccionamos desplegar almacenes, usamos el tipo de almacén para el eje x
					else
					{
						$idx_eje_x = 'tipo_almacen';
						$this->_array_push_unique($arr_eje_x, '\'' . $registro_stock[$idx_eje_x] . '\'');
					}
				}
				elseif ($arr_param['sel_tiposalm'] === 'sel_almacenes')
				{
					$idx_eje_x = 'cod_almacen';
					$this->_array_push_unique($arr_eje_x, '\'' . $registro_stock[$idx_eje_x] . '-' . $registro_stock['des_almacen'] . '\'');
				}
			}

			$graph_q_equipos[$registro_stock[$idx_series]][$registro_stock[$idx_eje_x]] = $registro_stock['EQUIPOS'];
			$graph_v_equipos[$registro_stock[$idx_series]][$registro_stock[$idx_eje_x]] = $registro_stock['VAL_EQUIPOS']/1000000;

			$graph_q_simcard[$registro_stock[$idx_series]][$registro_stock[$idx_eje_x]] = $registro_stock['SIMCARD'];
			$graph_v_simcard[$registro_stock[$idx_series]][$registro_stock[$idx_eje_x]] = $registro_stock['VAL_SIMCARD']/1000000;

			$graph_q_otros[$registro_stock[$idx_series]][$registro_stock[$idx_eje_x]] = $registro_stock['OTROS'];
			$graph_v_otros[$registro_stock[$idx_series]][$registro_stock[$idx_eje_x]] = $registro_stock['VAL_OTROS']/1000000;
		}

		$arr_datos = [
			'serie_q_equipos'  => $this->_arr_series_to_string($graph_q_equipos),
			'serie_v_equipos'  => $this->_arr_series_to_string($graph_v_equipos),
			'serie_q_simcard'  => $this->_arr_series_to_string($graph_q_simcard),
			'serie_v_simcard'  => $this->_arr_series_to_string($graph_v_simcard),
			'serie_q_otros'    => $this->_arr_series_to_string($graph_q_otros),
			'serie_v_otros'    => $this->_arr_series_to_string($graph_v_otros),
			'str_eje_x'        => '[' . implode(',', $arr_eje_x) . ']',
			'str_label_series' => '[' . implode(',', $arr_label_series) . ']',
		];

		return $arr_datos;
	}


	// --------------------------------------------------------------------

	/**
	 * Ingresa un valor a un arreglo, solo si no existe anteriormente
	 * @param  array  $arreglo Arreglo a ingresar el valor
	 * @param  string $valor   Valor a agregar
	 * @return none
	 */
	private function _array_push_unique(&$arreglo, $valor = '')
	{
		if ( ! in_array($valor, $arreglo))
		{
			array_push($arreglo, $valor);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve un string json con el valor de un arreglo (para usar en javascript)
	 *
	 * @param  array $arreglo Arreglo a convertir
	 * @return string
	 */
	private function _arr_series_to_string($arreglo = [])
	{
		$arr_temp = [];
		foreach($arreglo as $arr_elem)
		{
			array_push($arr_temp, '[' . implode(',', $arr_elem) . ']');
		}
		return('[' . implode(',', $arr_temp) . ']');
	}



}
/* libraries grafica_stock.php */
/* Location: ./application/libraries/grafica_stock.php */
