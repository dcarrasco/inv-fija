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
			<?php echo form_open('', 'id="frm_login"'); ?>
			<p class="ac">
				Usuario
				<?php echo form_input('usuario', set_value('usuario'),'maxlength="45" size="30"'); ?>
			</p>
			<p class="ac">
				Clave
				<?php echo form_password('clave', set_value('clave'),'maxlength="45" size="30"'); ?>
			</p>
			<p class="ac">
				<?php echo anchor('#','Ingresar', 'class="button b-active round ic-login"'); ?>
			</p>
			<?php echo form_close(); ?>
		</div>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->

</body>
</html>