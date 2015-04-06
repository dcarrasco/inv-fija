<div>
	<table class="table table-condensed reporte">
		<thead>
			<tr>
				<th></th>
				<?php foreach($marcas as $marca): ?>
					<th><?php echo $marca['marca']; ?></th>
				<?php endforeach; ?>
				<th>Total</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($almacenes as $almacen): ?>
			<tr>
				<th>
					<?php echo $almacen['almacen']; ?>
				</th>
				<?php $tot = 0; ?>
				<?php foreach ($marcas as $marca): ?>
					<td>
						<?php echo array_key_exists($marca['marca'], $reporte[$almacen['almacen']]) ? fmt_cantidad($reporte[$almacen['almacen']][$marca['marca']]) : ''; ?>
					</td>
					<?php $tot += array_key_exists($marca['marca'], $reporte[$almacen['almacen']]) ? (int) $reporte[$almacen['almacen']][$marca['marca']] : 0; ?>
				<?php endforeach ?>
				<th><?php echo fmt_cantidad($tot); ?></th>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div> <!-- fin content-module-main -->

