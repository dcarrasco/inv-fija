<?php
/**
 * INVENTARIO FIJA
 *
 * Aplicacion de conciliacion de inventario para la logistica fija.
 *
 * PHP version 7
 *
 * @category  CodeIgniter
 * @package   InventarioFija
 * @author    Daniel Carrasco <danielcarrasco17@gmail.com>
 * @copyright 2015 - DCR
 * @license   MIT License
 * @link      localhost:1520
 *
 */

namespace Stock;

use Reporte;
use Model\Orm_model;

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
class Stock_sap extends ORM_Model {

	use Reporte;

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
	public $rules_stock_sap = [
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

	/**
	 * Campos disponibles para el reporte
	 * @var array
	 */
	protected $campos = [
		'fecha_stock'   => ['titulo' => 'fecha stock', 'tipo' => 'fecha'],
		'tipo_almacen'  => ['titulo' => 'tipo almacen'],
		'centro'        => ['titulo' => 'centro'],
		'cod_almacen'   => ['titulo' => 'cod almacen'],
		'cod_bodega'    => ['titulo' => 'cod almacen'],
		'des_almacen'   => ['titulo' => 'des almacen'],
		'acreedor'      => ['titulo' => 'proveedor'],
		'des_proveedor' => ['titulo' => 'des proveedor'],
		'cod_articulo'  => ['titulo' => 'material'],
		'material'      => ['titulo' => 'material'],
		'descripcion'   => ['titulo' => 'desc material'],
		'lote'          => ['titulo' => 'lote'],
		'tipo_articulo' => ['titulo' => 'tipo articulo'],
		'LU'            => ['titulo' => 'cant LU', 'class' => 'text-right', 'tipo' => 'numero'],
		'BQ'            => ['titulo' => 'cant BQ', 'class' => 'text-right', 'tipo' => 'numero'],
		'CC'            => ['titulo' => 'cant CC', 'class' => 'text-right', 'tipo' => 'numero'],
		'TT'            => ['titulo' => 'cant TT', 'class' => 'text-right', 'tipo' => 'numero'],
		'OT'            => ['titulo' => 'cant OT', 'class' => 'text-right', 'tipo' => 'numero'],
		'VAL_LU'        => ['titulo' => 'valor LU', 'class' => 'text-right', 'tipo' => 'valor'],
		'VAL_BQ'        => ['titulo' => 'valor BQ', 'class' => 'text-right', 'tipo' => 'valor'],
		'VAL_CC'        => ['titulo' => 'valor CC', 'class' => 'text-right', 'tipo' => 'valor'],
		'VAL_TT'        => ['titulo' => 'valor TT', 'class' => 'text-right', 'tipo' => 'valor'],
		'VAL_OT'        => ['titulo' => 'valor OT', 'class' => 'text-right', 'tipo' => 'valor'],
		'total'         => ['titulo' => 'cant total', 'class' => 'text-right', 'tipo' => 'numero'],
		'VAL_total'     => ['titulo' => 'valor total', 'class' => 'text-right', 'tipo' => 'valor'],
		'estado'        => ['titulo' => 'estado'],
		'cantidad'      => ['titulo' => 'cantidad', 'class' => 'text-right', 'tipo' => 'numero'],
		'monto'         => ['titulo' => 'valor', 'class' => 'text-right', 'tipo' => 'valor'],
		'EQUIPOS'       => ['titulo' => 'cant equipos', 'class' => 'text-right', 'tipo' => 'numero'],
		'VAL_EQUIPOS'   => ['titulo' => 'valor equipos', 'class' => 'text-right', 'tipo' => 'valor'],
		'SIMCARD'       => ['titulo' => 'cant simcard', 'class' => 'text-right', 'tipo' => 'numero'],
		'VAL_SIMCARD'   => ['titulo' => 'valor simcard', 'class' => 'text-right', 'tipo' => 'valor'],
		'OTROS'         => ['titulo' => 'cant otros', 'class' => 'text-right', 'tipo' => 'numero'],
		'VAL_OTROS'     => ['titulo' => 'valor otros', 'class' => 'text-right', 'tipo' => 'valor'],
		'ventas_eq'     => ['titulo' => 'Ventas 28', 'class' => 'text-right', 'tipo' => 'numero'],
		'rotacion_eq'   => ['titulo' => 'DOI', 'class' => 'text-right', 'tipo' => 'doi'],
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
		// $this->datos_reporte = cached_query('mostrar_stock'.$this->tipo_op.serialize($mostrar).serialize($filtrar), $this, 'get_stock', [$mostrar, $filtrar]);
		$this->datos_reporte = $this->get_stock($mostrar, $filtrar);
		$this->campos_reporte = $this->campos_reporte_stock($mostrar);

		return $this->genera_reporte();
	}

	// --------------------------------------------------------------------

	/**
	 * Crea la tabla del reporte de stock
	 *
	 * @param  array $mostrar Campos a mostrar en el reporte
	 * @return string         HTML de la tabla del reporte
	 */
	protected function campos_reporte_stock($mostrar = [])
	{
		$mostrar = collect($mostrar);

		$campos = collect([])
			->merge($mostrar->contains('fecha') ? ['fecha_stock'] : [])
			->merge(($mostrar->contains('sel_tiposalm') && request('sel_tiposalm') !== 'sel_almacenes')
				? ['tipo_almacen']
				: []
			)
			->merge(($mostrar->contains('almacen') OR request('sel_tiposalm') === 'sel_almacenes')
				? ['centro', 'cod_bodega', 'des_almacen']
				: []
			)
			->merge($mostrar->contains('acreedor') ? ['acreedor', 'des_proveedor'] : [])
			->merge($mostrar->contains('material')
				? ($this->tipo_op === 'MOVIL' ? ['cod_articulo'] : ['material', 'descripcion'])
				: []
			)
			->merge($mostrar->contains('lote') ? ['lote'] : [])
			->merge($mostrar->contains('tipo_stock')
				? ($this->tipo_op === 'MOVIL'
					? ['tipo_articulo', 'LU', 'BQ', 'CC', 'TT', 'OT', 'VAL_LU', 'VAL_BQ', 'VAL_CC', 'VAL_TT', 'VAL_OT', 'total', 'VAL_total']
					: ['estado', 'cantidad', 'monto']
				)
				: ($this->tipo_op === 'MOVIL'
					? ($mostrar->contains('material')
						? ['total', 'VAL_total']
						: ['EQUIPOS', 'VAL_EQUIPOS', 'SIMCARD', 'VAL_SIMCARD', 'OTROS', 'VAL_OTROS'])
					: ['cantidad', 'monto']
				)
			)->merge(($mostrar->contains('material') && ! $mostrar->contains('tipo_stock')) ? ['ventas_eq', 'rotacion_eq'] : []);

		return $this->set_order_campos(collect($this->campos)->only($campos)->all(), 'fecha_stock');
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
	 * @return array           Fechas
	 */
	public function get_combo_fechas_db()
	{
		$fechas = $this->get_data_combo_fechas();
		$ultimo_dia = collect($fechas)
			->map(function($fecha, $llave) {
				return substr($llave, 0, 6);
			})->unique()
			->map(function($fecha, $llave) {
				return fmt_fecha($llave);
			})->all();

		return ['ultimodia' => $ultimo_dia, 'todas' => $fechas];
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
						->delete(config('bd_reporte_clasif'));
				}

				$result = $this->db
					->select('tipo_op, convert(varchar(20), fecha_stock, 102) fecha_stock, orden, clasificacion, tipo, color, cantidad, monto', FALSE)
					->from(config('bd_reporte_clasif'))
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

// actualiza PMP
// =================================
// UPDATE S
// SET
// 	S.VAL_LU = P.PMP_01*S.LIBRE_UTILIZACION,
// 	S.VAL_BQ = P.PMP_07*S.BLOQUEADO,
// 	S.VAL_CQ = P.PMP_02*S.CONTRO_CALIDAD,
// 	S.VAL_TT = P.PMP_06*S.TRANSITO_TRASLADO,
// 	S.VAL_OT = 0
// FROM
// STOCK_SCL S
// JOIN CP_PMP P ON S.CENTRO=P.CENTRO AND S.COD_ARTICULO=P.MATERIAL AND S.LOTE=P.LOTE
// WHERE S.FECHA_STOCK='20150331'




// SELECT *
// FROM
// STOCK_SCL S
// JOIN CP_PMP P ON S.CENTRO=P.CENTRO AND S.COD_ARTICULO=P.MATERIAL AND S.LOTE=P.LOTE
// WHERE S.FECHA_STOCK='20140930'




// REPORTE CLASIFICACION MOVIL
// =========================================
// SELECT
// A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
// SUM(E.LIBRE_UTILIZACION + E.BLOQUEADO + E.CONTRO_CALIDAD + E.TRANSITO_TRASLADO + E.OTROS) AS CANTIDAD,
// SUM(E.VAL_LU + E.VAL_BQ + E.VAL_CQ + E.VAL_TT + E.VAL_OT) AS MONTO
// FROM CP_CLASIFALM A
// JOIN CP_CLASIF_TIPOALM B ON A.ID_CLASIF=B.ID_CLASIF
// JOIN CP_TIPOSALM C ON B.ID_TIPO=C.ID_TIPO
// JOIN CP_TIPOS_ALMACENES D ON C.ID_TIPO=D.ID_TIPO
// JOIN STOCK_SCL E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.COD_BODEGA
// JOIN CP_TIPO_CLASIFALM F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
// WHERE A.TIPO_OP= ?
// AND E.FECHA_STOCK = ?
// GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION, F.TIPO, F.COLOR
// ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION



// SERIES ESTADO 01
// =========================================
// SELECT
// A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
// SUM(E.LIBRE_UTILIZACION + E.BLOQUEADO + E.CONTRO_CALIDAD + E.TRANSITO_TRASLADO + E.OTROS) AS CANTIDAD,
// SUM(E.VAL_LU + E.VAL_BQ + E.VAL_CQ + E.VAL_TT + E.VAL_OT) AS MONTO,
// SUM(E.LIBRE_UTILIZACION) AS CANTIDAD_01,
// SUM(E.VAL_LU) AS MONTO_01
// FROM CP_CLASIFALM A
// JOIN CP_CLASIF_TIPOALM B ON A.ID_CLASIF=B.ID_CLASIF
// JOIN CP_TIPOSALM C ON B.ID_TIPO=C.ID_TIPO
// JOIN CP_TIPOS_ALMACENES D ON C.ID_TIPO=D.ID_TIPO
// JOIN STOCK_SCL E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.COD_BODEGA
// JOIN CP_TIPO_CLASIFALM F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
// WHERE A.TIPO_OP= 'MOVIL'
// AND E.FECHA_STOCK IN ('20150429','20150331','20150228','20150131',
// '20141231','20141130','20141031','20140930','20140831','20140731','20140630','20140531','20140430','20140331','20140228','20140130')
// AND A.CLASIFICACION IN ('SUC', 'GMC APM WEB', 'CANALES REMOTOS', 'FQ')
// GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION, F.TIPO, F.COLOR
// ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION


}

// End of file stock_sap.php
// Location: ./models/Stock/stock_sap.php
