<?php
namespace Stock;

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
class Stock_sap_movil extends Stock_sap {

	/**
	 * Indica el tipo de operación del stock (fijo o movil)
	 * @var string
	 */
	public $tipo_op = 'MOVIL';

	/**
	 * Constructor de la clase
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock operación movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con campos a filtrar
	 * @return array          Arreglo con stock
	 */
	public function get_stock($mostrar = [], $filtrar = [])
	{
		$mostrar = collect($mostrar);
		$filtrar = collect($filtrar);

		$result = $this->result_stock($mostrar, $filtrar);

		return $this->procesa_result_stock($result, $mostrar, $filtrar);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock operación movil desde la BD
	 *
	 * @param  array $mostrar Collection con campos a mostrar
	 * @param  array $filtrar Collection con campos a filtrar
	 * @return array
	 */
	protected function result_stock($mostrar, $filtrar)
	{
		$select_tipo_articulo = "
CASE
	WHEN (substring(cod_articulo,1,8)='PKGCLOTK' OR substring(cod_articulo,1,2)='TS') THEN 'SIMCARD'
	WHEN substring(cod_articulo, 1,2) in ('TM','TO','TC','PK','PO') THEN 'EQUIPOS'
	ELSE 'OTROS'
END";

		// SELECT ==============================================================
		// fecha stock
		$mostrar->contains('fecha') AND $this->db
			->select('s.fecha_stock')->group_by('s.fecha_stock')->order_by('s.fecha_stock');

		// tipo de almacen
		($filtrar->get('sel_tiposalm') === 'sel_tiposalm') AND $this->db
			->select('t.tipo as tipo_almacen')->group_by('t.tipo')->order_by('t.tipo');

		// almacenes
		if ($filtrar->get('sel_tiposalm') === 'sel_almacenes' OR $mostrar->contains('almacen'))
		{
			$this->db->select('a.centro')->group_by('a.centro')->order_by('a.centro');
			$this->db->select('s.cod_bodega')->group_by('s.cod_bodega')->order_by('s.cod_bodega');
			$this->db->select('a.des_almacen')->group_by('a.des_almacen')->order_by('a.des_almacen');
		}

		// lotes
		$mostrar->contains('lote') AND $this->db
			->select('s.lote')->group_by('s.lote')->order_by('lote');

		// tipos de articulos
		$mostrar->contains('tipo_articulo') AND $this->db
			->select("{$select_tipo_articulo} as tipo_articulo", FALSE)->group_by($select_tipo_articulo, FALSE);

		// materiales
		$mostrar->contains('material') AND $this->db->select('s.cod_articulo')->group_by('s.cod_articulo')->order_by('s.cod_articulo');

		// cantidades y tipos de stock
		$this->db->select(
			$mostrar->contains('tipo_stock')
				? 'sum(s.libre_utilizacion) as LU, sum(s.bloqueado) as BQ, sum(s.contro_calidad) as CC, sum(s.transito_traslado) as TT, sum(s.otros) as OT, sum(s.VAL_LU) as VAL_LU, sum(s.VAL_BQ) as VAL_BQ, sum(s.VAL_CQ) as VAL_CC, sum(s.VAL_TT) as VAL_TT, sum(s.VAL_OT) as VAL_OT'
				: 'sum(s.libre_utilizacion+s.bloqueado+s.contro_calidad+s.transito_traslado+s.otros) as total, sum(s.VAL_LU+s.VAL_BQ+s.VAL_CQ+s.VAL_TT+s.VAL_OT) as VAL_total'
			, FALSE);

		// TABLAS ==============================================================
		$this->db->from(config('bd_stock_movil').' s')
			->join(config('bd_almacenes_sap').' a', 'a.centro=s.centro and a.cod_almacen=s.cod_bodega', 'left');

		($filtrar->get('sel_tiposalm') === 'sel_tiposalm') AND $this->db
				->join(config('bd_tipoalmacen_sap').' ta', 's.centro=ta.centro and s.cod_bodega=ta.cod_almacen')
				->join(config('bd_tiposalm_sap').' t', 't.id_tipo = ta.id_tipo');

		// CONDICIONES =========================================================
		// fechas
		$filtrar->has('fecha') AND $this->db->where_in('s.fecha_stock', $filtrar->get('fecha'));

		// tipos de almacen
		$this->db
			->where_in(
				$filtrar->get('sel_tiposalm') === 'sel_tiposalm'
					? 't.id_tipo' : "s.centro+'{$this->separador_campos}'+s.cod_bodega",
				$filtrar->get('almacenes'))
			->where('s.origen', 'SAP');

		$filtro_tipo_articulo = [];

		if ($filtrar->has('tipo_articulo_equipos'))
		{
			$filtro_tipo_articulo[] = 'EQUIPOS';
		}

		if ($filtrar->has('tipo_articulo_simcard'))
		{
			$filtro_tipo_articulo[] = 'SIMCARD';
		}

		if ($filtrar->has('tipo_articulo_otros'))
		{
			$filtro_tipo_articulo[] = 'OTROS';
		}

		$this->db->where_in("CASE WHEN (substring(cod_articulo,1,8)='PKGCLOTK' OR substring(cod_articulo,1,2)='TS') THEN 'SIMCARD' WHEN substring(cod_articulo, 1,2) in ('TM','TO','TC','PK','PO') THEN 'EQUIPOS' ELSE 'OTROS' END", $filtro_tipo_articulo);


		return collect($this->db->get()->result_array());
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock operación movil
	 *
	 * @param  array $result  Collection con datos de stock
	 * @param  array $mostrar Collection con campos a mostrar
	 * @param  array $filtrar Collection con campos a filtrar
	 * @return array          Arreglo con stock
	 */
	protected function procesa_result_stock($result, $mostrar = [], $filtrar = [])
	{
		if ($mostrar->contains('material'))
		{
			return $result;
		}


		// agrega llave
		$result = collect($result)->map(function($registro) {
			$reg = $registro;
			unset($reg['tipo_articulo']);
			unset($reg['total']);
			unset($reg['VAL_total']);
			$registro['_llave_'] = collect($reg)->implode();

			return $registro;
		});

		// registro unicos
		$result_temp = collect($result)->map(function($registro) {
			unset($registro['tipo_articulo']);
			unset($registro['total']);
			unset($registro['VAL_total']);
			$registro['EQUIPOS']     = 0;
			$registro['VAL_EQUIPOS'] = 0;
			$registro['SIMCARD']     = 0;
			$registro['VAL_SIMCARD'] = 0;
			$registro['OTROS']       = 0;
			$registro['VAL_OTROS']   = 0;

			return $registro;
		})->unique();

		return $result_temp->map(function($registro) use ($result) {
			$regs = $result->filter(function($reg) use ($registro) {
				return $reg['_llave_'] === $registro['_llave_'];
			});

			$regs->each(function($reg) use(&$registro) {
				$registro[$reg['tipo_articulo']] = $reg['total'];
				$registro['VAL_'.$reg['tipo_articulo']] = $reg['VAL_total'];
			});
			return $registro;
		});
	}

	// --------------------------------------------------------------------

	public function get_ventas($fecha, $centro, $almacen)
	{
		$fecha_fin = $fecha;
		$fecha_ini = date_create($fecha)->sub(new \DateInterval('P28D'))->format('Ymd');
		$result = $this->db
			->select('tm.tipo_movimiento, m.ce, m.alm, m.codigo_sap, sum(m.cantidad*t.signo) as cant', FALSE)
			->from(config('bd_resmovimientos_sap').' m')
			->join(config('bd_tipos_movs_cmv').' t', 't.cmv=m.cmv')
			->join(config('bd_tipos_movimientos').' tm', 'tm.id=t.id_tipo_movimiento')
			->where('m.fecha >=', $fecha_ini)
			->where('m.fecha <=', $fecha_fin)
			->where('m.ce', $centro)
			->where('m.alm', $almacen)
			->group_by('tm.tipo_movimiento, m.ce, m.alm, m.codigo_sap')
			->get()
			->result_array();

		return collect($result);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera stock en transito, operación fija y/o movil
	 *
	 * @param  array $mostrar Arreglo con campos a mostrar
	 * @param  array $filtrar Arreglo con filtros a aplicar
	 * @return array          Stock en transito
	 */
	public function get_stock_transito($mostrar = [], $filtrar = [])
	{
		return '';
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera fechas de la operación movil para mostrar en combobox
	 *
	 * @return array Fechas
	 */
	public function get_data_combo_fechas()
	{
		$fechas = $this->db
			->select('fecha_stock')
			->order_by('fecha_stock', 'desc')
			->get(config('bd_stock_movil_fechas'))
			->result_array();

		$fechas = collect($fechas)->pluck('fecha_stock')
			->map(function($fecha) {
				return ['llave' => fmt_fecha_db($fecha), 'valor' => fmt_fecha($fecha)];
			})->all();

		return form_array_format($fechas);
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
		if ($almacen === '' OR $almacen === '_')
		{
			return '';
		}

		if ($centro !== '' AND $centro !== '_')
		{
			$this->db->where('s.centro', $centro);
		}

		if ($almacen !== '' AND $almacen !== '_')
		{
			$this->db->where('s.almacen', $almacen);
		}

		if ($material !== '' AND $material !== '_')
		{
			$this->db->where('s.material', $material);
		}

		if ($lote !== '' AND $lote !== '_')
		{
			$this->db->where('s.lote', $lote);
		}

		$this->db
			->select('row_number() over (order by s.material, s.lote, s.serie) as lin', FALSE)
			->select('convert(varchar(20), s.fecha_stock, 103) as fecha_stock')
			->select('s.centro')
			->select('s.almacen')
			->select('a.des_almacen')
			->select('s.material')
			->select('s.des_material')
			->select('s.lote')
			->select('s.serie')
			->select('s.estado_stock')
			->select('p.pmp')
			->select('convert(varchar(20), s.modificado_el, 103) as fecha_modificacion')
			->select('s.modificado_por')
			->select('u.nom_usuario')
			->from(config('bd_stock_seriado_sap') . ' as s')
			->join(config('bd_almacenes_sap') . ' as a', 'a.centro=s.centro and a.cod_almacen=s.almacen', 'left')
			->join(config('bd_usuarios_sap') . ' as u', 'u.usuario=s.modificado_por', 'left')
			->join(config('bd_pmp') . ' p', 'p.centro = s.centro and p.material=s.material and p.lote=s.lote and p.estado_stock=s.estado_stock', 'left')
			->order_by('s.material, s.lote, s.serie');

		return $this->table->generate($this->db->get());
	}

	// --------------------------------------------------------------------

	/**
	 * Inserta registros del reporte de stock por clasificacion en tabla resumen
	 *
	 * @param  string $fecha   Fecha del reporte a generar
	 * @return boolean         Indicador de exito o fallo de la query
	 */
	private function _genera_reporte_clasificacion($fecha = NULL)
	{
		$sql_query = "INSERT INTO " . config('bd_reporte_clasif') .
" SELECT
A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.CLASIFICACION, F.TIPO, F.COLOR,
SUM(E.LIBRE_UTILIZACION + E.BLOQUEADO + E.CONTRO_CALIDAD + E.TRANSITO_TRASLADO + E.OTROS) AS CANTIDAD,
SUM(E.VAL_LU + E.VAL_BQ + E.VAL_CQ + E.VAL_TT + E.VAL_OT) AS MONTO
FROM " . config('bd_clasifalm_sap')      . " A
JOIN " . config('bd_clasif_tipoalm_sap') . " B ON A.ID_CLASIF=B.ID_CLASIF
JOIN " . config('bd_tiposalm_sap')       . " C ON B.ID_TIPO=C.ID_TIPO
JOIN " . config('bd_tipoalmacen_sap')    . " D ON C.ID_TIPO=D.ID_TIPO
JOIN " . config('bd_stock_movil')        . " E ON D.CENTRO=E.CENTRO AND D.COD_ALMACEN=E.COD_BODEGA
JOIN " . config('bd_tipo_clasifalm_sap') . " F ON A.ID_TIPOCLASIF=F.ID_TIPOCLASIF
WHERE A.TIPO_OP= ?
AND E.FECHA_STOCK = ?
GROUP BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION, F.TIPO, F.COLOR
ORDER BY A.TIPO_OP, E.FECHA_STOCK, A.ORDEN, A.ID_CLASIF, A.CLASIFICACION";

		return $this->db->query($sql_query, [$tipo_op, $fecha]);

	}


}
/* End of file stock_sap_movil_model.php */
/* Location: ./application/models/stock_sap_movil_model.php */
