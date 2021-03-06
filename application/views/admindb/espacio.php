<div>
<?php if(count($tablas)): ?>
	<table class="table table-striped table-hover table-condensed reporte table-fixed-header">
		<thead class="header">
			<tr>
				<th>Base de datos</th>
				<th>Tabla</th>
				<th class="text-right">cant filas</th>
				<th class="text-right">Espacio total [MB]</th>
				<th class="text-right">Espacio usado [MB]</th>
				<th class="text-right">Espacio no usado [MB]</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th></th>
				<th></th>
				<th class="text-right"><?=fmt_cantidad($arr_sum['RowCounts'])?></th>
				<th class="text-right"><?=fmt_cantidad($arr_sum['TotalSpaceKB']/1024)?></th>
				<th class="text-right"><?=fmt_cantidad($arr_sum['UsedSpaceKB']/1024)?></th>
				<th class="text-right"><?=fmt_cantidad($arr_sum['UnusedSpaceKB']/1024)?></th>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach($tablas as $tabla): ?>
			<tr class="<?= (strpos(strtolower($tabla['TableName']), 'tmp') !== FALSE OR strpos(strtolower($tabla['TableName']), 'respaldo') !== FALSE) ? 'danger' : '' ?>">
				<td class="text-left"><?= $tabla['DataBaseName']; ?></td>
				<td class="text-left"><?= $tabla['TableName']; ?></td>
				<td class="text-right"><?= fmt_cantidad($tabla['RowCounts']); ?></td>
				<td class="text-right"><?= fmt_cantidad($tabla['TotalSpaceKB']/1024); ?></td>
				<td class="text-right"><?= fmt_cantidad($tabla['UsedSpaceKB']/1024); ?></td>
				<td class="text-right"><?= fmt_cantidad($tabla['UnusedSpaceKB']/1024); ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

</div> <!-- fin content-module-main -->
<script type="text/javascript" src="<?= base_url() ?>js/reporte.js"></script>
