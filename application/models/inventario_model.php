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
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @param  string  $param1        Parámetros adicionales
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte($reporte = '', $id_inventario = 0, $orden_campo = NULL, $orden_tipo = 'ASC',
										$incl_ajustes = '0', $elim_sin_dif = '0', $param1 = NULL)
	{
		if ($reporte === 'hoja')
		{
			return $this->inventario_model->get_reporte_hoja($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);
		}
		elseif ($reporte === 'detalle_hoja')
		{
			return $this->inventario_model->get_reporte_detalle_hoja($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif, $param1);
		}
		elseif ($reporte === 'material')
		{
			return $this->inventario_model->get_reporte_material($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);
		}
		elseif ($reporte === 'material_faltante')
		{
			return $this->inventario_model->get_reporte_material_faltante($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);
		}
		elseif ($reporte === 'detalle_material')
		{
			return $this->inventario_model->get_reporte_detalle_material($id_inventario, $orden_campo, $orden_tipo, $param1, $incl_ajustes, $elim_sin_dif);
		}
		elseif ($reporte === 'ubicacion')
		{
			return $this->inventario_model->get_reporte_ubicacion($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);
		}
		elseif ($reporte === 'tipos_ubicacion')
		{
			return $this->inventario_model->get_reporte_tipos_ubicacion($id_inventario, $orden_campo, $orden_tipo, $incl_ajustes, $elim_sin_dif);
		}
		elseif ($reporte === 'ajustes')
		{
			return $this->inventario_model->get_reporte_ajustes($id_inventario, $orden_campo, $orden_tipo, $elim_sin_dif);
		}

	}


	/**
	 * Devuelve reporte por hojas
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_hoja($id_inventario = 0, $orden_campo = 'hoja', $orden_tipo = 'ASC',
										$incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = $orden_campo === '' ? 'hoja' : $orden_campo;

		$this->db->select('di.hoja, d.nombre as digitador, a.nombre as auditor');

		$this->db->select_sum('stock_sap', 'sum_stock_sap');
		$this->db->select_sum('stock_fisico', 'sum_stock_fisico');
		if ($incl_ajustes === '1')
		{
			$this->db->select_sum('stock_ajuste', 'sum_stock_ajuste');
			$this->db->select_sum('(stock_fisico - stock_sap + stock_ajuste)', 'sum_stock_diff');
		}
		else
		{
			$this->db->select_sum('(stock_fisico - stock_sap)', 'sum_stock_diff');
		}

		$this->db->select_sum('(stock_sap * c.pmp)', 'sum_valor_sap');
		$this->db->select_sum('(stock_fisico * c.pmp)', 'sum_valor_fisico');
		if ($incl_ajustes === '1')
		{
			$this->db->select_sum('(stock_ajuste * c.pmp)', 'sum_valor_ajuste');
			$this->db->select_sum('((stock_fisico - stock_sap + stock_ajuste) * c.pmp)', 'sum_valor_diff');
		}
		else
		{
			$this->db->select_sum('((stock_fisico-stock_sap) * c.pmp)', 'sum_valor_diff');
		}

		$this->db->select_max('fecha_modificacion', 'fecha');
		$this->db->from($this->config->item('bd_detalle_inventario') . ' di');
		$this->db->join($this->config->item('bd_usuarios') . ' d', 'd.id = di.digitador', 'left');
		$this->db->join($this->config->item('bd_auditores') . ' a', 'a.id = di.auditor', 'left');
		$this->db->join($this->config->item('bd_catalogos') . ' c', 'c.catalogo = di.catalogo', 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->group_by('di.hoja, d.nombre, a.nombre');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		if ($elim_sin_dif === '1')
		{
			if ($incl_ajustes === '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte con el detalle de una hoja
	 * @param  integer $id_inventario ID del inventario con el cual se hará el reporte
	 * @param  string  $orden_campo   Nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ASC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @param  integer $hoja          numero de la hoja del inventario
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_detalle_hoja($id_inventario = 0, $orden_campo = 'ubicacion', $orden_tipo = 'ASC',
												$incl_ajustes = '0', $elim_sin_dif = '0', $hoja = 0)
	{
		$orden_campo = $orden_campo === '' ? 'ubicacion' : $orden_campo;

		$this->db->select("case when reg_nuevo='S' THEN ubicacion + ' [*]' ELSE ubicacion END as ubicacion", FALSE);
		$this->db->select('di.catalogo');
		$this->db->select('di.descripcion');
		$this->db->select('di.lote');
		$this->db->select('di.centro');
		$this->db->select('di.almacen');
		$this->db->select('di.um');
		$this->db->select('di.stock_sap');
		$this->db->select('di.stock_fisico');
		$this->db->select('di.stock_ajuste');
		if ($incl_ajustes === '1')
		{
			$this->db->select('(di.stock_fisico - di.stock_sap + di.stock_ajuste) as stock_diff');
		}
		else
		{
			$this->db->select('(di.stock_fisico - di.stock_sap) as stock_diff');
		}
		$this->db->select('(di.stock_sap * c.pmp) as valor_sap');
		$this->db->select('(di.stock_fisico * c.pmp) as valor_fisico');
		$this->db->select('(di.stock_ajuste * c.pmp) as valor_ajuste');
		if ($incl_ajustes === '1')
		{
			$this->db->select('((di.stock_fisico - di.stock_sap + di.stock_ajuste) * c.pmp) as valor_diff');
		}
		else
		{
			$this->db->select('((di.stock_fisico - di.stock_sap) * c.pmp) as valor_diff');
		}

		$this->db->from($this->config->item('bd_detalle_inventario') . ' di');
		$this->db->join($this->config->item('bd_catalogos') . ' c', 'c.catalogo = di.catalogo', 'left');

		$this->db->where('di.id_inventario', $id_inventario);
		$this->db->where('di.hoja', $hoja);

		if ($elim_sin_dif === '1')
		{
			if ($incl_ajustes === '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}


		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por materiales
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_material($id_inventario = 0, $orden_campo = 'catalogo', $orden_tipo = 'ASC', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = $orden_campo === '' ? 'catalogo' : $orden_campo;

		$this->db->select("f.codigo + '-' + f.nombre + ' >> ' + sf.codigo + '-' + sf.nombre as nombre_fam", FALSE);
		$this->db->select("f.codigo + '_' + sf.codigo as fam_subfam", FALSE);
		$this->db->select('di.catalogo, di.descripcion, di.um, c.pmp');
		$this->db->select_sum('stock_sap', 'sum_stock_sap');
		$this->db->select_sum('stock_fisico', 'sum_stock_fisico');
		if ($incl_ajustes === '1')
		{
			$this->db->select_sum('stock_ajuste', 'sum_stock_ajuste');
		}
		if ($incl_ajustes === '1')
		{
			$this->db->select('SUM(stock_fisico - stock_sap + stock_ajuste) as sum_stock_dif', FALSE);
		}
		else
		{
			$this->db->select('SUM(stock_fisico - stock_sap) as sum_stock_dif', FALSE);
		}
		$this->db->select('SUM(stock_sap * c.pmp) as sum_valor_sap', FALSE);
		$this->db->select('SUM(stock_fisico * c.pmp) as sum_valor_fisico', FALSE);
		if ($incl_ajustes === '1')
		{
			$this->db->select('SUM(stock_ajuste * c.pmp) as sum_valor_ajuste', FALSE);
		}
		if ($incl_ajustes === '1')
		{
			$this->db->select('SUM(stock_fisico * c.pmp - stock_sap * c.pmp + stock_ajuste * pmp) as sum_valor_diff', FALSE);
		}
		else
		{
			$this->db->select('SUM(stock_fisico * c.pmp - stock_sap * c.pmp) as sum_valor_diff', FALSE);
		}
		$this->db->select_max('fecha_modificacion', 'fecha');
		$this->db->from($this->config->item('bd_detalle_inventario') . ' di');
		$this->db->join($this->config->item('bd_catalogos') . ' c', 'c.catalogo = di.catalogo', 'left');
		$this->db->join($this->config->item('bd_familias') . ' f', "f.codigo = substring(di.catalogo,1,5) and f.tipo='FAM'", 'left');
		$this->db->join($this->config->item('bd_familias') . ' sf', "sf.codigo = substring(di.catalogo,1,7) and sf.tipo='SUBFAM'", 'left');

		$this->db->where('id_inventario', $id_inventario);

		$this->db->group_by("f.codigo + '-' + f.nombre + ' >> ' + sf.codigo + '-' + sf.nombre");
		$this->db->group_by("f.codigo + '_' + sf.codigo");
		$this->db->group_by('di.catalogo, di.descripcion, di.um, c.pmp');
		if ($elim_sin_dif === '1')
		{
			if ($incl_ajustes === '1')
			{
				$this->db->having('sum(stock_fisico - stock_sap + stock_ajuste) <> 0');
			}
			else
			{
				$this->db->having('sum(stock_fisico - stock_sap) <> 0');
			}
		}
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por materiales faltantes
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_material_faltante($id_inventario = 0, $orden_campo = 'catalogo', $orden_tipo = 'ASC', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = $orden_campo === '' ? 'catalogo' : $orden_campo;

		$this->db->select('i.catalogo, i.descripcion, i.um, c.pmp');
		$this->db->select_sum('stock_fisico', 'q_fisico');
		$this->db->select_sum('stock_sap', 'q_sap');

		if ($incl_ajustes === '1')
		{
			$this->db->select('(SUM(stock_sap) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as q_faltante');
			$this->db->select('(0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as q_coincidente');
			$this->db->select('(SUM((stock_fisico+stock_ajuste)) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as q_sobrante');
			$this->db->select('c.pmp * (SUM(stock_sap) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as v_faltante');
			$this->db->select('c.pmp * (0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as v_coincidente');
			$this->db->select('c.pmp * (SUM((stock_fisico+stock_ajuste)) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) as v_sobrante');
		}
		else
		{
			$this->db->select('(SUM(stock_sap) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as q_faltante');
			$this->db->select('(0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as q_coincidente');
			$this->db->select('(SUM(stock_fisico) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as q_sobrante');
			$this->db->select('c.pmp * (SUM(stock_sap) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as v_faltante');
			$this->db->select('c.pmp * (0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as v_coincidente');
			$this->db->select('c.pmp * (SUM(stock_fisico) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) as v_sobrante');
		}

		$this->db->from($this->config->item('bd_detalle_inventario') . ' i');
		$this->db->join($this->config->item('bd_catalogos') . ' c', 'c.catalogo = i.catalogo', 'left');
		$this->db->where('id_inventario', $id_inventario);

		if ($elim_sin_dif === '1')
		{
			if ($incl_ajustes === '1')
			{
				$this->db->having('(SUM(stock_sap) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) <> 0');
				$this->db->or_having('(SUM((stock_fisico+stock_ajuste)) - 0.5 * (SUM(stock_sap + (stock_fisico+stock_ajuste)) - ABS(SUM(stock_sap - (stock_fisico+stock_ajuste))))) <> 0');
			}
			else
			{
				$this->db->having('(SUM(stock_sap) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) <> 0');
				$this->db->or_having('(SUM(stock_fisico) - 0.5 * (SUM(stock_sap + stock_fisico) - ABS(SUM(stock_sap - stock_fisico)))) <> 0');
			}
		}

		$this->db->group_by('i.catalogo, i.descripcion, i.um, c.pmp');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por detalle de los materiales
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $catalogo      catalogo a buscar
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_detalle_material($id_inventario = 0, $orden_campo = 'ubicacion', $orden_tipo = 'ASC', $catalogo = '', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = $orden_campo === '' ? 'ubicacion' : $orden_campo;

		$this->db->select('i.*, c.pmp');
		$this->db->select('stock_fisico as stock_fisico');
		$this->db->select('stock_sap as stock_sap');
		$this->db->select('stock_ajuste as stock_ajuste');
		$this->db->select('stock_fisico * c.pmp as valor_fisico', FALSE);
		$this->db->select('stock_sap * c.pmp as valor_sap', FALSE);
		$this->db->select('stock_ajuste * c.pmp as valor_ajuste', FALSE);

		if ($incl_ajustes === '1')
		{
			$this->db->select('(stock_fisico - stock_sap + stock_ajuste) as stock_diff');
			$this->db->select('(stock_fisico - stock_sap + stock_ajuste) * c.pmp as valor_diff');
			$this->db->select('(stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante');
			$this->db->select('(0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente');
		}
		else
		{
			$this->db->select('(stock_fisico - stock_sap) as stock_diff');
			$this->db->select('(stock_fisico - stock_sap) * c.pmp as valor_diff');
			$this->db->select('(stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante');
			$this->db->select('(0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente');
		}

		$this->db->select('(stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_faltante');
		$this->db->select('(0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_coincidente');
		$this->db->select('(stock_fisico - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as q_sobrante');
		$this->db->select('pmp * (stock_sap - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_faltante');
		$this->db->select('pmp * (0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_coincidente');
		$this->db->select('pmp * (stock_fisico - 0.5 * ((stock_sap + stock_fisico) - abs(stock_sap - stock_fisico))) as v_sobrante');
		$this->db->from($this->config->item('bd_detalle_inventario') . ' i');
		$this->db->join($this->config->item('bd_catalogos') . ' c', 'c.catalogo = i.catalogo', 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->where('i.catalogo', $catalogo);

		if ($elim_sin_dif === '1')
		{
			if ($incl_ajustes === '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}


		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por ubicaciones
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  Indica si mostrar o no los ajustes
	 * @param  string  $elim_sin_dif  Indica si mostrar o no registros sin diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_ubicacion($id_inventario = 0, $orden_campo = 'ubicacion', $orden_tipo = 'ASC', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = $orden_campo === '' ? 'ubicacion' : $orden_campo;

		$this->db->select('di.ubicacion');

		$this->db->select_sum('stock_sap', 'sum_stock_sap');
		$this->db->select_sum('stock_fisico', 'sum_stock_fisico');

		if ($incl_ajustes === '1')
		{
			$this->db->select_sum('stock_ajuste', 'sum_stock_ajuste');
			$this->db->select_sum('(stock_fisico - stock_sap + stock_ajuste)', 'sum_stock_dif');
		}
		else
		{
			$this->db->select_sum('(stock_fisico - stock_sap)', 'sum_stock_dif');
		}

		$this->db->select_sum('(stock_sap * c.pmp)', 'sum_valor_sap');
		$this->db->select_sum('(stock_fisico * c.pmp)', 'sum_valor_fisico');

		if ($incl_ajustes === '1')
		{
			$this->db->select_sum('(stock_ajuste * c.pmp)', 'sum_valor_ajuste');
			$this->db->select_sum('(stock_fisico * c.pmp - stock_sap * c.pmp + stock_ajuste * pmp)', 'sum_valor_dif');
		}
		else
		{
			$this->db->select_sum('(stock_fisico * c.pmp - stock_sap * c.pmp)', 'sum_valor_dif');
		}

		$this->db->select_max('fecha_modificacion', 'fecha');
		$this->db->from($this->config->item('bd_detalle_inventario') . ' di');
		$this->db->join($this->config->item('bd_catalogos') . ' c', 'c.catalogo = di.catalogo', 'left');
		$this->db->where('id_inventario', $id_inventario);
		$this->db->group_by('di.ubicacion');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		if ($elim_sin_dif === '1')
		{
			if ($incl_ajustes === '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}

		return $this->db->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve reporte por tipos de ubicaciones
	 * @param  integer $id_inventario ID del inventario que se usará para generar el reporte
	 * @param  string  $orden_campo   nombre del campo para ordenar el reporte
	 * @param  string  $orden_tipo    indica si el orden es ascendente o descendente (ADC/DESC)
	 * @param  string  $incl_ajustes  indica si se suman los ajustes realizados
	 * @param  string  $elim_sin_dif  indica si se muestran registros que no tengan diferencias
	 * @return array                  arreglo con el detalle del reporte
	 */
	public function get_reporte_tipos_ubicacion($id_inventario = 0, $orden_campo = 'ubicacion', $orden_tipo = 'ASC', $incl_ajustes = '0', $elim_sin_dif = '0')
	{
		$orden_campo = $orden_campo === '' ? 'tipo_ubicacion' : $orden_campo;

		$this->db->select('t.tipo_ubicacion');
		$this->db->select('d.ubicacion');
		$this->db->select_sum('stock_sap', 'sum_stock_sap');
		$this->db->select_sum('stock_fisico', 'sum_stock_fisico');

		if ($incl_ajustes === '0')
		{
			$this->db->select_sum('(stock_fisico - stock_sap)', 'sum_stock_diff');
		}
		else
		{
			$this->db->select_sum('stock_ajuste', 'sum_stock_ajuste');
			$this->db->select_sum('(stock_fisico - stock_sap + stock_ajuste)', 'sum_stock_diff');
		}

		$this->db->select_sum('(stock_fisico-stock_sap)', 'sum_stock_diff');
		$this->db->select_sum('(stock_sap * c.pmp)', 'sum_valor_sap');
		$this->db->select_sum('(stock_fisico * c.pmp)', 'sum_valor_fisico');

		if ($incl_ajustes === '0')
		{
			$this->db->select_sum('((stock_fisico-stock_sap) * c.pmp)', 'sum_valor_diff');
		}
		else
		{
			$this->db->select_sum('(stock_ajuste * c.pmp)', 'sum_valor_ajuste');
			$this->db->select_sum('((stock_fisico-stock_sap+stock_ajuste) * c.pmp)', 'sum_valor_diff');
		}

		$this->db->select_max('fecha_modificacion', 'fecha');

		$this->db->from($this->config->item('bd_detalle_inventario') . ' d');
		$this->db->join($this->config->item('bd_inventarios') . ' i', 'd.id_inventario=i.id', 'left');
		$this->db->join($this->config->item('bd_ubic_tipoubic') . ' ut', 'ut.tipo_inventario=i.tipo_inventario and ut.ubicacion = d.ubicacion', 'left');
		$this->db->join($this->config->item('bd_tipo_ubicacion') . ' t', 't.id=ut.id_tipo_ubicacion', 'left');
		$this->db->join($this->config->item('bd_catalogos') . ' c', 'c.catalogo = d.catalogo', 'left');

		$this->db->where('d.id_inventario', $id_inventario);

		$this->db->group_by('t.tipo_ubicacion');
		$this->db->group_by('d.ubicacion');
		$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		if ($elim_sin_dif === '1')
		{
			if ($incl_ajustes === '1')
			{
				$this->db->where('(stock_fisico - stock_sap + stock_ajuste) <> 0', NULL, FALSE);
			}
			else
			{
				$this->db->where('(stock_fisico - stock_sap) <> 0', NULL, FALSE);
			}
		}
		return $this->db->get()->result_array();
	}


	// --------------------------------------------------------------------

	/**
	 * Reporte de ajustes de inventario
	 *
	 * @param  integer $id_inventario Identificador del inventario
	 * @param  string  $orden_campo   Campo para ordenar el reporte
	 * @param  string  $orden_tipo    Tipo de orden (ascendente o descendente)
	 * @param  string  $elim_sin_dif  Elimina registros sin diferencias
	 * @return array                  Reporte de ajustes de inventario
	 */
	public function get_reporte_ajustes($id_inventario = 0, $orden_campo = 'catalogo', $orden_tipo = 'ASC', $elim_sin_dif = '0')
	{
		$orden_campo = $orden_campo === '' ? 'catalogo' : $orden_campo;

		$this->db->select('catalogo');
		$this->db->select('descripcion');
		$this->db->select('lote');
		$this->db->select('centro');
		$this->db->select('almacen');
		$this->db->select('ubicacion');
		$this->db->select('hoja');
		$this->db->select('um');
		$this->db->select('stock_sap');
		$this->db->select('stock_fisico');
		$this->db->select('stock_ajuste');
		$this->db->select('stock_fisico - stock_sap + stock_ajuste as stock_dif', FALSE);
		$this->db->select('CASE WHEN (stock_fisico - stock_sap + stock_ajuste) > 0 THEN \'SOBRANTE\' WHEN (stock_fisico - stock_sap + stock_ajuste) < 0 THEN \'FALTANTE\' WHEN (stock_fisico - stock_sap + stock_ajuste) = 0 THEN \'OK\' END as tipo', FALSE);
		$this->db->select('glosa_ajuste');
		$this->db->where('id_inventario', $id_inventario);
		if ($elim_sin_dif === 1)
		{
			$this->db->where('stock_fisico - stock_sap + stock_ajuste <> 0', NULL, FALSE);
		}
		else
		{
			$this->db->where('stock_fisico - stock_sap <> 0', NULL, FALSE);
		}

		$this->db->order_by('catalogo, lote, centro, almacen, ubicacion');
		//$this->db->order_by($orden_campo . ' ' . $orden_tipo);

		return $this->db->get($this->config->item('bd_detalle_inventario'))->result_array();
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
		if ($reporte === 'hoja')
		{
			return $this->inventario_model->get_campos_reporte_hoja();
		}
		elseif ($reporte === 'detalle_hoja')
		{
			return $this->inventario_model->get_campos_reporte_detalle_hoja();
		}
		elseif ($reporte === 'material')
		{
			return $this->inventario_model->get_campos_reporte_material();
		}
		elseif ($reporte === 'material_faltante')
		{
			return $this->inventario_model->get_campos_reporte_material_faltante();
		}
		elseif ($reporte === 'detalle_material')
		{
			return $this->inventario_model->get_campos_reporte_detalle_material();
		}
		elseif ($reporte === 'ubicacion')
		{
			return $this->inventario_model->get_campos_reporte_ubicacion();
		}
		elseif ($reporte === 'tipos_ubicacion')
		{
			return $this->inventario_model->get_campos_reporte_tipos_ubicacion();
		}
		elseif ($reporte === 'ajustes')
		{
			return $this->inventario_model->get_campos_reporte_ajustes();
		}
	}

	/**
	 * Devuelve los campos del reporte hoja
	 *
	 * @return array Arreglo de campos
	 */
	public function get_campos_reporte_hoja()
	{
		$arr_campos = array();

		$arr_campos['hoja'] = array('titulo' => 'Hoja', 'class' => '', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_hoja/');
		$arr_campos['auditor'] = array('titulo' => 'Auditor', 'class' => '', 'tipo' => 'texto');
		$arr_campos['digitador'] = array('titulo' => 'Digitador', 'class' => '', 'tipo' => 'texto');

		$arr_campos['sum_stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
		}

		$arr_campos['sum_stock_diff'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');

		$arr_campos['sum_valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
		}

		$arr_campos['sum_valor_diff'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');

		// Establece el orden de los campos
		$new_orden_tipo  = (set_value('order_sort') === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = $campo === set_value('order_by', 'hoja') ? $new_orden_tipo : 'ASC';

			$order_icon = ($arr_campos[$campo]['order_by'] === 'ASC')
				? 'glyphicon-circle-arrow-up'
				: 'glyphicon-circle-arrow-down';

			$arr_campos[$campo]['img_orden'] = $campo === set_value('order_by', 'hoja')
				? ' <span class="text-muted glyphicon ' . $order_icon . '" ></span>'
				: '';
		}

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
		$arr_campos = array();

		$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion', 'class' => '', 'tipo' => 'texto');
		$arr_campos['catalogo'] = array('titulo' => 'Catalogo', 'class' => '', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_material/');
		$arr_campos['descripcion'] = array('titulo' => 'Descripcion', 'class' => '', 'tipo' => 'texto');
		$arr_campos['lote'] = array('titulo' => 'lote', 'class' => '', 'tipo' => 'texto');
		$arr_campos['centro'] = array('titulo' => 'centro', 'class' => '', 'tipo' => 'texto');
		$arr_campos['almacen'] = array('titulo' => 'almacen', 'class' => '', 'tipo' => 'texto');

		$arr_campos['stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
		}

		$arr_campos['stock_diff'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');
		$arr_campos['valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
		}

		$arr_campos['valor_diff'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');

		// Establece el orden de los campos
		$new_orden_tipo  = (set_value('order_sort') === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = $campo === set_value('order_by', 'ubicacion') ? $new_orden_tipo : 'ASC';

			$order_icon = ($arr_campos[$campo]['order_by'] === 'ASC')
				? 'glyphicon-circle-arrow-up'
				: 'glyphicon-circle-arrow-down';

			$arr_campos[$campo]['img_orden'] = $campo === set_value('order_by', 'ubicacion')
				? ' <span class="text-muted glyphicon ' . $order_icon . '" ></span>'
				: '';
		}

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
			$arr_campos['nombre_fam'] = array('titulo' => 'Familia', 'class' => '', 'tipo' => 'subtotal');
		}

		$arr_campos['catalogo'] = array('titulo' => 'Catalogo', 'class' => '', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_material/');
		$arr_campos['descripcion'] = array('titulo' => 'Descripcion', 'class' => '', 'tipo' => 'texto');
		$arr_campos['um'] = array('titulo' => 'UM', 'class' => '', 'tipo' => 'texto');
		$arr_campos['pmp'] = array('titulo' => 'PMP', 'class' => 'text-center', 'tipo' => 'valor_pmp');

		$arr_campos['sum_stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
		}

		$arr_campos['sum_stock_dif'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');

		$arr_campos['sum_valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
		}

		$arr_campos['sum_valor_diff'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');

		// Establece el orden de los campos
		$new_orden_tipo  = (set_value('order_sort') === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = $campo === set_value('order_by', 'ubicacion') ? $new_orden_tipo : 'ASC';

			$order_icon = ($arr_campos[$campo]['order_by'] === 'ASC')
				? 'glyphicon-circle-arrow-up'
				: 'glyphicon-circle-arrow-down';

			$arr_campos[$campo]['img_orden'] = $campo === set_value('order_by', 'ubicacion')
				? ' <span class="text-muted glyphicon ' . $order_icon . '" ></span>'
				: '';
		}

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
		$arr_campos = array();

		$arr_campos['catalogo'] = array('titulo' => 'Catalogo', 'class' => '', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_material/');
		$arr_campos['descripcion'] = array('titulo' => 'Descripcion', 'class' => '', 'tipo' => 'texto');
		$arr_campos['um'] = array('titulo' => 'UM', 'class' => '', 'tipo' => 'texto');
		$arr_campos['q_faltante'] = array('titulo' => 'Cant Faltante', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['q_coincidente'] = array('titulo' => 'Cant Coincidente', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['q_sobrante'] = array('titulo' => 'Cant Sobrante', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['v_faltante'] = array('titulo' => 'Valor Faltante', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['v_coincidente'] = array('titulo' => 'Valor Coincidente', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['v_sobrante'] = array('titulo' => 'Valor Sobrante', 'class' => 'text-center', 'tipo' => 'valor');

		// Establece el orden de los campos
		$new_orden_tipo  = (set_value('order_sort') === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = $campo === set_value('order_by', 'catalogo') ? $new_orden_tipo : 'ASC';

			$order_icon = ($arr_campos[$campo]['order_by'] === 'ASC')
				? 'glyphicon-circle-arrow-up'
				: 'glyphicon-circle-arrow-down';

			$arr_campos[$campo]['img_orden'] = $campo === set_value('order_by', 'catalogo')
				? ' <span class="text-muted glyphicon ' . $order_icon . '" ></span>'
				: '';
		}

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
		$arr_campos = array();

		$arr_campos['catalogo'] = array('titulo' => 'Catalogo', 'class' => '', 'tipo' => 'texto');
		$arr_campos['descripcion'] = array('titulo' => 'Descripcion', 'class' => '', 'tipo' => 'texto');
		$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion', 'class' => '', 'tipo' => 'texto');
		$arr_campos['hoja'] = array('titulo' => 'Hoja', 'class' => '', 'tipo' => 'link', 'href' => $this->router->class . '/listado/detalle_hoja/');
		$arr_campos['lote'] = array('titulo' => 'lote', 'class' => '', 'tipo' => 'texto');

		$arr_campos['stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
		}

		$arr_campos['stock_diff'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');

		$arr_campos['valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
		}

		$arr_campos['valor_diff'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');

		// Establece el orden de los campos
		$new_orden_tipo  = (set_value('order_sort') === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = $campo === set_value('order_by', 'ubicacion') ? $new_orden_tipo : 'ASC';

			$order_icon = ($arr_campos[$campo]['order_by'] === 'ASC')
				? 'glyphicon-circle-arrow-up'
				: 'glyphicon-circle-arrow-down';

			$arr_campos[$campo]['img_orden'] = $campo === set_value('order_by', 'ubicacion')
				? ' <span class="text-muted glyphicon ' . $order_icon . '" ></span>'
				: '';
		}

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
		$arr_campos = array();

		$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion', 'class' => '', 'tipo' => 'texto');

		$arr_campos['sum_stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
		}

		$arr_campos['sum_stock_dif'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');

		$arr_campos['sum_valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
		}

		$arr_campos['sum_valor_dif'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');

		// Establece el orden de los campos
		$new_orden_tipo  = (set_value('order_sort') === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = $campo === set_value('order_by', 'ubicacion') ? $new_orden_tipo : 'ASC';

			$order_icon = ($arr_campos[$campo]['order_by'] === 'ASC')
				? 'glyphicon-circle-arrow-up'
				: 'glyphicon-circle-arrow-down';

			$arr_campos[$campo]['img_orden'] = $campo === set_value('order_by', 'ubicacion')
				? ' <span class="text-muted glyphicon ' . $order_icon . '" ></span>'
				: '';
		}

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
		$arr_campos = array();

		$arr_campos['tipo_ubicacion'] = array('titulo' => 'Tipo Ubicacion', 'class' => '', 'tipo' => 'subtotal');
		$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion', 'class' => '', 'tipo' => 'texto');
		$arr_campos['sum_stock_sap'] = array('titulo' => 'Cant SAP', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['sum_stock_fisico'] = array('titulo' => 'Cant Fisico', 'class' => 'text-center', 'tipo' => 'numero');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['sum_stock_ajuste'] = array('titulo' => 'Cant Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
		}

		$arr_campos['sum_stock_diff'] = array('titulo' => 'Cant Dif', 'class' => 'text-center', 'tipo' => 'numero_dif');

		$arr_campos['sum_valor_sap'] = array('titulo' => 'Valor SAP', 'class' => 'text-center', 'tipo' => 'valor');
		$arr_campos['sum_valor_fisico'] = array('titulo' => 'Valor Fisico', 'class' => 'text-center', 'tipo' => 'valor');

		if (set_value('incl_ajustes') === '1')
		{
			$arr_campos['sum_valor_ajuste'] = array('titulo' => 'Valor Ajuste', 'class' => 'text-center', 'tipo' => 'valor');
		}

		$arr_campos['sum_valor_diff'] = array('titulo' => 'Valor Dif', 'class' => 'text-center', 'tipo' => 'valor_dif');

		// Establece el orden de los campos
		$new_orden_tipo  = (set_value('order_sort') === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = $campo === set_value('order_by', 'tipo_ubicacion') ? $new_orden_tipo : 'ASC';

			$order_icon = ($arr_campos[$campo]['order_by'] === 'ASC')
				? 'glyphicon-circle-arrow-up'
				: 'glyphicon-circle-arrow-down';

			$arr_campos[$campo]['img_orden'] = $campo === set_value('order_by', 'tipo_ubicacion')
				? ' <span class="text-muted glyphicon ' . $order_icon . '" ></span>'
				: '';
		}

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
		$arr_campos = array();

		$arr_campos['catalogo'] = array('titulo' => 'Material', 'class' => '', 'tipo' => 'text');
		$arr_campos['descripcion']  = array('titulo' => 'Desc Material', 'class' => '', 'tipo' => 'text');
		$arr_campos['lote'] = array('titulo' => 'Lote', 'class' => '', 'tipo' => 'text');
		$arr_campos['centro'] = array('titulo' => 'Centro', 'class' => '', 'tipo' => 'text');
		$arr_campos['almacen'] = array('titulo' => 'Almacen', 'class' => '', 'tipo' => 'text');
		$arr_campos['ubicacion'] = array('titulo' => 'Ubicacion', 'class' => '', 'tipo' => 'text');
		$arr_campos['hoja'] = array('titulo' => 'Hoja', 'class' => '', 'tipo' => 'text');
		$arr_campos['um'] = array('titulo' => 'UM', 'class' => '', 'tipo' => 'text');
		$arr_campos['stock_sap'] = array('titulo' => 'Stock SAP', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['stock_fisico'] = array('titulo' => 'Stock Fisico', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['stock_ajuste'] = array('titulo' => 'Stock Ajuste', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['stock_dif'] = array('titulo' => 'Stock Dif', 'class' => 'text-center', 'tipo' => 'numero');
		$arr_campos['tipo'] = array('titulo' => 'Tipo Dif', 'class' => 'text-center', 'tipo' => 'texto');
		$arr_campos['glosa_ajuste'] = array('titulo' => 'Observacion', 'class' => '', 'tipo' => 'texto');

		// Establece el orden de los campos
		$new_orden_tipo  = (set_value('order_sort') === 'ASC') ? 'DESC' : 'ASC';

		foreach ($arr_campos as $campo => $valor)
		{
			$arr_campos[$campo]['order_by'] = $campo === set_value('order_by', 'tipo_ubicacion') ? $new_orden_tipo : 'ASC';

			$order_icon = ($arr_campos[$campo]['order_by'] === 'ASC')
				? 'glyphicon-circle-arrow-up'
				: 'glyphicon-circle-arrow-down';

			$arr_campos[$campo]['img_orden'] = $campo === set_value('order_by', 'tipo_ubicacion')
				? ' <span class="text-muted glyphicon ' . $order_icon . '" ></span>'
				: '';
		}

		return $arr_campos;
	}



}
/* End of file inventario_model.php */
/* Location: ./application/models/inventario_model.php */