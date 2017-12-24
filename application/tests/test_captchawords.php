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
class test_captchawords extends test_case {

	public function __construct()
	{
		parent::__construct();
	}

	public function test_word_is_string()
	{
		$this->assert_is_string(\CaptchaWords::word());
	}

	public function test_word_length()
	{
		$this->assert_equals(strlen(\CaptchaWords::word()), 8);
	}

}
