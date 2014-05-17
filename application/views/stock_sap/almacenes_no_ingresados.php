
<div class="content-module-main">
	<table class="table table-bordered table-striped table-hover table-condensed">
		<tr>
			<th>Centro</th>
			<th>Almacen</th>
		</tr>

		<?php foreach($almacenes as $alm): ?>
			<tr>
				<td><?php echo $alm['centro']; ?></td>
				<td><?php echo $alm['cod_bodega']; ?></td>
			</tr>
		<?php endforeach; ?>

	</table>
</div> <!-- fin content-module-main -->
