<!doctype html>
<html lang="es">
<head>
	<meta charset="utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">

	<title>Inventario Fija</title>
	<link rel="icon" href="<?php echo base_url(); ?>img/favicon.png" type="image/png" />
	<link rel="apple-touch-icon-precomposed" sizes="152x152" href="<?php echo base_url(); ?>img/favicon-152.png">

	<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/fix_bootstrap.css" />
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/jquery.jqplot.min.css" />

	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.jqplot.min.js"></script>

	<script type="text/javascript">
		js_base_url = '<?php echo ($this->config->item('index_page') == '') ? base_url() : base_url() . $this->config->item('index_page') . '/'; ?>';
	</script>

	<?php echo isset($extra_styles) ? $extra_styles : ''; ?>
</head>


<body>

<?php if ( ! $vista_login): ?>
	<!-- ********************************* NAVBAR ********************************* -->
	<?php $this->load->view('common/app_navbar') ?>
<?php endif; ?>

<div class="container">

<?php if ( ! $vista_login AND isset($menu_modulo)): ?>
	<!-- ********************************* MENU MODULO ********************************* -->
	<?php $this->load->view('common/app_menu_modulo') ?>
	<div class="tab-content">
	<div class="tab-pane active">
<?php endif; ?>

<!-- ********************************* MENSAJE ALERTA ********************************* -->
<?php echo (isset($msg_alerta) AND $msg_alerta !== '') ? $msg_alerta : ''; ?>

<!-- ********************************* VISTAS APP ********************************* -->
<?php foreach ($arr_vistas as $vista): ?>
	<?php $this->load->view($vista) ?>
<?php endforeach; ?>

<?php if ( ! $vista_login AND isset($menu_modulo)): ?>
	</div> <!-- DIV class="tab-content" -->
	</div> <!-- DIV class="tab-pane"    -->
<?php endif; ?>

</div> <!-- DIV principal de la aplicacion class="container"-->

</body>
</html>

