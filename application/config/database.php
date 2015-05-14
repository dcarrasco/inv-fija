<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7
|				 (and in table creation queries made with DB Forge).
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = 'default';

if (ENVIRONMENT == 'development')
{
	$active_group = 'dcr';
}
if (ENVIRONMENT == 'development-mac')
{
	$active_group = 'mac';
}

$query_builder = TRUE;

$db['default']['hostname'] = 'localhost';
$db['default']['username'] = 'invfija';
$db['default']['password'] = 'fijainv2014!';
$db['default']['database'] = 'BD_inventario';
$db['default']['dbdriver'] = 'sqlsrv';
$db['default']['dbprefix'] = '';
$db['default']['pconnect'] = FALSE;
$db['default']['db_debug'] = TRUE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = '';
$db['default']['char_set'] = 'utf8';
$db['default']['dbcollat'] = 'utf8_general_ci';
$db['default']['swap_pre'] = '';
$db['default']['autoinit'] = TRUE;
$db['default']['stricton'] = FALSE;

$db['dcr']['hostname'] = 'tcp:127.0.0.1,1500';
$db['dcr']['username'] = 'invfija';
$db['dcr']['password'] = 'fijainv2014!';
$db['dcr']['database'] = 'BD_inventario';
$db['dcr']['dbdriver'] = 'sqlsrv';
$db['dcr']['dbprefix'] = '';
$db['dcr']['pconnect'] = FALSE;
$db['dcr']['db_debug'] = TRUE;
$db['dcr']['cache_on'] = FALSE;
$db['dcr']['cachedir'] = '';
$db['dcr']['char_set'] = 'utf8';
$db['dcr']['dbcollat'] = 'utf8_general_ci';
$db['dcr']['swap_pre'] = '';
$db['dcr']['autoinit'] = TRUE;
$db['dcr']['stricton'] = FALSE;

$db['mac']['hostname'] = 'localhost';
$db['mac']['username'] = 'logistica';
$db['mac']['password'] = 'logistica';
$db['mac']['database'] = 'logistica';
$db['mac']['dbdriver'] = 'mysqli';
$db['mac']['dbprefix'] = '';
$db['mac']['pconnect'] = FALSE;
$db['mac']['db_debug'] = TRUE;
$db['mac']['cache_on'] = FALSE;
$db['mac']['cachedir'] = '';
$db['mac']['char_set'] = 'utf8';
$db['mac']['dbcollat'] = 'utf8_general_ci';
$db['mac']['swap_pre'] = '';
$db['mac']['autoinit'] = TRUE;
$db['mac']['stricton'] = FALSE;

$db['mac-sirio']['hostname'] = 'sirio';
$db['mac-sirio']['username'] = 'invfija';
$db['mac-sirio']['password'] = 'fijainv2014!';
$db['mac-sirio']['database'] = 'BD_inventario';
$db['mac-sirio']['dbdriver'] = 'mssql';
$db['mac-sirio']['dbprefix'] = '';
$db['mac-sirio']['pconnect'] = FALSE;
$db['mac-sirio']['db_debug'] = TRUE;
$db['mac-sirio']['cache_on'] = FALSE;
$db['mac-sirio']['cachedir'] = '';
$db['mac-sirio']['char_set'] = 'utf8';
$db['mac-sirio']['dbcollat'] = 'utf8_general_ci';
$db['mac-sirio']['swap_pre'] = '';
$db['mac-sirio']['autoinit'] = TRUE;
$db['mac-sirio']['stricton'] = FALSE;

$db['adminbd']['hostname'] = 'tcp:127.0.0.1,1500';
$db['default']['hostname'] = 'localhost';
$db['adminbd']['username'] = 'patripio';
$db['adminbd']['password'] = 'movi.2015.logistica';
$db['adminbd']['database'] = 'BD_inventario';
$db['adminbd']['dbdriver'] = 'sqlsrv';
$db['adminbd']['dbprefix'] = '';
$db['adminbd']['pconnect'] = FALSE;
$db['adminbd']['db_debug'] = TRUE;
$db['adminbd']['cache_on'] = FALSE;
$db['adminbd']['cachedir'] = '';
$db['adminbd']['char_set'] = 'utf8';
$db['adminbd']['dbcollat'] = 'utf8_general_ci';
$db['adminbd']['swap_pre'] = '';
$db['adminbd']['autoinit'] = TRUE;
$db['adminbd']['stricton'] = FALSE;


/* End of file database.php */
/* Location: ./application/config/database.php */
