<!doctype html>
<html>
<head>
	<title>inventario fija</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" /	>

	<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
</head>
<body>

<div class="container-fluid">
<div class="row-fluid">

	<div class="span6 offset3 well">
		<div>
			<h2>Inventario Fija</h2>
		</div>
		<div>
			<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
		</div>

		<?php echo form_open('login', 'id="frm_login" class="form-horizontal"'); ?>
			<legend>
				Login
			</legend>

			<div class="control-group">

				<label class="control-label" for="usr">Usuario</label>
				<div class="controls">
					<?php echo form_input('usr', set_value('usr'),'maxlength="45" class="input-large" tabindex="1" autofocus'); ?>
					<div><?php echo form_error('usr'); ?></div>
				</div>
			</div>
			<p></p>
			<p></p>
			<div class="control-group">
				<label class="control-label" for="pwd">
					Clave
				</label>
				<div class="controls">
					<?php echo form_password('pwd', '','maxlength="45" size="40" tabindex="2" class="round"'); ?>
					<?php echo anchor('#', 'cambiar clave', 'id="lnk_cambio_password"'); ?>
					<div><?php echo form_error('pwd'); ?></div>
			</div>
			<p></p>
			<div class="control-group">
				<div class="controls">
					<button type="submit" name="btn_submit" class="btn btn-primary">
						Ingresar
						<i class="icon-play icon-white"></i>
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

</body>
</html>