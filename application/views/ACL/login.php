<!doctype html>
<html>
<head>
	<title>inventario fija</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/estilo.css" /	>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
</head>
<body>

<div class="content-module">

	<div class="content-module-heading cf">
		<div class="cf">
			<h2 class="fl">Ingreso Digitaci&oacute;n Inventario Fija</h2>
		</div>
		<div class="msg-alerta cf ac">
			<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">
		<div class="ac">
		<div class="login al">
			<?php echo form_open('login', 'id="frm_login"'); ?>
			<p></p>
			<div>
				<label for="usr">Nombre de Usuario</label>
				<?php echo form_input('usr', set_value('usr'),'maxlength="45" size="40" tabindex="1"'); ?>
				<?php echo form_error('usr'); ?>
			</div>
			<p></p>
			<div>
				<label for="pwd">
					<div class="fl">Clave</div>
					<div class="fr"><?php echo anchor('login/cambio_password', 'cambiar clave'); ?></div>
				</label>

				<?php echo form_password('pwd', '','maxlength="45" size="40" tabindex="2"'); ?>
				<?php echo form_error('pwd'); ?>
			</div>
			<p></p>
			<div class="ar">
				<?php echo form_submit('btn_submit','Ingresar', 'class="button b-active round ic-login"'); ?>
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