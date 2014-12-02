<!doctype html>
<html>
<head>
	<title>inventario fija</title>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<link rel="icon" href="<?php echo base_url(); ?>favicon.png" type="image/png" />

	<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" /	>

	<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
	<style type="text/css">
		body {margin-top: 40px;}
	</style>
</head>
<body>

<div class="container">
<div class="row">

	<div class="col-md-6 col-md-offset-3 well">
		<div>
			<h2>Cambio de clave</h2>
		</div>


		<?php echo form_open('login/cambio_password/' . $usr, 'id="frm_login" class="form-horizontal"'); ?>

			<?php if ($msg_alerta != ''): ?>
				<div class="alert alert-danger">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<?php echo $msg_alerta; ?>
				</div>
			<?php endif; ?>

			<div class="control-group col-md-8 col-md-offset-2">
				<label class="control-label" for="usr">Nombre de usuario</label>
				<div class="controls">
					<?php echo form_input('usr', set_value('usr'),'maxlength="45" class="form-control"'); ?>
					<div><?php echo form_error('usr'); ?></div>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2">
				<label class="control-label" for="pwd_old">Clave anterior</label>
				<div class="controls">
					<?php echo form_password('pwd_old', '','maxlength="45" class="form-control"'); ?>
					<div><?php echo form_error('pwd_old'); ?></div>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2">
				<label class="control-label" for="usr">Clave nueva</label>
				<div class="controls">
					<?php echo form_password('pwd_new1', '','maxlength="45" class="form-control"'); ?>
					<div><?php echo form_error('pwd_new1'); ?></div>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2">
				<label class="control-label" for="usr">Reingrese clave nueva</label>
				<div class="controls">
					<?php echo form_password('pwd_new2', '','maxlength="45" class="form-control"'); ?>
					<div><?php echo form_error('pwd_new2'); ?></div>
				</div>
			</div>

			<div class="control-group col-md-8 col-md-offset-2">
				<hr/>
			</div>

			<div class="control-group pull-right">
				<div class="controls">
					<button type="submit" name="btn_submit" class="btn btn-primary">
						<span class="glyphicon glyphicon-lock"></span>
						Cambiar clave
					</button>
				</div>
			</div>
		<?php echo form_close(); ?>

	</div>
</div>
</div>

</body>
</html>