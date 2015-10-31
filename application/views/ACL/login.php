<!doctype html>
<html>
<head>
	<title>inventario fija</title>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="icon" href="<?php echo base_url(); ?>favicon.png" type="image/png" />

	<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/fix_bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/jquery.jqplot.min.css" />

	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>

	<style type="text/css">
		body {margin-top: 40px;}
	</style>
</head>

<body>
<div class="container">
<div class="row">

	<div class="col-md-6 col-md-offset-3 col-xs-12 well">
		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<h2>
				<?php echo $this->lang->line('login_form_title'); ?>
			</h2>
		</div>

		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<hr>
		</div>

		<?php if (validation_errors()): ?>
		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<div class="alert alert-danger">
				<ul>
					<?php echo validation_errors(); ?>
				</ul>
			</div>
		</div>
		<?php endif; ?>

		<?php if ($msg_alerta !== ''): ?>
		<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
			<div class="alert alert-danger">
				<?php echo $msg_alerta; ?>
			</div>
		</div>
		<?php endif; ?>

		<?php echo form_open('login', 'id="frm_login" class="form-horizontal"'); ?>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12 <?php echo form_has_error('usr') ? 'has-error' : ''; ?>">
				<label class="control-label" for="usr">
					<?php echo $this->lang->line('login_input_user'); ?>
				</label>
				<div class="controls">
					<?php echo form_input('usr', set_value('usr'),'maxlength="45" class="form-control" tabindex="1" autofocus'); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12 <?php echo form_has_error('pwd') ? 'has-error' : ''; ?>">
				<label class="control-label" for="pwd">
					<?php echo $this->lang->line('login_input_password'); ?>
				</label>
				<div class="controls">
					<?php echo form_password('pwd', '','maxlength="45" size="40" tabindex="2" class="form-control" autocomplete="off"'); ?>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<div class="pull-right">
					<?php echo anchor('#', $this->lang->line('login_link_change_password'), 'id="lnk_cambio_password"'); ?>
				</div>
			</div>

			<?php if ($usar_captcha): ?>
				<div class="control-group col-md-8 col-md-offset-2 col-xs-12 <?php echo form_has_error('catpcha') ? 'has-error' : ''; ?>">
					<label class="control-label" for="pwd">
						<?php echo $this->lang->line('login_input_captcha'); ?>
					</label>
					<div class="controls">
						<?php echo form_input('captcha', '','maxlength="15" tabindex="3" class="form-control"'); ?>
					</div>
					<div class="controls">
						<?php echo $captcha_img; ?>
					</div>
				</div>
			<?php endif ?>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<div class="checkbox">
					<label>
						<?php echo form_checkbox('remember_me', 'remember', set_checkbox('remember_me', 'remember', FALSE)); ?>
						<?php echo $this->lang->line('login_check_remember_me'); ?>
					</label>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<hr>
			</div>

			<div class="control-group col-md-8 col-md-offset-2 col-xs-12">
				<div class="pull-right">
					<button type="submit" name="btn_submit" class="btn btn-success">
						<?php echo $this->lang->line('login_button_login'); ?>
						<span class="glyphicon glyphicon-play"></span>
					</button>
				</div>
			</div>
		<?php echo form_close(); ?>
	</div>

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

</body>
</html>