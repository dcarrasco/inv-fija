<div class="row">
	<div class="text-right">
		<a href="#" class="btn btn-default" id="btn_mostrar_agregar">
			<i class="glyphicon glyphicon-plus-sign"></i>
			Agregar ubicacion
		</a>
	</div>
</div>

<div class="row">
	<div class="col-md-10 col-md-offset-1 well" id="form_agregar" style="display: none;">
		<?php echo form_open('','id=frm_agregar')?>
		<?php echo form_hidden('formulario','agregar'); ?>

		<div class="row">
			<div class="col-md-4">
				Tipo de Inventario
				<?php echo form_dropdown('agr-tipo_inventario', $combo_tipos_inventario, set_value('agr-tipo_inventario'), 'class="form-control"'); ?>
				<?php echo form_error('agr-tipo_inventario'); ?>
			</div>
			<div class="col-md-4">
				Ubicacion
				<?php echo form_multiselect('agr-ubicacion[]', array(), set_value('agr-ubicacion[]'), 'size="15" class="form-control"'); ?>
				<?php echo form_error('agr-ubicacion'); ?>
			</div>
			<div class="col-md-4">
				Tipo de Ubicacion
				<?php echo form_dropdown('agr-tipo_ubicacion', array('' => 'Seleccione tipo ubicacion...'), set_value('agr-tipo_ubicacion'), 'class="form-control"'); ?>
				<?php echo form_error('agr-tipo_ubicacion'); ?>
			</div>
		</div>

		<div class="row">
			<div class="pull-right">
				<a href="#" class="btn btn-primary" id="btn_agregar">
					<i class="glyphicon glyphicon-ok"></i>
					Agregar
				</a>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->
</div>

<div class="row">
	<div class="">
		<?php echo form_open('', 'id="frm_usuarios"'); ?>
		<?php echo form_hidden('formulario','editar'); ?>
		<table class="table table-hover table-condensed">
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
				<?php foreach ($datos_hoja as $reg): ?>
				<tr>
					<td><?php echo $reg['id']; ?></td>
					<td>
						<?php echo form_dropdown($reg['id'].'-tipo_inventario', $combo_tipos_inventario, set_value($reg['id'].'-tipo_inventario', $reg['tipo_inventario']), 'class="form-control"'); ?>
						<?php echo form_error($reg['id'].'-tipo_inventario'); ?>
					</td>
					<td>
						<?php echo form_dropdown($reg['id'].'-tipo_ubicacion', $combo_tipos_ubicacion[$reg['tipo_inventario']], set_value($reg['id'].'-tipo_ubicacion', $reg['id_tipo_ubicacion']), 'class="form-control"'); ?>
						<?php echo form_error($reg['id'].'-tipo_ubicacion'); ?>
					</td>
					<td>
						<?php echo form_input($reg['id'].'-ubicacion', set_value($reg['id'].'-ubicacion', $reg['ubicacion']),'maxlength="45" class="form-control"'); ?>
						<?php echo form_error($reg['id'].'-ubicacion'); ?>
					</td>
					<td>
						<a href="#" class="btn btn-default" id="btn_borrar" id-borrar="<?php echo $reg['id']; ?>">
							<i class="glyphicon glyphicon-trash"></i>
						</a>
					</td>
				</tr>
				<?php endforeach; ?>
			</tbody>
		</table>
		<?php echo form_close(); ?>

		<div class="text-center">
			<?php echo ($links_paginas != '') ? $links_paginas : ''; ?>
		</div>
	</div> <!-- fin content-module-main -->

	<div class="pull-right">
		<a href="#" class="btn btn-primary" id="btn_guardar">
			<i class="glyphicon glyphicon-ok"></i>
			Guardar
		</a>
	</div> <!-- fin content-module-footer -->

	<?php echo form_open('','id="frm_borrar"'); ?>
		<?php echo form_hidden('formulario','borrar'); ?>
		<?php echo form_hidden('id_borrar'); ?>
	<?php echo form_close(); ?>


</div> <!-- fin content-module -->

<script type="text/javascript">
	$(document).ready(function() {
		if ($('div.content-module-main-agregar div.error').length > 0) {
			$('div.content-module-main-agregar').toggle();
		}

		$('#btn_mostrar_agregar').click(function (event) {
			event.preventDefault();
			$('div#form_agregar').toggle();
		});

		$('#btn_guardar').click(function (event) {
			event.preventDefault();
			$('form#frm_usuarios').submit();
		});

		$('#btn_agregar').click(function (event) {
			event.preventDefault();
			$('form#frm_agregar').submit();
		});

		$('a.boton-borrar').click(function (event) {
			event.preventDefault();
			var id_borrar = $(this).attr('id-borrar');
			if (confirm('Seguro que desea borrar el usuario id=' + id_borrar)) {
				$('form#frm_borrar input[name="id_borrar"]').val(id_borrar);
				$('form#frm_borrar').submit();
			}
		});

		$('form#frm_agregar select[name="agr-tipo_inventario"]').change(function() {

			var url_json_ubic = js_base_url + 'config2/get_json_ubicaciones_libres/' + $(this).val() + '/' + Date.now();
			$.getJSON(url_json_ubic, function(data) {
				var items = [];
				$.each(data, function(key, val) {
					items.push('<option value="' + key + '">' + val + '</option>');
				});
				$('select[name="agr-ubicacion[]"]').empty().append(items.join(''));
			});

			var url_json_tipo = js_base_url + 'config2/get_json_tipo_ubicacion/' + $(this).val() + '/' + Date.now();
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
