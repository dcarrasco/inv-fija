<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Grafica_stock {

	public function __construct()
	{

	}

	public function datos_grafico($stock = array(), $param = array())
	{
		$graph_q_equipos  = array();
		$graph_q_simcard  = array();
		$graph_q_otros    = array();
		$graph_v_equipos  = array();
		$graph_v_simcard  = array();
		$graph_v_otros    = array();
		$arr_eje_x        = array();
		$arr_label_series = array();

		foreach($stock as $reg)
		{
			// si seleccionamos mas de una fecha, entonces usamos la fecha en el eje_X
			if (count($param['fecha_ultimodia'])>1 OR count($param['fecha_todas'])>1)
			{
				$idx_eje_x = 'fecha_stock';
				$this->_array_push_unique($arr_eje_x, "'" . $reg[$idx_eje_x] . "'");

				// si seleccionamos tipos de almacen, entonces usamos el tipo de almacen como las series
				if ($param['sel_tiposalm'] == 'sel_tiposalm')
				{
					// si seleccionamos desplegar almacenes, usamos también el almacén como parte de la serie
					if (array_key_exists('almacen', $param))
					{
						// si hay más de un tipo de almacén, componemos la serie con tipo_alm y almacen
						if (count($param['tipo_alm']) > 1)
						{
							$idx_series = 'cod_almacen';
							$this->_array_push_unique($arr_label_series, '{label:\'' . $reg['tipo_almacen'] . '/'. $reg[$idx_series] . '-' . $reg['des_almacen'] . '\'}');
						}
						// si hay solo un tipo de almacén, usamos sólo el codigo de almacen como la serie
						else
						{
							$idx_series = 'cod_almacen';
							$this->_array_push_unique($arr_label_series, '{label:\'' . $reg[$idx_series] . '-' . $reg['des_almacen'] . '\'}');
						}
					}
					// si no seleccionamos desplegar almacenes, usamos el tipo de almacén como la serie
					else
					{
						$idx_series = 'tipo_almacen';
						$this->_array_push_unique($arr_label_series, '{label:\'' . $reg[$idx_series] . '\'}');
					}
				}
				else if ($param['sel_tiposalm'] == 'sel_almacenes')
				{
					$idx_series = 'cod_almacen';
					$this->_array_push_unique($arr_label_series, '{label:\'' . $reg['centro'] . '-' . $reg[$idx_series] . ' ' . $reg['des_almacen'] . '\'}');
				}
			}
			// si seleccionamos sólo una fecha
			else
			{
				$idx_series = 'fecha_stock';
				$this->_array_push_unique($arr_label_series, '{label:\'' . $reg[$idx_series] . '\'}');

				// si seleccionamos tipos de almacen, entonces usamos el tipo de almacen como las series
				if ($param['sel_tiposalm'] == 'sel_tiposalm')
				{
					// si seleccionamos desplegar almacenes, usamos también el almacén como parte de la serie
					if ($param['almacen'])
					{
						// si hay más de un tipo de almacén, componemos la serie con tipo_alm y almacen
						if (count($param['tipo_alm']) > 1)
						{
							$idx_eje_x = 'cod_almacen';
							$this->_array_push_unique($arr_eje_x, '\'' . $reg['tipo_almacen'] . '/'. $reg[$idx_eje_x] . '-' . $reg['des_almacen'] . '\'');
						}
						// si hay solo un tipo de almacén, usamos sólo el codigo de almacen como la serie
						else
						{
							$idx_eje_x = 'cod_almacen';
							$this->_array_push_unique($arr_eje_x, '\'' . $reg[$idx_eje_x] . '-' . $reg['des_almacen'] . '\'');
						}
					}
					// si no seleccionamos desplegar almacenes, usamos el tipo de almacén para el eje x
					else
					{
						$idx_eje_x = 'tipo_almacen';
						$this->_array_push_unique($arr_eje_x, '\'' . $reg[$idx_eje_x] . '\'');
					}
				}
				else if ($param['sel_tiposalm'] == 'sel_almacenes')
				{
					$idx_eje_x = 'cod_almacen';
					$this->_array_push_unique($arr_eje_x, '\'' . $reg[$idx_eje_x] . '-' . $reg['des_almacen'] . '\'');
				}
			}

			$graph_q_equipos[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['EQUIPOS'];
			$graph_v_equipos[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['VAL_EQUIPOS']/1000000;

			$graph_q_simcard[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['SIMCARD'];
			$graph_v_simcard[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['VAL_SIMCARD']/1000000;

			$graph_q_otros[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['OTROS'];
			$graph_v_otros[$reg[$idx_series]][$reg[$idx_eje_x]] = $reg['VAL_OTROS']/1000000;
		}


		$arr_datos = array(
			'serie_q_equipos'  => $this->_arr_series_to_string($graph_q_equipos),
			'serie_v_equipos'  => $this->_arr_series_to_string($graph_v_equipos),
			'serie_q_simcard'  => $this->_arr_series_to_string($graph_q_simcard),
			'serie_v_simcard'  => $this->_arr_series_to_string($graph_v_simcard),
			'serie_q_otros'    => $this->_arr_series_to_string($graph_q_otros),
			'serie_v_otros'    => $this->_arr_series_to_string($graph_v_otros),
			'str_eje_x'        => '[' . implode(',', $arr_eje_x) . ']',
			'str_label_series' => '[' . implode(',', $arr_label_series) . ']',
		);

		return $arr_datos;
	}


	// --------------------------------------------------------------------

	/**
	 * Ingresa un valor a un arreglo, solo si no existe anteriormente
	 * @param  array $arr   Arreglo a ingresar el valor
	 * @param  string $valor Valor a agregar
	 * @return none
	 */
	private function _array_push_unique(&$arr, $valor = '')
	{
		if (!in_array($valor, $arr))
		{
			array_push($arr, $valor);
		}
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve un string json con el valor de un arreglo (para usar en javascript)
	 *
	 * @param  array $arr Arreglo a convertir
	 * @return string
	 */
	private function _arr_series_to_string($arr = array())
	{
		$arr_temp = array();
		foreach($arr as $arr_elem)
		{
			array_push($arr_temp, '[' . implode(',', $arr_elem) . ']');
		}
		return('[' . implode(',', $arr_temp) . ']');
	}



}

/* libraries grafica_stock.php */
/* Location: ./application/libraries/grafica_stock.php */