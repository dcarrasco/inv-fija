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

class Collection_test extends TestCase {

	public function test_make_collection()
	{
		$this->assertInstanceOf(Collection::class, collect([1,2,3,4,5]));
		$this->assertEquals(collect([1,2,3,4,5])->all(), [1,2,3,4,5]);
		$this->assertEquals(collect()->all(), []);
		$this->assertEquals(collect([])->all(), []);
		$this->assertEquals(collect(NULL)->all(), []);
		$this->assertEquals(collect('A')->all(), ['A']);
	}


	public function test_add_items()
	{
		$this->assertEquals(collect([1,2,3,4])->add_item(5)->all(), [1,2,3,4,5]);

		$this->assertEquals(collect([1,2,3,4])->add_item(5, 'a')->all(),
			[0 => 1, 1 => 2, 2 => 3, 3 => 4, 'a' => 5]
		);
	}


	public function test_delete_item()
	{
		$this->assertEquals(collect([1,2,3,4,5])->delete_item(2)->all(), [0=>1, 1=>2, 3=>4, 4=>5]);
		$this->assertEquals(collect([1,2,3,4,5])->delete_item('a')->all(), [1,2,3,4,5]);
	}


	public function test_item()
	{
		$this->assertEquals(collect([1,2,3,4,5])->item(1), 2);
		$this->assertEquals(collect(['a'=>1,'b'=>2,'c'=>3])->item('b'), 2);
		$this->assertNull(collect([1,2,3,4,5])->item('A'));
	}


	public function test_keys()
	{
		$this->assertEquals(collect([1,2,3,4])->keys()->all(), [0,1,2,3]);
		$this->assertEquals(collect(['a'=>1,'b'=>2,'aa'=>3,'bb'=>4])->keys()->all(), ['a','b','aa','bb']);
	}


	public function test_length()
	{
		$this->assertEquals(collect([1,2,3,4,5])->length(), 5);
		$this->assertEquals(collect([])->length(), 0);
	}


	public function test_count()
	{
		$this->assertEquals(collect([1,2,3,4,5])->count(), 5);
		$this->assertEquals(collect([])->count(), 0);
	}


	public function test_key_exists()
	{
		$this->assertTrue(collect([1,2,3,4,5])->key_exists(2));
		$this->assertTrue(collect(['a'=>1,'b'=>2,'aa'=>3,'bb'=>4])->key_exists('aa'));
		$this->assertFalse(collect([1,2,3,4,5])->key_exists(5));
		$this->assertFalse(collect(['a'=>1,'b'=>2,'aa'=>3,'bb'=>4])->key_exists('cc'));
	}


	public function test_get_element()
	{
		$this->assertEquals(collect([1,2,3,4,5])->get(2), 3);
		$this->assertEquals(collect(['a'=>1,'b'=>2,'aa'=>3,'bb'=>4])->get('aa'), 3);
		$this->assertNull(collect([1,2,3,4,5])->get(5));
	}


	public function test_map()
	{
		$this->assertEquals(collect([1,2,3,4,5])->map(function($el) {return $el*2;})->all(), [2,4,6,8,10]);
	}


	public function test_is_empty()
	{
		$this->assertTrue(collect()->is_empty());
		$this->assertTrue(collect(NULL)->is_empty());
		$this->assertTrue(collect([])->is_empty());
		$this->assertFalse(collect([1,2,3,4,5])->is_empty());
		$this->assertFalse(collect('a')->is_empty());
	}


	public function test_has()
	{
		$this->assertTrue(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->has('e'));
		$this->assertTrue(collect([1,2,3,4,5])->has(1));
		$this->assertFalse(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->has('f'));
		$this->assertFalse(collect([1,2,3,4,5])->has(10));
		$this->assertTrue(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->has(['d', 'e']));
		$this->assertFalse(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->has(['f', 'g']));
	}


