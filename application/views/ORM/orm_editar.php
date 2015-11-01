<div class="row">
	<div class="col-md-8 col-md-offset-2 well">
		<?php echo form_open('', 'id="frm_editar" class="form-horizontal" role="form"'); ?>
		<fieldset>
			<legend>
				<?php echo ($modelo->get_model_id() ? 'Editar ' : 'Crear ') . $modelo->get_model_label() ?>
			</legend>

			<?php echo print_validation_errors(); ?>

			<?php foreach ($modelo as $campo => $valor): ?>
				<?php echo $modelo->form_item($campo); ?>
			<?php endforeach; ?>

			<div class="form-group">
				<label class="control-label col-sm-4">
				</label>
				<div class="col-sm-8">
					<div class="pull-right">
						<button type="submit" class="btn btn-primary" name="grabar" value="grabar">
							<span class="glyphicon glyphicon-ok"></span>
							<?php echo $this->lang->line('orm_button_save'); ?>
						</button>
						<a href="<?php echo $link_cancelar; ?>" class="btn btn-default">
							<span class="glyphicon glyphicon-ban-circle"></span>
							<?php echo $this->lang->line('orm_button_cancel'); ?>
						</a>
					</div>

					<?php if ($modelo->get_model_id()): ?>
					<div class="pull-left">
						<button type="submit" class="btn btn-danger" name="borrar" value="borrar" onclick="return confirm('<?php echo sprintf($this->lang->line('orm_js_delete_confirm'), strtolower($modelo->get_model_label()), strtoupper($modelo)); ?>');">
							<span class="glyphicon glyphicon-trash"></span>
							<?php echo $this->lang->line('orm_button_delete'); ?>
						</button>
					</div>
					<?php endif; ?>

				</div>
			</div>

		</fieldset>
		<?php echo form_close(); ?>

	</div>
</div>
