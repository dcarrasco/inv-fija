<?php

if ( ! function_exists('dbg'))
{
	function dbg($item)
	{
		echo '<pre style="font-family: courier, font-size: 8px">';
		$dump = print_r($item, TRUE);
		echo gettype($item) . ' : ' . $dump;
		echo '</pre><hr>';
	}
}


if ( ! function_exists('form_has_error'))
{
	function form_has_error($form_field = '')
	{
		return (form_error($form_field) != '') ? 'has-error' : '';
	}
}



/* helpers varios_helper.php */
/* Location: ./application/helpers/varios_helper.php */