	public function test_contains()
	{
		$this->assertTrue(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->contains(5));
		$this->assertTrue(collect([1,2,3,4,5])->contains(1));
		$this->assertFalse(collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5])->contains(6));
		$this->assertFalse(collect([1,2,3,4,5])->contains(0));
	}


	public function test_implode()
	{
		$this->assertEquals(collect([1,2,3,4,5])->implode('-'), '1-2-3-4-5');
		$this->assertEquals(collect([])->implode('-'), '');
	}


	public function test_filter()
	{
		$this->assertEquals(collect([1,2,3,4,5])->filter(function($el) {return $el<=2;})->all(), [1,2]);
		$this->assertInstanceOf(Collection::class, collect([1,2,3,4,5])->filter(function($el) {return $el>20;}));
		$this->assertEquals(collect([1,2,3,4,5])->filter(function($el) {return $el>20;})->all(), []);
		$this->assertEquals(collect([1,2,3,4,5])->filter()->all(), [1,2,3,4,5]);
	}


	public function test_where()
	{
		$collect1 = collect([
			['campo1'=>1, 'campo2'=>'a', 'campo3'=>100],
			['campo1'=>2, 'campo2'=>'b', 'campo3'=>100],
			['campo1'=>3, 'campo2'=>'c', 'campo3'=>200],
			['campo1'=>4, 'campo2'=>'d', 'campo3'=>200],
		]);

		$this->assertEquals($collect1->where('campo1',1)->all(), [['campo1'=>1, 'campo2'=>'a', 'campo3'=>100]]);

		$this->assertEquals($collect1->where('campo2','b')->all(), ['1'=>['campo1'=>2, 'campo2'=>'b', 'campo3'=>100]]);

		$this->assertEquals($collect1->where('campo3', 200)->all(), [
			'2'=>['campo1'=>3, 'campo2'=>'c', 'campo3'=>200],
			'3'=>['campo1'=>4, 'campo2'=>'d', 'campo3'=>200],
		]);

		$this->assertEquals($collect1->where('campo1','>=',3)->all(), [
			'2'=>['campo1'=>3, 'campo2'=>'c', 'campo3'=>200],
			'3'=>['campo1'=>4, 'campo2'=>'d', 'campo3'=>200],
		]);

		$this->assertEquals($collect1->where('campo1', 'zzz')->all(), []);
	}


	public function test_where_in()
	{
		$collect1 = collect([
			['campo1'=>1, 'campo2'=>'a', 'campo3'=>100],
			['campo1'=>2, 'campo2'=>'b', 'campo3'=>100],
			['campo1'=>3, 'campo2'=>'c', 'campo3'=>200],
			['campo1'=>4, 'campo2'=>'d', 'campo3'=>200],
		]);

		$this->assertEquals($collect1->where_in('campo1',[1,2,3])->all(), [
			['campo1'=>1, 'campo2'=>'a', 'campo3'=>100],
			['campo1'=>2, 'campo2'=>'b', 'campo3'=>100],
			['campo1'=>3, 'campo2'=>'c', 'campo3'=>200],
		]);
	}


	public function test_sum()
	{
		$this->assertEquals(collect([1,2,3,4,5])->sum(), 15);
		$this->assertEquals(collect([])->sum(), 0);
	}


	public function test_max()
	{
		$this->assertEquals(collect([1,4,3,5,0,2])->max(), 5);
		$this->assertEquals(collect([])->max(), 0);
	}


	public function test_concat()
	{
		$this->assertEquals(collect([1,2,3,4,5])->concat(function($elem) {return $elem.'-';}), '1-2-3-4-5-');
		$this->assertEquals(collect([])->concat(function($elem) {return $elem.'-';}), '');
		$this->assertNull(collect([1,2,3,4,5])->concat(NULL));
	}


	public function test_sort()
	{
		$this->assertEquals(collect([2,1,4,3,5])->sort()->all(), [1=>1, 0=>2, 3=>3, 2=>4, 4=>5]);
		$this->assertEquals(collect(['a'=>2,'b'=>1,'c'=>4,'d'=>3,'e'=>5])->sort()->all(), ['b'=>1, 'a'=>2, 'd'=>3, 'c'=>4, 'e'=>5]);
	}


	public function test_flatten()
	{
		$this->assertEquals(collect([[1,2,3], [4,5,6], [7,8,9,10]])->flatten()->all(), [1,2,3,4,5,6,7,8,9,10]);
		$this->assertEquals(collect([[1,[2,[3]]], [4,[5,[6]]], [7,[8,[9,[10]]]]])->flatten()->all(), [1,2,3,4,5,6,7,8,9,10]);
	}


	public function test_merge()
	{
		$this->assertEquals(collect([1,2,3,4,5,6])->merge([7,8,9,10])->all(), [1,2,3,4,5,6,7,8,9,10]);
		$this->assertEquals(collect(['a'=>1,'b'=>2,'c'=>33])->merge(['c'=>3,'d'=>4])->all(),
			['a'=>1, 'b'=>2, 'c'=>3, 'd'=>4]
		);
	}


	public function test_only()
	{
		$this->assertEquals(
			collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10])
				->only(['a','b','c','d','e','j'])->all(),
			['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'j'=>10]);

		$this->assertEquals(
			collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10])
				->only(['k','l','m'])->all(),
			[]);
	}


	public function test_except()
	{
		$this->assertEquals(
			collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10])
				->except(['a','b','c','d','e','f'])->all(),
			['g'=>7,'h'=>8,'i'=>9,'j'=>10]
		);

		$this->assertEquals(
			collect(['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10])
				->except(['k','l','m'])->all(),
			['a'=>1,'b'=>2,'c'=>3,'d'=>4,'e'=>5,'f'=>6,'g'=>7,'h'=>8,'i'=>9,'j'=>10]
		);
	}


	public function test_combine()
	{
		$this->assertEquals(collect(['a','b','c','d'])->combine([1,2,3,4])->all(), ['a'=>1,'b'=>2,'c'=>3,'d'=>4]);
	}


	public function test_map_with_keys()
	{
		$this->assertEquals(collect([1,2,3,4])->map_with_keys(function($elem) {return [$elem=>10+$elem];})->all(),
			[1=>11, 2=>12, 3=>13, 4=>14]
		);
	}


	public function test_unique()
	{
		$this->assertEquals(collect([1,3,2,3,2,1,2,2,2,3,3,5])->unique()->all(), [0=>1,1=>3,2=>2,11=>5]);
		$this->assertEquals(collect([1,1,1,1,1,1,1,1,1,1,1,1])->unique()->all(), [0=>1]);
	}


	public function test_result_keys_to_lower()
	{
		$this->assertEquals(collect([['A'=>1,'AA'=>11], ['b'=>2], ['C'=>3], ['D'=>4]])->result_keys_to_lower(),
			[['a'=>1,'aa'=>11], ['b'=>2], ['c'=>3], ['d'=>4]]
		);
	}


	public function test_pluck()
	{
		$this->assertEquals(
			collect([['campo1'=>1,'campo2'=>2], ['campo1'=> 3], ['campo1' => 4,'campo2'=>5], ['campo2'=>6]])
				->pluck('campo1')->all(),
			[1, 3, 4, NULL]
		);
		$this->assertEquals(
			collect([['campo1'=>1,'campo2'=>2], ['campo1'=> 3], ['campo1' => 4,'campo2'=>5], ['campo2'=>6]])
				->pluck('campo1', 'campo2')->all(),
			['2'=>1, ''=>3, '5'=>4, '6'=>NULL]
		);
		$this->assertInstanceOf(Collection::class, collect([])->pluck('a'));
		$this->assertEmpty(collect([])->pluck('a')->all());
		$this->assertEquals(
			collect([
				(object) ['campo1'=>1, 'campo2'=>2],
				(object) ['campo1'=> 3],
				(object) ['campo1' => 4, 'campo2'=>5],
				(object) ['campo2'=>6]
			])->pluck('campo1')->all(),
			[1, 3, 4, NULL]
		);
	}


	public function test_first()
	{
		$this->assertEquals(
			collect([['campo1'=>1, 'campo2'=>2], ['campo1'=> 3], ['campo1'=>4,'campo2'=>5], ['campo2'=>6]])
				->first(),
			['campo1'=>1,'campo2'=>2]
		);

		$this->assertEquals(collect(['d'=>1,'b'=>2,'c'=>3])->first(), 1);
		$this->assertEquals(collect([])->first(), NULL);
		$this->assertEquals(collect([1,2,3,4,5,6])->first(function($val) {
			return $val>2;
		}), 3);
		$this->assertEquals(collect([1,2,3,4,5,6])->first(function($val) {
			return $val>12;
		}, 'xx'), 'xx');
	}

}
