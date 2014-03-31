
<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">
		<table class="table table-bordered table-striped table-hover table-condensed">
			<tr>
				<th>n</th>
				<th>fecha stock</th>
				<th>centro</th>
				<th>almacen</th>
				<th>desc almacen</th>
				<th>material</th>
				<th>desc material</th>
				<th>lote</th>
				<th>serie</th>
				<th>estado stock</th>
				<th>pmp</th>
				<th>fecha modificacion</th>
				<th>usuario modificacion</th>
				<th>nombre usuario modificacion</th>
			</tr>

			<?php $n_lin = 0; ?>
			<?php foreach($detalle_series as $reg): ?>
				<tr>
					<td><?php echo (++$n_lin); ?></td>
					<td><?php echo $reg['fecha_stock']; ?></td>
					<td><?php echo $reg['centro']; ?></td>
					<td><?php echo $reg['almacen']; ?></td>
					<td><?php echo $reg['des_almacen']; ?></td>
					<td><?php echo $reg['material']; ?></td>
					<td><?php echo $reg['des_material']; ?></td>
					<td><?php echo $reg['lote']; ?></td>
					<td><span class="serie"><?php echo $reg['serie']; ?></span></td>
					<td><?php echo $reg['estado_stock']; ?></td>
					<td><?php echo $reg['pmp']; ?></td>
					<td><?php echo $reg['fecha_modificacion']; ?></td>
					<td><?php echo $reg['modificado_por']; ?></td>
					<td><?php echo $reg['nom_usuario']; ?></td>
				</tr>
			<?php endforeach; ?>

		</table>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">

	</div> <!-- fin content-module-footer -->

<?php echo form_open('stock_analisis_series/historia'); ?>
<?php echo form_hidden('series'); ?>
<?php echo form_hidden('show_mov', 'show'); ?>
<?php echo form_close(); ?>

<script>
$(document).ready(function () {
    $('span.serie').css('cursor', 'pointer');

	$('span.serie').click(function (event) {
		var serie = $(this).text();
		$('input[name="series"]').val(serie);

		$('form').submit();
	});



});
</script>

</div> <!-- fin content-module -->
