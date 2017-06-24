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
	 * Indica el tipo de operación del stock (fijo o movil)
	 * @var string
	 */
	public $tipo_op = '';

	/**
	 * Arreglo con validación formulario stock SAP
	 *
	 * @var array
	 */
	public $stock_sap_validation = [
		[
			'field' => 'fecha[]',
			'label' => 'Fechas',
			'rules' => 'required',
		],
		[
			'field' => 'almacenes[]',
			'label' => 'Almacenes',
			'rules' => 'required',
		],
	];

	// --------------------------------------------------------------------

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
		$this->table->set_template([
			'table_open' => '<table class="table table-striped table-hover table-condensed reporte">',
		]);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera stock fijo o movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con campos a filtrar
	 * @return array          Arreglo con stock
	 */
	public function get_stock($mostrar = [], $filtrar = [])
	{
		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera stock fijo o movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con campos a filtrar
	 * @return array          Arreglo con stock
	 */
	public function reporte($mostrar = [], $filtrar = [])
	{
		$arr_stock = cached_query('mostrar_stock'.$this->tipo_op.serialize($mostrar).serialize($filtrar), $this, 'get_stock', [$mostrar, $filtrar]);

		return $this->format_table_stock($arr_stock, $mostrar, $filtrar);
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
	protected function format_table_stock($arr_result = [], $mostrar = [], $filtrar = [])
	{
		if (count($arr_result) > 0)
		{
			$campos_resultado = $arr_result[0];
		}

		$arr_campos = [];
		if (in_array('fecha', $mostrar))
		{
			$arr_campos['fecha_stock'] = ['titulo' => 'fecha stock', 'tipo' => 'fecha'];
		}
		if (in_array('sel_tiposalm', $mostrar))
		{
			$arr_campos['tipo_almacen'] = ['titulo' => 'tipo almacen'];
		}
		if (in_array('almacen', $mostrar))
		{
			$arr_campos['centro'] = ['titulo' => 'centro'];
			$arr_campos['cod_almacen'] = ['titulo' => 'cod almacen'];
			$arr_campos['des_almacen'] = ['titulo' => 'des almacen'];
		}
		if (in_array('acreedor', $mostrar))
		{
			$arr_campos['acreedor'] = ['titulo' => 'proveedor'];
			$arr_campos['des_proveedor'] = ['titulo' => 'des proveedor'];
		}
		if (in_array('material', $mostrar))
		{
			if (array_key_exists('cod_articulo', $campos_resultado))
			{
				$arr_campos['cod_articulo'] = ['titulo' => 'material'];
			}
			else
			{
				$arr_campos['material'] = ['titulo' => 'material'];
				$arr_campos['descripcion'] = ['titulo' => 'desc material'];

			}
		}
		if (in_array('lote', $mostrar))
		{
			$arr_campos['lote'] = ['titulo' => 'lote'];
		}
		if (in_array('tipo_stock', $mostrar))
		{
			if (array_key_exists('LU', $campos_resultado))
			{
				$arr_campos['tipo_articulo'] = ['titulo' => 'tipo articulo'];
				$arr_campos['LU'] = ['titulo' => 'cant LU', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['BQ'] = ['titulo' => 'cant BQ', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['CC'] = ['titulo' => 'cant CC', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['TT'] = ['titulo' => 'cant TT', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['OT'] = ['titulo' => 'cant OT', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['VAL_LU'] = ['titulo' => 'valor LU', 'class' => 'text-right', 'tipo' => 'valor'];
				$arr_campos['VAL_BQ'] = ['titulo' => 'valor BQ', 'class' => 'text-right', 'tipo' => 'valor'];
				$arr_campos['VAL_CC'] = ['titulo' => 'valor CC', 'class' => 'text-right', 'tipo' => 'valor'];
				$arr_campos['VAL_TT'] = ['titulo' => 'valor TT', 'class' => 'text-right', 'tipo' => 'valor'];
				$arr_campos['VAL_OT'] = ['titulo' => 'valor OT', 'class' => 'text-right', 'tipo' => 'valor'];
				$arr_campos['total'] = ['titulo' => 'cant total', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['VAL_total'] = ['titulo' => 'valor total', 'class' => 'text-right', 'tipo' => 'valor'];
			}
			else
			{
				$arr_campos['estado'] = ['titulo' => 'estado'];
				$arr_campos['cantidad'] = ['titulo' => 'cantidad', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['monto'] = ['titulo' => 'valor', 'class' => 'text-right', 'tipo' => 'valor'];
			}
		}
		else
		{
			if (array_key_exists('EQUIPOS', $campos_resultado))
			{
				$arr_campos['EQUIPOS'] = ['titulo' => 'cant equipos', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['VAL_EQUIPOS'] = ['titulo' => 'valor equipos', 'class' => 'text-right', 'tipo' => 'valor'];
				$arr_campos['SIMCARD'] = ['titulo' => 'cant simcard', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['VAL_SIMCARD'] = ['titulo' => 'valor simcard', 'class' => 'text-right', 'tipo' => 'valor'];
				$arr_campos['OTROS'] = ['titulo' => 'cant otros', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['VAL_OTROS'] = ['titulo' => 'valor otros', 'class' => 'text-right', 'tipo' => 'valor'];
			}
			elseif (array_key_exists('cantidad', $campos_resultado))
			{
				$arr_campos['cantidad'] = ['titulo' => 'cantidad', 'class' => 'text-right', 'tipo' => 'numero'];
				$arr_campos['monto'] = ['titulo' => 'valor', 'class' => 'text-right', 'tipo' => 'valor'];
			}
		}
		$this->reporte->set_order_campos($arr_campos, 'fecha_stock');

		return $this->reporte->genera_reporte($arr_campos, $arr_result);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock en transito, operación fija y/o movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con filtros a aplicar
	 * @return array           Stock en transito
	 */
	public function get_stock_transito($mostrar = [], $filtrar = [])
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
	public function get_combo_fechas()
	{
		return cached_query('combo_fechas'.$this->tipo_op, $this, 'get_combo_fechas_db', []);
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera fechas para mostrar en combobox
	 *
	 * @param  string $tipo_op Indicador del tipo de operación
	 * @return array           Fechas
	 */
	public function get_combo_fechas_db()
	{
		$arr_fecha_tmp = $this->get_data_combo_fechas();
		$arr_fecha = ['ultimodia' => [], 'todas' => $arr_fecha_tmp];

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
		$arr_result = [];
		$fechas = is_array($fechas) ? $fechas : [$fechas];

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
		$arr_final = [];
		$arr_fechas = [];
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
					$arr_final[$registro['orden']] = [];
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

		return ['datos' => $arr_final, 'fechas' => $arr_fechas];
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
