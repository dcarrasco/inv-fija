<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'login';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;


$route['auditores']['GET']           = 'inventario_config/listar/auditor';
$route['auditores/nuevo']['GET']     = 'inventario_config/mostrar/auditor';
$route['auditores/?(:any)?']['GET']  = 'inventario_config/mostrar/auditor/$1';
$route['auditores/?(:any)?']['POST'] = 'inventario_config/grabar/auditor/$1';

$route['familias']['GET']           = 'inventario_config/listar/familia';
$route['familias/nuevo']['GET']     = 'inventario_config/mostrar/familia';
$route['familias/?(:any)?']['GET']  = 'inventario_config/mostrar/familia/$1';
$route['familias/?(:any)?']['POST'] = 'inventario_config/grabar/familia/$1';

$route['catalogos']['GET']           = 'inventario_config/listar/catalogo';
$route['catalogos/nuevo']['GET']     = 'inventario_config/mostrar/catalogo';
$route['catalogos/?(:any)?']['GET']  = 'inventario_config/mostrar/catalogo/$1';
$route['catalogos/?(:any)?']['POST'] = 'inventario_config/grabar/catalogo/$1';

$route['tipos-inventario']['GET']           = 'inventario_config/listar/tipo_inventario';
$route['tipos-inventario/nuevo']['GET']     = 'inventario_config/mostrar/tipo_inventario';
$route['tipos-inventario/?(:any)?']['GET']  = 'inventario_config/mostrar/tipo_inventario/$1';
$route['tipos-inventario/?(:any)?']['POST'] = 'inventario_config/grabar/tipo_inventario/$1';

$route['inventarios']['GET']           = 'inventario_config/listar/inventario';
$route['inventarios/nuevo']['GET']     = 'inventario_config/mostrar/inventario';
$route['inventarios/?(:any)?']['GET']  = 'inventario_config/mostrar/inventario/$1';
$route['inventarios/?(:any)?']['POST'] = 'inventario_config/grabar/inventario/$1';

$route['tipos-ubicacion']['GET']           = 'inventario_config/listar/tipo_ubicacion';
$route['tipos-ubicacion/nuevo']['GET']     = 'inventario_config/mostrar/tipo_ubicacion';
$route['tipos-ubicacion/?(:any)?']['GET']  = 'inventario_config/mostrar/tipo_ubicacion/$1';
$route['tipos-ubicacion/?(:any)?']['POST'] = 'inventario_config/grabar/tipo_ubicacion/$1';

$route['centros']['GET']           = 'inventario_config/listar/centro';
$route['centros/nuevo']['GET']     = 'inventario_config/mostrar/centro';
$route['centros/?(:any)?']['GET']  = 'inventario_config/mostrar/centro/$1';
$route['centros/?(:any)?']['POST'] = 'inventario_config/grabar/centro/$1';

$route['almacenes']['GET']           = 'inventario_config/listar/almacen';
$route['almacenes/nuevo']['GET']     = 'inventario_config/mostrar/almacen';
$route['almacenes/?(:any)?']['GET']  = 'inventario_config/mostrar/almacen/$1';
$route['almacenes/?(:any)?']['POST'] = 'inventario_config/grabar/almacen/$1';

$route['unidades-medida']['GET']           = 'inventario_config/listar/unidad_medida';
$route['unidades-medida/nuevo']['GET']     = 'inventario_config/mostrar/unidad_medida';
$route['unidades-medida/?(:any)?']['GET']  = 'inventario_config/mostrar/unidad_medida/$1';
$route['unidades-medida/?(:any)?']['POST'] = 'inventario_config/grabar/unidad_medida/$1';




$route['peticiones_toa/?(:any)?']['GET'] = 'toa_consumos/peticiones_toa/$1';

$route['tecnicos_toa']['GET']           = 'toa_config/listar/tecnico_toa';
$route['tecnicos_toa/nuevo']['GET']     = 'toa_config/mostrar/tecnico_toa';
$route['tecnicos_toa/?(:any)?']['GET']  = 'toa_config/mostrar/tecnico_toa/$1';
$route['tecnicos_toa/?(:any)?']['POST'] = 'toa_config/grabar/tecnico_toa/$1';

