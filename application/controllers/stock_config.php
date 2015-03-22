<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Stock_config extends ORM_Controller {

	public $llave_modulo  = 'config_stock';
	public $titulo_modulo = 'Configuracion Stock';

	public function __construct()
	{
		parent::__construct();
		$this->lang->load('stock');

		$this->arr_menu = array(
			'almacen_sap' => array(
				'url'   => $this->uri->segment(1) . '/listado/almacen_sap',
				'texto' => $this->lang->line('stock_config_menu_alm'),
			),
			'tipoalmacen_sap' => array(
				'url'   => $this->uri->segment(1) . '/listado/tipoalmacen_sap',
				'texto' => $this->lang->line('stock_config_menu_tipalm'),
			),
			'proveedor' => array(
				'url'   => $this->uri->segment(1) . '/listado/proveedor',
				'texto' => $this->lang->line('stock_config_menu_proveedores'),
			),
			'almacenes_no_ingresados' => array(
				'url'   => $this->uri->segment(1) . '/almacenes_no_ingresados',
				'texto' => $this->lang->line('stock_config_menu_alm_no_ing'),
			),
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Despliega los almacenes que no están ingresados al sistema
	 *
	 * @param  none
	 * @return none
	 */
	public function almacenes_no_ingresados()
	{
		$almacen = new Almacen_sap;
			$data = array(
				'menu_modulo' => array('menu' => $this->arr_menu, 'mod_selected' => 'almacenes_no_ingresados'),
				'msg_alerta'  => $this->session->flashdata('msg_alerta'),
				'almacenes'   => $almacen->almacenes_no_ingresados(),
			);
		$this->_render_view('stock_sap/almacenes_no_ingresados', $data);

	}




}
/* End of file stock_config.php */
/* Location: ./application/controllers/stock_config.php */