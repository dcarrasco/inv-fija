<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>

	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main-agregar">
		<?php echo $modelo->render_form($modelo->fields); ?>
	</div> <!-- fin content-module-main-agregar -->

	<div class="content-module-footer cf">
		<?php echo form_submit('borrar', 'Borrar', 'class="button b-active round ic-borrar fr"'); ?>
		<?php echo form_submit('grabar', 'Guardar', 'class="button b-active round ic-ok fr"'); ?>
	</div> <!-- fin content-module-footer -->
	<?php echo form_close(); ?>

</div> <!-- fin content-module -->
