<?php


class tests extends CI_Controller {

	public function __construct()
	{
	    parent::__construct();

	}

	public function index()
	{
	    $test = new test_case();
	    $test->all_files();
	}

}
