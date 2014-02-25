<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Log_gestor_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}

	public function get_log($series = string, $tipo_serie = 'serie_deco', $ult_mov = FALSE)
	{
		$result = array();
		$arr_series = explode("\n", $series);
		foreach ($arr_series as $serie)
		{
			$serie = trim($serie);
			if ($tipo_serie == 'serie_deco')
			{
				$serie = substr($serie, 0, 10);
			}
			else if ($tipo_serie == 'rut')
			{
				if (strpos($serie, '-') !== FALSE)
				{
					$serie = substr($serie, 0, strpos($serie, '-'));
				}
			}

			if ($serie != "")
			{
				$s_query = 'SELECT id_log_deco_tarjeta, fecha_log, estado, peticion, tipo_operacion_cas, telefono, rut, serie_deco, serie_tarjeta, max(nombre) nombre ';
				$s_query .= 'FROM (';
				$s_query .= "SELECT L.ID_LOG_DECO_TARJETA, L.FECHA_LOG, L.ESTADO, L.PETICION, L.TIPO_OPERACION_CAS, L.AREA+'-'+L.TELEFONO AS TELEFONO, L.RUT+'-'+L.RUT_DV AS RUT, L.SERIE_DECO, L.SERIE_TARJETA, C.NOM_CLIENTE+' '+C.APE1_CLIENTE+' '+APE2_CLIENTE AS NOMBRE ";
				$s_query .= 'FROM BD_LOGISTICA..BD_DTH_LOG_DECOTARJETA L ';
				$s_query .= "LEFT JOIN BD_CONTROLES..TRAFICO_CLIENTES C ON C.NUM_IDENT = L.RUT+'-'+L.RUT_DV ";

				if ($tipo_serie == 'serie_deco')
				{
					$s_query .= "WHERE SERIE_DECO = '" .$serie . "' ";
				}
				else
				{
					$s_query .= "WHERE RUT = '" .$serie . "' ";
				}

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
					foreach($tmp_array as $reg)
					{
						if ($reg['serie_deco'] != $deco_ant AND $deco_ant != '')
						{
							array_push($res_array, $reg_ant);
						}
						$reg_ant = $reg;
						$deco_ant = $reg['serie_deco'];
					}
				}
				else
				{
					$res_array = $query->result_array();
				}


				// repasa las series de los decos, calculando los DV de c/u de ellas
				$res_array2 = array();
				foreach($res_array as $reg)
				{
					$reg['serie_deco'] = $reg['serie_deco'] . $this->dv_serie_deco($reg['serie_deco']);
					array_push($res_array2, $reg);
				}

				array_push($result, $res_array2);
			}
		}

		return $result;
	}


	public function dv_serie_deco($serie_deco = '')
	{
		$serie_deco = substr($serie_deco, 0, 10);
		$sum = 6*substr($serie_deco,0,2);
		$sum += 19*substr($serie_deco,2,1);
		$sum += 8*substr($serie_deco,3,3);
		$sum += 1*substr($serie_deco,6,2);
		$sum = $sum % 23;
		$sum += 1*substr($serie_deco,8,2);
		$sum = (string) $sum % 100;

		if (strlen($sum) == 1)
		{
			$sum = '0' . $sum;
		}
		return ($sum);
	}



}

/* End of file log_gestor_model.php */
/* Location: ./application/models/log_gestor_model.php */