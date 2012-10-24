<!doctype html>
<html>
<head>
	<title>inventario fija</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/estilo.css" /	>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
	<script type="text/javascript">
		js_base_url = '<?php echo ($this->config->item('index_page') == '') ? base_url() : base_url() . $this->config->item('index_page') . '/'; ?>';
	</script>
</head>
<body>


<div class="app-heading cf">
	<h2 class="fl"><?php echo $titulo_modulo; ?></h2>
	<?php echo $menu_app; ?>
</div> <!-- fin app-heading -->
