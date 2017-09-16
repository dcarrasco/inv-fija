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

use ORM_Model;

/**
 * Clase Modelo Inventario
 *
 * @category CodeIgniter
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Log_gestor extends ORM_Model {

	/**
	 * Arreglo de validacion
	 *
	 * @var array
	 */
	public $validation_rules = [
		[
			'field' => 'series',
			'label' => 'Series',
			'rules' => ''
		],
		[
			'field' => 'set_serie',
			'label' => 'Series',
			'rules' => ''
		],
		[
			'field' => 'tipo_reporte',
			'label' => 'Tipo de Reporte',
			'rules' => ''
		],
		[
			'field' => 'ult_mov',
			'label' => 'Ultimo movimiento',
			'rules' => ''
		],
		[
			'field' => 'tipo_op_alta',
			'label' => 'Tipo de operación de alta',
			'rules' => ''
		],
		[
			'field' => 'tipo_op_baja',
			'label' => 'Tipo de operción de baja',
			'rules' => ''
		],
	];


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
	 * Recupera registros log_deco_tarjeta
	 *
	 * @param  string  $series       Series a buscar
	 * @param  string  $tipo_serie   Indicador de busqueda (por serie deco o por RUT)
	 * @param  string  $tipo_reporte [description]
	 * @param  string  $filtro_cas   Filtro del estado del deco
	 * @param  boolean $ult_mov      Indicador si trae todos los movimientos o solo el ultimo
	 * @return array                 Reporte
	 */
	public function get_log($series = '', $tipo_serie = 'serie_deco', $tipo_reporte = 'log', $filtro_cas = "'ALTA', 'BAJA'", $ult_mov = FALSE)
	{

		$result = [];
		$arr_series = explode("\n", $series);

		foreach ($arr_series as $serie)
		{
			$serie = trim($serie);
			if ($tipo_serie === 'serie_deco')
			{
				$serie = substr($serie, 0, 10);
			}
			elseif ($tipo_serie === 'rut')
			{
				if (strpos($serie, '-') !== FALSE)
				{
					$serie = substr($serie, 0, strpos($serie, '-'));
				}
			}

			if ($serie !== '')
			{
				$tabla_log_dth = $tipo_reporte === 'log' ? 'bd_logistica..bd_dth_log_decotarjeta' : 'bd_logistica..bd_dth_ult_log';
				$campo_where = $tipo_serie === 'serie_deco' ? 'serie_deco' : 'rut';

				$s_query =
					"SELECT
						id_log_deco_tarjeta,
						fecha_log,
						estado,
						peticion,
						tipo_operacion_cas,
						telefono,
						rut,
						serie_deco,
						serie_tarjeta,
						MAX(nombre) nombre
					FROM (
						SELECT
							l.id_log_deco_tarjeta,
							l.fecha_log,
							l.estado,
							l.peticion,
							l.tipo_operacion_cas,
							l.area+'-'+l.telefono as telefono,
							l.rut+'-'+l.rut_dv as rut,
							l.serie_deco,
							l.serie_tarjeta,
							c.nom_cliente+' '+c.ape1_cliente+' '+ape2_cliente as nombre
						FROM $tabla_log_dth l
						LEFT JOIN bd_controles..trafico_clientes c on c.num_ident = l.rut+'-'+l.rut_dv
						WHERE $campo_where = '$serie'
							and TIPO_OPERACION_CAS in ($filtro_cas)
					) T1
					GROUP BY id_log_deco_tarjeta, fecha_log, estado, peticion, tipo_operacion_cas, telefono, rut, serie_deco, serie_tarjeta
					ORDER BY serie_deco, id_log_deco_tarjeta";

				$query = $this->db->query($s_query);

				// en caso de $ult_mov, filtra los movimientos que no sean los últimos
				$res_array = [];
				if ($ult_mov)
				{
					$tmp_array = $query->result_array();
					$deco_ant = '';
					foreach($tmp_array as $registro)
					{
						if ($registro['serie_deco'] !== $deco_ant AND $deco_ant !== '')
						{
							array_push($res_array, $registro_ant);
						}
						$reg_ant = $registro;
						$deco_ant = $registro['serie_deco'];
					}
					array_push($res_array, $reg_ant);
				}
				else
				{
					$res_array = $query->result_array();
				}


				// repasa las series de los decos, calculando los DV de c/u de ellas
				$res_array2 = [];
				foreach($res_array as $registro)
				{
					$registro['serie_deco'] = $registro['serie_deco'] . $this->dv_serie_deco($registro['serie_deco']);
					array_push($res_array2, $registro);
				}

				array_push($result, $res_array2);
			}
		}

		return $result;
	}

	// --------------------------------------------------------------------

	/**
	 * Calcula el digito verificador de un decodificador
	 *
	 * @param  string $serie_deco Serie del decodificador a calcular
	 *
	 * @return string             Digito verificador
	 */
	public function dv_serie_deco($serie_deco = '')
	{
		$serie_deco = substr($serie_deco, 0, 10);

		$digito_verificador  =  6 * substr($serie_deco, 0, 2);
		$digito_verificador += 19 * substr($serie_deco, 2, 1);
		$digito_verificador +=  8 * substr($serie_deco, 3, 3);
		$digito_verificador +=  1 * substr($serie_deco, 6, 2);

		$digito_verificador  = $digito_verificador % 23;
		$digito_verificador += 1 * substr($serie_deco, 8, 2);

		$digito_verificador = (string) $digito_verificador % 100;

		if (strlen($digito_verificador) === 1)
		{
			$digito_verificador = '0' . $digito_verificador;
		}
		return (string) $digito_verificador;
	}



}

/* End of file log_gestor_model.php */
/* Location: ./application/models/log_gestor_model.php */
