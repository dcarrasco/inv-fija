<?php

use test\test_case;

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_assertions extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function xtest_equals()
	{
		$this->assert_equals([1,2,3], [4,5,6]);
		$this->assert_equals((object) [1,2,3], (object) [4,5,6]);
		$this->assert_equals('aa', 'bb');
		$this->assert_equals(11, 22);
		$this->assert_equals(true, false);
		$this->assert_equals(false, true);
		$this->assert_equals('11', NULL);
	}

	public function xtest_is_string()
	{
		$this->assert_is_string([1,2,3]);
		$this->assert_is_string((object) [1,2,3]);
		$this->assert_is_string(11);
		$this->assert_is_string(true);
		$this->assert_is_string(false);
		$this->assert_is_string(NULL);
	}

	public function xtest_is_object()
	{
		$this->assert_is_object([1,2,3]);
		$this->assert_is_object('aa');
		$this->assert_is_object(11);
		$this->assert_is_object(true);
		$this->assert_is_object(false);
		$this->assert_is_object(NULL);
	}

	public function xtest_is_array()
	{
		$this->assert_is_array((object) [1,2,3]);
		$this->assert_is_array('aa');
		$this->assert_is_array(11);
		$this->assert_is_array(true);
		$this->assert_is_array(false);
		$this->assert_is_array(NULL);
	}

	public function xtest_is_int()
	{
		$this->assert_is_int([1,2,3]);
		$this->assert_is_int((object) [1,2,3]);
		$this->assert_is_int('aa');
		$this->assert_is_int(true);
		$this->assert_is_int(false);
		$this->assert_is_int(NULL);
	}

	public function xtest_true()
	{
		$this->assert_true([1,2,3]);
		$this->assert_true((object) [1,2,3]);
		$this->assert_true('aa');
		$this->assert_true(11);
		$this->assert_true(false);
		$this->assert_true(NULL);
	}

	public function xtest_false()
	{
		$this->assert_false([1,2,3]);
		$this->assert_false((object) [1,2,3]);
		$this->assert_false('aa');
		$this->assert_false(11);
		$this->assert_false(true);
		$this->assert_false(NULL);
	}

	public function xtest_null()
	{
		$this->assert_null([1,2,3]);
		$this->assert_null((object) [1,2,3]);
		$this->assert_null('aa');
		$this->assert_null(11);
		$this->assert_null(true);
		$this->assert_null(false);
	}

	public function xtest_empty()
	{
		$this->assert_empty([1,2,3]);
		$this->assert_empty((object) [1,2,3]);
		$this->assert_empty('aa');
		$this->assert_empty(11);
		$this->assert_empty(true);
	}

	public function xtest_not_empty()
	{
		$this->assert_not_empty([]);
		$this->assert_not_empty((object) []);
		$this->assert_not_empty('');
		$this->assert_not_empty(0);
		$this->assert_not_empty(false);
		$this->assert_not_empty(NULL);
	}

	public function xtest_count()
	{
		$this->assert_count([1,2,3], 4);
		$this->assert_count((object) [1,2,3], 4);
		$this->assert_count('aa', 3);
		$this->assert_count(0,3);
		$this->assert_count(false,3);
		$this->assert_count(NULL,3);
	}

	public function xtest_contains()
	{
		$this->assert_contains('aa', 'b');
	}

}
