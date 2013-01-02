<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Migration extends CI_Controller {



	public function __construct()
	{
		parent::__construct();
	}


	public function migrate()
	{
		$this->load->library('migration');

	    if ( ! $this->migration->current())
	    {
    		show_error($this->migration->error_string());
    	}

	}




}

/* End of file migrationx.php */
/* Location: ./application/controllers/migrationx.php */
