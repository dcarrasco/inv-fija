<!doctype html>
<html>
<head>
	<title>inventario fija</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/estilo.css" /	>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script type="text/javascript">
		js_base_url = '<?php echo base_url(); ?>';
	</script>
</head>
<body>


<div class="app-heading cf">
	<h2 class="fl"><?php echo $titulo_modulo; ?></h2>
	<?php echo anchor('config','Configuracion', 'class="button b-active round ic-config fr"'); ?>
	<?php echo anchor('reportes','Reportes', 'class="button b-active round ic-reporte fr"'); ?>
	<?php echo anchor('inventario','Inventario', 'class="button b-active round ic-inventario fr"'); ?>
</div> <!-- fin app-heading -->
