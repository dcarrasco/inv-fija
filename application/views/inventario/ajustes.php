<div>

	<?php echo anchor($this->uri->segment(1) . '/ajustes/' . (($ocultar_regularizadas == 0) ? '1' : '0') . '/' . $pag . '/' . time() ,(($ocultar_regularizadas == 0) ? 'Ocultar' : 'Mostrar') . ' lineas regularizadas') ?>
</div>

<div>
	<?php echo form_open($this->uri->segment(1) . '/ajustes/' . $ocultar_regularizadas . '/' . $pag . '/' . time(), 'id="frm_inventario"'); ?>
	<?php echo form_hidden('formulario','ajustes'); ?>

	<table class="table table-bordered table-hover table-condensed reporte">
		<thead>
			<tr>
				<th>
					<?php echo $this->lang->line('inventario_digit_th_material'); ?>
				</th>
				<th>
					<?php echo $this->lang->line('inventario_digit_th_descripcion'); ?>
				</th>
				<th>
					<?php echo $this->lang->line('inventario_digit_th_lote'); ?>
				</th>
				<th>
					<?php echo $this->lang->line('inventario_digit_th_centro'); ?>
				</th>
				<th>
					<?php echo $this->lang->line('inventario_digit_th_almacen'); ?>
				</th>
				<th>
					<?php echo $this->lang->line('inventario_digit_th_ubicacion'); ?>
				</th>
				<!-- <th>hu</th> -->
				<th>
					<?php echo $this->lang->line('inventario_digit_th_hoja'); ?>
				</th>
				<th>
					<?php echo $this->lang->line('inventario_digit_th_UM'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_cant_sap'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_cant_fisica'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_cant_ajuste'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_dif'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_tipo_dif'); ?>
				</th>
				<th>
					<?php echo $this->lang->line('inventario_digit_th_observacion_ajuste'); ?>
				</th>
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
					<td class="text-center"><?php echo fmt_cantidad($detalle->stock_sap); ?></td>
					<td class="text-center"><?php echo fmt_cantidad($detalle->stock_fisico); ?></td>
					<td>
						<?php echo form_input('stock_ajuste_' . $detalle->id, set_value('stock_ajuste_' . $detalle->id, $detalle->stock_ajuste), 'class="form-control input-sm" size="5" tabindex="' . $tab_index . '"'); ?>
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
					<!--
					<td style="<?php echo (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) > 0) ? 'background-color: yellow; color: black' : ((($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) < 0) ? 'background-color: red; color: white' : 'background-color: green; color: white'); ?>">
						<strong><?php echo (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) > 0) ? 'Sobrante' : ((($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) < 0) ? 'Faltante' : 'OK'); ?></strong>
					</td>
					-->
					<td>
						<?php echo form_input('observacion_' . $detalle->id, set_value('observacion_' . $detalle->id, $detalle->glosa_ajuste), 'class="form-control input-sm" max_length="200" tabindex="' . ($tab_index + 10000) . '"'); ?>
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
		</tbody>
	</table>
	<?php echo form_close(); ?>
</div><!-- fin content-module-main-principal -->

<div class="text-center">
	<?php echo ($links_paginas != '') ? $links_paginas : ''; ?>
</div>
