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

namespace Toa\Asignacion;

if ( ! defined('BASEPATH')) exit('No direct script access allowed');
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
trait Listado {

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
	public function listado($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		if ( ! $tipo_reporte)
		{
			return '';
		}

		$this->datos_reporte = $this->datos_listado($tipo_reporte, $param1, $param2, $param3, $param4);
		$this->campos_reporte = $this->campos_listado();

		return $this->genera_reporte();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera datos peticiones dados argumentos de busqueda
	 *
	 * @param  string $tipo_reporte Tipo de reporte a buscar
	 * @param  string $param1       Primer parametro
	 * @param  string $param2       Segundo parametro
	 * @param  string $param3       Tercer parametro
	 * @param  string $param4       Cuarto parametro
	 * @return array
	 */
	protected function datos_listado($tipo_reporte = NULL, $param1 = NULL, $param2 = NULL, $param3 = NULL, $param4 = NULL)
	{
		$filtros = [
			'material' => 'a.material',
			'lote'     => 'a.lote',
			'tecnicos' => 'a.cliente',
		];

		if (array_key_exists($tipo_reporte, $filtros))
		{
			$this->db->where($filtros[$tipo_reporte], $param3);
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

		return $this->rango_asignaciones($param1, $param2)
			->select("convert(varchar(20), a.fecha_contabilizacion, 112) as fecha, a.documento_material, c.empresa, a.cliente, b.tecnico, a.centro, a.almacen, d.des_almacen, a.material, a.texto_material, a.valor, a.lote, 'ver detalle' as texto_link, sum(-a.cantidad_en_um) as cant, sum(-a.importe_ml) as monto", FALSE)
			->group_by('a.fecha_contabilizacion, a.documento_material, c.empresa, a.cliente, b.tecnico, a.centro, a.almacen, d.des_almacen, a.material, a.texto_material, a.valor, a.lote')
			->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
			->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
			->join(config('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left')
			->order_by('a.documento_material', 'ASC')
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera campos peticiones dados argumentos de busqueda
	 *
	 * @return array
	 */
	protected function campos_listado()
	{
		$campos_reporte = [
			'fecha'              => ['titulo' => 'Fecha', 'tipo' => 'fecha'],
			'documento_material' => ['titulo' => 'Documento SAP'],
			'empresa'            => ['titulo' => 'Empresa'],
			'cliente'            => ['titulo' => 'Cod Tecnico'],
			'tecnico'            => ['titulo' => 'Nombre Tecnico'],
			'centro'             => ['titulo' => 'Centro'],
			'almacen'            => ['titulo' => 'Almac&eacute;n'],
			'des_almacen'        => ['titulo' => 'Desc Almac&eacute;n'],
			'material'           => ['titulo' => 'Material'],
			'texto_material'     => ['titulo' => 'Desc Material'],
			'lote'               => ['titulo' => 'Lote'],
			'valor'              => ['titulo' => 'Valor'],
			'cant'               => ['titulo' => 'Cantidad', 'tipo' => 'numero', 'class' => 'text-right'],
			'monto'              => ['titulo' => 'Monto', 'tipo' => 'valor', 'class' => 'text-right'],
			'texto_link'         => [
				'titulo'         => '',
				'class'          => 'text-right',
				'tipo'           => 'link_registro',
				'href'           => 'toa_asignaciones/detalle_asignacion',
				'href_registros' => ['fecha','documento_material']
			],
		];

		return $this->set_order_campos($campos_reporte, 'documento_material');
	}

}

// End of file Listado.php
// Location: ./models/Toa/Asignacion/Listado.php
