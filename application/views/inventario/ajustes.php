<div class="content-module">
	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_ajustes; ?>
		</div>
		<div class="fr">
			<?php echo anchor('analisis/ajustes/' . (($ocultar_regularizadas == 0) ? '1' : '0') . '/' . $pag . '/' . time() ,(($ocultar_regularizadas == 0) ? 'Ocultar' : 'Mostrar') . ' lineas regularizadas') ?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta cf ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main">
		<div class="content-module-main-agregar" style="display: none;">
		</div> <!-- fin content-module-main-agregar -->

		<div class="content-module-main-principal">
		<?php echo form_open('analisis/ajustes/' . $ocultar_regularizadas . '/' . $pag . '/' . time(), 'id="frm_inventario"'); ?>
		<?php echo form_hidden('formulario','ajustes'); ?>
		<table>
			<thead>
				<tr>
					<th class="ac">material</th>
					<th class="al">descripcion</th>
					<th class="ac">lote</th>
					<th class="ac">centro</th>
					<th class="ac">almacen</th>
					<th class="ac">ubicacion</th>
					<th class="ac">hu</th>
					<th class="ac">hoja</th>
					<th class="ac">UM</th>
					<th class="ar">cant sap</th>
					<th class="ar">cant fisica</th>
					<th class="ac">cant ajuste</th>
					<th class="ac">dif</th>
					<th class="ac">tipo dif</th>
					<th class="al">observacion ajuste</th>
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
						<td class="ac"><?php echo $detalle->catalogo; ?></td>
						<td class="al"><?php echo $detalle->descripcion; ?></td>
						<td class="ac"><?php echo $detalle->lote; ?></td>
						<td class="ac"><?php echo $detalle->centro; ?></td>
						<td class="ac"><?php echo $detalle->almacen; ?></td>
						<td class="ac"><?php echo $detalle->ubicacion; ?></td>
						<td class="ac"><?php echo $detalle->hu; ?></td>
						<td class="ac"><?php echo $detalle->hoja; ?></td>
						<td class="ac"><?php echo $detalle->um; ?></td>
						<td class="ar"><?php echo number_format($detalle->stock_sap,0,',','.'); ?></td>
						<td class="ar"><?php echo number_format($detalle->stock_fisico,0,',','.'); ?></td>
						<td class="ac">
							<?php echo form_input('stock_ajuste_' . $detalle->id, set_value('stock_ajuste_' . $detalle->id, $detalle->stock_ajuste), 'class="ar" size="5" tabindex="' . $tab_index . '"'); ?>
							<?php echo form_error('stock_ajuste_' . $detalle->id); ?>
						</td>
						<td class="ac">
							<?php echo number_format($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste,0,',','.'); ?>
						</td>
						<td class="ac" style="<?php echo (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) > 0) ? 'background-color: yellow; color: black' : ((($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) < 0) ? 'background-color: red; color: white' : 'background-color: green; color: white'); ?>">
							<strong><?php echo (($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) > 0) ? 'Sobrante' : ((($detalle->stock_fisico - $detalle->stock_sap + $detalle->stock_ajuste) < 0) ? 'Faltante' : 'OK'); ?></strong>
						</td>
						<td class="al">
							<?php echo form_input('observacion_' . $detalle->id, set_value('observacion_' . $detalle->id, $detalle->glosa_ajuste), 'size="30" max_length="200" tabindex="' . ($tab_index + 10000) . '"'); ?>
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
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="ar"><strong><?php echo number_format($sum_sap,0,',','.'); ?></strong></td>
					<td class="ar"><strong><?php echo number_format($sum_fisico,0,',','.'); ?></strong></td>
					<td class="ar"><strong><?php echo number_format($sum_ajuste,0,',','.'); ?></strong></td>
					<td class="ar"><strong><?php echo number_format($sum_fisico - $sum_sap + $sum_ajuste,0,',','.'); ?></strong></td>
					<td></td>
					<td></td>
				</tr>

				<?php if ($links_paginas != ''):?>
					<tr>
						<td colspan="15"><div class="paginacion ac"><?php echo $links_paginas; ?></div></td>
					</tr>
				<?php endif; ?>

			</tbody>
		</table>
		<?php echo form_close(); ?>
	</div><!-- fin content-module-main-principal -->

	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
		<?php echo anchor('#','Guardar ajustes', 'class="button b-active round ic-ok fr" id="btn_guardar"'); ?>
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->

<script type="text/javascript">
	$(document).ready(function() {
		$('#btn_guardar').click(function(event) {
			event.preventDefault();
			$('form#frm_inventario').submit();
		});

	});
</script>