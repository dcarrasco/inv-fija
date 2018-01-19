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
trait Reporte {

	// --------------------------------------------------------------------

	/**
	 * Devuelve las asignaciones realizados en TOA
	 *
	 * @param  string $reporte     Tipo de reporte a desplegar
	 * @param  string $fecha_desde Fecha de los datos del reporte
	 * @param  string $fecha_hasta Fecha de los datos del reporte
	 * @param  string $orden_campo Campo para ordenar el resultado
	 * @param  string $orden_tipo  Orden del resultado (ascendente o descendente)
	 * @return string
	 */
	public function reporte($reporte = 'tecnicos', $fecha_desde = NULL, $fecha_hasta = NULL, $orden_campo = '', $orden_tipo = 'ASC')
	{
		if ( ! $reporte OR ! $fecha_desde OR ! $fecha_hasta)
		{
			return '';
		}

		$this->datos_reporte = $this->datos_reporte($reporte, $fecha_desde, $fecha_hasta);
		$this->campos_reporte = $this->campos_reporte($reporte, $fecha_desde, $fecha_hasta, $orden_campo, $orden_tipo);

		return $this->genera_reporte();
	}

	// --------------------------------------------------------------------

	/**
	 * Recupera los datos de las asignaciones
	 *
	 * @param  string  $reporte      Tipo de reporte
	 * @param  string  $fecha_desde  Fecha inicial
	 * @param  atring  $fecha_hasta  Fecha final
	 * @param  boolean $filtra_signo Indicador si se filtra el signo
	 * @return array
	 */
	protected function datos_reporte($reporte, $fecha_desde, $fecha_hasta, $filtra_signo = TRUE)
	{
		if ($reporte === 'tecnicos')
		{
			$this->db
				->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left')
				->join(config('bd_almacenes_sap').' d', 'a.centro=d.centro and a.almacen=d.cod_almacen', 'left');
		}
		elseif ($reporte === 'detalle')
		{
			$this->db
				->join(config('bd_tecnicos_toa').' b', 'a.cliente=b.id_tecnico', 'left', FALSE)
				->join(config('bd_empresas_toa').' c', 'b.id_empresa = c.id_empresa', 'left');
		}

		return $this->rango_asignaciones($fecha_desde, $fecha_hasta, $filtra_signo)
			->select(array_get($this->select_fields, $reporte), FALSE)
			->group_by(array_get($this->group_by_fields, $reporte))
			->order_by(array_get($this->order_fields, $reporte))
			->get()->result_array();
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los campos a mostrar en reporte de asignación
	 * @param  string $reporte     Tipo de reporte a desplegar
	 * @param  string $fecha_desde Fecha inicial
	 * @param  atring $fecha_hasta Fecha final
	 * @param  string $orden_campo Campo para ordenar el resultado
	 * @param  string $orden_tipo  Orden del resultado (ascendente o descendente)
	 * @return array
	 */
	protected function campos_reporte($reporte = 'tecnicos', $fecha_desde = NULL, $fecha_hasta = NULL, $orden_campo = '', $orden_tipo = 'ASC')
	{
		$orden_tipo = ($orden_tipo === '') ? 'ASC' : $orden_tipo;
		$orden_campo = empty($orden_campo) ? array_get($this->order_fields, $reporte) : $orden_campo;

		$arr_campos = collect($this->configuracion_campos)
			->only(array_get($this->config_campos_reportes, $reporte))
			->merge($this->texto_link($reporte, $fecha_desde, $fecha_hasta));

		return $this->set_order_campos($arr_campos, $orden_campo);
	}

	// --------------------------------------------------------------------

	/**
	 * Devuelve los parametros para un link del reporte
	 *
	 * @param  string $reporte     Tipo de reporte
	 * @param  string $fecha_desde Fecha de inicio
	 * @param  string $fecha_hasta Fecha de fin
	 * @return array
	 */
	protected function texto_link($reporte, $fecha_desde, $fecha_hasta)
	{
		$href = [
			'tecnicos'      => ['cliente'],
			'material'      => ['material'],
			'lote'          => ['lote'],
			'lote-material' => ['lote', 'material'],
		];

		return ['texto_link' => [
			'titulo'         => '',
			'tipo'           => 'link_registro',
			'class'          => 'text-right',
			'href'           => "toa_asignaciones/ver_asignaciones/{$reporte}/{$fecha_desde}/{$fecha_hasta}",
			'href_registros' => array_get($href, $reporte, []),
		]];
	}

}

// End of file Reporte.php
// Location: ./models/Toa/Asignacion/Reporte.php
