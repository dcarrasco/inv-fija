<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

date_default_timezone_set('America/Santiago');

/*
|--------------------------------------------------------------------------
| Tablas del sistema
|--------------------------------------------------------------------------
|
| Listado con las tablas del sistema
|
*/
$config['app_nombre'] = 'Inventario Fija';

/*
|--------------------------------------------------------------------------
| Tablas del sistema
|--------------------------------------------------------------------------
|
| Listado con las tablas del sistema
|
*/

// Nombres de las bases de datos
define('BD_INVENTARIO', getenv('BD_INVENTARIO'));
define('BD_TOA', getenv('BD_TOA'));
define('BD_LOGISTICA', getenv('BD_LOGISTICA'));
define('BD_CONTROLES', getenv('BD_CONTROLES'));
define('BD_PLANIFICACION', getenv('BD_PLANIFICACION'));
define('BD_COLLATE', getenv('BD_COLLATE'));

// Inventarios
$config['bd_usuarios']           = BD_INVENTARIO.'fija_usuarios';
$config['bd_inventarios']        = BD_INVENTARIO.'fija_inventarios';
$config['bd_tipos_inventario']   = BD_INVENTARIO.'fija_tipos_inventario';
$config['bd_detalle_inventario'] = BD_INVENTARIO.'fija_detalle_inventario';
$config['bd_catalogos']          = BD_INVENTARIO.'fija_catalogos';
$config['bd_catalogos_fotos']    = BD_INVENTARIO.'fija_catalogos_fotos';
$config['bd_centros']            = BD_INVENTARIO.'fija_centros';
$config['bd_unidades']           = BD_INVENTARIO.'fija_unidades';
$config['bd_auditores']          = BD_INVENTARIO.'fija_auditores';
$config['bd_almacenes']          = BD_INVENTARIO.'fija_almacenes';
$config['bd_familias']           = BD_INVENTARIO.'fija_familias';
$config['bd_tipo_ubicacion']     = BD_INVENTARIO.'fija_tipo_ubicacion';
$config['bd_ubic_tipoubic']      = BD_INVENTARIO.'fija_ubicacion_tipo_ubicacion';

// ACL
$config['bd_app']         = BD_INVENTARIO.'acl_app';
$config['bd_modulos']     = BD_INVENTARIO.'acl_modulo';
$config['bd_rol']         = BD_INVENTARIO.'acl_rol';
$config['bd_usuario_rol'] = BD_INVENTARIO.'acl_usuario_rol';
$config['bd_rol_modulo']  = BD_INVENTARIO.'acl_rol_modulo';
$config['bd_captcha']     = BD_INVENTARIO.'ci_captcha';
$config['bd_pcookies']    = BD_INVENTARIO.'fija_pcookies';

