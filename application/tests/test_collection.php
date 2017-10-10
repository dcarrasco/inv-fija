<?php

/**
 * testeo clase collection
 *
 * @category CodeIgniter
 * @package  InventarioFija
 * @author   Daniel Carrasco <danielcarrasco17@gmail.com>
 * @license  MIT License
 * @link     localhost:1520
 */
class test_collection extends test_case {

	public function __construct()
	{
	    parent::__construct();
	}

	public function test_make_collection_con_array()
	{
		$expected = [1,2,3,4,5];

		$this->test(collect([1,2,3,4,5])->all(), $expected);
	}

	public function test_make_collection_con_null()
	{
		$expected = [];

		$this->test(collect(NULL)->all(), $expected);
	}

	public function test_make_collection_con_elemento()
	{
		$expected = ['A'];

		$this->test(collect('A')->all(), $expected);
	}

	public function test_add_items_not_key()
	{
		$expected = [1,2,3,4,5];

		$this->test(collect([1,2,3,4])->add_item(5)->all(), $expected);
	}

	public function test_add_items_key()
	{
		$expected = [0 => 1, 1 => 2, 2 => 3, 3 => 4, 'a' => 5];

		$this->test(collect([1,2,3,4])->add_item(5, 'a')->all(), $expected);
	}

	public function test_delete_item()
	{
		$expected = [0 => 1, 1 => 2, 3 => 4, 4 => 5];

		$this->test(collect([1,2,3,4,5])->delete_item(2)->all(), $expected);
	}

	public function test_get_item()
	{
		$expected = 2;

		$this->test(collect([1,2,3,4,5])->item(1), $expected);
	}

	public function test_get_keys()
	{
		$expected = [0,1,2,3];

		$this->test(collect([1,2,3,4])->keys()->all(), $expected);
	}

	public function test_length()
	{
		$expected = 5;

		$this->test(collect([1,2,3,4,5])->length(), $expected);
	}

	public function test_count()
	{
		$expected = 5;

		$this->test(collect([1,2,3,4,5])->count(), $expected);
	}

	public function test_key_exists()
	{
		$expected = TRUE;

		$this->test(collect([1,2,3,4,5])->key_exists(2), $expected);
	}

	public function test_key_not_exists()
	{
		$expected = FALSE;

		$this->test(collect([1,2,3,4,5])->key_exists(5), $expected);
	}

	public function test_get_existing_element()
	{
		$expected = 3;

		$this->test(collect([1,2,3,4,5])->get(2), $expected);
	}

	public function test_get_non_existing_element()
	{
		$expected = NULL;

		$this->test(collect([1,2,3,4,5])->get(22), $expected);
	}

	public function test_map()
	{
		$expected = [2,4,6,8,10];

		$this->test(collect([1,2,3,4,5])->map(function($el) {return $el*2;})->all(), $expected);
	}

	public function test_is_empty()
	{
		$expected = TRUE;

		$this->test(collect()->is_empty(), $expected);
	}

	public function test_not_is_empty()
	{
		$expected = FALSE;

		$this->test(collect([1,2,3,4,5])->is_empty(), $expected);
	}

	public function test_has()
	{
		$expected = TRUE;

		$this->test(collect([1,2,3,4,5])->has(4), $expected);
	}

	public function test_contains()
	{
		$expected = TRUE;

		$this->test(collect([1,2,3,4,5])->contains(5), $expected);
	}

	public function test_implode()
	{
		$expected = '1-2-3-4-5';

		$this->test(collect([1,2,3,4,5])->implode('-'), $expected);
	}

	public function test_filter()
	{
		$expected = [1,2];

		$this->test(collect([1,2,3,4,5])->filter(function($el) {return $el<=2;})->all(), $expected);
	}



}
