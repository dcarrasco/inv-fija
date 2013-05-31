<div class="container-fluid">

	<div>
		<div>
			<?php echo $menu_configuracion; ?>
		</div>

	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div>
		<?php echo form_open('', 'id="frm_editar" class="form-horizontal"'); ?>

		<?php foreach ($modelo as $campo => $valor): ?>
		<div class="control-group">
			<label class="control-label"><?php echo ucfirst($modelo->get_label_field($campo)); ?></label>
			<div class="controls">
				<?php echo $modelo->print_form_field($campo); ?>
				<?php echo ($modelo->get_es_obligatorio_field($campo)) ? '<span class="form_requerido">*</span>' : ''; ?>
				<?php echo form_error($campo); ?>
				<span class="help-block"><?php echo $modelo->get_texto_ayuda_field($campo); ?></span>
			</div>
		</div>
		<?php endforeach; ?>

		<div class="control-group">
			<div class="controls">
				<button type="submit" class="btn btn-primary" name="grabar"><i class="icon-ok icon-white"></i>Guardar</button>
				<button type="submit" class="btn" name="borrar" onclick="return confirm('Esta seguro de borrar este(a) <?php echo strtolower($modelo->get_model_label()); ?>?\n\nEliminar: <?php echo strtoupper($modelo->__toString()); ?>\n\');"><i class="icon-trash"></i>Borrar</button>
			</div>
		</div> <!-- fin content-module-footer -->
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->


</div> <!-- fin content-module -->
