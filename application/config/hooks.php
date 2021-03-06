<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	http://codeigniter.com/user_guide/general/hooks.html
|
*/

$hook['pre_system'] = array(
	'class'    => 'MY_Hooks',
	'function' => 'pre_system_hook',
	'filename' => 'MY_Hooks.php',
	'filepath' => 'hooks',
);

$hook['post_controller_constructor'] = array(
	'class'    => 'MY_Hooks',
	'function' => 'acl_hook',
	'filename' => 'MY_Hooks.php',
	'filepath' => 'hooks',
);
