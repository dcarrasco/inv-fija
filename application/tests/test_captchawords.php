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
class test_captchawords extends test_case {

	public function __construct()
	{
	    parent::__construct();
	}

	public function test_word()
	{
		$expected = 'is_string';

		$this->test(\CaptchaWords::word(), $expected);
	}

}
