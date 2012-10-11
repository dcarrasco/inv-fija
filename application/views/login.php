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
			<h2 class="fl">Ingreso Digitaci&oacute;n Inventario Fija</h2>
		</div>
		<div class="msg-alerta cf ac">
			<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
		</div>		
	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">
		<div class="login">
			<?php echo form_open('login', 'id="frm_login"'); ?>
			<p></p>
			<div class="ac">
				Nombre de Usuario
				<?php echo form_input('usr', set_value('usr'),'maxlength="45" size="30"'); ?>
				<?php echo form_error('usr'); ?>
			</div>
			<p></p>
			<div class="ac">
				Clave
				<?php echo form_password('pwd', '','maxlength="45" size="30"'); ?> 
				(<?php echo anchor('login/cambio_password', 'cambiar'); ?>)
				<?php echo form_error('pwd'); ?>
			</div>
			<p></p>
			<div class="ac">
				<?php echo form_submit('btn_submit','Ingresar', 'class="button b-active round ic-login"'); ?>
				<?php echo form_close(); ?>
			</div>
			<p></p>
	</div>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->

</body>
</html>