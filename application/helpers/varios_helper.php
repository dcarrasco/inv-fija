<?php

if ( ! function_exists('dbg'))
{
	function dbg($item)
	{
		echo '<pre style="font-family: courier, font-size: 8px">';
		$dump = print_r($item, true);
		echo gettype($item) . ' : ' . $dump;
		echo '</pre><hr>';
	}
}
