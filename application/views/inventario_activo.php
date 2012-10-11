<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>

		<div class="fr">
			<a href="#" class="button b-active round ic-desplegar fl" id="btn_mostrar_agregar">Nuevo inventario ...</a>
		</div>

	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta cf ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main-agregar" style="display: none;">
		<?php echo form_open('','id=frm_agregar')?>
		<?php echo form_hidden('formulario','agregar'); ?>
		<table>
			<thead>
				<tr>
					<th class="ac">Nombre</th>
					<th class="ac">Activo</th>
					<th></th>
				</tr>
			</thead>
			<tr>
				<td class="ac">
					<?php echo form_input('agr_nombre', set_value('agr_nombre'), 'maxlength="100" size="60"'); ?>
					<?php echo form_error('agr_nombre'); ?>
				</td>
				<td class="ac">
					<?php echo form_checkbox('agr_activo', 'agr_activo', set_value('agr_activo')); ?>
				</td>
				<td class="ac">		
					<a href="#" class="button b-active round ic-agregar fl" id="btn_agregar">Agregar</a>
				</td>
			</tr>
		</table>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->

	<div class="content-module-main">
		<?php echo form_open('', 'id="frm_inventario_activo"'); ?>
		<?php echo form_hidden('formulario','inventario_activo'); ?>
		<table>
			<thead>
				<tr>
					<th class="ac">id</th>
					<th>Nombre</th>
					<th class="ac">Tipo Inventario</th>
					<th class="ac">Activo</th>
					<th class="ac">Borrar</th>
					<th>Acciones Inventario</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($datos_hoja as $reg): ?>
				<tr>
					<td class="ac"><?php echo $reg['id']; ?></td>
					<td>
						<?php echo form_input($reg['id'].'-nombre', set_value($reg['id'].'-nombre', $reg['nombre']),'maxlength="100" size="60"'); ?>
						<?php echo form_error($reg['id'].'-nombre'); ?>
					</td>
					<td class="ac">
						<?php echo form_dropdown($reg['id'].'-tipo', $arr_tipos_inventario, set_value($reg['id'].'-tipo', $reg['tipo_inventario'])); ?>
					</td>
					<td class="ac">
						<?php echo form_checkbox($reg['id'].'-activo', 1, set_checkbox($reg['id'].'-activo', 1, (($reg['activo'] == 1) ? TRUE: FALSE))); ?>
					</td>
					<td class="ac">
						<a href="#" class="button_micro b-active round boton-borrar" id="btn_borrar" id-borrar="<?php echo $reg['id']; ?>">
							<img src="<?php echo base_url(); ?>img/ic_delete.png" />
						</a>
					</td>
					<td>
						<?php if($reg['activo'] == 1): ?>
							<?php echo anchor('config/sube_stock' . '/' . $reg['id'], 'Sube stock');; ?> /
							<?php echo anchor('config/imprime_inventario' . '/' . $reg['id'], 'Imprime inventario');; ?>
						<?php endif; ?>
					</td>
				</tr>
				<?php endforeach; ?>

				<?php if ($links_paginas != ''):?>
				<tr>
					<td colspan="4"><div class="paginacion ac"><?php echo $links_paginas; ?></div></td>
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
			$('form#frm_inventario_activo').submit();
		});			

		$('#btn_agregar').click(function (event) {
			event.preventDefault();
			$('form#frm_agregar').submit();
		});			

		$('a.boton-borrar').click(function (event) {
			event.preventDefault();
			var id_inventario = $(this).attr('id-borrar');
			if (confirm('Seguro que desea borrar el inventario id=' + id_inventario)) {
				$('form#frm_borrar input[name="id_borrar"]').val(id_inventario);
				$('form#frm_borrar').submit();
			}
		});

	});
</script>
