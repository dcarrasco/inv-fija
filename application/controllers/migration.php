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

	public function version($version = -1)
	{
		$this->load->library('migration');

		if ($version > -1)
		{
			$this->migration->version($version);
		}

	}




}

/* End of file migrationx.php */
/* Location: ./application/controllers/migrationx.php */
