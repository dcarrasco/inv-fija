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

use Stock\Clase_movimiento;

/**
 * Clase Modelo TOA Asignacion
 * *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Toa_asignacion extends CI_Model {

	/**
	 * Arreglo con validación formulario consumos
	 *
	 * @var array
	 */
	public $asignacion_validation = [
		['field' => 'sel_reporte', 'label' => 'Reporte',     'rules' => 'required'],
		['field' => 'fecha_desde', 'label' => 'Fecha desde', 'rules' => 'required'],
		['field' => 'fecha_hasta', 'label' => 'Fecha hasta', 'rules' => 'required'],
	];

	/**
	 * Tipos de reporte asignaciones
	 *
	 * @var array
	 */
	public $tipos_reporte_asignaciones = [
		'tecnicos'      => 'T&eacute;cnicos',
		'material'      => 'Materiales',
		'lote'          => 'Lotes',
		'lote-material' => 'Lotes y materiales',
		'detalle'       => 'Detalle todos los registros',
	];

	/**
	 * Movimientos validos de asignaciones TOA
	 *
	 * @var array
	 */
	public $movimientos_asignaciones = ['Z31', 'Z32'];

	/**
	 * Centros validos de consumos TOA
	 *
	 * @var array
	 */
	public $centros_asignacion = ['CH32', 'CH33'];

	/**
	 * Combo de unidades reporte control asignaciones
	 *
	 * @var array
	 */
	public $combo_unidades_asignacion = [
		'asignaciones' => 'Cantidad de asignaciones',
		'unidades'     => 'Suma de unidades',
		'monto'        => 'Suma de montos',
	];

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @return  void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('date');
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve las asignaciones realizados en TOA
	 *
	 * @param  string $tipo_reporte Tipo de reporte a desplegar
	 * @param  string $fecha_desde  Fecha de los datos del reporte
	 * @param  string $fecha_hasta  Fecha de los datos del reporte
	 * @param  string $orden_campo  Campo para ordenar el resultado
	 * @param  string $orden_tipo   Orden del resultado (ascendente o descendente)
	 * @return string               Reporte
	 */
	public function asignaciones_toa($tipo_reporte = 'tecnicos', $fecha_desde = NULL, $fecha_hasta = NULL, $orden_campo = '', $orden_tipo = 'ASC')
	{
		if ( ! $tipo_reporte OR ! $fecha_desde OR ! $fecha_hasta)
		{
			return '';
		}

		$arr_data = [];

		$this->db
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<=', $fecha_hasta)
			->where_in('a.codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('a.centro', $this->centros_asignacion)
			->where('a.signo','NEG');

		$orden_tipo = ($orden_tipo === '') ? 'ASC' : $orden_tipo;

		if ($tipo_reporte === 'detalle')
		{
			$orden_campo = empty($orden_campo) ? 'referencia' : $orden_campo;

			$arr_data = $this->db
				->select('a.fecha_contabilizacion as fecha')
				->select('a.referencia')
				->select('c.empresa')
				->select('a.cliente')
				->select('b.tecnico')
				->select('a.codigo_movimiento')
				->select('a.texto_movimiento')
				->select('a.elemento_pep')
				->select('a.documento_material')
				->select('a.centro')
				->select('a.material')
				->select('a.texto_material')
				->select('a.lote')
				->select('a.valor')
				->select('a.umb')
				->select('(-a.cantidad_en_um) as cant', FALSE)
				->select('(-a.importe_ml) as monto', FALSE)
				->select('a.usuario')
				->from(config('bd_movimientos_sap_fija').' a')
				->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				//->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = [];
			$arr_campos['fecha']              = ['titulo' => 'Fecha', 'tipo' => 'fecha'];
			$arr_campos['referencia']         = ['titulo' => 'Numero Peticion'];
			$arr_campos['empresa']            = ['titulo' => 'Empresa'];
			$arr_campos['cliente']            = ['titulo' => 'Cod Tecnico'];
			$arr_campos['tecnico']            = ['titulo' => 'Nombre Tecnico'];
			$arr_campos['codigo_movimiento']  = ['titulo' => 'Cod Movimiento'];
			$arr_campos['texto_movimiento']   = ['titulo' => 'Desc Movimiento'];
			$arr_campos['elemento_pep']       = ['titulo' => 'PEP'];
			$arr_campos['documento_material'] = ['titulo' => 'Documento SAP'];
			$arr_campos['centro']             = ['titulo' => 'Centro'];
			$arr_campos['material']           = ['titulo' => 'Cod material'];
			$arr_campos['texto_material']     = ['titulo' => 'Desc material'];
			$arr_campos['lote']               = ['titulo' => 'Lote'];
			$arr_campos['valor']              = ['titulo' => 'Valor'];
			$arr_campos['umb']                = ['titulo' => 'Unidad'];
			$arr_campos['cant']               = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']              = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['usuario']            = ['titulo' => 'Usuario SAP'];
			$this->reporte->set_order_campos($arr_campos, 'referencia');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'material')
		{
			$orden_campo = empty($orden_campo) ? 'material' : $orden_campo;

			$arr_data = $this->db
				->select('material')
				->select('texto_material')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('material')
				->group_by('texto_material')
				//->order_by($orden_campo, $orden_tipo)
				->from(config('bd_movimientos_sap_fija').' a')
				->get()->result_array();

			$arr_campos = [];
			$arr_campos['material']       = ['titulo' => 'Cod material'];
			$arr_campos['texto_material'] = ['titulo' => 'Desc material'];
			$arr_campos['cant']           = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']          = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link']     = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/material/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['material']];
			$this->reporte->set_order_campos($arr_campos, 'material');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'lote')
		{
			$orden_campo = empty($orden_campo) ? 'valor' : $orden_campo;

			$arr_data = $this->db
				->select('valor')
				->select('lote')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('valor')
				->group_by('lote')
				//->order_by($orden_campo, $orden_tipo)
				->from(config('bd_movimientos_sap_fija').' a')
				->get()->result_array();

			$arr_campos = [];
			$arr_campos['valor'] = ['titulo' => 'Valor'];
			$arr_campos['lote']  = ['titulo' => 'Lote'];
			$arr_campos['cant']  = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto'] = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link'] = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/lote/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['lote']];
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'lote-material')
		{
			$orden_campo = empty($orden_campo) ? 'valor' : $orden_campo;

			$arr_data = $this->db
				->select('valor')
				->select('lote')
				->select('material')
				->select('texto_material')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-cantidad_en_um)', 'cant')
				->select_sum('(-importe_ml)', 'monto')
				->group_by('valor')
				->group_by('lote')
				->group_by('material')
				->group_by('texto_material')
				//->order_by($orden_campo, $orden_tipo)
				->from(config('bd_movimientos_sap_fija').' a')
				->get()->result_array();

			$arr_campos = [];
			$arr_campos['valor']          = ['titulo' => 'Valor'];
			$arr_campos['lote']           = ['titulo' => 'Lote'];
			$arr_campos['material']       = ['titulo' => 'Cod material'];
			$arr_campos['texto_material'] = ['titulo' => 'Desc material'];
			$arr_campos['cant']           = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']          = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link']     = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/lote-material/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['lote','material']];
			$this->reporte->set_order_campos($arr_campos, 'valor');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
		elseif ($tipo_reporte === 'tecnicos')
		{
			$orden_campo = empty($orden_campo) ? 'empresa' : $orden_campo;

			$arr_data = $this->db
				->select('c.empresa')
				->select('a.cliente')
				->select('b.tecnico')
				->select("'ver asignaciones' as texto_link")
				->select_sum('(-a.cantidad_en_um)', 'cant')
				->select_sum('(-a.importe_ml)', 'monto')
				->group_by('c.empresa')
				->group_by('a.cliente')
				->group_by('b.tecnico')
				->from(config('bd_movimientos_sap_fija').' a')
				->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->join(config('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left')
				//->order_by($orden_campo, $orden_tipo)
				->get()->result_array();

			$arr_campos = [];
			$arr_campos['empresa'] = ['titulo' => 'Empresa'];
			$arr_campos['cliente'] = ['titulo' => 'Cod Tecnico'];
			$arr_campos['tecnico'] = ['titulo' => 'Nombre Tecnico'];
			$arr_campos['cant']    = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
			$arr_campos['monto']   = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
			$arr_campos['texto_link'] = ['titulo' => '', 'tipo' => 'link_registro', 'class' => 'text-right', 'href' => 'toa_asignaciones/ver_asignaciones/tecnicos/'.$fecha_desde.'/'.$fecha_hasta, 'href_registros' => ['cliente']];
			$this->reporte->set_order_campos($arr_campos, 'empresa');

			return $this->reporte->genera_reporte($arr_campos, $arr_data);
		}
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera peticiones dados argumentos de busqueda
	 *
	 * @param  string $tipo_reporte Tipo de reporte a buscar
	 * @param  string $param1       Primer parametro
	 * @param  string $param2       Segundo parametro
	 * @param  string $param3       Tercer parametro
	 * @param  string $param4       Cuarto parametro
	 * @return string               Reporte con las peticiones encontradas
	 */
	public function documentos_asignaciones_toa($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		if ( ! $tipo_reporte)
		{
			return '';
		}

		$arr_data = [];

		$this->db
			->where('a.fecha_contabilizacion>=', $param1)
			->where('a.fecha_contabilizacion<=', $param2)
			->where_in('a.codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('a.centro', $this->centros_asignacion)
			->where('a.signo', 'NEG');

		if ($tipo_reporte === 'material')
		{
			$this->db->where('a.material', $param3);
		}
		elseif ($tipo_reporte === 'lote')
		{
			$this->db->where('a.lote', $param3);
		}
		elseif ($tipo_reporte === 'lote-material')
		{
			$this->db->where('a.lote', $param3);
			$this->db->where('a.material', $param4);
		}
		elseif ($tipo_reporte === 'pep')
		{
			$this->db->where('a.codigo_movimiento', $param3);
			$this->db->where('a.elemento_pep', $param4);
		}
		elseif ($tipo_reporte === 'tecnicos')
		{
			$this->db->where('a.cliente', $param3);
		}

		$arr_data = $this->db
			->select('convert(varchar(20), a.fecha_contabilizacion, 112) as fecha')
			->select('a.documento_material')
			->select('c.empresa')
			->select('a.cliente')
			->select('b.tecnico')
			->select('a.centro')
			->select('a.almacen')
			->select('d.des_almacen')
			->select('a.material')
			->select('a.texto_material')
			->select('a.valor')
			->select('a.lote')
			->select("'ver detalle' as texto_link")
			->select_sum('(-a.cantidad_en_um)', 'cant')
			->select_sum('(-a.importe_ml)', 'monto')
			->group_by('a.fecha_contabilizacion')
			->group_by('a.documento_material')
			->group_by('c.empresa')
			->group_by('a.cliente')
			->group_by('b.tecnico')
			->group_by('a.centro')
			->group_by('a.almacen')
			->group_by('d.des_almacen')
			->group_by('a.material')
			->group_by('a.texto_material')
			->group_by('a.valor')
			->group_by('a.lote')
			->from(config('bd_movimientos_sap_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->join(config('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left')
			->order_by('a.documento_material', 'ASC')
			->get()->result_array();

		$arr_campos = [];
		$arr_campos['fecha']              = ['titulo' => 'Fecha', 'tipo' => 'fecha'];
		$arr_campos['documento_material'] = ['titulo' => 'Documento SAP'];
		$arr_campos['empresa']            = ['titulo' => 'Empresa'];
		$arr_campos['cliente']            = ['titulo' => 'Cod Tecnico'];
		$arr_campos['tecnico']            = ['titulo' => 'Nombre Tecnico'];
		$arr_campos['centro']             = ['titulo' => 'Centro'];
		$arr_campos['almacen']            = ['titulo' => 'Almac&eacute;n'];
		$arr_campos['des_almacen']        = ['titulo' => 'Desc Almac&eacute;n'];
		$arr_campos['material']           = ['titulo' => 'Material'];
		$arr_campos['texto_material']     = ['titulo' => 'Desc Material'];
		$arr_campos['lote']               = ['titulo' => 'Lote'];
		$arr_campos['valor']              = ['titulo' => 'Valor'];
		$arr_campos['cant']               = ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'];
		$arr_campos['monto']              = ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'];
		$arr_campos['texto_link']         = [
			'titulo'         => '',
			'class'          => 'text-right',
			'tipo'           => 'link_registro',
			'href'           => 'toa_asignaciones/detalle_asignacion',
			'href_registros' => ['fecha','documento_material']
		];
		$this->reporte->set_order_campos($arr_campos, 'documento_material');

		return $this->reporte->genera_reporte($arr_campos, $arr_data);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera datos de detalle de una peticion
	 *
	 * @param  string $fecha    Fecha de la peticion
	 * @param  string $peticion ID de la peticion
	 * @return array            Detalle de la peticion
	 */
	public function detalle_asignacion_toa($fecha = NULL, $peticion = NULL)
	{
		if ( ! $fecha OR ! $peticion)
		{
			return [];
		}

		return $this->db
			->select('a.fecha_contabilizacion as fecha')
			->select('a.referencia')
			->select('c.empresa')
			->select('a.cliente')
			->select('b.tecnico')
			->select('a.codigo_movimiento')
			->select('a.texto_movimiento')
			->select('a.elemento_pep')
			->select('a.documento_material')
			->select('a.centro')
			->select('a.almacen')
			->select('a.material')
			->select('a.texto_material')
			->select('a.lote')
			->select('a.valor')
			->select('a.umb')
			->select('(a.cantidad_en_um) as cant', FALSE)
			->select('(a.importe_ml) as monto', FALSE)
			->select('a.usuario')
			->from(config('bd_movimientos_sap_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			//->order_by($orden_campo, $orden_tipo)
			->where('fecha_contabilizacion', $fecha)
			->where('documento_material', $peticion)
			->where_in('codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('centro', $this->centros_asignacion)
			->order_by('material, cantidad_en_um')
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve matriz de asignaciones a técnicos por dia
	 *
	 * @param  string $empresa        Empresa a consultar
	 * @param  string $anomes         Mes a consultar, formato YYYYMM
	 * @param  string $filtro_trx     Filtro para revisar un codigo movimiento
	 * @param  string $dato_desplegar Tipo de dato a desplegar
	 * @return array                  Arreglo con los datos del reporte
	 */
	public function control_asignaciones($empresa = NULL, $anomes = NULL, $filtro_trx = NULL, $dato_desplegar = 'asignaciones')
	{
		if ( ! $empresa OR ! $anomes)
		{
			return NULL;
		}

		$arr_dias = get_arr_dias_mes($anomes);

		$fecha_desde = $anomes.'01';
		$fecha_hasta = get_fecha_hasta($anomes);

		if ($filtro_trx AND $filtro_trx !== '000')
		{
			$this->db->where('codigo_movimiento', $filtro_trx);
		}

		$this->db
			->select('a.fecha_contabilizacion as fecha')
			->select('a.cliente')
			->group_by('a.fecha_contabilizacion')
			->group_by('a.cliente')
			->where('a.fecha_contabilizacion>=', $fecha_desde)
			->where('a.fecha_contabilizacion<', $fecha_hasta)
			->where('b.id_empresa', $empresa)
			->where_in('codigo_movimiento', $this->movimientos_asignaciones)
			->where_in('centro', $this->centros_asignacion)
			->where('almacen', NULL)
			->from(config('bd_movimientos_sap_fija').' a')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE);

		if (in_array($dato_desplegar, ['unidades', 'monto']))
		{
			$this->db->select_sum($dato_desplegar === 'unidades' ? 'a.cantidad_en_um' : 'a.importe_ml', 'dato');
		}
		else
		{
			$this->db->select('count(*) as dato', FALSE);
		}

		$datos = $this->db->get()->result_array();

		$tecnicos = new Tecnico_toa();
		$arr_tecnicos = $tecnicos->find('all', ['conditions' => ['id_empresa' => $empresa]]);

		return $arr_tecnicos
			->map_with_keys(function($tecnico) use ($arr_dias, $datos) {
				$datos_tecnico = collect($datos)
					->filter(function($dato) use ($tecnico) {
						return $dato['cliente'] === $tecnico->id_tecnico;
					})
					->map_with_keys(function($dato) {
						return [substr($dato['fecha'], 8, 2) => $dato['dato']];
					});

				return [$tecnico->id_tecnico => [
					'nombre'       => $tecnico->tecnico,
					'rut'          => $tecnico->rut,
					'ciudad'       => (string) $tecnico->get_relation_object('id_ciudad'),
					'orden_ciudad' => (int) $tecnico->get_relation_object('id_ciudad')->orden,
					'actuaciones'  => collect($arr_dias)->merge($datos_tecnico)->all(),
				]];
			})
			->filter(function($tecnico) {
				return collect($tecnico['actuaciones'])->sum() !== 0;
			})
			->sort(function($tecnico1, $tecnico2) {
				return $tecnico1['orden_ciudad'] > $tecnico2['orden_ciudad'];
			});
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve combo con las clases de movimiento de asignacion
	 *
	 * @return array Clases de movimiento de asignacion
	 */
	public function get_combo_movimientos_asignacion()
	{
		$cmv = new Clase_movimiento;

		return array_merge(
			['000' => 'Todos los movimientos'],
			$cmv->find('list', [
				'conditions' => ['cmv' => $this->movimientos_asignaciones],
				'opc_ini'    => FALSE,
			])
		);
	}



}

/* End of file Toa_model.php */
/* Location: ./application/models/Toa_model.php */
