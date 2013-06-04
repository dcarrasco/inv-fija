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
	<div class="span6 offset3">
		<div>
			<h2>Inventario fija</h2>
		</div>


		<?php echo form_open('login/cambio_password/' . $usr, 'id="frm_login" class="form-horizontal"'); ?>
			<legend>
				Cambio de clave
			</legend>

			<?php if ($msg_alerta != ''): ?>
				<div class="alert">
					<button type="button" class="close" data-dismiss="alert">&times;</button>
					<?php echo $msg_alerta; ?>
				</div>
			<?php endif; ?>

			<div class="control-group">
				<label class="control-label" for="usr">Nombre de usuario</label>
				<div class="controls">
					<?php echo form_input('usr', set_value('usr'),'maxlength="45" class="input-large"'); ?>
					<div><?php echo form_error('usr'); ?></div>
				</div>
			</div>
			<p></p>

			<?php if ($ocultar_password): ?>

				<?php if ($tiene_clave): ?>
					<div class="control-group">
						<label class="control-label" for="usr">Clave anterior</label>
						<div class="controls">
							<?php echo form_password('pwd_old', '','maxlength="45" class="input-large"'); ?>
							<div><?php echo form_error('pwd_old'); ?></div>
						</div>
					</div>
					<p></p>
				<?php endif; ?>

				<div class="control-group">
					<label class="control-label" for="usr">Clave nueva</label>
					<div class="controls">
						<?php echo form_password('pwd_new1', '','maxlength="45" class="input-large"'); ?>
						<div><?php echo form_error('pwd_new1'); ?></div>
					</div>
				</div>
				<p></p>

				<div class="control-group">
					<label class="control-label" for="usr">Reingrese clave nueva</label>
					<div class="controls">
						<?php echo form_password('pwd_new2', '','maxlength="45" class="input-large"'); ?>
						<div><?php echo form_error('pwd_new2'); ?></div>
					</div>
				</div>
				<p></p>

			<?php endif; ?>

			<div class="control-group">
				<div class="controls">
					<button type="submit" name="btn_submit" class="btn btn-primary">
						<i class="icon-lock icon-white"></i>
						Cambiar clave
					</button>
				</div>
			</div>
			<p></p>
		<?php echo form_close(); ?>

	</div>
</div>
</div>

</body>
</html>