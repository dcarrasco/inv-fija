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
				$this->db->limit(1000);
				$this->db->from('bd_logistica..bd_dth_log_decotarjeta');
				$this->db->order_by('serie_deco, id_log_deco_tarjeta');


				if ($tipo_serie == 'serie_deco')
				{
					$this->db->where(array('serie_deco' => $serie));
				}
				else
				{
					$this->db->where(array('rut' => $serie));
				}

				$res_array = array();
				if ($ult_mov)
				{
					$tmp_array = $this->db->get()->result_array();
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
					$res_array = $this->db->get()->result_array();
				}
				array_push($result, $res_array);
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