// Stock
$config['bd_almacenes_sap']        = BD_LOGISTICA.'cp_almacenes';
$config['bd_tipoalmacen_sap']      = BD_LOGISTICA.'cp_tipos_almacenes';
$config['bd_tiposalm_sap']         = BD_LOGISTICA.'cp_tiposalm';
$config['bd_clasifalm_sap']        = BD_LOGISTICA.'cp_clasifalm';
$config['bd_clasif_tipoalm_sap']   = BD_LOGISTICA.'cp_clasif_tipoalm';
$config['bd_tipo_clasifalm_sap']   = BD_LOGISTICA.'cp_tipo_clasifalm';
$config['bd_reporte_clasif']       = BD_LOGISTICA.'cp_reporte_clasificacion';
$config['bd_proveedores']          = BD_LOGISTICA.'cp_proveedores';
$config['bd_permanencia']          = BD_LOGISTICA.'cp_permanencia';
$config['bd_permanencia_fija']     = BD_LOGISTICA.'perm_series_consumo_fija';
$config['bd_stock_movil']          = BD_LOGISTICA.'stock_scl';
$config['bd_stock_movil_res01']    = BD_LOGISTICA.'stock_scl_res01';
$config['bd_stock_movil_fechas']   = BD_LOGISTICA.'stock_scl_fechas';
$config['bd_stock_fija']           = BD_LOGISTICA.'bd_stock_sap_fija';
$config['bd_stock_fija_fechas']    = BD_LOGISTICA.'bd_stock_sap_fija_fechas';
$config['bd_movimientos_sap']      = BD_LOGISTICA.'mov_hist';
$config['bd_movimientos_sap_fija'] = BD_LOGISTICA.'bd_movmb51_fija';
$config['bd_resmovimientos_sap']   = BD_LOGISTICA.'mov_hist_res01';
$config['bd_cmv_sap']              = BD_LOGISTICA.'cp_cmv';
$config['bd_tipos_movimientos']    = BD_LOGISTICA.'cp_tipos_movimientos';
$config['bd_tipos_movs_cmv']       = BD_LOGISTICA.'cp_tipos_movimientos_cmv';
$config['bd_fechas_sap']           = BD_LOGISTICA.'cp_fechas';
$config['bd_usuarios_sap']         = BD_LOGISTICA.'cp_usuarios';
$config['bd_despachos_sap']        = BD_LOGISTICA.'despachos_sap';
$config['bd_materiales_sap']       = BD_LOGISTICA.'al_articulos';
$config['bd_materiales2_sap']      = BD_LOGISTICA.'cp_materiales_sap';
$config['bd_stock_seriado_sap']    = BD_LOGISTICA.'bd_stock_sap';
$config['bd_stock_seriado_sap_03'] = BD_LOGISTICA.'bd_stock_03';
$config['bd_stock_scl']            = BD_LOGISTICA.'bd_stock_scl';
$config['bd_al_bodegas']           = BD_LOGISTICA.'al_bodegas';
$config['bd_al_tipos_bodegas']     = BD_LOGISTICA.'al_tipos_bodegas';
$config['bd_al_tipos_stock']       = BD_LOGISTICA.'al_tipos_stock';
$config['bd_al_estados']           = BD_LOGISTICA.'al_estados';
$config['bd_al_usos']              = BD_LOGISTICA.'al_usos';
$config['bd_trafico_mes']          = BD_LOGISTICA.'trafico_mes';
$config['bd_trafico_abocelamist']  = BD_CONTROLES.'trafico_abocelamist';
$config['bd_trafico_clientes']     = BD_CONTROLES.'trafico_clientes';
$config['bd_trafico_causabaja']    = BD_CONTROLES.'trafico_causabaja';
$config['bd_trafico_dias_proc']    = BD_CONTROLES.'trafico_dias_procesados';
$config['bd_pmp']                  = BD_PLANIFICACION.'ca_stock_sap_04';

// Despachos
$config['bd_despachos_pack']       = BD_LOGISTICA.'despachos_sap_res01_pack';

// TOA
$config['bd_peticiones_sap']            = BD_TOA.'toa_peticiones_sap';
$config['bd_peticiones_toa']            = BD_TOA.'appt_toa2';
$config['bd_materiales_peticiones_toa'] = BD_TOA.'inv_fields_toa';
$config['bd_peticiones_vpi']            = BD_TOA.'consumo_toa_vpi';
$config['bd_claves_cierre_toa']         = BD_TOA.'toa_claves_cierre';
$config['bd_tecnicos_toa']              = BD_TOA.'toa_tecnicos';
$config['bd_empresas_toa']              = BD_TOA.'toa_empresas';
$config['bd_ciudades_toa']              = BD_TOA.'toa_ciudades';
$config['bd_empresas_toa_tiposalm']     = BD_TOA.'toa_empresas_tiposalm';
$config['bd_empresas_ciudades_toa']     = BD_TOA.'toa_empresas_ciudades';
$config['bd_empresas_ciudades_almacenes_toa'] = BD_TOA.'toa_empresas_ciudades_almacenes';
$config['bd_tipos_trabajo_toa']         = BD_TOA.'toa_tipos_trabajo';
$config['bd_tip_material_toa']          = BD_TOA.'toa_tip_material';
$config['bd_catalogo_tip_material_toa'] = BD_TOA.'toa_catalogo_tip_material';
$config['bd_resumen_panel_toa']         = BD_TOA.'toa_resumen_panel';
$config['bd_uso_toa']                   = BD_TOA.'toa_reporte_uso';
$config['bd_uso_toa_dia']               = BD_TOA.'toa_reporte_uso_dia';
$config['bd_uso_toa_vpi']               = BD_TOA.'toa_reporte_uso_vpi';
$config['bd_uso_toa_toa']               = BD_TOA.'toa_reporte_uso_toa';
$config['bd_uso_toa_repara']            = BD_TOA.'toa_reporte_uso_repara';
$config['bd_ps_vpi_toa']                = BD_TOA.'toa_ps_vpi';
$config['bd_ps_tip_material_toa']       = BD_TOA.'toa_ps_tip_material';
$config['bd_claves_cierre_tip_material_toa'] = BD_TOA.'toa_claves_cierre_tip_material';
$config['bd_type_list_toa']             = BD_TOA.'toa_type_list';


/* End of file inv-fija.php */
/* Location: ./application/config/inv-fija.php */
