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

		<?php echo form_open('login', 'id="frm_login" class="form-horizontal"'); ?>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12 <?php echo form_has_error('usr'); ?>">
				<label class="control-label" for="usr">
					{_login_input_user_}
				</label>
				<div class="controls">
					<?php echo form_input('usr', set_value('usr'),'maxlength="45" class="form-control" tabindex="1" autofocus'); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12 <?php echo form_has_error('pwd'); ?>">
				<label class="control-label" for="pwd">{_login_input_password_}</label>
				<div class="controls">
					<?php echo form_password('pwd', '','maxlength="45" size="40" tabindex="2" class="form-control" autocomplete="off"'); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<div class="pull-right">
					<?php echo anchor('#', '{_login_link_change_password_}', 'id="lnk_cambio_password"'); ?>
				</div>
			</div>

			<?php if ($usar_captcha): ?>
				<div class="control-group col-md-8 col-md-offset-2 col-xs-12 <?php echo form_has_error('catpcha'); ?>">
					<label class="control-label" for="pwd">{_login_input_captcha_}</label>
					<div class="controls">
						<?php echo form_input('captcha', '','maxlength="15" tabindex="3" class="form-control"'); ?>
					</div>
					<div class="controls">
						{captcha_img}
					</div>
				</div>
			<?php endif ?>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<div class="checkbox">
					<label>
						<?php echo form_checkbox('remember_me', 'remember', set_value('remember_me')); ?>
						{_login_check_remember_me_}
					</label>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<hr>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<div class="pull-right">
					<button type="submit" name="btn_submit" class="btn btn-success">
						{_login_button_login_}
						<span class="glyphicon glyphicon-play"></span>
					</button>
				</div>
			</div>
		<?php echo form_close(); ?>
	</div>

</div>

<?php echo form_open('login/cambio_password', 'id="frm_cambio_password"') ?>
<?php echo form_hidden('usr',''); ?>
<?php echo form_close(); ?>

<script type="text/javascript">
	$(document).ready(function () {

		$('#lnk_cambio_password').click(function(e) {
			e.preventDefault();
			$('#frm_cambio_password input[name="usr"]').val($('#frm_login input[name="usr"]').val());
			$('#frm_cambio_password').submit();
		});

	});
</script>
