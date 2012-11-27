<!doctype html>
<html>
<head>
	<title>inventario fija</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/estilo.css" /	>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script type="text/javascript">
	</script>
</head>
<body>

<div class="content-module">

	<div class="content-module-heading cf">
		<div class="cf">
			<h2 class="fl">Cambio de clave</h2>
		</div>
		<div class="msg-alerta cf ac">
			<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">
		<div class="ac">
		<div class="login al">
			<?php echo form_open('login/cambio_password/'.$usr, 'id="frm_login"'); ?>
			<p></p>
			<div>
				<label for="usr">Nombre de usuario</label>
				<?php echo form_input('usr', set_value('usr'),'maxlength="45" size="30" class="round"'); ?>
				<div><?php echo form_error('usr'); ?></div>
			</div>
			<p></p>

			<?php if ($ocultar_password): ?>

				<?php if ($tiene_clave): ?>
					<div>
						<label for="pwd_old">Clave anterior</label>
						<?php echo form_password('pwd_old', '','maxlength="45" size="30" class="round"'); ?>
						<div><?php echo form_error('pwd_old'); ?></div>
					</div>
					<p></p>
				<?php endif; ?>

				<div>
					<label for="pwd_new1">Clave nueva</label>
					<?php echo form_password('pwd_new1', '','maxlength="45" size="30" class="round"'); ?>
					<div><?php echo form_error('pwd_new1'); ?></div>
				</div>
				<p></p>

				<div>
					<label for="pwd_new2">Reingrese clave nueve</label>
					<?php echo form_password('pwd_new2', '','maxlength="45" size="30" class="round"'); ?>
					<div><?php echo form_error('pwd_new2'); ?></div>
				</div>
				<p></p>

			<?php endif; ?>


			<div class="ac">
				<?php echo form_submit('btn_submit','Cambiar clave', 'class="button b-active round ic-login"'); ?>
				<?php //echo anchor('', 'Enviar nueva clave', 'class="button b-active round ic-correo"'); ?>
			</div>
			<p></p>
			<?php echo form_close(); ?>
		</div>
		</div>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->

</body>
</html>