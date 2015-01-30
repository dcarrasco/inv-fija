<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Despachos_model extends CI_Model {

	public $limite_facturas = 5;


	public function __construct()
	{
		parent::__construct();
	}


	// --------------------------------------------------------------------

	/**
	 *
	 * @return array Arreglo con los ruts de los retail
	 */
	public function get_combo_rut_retail()
	{
		$arr_result = array();

		$this->db
			->select('rut as llave')
			->select_max('des_bodega + \' (\' + rut + \')\'', 'valor')
			->group_by('rut')
			->order_by('valor')
			->from($this->config->item('bd_despachos_pack'));

		$arr_result = $this->db->get()->result_array();

		return form_array_format($arr_result);
	}



	// --------------------------------------------------------------------

	/**
	 *
	 * @return array Arreglo con los tipos de inventiaro
	 */
	public function get_listado_ultimas_facturas($rut = NULL, $modelos = NULL)
	{
		if ($rut AND $modelos)
		{
			$arr_result = array();
			$arr_modelos = explode("\r\n", $modelos);

			foreach ($arr_modelos as $modelo)
			{
				$arr_result[$modelo] = $this->get_ultimas_facturas($rut, $modelo);
			}

			return $arr_result;
		}
	}



	// --------------------------------------------------------------------

	/**
	 *
	 * @return array Arreglo con los tipos de inventiaro
	 */
	public function get_ultimas_facturas($rut = NULL, $modelo = NULL)
	{
		if ($rut AND $modelo)
		{
			$this->db
				->limit($this->limite_facturas)
				->where('rut', $rut)
				->like('texto_breve_material', $modelo)
				->from($this->config->item('bd_despachos_pack'))
				->order_by('fecha', 'desc');

			$arr_result = $this->db->get()->result_array();


			$arr_facturas = array();

			$arr_facturas['datos'] = array();
			$arr_campos_enc = array('operador', 'operador_c', 'rut', 'des_bodega', 'cod_cliente');
			foreach($arr_campos_enc as $campo)
			{
				$arr_facturas['datos'][$campo] = '';
			}

			$arr_campos_det = array('alm', 'cmv', 'n_doc', 'referencia', 'cod_sap', 'texto_breve_material', 'lote', 'fecha', 'cant');
			for($i = 0; $i < $this->limite_facturas; $i++)
			{
				$arr_facturas['factura_' . $i] = array();
				foreach($arr_campos_det as $campo)
				{
					$arr_facturas['factura_' . $i][$campo] = '';
				}
			}

			$i = 0;
			foreach($arr_result as $factura)
			{
				if ($i == 0)
				{
					foreach($arr_campos_enc as $campo)
					{
						$arr_facturas['datos'][$campo] = $factura[$campo];
					}
				}

				foreach($arr_campos_det as $campo)
				{
					$arr_facturas['factura_' . $i][$campo] = $factura[$campo];
				}

				$i++;
			}

			return $arr_facturas;
		}
	}

}
/* End of file despachos_model.php */
/* Location: ./application/models/despachos_model.php */