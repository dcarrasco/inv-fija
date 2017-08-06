<div class="row">

	<div class="col-md-4 col-md-offset-4 well">
		<div class="form-group text-center">
			<h2>{_login_form_change_password_}</h2>
		</div>

		<div class="col-md-12">
			{validation_errors}
		</div>

		<div class="col-md-12">
			{msg_alerta}
		</div>

		<?= form_open($url_form, 'id="frm_login"'); ?>


			<div class="form-group col-md-12 <?= form_has_error_class('usr'); ?>">
				<label for="usr">{_login_input_user_}</label>
				<?= form_input('usr', request('usr', $usr),'maxlength="45" class="form-control"'); ?>
			</div>

			<div class="form-group col-md-12 <?= form_has_error_class('pwd_old'); ?>">
				<label for="pwd_old">{_login_input_old_password_}</label>
				<?= form_password('pwd_old', '','maxlength="45" autocomplete="off" class="form-control" ' . $tiene_clave_class); ?>
			</div>

			<div class="form-group col-md-12 <?= form_has_error_class('pwd_new1'); ?>">
				<label for="pwd_new1">{_login_input_new1_password_}</label>
				<?= form_password('pwd_new1', '','maxlength="45" class="form-control"'); ?>
			</div>

			<div class="form-group col-md-12 <?= form_has_error_class('pwd_new2'); ?>">
				<label for="pwd_new2">{_login_input_new2_password_}</label>
				<?= form_password('pwd_new2', '','maxlength="45" class="form-control"'); ?>
				<p class="help-block"><em><small>M&iacute;nimo 8 caracteres. Debe incluir may&uacute;sculas, min&uacute;sculas y n&uacute;meros.</small></em></p>
			</div>

			<div class="form-group col-md-12">
				<button type="submit" name="btn_submit" class="btn btn-primary btn-block btn-lg">
					<i class="fa fa-lock"></i> {_login_button_change_password_}
				</button>
			</div>
		<?= form_close(); ?>

	</div>

</div>
