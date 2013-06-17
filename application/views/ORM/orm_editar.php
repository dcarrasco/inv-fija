<div class="row-fluid">
	<div class="span8 offset2 well">
		<?php echo form_open('', 'id="frm_editar" class="form-horizontal"'); ?>

		<?php foreach ($modelo as $campo => $valor): ?>
			<div class="control-group">
				<label class="control-label">
					<?php echo ucfirst($modelo->get_label_field($campo)); ?>
					<?php echo $modelo->get_marca_obligatorio_field($campo); ?>

				</label>

				<div class="controls">
					<?php echo $modelo->print_form_field($campo); ?>
					<span class="help-block"><?php echo $modelo->get_texto_ayuda_field($campo); ?></span>
					<?php echo form_error($campo); ?>
				</div>
			</div>
		<?php endforeach; ?>
		<hr />
		<div class="control-group">
			<div class="controls">
				<div class="pull-left">
					<button type="submit" class="btn btn-danger" name="borrar" value="borrar" onclick="return confirm('Esta seguro de borrar este(a) <?php echo strtolower($modelo->get_model_label()); ?>?\n\nEliminar: <?php echo strtoupper($modelo->__toString()); ?>\n');">
						<i class="icon-trash icon-white"></i>
						Borrar
					</button>
				</div>
				<div class="pull-right">
					<button type="submit" class="btn btn-primary" name="grabar" value="grabar">
						<i class="icon-ok icon-white"></i>
						Guardar
					</button>
					<button type="submit" class="btn" name="cancelar" value="cancelar">
						<i class="icon-ban-circle"></i>
						Cancelar
					</button>
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
