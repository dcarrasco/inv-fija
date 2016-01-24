<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


$lang['inventario_config_menu_auditores']         = 'Auditores';
$lang['inventario_config_menu_familias']          = 'Familias';
$lang['inventario_config_menu_materiales']        = 'Materiales';
$lang['inventario_config_menu_tipos_inventarios'] = 'Tipos de inventario';
$lang['inventario_config_menu_inventarios']       = 'Inventarios';
$lang['inventario_config_menu_tipo_ubicacion']    = 'Tipo Ubicaci&oacute;n';
$lang['inventario_config_menu_ubicaciones']       = 'Ubicaciones';
$lang['inventario_config_menu_centros']           = 'Centros';
$lang['inventario_config_menu_almacenes']         = 'Almacenes';
$lang['inventario_config_menu_unidades_medida']   = 'Unidades de medida';

$lang['inventario_menu_reporte_hoja']      = 'Hoja';
$lang['inventario_menu_reporte_mat']       = 'Material';
$lang['inventario_menu_reporte_faltante']  = 'Faltante-Sobrante';
$lang['inventario_menu_reporte_ubicacion'] = 'Ubicaci&oacute;n';
$lang['inventario_menu_reporte_tip_ubic']  = 'Tipos Ubicaci&oacute;n';
$lang['inventario_menu_reporte_ajustes']   = 'Ajustes';

$lang['inventario_menu_ajustes']     = 'Ajustes de inventario';
$lang['inventario_menu_upload']      = 'Subir stock';
$lang['inventario_menu_print']       = 'Imprimir hojas';
$lang['inventario_menu_act_precios'] = 'Actualiza precios cat&aacute;logo';

$lang['inventario_inventario']      = 'Inventario';
$lang['inventario_page']            = 'Hoja';
$lang['inventario_auditor']         = 'Auditor';
$lang['inventario_button_new_line'] = 'Nuevo material...';

$lang['inventario_form_new']               = 'Ingreso datos inventario';
$lang['inventario_form_new_material']      = 'Material';
$lang['inventario_form_new_material_placeholder'] = 'Buscar...';
$lang['inventario_form_new_button_delete'] = 'Borrar';
$lang['inventario_form_new_button_add']    = 'Agregar registro inventario';
$lang['inventario_form_new_button_edit']   = 'Modificar registro inventario';
$lang['inventario_form_new_button_cancel'] = 'Cancelar';
$lang['inventario_digit_button_save_page'] = 'Guardar hoja';

$lang['inventario_digit_th_ubicacion']          = 'ubicaci&oacute;n';
$lang['inventario_digit_th_material']           = 'material';
$lang['inventario_digit_th_descripcion']        = 'descripci&oacute;n';
$lang['inventario_digit_th_lote']               = 'lote';
$lang['inventario_digit_th_centro']             = 'centro';
$lang['inventario_digit_th_almacen']            = 'almac&eacute;n';
$lang['inventario_digit_th_UM']                 = 'UM';
$lang['inventario_digit_th_cant_sap']           = 'cant SAP';
$lang['inventario_digit_th_cant_fisica']        = 'cant f&iacute;sica';
$lang['inventario_digit_th_HU']                 = 'HU';
$lang['inventario_digit_th_observacion']        = 'observaci&oacute;n';
$lang['inventario_digit_th_hoja']               = 'hoja';
$lang['inventario_digit_th_cant_ajuste']        = 'cant ajuste';
$lang['inventario_digit_th_dif']                = 'dif';
$lang['inventario_digit_th_tipo_dif']           = 'tipo dif';
$lang['inventario_digit_th_observacion_ajuste'] = 'observaci&oacute;n ajuste';

$lang['inventario_digit_msg_save']   = '%d linea(s) modificadas correctamente en hoja %d.';
$lang['inventario_digit_msg_delete'] = 'Linea (id=%d) borrada correctamente en hoja %d.';
$lang['inventario_digit_msg_add']    = 'Linea agregada correctamente en hoja %d.';
$lang['inventario_adjust_msg_save']  = '%d linea(s) modificadas correctamente.';
$lang['inventario_adjust_link_hide'] = 'Ocultar lineas regularizadas';
$lang['inventario_adjust_link_show'] = 'Mostrar lineas regularizadas';

$lang['inventario_report_filter']                 = 'Buscar texto...';
$lang['inventario_report_check_ocultar_regs']     = 'Ocultar registros sin diferencias';
$lang['inventario_report_check_incluir_ajustes']  = 'Incluir ajustes de inventario';
$lang['inventario_report_check_incluir_familias'] = 'Incluir familias de productos';
$lang['inventario_report_label_inventario']       = 'Inventario';
$lang['inventario_report_label_faltante']         = 'Faltante';
$lang['inventario_report_label_sobrante']         = 'Sobrante';
$lang['inventario_report_label_OK']               = 'OK';
$lang['inventario_report_save']                   = 'Guardar ajustes';

$lang['inventario_upload_label_fieldset']   = 'Subir archivo de stock';
$lang['inventario_upload_label_inventario'] = 'Inventario';
$lang['inventario_upload_label_file']       = 'Archivo';
$lang['inventario_upload_label_password']   = 'Clave administrador';
$lang['inventario_upload_label_format']     = 'Formato del archivo';
$lang['inventario_upload_label_progress']   = 'Progreso';
$lang['inventario_upload_error']            = 'Se produjo un error tratar de subir el archivo de inventario.';
$lang['inventario_upload_warning_line1']    = 'ADVERTENCIA';
$lang['inventario_upload_warning_line2']    = 'Al subir un archivo se eliminar&aacute;n <strong>TODOS</strong> los registros asociados al inventario';
$lang['inventario_upload_status_OK']        = 'Registros cargados OK:';
$lang['inventario_upload_status_error']     = 'Registros con error:';
$lang['inventario_upload_button_load']      = 'Ejecutar carga';
$lang['inventario_upload_button_upload']    = 'Subir archivo';
$lang['inventario_upload_format_file']      = "Archivo de texto
Extension .txt
Campos separados por tabulación

Campos
		Ubicacion
		[HU - eliminada]
		Catalogo
		Descripcion catalogo
		Lote
		Centro
		Almacen
		Unidad de medida
		Stock SAP
		Hoja";

$lang['inventario_print_label_legend']       = 'Imprimir inventario';
$lang['inventario_print_label_inventario']   = 'Inventario';
$lang['inventario_print_label_page_from']    = 'P&aacute;gina desde';
$lang['inventario_print_label_page_to']      = 'P&aacute;gina hasta';
$lang['inventario_print_label_options']      = 'Opciones';
$lang['inventario_print_check_hide_columns'] = 'Oculta columnas [STOCK_SAP] y [F/A]';
$lang['inventario_print_button_print']       = 'Imprimir';

$lang['inventario_act_precios_button'] = 'Actualizar precios';
$lang['inventario_act_precios_msg']    = 'Actualizados %s registros.';



/* End of file orm_lang.php */
/* Location: ./application/language/spanish/orm_lang.php */