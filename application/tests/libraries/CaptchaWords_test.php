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
class CaptchaWords_test extends TestCase {

	public function test_word_is_string()
	{
		$this->assertInternalType('string', \CaptchaWords::word());
	}

	public function test_word_length()
	{
		$this->assertEquals(8, strlen(\CaptchaWords::word()));
	}

}
