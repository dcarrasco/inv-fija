<div class="print-heading">
	<table>
		<tr>
			<td>Fecha: ___________________________</td>
			<td><h2><?php echo 'Inventario: ' . $nombre_inventario; ?></h2></td>
			<td class="ar"><h2>Hoja: <?php echo $hoja; ?></h2></td>
		</tr>
		<tr>
			<td colspan="2">Auditor: _______________________________</td>
			<td class="ar">Digitador: _______________________________</td>
		</tr>
		<tr>
			<td colspan="2">Operario: ____________________________</td>
			<td><strong>F</strong>: FRENTE / <strong>A</strong>: ATRAS</td>
		</tr>
	</table>
</div> <!-- fin print-heading -->

<br>

<div class="print-main">
	<table>
		<thead>
			<tr>
				<th class="ac">ubic</th>
				<th class="ac">material</th>
				<th class="ac" style="width: 30%">descripcion</th>
				<th class="ac">lote</th>
				<th class="ac">cen</th>
				<th class="ac">alm</th>
				<th class="ac">UM</th>
	
				<?php if (!$oculta_stock_sap): ?>
					<th class="ac">cant <br> sap</th>
				<?php endif; ?>
	
				<th class="ac">cant <br> fisico</th>
	
				<?php if (!$oculta_stock_sap): ?>
					<th class="ac">F</th>
					<th class="ac">A</th>
				<?php endif; ?>
	
				<th  class="ac" style="width: 22%">observacion</th>
			</tr>
		</thead>
		<tbody>
			<?php $sum_sap = 0; ?>
			<?php $lin = 0; ?>
			<?php foreach ($datos_hoja as $reg): ?>
				<?php $lin += 1; ?>
				<tr>
					<td class="ac"><?php echo $reg['ubicacion']; ?></td>
					<td class="ac"><?php echo $reg['catalogo']; ?></td>
					<td><?php echo $reg['descripcion']; ?></td>
					<td class="ac"><?php echo $reg['lote']; ?></td>
					<td class="ac"><?php echo $reg['centro']; ?></td>
					<td class="ac"><?php echo $reg['almacen']; ?></td>
					<td class="ac"><?php echo $reg['um']; ?></td>

					<?php if (!$oculta_stock_sap): ?>
						<td class="ac"><?php echo number_format($reg['stock_sap'],0,',','.'); ?></td>
					<?php endif; ?>

					<td></td>

					<?php if (!$oculta_stock_sap): ?>
						<td></td>
						<td></td>
					<?php endif; ?>

					<td></td>
				</tr>
				<?php $sum_sap += $reg['stock_sap']; ?>
			<?php endforeach; ?>
			
			<?php for($i=$lin; $i<21; $i++): ?>
				<tr>
					<td>&nbsp;</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<?php if (!$oculta_stock_sap): ?>
						<td></td>
						<td></td>
						<td></td>
					<?php endif; ?>
				</tr>
			<?php endfor; ?>
			<!-- totales -->
			<?php if (!$oculta_stock_sap): ?>
				<tr>
					<td colspan="7" class="no-border"></td>
					<td class="ac"><strong><?php echo number_format($sum_sap,0,',','.'); ?></strong></td>
					<td colspan="4" class="no-border"></td>
				</tr>
			<?php endif; ?>

			<tr>
				<?php if (!$oculta_stock_sap): ?>
					<td class="no-border"></td>
				<?php endif; ?>
				<td colspan="8" class="ar no-border">TOTAL HOJA: _____________</td>
				<td colspan="3" class="no-border"></td>
			</tr>


		</tbody>
	</table>
</div> <!-- fin print-main -->

