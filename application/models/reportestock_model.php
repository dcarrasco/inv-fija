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
 * Clase Modelo Proveedor
 *
 * @category CodeIgniter
 * @package  ACL
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Reportestock_model extends CI_Model {

	/**
	 * Arreglo de reglas de validación reporte permanencia
	 *
	 * @var array
	 */
	public $permanencia_validation = array(
		array(
			'field' => 'tipo_alm[]',
			'label' => 'Almacenes',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'estado_sap[]',
			'label' => 'Estados Stock',
			'rules' => 'trim'
		),
		array(
			'field' => 'tipo_mat[]',
			'label' => 'Tipos de material',
			'rules' => 'trim'
		),
		array(
			'field' => 'tipo_op',
			'label' => 'Tipo de operacion',
			'rules' => 'trim|required'
		),
		array(
			'field' => 'incl_almacen',
			'label' => 'Mostrar almacenes',
			'rules' => 'trim'
		),
		array(
			'field' => 'incl_lotes',
			'label' => 'Mostrar lotes',
			'rules' => 'trim'
		),
		array(
			'field' => 'incl_modelos',
			'label' => 'Mostrar modelos equipos',
			'rules' => 'trim'
		),
		array(
			'field' => 'order_by',
			'label' => '',
			'rules' => 'trim'
		),
		array(
			'field' => 'order_sort',
			'label' => '',
			'rules' => 'trim'
		),
	);


	// --------------------------------------------------------------------

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
	 * Devuelve datos para poblar combobox de tipos de inventario
	 *
	 * @return array Arreglo con los tipos de inventiaro
	 */
	public function get_fecha_reporte()
	{
		return $this->db
			->distinct()
			->select('convert(varchar(20), fecha_stock, 102) as fecha_stock', FALSE)
			->get($this->config->item('bd_permanencia'))
			->row()
			->fecha_stock;
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los tipos de almacén segun el tipo de operación
	 *
	 * @param  string $tipo_op Tipo de operacion (fija o movil)
	 * @return array          Arreglo de almacenes
	 */
	public function combo_tipo_alm($tipo_op = '')
	{
		$arr_rs = $this->db
			->distinct()
			->select('t.id_tipo as llave')
			->select('t.tipo as valor')
			->from($this->config->item('bd_tiposalm_sap') . ' t')
			->where('t.tipo_op', $tipo_op)
			->order_by('t.tipo')
			->get()
			->result_array();

		return form_array_format($arr_rs);
	}


	// --------------------------------------------------------------------

	/**
	 * Recupera los estados SAP
	 *
	 * @return array Estados SAP
	 */
	public function combo_estado_sap()
	{
		return array(
			'01' => '01 Libre Utilizacion',
			'02' => '02 Control de Calidad',
			'03' => '03 Devolucion Cliente',
			'06' => '06 Transito',
			'07' => '07 Bloqueado',
		);
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
			->from($this->config->item('bd_tipoalmacen_sap') . ' ta')
			->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left')
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
		return array(
				'EQUIPO'  => 'Equipos',
				'SIMCARD' => 'Simcard',
				'FIJA'    => 'Fija',
				'OTRO'    => 'Otros',
			);
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
	public function get_reporte_permanencia($config = array())
	{
		//dbg($config);
		$arr_reporte = array();
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
				->select('sum(total) as \'total\'')
				->from($this->config->item('bd_permanencia') . ' as p')
				->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'ta.centro=p.centro and ta.cod_almacen=p.almacen', 'left')
				->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left')
				->group_by('t.tipo, t.id_tipo')
				->order_by($config['orden']['campo'], $config['orden']['tipo']);


			// FILTROS
			if (count($config['filtros']['tipo_alm']) > 0)
			{
				$this->db->where_in('t.id_tipo', $config['filtros']['tipo_alm']);
			}

			if (count($config['filtros']['estado_sap']) > 0 AND is_array($config['filtros']['estado_sap']))
			{
				$this->db
					->where_in('p.estado_stock', $config['filtros']['estado_sap'])
					->select("case p.estado_stock when '01' then '01 Libre util' when '02' then '02 Control Calidad' when '03' then '03 Devol cliente' when '07' then '07 Bloqueado' when '06' then '06 Transito' else p.estado_stock end as estado_sap, p.estado_stock")
					->group_by('p.estado_stock');
			}

			if (count($config['filtros']['tipo_mat']) > 0 AND is_array($config['filtros']['tipo_mat']))
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

		return $arr_reporte;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve la configuraciob del reporte permanencia
	 *
	 * @return array Arreglo de configuracion
	 */
	public function get_config_reporte_permanencia()
	{
		return array(
			'orden' => array(
				'campo' => (set_value('order_by') === '') ? 'tipo' : set_value('order_by'),
				'tipo'  => set_value('order_sort'),
			),
			'filtros' => array(
				'tipo_alm'   => set_value('tipo_alm'),
				'estado_sap' => set_value('estado_sap'),
				'tipo_mat'   => set_value('tipo_mat'),
			),
			'mostrar' => array(
				'almacen' => set_value('incl_almacen'),
				'lote'    => set_value('incl_lote'),
				'modelos' => set_value('incl_modelos'),
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos a usar en reporte de permanencia.
	 *
	 * @return array Arreglo de campos para reporte permanencia
	 */
	public function get_campos_reporte_permanencia()
	{
		$arr_campos = array();

		$arr_campos['tipo'] = array('titulo' => 'Tipo Almacen','class' => '', 'tipo' => 'texto');

		if (set_value('incl_almacen') === '1')
		{
			$arr_campos['centro'] = array('titulo' => 'Centro','class' => '', 'tipo' => 'texto');
			$arr_campos['almacen'] = array('titulo' => 'CodAlmacen','class' => '', 'tipo' => 'texto');
			$arr_campos['des_almacen'] = array('titulo' => 'Almacen','class' => '', 'tipo' => 'texto');
		}

		if (is_array(set_value('estado_sap')) AND count(set_value('estado_sap')) > 0)
		{
			$arr_campos['estado_sap'] = array('titulo' => 'Estado Stock','class' => '', 'tipo' => 'texto');
		}

		if (set_value('incl_lote') === '1')
		{
			$arr_campos['lote'] = array('titulo' => 'Lote','class' => '', 'tipo' => 'texto');
		}

		if (is_array(set_value('tipo_mat')) AND count(set_value('tipo_mat')) > 0)
		{
			$arr_campos['tipo_material'] = array('titulo' => 'Tipo Material','class' => '', 'tipo' => 'texto');
		}

		if (set_value('incl_modelos') === '1')
		{
			$arr_campos['modelo'] = array('titulo' => 'Modelo','class' => '', 'tipo' => 'texto');
		}

		$campos_datos = array(
			'm030' => '000-030',
			'm060' => '031-060',
			'm090' => '061-090',
			'm120' => '091-120',
			'm180' => '121-180',
			'm360' => '181-360',
			'm720' => '361-720',
			'mas720' => '+720',
			'otro' => 'otro',
			'total' => 'Total',
		);

		foreach ($campos_datos as $llave => $valor)
		{
			$arr_campos[$llave] = array(
				'titulo' => $valor,
				'class'  => 'text-center',
				'tipo'   => 'link_detalle_series',
				'href'   => $this->router->class . '/detalle',
			);

		}

		$new_orden_tipo  = (set_value('order_sort') === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by']  = $campo === set_value('order_by', 'tipo') ? $new_orden_tipo : 'ASC';
			$arr_campos[$campo]['img_orden'] = $campo === set_value('order_by', 'tipo')
				? ' <span class="text-muted glyphicon ' .
					(($arr_campos[$campo]['order_by'] === 'ASC') ? 'glyphicon-circle-arrow-up' : 'glyphicon-circle-arrow-down') .
					'" ></span>'
				: '';
		}

		return $arr_campos;
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
	public function get_reporte_perm_consumo($orden_campo = 't.tipo', $orden_tipo = 'ASC', $tipo_alm = array(), $estado_sap = array(), $incl_almacen = '0', $incl_lote = '0', $incl_estado = '0', $incl_modelos = '0')
	{

		$this->db->select('t.tipo');
		$this->db->select('count(case when s.dias <= 30                 then 1 else null end) as m030');
		$this->db->select('count(case when s.dias > 30 and s.dias<= 60  then 1 else null end) as m060');
		$this->db->select('count(case when s.dias > 60 and s.dias<= 90  then 1 else null end) as m090');
		$this->db->select('count(case when s.dias > 90 and s.dias<= 180 then 1 else null end) as m180');
		$this->db->select('count(case when s.dias > 180 then 1 else null end) as mas180');
		$this->db->select("count(1) as 'total'");
		$this->db->from($this->config->item('bd_permanencia_fija') . ' s');
		$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'ta.centro=s.centro and ta.cod_almacen=s.almacen', 'left');
		$this->db->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left');
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
			$this->db->join($this->config->item('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.almacen=a.cod_almacen', 'left');
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
	 * Recupera el detalle de las series
	 *
	 * @param  string $tipo_alm      Tipo de almacen a recuperar
	 * @param  string $centro        Centro
	 * @param  string $almacen       Almacén a recuperar
	 * @param  string $estado_stock  Estado a recuperar
	 * @param  string $lote          Lote a recuperar
	 * @param  string $material      Material a recuperar
	 * @param  string $tipo_material Tipo de material a recuperar
	 * @param  string $permanencia   Permanencia a recuperar
	 * @return array                 Arreglo con el detalle de las series
	 */
	public function get_detalle_series($tipo_alm = '', $centro = '', $almacen = '', $estado_stock = '', $lote = '', $material = '', $tipo_material = '', $permanencia = '')
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

		$this->db->from($this->config->item('bd_stock_seriado_sap') . ' s');
		$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'ta.centro=s.centro and ta.cod_almacen=s.almacen', 'left');
		$this->db->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left');
		$this->db->join($this->config->item('bd_almacenes_sap') . ' a', 's.centro=a.centro and s.almacen=a.cod_almacen', 'left');
		$this->db->join($this->config->item('bd_usuarios_sap') . ' u', 'u.usuario=s.modificado_por', 'left');
		$this->db->order_by('t.tipo');
		$this->db->order_by('s.centro');
		$this->db->order_by('s.almacen');
		$this->db->order_by('s.material');

		if ($tipo_alm !== '')
		{
			$this->db->where('t.id_tipo', $tipo_alm);
		}

		if ($centro !== '')
		{
			$this->db->where('s.centro', $centro);
		}

		if ($almacen !== '')
		{
			$this->db->where('s.almacen', $almacen);
		}

		if ($estado_stock !== '')
		{
			$this->db->where('s.estado_stock', $estado_stock);
		}

		if ($lote !== '')
		{
			$this->db->where('s.lote', $lote);
		}

		if ($material !== '')
		{
			$this->db->where('s.material', $material);
		}

		if ($tipo_material === 'EQUIPO')
		{
			$this->db->where_in('substring(s.material,1,2)', array('TM', 'TC', 'TO', 'PK', 'PO'));
			$this->db->where('substring(s.material,1,8) <>', 'PKGCLOTK');
		}
		elseif ($tipo_material === 'SIMCARD')
		{
			$this->db->where("(substring(s.material,1,2) = 'TS' OR substring(s.material,1,8)='PKGCLOTK')");
		}
		elseif ($tipo_material === 'FIJA')
		{
			$this->db->where('substring(s.material,1,3)', '103');
		}

		if ($permanencia === 'm030')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 30);
		}
		elseif ($permanencia === 'm060')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 31);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 60);
		}
		elseif ($permanencia === 'm090')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 61);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 90);
		}
		elseif ($permanencia === 'm120')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 91);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 120);
		}
		elseif ($permanencia === 'm180')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 121);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 180);
		}
		elseif ($permanencia === 'm360')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 181);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 360);
		}
		elseif ($permanencia === 'm720')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 361);
			$this->db->where('datediff(dd, modificado_el, fecha_stock) <=', 720);
		}
		elseif ($permanencia === 'mas720')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) >=', 721);
		}
		elseif ($permanencia === 'otro')
		{
			$this->db->where('datediff(dd, modificado_el, fecha_stock) IS NULL');
		}
		elseif ($permanencia === 'total')
		{
		}

		return $this->db->get()->result_array();

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
			->from($this->config->item('bd_permanencia') . ' as s')
			->join($this->config->item('bd_tipoalmacen_sap') . ' ta', 'ta.centro=s.centro and ta.cod_almacen=s.almacen', 'left')
			->join($this->config->item('bd_tiposalm_sap') . ' t', 'ta.id_tipo=t.id_tipo', 'left')
			->join($this->config->item('bd_pmp') . ' v', 's.centro=v.centro and s.material=v.material and s.lote=v.lote and s.estado_stock=v.estado_stock', 'left')
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
	public function arr_query2treemap($tipo = 'cantidad', $arr_treemap = array(), $arr_nodos = array(), $campo_size = '', $campo_color = '')
	{
		$arr_final = array();

		$tipo = ($tipo === 'cantidad') ? 'cantidad' : 'valor';

		if(count($arr_treemap) > 0 AND count($arr_nodos) > 0)
		{
			$campo_item = array_pop($arr_nodos);
			$campo_padre = array_pop($arr_nodos);

			foreach($arr_treemap as $registro)
			{
				array_push($arr_final, array("'".$registro[$campo_item]."'", "'".$registro[$campo_padre]."'", (int) $registro[$campo_size], $registro[$campo_color]));
			}

			$campo_item = $campo_padre;

			while (count($arr_nodos) > 0)
			{
				$campo_padre = array_pop($arr_nodos);
				$arr_tmp = array();
				$arr_tmp2 = $arr_final;

				foreach($arr_treemap as $registro)
				{
					$arr_v = array("'".$registro[$campo_item]."'", "'".$registro[$campo_padre]."'", 0, 0);

					if( ! in_array($arr_v, $arr_tmp))
					{
						array_push($arr_tmp, $arr_v);
					}
				}

				$arr_final = array();
				$arr_final = array_merge($arr_tmp, $arr_tmp2);
				$campo_item = $campo_padre;
			}

			$campo_padre = "'TOTAL'";
			$arr_tmp = array();
			$arr_tmp2 = $arr_final;
			foreach($arr_treemap as $regitro)
			{
				$arr_v = array("'".$regitro[$campo_item]."'", $campo_padre, 0, 0);
				if( ! in_array($arr_v, $arr_tmp))
				{
					array_push($arr_tmp, $arr_v);
				}
			}
			$arr_final = array();
			$arr_tmp3 = array(array($campo_padre, 'null', 0, 0));
			$arr_final = array_merge($arr_tmp3, $arr_tmp, $arr_tmp2);

			$salida = "['Item','Padre','".$tipo."','Antiguedad'],\n";
			foreach($arr_final as $registro)
			{
				$salida .= '[' . implode($registro, ',') . "],\n";
			}

			return '['.$salida.']';

		}
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
		$arr_tipo_fechas = array(
			'ANNO'      => 'Año',
			'TRIMESTRE' => 'Trimestre',
			'MES'       => 'Meses',
			'DIA'       => 'Dias'
		);

		if ($tipo_fecha === 'ANNO')
		{
			unset($arr_tipo_fechas['ANNO']);
		}
		else if ($tipo_fecha === 'TRIMESTRE')
		{
			unset($arr_tipo_fechas['ANNO']);
			unset($arr_tipo_fechas['TRIMESTRE']);
		}
		else if ($tipo_fecha === 'MES')
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
		$arr_filtro = explode('~', urldecode($filtro));

		if ($tipo_fecha === 'ANNO')
		{
			$this->db->distinct()
				->select('anno as llave')
				->select('anno as valor')
				->from($this->config->item('bd_fechas_sap'))
				->order_by('anno ASC');
		}
		else if ($tipo_fecha === 'TRIMESTRE')
		{
			$this->db->distinct()
				->select("cast(anno as varchar(10)) + '-' + trimestre as llave")
				->select("cast(anno as varchar(10)) + '-' + trimestre as valor")
				->from($this->config->item('bd_fechas_sap'))
				->order_by("cast(anno as varchar(10)) + '-' + trimestre ASC");

			if ($filtro !== '')
			{
				$this->db->where_in('anno', $arr_filtro);
			}
		}
		else if ($tipo_fecha === 'MES')
		{
			$this->db->distinct()
				->select('anomes as llave')
				->select('anomes as valor')
				->from($this->config->item('bd_fechas_sap'))
				->order_by('anomes ASC');

			if ($filtro !== '')
			{
				$this->db->where_in("cast(anno as varchar(10)) + '-' + trimestre", $arr_filtro);
			}
		}
		else if ($tipo_fecha === 'DIA')
		{
			$this->db->distinct()
				->select('convert(varchar(8), fecha, 112) as llave')
				->select('convert(varchar(8), fecha, 112) as valor')
				->from($this->config->item('bd_fechas_sap'))
				->order_by('convert(varchar(8), fecha, 112) ASC');

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
			->from($this->config->item('bd_cmv_sap'))
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
		$arr_tipos_almacen = array(
			'MOVIL-TIPOALM' => 'Móvil - Tipos Almacén',
			'MOVIL-ALM'     => 'Móvil - Almacenes',
			'FIJA-TIPOALM'  => 'Fija - Tipos Almacén',
			'FIJA-ALM'      => 'Fija - Almacenes',
		);

		if ($tipo_alm === 'MOVIL-TIPOALM')
		{
			unset($arr_tipos_almacen['MOVIL-TIPOALM']);
			unset($arr_tipos_almacen['FIJA-TIPOALM']);
			unset($arr_tipos_almacen['FIJA-ALM']);
		}
		else if ($tipo_alm === 'FIJA-TIPOALM')
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

		$almacen_sap = new Almacen_sap;
		$tipoalmacen_sap = new Tipoalmacen_sap;

		return ($param[1] === 'TIPOALM') ?
			$tipoalmacen_sap->get_combo_tiposalm($param[0]) :
			$almacen_sap->get_combo_almacenes($param[0], $filtro);
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
		$arr_tipos_material = array(
			'TIPO'     => 'Tipo',
			'MARCA'    => 'Marca',
			'MODELO'   => 'Modelo',
			'MATERIAL' => 'Material',
		);

		if ($tipo_mat === 'TIPO')
		{
			unset($arr_tipos_material['TIPO']);
		}
		else if ($tipo_mat === 'MARCA')
		{
			unset($arr_tipos_material['TIPO']);
			unset($arr_tipos_material['MARCA']);
		}
		else if ($tipo_mat === 'MODELO')
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
		$arr_filtro = explode('~', urldecode($filtro));

		if ($tipo === 'TIPO')
		{
			$this->db
				->select('tipo1 as llave')
				->select('tipo1 as valor')
				->from($this->config->item('bd_materiales2_sap'))
				->order_by('tipo1 ASC');
		}
		else if ($tipo === 'MARCA')
		{
			$this->db
				->select('sub_marca as llave')
				->select("sub_marca + ' '+ des_marca as valor")
				->from($this->config->item('bd_materiales2_sap'))
				->order_by('des_marca ASC');

			if ($filtro !== '')
			{
				$this->db->where_in('tipo1', $arr_filtro);
			}
		}
		else if ($tipo === 'MODELO')
		{
			$this->db
				->select('sub_marcamodelo as llave')
				->select('sub_marcamodelo as valor')
				->from($this->config->item('bd_materiales2_sap'))
				->order_by('sub_marcamodelo ASC');

			if ($filtro !== '')
			{
				$this->db->where_in('sub_marca', $arr_filtro);
			}
		}
		else if ($tipo === 'MATERIAL')
		{
			$arr_combo = $this->db
				->select('codigo_sap as llave')
				->select("codigo_sap + ' ' + descripcion as valor")
				->from($this->config->item('bd_materiales2_sap'))
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
	 * Recupera datos reporte historial de movimientos
	 *
	 * @param  array $config Arreglo de configuración del reporte
	 * @return array         Reporte
	 */
	public function get_reporte_movhist($config = array())
	{
		$param_ok = $config['fechas'] AND $config['cmv'] AND $config['almacenes'] AND $config['materiales'];

		$arr_reporte = array();

		$arr_filtro_fechas = array(
			'ANNO'      => 'f.anno',
			'TRIMESTRE' => "cast(f.anno as varchar(10)) + '/' + trimestre",
			'MES'       => 'f.anomes',
			'DIA'       => 'f.fecha',
		);

		if ($config['tipo_cruce_alm'] === 'alm')
		{
			$arr_filtro_almacenes = array(
				'MOVIL-TIPOALM' => 't.id_tipo',
				'MOVIL-ALM'     => "m.ce+'-'+m.alm",
				'FIJA-TIPOALM'  => 't.id_tipo',
				'FIJA-ALM'      => "m.ce+'-'+m.alm",
			);
		}
		else
		{
			$arr_filtro_almacenes = array(
				'MOVIL-TIPOALM' => 't.id_tipo',
				'MOVIL-ALM'     => "m.ce+'-'+m.rec",
				'FIJA-TIPOALM'  => 't.id_tipo',
				'FIJA-ALM'      => "m.ce+'-'+m.rec",
			);
		}

		$arr_filtro_materiales = array(
			'TIPO'     => 'mat.tipo1',
			'MARCA'    => 'mat.sub_marca',
			'MODELO'   => 'mat.sub_marcamodelo',
			'MATERIAL' => 'm.codigo_sap',
		);

		if ($param_ok)
		{
			$this->db->limit(1000);

			$this->db->from($this->config->item('bd_resmovimientos_sap') . ' as m');
			$this->db->join($this->config->item('bd_fechas_sap') . ' as f', 'm.fecha=f.fecha');
			$this->db->join($this->config->item('bd_cmv_sap') . ' as c', 'm.cmv=c.cmv');
			$this->db->join($this->config->item('bd_materiales2_sap') . ' as mat', 'm.codigo_sap=mat.codigo_sap');

			if ($config['tipo_cruce_alm'] === 'alm')
			{
				$this->db->join($this->config->item('bd_almacenes_sap') . ' as a', 'm.ce=a.centro and m.alm=a.cod_almacen');
				$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' as t', 'm.ce=t.centro and m.alm=t.cod_almacen');
			}
			else
			{
				$this->db->join($this->config->item('bd_almacenes_sap') . ' as a', 'm.ce=a.centro and m.rec=a.cod_almacen');
				$this->db->join($this->config->item('bd_tipoalmacen_sap') . ' as t', 'm.ce=t.centro and m.rec=t.cod_almacen');
			}


			$this->db->where_in($arr_filtro_fechas[$config['tipo_fecha']], $config['fechas']);
			$this->db->where_in('m.cmv', $config['cmv']);
			$this->db->where_in($arr_filtro_almacenes[$config['tipo_alm']], $config['almacenes']);
			$this->db->where_in($arr_filtro_materiales[$config['tipo_mat']], $config['materiales']);

			$arr_reporte = $this->db->get()->result_array();
		}

		return $arr_reporte;

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
			->from($this->config->item('bd_stock_seriado_sap_03'))
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
			->from($this->config->item('bd_stock_seriado_sap_03'))
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
			->from($this->config->item('bd_stock_seriado_sap_03'))
			->group_by("centro + '-' + almacen + ' ' + des_almacen, substring(material, 6, 2)", FALSE)
			->order_by('almacen, marca');

		$return_array = array();
		foreach($this->db->get()->result_array() as $registro)
		{
			$return_array[$registro['almacen']][$registro['marca']] = $registro['cantidad'];
		}

		return $return_array;
	}


}

/* End of file reportestock_model.php */
/* Location: ./application/models/reportestock_model.php */