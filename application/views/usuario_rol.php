<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>

		<div class="fr">
			<a href="#" class="button b-active round ic-desplegar fl" id="btn_mostrar_agregar">Nuevo usuario/rol ...</a>
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
					<th class="ac">Usuario</th>
					<th class="ac">Rol</th>
					<th></th>
				</tr>
			</thead>
			<tr>
				<td class="ac">
					<?php echo form_dropdown('agr-id_usuario', $combo_usuarios, set_value('agr-id_usuario')); ?>
					<?php echo form_error('agr-id_usuario'); ?>
				</td>
				<td class="ac">
					<?php echo form_dropdown('agr-id_rol', $combo_rol, set_value('agr-id_rol')); ?>
					<?php echo form_error('agr-id_rol'); ?>
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
					<th>Usuario</th>
					<th>Rol</th>
					<th class="ac">Borrar</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($datos_hoja as $reg): ?>
				<tr>
					<td><?php echo $reg['nombre']; ?></td>
					<td><?php echo $reg['app'] . ': ' . $reg['rol']; ?></td>
					<td class="ac">
						<a href="#" class="button_micro b-active round boton-borrar" id="btn_borrar" id-usuario="<?php echo $reg['id_usuario']; ?>" id-rol="<?php echo $reg['id_rol'];?>">
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
		<?php echo form_hidden('id_usuario'); ?>
		<?php echo form_hidden('id_rol'); ?>
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
			var id_usuario = $(this).attr('id-usuario');
			var id_rol     = $(this).attr('id-rol');
			if (confirm('Seguro que desea borrar la asociacion usuario (id=' + id_usuario + ') / rol (id=' + id_rol + ')')) {
				$('form#frm_borrar input[name="id_usuario"]').val(id_usuario);
				$('form#frm_borrar input[name="id_rol"]').val(id_rol);
				$('form#frm_borrar').submit();
			}
		});


	});
</script>
