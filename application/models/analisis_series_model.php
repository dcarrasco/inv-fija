<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analisis_series_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}


	private function _series_list_to_array($series = string, $tipo = 'SAP')
	{
		$arr_series = array();
		$series = str_replace(" ", "", $series);
		$arr_series = preg_grep('/[\d]+/', explode("\r\n", $series));
		foreach ($arr_series as $k => $v)
		{
			$serie_temp = $v;
			// Modificaciones de formato SAP
			if ($tipo == 'SAP')
			{
				$serie_temp = preg_replace('/^01/', '1', $serie_temp);
				$arr_series[$k] = (strlen($serie_temp) == '19') ? substr($serie_temp, 1, 18) : $serie_temp;
			}
			// Modificaciones de formato SCL
			else
			{
				$serie_temp = preg_replace('/^1/', '01', $serie_temp);
			}
		}

		return $arr_series;
	}


	private function _get_dv_imei($serie = '')
	{
		$serie15 = '';

		if (strlen($serie) == 14 AND substr($serie, 0, 1) == '1')
		{
			$serie15 = '0' . $serie;
		}

		if (strlen($serie) == 15)
		{
			$serie15 = $serie;
		}

		$serie14 = substr($serie15, 0, 14);

		$sum_dv = 0;
		foreach(str_split($serie14) as $i => $n)
		{
			$sum_dv += ($i %2 !== 0) ? (($n*2>9)?$n*2-9:$n*2): $n;
		}

		return $serie14 . (10 - $sum_dv % 10);

	}


	public function get_historia($series = string)
	{
		$result = array();
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			$this->db->limit(3000);
			$this->db->select('mov_hist.*, alm1.des_almacen as des_alm, alm2.des_almacen as des_rec, cp_usuarios.nom_usuario, cp_cmv.*, convert(varchar(20),fec_entrada_doc,120) as fecha_entrada_doc, convert(varchar(20),fecha,103) as fec');
			$this->db->from('bd_logistica..mov_hist');
			$this->db->join('bd_logistica..cp_cmv', 'mov_hist.cmv=cp_cmv.cmv', 'left');
			$this->db->join('bd_logistica..cp_almacenes as alm1', "alm1.centro=mov_hist.ce and mov_hist.alm=alm1.cod_almacen", 'left');
			$this->db->join('bd_logistica..cp_almacenes as alm2', "alm2.centro=mov_hist.ce and mov_hist.rec=alm2.cod_almacen", 'left');
			$this->db->join('bd_logistica..cp_usuarios', 'mov_hist.usuario=cp_usuarios.usuario', 'left');
			$this->db->order_by('serie','asc');
			$this->db->order_by('fecha','asc');
			$this->db->order_by('fec_entrada_doc','asc');
			$this->db->where_in('serie', $arr_series);

			return $this->db->get()->result_array();
		}
	}


	public function get_despacho($series = string)
	{
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			$this->db->limit(1000);
			$this->db->select('convert(varchar(20),fecha,120) as fecha_despacho, *');
			$this->db->from('bd_logistica..despachos_sap');
			$this->db->where_in('n_serie', $arr_series);

			return $this->db->get()->result_array();
		}
	}


	public function get_stock_sap($series = string)
	{
		$result = array();
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			$this->db->limit(1000);
			$this->db->select('bd_stock_sap.*, convert(varchar(20), fecha_stock, 103) as fecha, convert(varchar(20), modificado_el, 103) as modif_el, cp_almacenes.des_almacen, cp_usuarios.*, al_articulos.*');
			$this->db->from('bd_logistica..bd_stock_sap');
			$this->db->join('bd_logistica..al_articulos', 'bd_stock_sap.material = al_articulos.cod_articulo', 'left');
			$this->db->join('bd_logistica..cp_almacenes', 'bd_stock_sap.almacen=cp_almacenes.cod_almacen and bd_stock_sap.centro=cp_almacenes.centro', 'left');
			$this->db->join('bd_logistica..cp_usuarios', 'bd_stock_sap.modificado_por=cp_usuarios.usuario', 'left');
			$this->db->where_in('serie', $arr_series);

			return $this->db->get()->result_array();
		}
	}

	public function get_stock_scl($series = string) {
		$result = array();
		$arr_series = $this->_series_list_to_array($series);

		if (count($arr_series) > 0)
		{
			$this->db->limit(1000);
			$this->db->select('bd_stock_scl.*, convert(varchar(20), FECHA_STOCK, 103) as FECHA, al_bodegas.*, al_tipos_bodegas.*, al_articulos.*, al_tipos_stock.*, al_usos.*, al_estados.*');
			$this->db->from('bd_logistica..bd_stock_scl');
			$this->db->join('bd_logistica..al_bodegas', 'cast(bd_stock_scl.cod_bodega as varchar(10)) = al_bodegas.cod_bodega', 'left');
			$this->db->join('bd_logistica..al_tipos_bodegas', 'cast(bd_stock_scl.tip_bodega as varchar(10)) = al_tipos_bodegas.tip_bodega', 'left');
			$this->db->join('bd_logistica..al_articulos', 'cast(bd_stock_scl.cod_articulo as varchar(10)) = al_articulos.cod_articulo', 'left');
			$this->db->join('bd_logistica..al_tipos_stock', 'bd_stock_scl.tip_stock = al_tipos_stock.tipo_stock', 'left');
			$this->db->join('bd_logistica..al_estados', 'bd_stock_scl.cod_estado = al_estados.cod_estado', 'left');
			$this->db->join('bd_logistica..al_usos', 'bd_stock_scl.cod_uso = al_usos.cod_uso', 'left');
			$this->db->where_in('serie_sap', $arr_series);

			return $this->db->get()->result_array();
		}

		return $result;
	}

	public function get_trafico($series = "")
	{
		$result = array();
		$arr_series = $this->_series_list_to_array($series, 'SCL');

		foreach ($arr_series as $serie)
		{
			$serie_orig = $serie;
			$serie_cero = substr($serie,0,14) . "0";

			$row = $this->db->query('select distinct ano as ano, mes as mes from bd_controles.dbo.trafico_dias_procesados where ano*100+mes in (select max(ano*100+mes) from bd_controles.dbo.trafico_dias_procesados)')->row();
			$ano = $row->ano;
			$mes = $row->mes;
			$this->db->select('t.*, a.*, c.*, b.*, convert(varchar(20), a.fec_alta, 103) as fecha_alta, convert(varchar(20), a.fec_baja, 103) as fecha_baja');
			$this->db->from('bd_logistica..trafico_mes t');
			$this->db->join('bd_controles..trafico_abocelamist a', 't.celular = a.num_celular', 'left');
			$this->db->join('bd_controles..trafico_clientes c', 'a.cod_cliente = c.cod_cliente', 'left');
			$this->db->join('bd_controles..trafico_causabaja b', 'a.cod_causabaja = b.cod_causabaja', 'left');
			//$this->db->where(array('ano' => $row->ano, 'mes' => $row->mes, 'imei'=> $serie));
			$this->db->where(array('ano' => $row->ano, 'mes' => $row->mes));
			$this->db->where_in('imei', array($serie_orig, $serie_cero));
			$this->db->order_by('imei, fec_alta');
			array_push($result, $this->db->get()->result_array());
		}

		return $result;
	}

	public function get_meses_trafico()
	{
		$resultado = array();

		$this->db
			->distinct()
			->select('100*ano+mes as mes')
			->from('bd_controles..trafico_dias_procesados')
			->order_by('mes desc');

		foreach($this->db->get()->result_array() as $res)
		{
			$resultado[$res['mes']] = substr($res['mes'],0,4) . "-" . substr($res['mes'],4,2);
		}
		return $resultado;
	}

	public function get_trafico_mes($series = "", $meses = array())
	{
		$result = array();
		$arr_series = $this->_series_list_to_array($series, 'SCL');

		foreach ($arr_series as $serie)
		{
			$serie_orig = $serie;
			$serie_cero = substr($serie,0,14) . "0";

			foreach($meses as $mes)
			{
				$mes_ano = substr($mes,0,4);
				$mes_mes = substr($mes,4,2);
				$this->db->select('t.*, a.*, c.*, b.*, convert(varchar(20), a.fec_alta, 103) as fecha_alta, convert(varchar(20), a.fec_baja, 103) as fecha_baja');
				$this->db->from('bd_logistica..trafico_mes t');
				$this->db->join('bd_controles..trafico_abocelamist a', 't.celular = a.num_celular', 'left');
				$this->db->join('bd_controles..trafico_clientes c', 'a.cod_cliente = c.cod_cliente', 'left');
				$this->db->join('bd_controles..trafico_causabaja b', 'a.cod_causabaja = b.cod_causabaja', 'left');
				//$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes, 'imei'=> $serie, 'cod_situacion<>' => 'BAA'));
				$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes, 'cod_situacion<>' => 'BAA'));
				$this->db->where_in('imei', array($serie_orig, $serie_cero));
				$this->db->order_by('imei, fec_alta');
				foreach($this->db->get()->result_array() as $reg)
				{
					$result[$serie][$reg['celular']][$mes] = $reg;
				}
			}
		}
		return $result;
	}

	public function get_trafico_mes2($series = "", $str_meses = "", $tipo = 'imei')
	{
		$result = array();
		$serie = trim($series);

		if ($tipo == 'imei' AND substr($serie,0,1) == '1' AND strlen($serie) == 14)
		{
			$serie = "0" . $serie;
		}

		if ($tipo == 'imei' AND strlen($serie) == 15)
		{
			$serie = substr($serie, 0, 14) . '0';
		}

		if ($serie != "")
		{
			$meses = array();
			$meses = explode("-", $str_meses);

			foreach($meses as $mes)
			{
				$mes_ano = (int) substr($mes,0,4);
				$mes_mes = (int) substr($mes,4,2);

				// recupera datos de trafico
				$this->db->from('bd_logistica..trafico_mes');
				$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes));

				if ($tipo == 'imei')
				{
					$this->db->where('imei', $serie);
				}
				else
				{
					$this->db->where('celular', $serie);
				}

				foreach($this->db->get()->result_array() as $reg)
				{
					$result[$this->_get_dv_imei($reg['imei'])][$reg['celular']][$mes] = ($reg['seg_entrada']+$reg['seg_salida'])/60;
				}
				//dbg($result);
			}
		}

		// recupera datos comerciales
		$result_final = array();

		foreach($result as $imei => $datos_imei)
		{
			foreach($datos_imei as $cel => $datos_cel)
			{
				$num_celular = (string) $cel;
				$result_temp = $this->_get_datos_comerciales($imei, $num_celular);
				$result_temp['celular'] = $num_celular;
				$result_temp['imei'] = $imei;

				foreach($datos_cel as $mes => $trafico)
				{
					$result_temp[$mes] = fmt_cantidad($trafico, 1);
				}

				array_push($result_final, $result_temp);
			}
		}

		return $result_final;
	}

	private function _get_datos_comerciales($imei = '', $celular = '')
	{
		$maxfecha = $this->db
			->select('max(convert(varchar(20), fec_alta, 112)) as fec_alta')
			->where('num_celular', $celular)
			->get('bd_controles..trafico_abocelamist')
			->row()
			->fec_alta;

		return $this->db
			->select('a.tipo, a.num_celular, a.cod_situacion')
			->select('convert(varchar(20), a.fec_alta, 103) as fecha_alta')
			->select('convert(varchar(20), a.fec_baja, 103) as fecha_baja')
			->select('c.num_ident as rut')
			->select('c.nom_cliente + \' \' + c.ape1_cliente + \' \' + c.ape2_cliente as nombre')
			->from('bd_controles..trafico_abocelamist a', 't.celular = a.num_celular', 'left')
			->join('bd_controles..trafico_clientes c', 'a.cod_cliente = c.cod_cliente', 'left')
			->where(array('a.num_celular' => $celular, 'a.fec_alta' => $maxfecha))
			->get()->row_array();
	}




}

/* End of file analisis_series_model.php */
/* Location: ./application/models/analisis_series_model.php */