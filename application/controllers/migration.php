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

	public function commands($command = '', $param1 = null)
	{
		$this->load->library('migration');

		if ($command == 'find')
		{
			dbg($this->migration->find_migrations());
		}
		else if ($command == 'current')
		{
			dbg($this->migration->current());
		}
		else if ($command == 'latest')
		{
			dbg($this->migration->latest());
		}
		else if ($command == 'version')
		{
			dbg($this->migration->version($param1));
		}

	}




}

/* End of file migration.php */
/* Location: ./application/controllers/migration.php */
