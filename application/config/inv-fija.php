<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

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
// Inventarios
$config['bd_usuarios']           = 'fija_usuarios';
$config['bd_inventarios']        = 'fija_inventarios';
$config['bd_tipos_inventario']   = 'fija_tipos_inventario';
$config['bd_detalle_inventario'] = 'fija_detalle_inventario';
$config['bd_catalogos']          = 'fija_catalogos';
$config['bd_catalogos_fotos']    = 'fija_catalogos_fotos';
$config['bd_centros']            = 'fija_centros';
$config['bd_unidades']           = 'fija_unidades';
$config['bd_auditores']          = 'fija_auditores';
$config['bd_almacenes']          = 'fija_almacenes';
$config['bd_familias']           = 'fija_familias';
$config['bd_tipo_ubicacion']     = 'fija_tipo_ubicacion';
$config['bd_ubic_tipoubic']      = 'fija_ubicacion_tipo_ubicacion';

// ACL
$config['bd_app']         = 'acl_app';
$config['bd_modulos']     = 'acl_modulo';
$config['bd_rol']         = 'acl_rol';
$config['bd_usuario_rol'] = 'acl_usuario_rol';
$config['bd_rol_modulo']  = 'acl_rol_modulo';
$config['bd_captcha']     = 'ci_captcha';
$config['bd_pcookies']    = 'fija_pcookies';

// Stock
$config['bd_almacenes_sap']        = 'bd_logistica..cp_almacenes';
$config['bd_tipoalmacen_sap']      = 'bd_logistica..cp_tipos_almacenes';
$config['bd_tiposalm_sap']         = 'bd_logistica..cp_tiposalm';
$config['bd_clasifalm_sap']        = 'bd_logistica..cp_clasifalm';
$config['bd_clasif_tipoalm_sap']   = 'bd_logistica..cp_clasif_tipoalm';
$config['bd_tipo_clasifalm_sap']   = 'bd_logistica..cp_tipo_clasifalm';
$config['bd_reporte_clasif']       = 'bd_logistica..cp_reporte_clasificacion';
$config['bd_proveedores']          = 'bd_logistica..cp_proveedores';
$config['bd_permanencia']          = 'bd_logistica..cp_permanencia';
$config['bd_permanencia_fija']     = 'bd_logistica..perm_series_consumo_fija';
$config['bd_stock_movil']          = 'bd_logistica..stock_scl';
$config['bd_stock_movil_res01']    = 'bd_logistica..stock_scl_res01';
$config['bd_stock_movil_fechas']   = 'bd_logistica..stock_scl_fechas';
$config['bd_stock_fija']           = 'bd_logistica..bd_stock_sap_fija';
$config['bd_stock_fija_fechas']    = 'bd_logistica..bd_stock_sap_fija_fechas';
$config['bd_movimientos_sap']      = 'bd_logistica..mov_hist';
$config['bd_movimientos_sap_fija'] = 'bd_logistica..bd_movmb51_fija';
$config['bd_resmovimientos_sap']   = 'bd_logistica..mov_hist_res01';
$config['bd_cmv_sap']              = 'bd_logistica..cp_cmv';
$config['bd_fechas_sap']           = 'bd_logistica..cp_fechas';
$config['bd_usuarios_sap']         = 'bd_logistica..cp_usuarios';
$config['bd_despachos_sap']        = 'bd_logistica..despachos_sap';
$config['bd_materiales_sap']       = 'bd_logistica..al_articulos';
$config['bd_materiales2_sap']      = 'bd_logistica..cp_materiales_sap';
$config['bd_stock_seriado_sap']    = 'bd_logistica..bd_stock_sap';
$config['bd_stock_seriado_sap_03'] = 'bd_logistica..bd_stock_03';
$config['bd_stock_scl']            = 'bd_logistica..bd_stock_scl';
$config['bd_al_bodegas']           = 'bd_logistica..al_bodegas';
$config['bd_al_tipos_bodegas']     = 'bd_logistica..al_tipos_bodegas';
$config['bd_al_tipos_stock']       = 'bd_logistica..al_tipos_stock';
$config['bd_al_estados']           = 'bd_logistica..al_estados';
$config['bd_al_usos']              = 'bd_logistica..al_usos';
$config['bd_trafico_mes']          = 'bd_logistica..trafico_mes';
$config['bd_trafico_abocelamist']  = 'bd_controles..trafico_abocelamist';
$config['bd_trafico_clientes']     = 'bd_controles..trafico_clientes';
$config['bd_trafico_causabaja']    = 'bd_controles..trafico_causabaja';
$config['bd_trafico_dias_proc']    = 'bd_controles..trafico_dias_procesados';
$config['bd_pmp']                  = 'bd_planificacion..ca_stock_sap_04';

// Despachos
$config['bd_despachos_pack']       = 'bd_logistica..despachos_sap_res01_pack';

// TOA
$config['bd_tecnicos_toa']         = 'toa_tecnicos';
$config['bd_empresas_toa']         = 'toa_empresas';
$config['bd_empresas_toa_tiposalm'] = 'toa_empresas_tiposalm';


/* End of file inv-fija.php */
/* Location: ./application/config/inv-fija.php */