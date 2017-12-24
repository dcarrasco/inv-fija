<?php

use test\test_case;


class tests extends Controller_base {

	protected $test;

	public function __construct()
	{
		parent::__construct();

		$this->test = new test_case();
	}

	public function index()
	{
		$this->test->resumen_all_files();
	}

	public function detalle()
	{
		$this->test->all_files();
	}

	public function help()
	{
		$this->test->print_cli_help();
	}

	public function coverage()
	{
		$this->test->resumen_all_files();
		$this->test->print_coverage();
	}

	public function rake($detalle = '')
	{
		$this->test->rake($detalle);
	}

}
