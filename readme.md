# Inventario fija

Aplicacion de conciliacion de inventario para la logistica fija.

## Modulo Inventario fija

Modulo que permite ingresar, digitar y conciliar un inventario para la operacion fija. Se extiende la aplicacion para la toma de inventarios de cables y la operación de empresas.

* Digitación de inventario. Permite ingresar un inventario a partir de las hojas de toma de inventario impresas.
* Reportes. Varios reportes que permiten conciliar y cuadrar el inventario digitado.
* Ajustes. Permite ingresar ajustes para conciliar el inventario. Incluye las opciones de subir stock para un inventario e imprimir las hojas del inventario.
* Configuracion. Permite ingresar/modificar/eliminar datos a los maestros que soportan la toma de invenario.

## Modulo de Stock SAP

Modulo que permite obtener reportes sobre información de stock de SAP (movil y fijo)

* Analisis de series. Permite identificar información relevante de una serie (movimientos, stock, despacho, trafico)
* Consulta de stock. Permite visualizar stock fijo/movil.
* Reporte permanencia canal. Permite visualizar la antigüedad de las series en canal de venta movil.
* Reportes Trazabilidad. Varios reportes que soportan la trazabilidad fija
* Configuración de stock. Permite ingresar/modificar/eliminar datos a los maestros que soportan los reportes de stock.

## Modulo ACL

Modulo que permite controlar el acceso a las funciones de esta aplicacion a través de una lista de privilegios asociados a roles, los cuales se asocian a usuarios.

* Configuración ACL. Permite ingresar/modificar/eliminar datos a los maestros que soportan el control de acceso.

## Modulo Despachos

Modulo que permite generar reportes sobre los despachos de la operación móvil

* Despachos retail. Permite visualizar documentos de despacho para un retail y materiales especificados.

## Requerimientos y especificaciones

* PHP 5.5.9
* SQL Server / MySQL
* Codeigniter 3.1.6
* JQuery 2.1.4
* Bootstrap 3.3.7
* JqPlot 1.0.8
* Bootstrap Datepicker 1.6.1 (en github eternicode/bootstrap-datepicker)


## Auditoría

* __vuln-001-0110/2015__ Cierre puertos
* __vuln-002-0110/2015__ Credenciales en texto claro
	* https
* __vuln-003-0110/2015__ Acceso a listado de directorios
	* Apache: httpd.conf
		* `<Directory "app_path">`
			* `Option -Indexes`
		* `</Directory>`
* __vuln-004-0110/2015__ Mensajes de error db
	* En config\database.php
		* `db_debug => false`
* __vuln-005-0110/2015__ Información técnica
	* Apache: httpd.conf
		* `<Directory "/">`
			* `Order Deny,Allow`
			* `Deny from all`
			* `Options None`
			* `Allow Override None`
		* `</Directory>`
		* `<Directory "app_path">`
			* `Order Allow,Deny`
			* `Allow from all`
		* `</Directory>`
* __vuln-008-0110/2015__ Clickjacking
	* En .htaccess
		* `<IfModule mod_headers.c>`
			* `Header always append X-Frame-Options SAMEORIGIN`
		* `</ifModule>`
* __vuln-009-0110/2015__ Cookies sesion
	* cookies http-only
* __vuln-010-0110/2015__ Método http trace
	* Apache: httpd.conf
		* Agregar
			* `TraceEnable off`
