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




$routes_orm = array(
	// CONFIG INVENTARIO
	array('uri' => 'auditores',        'controller' => 'inventario_config', 'object' => 'auditor'),
	array('uri' => 'familias',         'controller' => 'inventario_config', 'object' => 'familia'),
	array('uri' => 'catalogos',        'controller' => 'inventario_config', 'object' => 'catalogo'),
	array('uri' => 'tipos-inventario', 'controller' => 'inventario_config', 'object' => 'tipo_inventario'),
	array('uri' => 'inventarios',      'controller' => 'inventario_config', 'object' => 'inventario'),
	array('uri' => 'tipos-ubicacion',  'controller' => 'inventario_config', 'object' => 'tipo_ubicacion'),
	array('uri' => 'centros',          'controller' => 'inventario_config', 'object' => 'centro'),
	array('uri' => 'almacenes',        'controller' => 'inventario_config', 'object' => 'almacene'),
	array('uri' => 'unidades-medida',  'controller' => 'inventario_config', 'object' => 'unidad_medida'),
	// CONFIG STOCK
	array('uri' => 'sapalmacenes',      'controller' => 'stock_config', 'object' => 'almacen_sap'),
	array('uri' => 'tipos-almacen-sap', 'controller' => 'stock_config', 'object' => 'tipoalmacen_sap'),
	array('uri' => 'clasificacion-almacenes-sap', 'controller' => 'stock_config', 'object' => 'clasifalmacen_sap'),
	array('uri' => 'tipos-clasificacion-almacen-sap', 'controller' => 'stock_config', 'object' => 'tipo_clasifalm'),
	array('uri' => 'proveedores',       'controller' => 'stock_config', 'object' => 'proveedor'),
	array('uri' => 'usuarios-sap',      'controller' => 'stock_config', 'object' => 'usuario_sap'),
	array('uri' => 'clases-movimiento', 'controller' => 'stock_config', 'object' => 'clase_movimiento'),
	// CONFIG ACL
	array('uri' => 'usuarios',     'controller' => 'acl_config', 'object' => 'usuario'),
	array('uri' => 'aplicaciones', 'controller' => 'acl_config', 'object' => 'app'),
	array('uri' => 'roles',        'controller' => 'acl_config', 'object' => 'rol'),
	array('uri' => 'modulos',      'controller' => 'acl_config', 'object' => 'modulo'),
	// CONFIG TOA
	array('uri' => 'tecnicos-toa',          'controller' => 'toa_config', 'object' => 'tecnico_toa'),
	array('uri' => 'empresas-toa',          'controller' => 'toa_config', 'object' => 'empresa_toa'),
	array('uri' => 'tipos-trabajo-toa',     'controller' => 'toa_config', 'object' => 'tipo_trabajo_toa'),
	array('uri' => 'tipos-material-trabajo-toa', 'controller' => 'toa_config', 'object' => 'tip_material_trabajo_toa'),
	array('uri' => 'ciudades-toa',          'controller' => 'toa_config', 'object' => 'ciudad_toa'),
	array('uri' => 'empresas-ciudades-toa', 'controller' => 'toa_config', 'object' => 'empresa_ciudad_toa'),
);

foreach ($routes_orm as $route_orm)
{
	$route[$route_orm['uri']]['GET']              = $route_orm['controller'].'/listar/'. $route_orm['object'];
	$route[$route_orm['uri'].'/nuevo']['GET']     = $route_orm['controller'].'/mostrar/'.$route_orm['object'];
	$route[$route_orm['uri'].'/?(:any)?']['GET']  = $route_orm['controller'].'/mostrar/'.$route_orm['object'].'/$1';
	$route[$route_orm['uri'].'/?(:any)?']['POST'] = $route_orm['controller'].'/grabar/'. $route_orm['object'].'/$1';
}



$route['peticiones_toa/?(:any)?']['GET'] = 'toa_consumos/peticiones_toa/$1';

