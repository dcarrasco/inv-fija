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
 * Clase Modelo Inventario
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Inventario_model extends CI_Model {

	/**
	 * Reglas de validación para los reportes
	 *
	 * @var array
	 */
	public $reportes_validation = array(
		array(
			'field' => 'inv_activo',
			'label' => 'Inventario activo',
			'rules' => 'trim'
		),
		array(
			'field' => 'elim_sin_dif',
			'label' => 'Ocultar registros sin diferencias',
			'rules' => 'trim'
		),
		array(
			'field' => 'incl_ajustes',
			'label' => 'Incluir ajustes de inventario',
			'rules' => 'trim'
		),
		array(
			'field' => 'incl_familias',
			'label' => 'Incluir familias de productos',
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
	 * Devuelve el identificador del inventario activo
	 *
	 * @return integer ID del inventario activo
	 */
	public function get_id_inventario_activo()
	{
		return $this->db
			->where('activo', 1)
			->get($this->config->item('bd_inventarios'))
			->row()
			->id;
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los inventarios para desplegar en combobox
	 *
	 * @return array Arreglo con inventarios para usar en combobox
	 */
	public function get_combo_inventarios()
	{
		$arr_rs = $this->db
			->from($this->config->item('bd_inventarios').' a')
			->join($this->config->item('bd_tipos_inventario').' b', 'a.tipo_inventario=b.id_tipo_inventario')
			->order_by('desc_tipo_inventario, nombre')
			->get()->result_array();

		$arr_combo = array();
		foreach($arr_rs as $item)
		{
			if ( ! array_key_exists($item['desc_tipo_inventario'], $arr_combo))
			{
				$arr_combo[$item['desc_tipo_inventario']] = array();
			}

			$arr_combo[$item['desc_tipo_inventario']][$item['id']] = $item['nombre'];
		}

		return $arr_combo;
	}


	// =====================================================================================================
	// REPORTES
	// =====================================================================================================

	/**
	 * Devuelve reporte por hojas
	 * @param  string  $reporte       Nombre del reporte a ejecutar
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $sort_by       nombre del campo para ordenar el reporte
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @param  string  $param1        Parámetros adicionales
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte($reporte = '', $id_inventario = 0, $sort_by = NULL,
										$incl_ajustes = '0', $elim_sin_dif = '0', $param1 = NULL)
	{
		return call_user_func_array(array($this, 'get_reporte_'.$reporte), array($id_inventario, $sort_by, $incl_ajustes, $elim_sin_dif, $param1));
	}

	// --------------------------------------------------------------------

	protected function _db_select_sum_cantidades($incl_ajustes = '0')
	{
		$stock_ajuste = ($incl_ajustes === '1') ? ' + di.stock_ajuste' : '';

		$this->db->select_sum('di.stock_sap', 'sum_stock_sap')
			->select_sum('di.stock_fisico', 'sum_stock_fisico')
			->select_sum('di.stock_ajuste', 'sum_stock_ajuste')
			->select_sum('(di.stock_fisico - di.stock_sap'.$stock_ajuste.')', 'sum_stock_diff')
			->select_sum('(di.stock_sap * c.pmp)', 'sum_valor_sap')
			->select_sum('(di.stock_fisico * c.pmp)', 'sum_valor_fisico')
			->select_sum('(di.stock_ajuste * c.pmp)', 'sum_valor_ajuste')
			->select_sum('((di.stock_fisico - di.stock_sap'.$stock_ajuste.') * c.pmp)', 'sum_valor_diff');

		return $this;
	}

	// --------------------------------------------------------------------

	protected function _db_select_cantidades($incl_ajustes = '0')
	{
		$stock_ajuste = ($incl_ajustes === '1') ? ' + di.stock_ajuste' : '';

		$this->db->select('di.stock_sap')
			->select('di.stock_fisico')
			->select('di.stock_ajuste')
			->select('(di.stock_fisico - di.stock_sap'.$stock_ajuste.') as stock_diff', FALSE)

			->select('(di.stock_sap * c.pmp) as valor_sap', FALSE)
			->select('(di.stock_fisico * c.pmp) as valor_fisico', FALSE)
			->select('(di.stock_ajuste * c.pmp) as valor_ajuste', FALSE)
			->select('((di.stock_fisico - di.stock_sap'.$stock_ajuste.') * c.pmp) as valor_diff', FALSE);

		return $this;
	}

	// --------------------------------------------------------------------

	protected function _db_base_reporte($id_inventario = 0)
	{
		$this->db->from($this->config->item('bd_detalle_inventario').' di')
			->join($this->config->item('bd_catalogos').' c', 'c.catalogo = di.catalogo collate Latin1_General_CI_AS', 'left', FALSE)
			->where('id_inventario', $id_inventario);

		return $this;
	}

	// --------------------------------------------------------------------

	protected function _db_elim_sin_diferencia($incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$stock_ajuste = ($incl_ajustes === '1') ? ' + di.stock_ajuste' : '';

		if ($elim_sin_dif === '1')
		{
			$this->db->where('(di.stock_fisico - di.stock_sap'.$stock_ajuste.') <> 0', NULL, FALSE);
		}

		return $this;
	}

	// --------------------------------------------------------------------


	/**
	 * Devuelve reporte por hojas
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_hoja($id_inventario = 0, $orden_campo = '+hoja', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = empty($orden_campo) ? '+hoja' : $orden_campo;

		$this->_db_select_sum_cantidades($incl_ajustes)
			->_db_base_reporte($id_inventario)
			->_db_elim_sin_diferencia($incl_ajustes, $elim_sin_dif);

		$this->db->select('di.hoja, d.nombre as digitador, a.nombre as auditor')
			->select_max('fecha_modificacion', 'fecha')
			->join($this->config->item('bd_usuarios').' d', 'd.id = di.digitador', 'left')
			->join($this->config->item('bd_auditores').' a', 'a.id = di.auditor', 'left')
			->group_by('di.hoja, d.nombre, a.nombre')
			->order_by($this->reporte->get_order_by($orden_campo));

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte con el detalle de una hoja
	 * @param  integer $id_inventario ID del inventario con el cual se hará el reporte
	 * @param  string  $orden_campo   Nombre del campo para ordenar el reporte
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @param  integer $hoja          numero de la hoja del inventario
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_detalle_hoja($id_inventario = 0, $orden_campo = '+ubicacion',
												$incl_ajustes = '0', $elim_sin_dif = '0', $hoja = 0)
	{
		$orden_campo = empty($orden_campo) ? '+ubicacion' : $orden_campo;

		$this->_db_select_cantidades($incl_ajustes)
			->_db_base_reporte($id_inventario)
			->_db_elim_sin_diferencia($incl_ajustes, $elim_sin_dif);

		$this->db->select("case when reg_nuevo='S' THEN ubicacion+' [*]' ELSE ubicacion END as ubicacion", FALSE)
			->select('di.catalogo')
			->select('di.descripcion')
			->select('di.lote')
			->select('di.centro')
			->select('di.almacen')
			->select('di.um')
			->where('di.hoja', $hoja)
			->order_by($this->reporte->get_order_by($orden_campo));

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por materiales
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_material($id_inventario = 0, $orden_campo = '+catalogo',  $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = empty($orden_campo) ? '+catalogo' : $orden_campo;

		$this->_db_select_sum_cantidades($incl_ajustes)
			->_db_base_reporte($id_inventario)
			->_db_elim_sin_diferencia($incl_ajustes, $elim_sin_dif);

		$this->db->select("f.codigo + '-' + f.nombre + ' >> ' + sf.codigo + '-' + sf.nombre as nombre_fam", FALSE)
			->select("f.codigo + '_' + sf.codigo as fam_subfam", FALSE)
			->select('di.catalogo, di.descripcion, di.um, c.pmp')
			->select_max('fecha_modificacion', 'fecha')
			->join($this->config->item('bd_familias').' f', "f.codigo = substring(di.catalogo,1,5) collate Latin1_General_CI_AS and f.tipo='FAM'", 'left', FALSE)
			->join($this->config->item('bd_familias').' sf', "sf.codigo = substring(di.catalogo,1,7) collate Latin1_General_CI_AS and sf.tipo='SUBFAM'", 'left', FALSE)
			->group_by("f.codigo + '-' + f.nombre + ' >> ' + sf.codigo + '-' + sf.nombre")
			->group_by("f.codigo + '_' + sf.codigo")
			->group_by('di.catalogo, di.descripcion, di.um, c.pmp')
			->order_by($this->reporte->get_order_by($orden_campo));

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por materiales faltantes
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_material_faltante($id_inventario = 0, $orden_campo = '+catalogo',  $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = empty($orden_campo) ? '+catalogo' : $orden_campo;
		$stock_fisico = ($incl_ajustes === '1') ? '(di.stock_fisico+di.stock_ajuste)' : 'di.stock_fisico';
		$min_sap_fisico = "(0.5 * (SUM(stock_sap + {$stock_fisico}) - ABS(SUM(stock_sap - {$stock_fisico}))))";

		$this->_db_base_reporte($id_inventario);

		$this->db->select('di.catalogo, di.descripcion, di.um, c.pmp')
			->select_sum('stock_fisico', 'q_fisico')
			->select_sum('stock_sap', 'q_sap')
			->select("(SUM(stock_sap) - {$min_sap_fisico}) as q_faltante")
			->select("{$min_sap_fisico} as q_coincidente")
			->select("(SUM({$stock_fisico}) - {$min_sap_fisico}) as q_sobrante")
			->select("c.pmp * (SUM(stock_sap) - {$min_sap_fisico}) as v_faltante")
			->select("c.pmp * {$min_sap_fisico} as v_coincidente")
			->select("c.pmp * (SUM({$stock_fisico}) - {$min_sap_fisico}) as v_sobrante")
			->group_by('di.catalogo, di.descripcion, di.um, c.pmp')
			->order_by($this->reporte->get_order_by($orden_campo));

		if ($elim_sin_dif === '1')
		{
			$this->db->having("(SUM(stock_sap) - {$min_sap_fisico}) <> 0");
			$this->db->or_having("(SUM({$stock_fisico}) - {$min_sap_fisico}) <> 0");
		}

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por detalle de los materiales
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @param  string  $catalogo      catalogo a buscar
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_detalle_material($id_inventario = 0, $orden_campo = '+ubicacion',  $incl_ajustes = '0', $elim_sin_dif = '0', $catalogo = '')
	{
		$orden_campo = empty($orden_campo) ? '+ubicacion' : $orden_campo;

		$this->_db_select_cantidades($incl_ajustes)
			->_db_base_reporte($id_inventario)
			->_db_elim_sin_diferencia($incl_ajustes, $elim_sin_dif);

		$this->db->select('di.*, c.pmp')
			->select('(stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante')
			->select('(0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente')
			->select('(stock_fisico - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_sobrante')
			->select('pmp * (stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_faltante')
			->select('pmp * (0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_coincidente')
			->select('pmp * (stock_fisico - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_sobrante')
			->select('(stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante')
			->select('(0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente')
			->where('di.catalogo', $catalogo)
			->order_by($this->reporte->get_order_by($orden_campo));

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por ubicaciones
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $incl_ajustes  Indica si mostrar o no los ajustes
	 * @param  string  $elim_sin_dif  Indica si mostrar o no registros sin diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_ubicacion($id_inventario = 0, $orden_campo = '+ubicacion',  $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = empty($orden_campo) ? '+ubicacion' : $orden_campo;

		$this->_db_select_sum_cantidades($incl_ajustes)
			->_db_base_reporte($id_inventario)
			->_db_elim_sin_diferencia($incl_ajustes, $elim_sin_dif);

		$this->db->select('di.ubicacion')
			->select_max('fecha_modificacion', 'fecha')
			->group_by('di.ubicacion')
			->order_by($this->reporte->get_order_by($orden_campo));

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por tipos de ubicaciones
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_tipos_ubicacion($id_inventario = 0, $orden_campo = '+tipo_ubicacion',  $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = empty($orden_campo) ? '+tipo_ubicacion' : $orden_campo;

		$this->_db_select_sum_cantidades($incl_ajustes)
			->_db_base_reporte($id_inventario)
			->_db_elim_sin_diferencia($incl_ajustes, $elim_sin_dif);

		$this->db->select('t.tipo_ubicacion')
			->select('di.ubicacion')
			->select_max('fecha_modificacion', 'fecha')
			->join($this->config->item('bd_inventarios').' i', 'di.id_inventario=i.id', 'left')
			->join($this->config->item('bd_ubic_tipoubic').' ut', 'ut.tipo_inventario=i.tipo_inventario and ut.ubicacion = di.ubicacion', 'left')
			->join($this->config->item('bd_tipo_ubicacion').' t', 't.id=ut.id_tipo_ubicacion', 'left')
			->group_by('t.tipo_ubicacion')
			->group_by('di.ubicacion')
			->order_by($this->reporte->get_order_by($orden_campo));

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Reporte de ajustes de inventario
	 *
	 * @param  integer $id_inventario Identificador del inventario
	 * @param  string  $orden_campo   Campo para ordenar el reporte
	 * @param  string  $elim_sin_dif  Elimina registros sin diferencias
	 * @return array                  Reporte de ajustes de inventario
	 */
	public function get_reporte_ajustes($id_inventario = 0, $orden_campo = '+catalogo',  $elim_sin_dif = '0')
	{
		$orden_campo = empty($orden_campo) ? '+catalogo' : $orden_campo;

		$this->_db_select_cantidades()
			->_db_base_reporte($id_inventario)
			->_db_elim_sin_diferencia(0, $elim_sin_dif);

		$this->db->select('di.catalogo')
			->select('di.descripcion')
			->select('di.lote')
			->select('di.centro')
			->select('di.almacen')
			->select('di.ubicacion')
			->select('di.hoja')
			->select('di.um')
			->select('CASE WHEN (stock_fisico-stock_sap+stock_ajuste) > 0 THEN \'SOBRANTE\' WHEN (stock_fisico-stock_sap+stock_ajuste) < 0 THEN \'FALTANTE\' WHEN (stock_fisico-stock_sap+stock_ajuste) = 0 THEN \'OK\' END as tipo', FALSE)
			->select('glosa_ajuste')
			->order_by('catalogo, lote, centro, almacen, ubicacion');

		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

	// =====================================================================================================
	// CAMPOS DE LOS REPORTES
	// =====================================================================================================

	/**
	 * Devuelve los campos de un reporte
	 *
	 * @param  string $reporte Nombre del reporte
	 * @return array          Arreglo de campos
	 */
	public function get_campos_reporte($reporte)
	{
		return call_user_func_array(array($this, 'get_campos_reporte_'.$reporte), array());
	}

	// --------------------------------------------------------------------

	protected function _get_campos_sum_stock($incl_ajustes = '0')
	{
		$arr_campos = array();

		$arr_campos['sum_stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');
		if ($incl_ajustes === '1')
		{
			$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
		}
		$arr_campos['sum_stock_diff'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');

		$arr_campos['sum_valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');
		if ($incl_ajustes === '1')
		{
			$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
		}
		$arr_campos['sum_valor_diff'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');

		return $arr_campos;

	}

	// --------------------------------------------------------------------

	protected function _get_campos_stock($incl_ajustes = '0')
	{
		$arr_campos = array();

		$arr_campos['stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');
		if ($incl_ajustes === '1')
		{
			$arr_campos['stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
		}
		$arr_campos['stock_diff'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');

		$arr_campos['valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');
		if ($incl_ajustes === '1')
		{
			$arr_campos['valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
		}
		$arr_campos['valor_diff'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');

		return $arr_campos;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos del reporte hoja
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_hoja()
	{
		$arr_campos = array(
			'hoja'      => array('titulo' => 'Hoja', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_hoja/'),
			'auditor'   => array('titulo' => 'Auditor', 'tipo' => 'texto'),
			'digitador' => array('titulo' => 'Digitador', 'tipo' => 'texto'),
		);

		$arr_campos = array_merge($arr_campos, $this->_get_campos_sum_stock(set_value('incl_ajustes')));

		$this->reporte->set_order_campos($arr_campos, 'hoja');

		return $arr_campos;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos del reporte detalle hoja
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_detalle_hoja()
	{
		$arr_campos = array(
			'ubicacion'   => array('titulo' => 'Ubicacion', 'tipo' => 'texto'),
			'catalogo'    => array('titulo' => 'Catalogo', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_material/'),
			'descripcion' => array('titulo' => 'Descripcion', 'tipo' => 'texto'),
			'lote'        => array('titulo' => 'Lote', 'tipo' => 'texto'),
			'centro'      => array('titulo' => 'Centro', 'tipo' => 'texto'),
			'almacen'     => array('titulo' => 'Almacen', 'tipo' => 'texto'),
		);

		$arr_campos = array_merge($arr_campos, $this->_get_campos_stock(set_value('incl_ajustes')));

		$this->reporte->set_order_campos($arr_campos, 'ubicacion');

		return $arr_campos;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos del reporte material
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_material()
	{
		$arr_campos = array();

		if (set_value('incl_familias') === '1')
		{
			$arr_campos['nombre_fam'] = array('titulo' => 'Familia', 'tipo' => 'subtotal');
		}

		$arr_campos['catalogo'] = array('titulo' => 'Catalogo', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_material/');
		$arr_campos['descripcion'] = array('titulo' => 'Descripcion', 'tipo' => 'texto');
		$arr_campos['um'] = array('titulo' => 'UM', 'tipo' => 'texto');
		$arr_campos['pmp'] = array('titulo' => 'PMP', 'class' => 'text-center', 'tipo' => 'valor_pmp');

		$arr_campos = array_merge($arr_campos, $this->_get_campos_sum_stock(set_value('incl_ajustes')));

		$this->reporte->set_order_campos($arr_campos, 'catalogo');

		return $arr_campos;
	}


	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos del reporte material faltante
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_material_faltante()
	{
		$arr_campos = array(
			'catalogo'      => array('titulo' => 'Catalogo', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_material/'),
			'descripcion'   => array('titulo' => 'Descripcion', 'tipo' => 'texto'),
			'um'            => array('titulo' => 'UM', 'tipo' => 'texto'),
			'q_faltante'    => array('titulo' => 'Cant Faltante', 'class' => 'text-center', 'tipo' => 'numero'),
			'q_coincidente' => array('titulo' => 'Cant Coincidente', 'class' => 'text-center', 'tipo' => 'numero'),
			'q_sobrante'    => array('titulo' => 'Cant Sobrante', 'class' => 'text-center', 'tipo' => 'numero'),
			'v_faltante'    => array('titulo' => 'Valor Faltante', 'class' => 'text-center', 'tipo' => 'valor'),
			'v_coincidente' => array('titulo' => 'Valor Coincidente', 'class' => 'text-center', 'tipo' => 'valor'),
			'v_sobrante'    => array('titulo' => 'Valor Sobrante', 'class' => 'text-center', 'tipo' => 'valor'),
		);

		$this->reporte->set_order_campos($arr_campos, 'catalogo');

		return $arr_campos;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos del reporte detalle material
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_detalle_material()
	{
		$arr_campos = array(
			'catalogo'    => array('titulo' => 'Catalogo', 'tipo' => 'texto'),
			'descripcion' => array('titulo' => 'Descripcion', 'tipo' => 'texto'),
			'ubicacion'   => array('titulo' => 'Ubicacion', 'tipo' => 'texto'),
			'hoja'        => array('titulo' => 'Hoja', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_hoja/'),
			'lote'        => array('titulo' => 'lote', 'tipo' => 'texto'),
		);

		$arr_campos = array_merge($arr_campos, $this->_get_campos_stock(set_value('incl_ajustes')));

		$this->reporte->set_order_campos($arr_campos, 'ubicacion');

		return $arr_campos;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos del reporte ubicacion
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_ubicacion()
	{
		$arr_campos = array(
			'ubicacion' => array('titulo' => 'Ubicaci&oacute;n', 'tipo' => 'texto'),
		);

		$arr_campos = array_merge($arr_campos, $this->_get_campos_sum_stock(set_value('incl_ajustes')));

		$this->reporte->set_order_campos($arr_campos, 'ubicacion');

		return $arr_campos;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos del reporte tipos ubicacion
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_tipos_ubicacion()
	{
		$arr_campos = array(
			'tipo_ubicacion' => array('titulo' => 'Tipo Ubicacion', 'tipo' => 'subtotal'),
			'ubicacion'      => array('titulo' => 'Ubicacion', 'tipo' => 'texto'),
		);

		$arr_campos = array_merge($arr_campos, $this->_get_campos_sum_stock(set_value('incl_ajustes')));

		$this->reporte->set_order_campos($arr_campos, 'tipo_ubicacion');

		return $arr_campos;
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos del reporte ajustes
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_ajustes()
	{
		$arr_campos = array(
			'catalogo'     => array('titulo' => 'Material', 'tipo' => 'texto'),
			'descripcion'  => array('titulo' => 'Descripci&oacute;n material', 'tipo' => 'texto'),
			'lote'         => array('titulo' => 'Lote', 'tipo' => 'texto'),
			'centro'       => array('titulo' => 'Centro', 'tipo' => 'texto'),
			'almacen'      => array('titulo' => 'Almacen', 'tipo' => 'texto'),
			'ubicacion'    => array('titulo' => 'Ubicaci&oacute;n', 'tipo' => 'texto'),
			'hoja'         => array('titulo' => 'Hoja', 'tipo' => 'texto'),
			'um'           => array('titulo' => 'UM', 'tipo' => 'texto'),
			'stock_sap'    => array('titulo' => 'Stock SAP', 'class' => 'text-center', 'tipo' => 'numero'),
			'stock_fisico' => array('titulo' => 'Stock F&iacute;sico', 'class' => 'text-center', 'tipo' => 'numero'),
			'stock_ajuste' => array('titulo' => 'Stock Ajuste', 'class' => 'text-center', 'tipo' => 'numero'),
			'stock_diff'   => array('titulo' => 'Stock Dif', 'class' => 'text-center', 'tipo' => 'numero'),
			'tipo'         => array('titulo' => 'Tipo Dif', 'class' => 'text-center', 'tipo' => 'texto'),
			'glosa_ajuste' => array('titulo' => 'Observaci&oacute;n', 'tipo' => 'texto'),
		);

		$this->reporte->set_order_campos($arr_campos, 'catalogo');

		return $arr_campos;
	}

}
/* End of file inventario_model.php */
/* Location: ./application/models/inventario_model.php */
