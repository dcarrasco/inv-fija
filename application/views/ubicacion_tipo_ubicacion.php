<div class="row">
	<div class="col-md-12 text-right" id="btn_agregar_ubicacion">
		<a href="#" class="btn btn-primary" id="btn_mostrar_agregar">
			<span class="fa fa-plus-circle"></span>
			Agregar ubicacion
		</a>
	</div>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1 well" id="form_agregar" style="{display_form_agregar}">

		<fieldset>
			<?= form_open($add_url_form,'id=frm_agregar')?>

			<legend>{_inventario_config_add_ubicaciones_}</legend>

			{validation_errors}

			<div class="row">
				<div class="col-md-4 form-group <?= form_has_error_class('agr-tipo_inventario') ?>">
					<label class="control-label">Tipo de Inventario</label>
					<?= form_dropdown('agr-tipo_inventario', $combo_tipos_inventario, request('agr-tipo_inventario'), 'class="form-control input-sm"'); ?>
					<?= form_error('agr-tipo_inventario'); ?>
				</div>
				<div class="col-md-4 form-group <?= form_has_error_class('agr-ubicacion[]') ?>">
					<label class="control-label">Ubicaci&oacute;n</label>
					<?= form_multiselect('agr-ubicacion[]', [], request('agr-ubicacion[]'), 'size="15" class="form-control input-sm"'); ?>
					<?= form_error('agr-ubicacion'); ?>
				</div>
				<div class="col-md-4 form-group <?= form_has_error_class('agr-tipo_ubicacion') ?>">
					<label class="control-label">Tipo de Ubicaci&oacute;n</label>
					<?= form_dropdown('agr-tipo_ubicacion', ['' => 'Seleccione tipo ubicacion...'], request('agr-tipo_ubicacion'), 'class="form-control input-sm"'); ?>
					<?= form_error('agr-tipo_ubicacion'); ?>
				</div>
			</div>

			<div class="row">
				<div class="col-md-12 text-right">
					<a href="#" class="btn btn-primary" id="btn_agregar">
						<span class="fa fa-check"></span> Agregar
					</a>
					<a href="#" class="btn btn-default" id="btn_cancelar">
						<span class="fa fa-ban"></span> Cancelar
					</a>
				</div>
			</div>
			<?= form_close(); ?>

		</fieldset>
	</div> <!-- fin content-module-main-agregar -->
</div>

<div class="row">
	<div class="col-md-12">
		<?= form_open($update_url_form, 'id="frm-ubicaciones"'); ?>
		<table class="table table-hover table-condensed table-striped">
			<thead>
				<tr>
					<th>id</th>
					<th>Tipo de Inventario</th>
					<th>Tipo de Ubicacion</th>
					<th>Ubicacion</th>
					<th>Borrar</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($ubicaciones as $ubicacion): ?>
				<tr class="<?= $errors_id->has($ubicacion->id) ? 'danger' : '' ?>">
					<td>
						<?= $ubicacion->id ?>
					</td>
					<td class="<?= form_has_error_class("tipo_inventario[{$ubicacion->id}]") ?>">
						<?= form_dropdown("tipo_inventario[{$ubicacion->id}]", $combo_tipos_inventario, request("tipo_inventario[{$ubicacion->id}]", $ubicacion->tipo_inventario), 'class="form-control input-sm"'); ?>
						<?= errors("tipo_inventario[{$ubicacion->id}]"); ?>
					</td>
					<td class="<?= form_has_error_class("tipo_ubicacion[{$ubicacion->id}]") ?>">
						<?= form_dropdown("tipo_ubicacion[{$ubicacion->id}]", $combo_tipos_ubicacion[$ubicacion->tipo_inventario], request("tipo_ubicacion[{$ubicacion->id}]", $ubicacion->id_tipo_ubicacion), 'class="form-control input-sm"'); ?>
						<?= errors("tipo_ubicacion[{$ubicacion->id}]"); ?>
					</td>
					<td class="<?= form_has_error_class("ubicacion[{$ubicacion->id}]") ?>">
						<?= form_input("ubicacion[{$ubicacion->id}]", request("ubicacion[{$ubicacion->id}]", $ubicacion->ubicacion),'maxlength="45" class="form-control input-sm"'); ?>
						<?= errors("ubicacion[{$ubicacion->id}]"); ?>
					</td>
					<td>
						<a href="#" class="btn btn-default btn-sm" id="btn-borrar" data-rowid="<?= $ubicacion->id; ?>">
							<span class="fa fa-trash"></span>
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?= form_close(); ?>

		<div class="row">
			<div class="col-md-12 text-right">
				<a href="#" class="btn btn-primary" id="btn-guardar">
					<span class="fa fa-check"></span> Guardar
				</a>
			</div>
		</div>

		<div class="text-center">
			<?= Inventario\Ubicacion::create()->crea_links_paginas(); ?>
		</div>
	</div> <!-- fin content-module-main -->


	<?= form_open($borrar_url_form,'id="frm_borrar"'); ?>
		<?= form_hidden('id_borrar'); ?>
	<?= form_close(); ?>


</div> <!-- fin content-module -->

<script type="text/javascript">
	$(document).ready(function() {
		if ($('div.content-module-main-agregar div.error').length > 0) {
			$('div.content-module-main-agregar').toggle();
		}

		$('#btn_mostrar_agregar').click(function (event) {
			event.preventDefault();
			$('div#form_agregar').slideToggle(300);
			$('div#btn_agregar_ubicacion').toggle();
		});

		$('#btn_cancelar').click(function (event) {
			event.preventDefault();
			$('div#form_agregar').slideToggle(300);
			$('div#btn_agregar_ubicacion').toggle();
		});

		$('#btn-guardar').click(function (event) {
			event.preventDefault();
			$('#frm-ubicaciones').submit();
		});

		$('#btn_agregar').click(function (event) {
			event.preventDefault();
			$('form#frm_agregar').submit();
		});

		$('#frm-ubicaciones>table>tbody>tr>td>a').click(function (event) {
			event.preventDefault();
			var id_borrar = $(this).data('rowid');
			if (confirm('Seguro que desea borrar la ubicacion con id=' + id_borrar)) {
				$('form#frm_borrar input[name="id_borrar"]').val(id_borrar);
				$('form#frm_borrar').submit();
			}
		});

		$('form#frm_agregar select[name="agr-tipo_inventario"]').change(function() {

			var url_json_ubic = js_base_url + 'inventario_config/get_json_ubicaciones_libres/' + $(this).val() + '/' + Date.now();
			$.getJSON(url_json_ubic, function(data) {
				var items = [];
				$.each(data, function(key, val) {
					items.push('<option value="' + key + '">' + val + '</option>');
				});
				$('select[name="agr-ubicacion[]"]').empty().append(items.join(''));
			});

			var url_json_tipo = js_base_url + 'inventario_config/get_json_tipo_ubicacion/' + $(this).val() + '/' + Date.now();
			$.getJSON(url_json_tipo, function(data) {
				var items = [];
				$.each(data, function(key, val) {
					items.push('<option value="' + key + '">' + val + '</option>');
				});
				$('select[name="agr-tipo_ubicacion"]').empty().append(items.join(''));
			});
		});



	});
</script>
