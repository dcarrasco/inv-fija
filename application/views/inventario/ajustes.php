<div>
	<div class="pull-left">
		<strong>Inventario</strong>
		<?php echo $inventario; ?>
	</div>
	<div class="pull-right">
		<?php echo anchor(
			$this->uri->segment(1) . '/ajustes/' . (($ocultar_regularizadas == 0) ? '1' : '0') . '/' . $pag . '/' . time(),
			($ocultar_regularizadas == 0) ? $this->lang->line('inventario_adjust_link_hide') : $this->lang->line('inventario_adjust_link_show')
		); ?>
	</div>
</div>
<hr>


<div>
	<?php echo form_open($this->uri->segment(1) . '/ajustes/' . $ocultar_regularizadas . '/' . $pag . '/' . time(), 'id="frm_inventario"'); ?>
	<?php echo form_hidden('formulario','ajustes'); ?>

	<table class="table table-hover table-condensed reporte">

		<!-- encabezado -->
		<thead>
			<tr>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_material'); ?></th>
				<th><?php echo $this->lang->line('inventario_digit_th_descripcion'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_lote'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_centro'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_almacen'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_ubicacion'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_hoja'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_UM'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_cant_sap'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_cant_fisica'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_cant_ajuste'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_dif'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_tipo_dif'); ?></th>
				<th class="text-center"><?php echo $this->lang->line('inventario_digit_th_observacion_ajuste'); ?></th>
			</tr>
		</thead>

		<!-- cuerpo -->
		<tbody>
			<?php $sum_sap = 0; $sum_fisico = 0; $sum_ajuste = 0; ?>
			<?php $subtot_sap = 0; $subtot_fisico = 0; $subtot_ajuste = 0; ?>
			<?php $tab_index = 10; ?>
			<?php $cat_ant = ''; ?>
			<?php foreach ($detalle_ajustes->get_model_all() as $detalle): ?>
				<?php if ($cat_ant != $detalle->catalogo AND $cat_ant != ''): ?>
					<tr class="active">
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td></td>
						<td class="text-center"><strong><?php echo fmt_cantidad($subtot_sap, 0, true); ?></strong></td>
						<td class="text-center"><strong><?php echo fmt_cantidad($subtot_fisico, 0, true); ?></strong></td>
						<td class="text-center"><strong><?php echo fmt_cantidad($subtot_ajuste, 0, true); ?></strong></td>
						<td class="text-center"><strong><?php echo fmt_cantidad($subtot_fisico - $subtot_sap + $subtot_ajuste, 0, true); ?></strong></td>
						<td></td>
						<td></td>
					</tr>
					<tr>
						<td colspan="15">&nbsp;</td>
					</tr>
					<?php $subtot_sap = 0; $subtot_fisico = 0; $subtot_ajuste = 0; ?>
				<?php endif; ?>

				<tr>
					<td class="text-center"><?php echo ($cat_ant != $detalle->catalogo) ? $detalle->catalogo : ''; ?></td>
					<td><?php echo ($cat_ant != $detalle->catalogo) ? $detalle->descripcion : ''; ?></td>
					<td class="text-center"><?php echo $detalle->lote; ?></td>
					<td class="text-center"><?php echo $detalle->centro; ?></td>
					<td class="text-center"><?php echo $detalle->almacen; ?></td>
					<td class="text-center"><?php echo $detalle->ubicacion; ?></td>
					<!-- <td class="text-center"><?php //echo $detalle->hu; ?></td> -->
					<td class="text-center"><?php echo $detalle->hoja; ?></td>
					<td class="text-center"><?php echo $detalle->um; ?></td>
					<td class="text-center"><?php echo fmt_cantidad($detalle->stock_sap); ?></td>
					<td class="text-center"><?php echo fmt_cantidad($detalle->stock_fisico); ?></td>
					<td>
						<?php echo form_input('stock_ajuste_' . $detalle->id, set_value('stock_ajuste_' . $detalle->id, $detalle->stock_ajuste), 'class="form-control input-sm text-right" size="5" tabindex="' . $tab_index . '"'); ?>
						<?php echo form_error('stock_ajuste_' . $detalle->id); ?>
					</td>
					<td class="text-center">
						<?php echo fmt_cantidad($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste); ?>
					</td>
					<td class="text-center">
						<?php if (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) > 0): ?>
							<button class="btn btn-default btn-sm btn-warning" style="white-space: nowrap;">
								<span class="glyphicon glyphicon-question-sign"></span>
								<?php echo $this->lang->line('inventario_report_label_sobrante'); ?>
							</button>
						<?php elseif (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) < 0): ?>
							<button class="btn btn-default btn-sm btn-danger" style="white-space: nowrap;">
								<span class="glyphicon glyphicon-remove"></span>
								<?php echo $this->lang->line('inventario_report_label_faltante'); ?>
							</button>
						<?php else: ?>
							<button class="btn btn-default btn-sm btn-success" style="white-space: nowrap;">
								<span class="glyphicon glyphicon-ok"></span>
								<?php echo $this->lang->line('inventario_report_label_OK'); ?>
							</button>
						<?php endif; ?>
					</td>
					<td class="text-center">
						<?php echo form_input('observacion_' . $detalle->id, set_value('observacion_' . $detalle->id, $detalle->glosa_ajuste), 'class="form-control input-sm" max_length="200" tabindex="' . ($tab_index + 10000) . '"'); ?>
					</td>
				</tr>
				<?php $sum_sap += $detalle->stock_sap; $sum_fisico += $detalle->stock_fisico; $sum_ajuste += $detalle->stock_ajuste?>
				<?php $subtot_sap += $detalle->stock_sap; $subtot_fisico += $detalle->stock_fisico; $subtot_ajuste += $detalle->stock_ajuste?>
				<?php $tab_index += 1; ?>
				<?php $cat_ant = $detalle->catalogo; ?>
			<?php endforeach; ?>

			<!-- subtotales (ultima linea) -->
			<tr class="active">
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class="text-center"><strong><?php echo fmt_cantidad($subtot_sap, 0, true); ?></strong></td>
				<td class="text-center"><strong><?php echo fmt_cantidad($subtot_fisico, 0, true); ?></strong></td>
				<td class="text-center"><strong><?php echo fmt_cantidad($subtot_ajuste, 0, true); ?></strong></td>
				<td class="text-center"><strong><?php echo fmt_cantidad($subtot_fisico - $subtot_sap + $subtot_ajuste, 0, true); ?></strong></td>
				<td></td>
				<td></td>
			</tr>
			<tr>
				<td colspan="15">&nbsp;</td>
			</tr>
		</tbody>

		<!-- totales -->
		<tfoot>
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
				<td class="text-center"><strong><?php echo fmt_cantidad($sum_sap); ?></strong></td>
				<td class="text-center"><strong><?php echo fmt_cantidad($sum_fisico); ?></strong></td>
				<td class="text-center"><strong><?php echo fmt_cantidad($sum_ajuste); ?></strong></td>
				<td class="text-center"><strong><?php echo fmt_cantidad($sum_fisico - $sum_sap + $sum_ajuste); ?></strong></td>
				<td></td>
				<td>
					<button type="submit" class="btn btn-primary">
						<span class="glyphicon glyphicon-ok-sign"></span>
						<?php echo $this->lang->line('inventario_report_save'); ?>
					</button>
				</td>
			</tr>
		</tfoot>
	</table>
	<?php echo form_close(); ?>
</div><!-- fin content-module-main-principal -->

<div class="text-center">
	<?php echo ($links_paginas != '') ? $links_paginas : ''; ?>
</div>
