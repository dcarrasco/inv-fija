<div>
<?php if(count($tablas)): ?>
	<table class="table table-striped table-hover table-condensed reporte">
		<thead>
			<tr>
				<th>Base de datos</th>
				<th>Tabla</th>
				<th class="text-right">cant filas</th>
				<th class="text-right">Espacio total [MB]</th>
				<th class="text-right">Espacio usado [MB]</th>
				<th class="text-right">Espacio no usado [MB]</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($tablas as $base_datos => $arr_tablas): ?>
			<?php foreach($arr_tablas as $tabla): ?>
				<tr>
					<td class="text-left"><?= $base_datos; ?></td>
					<td class="text-left"><?= $tabla['TableName']; ?></td>
					<td class="text-right"><?= fmt_cantidad($tabla['RowCounts']); ?></td>
					<td class="text-right"><?= fmt_cantidad($tabla['TotalSpaceKB']/1024); ?></td>
					<td class="text-right"><?= fmt_cantidad($tabla['UsedSpaceKB']/1024); ?></td>
					<td class="text-right"><?= fmt_cantidad($tabla['UnusedSpaceKB']/1024); ?></td>
				</tr>
			<?php endforeach; ?>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

</div> <!-- fin content-module-main -->
