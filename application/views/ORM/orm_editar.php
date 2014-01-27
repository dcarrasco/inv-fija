<div class="row">
	<div class="col-md-8 col-md-offset-2 well">
		<?php echo form_open('', 'id="frm_editar" class="form-horizontal"'); ?>

		<?php foreach ($modelo as $campo => $valor): ?>
			<div class="control-group">
				<label class="control-label">
					<?php echo ucfirst($modelo->get_label_field($campo)); ?>
					<?php echo $modelo->get_marca_obligatorio_field($campo); ?>
				</label>

				<div class="controls">
					<?php echo $modelo->print_form_field($campo); ?>
					<span class="help-block">
						<em><small><?php echo $modelo->get_texto_ayuda_field($campo); ?></small></em>
					</span>
					<?php echo form_error($campo); ?>
				</div>
			</div>
		<?php endforeach; ?>

		<hr />
		<div class="control-group">
			<div class="controls">
				<div class="pull-right">
					<button type="submit" class="btn btn-primary" name="grabar" value="grabar">
						<span class="glyphicon glyphicon-ok"></span>
						Guardar
					</button>
					<button type="submit" class="btn btn-default" name="cancelar" value="cancelar">
						<span class="glyphicon glyphicon-ban-circle"></span>
						Cancelar
					</button>
				</div>
				<div class="pull-left">
					<button type="submit" class="btn btn-danger" name="borrar" value="borrar" onclick="return confirm('Esta seguro de borrar este(a) <?php echo strtolower($modelo->get_model_label()); ?>?\n\nEliminar: <?php echo strtoupper($modelo->__toString()); ?>\n');">
						<span class="glyphicon glyphicon-trash"></span>
						Borrar
					</button>
				</div>
			</div>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>
