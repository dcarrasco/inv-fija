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
use Stock\Almacen_sap;
use Stock\Tipoalmacen_sap;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * Clase Modelo Proveedor
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Reporte_stock extends ORM_Model {

	use Reporte;

	/**
	 * Arreglo de reglas de validación reporte permanencia
	 *
	 * @var array
	 */
	public $rules_permanencia = [
		[
			'field' => 'tipo_alm[]',
			'label' => 'Almacenes',
			'rules' => 'trim|required'
		],
		[
			'field' => 'estado_sap[]',
			'label' => 'Estados Stock',
			'rules' => 'trim'
		],
		[
			'field' => 'tipo_mat[]',
			'label' => 'Tipos de material',
			'rules' => 'trim'
		],
		[
			'field' => 'tipo_op',
			'label' => 'Tipo de operacion',
			'rules' => 'trim|required'
		],
		[
			'field' => 'incl_almacen',
			'label' => 'Mostrar almacenes',
			'rules' => 'trim'
		],
		[
			'field' => 'incl_lotes',
			'label' => 'Mostrar lotes',
			'rules' => 'trim'
		],
		[
			'field' => 'incl_modelos',
			'label' => 'Mostrar modelos equipos',
			'rules' => 'trim'
		],
		[
			'field' => 'order_by',
			'label' => '',
			'rules' => 'trim'
		],
		[
			'field' => 'order_sort',
			'label' => '',
			'rules' => 'trim'
		],
	];

	/**
	 * Arreglo de reglas de validación reporte movhist
	 *
	 * @var array
	 */
	public $rules_movhist = [
		[
			'field' => 'tipo_fecha',
			'label' => '',
			'rules' => 'trim'
		],
		[
			'field' => 'tipo_alm',
			'label' => '',
			'rules' => 'trim'
		],
		[
			'field' => 'tipo_mat',
			'label' => '',
			'rules' => 'trim'
		],
		[
			'field' => 'tipo_cruce_alm',
			'label' => '',
			'rules' => 'trim'
		],
		[
			'field' => 'fechas[]',
			'label' => 'Fecha reporte',
			'rules' => 'trim|required'
		],
		[
			'field' => 'cmv[]',
			'label' => 'Movimientos',
			'rules' => 'trim|required'
		],
		[
			'field' => 'almacenes[]',
			'label' => 'Almacenes',
			'rules' => 'trim'
		],
		[
			'field' => 'materiales',
			'label' => 'Materiales',
			'rules' => 'trim'
		],
		[
			'field' => 'sort',
			'label' => 'Orden reporte',
			'rules' => 'trim'
		],
	];

	// --------------------------------------------------------------------

	/**
	 * Constructor de la clase
	 *
	 * @param  array $atributos Valores para inicializar el modelo
	 * @return void
	 */
	public function __construct($atributos = [])
	{
		parent::__construct($atributos);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve datos para poblar combobox de tipos de inventario
	 *
	 * @return array Arreglo con los tipos de inventiaro
	 */
	public function get_fecha_reporte()
	{
		return fmt_fecha($this->db
			->distinct()
			->select('fecha_stock')
			->get(config('bd_permanencia'))
			->row()
			->fecha_stock
		);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los estados SAP
	 *
	 * @return array Estados SAP
	 */
	public function combo_estado_sap()
	{
		return [
			'01' => '01 Libre Utilizacion',
			'02' => '02 Control de Calidad',
			'03' => '03 Devolucion Cliente',
			'06' => '06 Transito',
			'07' => '07 Bloqueado',
		];
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los tipos de almacén de consumo
	 *
	 * @return array Tipo de almacén consumo
	 */
	public function combo_tipo_alm_consumo()
	{
		$arr_rs = $this->db
			->distinct()
			->select('t.id_tipo as llave')
			->select('t.tipo as valor')
			->from(config('bd_tipoalmacen_sap') . ' ta')
			->join(config('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left')
			->like('t.tipo', '(CONSUMO)')
			->order_by('t.tipo')
			->get()
			->result_array();

		return form_array_format($arr_rs);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera tipos de materiales
	 *
	 * @return array Tipos de materiales
	 */
	public function combo_tipo_mat()
	{
		return [
			'EQUIPO'  => 'Equipos',
			'SIMCARD' => 'Simcard',
			'FIJA'    => 'Fija',
			'OTRO'    => 'Otros',
		];
	}

	// =====================================================================================================
	// REPORTES
	// =====================================================================================================


	/**
	 * Devuelve reporte por hojas
	 *
	 * @param  array $config Arreglo de configuración
	 * @return array         Arreglo con el detalle del reporte
	 */
	public function get_reporte_permanencia($config = [])
	{
		$arr_reporte = [];
		$param_ok = $config['filtros']['tipo_alm'];

		if ($param_ok)
		{
			$this->db
				->select('t.tipo, t.id_tipo')
				->select('sum(m030) as m030')
				->select('sum(m060) as m060')
				->select('sum(m090) as m090')
				->select('sum(m120) as m120')
				->select('sum(m180) as m180')
				->select('sum(m360) as m360')
				->select('sum(m720) as m720')
				->select('sum(mas720) as mas720')
				->select('sum(otro) as otro')
				->select("sum(total) as 'total'")
				->from(config('bd_permanencia') . ' as p')
				->join(config('bd_tipoalmacen_sap') . ' ta', 'ta.centro=p.centro and ta.cod_almacen=p.almacen', 'left')
				->join(config('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left')
				->group_by('t.tipo, t.id_tipo')
				->order_by($this->format_order_by($config['orden']));


			// FILTROS
			if (count($config['filtros']['tipo_alm']) > 0)
			{
				$this->db->where_in('t.id_tipo', $config['filtros']['tipo_alm']);
			}

			if (count($config['filtros']['estado_sap']) > 0 && is_array($config['filtros']['estado_sap']))
			{
				$this->db
					->where_in('p.estado_stock', $config['filtros']['estado_sap'])
					->select("case p.estado_stock when '01' then '01 Libre util' when '02' then '02 Control Calidad' when '03' then '03 Devol cliente' when '07' then '07 Bloqueado' when '06' then '06 Transito' else p.estado_stock end as estado_sap, p.estado_stock")
					->group_by('p.estado_stock');
			}

			if (count($config['filtros']['tipo_mat']) > 0 && is_array($config['filtros']['tipo_mat']))
			{
				$this->db
					->where_in('tipo_material', $config['filtros']['tipo_mat'])
					->select('tipo_material')
					->group_by('tipo_material');
			}

			// INFORMACION ADICIONAL
			if ($config['mostrar']['almacen'] === '1')
			{
				$this->db
					->select('p.centro, p.almacen, p.des_almacen')
					->group_by('p.centro, p.almacen, p.des_almacen');
			}

			if ($config['mostrar']['lote'] === '1')
			{
				$this->db
					->select('p.lote')
					->group_by('p.lote');
			}

			if ($config['mostrar']['modelos'] === '1')
			{
				$this->db
					->select('p.material')
					->select("p.material + ' ' + p.des_material as modelo")
					->group_by("p.material + ' ' + p.des_material")
					->group_by('p.material');
			}

			$arr_reporte = $this->db->get()->result_array();
		}

		$this->datos_reporte = $arr_reporte;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la configuraciob del reporte permanencia
	 *
	 * @return array Arreglo de configuracion
	 */
	public function get_config_reporte_permanencia()
	{
		return [
			'orden'   => empty(request('sort')) ? '+tipo' : request('sort'),
			'filtros' => [
				'tipo_alm'   => request('tipo_alm'),
				'estado_sap' => request('estado_sap'),
				'tipo_mat'   => request('tipo_mat'),
			],
			'mostrar' => [
				'almacen' => request('incl_almacen'),
				'lote'    => request('incl_lote'),
				'modelos' => request('incl_modelos'),
			],
		];
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos a usar en reporte de permanencia.
	 *
	 * @return array Arreglo de campos para reporte permanencia
	 */
	public function get_campos_reporte_permanencia()
	{
		$arr_campos = [];

		$arr_campos['tipo'] = ['titulo' => 'Tipo Almacen'];

		if (request('incl_almacen') === '1')
		{
			$arr_campos['centro'] = ['titulo' => 'Centro'];
			$arr_campos['almacen'] = ['titulo' => 'CodAlmacen'];
			$arr_campos['des_almacen'] = ['titulo' => 'Almacen'];
		}

		if (is_array(request('estado_sap')) && count(request('estado_sap')) > 0)
		{
			$arr_campos['estado_sap'] = ['titulo' => 'Estado Stock'];
		}

		if (request('incl_lote') === '1')
		{
			$arr_campos['lote'] = ['titulo' => 'Lote'];
		}

		if (is_array(request('tipo_mat')) && count(request('tipo_mat')) > 0)
		{
			$arr_campos['tipo_material'] = ['titulo' => 'Tipo Material'];
		}

		if (request('incl_modelos') === '1')
		{
			$arr_campos['modelo'] = ['titulo' => 'Modelo'];
		}

		$campos_datos = [
			'm030'   => '000-030',
			'm060'   => '031-060',
			'm090'   => '061-090',
			'm120'   => '091-120',
			'm180'   => '121-180',
			'm360'   => '181-360',
			'm720'   => '361-720',
			'mas720' => '+720',
			'otro'   => 'otro',
			'total'  => 'Total',
		];

		foreach ($campos_datos as $llave => $valor)
		{
			$arr_campos[$llave] = [
				'titulo' => $valor,
				'class'  => 'text-center',
				'tipo'   => 'link_detalle_series',
				'href'   => $this->router->class.'/detalle',
			];

		}

		$this->campos_reporte = $this->set_order_campos($arr_campos, 'tipo');
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por hojas
	 *
	 * @param  string $orden_campo  nombre del campo para ordenar el reporte
	 * @param  string $orden_tipo   indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  array  $tipo_alm     Tipos de almacen
	 * @param  array  $estado_sap   Estados de materiales SAP
	 * @param  string $incl_almacen indica si se incluiran los almacenes
	 * @param  string $incl_lote    indica si se incluiran los lotes
	 * @param  string $incl_estado  indica si se incluiran los estados
	 * @param  string $incl_modelos indica si se incluiran los modelos
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_perm_consumo($orden_campo = 't.tipo', $orden_tipo = 'ASC', $tipo_alm = [], $estado_sap = [], $incl_almacen = '0', $incl_lote = '0', $incl_estado = '0', $incl_modelos = '0')
	{

		$this->db->select('t.tipo');
		$this->db->select('count(case when s.dias <= 30                 then 1 else null end) as m030');
		$this->db->select('count(case when s.dias > 30 and s.dias<= 60  then 1 else null end) as m060');
		$this->db->select('count(case when s.dias > 60 and s.dias<= 90  then 1 else null end) as m090');
		$this->db->select('count(case when s.dias > 90 and s.dias<= 180 then 1 else null end) as m180');
		$this->db->select('count(case when s.dias > 180 then 1 else null end) as mas180');
		$this->db->select("count(1) as 'total'");
		$this->db->from(config('bd_permanencia_fija') . ' s');
		$this->db->join(config('bd_tipoalmacen_sap') . ' ta', 'ta.centro=s.centro and ta.cod_almacen=s.almacen', 'left');
		$this->db->join(config('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left');
		$this->db->group_by('t.tipo');
		$this->db->order_by($orden_campo, $orden_tipo);

		if (count($tipo_alm) > 0)
		{
			$this->db->where_in('t.id_tipo', $tipo_alm);
		}
		else
		{
			$this->db->where('t.id_tipo', -1);
		}

		if ($incl_almacen === '1')
		{
			$this->db->select('s.centro, s.almacen, a.des_almacen');
			$this->db->join(config('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.almacen=a.cod_almacen', 'left');
			$this->db->group_by('s.centro, s.almacen, a.des_almacen');
			$this->db->order_by('s.centro, s.almacen, a.des_almacen');
		}

		if ($incl_lote === '1')
		{
			$this->db->select('s.lote');
			$this->db->group_by('s.lote');
			$this->db->order_by('s.lote');
		}

		if ($incl_modelos === '1')
		{
			$this->db->select('s.material');
			$this->db->select('s.des_material');
			$this->db->group_by('s.material');
			$this->db->group_by('s.des_material');
			$this->db->order_by('s.material');
		}

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera campos de detalle de las series
	 *
	 * @return array            Arreglo con campos del detalle de las series
	 */
	public function get_campos_reporte_detalle_series()
	{
		$arr_campos = [
			'tipo'           => ['titulo' => 'Tipo Almacen'],
			'fecha_stock'    => ['titulo' => 'Fecha stock'],
			'centro'         => ['titulo' => 'Centro'],
			'almacen'        => ['titulo' => 'Almacen'],
			'des_almacen'    => ['titulo' => 'Desc Almacen'],
			'material'       => ['titulo' => 'Material'],
			'des_material'   => ['titulo' => 'Desc Material'],
			'lote'           => ['titulo' => 'Lote'],
			'estado_stock'   => ['titulo' => 'Estado stock'],
			'modificado_el'  => ['titulo' => 'Modificado el'],
			'serie'          => ['titulo' => 'Serie'],
			'pmp'            => ['titulo' => 'PMP'],
			'modificado_por' => ['titulo' => 'Modificado por'],
			'nom_usuario'    => ['titulo' => 'Nombre usuario'],
		];

		$this->campos_reporte = $this->set_order_campos($arr_campos, 'tipo');
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera el detalle de las series
	 *
	 * @param  array $arr_param Arreglo con parámtero de la consulta
	 * @return array            Arreglo con el detalle de las series
	 */
	public function get_detalle_series($arr_param)
	{
		$this->db->select('t.tipo');
		$this->db->select('s.fecha_stock');
		$this->db->select('s.centro');
		$this->db->select('s.almacen');
		$this->db->select('a.des_almacen');
		$this->db->select('s.material');
		$this->db->select('s.des_material');
		$this->db->select('s.lote');
		$this->db->select('s.estado_stock');
		$this->db->select('s.modificado_el');
		$this->db->select('s.serie');
		$this->db->select('0 as pmp');
		$this->db->select('s.modificado_el as fecha_modificacion');
		$this->db->select('s.modificado_por');
		$this->db->select('u.nom_usuario');

		$this->db->from(config('bd_stock_seriado_sap') . ' s');
		$this->db->join(config('bd_tipoalmacen_sap') . ' ta', 'ta.centro=s.centro and ta.cod_almacen=s.almacen', 'left');
		$this->db->join(config('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left');
		$this->db->join(config('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.almacen=a.cod_almacen', 'left');
		$this->db->join(config('bd_usuarios_sap') . ' u', 'u.usuario=s.modificado_por', 'left');
		$this->db->order_by('t.tipo');
		$this->db->order_by('s.centro');
		$this->db->order_by('s.almacen');
		$this->db->order_by('s.material');

		if (array_key_exists('id_tipo', $arr_param) && $arr_param['id_tipo'] !== '')
		{
			$this->db->where('t.id_tipo', $arr_param['id_tipo']);
		}

		if (array_key_exists('centro', $arr_param) && $arr_param['centro'] !== '')
		{
			$this->db->where('s.centro', $arr_param['centro']);
		}

		if (array_key_exists('almacen', $arr_param) && $arr_param['almacen'] !== '')
		{
			$this->db->where('s.almacen', $arr_param['almacen']);
		}

		if (array_key_exists('estado_stock', $arr_param) && $arr_param['estado_stock'] !== '')
		{
			$this->db->where('s.estado_stock', $arr_param['estado_stock']);
		}

		if (array_key_exists('lote', $arr_param) && $arr_param['lote'] !== '')
		{
			$this->db->where('s.lote', $arr_param['lote']);
		}

		if (array_key_exists('material', $arr_param) && $arr_param['material'] !== '')
		{
			$this->db->where('s.material', $arr_param['material']);
		}

		if (array_key_exists('tipo_material', $arr_param) && $arr_param['tipo_material'] === 'EQUIPO')
		{
			$this->db->where_in('substring(s.material,1,2)', ['TM', 'TC', 'TO', 'PK', 'PO']);
			$this->db->where('substring(s.material,1,8) <>', 'PKGCLOTK');
		}
		elseif (array_key_exists('tipo_material', $arr_param) && $arr_param['tipo_material'] === 'SIMCARD')
		{
			$this->db->where("(substring(s.material,1,2) = 'TS' OR substring(s.material,1,8)='PKGCLOTK')");
		}
		elseif (array_key_exists('tipo_material', $arr_param) && $arr_param['tipo_material'] === 'FIJA')
		{
			$this->db->where('substring(s.material,1,3)', '103');
		}

		if (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'm030')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 30);
		}
		elseif (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'm060')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 31);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 60);
		}
		elseif (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'm090')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 61);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 90);
		}
		elseif (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'm120')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 91);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 120);
		}
		elseif (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'm180')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 121);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 180);
		}
		elseif (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'm360')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 181);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 360);
		}
		elseif (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'm720')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 361);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 720);
		}
		elseif (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'mas720')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 721);
		}
		elseif (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'otro')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) IS NULL');
		}
		elseif (array_key_exists('permanencia', $arr_param) && $arr_param['permanencia'] === 'total')
		{
		}

		$this->datos_reporte = $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los datos de stock para mostrar treemap de permanencia
	 *
	 * @param  string $centro        Centro a deplegar
	 * @param  string $tipo_material Tipo de material a desplegar
	 * @return array                Arreglo con los datos de stock
	 */
	public function get_treemap_permanencia($centro = 'CL03', $tipo_material = 'EQUIPO')
	{
		return $this->db
			->select('t.tipo')
			->select("s.almacen + ' - ' + s.des_almacen as alm", FALSE)
			->select("substring(s.material,6,2) + ' (' + s.almacen + ')' as marca", FALSE)
			->select("substring(s.material,6,7) + ' (' + s.almacen + ')' as modelo", FALSE)
			->select_sum('s.total', 'cant')
			->select_sum('convert(FLOAT, s.total)*(case when v.pmp IS NULL then 0 else v.pmp END)/1000000', 'valor', FALSE)
			->select_sum('(s.m030*15 + s.m060*45 + s.m090*75 + s.m120*115 + s.m180*150 + s.m360*270 + s.m720*540 + s.mas720*720)', 'perm', FALSE)
			->from(config('bd_permanencia') . ' as s')
			->join(config('bd_tipoalmacen_sap') . ' ta', 'ta.centro=s.centro and ta.cod_almacen=s.almacen', 'left')
			->join(config('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left')
			->join(config('bd_pmp') . ' v', 's.centro=v.centro and s.material=v.material and s.lote=v.lote and s.estado_stock=v.estado_stock', 'left')
			->where('s.centro', $centro)
			->where('s.almacen is not NULL')
			->where('s.tipo_material', $tipo_material)
			->group_by('t.tipo')
			->group_by("s.almacen + ' - ' + s.des_almacen")
			->group_by("substring(s.material,6,2) + ' (' + s.almacen + ')'")
			->group_by("substring(s.material,6,7) + ' (' + s.almacen + ')'")
			->order_by('t.tipo')
			->order_by("s.almacen + ' - ' + s.des_almacen")
			->order_by("substring(s.material,6,2) + ' (' + s.almacen + ')'")
			->order_by("substring(s.material,6,7) + ' (' + s.almacen + ')'")
			->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Transforma un arreglo en string necesario para ser usado por treemap
	 *
	 * @param  string $tipo        Tipo de dato a desplegar
	 * @param  array  $arr_treemap Arreglo de datos a desplegar
	 * @param  array  $arr_nodos   Arreglo de nodos
	 * @param  string $campo_size  Tamaño del campo
	 * @param  string $campo_color Color del campo
	 * @return string              Arreglo en formato javascript
	 */
	public function arr_query2treemap($tipo = 'cantidad', $arr_treemap = [], $arr_nodos = [], $campo_size = '', $campo_color = '')
	{
		$tipo = ($tipo === 'cantidad') ? 'cantidad' : 'valor';
		$cant_niveles = count($arr_nodos);
		$padre = 'TOTAL';

		$datos = collect([["'TOTAL',null", 0, 0]])
			->merge(collect($arr_nodos)
				->map(function($campo, $indice) use (&$padre, $arr_treemap, $campo_size, $campo_color, $cant_niveles) {
					$campo_padre  = $padre;
					$padre        = $campo;
					$ultimo_nivel = ($indice + 1 === $cant_niveles);

					return collect($arr_treemap)
						->map(function($dato) use ($campo, $campo_padre, $campo_size, $campo_color, $ultimo_nivel) {
							return [
								"'".$dato[$campo]."'",
								$campo_padre === 'TOTAL' ? "'TOTAL'" : "'".$dato[$campo_padre]."'",
								$ultimo_nivel ? (int) $dato[$campo_size] : 0,
								$ultimo_nivel ? $dato[$campo_color] : 0,
							];
						})
						->unique();
				})->flatten(1)
			)
			->map(function($dato) {
				return '[' . collect($dato)->implode(',') . ']';
			})
			->implode(',');

		return ("[['Item','Padre','".$tipo."','Antiguedad'],\n{$datos}]");
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera datos para desplegar combo tipos de fecha
	 * Al filtrar por un tipo de fecha (ej Trimestre), se filtraran
	 * los tipos de fecha mayores (en este caso, Año y Trimestre)
	 *
	 * @param  string $tipo_fecha Tipo de fecha a filtrar
	 *
	 * @return array             Arreglo con tipos de fechas
	 */
	public function get_combo_tipo_fecha($tipo_fecha = '')
	{
		$arr_tipo_fechas = [
			'ANNO'      => 'Año',
			'TRIMESTRE' => 'Trimestre',
			'MES'       => 'Meses',
			'DIA'       => 'Dias'
		];

		if ($tipo_fecha === 'ANNO')
		{
			unset($arr_tipo_fechas['ANNO']);
		}
		elseif ($tipo_fecha === 'TRIMESTRE')
		{
			unset($arr_tipo_fechas['ANNO']);
			unset($arr_tipo_fechas['TRIMESTRE']);
		}
		elseif ($tipo_fecha === 'MES')
		{
			unset($arr_tipo_fechas['ANNO']);
			unset($arr_tipo_fechas['TRIMESTRE']);
			unset($arr_tipo_fechas['MES']);
		}

		return $arr_tipo_fechas;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera fechas para poblar combo
	 *
	 * @param  string $tipo_fecha Tipo de fechas a recuperar (año, trimestre, mes, ...)
	 * @param  string $filtro     Filtro a utilizar
	 * @return array              Datos fechas
	 */
	public function get_combo_fechas($tipo_fecha = 'ANNO', $filtro = '')
	{
		$arr_filtro = explode($this->separador_campos, urldecode($filtro));

		if ($tipo_fecha === 'ANNO')
		{
			$this->db->distinct()
				->select('anno as llave')
				->select('anno as valor')
				->from(config('bd_fechas_sap'))
				->order_by('anno ASC');
		}
		elseif ($tipo_fecha === 'TRIMESTRE')
		{
			$this->db->distinct()
				->select("cast(anno as varchar(10)) + '-' + trimestre as llave")
				->select("cast(anno as varchar(10)) + '-' + trimestre as valor")
				->from(config('bd_fechas_sap'))
				->order_by("cast(anno as varchar(10)) + '-' + trimestre ASC");

			if ($filtro !== '')
			{
				$this->db->where_in('anno', $arr_filtro);
			}
		}
		elseif ($tipo_fecha === 'MES')
		{
			$this->db->distinct()
				->select('anomes as llave')
				->select('anomes as valor')
				->from(config('bd_fechas_sap'))
				->order_by('anomes ASC');

			if ($filtro !== '')
			{
				$this->db->where_in("cast(anno as varchar(10)) + '-' + trimestre", $arr_filtro);
			}
		}
		elseif ($tipo_fecha === 'DIA')
		{
			$this->db->distinct()
				->select('fecha as llave')
				->select('fecha as valor')
				->from(config('bd_fechas_sap'))
				->order_by('fecha', 'ASC');

			if ($filtro !== '')
			{
				$this->db->where_in('anomes', $arr_filtro);
			}
		}

		$arr_combo = $this->db->get()->result_array();

		return form_array_format($arr_combo);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera clases de movimientos para poblar combobox
	 *
	 * @return array Clases de movimientos
	 */
	public function get_combo_cmv()
	{
		$arr_combo = $this->db
			->select('cmv as llave')
			->select("cmv + ' ' + des_cmv as valor")
			->from(config('bd_cmv_sap'))
			->order_by('cmv ASC')
			->get()->result_array();

		return form_array_format($arr_combo);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera tipos de almacén para poblar combobox
	 *
	 * @param  string $tipo_alm Tipos de almacén (fija/movil, almacenes/tipos de almacenes)
	 * @return array            Almacenes
	 */
	public function get_combo_tipo_alm($tipo_alm = '')
	{
		$arr_tipos_almacen = [
			'MOVIL-TIPOALM' => 'Móvil - Tipos Almacén',
			'MOVIL-ALM'     => 'Móvil - Almacenes',
			'FIJA-TIPOALM'  => 'Fija - Tipos Almacén',
			'FIJA-ALM'      => 'Fija - Almacenes',
		];

		if ($tipo_alm === 'MOVIL-TIPOALM')
		{
			unset($arr_tipos_almacen['MOVIL-TIPOALM']);
			unset($arr_tipos_almacen['FIJA-TIPOALM']);
			unset($arr_tipos_almacen['FIJA-ALM']);
		}
		elseif ($tipo_alm === 'FIJA-TIPOALM')
		{
			unset($arr_tipos_almacen['MOVIL-TIPOALM']);
			unset($arr_tipos_almacen['MOVIL-ALM']);
			unset($arr_tipos_almacen['FIJA-TIPOALM']);
		}

		return $arr_tipos_almacen;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera tipos de almacenes para poblar combobox
	 *
	 * @param  string $tipo   Tipo de almacén a recuperar
	 * @param  string $filtro Filtro a utilizar
	 * @return array          Almacenes
	 */
	public function get_combo_almacenes($tipo = 'MOVIL-TIPOALM', $filtro = '')
	{
		$param = explode('-', $tipo);

		return ($param[1] === 'TIPOALM') ?
			Tipoalmacen_sap::create()->get_combo_tiposalm($param[0]) :
			Almacen_sap::create()->get_combo_almacenes($param[0], $filtro);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera tipos de material para poblar combobox
	 *
	 * @param  string $tipo_mat Tipo de material a recuperar
	 * @return array           Tipos de material
	 */
	public function get_combo_tipo_mat($tipo_mat = '')
	{
		$arr_tipos_material = [
			'TIPO'     => 'Tipo',
			'MARCA'    => 'Marca',
			'MODELO'   => 'Modelo',
			'MATERIAL' => 'Material',
		];

		if ($tipo_mat === 'TIPO')
		{
			unset($arr_tipos_material['TIPO']);
		}
		elseif ($tipo_mat === 'MARCA')
		{
			unset($arr_tipos_material['TIPO']);
			unset($arr_tipos_material['MARCA']);
		}
		elseif ($tipo_mat === 'MODELO')
		{
			unset($arr_tipos_material['TIPO']);
			unset($arr_tipos_material['MARCA']);
			unset($arr_tipos_material['MODELO']);
		}

		return $arr_tipos_material;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera materiales para poblar combobox
	 *
	 * @param  string $tipo   Tipo de material a recuperar
	 * @param  string $filtro Filtro a utilizar
	 * @return array          Materiales
	 */
	public function get_combo_materiales($tipo = 'TIPO', $filtro = '')
	{
		$arr_filtro = explode($this->separador_campos, urldecode($filtro));

		if ($tipo === 'TIPO')
		{
			$this->db
				->select('tipo1 as llave')
				->select('tipo1 as valor')
				->from(config('bd_materiales2_sap'))
				->order_by('tipo1 ASC');
		}
		elseif ($tipo === 'MARCA')
		{
			$this->db
				->select('sub_marca as llave')
				->select("sub_marca + ' '+ des_marca as valor")
				->from(config('bd_materiales2_sap'))
				->order_by('des_marca ASC');

			if ($filtro !== '')
			{
				$this->db->where_in('tipo1', $arr_filtro);
			}
		}
		elseif ($tipo === 'MODELO')
		{
			$this->db
				->select('sub_marcamodelo as llave')
				->select('sub_marcamodelo as valor')
				->from(config('bd_materiales2_sap'))
				->order_by('sub_marcamodelo ASC');

			if ($filtro !== '')
			{
				$this->db->where_in('sub_marca', $arr_filtro);
			}
		}
		elseif ($tipo === 'MATERIAL')
		{
			$arr_combo = $this->db
				->select('codigo_sap as llave')
				->select("codigo_sap + ' ' + descripcion as valor")
				->from(config('bd_materiales2_sap'))
				->order_by('codigo_sap ASC');

			if ($filtro !== '')
			{
				$this->db->where_in('sub_marcamodelo', $arr_filtro);
			}
		}

		$arr_combo = $this->db->get()->result_array();

		return form_array_format($arr_combo);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos del reporte movhist
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_movhist()
	{
		$arr_campos = [];

		$arr_campos['fecha']        = ['titulo' => 'Fecha'];
		$arr_campos['cmv']          = ['titulo' => 'CMov', 'class' => 'text-center'];
		$arr_campos['ce']           = ['titulo' => 'Centro', 'class' => 'text-center'];
		$arr_campos['alm']          = ['titulo' => 'Almacen', 'class' => 'text-center'];
		$arr_campos['rec']          = ['titulo' => 'Dest', 'class' => 'text-center'];
		$arr_campos['n_doc']        = ['titulo' => 'Num doc'];
		$arr_campos['ref']          = ['titulo' => 'Ref'];
		$arr_campos['codigo_sap']   = ['titulo' => 'Codigo SAP'];
		$arr_campos['des_material'] = ['titulo' => 'Desc material'];
		$arr_campos['lote']         = ['titulo' => 'Lote'];
		$arr_campos['cantidad']     = ['titulo' => 'Cantidad', 'class' => 'text-right', 'tipo' => 'numero'];
		$arr_campos['usuario']      = ['titulo' => 'Usuario'];
		$arr_campos['nom_usuario']  = ['titulo' => 'Nom usuario'];

		$this->campos_reporte = $this->set_order_campos($arr_campos, 'fecha');
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera datos reporte historial de movimientos
	 *
	 * @param  array $config Arreglo de configuración del reporte
	 * @return array         Reporte
	 */
	public function get_reporte_movhist($config = [])
	{

		$arr_filtros = array_key_exists('filtros', $config) ? $config['filtros'] : [];
		$order_by    = array_key_exists('orden', $config) ? $config['orden'] : '';
		$order_by    = empty($order_by) ? '+fecha' : $order_by;

		$param_ok = $arr_filtros['fechas'] && $arr_filtros['cmv'] && $arr_filtros['almacenes'] && $arr_filtros['materiales'] && $arr_filtros['tipo_alm'];

		$tipo_op = in_array($arr_filtros['tipo_alm'], ['MOVIL-TIPOALM', 'MOVIL-ALM']) ? 'MOVIL' : 'FIJA';

		$mov_fecha  = ($tipo_op === 'MOVIL') ? 'fecha' : 'fecha_contabilizacion';
		$mov_cmv    = ($tipo_op === 'MOVIL') ? 'cmv' : 'codigo_movimiento';
		$mov_mat    = ($tipo_op === 'MOVIL') ? 'codigo_sap' : 'material';
		$mov_desmat = ($tipo_op === 'MOVIL') ? 'texto_breve_material' : 'texto_material';
		$mov_centro = ($tipo_op === 'MOVIL') ? 'ce' : 'centro';
		$mov_alm    = ($tipo_op === 'MOVIL') ? 'alm' : 'almacen';
		$mov_rec    = ($tipo_op === 'MOVIL') ? 'rec' : 'proveedor';
		$mov_ndoc   = ($tipo_op === 'MOVIL') ? 'n_doc' : 'documento_material';
		$mov_ref    = ($tipo_op === 'MOVIL') ? 'referencia' : 'referencia';
		$mov_cant   = ($tipo_op === 'MOVIL') ? 'cantidad' : 'cantidad';
		$tabla_mat  = ($tipo_op === 'MOVIL') ? 'bd_materiales2_sap' : 'bd_catalogos';
		$mov_mat2   = ($tipo_op === 'MOVIL') ? 'codigo_sap' : 'catalogo';
		$mov_user   = ($tipo_op === 'MOVIL') ? 'usuario' : 'usuario';

		$arr_reporte = [];

		$arr_filtro_fechas = [
			'ANNO'      => 'f.anno',
			'TRIMESTRE' => "cast(f.anno as varchar(10)) + '/' + trimestre",
			'MES'       => 'f.anomes',
			'DIA'       => 'f.fecha',
		];

		if ($arr_filtros['tipo_cruce_alm'] === 'alm')
		{
			$arr_filtro_almacenes = [
				'MOVIL-TIPOALM' => 't.id_tipo',
				'MOVIL-ALM'     => "m.{$mov_centro}+'{$this->separador_campos}'+m.{$mov_alm}",
				'FIJA-TIPOALM'  => 't.id_tipo',
				'FIJA-ALM'      => "m.{$mov_centro}+'{$this->separador_campos}'+m.{$mov_alm}",
			];
		}
		else
		{
			$arr_filtro_almacenes = [
				'MOVIL-TIPOALM' => 't.id_tipo',
				'MOVIL-ALM'     => "m.{$mov_centro}+'-'+m.rec",
				'FIJA-TIPOALM'  => 't.id_tipo',
				'FIJA-ALM'      => "m.{$mov_centro}+'-'+m.rec",
			];
		}

		$arr_filtro_materiales = [
			'TIPO'     => 'mat.tipo1',
			'MARCA'    => 'mat.sub_marca',
			'MODELO'   => 'mat.sub_marcamodelo',
			'MATERIAL' => 'm.codigo_sap',
		];


		if ($param_ok)
		{
			$this->db->limit(5000);
			$this->db->select('f.fecha2 as fecha');
			$this->db->select('m.'.$mov_cmv.' as cmv');
			$this->db->select('m.'.$mov_centro.' as ce');
			$this->db->select('m.'.$mov_alm.' as alm');
			$this->db->select('m.'.$mov_rec.' as rec');
			$this->db->select('m.'.$mov_ndoc.' as n_doc');
			$this->db->select('m.'.$mov_ref.' as ref');
			$this->db->select('m.lote as lote');
			$this->db->select('m.'.$mov_mat.' as codigo_sap');
			$this->db->select('m.'.$mov_desmat.' as des_material');
			$this->db->select('m.'.$mov_cant.' as cantidad');
			$this->db->select('m.'.$mov_user.' as usuario');
			$this->db->select('u.nom_usuario as nom_usuario');


			if ($tipo_op === 'MOVIL')
			{
				$this->db->from(config('bd_resmovimientos_sap') . ' as m');
			}
			else
			{
				$this->db->from(config('bd_movimientos_sap_fija') . ' as m');
			}


			$this->db->join(config('bd_fechas_sap').' as f', 'm.'.$mov_fecha.'=f.fecha', 'LEFT');
			$this->db->join(config('bd_cmv_sap').' as c', 'm.'.$mov_cmv.'=c.cmv', 'LEFT');
			$this->db->join(config('bd_usuarios_sap').' as u', 'm.'.$mov_user.'=u.usuario', 'LEFT');
			$this->db->join(config($tabla_mat) . ' as mat', 'm.'.$mov_mat.'=mat.'.$mov_mat2, 'LEFT', FALSE);

			if ($arr_filtros['tipo_cruce_alm'] === 'alm')
			{
				$this->db->join(config('bd_almacenes_sap').' as a', 'm.'.$mov_centro.'=a.centro and m.'.$mov_alm.'=a.cod_almacen', 'LEFT');
				$this->db->join(config('bd_tipoalmacen_sap').' as t', 'm.'.$mov_centro.'=t.centro and m.'.$mov_alm.'=t.cod_almacen', 'LEFT');
			}
			else
			{
				$this->db->join(config('bd_almacenes_sap').' as a', 'm.'.$mov_centro.'=a.centro and m.'.$mov_rec.'=a.cod_almacen', 'LEFT');
				$this->db->join(config('bd_tipoalmacen_sap').' as t', 'm.'.$mov_centro.'=t.centro and m.'.$mov_rec.'=t.cod_almacen', 'LEFT');
			}


			$this->db->where_in($arr_filtro_fechas[$arr_filtros['tipo_fecha']], $arr_filtros['fechas']);
			$this->db->where_in('m.'.$mov_cmv.'', $arr_filtros['cmv']);
			$this->db->where_in($arr_filtro_almacenes[$arr_filtros['tipo_alm']], $arr_filtros['almacenes']);
			$this->db->where_in($arr_filtro_materiales[$arr_filtros['tipo_mat']], $arr_filtros['materiales']);
			$this->db->order_by($this->format_order_by($order_by));

			$arr_reporte = $this->db->get()->result_array();
		}

		$this->datos_reporte = $arr_reporte;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera almacenes para reporte estado 03
	 *
	 * @param  boolean $garantia_cliente Indicador para generar reporte con aquellos estados 03 menores a 10 dias
	 * @return array                     Arreglo con almacenes
	 */
	public function get_almacenes_est03($garantia_cliente = TRUE)
	{
		if ($garantia_cliente)
		{
			$this->db->where('dif_03_eng >=', 0);
			$this->db->where('dif_03_eng <=', 10);
		}

		return $this->db
			->distinct()
			->select("centro + '-' + almacen + ' ' + des_almacen as almacen", FALSE)
			->from(config('bd_stock_seriado_sap_03'))
			->order_by('almacen')
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera marcas de equipos para reporte estado 03
	 *
	 * @param  boolean $garantia_cliente Indicador para generar reporte con aquellos estados 03 menores a 10 dias
	 * @return array                     Arreglo con marcas de equipos
	 */
	public function get_marcas_est03($garantia_cliente = TRUE)
	{
		if ($garantia_cliente)
		{
			$this->db->where('dif_03_eng >=', 0);
			$this->db->where('dif_03_eng <=', 10);
		}

		return $this->db
			->distinct()
			->select('substring(material, 6, 2) as marca', FALSE)
			->from(config('bd_stock_seriado_sap_03'))
			->order_by('marca')
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Genera reportes de cantidad de equipos en estado 03
	 *
	 * @param  boolean $garantia_cliente Indicador para generar reporte con aquellos estados 03 menores a 10 dias
	 * @return array                     Reporte
	 */
	public function get_stock_est03($garantia_cliente = TRUE)
	{
		if ($garantia_cliente)
		{
			$this->db->where('dif_03_eng >=', 0);
			$this->db->where('dif_03_eng <=', 10);
		}

		$this->db
			->select("centro + '-' + almacen + ' ' + des_almacen as almacen", FALSE)
			->select('substring(material, 6, 2) as marca', FALSE)
			->select('count(*) as cantidad', FALSE)
			->from(config('bd_stock_seriado_sap_03'))
			->group_by("centro + '-' + almacen + ' ' + des_almacen, substring(material, 6, 2)", FALSE)
			->order_by('almacen, marca');

		$return_array = [];
		foreach($this->db->get()->result_array() as $registro)
		{
			$return_array[$registro['almacen']][$registro['marca']] = $registro['cantidad'];
		}

		return $return_array;
	}

}

// End of file Reporte_stock.php
// Location: ./models/Stock/Reporte_stock.php
