<div class="row">
	<div class="col-md-10 col-md-offset-1 well">

		<?= form_open($url_form, 'id="frm_editar" class="form-horizontal" role="form"'); ?>
		<fieldset>

			<legend>
				<?php if ($modelo->get_model_id()): ?>
					{_orm_title_edit_}
				<?php else: ?>
					{_orm_title_create_}
				<?php endif ?>
				<?= $modelo->get_model_label() ?>
			</legend>

			{validation_errors}

			<?php foreach ($modelo as $campo => $valor): ?>
				<?= $modelo->form_item($campo); ?>
			<?php endforeach; ?>

			<div class="form-group">
				<label class="control-label col-sm-4">
				</label>
				<div class="col-sm-8">
					<div class="pull-right">
						<button type="submit" class="btn btn-primary" name="grabar" value="grabar">
							<span class="fa fa-check"></span>
							{_orm_button_save_}
						</button>
						<a href="{link_cancelar}" class="btn btn-default" role="button">
							<span class="fa fa-ban"></span>
							{_orm_button_cancel_}
						</a>
					</div>

					<?php if ($modelo->get_model_id()): ?>
					<div class="pull-left">
						<button type="submit" class="btn btn-danger" name="borrar" value="borrar" onclick="return confirm('<?= sprintf('{_orm_js_delete_confirm_}', strtolower($modelo->get_model_label()), strtoupper($modelo)); ?>');">
							<span class="fa fa-trash-o"></span>
							{_orm_button_delete_}
						</button>
					</div>
					<?php endif; ?>

				</div>
			</div>

		</fieldset>
		<?= form_close(); ?>

	</div> <!-- DIV   class="col-md-8 col-md-offset-2 well" -->
</div> <!-- DIV   class="row" -->
