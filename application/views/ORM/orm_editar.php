<div class="row-fluid">
	<?php echo $menu_configuracion; ?>
</div>

<?php if ($msg_alerta != ''): ?>
	<div class="alert">
		<button type="button" class="close" data-dismiss="alert">&times;</button>
		<?php echo $msg_alerta; ?>
	</div>
<?php endif; ?>

<div class="span8 offset2">
	<?php echo form_open('', 'id="frm_editar" class="form-horizontal"'); ?>

	<?php foreach ($modelo as $campo => $valor): ?>
		<div class="control-group">
			<label class="control-label">
				<?php echo ucfirst($modelo->get_label_field($campo)); ?>
				<?php echo ($modelo->get_es_obligatorio_field($campo)) ? '<span>[*]</span>' : ''; ?>

			</label>

			<div class="controls">
				<?php echo $modelo->print_form_field($campo); ?>
				<span class="help-block"><?php echo $modelo->get_texto_ayuda_field($campo); ?></span>
				<?php echo form_error($campo); ?>
			</div>
		</div>
	<?php endforeach; ?>

	<div class="control-group">
		<div class="controls">
			<button type="submit" class="btn btn-primary" name="grabar" value="grabar">
				<i class="icon-ok icon-white"></i>
				Guardar
			</button>
			<button type="submit" class="btn" name="borrar" value="borrar" onclick="return confirm('Esta seguro de borrar este(a) <?php echo strtolower($modelo->get_model_label()); ?>?\n\nEliminar: <?php echo strtoupper($modelo->__toString()); ?>\n');">
				<i class="icon-trash"></i>
				Borrar
			</button>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
