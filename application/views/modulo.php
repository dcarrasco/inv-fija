<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>

		<div class="fr">
			<a href="#" class="button b-active round ic-desplegar fl" id="btn_mostrar_agregar">Nuevo modulo ...</a>
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
					<th class="ac">Aplicacion</th>
					<th class="ac">Modulo</th>
					<th class="ac">Descripcion</th>
					<th class="ac">Llave Modulo</th>
					<th></th>
				</tr>
			</thead>
			<tr>
				<td class="ac">
					<?php echo form_dropdown('agr-id_app', $combo_aplicaciones, set_value('agr-id_app')); ?>
					<?php echo form_error('agr-id_app'); ?>
				</td>
				<td class="ac">
					<?php echo form_input('agr-modulo', set_value('agr-modulo'), 'maxlength="50" size="50"'); ?>
					<?php echo form_error('agr-modulo'); ?>
				</td>
				<td class="ac">
					<?php echo form_input('agr-descripcion', set_value('agr-descripcion'), 'maxlength="100" size="50"'); ?>
					<?php echo form_error('agr-descripcion'); ?>
				</td>
				<td class="ac">
					<?php echo form_input('agr-llave_modulo', set_value('llave_modulo'), 'maxlength="100" size="50"'); ?>
					<?php echo form_error('agr-llave_modulo'); ?>
				</td>
				<td class="ac">		
					<a href="#" class="button b-active round ic-agregar fl" id="btn_agregar">Agregar</a>
				</td>
			</tr>
		</table>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->

	<div class="content-module-main">
		<?php echo form_open('', 'id="frm_editar"'); ?>
		<?php echo form_hidden('formulario','editar'); ?>
		<table>
			<thead>
				<tr>
					<th>id</th>
					<th>Aplicacion</th>
					<th>Modulo</th>
					<th>Descripcion</th>
					<th>Llave modulo</th>
					<th class="ac">Borrar</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($datos_hoja as $reg): ?>
				<tr>
					<td><?php echo $reg['id']; ?></td>
					<td>
						<?php echo form_dropdown($reg['id'].'-id_app', $combo_aplicaciones, set_value($reg['id'].'-id_app', $reg['id_app'])); ?>
						<?php echo form_error($reg['id'].'-id_app'); ?>
					</td>
					<td>
						<?php echo form_input($reg['id'].'-modulo', set_value($reg['id'].'-modulo', $reg['modulo']),'maxlength="50" size="50"'); ?>
						<?php echo form_error($reg['id'].'-modulo'); ?>
					</td>
					<td>
						<?php echo form_input($reg['id'].'-descripcion', set_value($reg['id'].'-descripcion', $reg['descripcion']),'maxlength="100" size="50"'); ?>
						<?php echo form_error($reg['id'].'-descripcion'); ?>
					</td>
					<td>
						<?php echo form_input($reg['id'].'-llave_modulo', set_value($reg['id'].'-llave_modulo', $reg['llave_modulo']),'maxlength="50" size="50"'); ?>
						<?php echo form_error($reg['id'].'-llave_modulo'); ?>
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
					<td colspan="6"><div class="paginacion ac"><?php echo $links_paginas; ?></div></td>
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
			$('form#frm_editar').submit();
		});			

		$('#btn_agregar').click(function (event) {
			event.preventDefault();
			$('form#frm_agregar').submit();
		});			

		$('a.boton-borrar').click(function (event) {
			event.preventDefault();
			var id_borrar = $(this).attr('id-borrar');
			if (confirm('Seguro que desea borrar el modulo id=' + id_borrar)) {
				$('form#frm_borrar input[name="id_borrar"]').val(id_borrar);
				$('form#frm_borrar').submit();
			}
		});


	});
</script>
