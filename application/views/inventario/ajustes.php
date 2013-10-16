<div>

	<?php echo anchor('analisis/ajustes/' . (($ocultar_regularizadas == 0) ? '1' : '0') . '/' . $pag . '/' . time() ,(($ocultar_regularizadas == 0) ? 'Ocultar' : 'Mostrar') . ' lineas regularizadas') ?>
</div>

<div>
	<?php echo form_open('analisis/ajustes/' . $ocultar_regularizadas . '/' . $pag . '/' . time(), 'id="frm_inventario"'); ?>
	<?php echo form_hidden('formulario','ajustes'); ?>

	<table class="table table-bordered table-hover table-condensed">
		<thead>
			<tr>
				<th>material</th>
				<th>descripcion</th>
				<th>lote</th>
				<th>centro</th>
				<th>almacen</th>
				<th>ubicacion</th>
				<!-- <th>hu</th> -->
				<th>hoja</th>
				<th>UM</th>
				<th class="text-center">cant sap</th>
				<th class="text-center">cant fisica</th>
				<th class="text-center">cant ajuste</th>
				<th class="text-center">dif</th>
				<th class="text-center">tipo dif</th>
				<th>observacion ajuste</th>
			</tr>
		</thead>

		<tbody>
			<?php $sum_sap = 0; $sum_fisico = 0; $sum_ajuste = 0; ?>
			<?php $tab_index = 10; ?>
			<?php $cat_ant = ''; ?>
			<?php foreach ($detalle_ajustes->get_model_all() as $detalle): ?>
				<?php if($cat_ant != $detalle->catalogo and $cat_ant != ''): ?>
					<tr>
						<td colspan="15">&nbsp;</td>
					</tr>
				<?php endif; ?>

				<tr>
					<td><?php echo $detalle->catalogo; ?></td>
					<td><?php echo $detalle->descripcion; ?></td>
					<td><?php echo $detalle->lote; ?></td>
					<td><?php echo $detalle->centro; ?></td>
					<td><?php echo $detalle->almacen; ?></td>
					<td><?php echo $detalle->ubicacion; ?></td>
					<!-- <td><?php //echo $detalle->hu; ?></td> -->
					<td><?php echo $detalle->hoja; ?></td>
					<td><?php echo $detalle->um; ?></td>
					<td class="text-center"><?php echo number_format($detalle->stock_sap,0,',','.'); ?></td>
					<td class="text-center"><?php echo number_format($detalle->stock_fisico,0,',','.'); ?></td>
					<td>
						<?php echo form_input('stock_ajuste_' . $detalle->id, set_value('stock_ajuste_' . $detalle->id, $detalle->stock_ajuste), 'class="form-control" size="5" tabindex="' . $tab_index . '"'); ?>
						<?php echo form_error('stock_ajuste_' . $detalle->id); ?>
					</td>
					<td class="text-center">
						<?php echo number_format($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste,0,',','.'); ?>
					</td>
					<td class="text-center">
						<?php if (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) > 0): ?>
							<button class="btn btn-default btn-sm btn-warning" style="white-space: nowrap;">
								<i class="glyphicon glyphicon-question-sign"></i>
								Sobrante
							</button>
						<?php elseif (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) < 0): ?>
							<button class="btn btn-default btn-sm btn-danger" style="white-space: nowrap;">
								<i class="glyphicon glyphicon-remove"></i>
								Faltante
							</button>
						<?php else: ?>
							<button class="btn btn-default btn-sm btn-success" style="white-space: nowrap;">
								<i class="glyphicon glyphicon-ok"></i>
								OK
							</button>
						<?php endif; ?>
					</td>
					<!--
					<td style="<?php echo (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) > 0) ? 'background-color: yellow; color: black' : ((($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) < 0) ? 'background-color: red; color: white' : 'background-color: green; color: white'); ?>">
						<strong><?php echo (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) > 0) ? 'Sobrante' : ((($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) < 0) ? 'Faltante' : 'OK'); ?></strong>
					</td>
					-->
					<td>
						<?php echo form_input('observacion_' . $detalle->id, set_value('observacion_' . $detalle->id, $detalle->glosa_ajuste), 'class="form-control" max_length="200" tabindex="' . ($tab_index + 10000) . '"'); ?>
					</td>
				</tr>
				<?php $sum_sap += $detalle->stock_sap; $sum_fisico += $detalle->stock_fisico; $sum_ajuste += $detalle->stock_ajuste?>
				<?php $tab_index += 1; ?>
				<?php $cat_ant = $detalle->catalogo; ?>
			<?php endforeach; ?>

			<!-- totales -->
			<tr>
				<td></td>
				<td></td>
				<!-- <td></td> -->
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class="text-center"><strong><?php echo number_format($sum_sap,0,',','.'); ?></strong></td>
				<td class="text-center"><strong><?php echo number_format($sum_fisico,0,',','.'); ?></strong></td>
				<td class="text-center"><strong><?php echo number_format($sum_ajuste,0,',','.'); ?></strong></td>
				<td class="text-center"><strong><?php echo number_format($sum_fisico - $sum_sap + $sum_ajuste,0,',','.'); ?></strong></td>
				<td></td>
				<td>
					<a href="#" class="btn btn-primary" id="btn_guardar">
						<i class="glyphicon glyphicon-ok-sign"></i>
						Guardar ajustes
					</a>
				</td>
			</tr>
		</tbody>
	</table>
	<?php echo form_close(); ?>
</div><!-- fin content-module-main-principal -->

<div class="pagination pagination-centered">
	<?php echo ($links_paginas != '') ? $links_paginas : ''; ?>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$('#btn_guardar').click(function(event) {
			event.preventDefault();
			$('form#frm_inventario').submit();
		});

	});
</script>