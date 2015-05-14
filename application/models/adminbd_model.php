<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Adminbd_model extends CI_Model {

	private $DB1;


	public function __construct()
	{
		parent::__construct();

		$this->DB1 = $this->load->database("adminbd", TRUE);

	}


	// --------------------------------------------------------------------
	public function get_running_queries()
	{
		$arr_final = array();

		$arr_users = array();
		$arr_users_tmp = $this->DB1
			->query("sp_who2 \"active\"")
			->result_array();

		foreach ($arr_users_tmp as $row)
		{
			if (trim($row['Login']) != 'sa' AND trim($row['Login']) != '')
			{
				if (trim($row['Login']) != 'patripio' OR trim($row['ProgramName']) != 'Apache HTTP Server')
				{
					$arr_users[trim($row['SPID'])] = $row;
				}
			}
		}

		$arr_queries = array();
		$arr_queries_tmp = $this->DB1
			->query("SELECT  * FROM sys.dm_exec_requests CROSS APPLY sys.dm_exec_sql_text(sql_handle)")
			->result_array();

		foreach ($arr_queries_tmp as $row)
		{
			$arr_queries[trim($row['session_id'])] = $row;
		}

		foreach($arr_users as $key => $val)
		{
			$arr_final[$key] = array_merge($arr_users[$key], $arr_queries[$key]);
		}

		return $arr_final;
	}



}
/* End of file adminbd_model.php */
/* Location: ./application/models/adminbd_model.php */