<div class="row">

	<div class="col-md-6 col-md-offset-3 col-xs-12 well">
		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<h2>{_login_form_title_}</h2>
		</div>

		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<hr>
		</div>

		<div class="col-md-12">
			{validation_errors}
		</div>

		<div class="col-md-12">
			{msg_alerta}
		</div>

		<?= form_open('login', 'id="frm_login" class="form-horizontal"'); ?>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12 <?= form_has_error_class('usr'); ?>">
				<label class="control-label" for="usr">
					{_login_input_user_}
				</label>
				<div class="controls">
					<?= form_input('usr', request('usr'),'maxlength="45" class="form-control" tabindex="1" autofocus'); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12 <?= form_has_error_class('pwd'); ?>">
				<label class="control-label" for="pwd">{_login_input_password_}</label>
				<div class="controls">
					<?= form_password('pwd', '','maxlength="45" size="40" tabindex="2" class="form-control" autocomplete="off"'); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<div class="pull-right">
					<?= anchor('#', '{_login_link_change_password_}', 'id="lnk_cambio_password"'); ?>
				</div>
			</div>

			<?php if ($usar_captcha): ?>
				<div class="control-group col-md-8 col-md-offset-2 col-xs-12 <?= form_has_error_class('catpcha'); ?>">
					<label class="control-label" for="pwd">{_login_input_captcha_}</label>
					<div class="controls">
						<?= form_input('captcha', '','maxlength="15" tabindex="3" class="form-control"'); ?>
					</div>
					<div class="controls">
						{captcha_img}
					</div>
				</div>
			<?php endif ?>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<div class="checkbox">
					<label>
						<?= form_checkbox('remember_me', 'remember', request('remember_me')); ?>
						{_login_check_remember_me_}
					</label>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<hr>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<button type="submit" name="btn_submit" class="btn btn-success pull-right col-md-6">
					{_login_button_login_} &nbsp; <span class="fa fa-sign-in"></span>
				</button>
			</div>
		<?= form_close(); ?>
	</div>

</div>

<?= form_open('login/cambio_password', 'id="frm_cambio_password"') ?>
<?= form_hidden('usr',''); ?>
<?= form_close(); ?>

<script type="text/javascript">
	$(document).ready(function () {

		$('#lnk_cambio_password').click(function(e) {
			e.preventDefault();
			$('#frm_cambio_password input[name="usr"]').val($('#frm_login input[name="usr"]').val());
			$('#frm_cambio_password').submit();
		});

	});
</script>
