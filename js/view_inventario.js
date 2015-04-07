$(document).ready(function() {

	if ($('#formulario_agregar div.alert-danger').length > 0) {
		$('#formulario_agregar').toggle();
		$('#formulario_digitador').toggle();
 		$('#btn_guardar').toggle();
	}

	$('#btn_mostrar_agregar').click(function (event) {
		event.preventDefault();
		$('#agr_material').html('<option value="">Buscar y seleccionar material...</option>');
		$('#frm_agregar input[name="id"]').val('0');
		$('#frm_agregar input[name="ubicacion"]').val('');
		$('#frm_agregar input[name="hu"]').val('');
		$('#frm_agregar select[name="agr_material"]').val('');
		$('#frm_agregar input[name="lote"]').val('');
		$('#frm_agregar select[name="um"]').val('');
		$('#frm_agregar select[name="centro"]').val('');
		$('#frm_agregar select[name="almacen"]').val('');
		$('#frm_agregar input[name="stock_fisico"]').val('');
		$('#frm_agregar input[name="observacion"]').val('');

		$('#formulario_agregar').toggle();
		$('#formulario_digitador').toggle();
		$('#btn_guardar').toggle();
	});

	$('#btn_buscar').click(function (event) {
		event.preventDefault();
		$('form#frm_buscar').submit();
	});

	$('#btn_guardar').click(function (event) {
		event.preventDefault();
		$('form#frm_inventario input[name="auditor"]').val($('#id_auditor').val());
		$('form#frm_inventario').submit();
	});

	$('#btn_agregar').click(function (event) {
		event.preventDefault();
		$('#frm_agregar').submit();
	});

	$('#btn_cancelar').click(function (event) {
		event.preventDefault();
		$('#formulario_agregar').toggle();
		$('#formulario_digitador').toggle();
		$('#btn_mostrar_agregar').show();
		$('#btn_guardar').toggle();
	});

	$('#btn_borrar').click(function (event) {
		event.preventDefault();
		$('#frm_agregar input[name="accion"]').val('borrar');
		$('#frm_agregar').submit();
	});

	$('#agr_filtrar').bind('keypress', function (event) {
		if(event.keyCode === 13) {
			event.preventDefault();
			actualizaMateriales($('#agr_filtrar').val());
		}
	});

	$('#agr_filtrar').blur(function () {
		actualizaMateriales($('#agr_filtrar').val());
	});

	$('a[id^="btn_editar_id"]').click(function (event) {
		event.preventDefault();
		var id_elem = $(this).attr('id');
		var id = id_elem.substr(14, id_elem.length);
		$('#formulario_agregar').toggle();
		$('#formulario_digitador').toggle();
		$('#btn_mostrar_agregar').toggle();
		$('#btn_guardar').toggle();

		$('#agr_material').html('<option value="' + $('form#frm_inventario input[name="catalogo_' + id + '"]').val() + '">' + $('form#frm_inventario input[name="descripcion_' + id + '"]').val()+ '</option>');
		$('#frm_agregar input[name="id"]').val(id);
		$('#frm_agregar input[name="ubicacion"]').val($('form#frm_inventario input[name="ubicacion_' + id + '"]').val());
		$('#frm_agregar input[name="hu"]').val($('form#frm_inventario input[name="hu_' + id + '"]').val());
		$('#frm_agregar select[name="agr_material"]').val($('form#frm_inventario input[name="catalogo_' + id + '"]').val());
		$('#frm_agregar input[name="lote"]').val($('form#frm_inventario input[name="lote_' + id + '"]').val());
		$('#frm_agregar select[name="um"]').val($('form#frm_inventario input[name="um_' + id + '"]').val());
		$('#frm_agregar select[name="centro"]').val($('form#frm_inventario input[name="centro_' + id + '"]').val());
		$('#frm_agregar select[name="almacen"]').val($('form#frm_inventario input[name="almacen_' + id + '"]').val());
		$('#frm_agregar input[name="stock_fisico"]').val($('form#frm_inventario input[name="stock_fisico_' + id + '"]').val());
		$('#frm_agregar input[name="observacion"]').val($('form#frm_inventario textarea[name="observacion_' + id + '"]').val());
		$('body').scrollTop(0);
	});

	function actualizaMateriales(filtro) {
		var tt = new Date().getTime();
		var url_datos = js_base_url + 'inventario_digitacion/ajax_act_agr_materiales/' + filtro + '/' + tt;
		$.get(url_datos, function (data) {$('#agr_material').html(data); });
	}

});