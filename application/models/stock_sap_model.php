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
 * Clase Modelo Stock SAP
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Stock_sap_model extends CI_Model {

	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();

		$this->load->helper('html');
		$this->load->library('table');
		$this->table->set_template(array(
			'table_open' => '<table class="table table-striped table-hover table-condensed reporte">',
		));
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock fijo o movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con campos a filtrar
	 * @return array          Arreglo con stock
	 */
	public function get_stock($mostrar = array(), $filtrar = array())
	{
		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Crea la tabla del reporte de stock
	 *
	 * @param  array $arr_result Resultado de la query de stock
	 * @param  array $mostrar    Campos a mostrar en el reporte
	 * @param  array $filtrar    Filtros de datos del reporte
	 * @return string            HTML de la tabla del reporte
	 */
	protected function format_table_stock($arr_result = array(), $mostrar = array(), $filtrar = array())
	{
		$campos_sumables = array('LU','BQ','CC','TT','OT','total','EQUIPOS','SIMCARD','OTROS','cantidad','VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','VAL_total','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS', 'VAL_cantidad', 'monto');
		$campos_montos   = array('VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','VAL_total','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS','VAL_cantidad', 'monto');
		$campos_fechas   = array('fecha_stock');
		$totales = array();

		$num_reg = 0;

		foreach ($arr_result as $linea_stock)
		{
			if ($num_reg === 0)
			{
				$arr_linea = array();
				foreach ($linea_stock as $campo => $valor)
				{
					array_push($arr_linea, array(
						'data'  => str_replace('_', ' ', $campo),
						'class' => in_array($campo, $campos_sumables) ? 'text-right' : '',
					));

					$totales[$campo] = 0;
				}
				$this->table->set_heading($arr_linea);
			}

			$arr_linea = array();
			foreach ($linea_stock as $campo => $valor)
			{
				if (in_array($campo, $campos_sumables))
				{
					if (array_key_exists('cod_almacen', $linea_stock))
					{
						$str_url = base_url(
							'stock_sap/detalle_series/' .
							(array_key_exists('centro', $linea_stock) ? $linea_stock['centro'] : '_') . '/' .
							(array_key_exists('cod_almacen', $linea_stock) ? $linea_stock['cod_almacen'] : '_') . '/' .
							(array_key_exists('cod_articulo', $linea_stock) ? $linea_stock['cod_articulo'] : '_') . '/' .
							(array_key_exists('lote', $linea_stock) ? $linea_stock['lote'] : '_')
						);
						$data = anchor($str_url, nbs().(in_array($campo, $campos_montos) ? fmt_monto($valor) : fmt_cantidad($valor)));
					}
					else
					{
						$data = in_array($campo, $campos_montos) ? fmt_monto($valor) : fmt_cantidad($valor);
					}

					$totales[$campo] += $valor;
				}
				else
				{
					$data = in_array($campo, $campos_fechas) ? fmt_fecha($valor) : $valor;
				}

				array_push($arr_linea, array(
					'data'  => $data,
					'class' => in_array($campo, $campos_sumables) ? 'text-right' : '',
				));
			}
			$this->table->add_row($arr_linea);
			$num_reg += 1;
		}

		$arr_linea = array();
		foreach($totales as $campo => $valor)
		{
			$data = '';

			if (in_array($campo, $campos_sumables))
			{
				$data = in_array($campo, $campos_montos) ? fmt_monto($valor) : fmt_cantidad($valor);
			}

			array_push($arr_linea, array(
				'data'  => '<strong>'.$data.'</strong>',
				'class' => in_array($campo, $campos_sumables) ? 'text-right' : '',
			));
		}
		$this->table->add_row($arr_linea);

		return $this->table->generate();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera stock en transito, operación fija y/o movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con filtros a aplicar
	 * @return array           Stock en transito
	 */
	public function get_stock_transito($mostrar = array(), $filtrar = array())
	{
		return '';
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera fechas para mostrar en combobox
	 *
	 * @param  string $tipo_op Indicador del tipo de operación
	 * @return array           Fechas
	 */
	public function get_combo_fechas($tipo_op = '')
	{
		$arr_fecha_tmp = $this->get_data_combo_fechas();
		$arr_fecha = array('ultimodia' => array(), 'todas' => $arr_fecha_tmp);

		$anno_mes_ant = '';
		foreach($arr_fecha_tmp as $llave => $valor)
		{
			if ($anno_mes_ant !== substr($llave, 0, 6))
			{
				$arr_fecha['ultimodia'][$llave] = $valor;
			}

			$anno_mes_ant = substr($llave, 0, 6);
		}

		return $arr_fecha;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera fechas de la operación movil para mostrar en combobox
	 *
	 * @return array Fechas
	 */
	public function get_data_combo_fechas()
	{
		return '';
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera el detalle de las series, dado un centro, almacén material y lote
	 *
	 * @param  string $centro   Centro series a recuperar
	 * @param  string $almacen  Almacén series a recuperar
	 * @param  string $material Material series a recuperar
	 * @param  string $lote     Lote series a recuperar
	 * @return string           Texto tabla formateada
	 */
	public function get_detalle_series($centro = '', $almacen = '', $material = '', $lote = '')
	{
		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Genera reporte de stock con clasificacion de almacens
	 *
	 * @param  string  $tipo_op      Indica el tipo de operación (MOVIL o FIJO)
	 * @param  array   $fechas       Fechas a proceosar
	 * @param  boolean $borrar_datos Indicador si el reporte se generar desde cero o se recupera
	 * @return array                 Arreglo con el reporte
	 */
	public function reporte_clasificacion($tipo_op = 'MOVIL', $fechas = NULL, $borrar_datos = FALSE)
	{
		$arr_result = array();

		if ($fechas)
		{
			foreach($fechas as $fecha)
			{
				if ($borrar_datos)
				{
					$this->db
						->where('tipo_op', $tipo_op)
						->where('fecha_stock', $fecha)
						->delete($this->config->item('bd_reporte_clasif'));
				}

				$result = $this->db
					->select('tipo_op, convert(varchar(20), fecha_stock, 102) fecha_stock, orden, clasificacion, tipo, color, cantidad, monto', FALSE)
					->from($this->config->item('bd_reporte_clasif'))
					->where('tipo_op', $tipo_op)
					->where('fecha_stock', $fecha)
					->order_by('tipo_op, fecha_stock, orden')
					->get()->result_array();

				if (count($result) === 0)
				{
					$this->_genera_reporte_clasificacion($tipo_op, $fecha);

					array_push($arr_result, $this->reporte_clasificacion($tipo_op, $fecha));
				}
				else
				{
					array_push($arr_result, $result);
				}
			}
		}

		// agrega registros en arreglo final
		$arr_final = array();
		$arr_fechas = array();
		foreach($arr_result as $result_fecha)
		{
			foreach ($result_fecha as $registro)
			{
				if ( ! in_array($registro['fecha_stock'], $arr_fechas))
				{
					array_push($arr_fechas, $registro['fecha_stock']);
				}

				if ( ! array_key_exists($registro['orden'], $arr_final))
				{
					$arr_final[$registro['orden']] = array();
				}

				$arr_final[$registro['orden']]['tipo_op'] = $registro['tipo_op'];
				$arr_final[$registro['orden']]['orden'] = $registro['orden'];
				$arr_final[$registro['orden']]['clasificacion'] = $registro['clasificacion'];
				$arr_final[$registro['orden']]['tipo'] = $registro['tipo'];
				$arr_final[$registro['orden']]['color'] = $registro['color'];
				$arr_final[$registro['orden']][$registro['fecha_stock']] = $registro['monto'];

			}
		}

		// corrige arreglo final, agregando valores =0 en aquellas llaves de fecha que no existan
		foreach($arr_final as $llave => $registro)
		{
			foreach($arr_fechas as $fecha)
			{
				if ( ! array_key_exists($fecha, $registro))
				{
					$arr_final[$llave][$fecha] = 0;
				}
			}
		}

		asort($arr_fechas);

		return array('datos' => $arr_final, 'fechas' => $arr_fechas);
	}

	// --------------------------------------------------------------------

	/**
	 * Inserta registros del reporte de stock por clasificacion en tabla resumen
	 *
	 * @param  string $fecha Fecha del reporte a generar
	 * @return boolean       Indicador de exito o fallo de la query
	 */
	private function _genera_reporte_clasificacion($fecha = NULL)
	{
		return NULL;
	}

/*
actualiza PMP
=================================
UPDATE S
SET
	S.VAL_LU = P.PMP_01*S.LIBRE_UTILIZACION,
	S.VAL_BQ = P.PMP_07*S.BLOQUEADO,
	S.VAL_CQ = P.PMP_02*S.CONTRO_CALIDAD,
	S.VAL_TT = P.PMP_06*S.TRANSITO_TRASLADO,
	S.VAL_OT = 0
FROM
STOCK_SCL S
JOIN CP_PMP P ON S.CENTRO=P.CENTRO AND S.COD_ARTICULO=P.MATERIAL AND S.LOTE=P.LOTE
WHERE S.FECHA_STOCK='20150331'




SELECT *
FROM
STOCK_SCL S
JOIN CP_PMP P ON S.CENTRO=P.CENTRO AND S.COD_ARTICULO=P.MATERIAL AND S.LOTE=P.LOTE
WHERE S.FECHA_STOCK='20140930'




REPORTE CLASIFICACION MOVIL
=========================================
SELECT
A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
SUM(E.LIBRE_UTILIZACION + E.BLOQUEADO + E.CONTRO_CALIDAD + E.TRANSITO_TRASLADO + E.OTROS) AS CANTIDAD,
SUM(E.VAL_LU + E.VAL_BQ + E.VAL_CQ + E.VAL_TT + E.VAL_OT) AS MONTO
FROM CP_CLASIFALM A
JOIN CP_CLASIF_TIPOALM B ON A.ID_CLASIF=B.ID_CLASIF
JOIN CP_TIPOSALM C ON B.ID_TIPO=C.ID_TIPO
JOIN CP_TIPOS_ALMACENES D ON C.ID_TIPO=D.ID_TIPO
JOIN STOCK_SCL E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.COD_BODEGA
JOIN CP_TIPO_CLASIFALM F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
WHERE A.TIPO_OP= ?
AND E.FECHA_STOCK = ?
GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION, F.TIPO, F.COLOR
ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION



SERIES ESTADO 01
=========================================
SELECT
A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
SUM(E.LIBRE_UTILIZACION + E.BLOQUEADO + E.CONTRO_CALIDAD + E.TRANSITO_TRASLADO + E.OTROS) AS CANTIDAD,
SUM(E.VAL_LU + E.VAL_BQ + E.VAL_CQ + E.VAL_TT + E.VAL_OT) AS MONTO,
SUM(E.LIBRE_UTILIZACION) AS CANTIDAD_01,
SUM(E.VAL_LU) AS MONTO_01
FROM CP_CLASIFALM A
JOIN CP_CLASIF_TIPOALM B ON A.ID_CLASIF=B.ID_CLASIF
JOIN CP_TIPOSALM C ON B.ID_TIPO=C.ID_TIPO
JOIN CP_TIPOS_ALMACENES D ON C.ID_TIPO=D.ID_TIPO
JOIN STOCK_SCL E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.COD_BODEGA
JOIN CP_TIPO_CLASIFALM F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
WHERE A.TIPO_OP= 'MOVIL'
AND E.FECHA_STOCK IN ('20150429','20150331','20150228','20150131',
'20141231','20141130','20141031','20140930','20140831','20140731','20140630','20140531','20140430','20140331','20140228','20140130')
AND A.CLASIFICACION IN ('SUC', 'GMC APM WEB', 'CANALES REMOTOS', 'FQ')
GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION, F.TIPO, F.COLOR
ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION

*/


}
/* End of file stock_sap_model.php */
/* Location: ./application/models/stock_sap_model.php */