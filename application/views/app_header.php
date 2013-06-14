<!doctype html>
<html>
<head>
	<title>inventario fija</title>
	<meta charset="utf-8" />

	<link rel="stylesheet" href="<?php echo base_url(); ?>css/bootstrap.min.css" /	>
	<link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/jquery.jqplot.min.css" />

	<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/bootstrap.min.js"></script>
	<script language="javascript" type="text/javascript" src="<?php echo base_url(); ?>js/jquery.jqplot.min.js"></script>

	<script type="text/javascript">
		js_base_url = '<?php echo ($this->config->item('index_page') == '') ? base_url() : base_url() . $this->config->item('index_page') . '/'; ?>';
	</script>
</head>

<body>

<!-- DIV principal de la aplicacion -->
<div class="container-fluid">

<div class="navbar">
	<div class="navbar-inner">
		<a class="brand" href="#">
			<strong>Inventario fija</strong> :: <?php echo $this->app_common->titulo_modulo(); ?>
		</a>
		<?php echo $this->app_common->menu_app(); ?>
	</div>
</div>
