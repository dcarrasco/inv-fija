$(document).ready(function() {

	var alto_div_fijo = $('div.content-module-heading').height() + $('div.content-module-footer').height() + 70;
	var alto_div = $(window).height() - alto_div_fijo;
	$('div.content-module-main-principal').css({'overflow': 'auto'});
	$('div.content-module-main-principal').height(alto_div);

	if ($('div.content-module-main-agregar div.error').length > 0) {
		$('div.content-module-main-agregar').toggle();
		$('#btn_guardar').toggle();
	}

	$('#btn_mostrar_agregar').click(function (event) {
		event.preventDefault();
		$('#agr_material').html('<option value="">Seleccionar material...</option>');
		$('form#frm_agregar input[name="agr_id"]').val('0');
		$('form#frm_agregar input[name="agr_ubicacion"]').val('');
		$('form#frm_agregar select[name="agr_material"]').val('');
		$('form#frm_agregar input[name="agr_lote"]').val('');
		$('form#frm_agregar select[name="agr_um"]').val('');
		$('form#frm_agregar select[name="agr_centro"]').val('');
		$('form#frm_agregar select[name="agr_almacen"]').val('');
		$('form#frm_agregar input[name="agr_cantidad"]').val('');
		$('form#frm_agregar input[name="agr_observacion"]').val('');

		$('div.content-module-main-agregar').toggle();
		$('#btn_guardar').toggle();
		$('form#frm_agregar a#btn_agregar').html('Agregar material');
	});

	$('#btn_buscar').click(function (event) {
		event.preventDefault();
		$('form#frm_buscar').submit();
	});

	$('#btn_guardar').click(function (event) {
		event.preventDefault();
		$('form#frm_inventario input[name="sel_auditor"]').val($('form#frm_buscar select[name="sel_auditor"]').val());
		$('form#frm_inventario').submit();
	});

	$('#btn_agregar').click(function (event) {
		event.preventDefault();
		$('form#frm_agregar').submit();
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
		$('div.content-module-main-agregar').toggle();
		$('#btn_guardar').toggle();

		$('#agr_material').html('<option value="' + $('form#frm_inventario input[name="catalogo_' + id + '"]').val() + '">' + $('form#frm_inventario input[name="descripcion_' + id + '"]').val()+ '</option>');
		$('form#frm_agregar input[name="agr_id"]').val(id);
		$('form#frm_agregar input[name="agr_ubicacion"]').val($('form#frm_inventario input[name="ubicacion_' + id + '"]').val());
		$('form#frm_agregar select[name="agr_material"]').val($('form#frm_inventario input[name="catalogo_' + id + '"]').val());
		$('form#frm_agregar input[name="agr_lote"]').val($('form#frm_inventario input[name="lote_' + id + '"]').val());
		$('form#frm_agregar select[name="agr_um"]').val($('form#frm_inventario input[name="um_' + id + '"]').val());
		$('form#frm_agregar select[name="agr_centro"]').val($('form#frm_inventario input[name="centro_' + id + '"]').val());
		$('form#frm_agregar select[name="agr_almacen"]').val($('form#frm_inventario input[name="almacen_' + id + '"]').val());
		$('form#frm_agregar input[name="agr_cantidad"]').val($('form#frm_inventario input[name="stock_fisico_' + id + '"]').val());
		$('form#frm_agregar input[name="agr_observacion"]').val($('form#frm_inventario input[name="observacion_' + id + '"]').val());
		$('form#frm_agregar a#btn_agregar').html('Guardar material');
	});

	function actualizaMateriales(filtro) {
		var tt = new Date().getTime();
		var url_datos = js_base_url + 'index.php/inventario/ajax_act_agr_materiales/' + filtro + '/' + tt;
		$.get(url_datos, function (data) {$('#agr_material').html(data); });
	}

});