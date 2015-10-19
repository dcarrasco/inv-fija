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
 * @package  Stock
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 *
 */
class Log_gestor_model extends CI_Model {


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
	public function get_log($series = string, $tipo_serie = 'serie_deco', $tipo_reporte = 'log', $filtro_cas = "'ALTA', 'BAJA'", $ult_mov = FALSE)
	{

		$result = array();
		$arr_series = explode("\n", $series);
		foreach ($arr_series as $serie)
		{
			$serie = trim($serie);
			if ($tipo_serie === 'serie_deco')
			{
				$serie = substr($serie, 0, 10);
			}
			else if ($tipo_serie === 'rut')
			{
				if (strpos($serie, '-') !== FALSE)
				{
					$serie = substr($serie, 0, strpos($serie, '-'));
				}
			}

			if ($serie !== '')
			{
				$s_query = 'SELECT id_log_deco_tarjeta, fecha_log, estado, peticion, tipo_operacion_cas, telefono, rut, serie_deco, serie_tarjeta, max(nombre) nombre ';
				$s_query .= 'FROM (';
				$s_query .= "SELECT L.ID_LOG_DECO_TARJETA, L.FECHA_LOG, L.ESTADO, L.PETICION, L.TIPO_OPERACION_CAS, L.AREA+'-'+L.TELEFONO AS TELEFONO, L.RUT+'-'+L.RUT_DV AS RUT, L.SERIE_DECO, L.SERIE_TARJETA, C.NOM_CLIENTE+' '+C.APE1_CLIENTE+' '+APE2_CLIENTE AS NOMBRE ";

				if ($tipo_reporte === 'log')
				{
					$s_query .= 'FROM BD_LOGISTICA..BD_DTH_LOG_DECOTARJETA L ';
				}
				else
				{
					$s_query .= 'FROM BD_LOGISTICA..BD_DTH_ULT_LOG L ';
				}

				$s_query .= "LEFT JOIN BD_CONTROLES..TRAFICO_CLIENTES C ON C.NUM_IDENT = L.RUT+'-'+L.RUT_DV ";

				if ($tipo_serie === 'serie_deco')
				{
					$s_query .= "WHERE SERIE_DECO = '" .$serie . "' ";
				}
				else
				{
					$s_query .= "WHERE RUT = '" .$serie . "' ";
				}

				$s_query .= 'AND TIPO_OPERACION_CAS IN ( ' . $filtro_cas . ') ';

				$s_query .= ') T1 ';
				$s_query .= 'GROUP BY ID_LOG_DECO_TARJETA, FECHA_LOG, ESTADO, PETICION, TIPO_OPERACION_CAS, TELEFONO, RUT, SERIE_DECO, SERIE_TARJETA ';
				$s_query .= 'ORDER BY SERIE_DECO, ID_LOG_DECO_TARJETA';

				$query = $this->db->query($s_query);

				// en caso de $ult_mov, filtra los movimientos que no sean los últimos
				$res_array = array();
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
				$res_array2 = array();
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
		$digito_verificador = 6*substr($serie_deco, 0, 2);
		$digito_verificador += 19*substr($serie_deco, 2, 1);
		$digito_verificador += 8*substr($serie_deco, 3, 3);
		$digito_verificador += 1*substr($serie_deco, 6, 2);
		$digito_verificador = $digito_verificador % 23;
		$digito_verificador += 1*substr($serie_deco, 8, 2);
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