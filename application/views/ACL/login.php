<div class="row">

	<div class="col-md-4 col-md-offset-4 col-xs-12 well">
		<div class="control-group text-center">
			<h2>{_login_form_title_}</h2>
		</div>

		<div class="col-md-12">
			{validation_errors}
		</div>

		<div class="col-md-12">
			{msg_alerta}
		</div>

		<?= form_open($url_login, 'id="frm_login"'); ?>

			<div class="form-group col-md-12 <?= form_has_error_class('usr'); ?>">
				<label for="usr">{_login_input_user_}</label>
				<?= form_input('usr', request('usr'),'maxlength="45" class="form-control input-lg" tabindex="1" autofocus'); ?>
			</div>

			<div class="form-group col-md-12 <?= form_has_error_class('pwd') ?>">
				<label for="pwd">{_login_input_password_}</label>
				<?= form_password('pwd', '','maxlength="45" size="40" tabindex="2" class="form-control input-lg" autocomplete="off"'); ?>
			</div>

			<div class="form-group col-md-12">
				<div class="checkbox col-md-6">
					<label>
						<?= form_checkbox('remember_me', 'remember', request('remember_me')); ?>
						{_login_check_remember_me_}
					</label>
				</div>
				<div class="pull-right">
					<?= anchor('#', '{_login_link_change_password_}', 'id="lnk_cambio_password"'); ?>
				</div>
			</div>

			<?php if ( ! empty($captcha_img)): ?>
				<div class="form-group col-md-12 <?= form_has_error_class('captcha') ?>">
					<label for="pwd">{_login_input_captcha_}</label>
					<?= form_input('captcha', '','maxlength="15" tabindex="3" class="form-control input-lg"'); ?>
				</div>
				<div class="form-group col-md-12 text-center <?= form_has_error_class('captcha') ?>">
					{captcha_img}
				</div>
			<?php endif ?>

			<div class="form-group col-md-12">
				<button type="submit" name="btn_submit" class="btn btn-success btn-lg btn-block">
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
