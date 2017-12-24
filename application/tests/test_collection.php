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

class test_collection extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_make_collection()
	{
		$this->assert_equals(collect([1,2,3,4,5]), 'is_object');
		$this->assert_equals(collect([1,2,3,4,5])->all(), [1,2,3,4,5]);
		$this->assert_equals(collect()->all(), []);
		$this->assert_equals(collect([])->all(), []);
		$this->assert_equals(collect(NULL)->all(), []);
		$this->assert_equals(collect('A')->all(), ['A']);
	}

	public function test_add_items()
	{
		$this->assert_equals(collect([1,2,3,4])->add_item(5)->all(), [1,2,3,4,5]);

		$this->assert_equals(collect([1,2,3,4])->add_item(5, 'a')->all(),
			[0 => 1, 1 => 2, 2 => 3, 3 => 4, 'a' => 5]
		);
	}

	public function test_delete_item()
	{
		$this->assert_equals(collect([1,2,3,4,5])->delete_item(2)->all(), [0=>1, 1=>2, 3=>4, 4=>5]);
		$this->assert_equals(collect([1,2,3,4,5])->delete_item('a')->all(), [1,2,3,4,5]);
	}

	public function test_item()
	{
		$this->assert_equals(collect([1,2,3,4,5])->item(1), 2);
		$this->assert_equals(collect(['a'=>1,'b'=>2,'c'=>3])->item('b'), 2);
		$this->assert_null(collect([1,2,3,4,5])->item('A'));
	}

	public function test_get_keys()
	{
		$this->assert_equals(collect([1,2,3,4])->keys()->all(), [0,1,2,3]);
		$this->assert_equals(collect(['a'=>1,'b'=>2,'aa'=>3,'bb'=>4])->keys()->all(), ['a','b','aa','bb']);
	}

	public function test_length()
	{
		$this->assert_equals(collect([1,2,3,4,5])->length(), 5);
	}

	public function test_count()
	{
		$this->assert_equals(collect([1,2,3,4,5])->count(), 5);
	}

	public function test_key_exists()
	{
		$this->assert_true(collect([1,2,3,4,5])->key_exists(2));
		$this->assert_true(collect(['a'=>1,'b'=>2,'aa'=>3,'bb'=>4])->key_exists('aa'));
		$this->assert_false(collect([1,2,3,4,5])->key_exists(5));
	}

	public function test_get_element()
	{
		$this->assert_equals(collect([1,2,3,4,5])->get(2), 3);
		$this->assert_null(collect([1,2,3,4,5])->get(5));
	}

	public function test_map()
	{
		$this->assert_equals(collect([1,2,3,4,5])->map(function($el) {return $el*2;})->all(), [2,4,6,8,10]);
	}

	public function test_is_empty()
	{
		$this->assert_true(collect()->is_empty());
		$this->assert_true(collect(NULL)->is_empty());
		$this->assert_true(collect([])->is_empty());
		$this->assert_false(collect([1,2,3,4,5])->is_empty());
		$this->assert_false(collect('a')->is_empty());
	}

	public function test_has()
	{
		$this->assert_true(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->has('e'));
		$this->assert_false(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->has('f'));
	}

	public function test_contains()
	{
		$this->assert_true(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->contains(5));
		$this->assert_false(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->contains(6));
	}

	public function test_implode()
	{
		$this->assert_equals(collect([1,2,3,4,5])->implode('-'), '1-2-3-4-5');
	}

	public function test_filter()
	{
		$this->assert_equals(collect([1,2,3,4,5])->filter(function($el) {return $el<=2;})->all(), [1,2]);
		$this->assert_equals(collect([1,2,3,4,5])->filter(function($el) {return $el>20;}), 'is_object');
		$this->assert_equals(collect([1,2,3,4,5])->filter(function($el) {return $el>20;})->all(), []);
	}

	public function test_sum()
	{
		$this->assert_equals(collect([1,2,3,4,5])->sum(), 15);
	}

	public function test_max()
	{
		$this->assert_equals(collect([1,4,3,5,0,2])->max(), 5);
	}

	public function test_concat()
	{
		$this->assert_equals(collect([1,2,3,4,5])->concat(function($elem) {return $elem.'-';}), '1-2-3-4-5-');
	}

	public function test_sort()
	{
		$this->assert_equals(collect([2,1,4,3,5])->sort()->all(), [1=>1, 0=>2, 3=>3, 2=>4, 4=>5]);
	}

	public function test_flatten()
	{
		$this->assert_equals(collect([[1,2,3], [4,5,6], [7,8,9,10]])->flatten()->all(), [1,2,3,4,5,6,7,8,9,10]);
	}

	public function test_merge()
	{
		$this->assert_equals(collect([1,2,3,4,5,6])->merge([7,8,9,10])->all(), [1,2,3,4,5,6,7,8,9,10]);
		$this->assert_equals(collect(['a'=>1,'b'=>2,'c'=>33])->merge(['c'=>3,'d'=>4])->all(),
			['a'=>1, 'b'=>2, 'c'=>3, 'd'=>4]
		);
	}

	public function test_only()
	{
		$this->assert_equals(
			collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10])
				->only(['a','b','c','d','e','j'])->all(),
			['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'j'=>10]);

		$this->assert_equals(
			collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10])
				->only(['k','l','m'])->all(),
			[]);
	}

	public function test_except()
	{
		$this->assert_equals(
			collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10])
				->except(['a','b','c','d','e','f'])->all(),
			['g'=>7,'h'=>8,'i'=>9,'j'=>10]
		);

		$this->assert_equals(
			collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10])
				->except(['k','l','m'])->all(),
			['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10]
		);
	}

	public function test_combine()
	{
		$this->assert_equals(collect(['a','b','c','d'])->combine([1,2,3,4])->all(), ['a'=>1,'b'=>2,'c'=>3,'d'=>4]);
	}

	public function test_map_with_keys()
	{
		$this->assert_equals(collect([1,2,3,4])->map_with_keys(function($elem) {return [$elem=>10+$elem];})->all(),
			[1=>11, 2=>12, 3=>13, 4=>14]
		);
	}

	public function test_unique()
	{
		$this->assert_equals(collect([1,3,2,3,2,1,2,2,2,3,3,5])->unique()->all(), [0=>1,1=>3,2=>2,11=>5]);
		$this->assert_equals(collect([1,1,1,1,1,1,1,1,1,1,1,1])->unique()->all(), [0=>1]);
	}

	public function test_result_keys_to_lower()
	{
		$this->assert_equals(collect([['A'=>1,'AA'=>11], ['b'=>2], ['C'=>3], ['D'=>4]])->result_keys_to_lower(),
			[['a'=>1,'aa'=>11], ['b'=>2], ['c'=>3], ['d'=>4]]
		);
	}

	public function test_pluck()
	{
		$this->assert_equals(
			collect([['campo1'=>1,'campo2'=>2], ['campo1'=> 3], ['campo1' => 4,'campo2'=>5], ['campo2'=>6]])
				->pluck('campo1')->all(),
			[1, 3, 4, NULL]
		);
	}

	public function test_first()
	{
		$this->assert_equals(
			collect([['campo1'=>1, 'campo2'=>2], ['campo1'=> 3], ['campo1'=>4,'campo2'=>5], ['campo2'=>6]])
				->first(),
			['campo1'=>1,'campo2'=>2]
		);

		$this->assert_equals(collect(['d'=>1,'b'=>2,'c'=>3])->first(), 1);
	}

}
