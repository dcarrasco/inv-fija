<?php

if ( ! function_exists('debug'))
{
	function dbg($item)
	{
		echo '<pre style="font-family: courier, font-size: 8px">';
		$dump = print_r($item, true);
		echo $dump;
		echo '</pre><hr>';
	}
}
