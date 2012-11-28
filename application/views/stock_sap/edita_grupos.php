<div class="content-module">

	<div class="content-module-heading cf">
		<?php echo $menu_configuracion; ?>
	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">

		<?php echo form_open(); ?>
		<table>
			<tr>
				<td>Tipo Almacen</td>
				<td>
					<?php echo form_input('tipo', $nombre_grupo, 'size="80" maxlength="80"'); ?>
					<?php echo form_error('tipo'); ?>
				</td>
			</tr>
			<tr>
				<td>Almacenes</td>
				<td>
					<?php echo form_multiselect('almacenes[]', $combo_almacenes, $sel_tipos_almacenes, 'size="20"'); ?>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><?php echo form_submit('btn_accion', 'Grabar'); ?>
					<?php echo form_submit('btn_accion', 'Borrar'); ?></td>
			</tr>
		</table>
		<?php echo form_close(); ?>
		<?php echo anchor('stock_sap', 'Volver'); ?>

	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">

	</div> <!-- fin content-module-footer -->


</div> <!-- fin content-module -->



<!doctype html>
<html>
<head>
	<title>stock - almacenes</title>
	<meta charset="utf-8" />
	<link rel="stylesheet" href="<?php echo base_url(); ?>css/estilo.css" /	>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.js"></script>
</head>
<body>



</body>
</html>
