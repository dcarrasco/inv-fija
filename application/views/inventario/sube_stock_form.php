<div class="row">
	<div class="col-md-10 col-md-offset-1 well">
		<?= form_open_multipart("{$this->router->class}/upload", 'class="form-horizontal" role="form"'); ?>
		<fieldset>

			<legend>{_inventario_upload_label_fieldset_}</legend>

			{validation_errors}

			<div class="form-group">
				<label class="control-label col-sm-4">{_inventario_upload_label_inventario_}</label>
				<div class="col-sm-8">
					<p class="form-control-static">{inventario_id} - {inventario_nombre}</p>
					<?= print_message('{_inventario_upload_warning_line2_} "{inventario_nombre}".'	, 'warning') ?>
				</div>
			</div>

			<div class="form-group <?= form_has_error_class('upload_file') ?>">
				<label class="control-label col-sm-4">{_inventario_upload_label_file_}</label>
				<div class="col-sm-8">
					<?= form_upload('upload_file', '', 'class="form-control" accept=".txt,.csv"'); ?>
				</div>
			</div>

			<div class="form-group <?= form_has_error_class('upload_password') ?>">
				<label class="control-label col-sm-4">{_inventario_upload_label_password_}</label>
				<div class="col-sm-8">
					<?= form_password('upload_password', '', 'class="form-control"'); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">
				</label>
				<div class="col-sm-8">
					<button type="submit" name="submit" class="btn btn-primary pull-right" id="btn_guardar">
						<span class="fa fa-cloud-upload"></span>
						{_inventario_upload_button_upload_}
					</button>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">{_inventario_upload_label_format_}</label>
				<div class="col-sm-8">
					<pre>
{_inventario_upload_format_file_}
					</pre>
				</div>
			</div>

		</fieldset>
		<?= form_close(); ?>

	</div>
</div>
