<?php


class tests extends Controller_base {

	public function __construct()
	{
		parent::__construct();

	}

	public function index()
	{
		$test = new test_case();
		$test->resumen_all_files();
	}

	public function detalle()
	{
		$test = new test_case();
		$test->all_files();
	}

	public function help()
	{
		$test = new test_case();
		$test->print_help();
	}

	public function coverage()
	{
		$test = new test_case();
		$test->resumen_all_files();
		$test->print_coverage();
	}

	public function rake($detalle = '')
	{
		$test = new test_case();
		$test->rake($detalle);
	}

}