$(document).ready(function() {

	if ($('#frm_agregar p.text-error').length > 0) {
		$('#frm_agregar').toggle();
		$('#btn_guardar').toggle();
	}

	$('#btn_mostrar_agregar').click(function (event) {
		event.preventDefault();
		$('#agr_material').html('<option value="">Buscar y seleccionar material...</option>');
		$('form#frm_agregar input[name="id"]').val('0');
		$('form#frm_agregar input[name="ubicacion"]').val('');
		$('form#frm_agregar input[name="hu"]').val('');
		$('form#frm_agregar select[name="agr_material"]').val('');
		$('form#frm_agregar input[name="lote"]').val('');
		$('form#frm_agregar select[name="um"]').val('');
		$('form#frm_agregar select[name="centro"]').val('');
		$('form#frm_agregar select[name="almacen"]').val('');
		$('form#frm_agregar input[name="stock_fisico"]').val('');
		$('form#frm_agregar input[name="observacion"]').val('');

		$('div#frm_agregar').toggle();
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
		$('form#frm_agregar').submit();
	});

	$('#btn_cancelar').click(function (event) {
		event.preventDefault();
		$('div#frm_agregar').toggle();
		$('#btn_guardar').toggle();
	});

	$('#btn_borrar').click(function (event) {
		event.preventDefault();
		$('form#frm_agregar input[name="accion"]').val('borrar');
		$('form#frm_agregar').submit();
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
		$('#frm_agregar').toggle();
		$('#btn_guardar').toggle();

		$('#agr_material').html('<option value="' + $('form#frm_inventario input[name="catalogo_' + id + '"]').val() + '">' + $('form#frm_inventario input[name="descripcion_' + id + '"]').val()+ '</option>');
		$('form#frm_agregar input[name="id"]').val(id);
		$('form#frm_agregar input[name="ubicacion"]').val($('form#frm_inventario input[name="ubicacion_' + id + '"]').val());
		$('form#frm_agregar input[name="hu"]').val($('form#frm_inventario input[name="hu_' + id + '"]').val());
		$('form#frm_agregar select[name="agr_material"]').val($('form#frm_inventario input[name="catalogo_' + id + '"]').val());
		$('form#frm_agregar input[name="lote"]').val($('form#frm_inventario input[name="lote_' + id + '"]').val());
		$('form#frm_agregar select[name="um"]').val($('form#frm_inventario input[name="um_' + id + '"]').val());
		$('form#frm_agregar select[name="centro"]').val($('form#frm_inventario input[name="centro_' + id + '"]').val());
		$('form#frm_agregar select[name="almacen"]').val($('form#frm_inventario input[name="almacen_' + id + '"]').val());
		$('form#frm_agregar input[name="stock_fisico"]').val($('form#frm_inventario input[name="stock_fisico_' + id + '"]').val());
		$('form#frm_agregar input[name="observacion"]').val($('form#frm_inventario textarea[name="observacion_' + id + '"]').val());
		$('body').scrollTop(0);
	});

	function actualizaMateriales(filtro) {
		var tt = new Date().getTime();
		var url_datos = js_base_url + 'inventario_digitacion/ajax_act_agr_materiales/' + filtro + '/' + tt;
		$.get(url_datos, function (data) {$('#agr_material').html(data); });
	}

});