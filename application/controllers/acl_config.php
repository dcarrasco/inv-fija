<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Acl_config extends ORM_Controller {

	public $llave_modulo  = 'acl_config';
	public $titulo_modulo = 'Configuración Control de Acceso';

	public function __construct()
	{
		parent::__construct();

		$this->arr_menu = array(
			'usuario'        => array('url' => $this->uri->segment(1) . '/listado/usuario', 'texto' => 'Usuarios'),
			'app'            => array('url' => $this->uri->segment(1) . '/listado/app', 'texto' => 'Aplicaciones'),
			'rol'            => array('url' => $this->uri->segment(1) . '/listado/rol', 'texto' => 'Roles'),
			'modulo'         => array('url' => $this->uri->segment(1) . '/listado/modulo', 'texto' => 'Modulos'),
		);

	}


}
/* End of file acl_config.php */
/* Location: ./application/controllers/acl_config.php */