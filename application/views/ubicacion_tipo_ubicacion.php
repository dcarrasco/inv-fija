<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>

		<div class="fr">
			<a href="#" class="button b-active round ic-desplegar fl" id="btn_mostrar_agregar">Nuevo tipo de ubicacion ...</a>
		</div>

	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main-agregar" style="display: none;">
		<?php echo form_open('','id=frm_agregar')?>
		<?php echo form_hidden('formulario','agregar'); ?>
		<table>
			<thead>
				<tr>
					<th class="ac">Tipo de Inventario</th>
					<th class="ac">Ubicacion</th>
					<th class="ac">Tipo de Ubicacion</th>
					<th></th>
				</tr>
			</thead>
			<tr>
				<td class="ac">
					<?php echo form_dropdown('agr-tipo_inventario', $combo_tipos_inventario, set_value('agr-tipo_inventario')); ?>
					<?php echo form_error('agr-tipo_inventario'); ?>
				</td>
				<td class="ac">
					<?php echo form_multiselect('agr-ubicacion[]', array(), set_value('agr-ubicacion[]'), 'size="20"'); ?>
					<?php echo form_error('agr-ubicacion'); ?>
				</td>
				<td class="ac">
					<?php echo form_dropdown('agr-tipo_ubicacion', array('' => 'Seleccione tipo ubicacion...'), set_value('agr-tipo_ubicacion')); ?>
					<?php echo form_error('agr-tipo_ubicacion'); ?>
				</td>
				<td class="ac">		
					<a href="#" class="button b-active round ic-agregar fl" id="btn_agregar">Agregar</a>
				</td>
			</tr>
		</table>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->

	<div class="content-module-main">
		<?php echo form_open('', 'id="frm_usuarios"'); ?>
		<?php echo form_hidden('formulario','editar'); ?>
		<table>
			<thead>
				<tr>
					<th>id</th>
					<th>Tipo de Inventario</th>
					<th>Tipo de Ubicacion</th>
					<th>Ubicacion</th>
					<th class="ac">Borrar</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($datos_hoja as $reg): ?>
				<tr>
					<td><?php echo $reg['id']; ?></td>
					<td>
						<?php echo form_dropdown($reg['id'].'-tipo_inventario', $combo_tipos_inventario, set_value($reg['id'].'-tipo_inventario', $reg['tipo_inventario'])); ?>
						<?php echo form_error($reg['id'].'-tipo_inventario'); ?>
					</td>
					<td>
						<?php echo form_dropdown($reg['id'].'-tipo_ubicacion', $combo_tipos_ubicacion[$reg['tipo_inventario']], set_value($reg['id'].'-tipo_ubicacion', $reg['id_tipo_ubicacion'])); ?>
						<?php echo form_error($reg['id'].'-tipo_ubicacion'); ?>
					</td>
					<td>
						<?php echo form_input($reg['id'].'-ubicacion', set_value($reg['id'].'-ubicacion', $reg['ubicacion']),'maxlength="45" size="45"'); ?>
						<?php echo form_error($reg['id'].'-ubicacion'); ?>
					</td>
					<td class="ac">
						<a href="#" class="button_micro b-active round boton-borrar" id="btn_borrar" id-borrar="<?php echo $reg['id']; ?>">
							<img src="<?php echo base_url(); ?>img/ic_delete.png" />
						</a>
					</td>
				</tr>
				<?php endforeach; ?>

				<?php if ($links_paginas != ''):?>
				<tr>
					<td colspan="5"><div class="paginacion ac"><?php echo $links_paginas; ?></div></td>
				</tr>
			<?php endif; ?>
			</tbody>
		</table>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
		<a href="#" class="button b-active round ic-ok fr" id="btn_guardar">Guardar</a>
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
			$('div.content-module-main-agregar').toggle();
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

			var url_json_ubic = js_base_url + 'index.php/config/get_json_ubicaciones_libres/' + $(this).val() + '/' + Date.now();
			$.getJSON(url_json_ubic, function(data) {
				var items = [];
				$.each(data, function(key, val) {
					items.push('<option value="' + key + '">' + val + '</option>');
				});
				$('select[name="agr-ubicacion[]"]').empty().append(items.join(''));
			});

			var url_json_tipo = js_base_url + 'index.php/config/get_json_tipo_ubicacion/' + $(this).val() + '/' + Date.now();
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
