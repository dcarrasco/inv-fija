<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Analisis_series_model extends CI_Model {


	public function __construct()
	{
		parent::__construct();
	}

	function get_historia($series = string)
	{
		$result = array();
		$arr_series = explode("\n", $series);
		foreach ($arr_series as $serie) {
			$serie = trim($serie);
			if (substr($serie,0,2) == '01')
			{
			$serie = substr($serie, 1);
			}

			if ($serie != "") {
				$this->db->limit(100);
				$this->db->select('mov_hist.*, alm1.des_almacen as des_alm, alm2.des_almacen as des_rec, cp_usuarios.nom_usuario, cp_cmv.*, convert(varchar(20),fec_entrada_doc,120) as fecha_entrada_doc, convert(varchar(20),fecha,103) as fec');
				$this->db->from('bd_logistica..mov_hist');
				$this->db->join('bd_logistica..cp_cmv', 'mov_hist.cmv=cp_cmv.cmv', 'left');
				$this->db->join('bd_logistica..cp_almacenes as alm1', "alm1.centro=mov_hist.ce and mov_hist.alm=alm1.cod_almacen", 'left');
				$this->db->join('bd_logistica..cp_almacenes as alm2', "alm2.centro=mov_hist.ce and mov_hist.rec=alm2.cod_almacen", 'left');
				$this->db->join('bd_logistica..cp_usuarios', 'mov_hist.usuario=cp_usuarios.usuario', 'left');
				$this->db->order_by('fecha','asc');
				$this->db->order_by('fec_entrada_doc','asc');
				$this->db->where(array('serie' => $serie));
				array_push($result, $this->db->get()->result_array());				}
		}

		return $result;
	}

	function get_despacho($series = string)
	{
		$result = array();
		$arr_series = explode("\n", $series);
		foreach ($arr_series as $serie) {
			$serie = trim($serie);
			if (substr($serie,0,1) == '1')
			{
				$serie = '0' . $serie;
			}

			if ($serie != "") {
				$this->db->limit(100);
				$this->db->select('convert(varchar(20),fecha,120) as fecha_desp, *');
				$this->db->from('bd_logistica..despachos_sap');
				$this->db->where(array('serie' => $serie));
				array_push($result, $this->db->get()->result_array());				}
		}

		return $result;
	}

	function get_stock_sap($series = string) {
		$result = array();
		$arr_series = explode("\n", $series);
		foreach ($arr_series as $serie) {
			$serie = trim($serie);
			if (substr($serie,0,2) == '01')
			{
			$serie = substr($serie, 1);
			}

			if ($serie != "") {
				$this->db->limit(100);
				$this->db->select('bd_stock_sap.*, convert(varchar(20), fecha_stock, 103) as fecha, convert(varchar(20), modificado_el, 103) as modif_el, cp_almacenes.*, cp_usuarios.*, al_articulos.*');
				$this->db->from('bd_logistica..bd_stock_sap');
				$this->db->join('bd_logistica..al_articulos', 'bd_stock_sap.material = al_articulos.cod_articulo', 'left');
				$this->db->join('bd_logistica..cp_almacenes', 'bd_stock_sap.almacen=cp_almacenes.cod_almacen and bd_stock_sap.centro=cp_almacenes.centro', 'left');
				$this->db->join('bd_logistica..cp_usuarios', 'bd_stock_sap.modificado_por=cp_usuarios.usuario', 'left');
				$this->db->where(array('serie' => $serie));
				array_push($result, $this->db->get()->result_array());				}
		}
		return $result;
	}

	function get_stock_scl($series = string) {
		$result = array();
		$arr_series = explode("\n", $series);
		foreach ($arr_series as $serie) {
			$serie = trim($serie);
			if (substr($serie,0,2) == '01')
			{
			$serie = substr($serie, 1);
			}

			if ($serie != "") {
				$this->db->limit(100);
				$this->db->select('bd_stock_scl.*, convert(varchar(20), FECHA_STOCK, 103) as FECHA, al_bodegas.*, al_tipos_bodegas.*, al_articulos.*, al_tipos_stock.*, al_usos.*, al_estados.*');
				$this->db->from('bd_logistica..bd_stock_scl');
				$this->db->join('bd_logistica..al_bodegas', 'cast(bd_stock_scl.cod_bodega as varchar(10)) = al_bodegas.cod_bodega', 'left');
				$this->db->join('bd_logistica..al_tipos_bodegas', 'cast(bd_stock_scl.tip_bodega as varchar(10)) = al_tipos_bodegas.tip_bodega', 'left');
				$this->db->join('bd_logistica..al_articulos', 'cast(bd_stock_scl.cod_articulo as varchar(10)) = al_articulos.cod_articulo', 'left');
				$this->db->join('bd_logistica..al_tipos_stock', 'bd_stock_scl.tip_stock = al_tipos_stock.tipo_stock', 'left');
				$this->db->join('bd_logistica..al_estados', 'bd_stock_scl.cod_estado = al_estados.cod_estado', 'left');
				$this->db->join('bd_logistica..al_usos', 'bd_stock_scl.cod_uso = al_usos.cod_uso', 'left');
				$this->db->where(array('serie_sap' => $serie));
				array_push($result, $this->db->get()->result_array());				}
		}
		return $result;
	}

	public function get_trafico($series = "")
	{
		$result = array();
		$arr_series = explode("\n", $series);
		foreach ($arr_series as $serie)
		{
			$serie = trim($serie);
			if (substr($serie,0,1) == '1' and strlen($serie) == 14)
			{
				$serie = "0" . $serie;
			}

			if ($serie != "")
			{
				$serie = substr($serie,0,14) . "0";

				$row = $this->db->query('select distinct ano as ano, mes as mes from bd_controles.dbo.trafico_dias_procesados where ano*100+mes in (select max(ano*100+mes) from bd_controles.dbo.trafico_dias_procesados)')->row();
				$ano = $row->ano;
				$mes = $row->mes;
				$this->db->select('t.*, a.*, c.*, b.*, convert(varchar(20), a.fec_alta, 103) as fecha_alta, convert(varchar(20), a.fec_baja, 103) as fecha_baja');
				$this->db->from('bd_logistica..trafico_mes t');
				$this->db->join('bd_controles..trafico_abocelamist a', 't.celular = a.num_celular', 'left');
				$this->db->join('bd_controles..trafico_clientes c', 'a.cod_cliente = c.cod_cliente', 'left');
				$this->db->join('bd_controles..trafico_causabaja b', 'a.cod_causabaja = b.cod_causabaja', 'left');
				$this->db->where(array('ano' => $row->ano, 'mes' => $row->mes, 'imei'=> $serie));
				$this->db->order_by('imei, fec_alta');
				array_push($result, $this->db->get()->result_array());
			}
		}
		return $result;
	}

	public function get_meses_trafico()
	{
		$this->db->distinct();
		$this->db->select('100*ano+mes as mes');
		$this->db->from('bd_controles..trafico_dias_procesados');
		$this->db->order_by('mes');
		$resultado = array();
		foreach($this->db->get()->result_array() as $res)
		{
			$resultado[$res['mes']] = substr($res['mes'],0,4) . "/" . substr($res['mes'],4,2);
		}
		return $resultado;
	}

	public function get_trafico_mes($series = "", $meses = array())
	{
		$result = array();
		$arr_series = explode("\n", $series);
		foreach ($arr_series as $serie)
		{
			$serie = trim($serie);
			if (substr($serie,0,1) == '1' and strlen($serie) == 14)
			{
				$serie = "0" . $serie;
			}
			if ($serie != "")
			{
				$serie = substr($serie,0,14) . "0";

				foreach($meses as $mes)
				{
					$mes_ano = substr($mes,0,4);
					$mes_mes = substr($mes,4,2);
					$this->db->select('t.*, a.*, c.*, b.*, convert(varchar(20), a.fec_alta, 103) as fecha_alta, convert(varchar(20), a.fec_baja, 103) as fecha_baja');
					$this->db->from('bd_logistica..trafico_mes t');
					$this->db->join('bd_controles..trafico_abocelamist a', 't.celular = a.num_celular', 'left');
					$this->db->join('bd_controles..trafico_clientes c', 'a.cod_cliente = c.cod_cliente', 'left');
					$this->db->join('bd_controles..trafico_causabaja b', 'a.cod_causabaja = b.cod_causabaja', 'left');
					$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes, 'imei'=> $serie, 'cod_situacion<>' => 'BAA'));
					$this->db->order_by('imei, fec_alta');
					foreach($this->db->get()->result_array() as $reg)
					{
						$result[$serie][$reg['celular']][$mes] = $reg;
					}
				}
			}
		}
		return $result;
	}

	public function get_trafico_mes2($series = "", $str_meses = "")
	{
		$result = array();
		$serie = trim($series);
		if (substr($serie,0,1) == '1' and strlen($serie) == 14)
		{
			$serie = "0" . $serie;
		}
		if ($serie != "")
		{
			$serie = substr($serie,0,14) . "0";
			$meses = array();
			$meses = explode("-", $str_meses);

			foreach($meses as $mes)
			{
				$mes_ano = substr($mes,0,4);
				$mes_mes = substr($mes,4,2);

				// recupera datos de trafico
				$this->db->from('bd_logistica..trafico_mes');
				$this->db->where(array('ano' => $mes_ano, 'mes' => $mes_mes, 'imei'=> $serie));
				foreach($this->db->get()->result_array() as $reg)
				{
					$result[$series][$reg['celular']][$mes] = ($reg['seg_entrada']+$reg['seg_salida'])/60;
				}
				//print_r($result);
			}
		}

		// recupera datos comerciales

		$arr_maxfecha = array();
		$arr_datoscom = array();
		$result_final = array();
		$i = 0;
		foreach($result as $imei => $datos_imei)
		{
			foreach($datos_imei as $cel => $datos_cel)
			{
				$arr_maxfecha = array();
				$arr_datoscom = array();
				$num_celular = (string) $cel;

				$this->db->select('max(convert(varchar(20), fec_alta, 112)) as fec_alta')->where('num_celular', $num_celular);
				$arr_maxfecha = $this->db->get('bd_controles..trafico_abocelamist')->row_array();

				$this->db->select('a.tipo, a.num_celular, a.cod_situacion, convert(varchar(20), a.fec_alta, 103) as fecha_alta, convert(varchar(20), a.fec_baja, 103) as fecha_baja');
				$this->db->select('c.num_ident, c.nom_cliente + \' \' + c.ape1_cliente + \' \' + c.ape2_cliente as nombre');
				$this->db->from('bd_controles..trafico_abocelamist a', 't.celular = a.num_celular', 'left');
				$this->db->join('bd_controles..trafico_clientes c', 'a.cod_cliente = c.cod_cliente', 'left');
				$this->db->where(array('a.num_celular' => $num_celular, 'a.fec_alta' => $arr_maxfecha['fec_alta']));
				$arr_datoscom[$imei][$cel] = $this->db->get()->row_array();

				$result_final[$i] = array(
											'imei' => $series,
											'celular' => $cel,
											'rut'     => $arr_datoscom[$imei][$cel]['num_ident'],
											'nombre'  => $arr_datoscom[$imei][$cel]['nombre'],
											'cod_situacion' => $arr_datoscom[$imei][$cel]['cod_situacion'],
											'fecha_alta'    => $arr_datoscom[$imei][$cel]['fecha_alta'],
											'fecha_baja'    => $arr_datoscom[$imei][$cel]['fecha_baja'],
											'tipo'          => $arr_datoscom[$imei][$cel]['tipo']
										);
				foreach($datos_cel as $mes => $trafico)
				{
					$result_final[$i][$mes] = number_format($trafico,1,'.',',');
				}
				$i += 1;
			}
			//print_r($arr_datoscom);
		}

		return $result_final;
	}




}

/* End of file analisis_series_model.php */
/* Location: ./application/models/analisis_series_model.php */