<div class="content-module">
	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $nombre_inventario?>
			[
			<?php echo anchor('analisis/sube_stock/' . $id_inventario, 'Sube Inventario') ?>
			/
			<?php echo anchor('analisis/imprime_inventario/' . $id_inventario, 'Imprime Inventario') ?>
			]
		</div>
		<div class="fr">
			<?php echo anchor('analisis/ajustes/' . (($ocultar_regularizadas == 0) ? '1' : '0') . '/' . time() ,(($ocultar_regularizadas == 0) ? 'Ocultar' : 'Mostrar') . ' lineas regularizadas') ?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta cf ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main">
		<div class="content-module-main-agregar" style="display: none;">
		</div> <!-- fin content-module-main-agregar -->

		<div class="content-module-main-principal">
		<?php echo form_open('analisis/ajustes/' . $ocultar_regularizadas . '/' . time(), 'id="frm_inventario"'); ?>
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
				<?php foreach ($datos_hoja as $reg): ?>
					<?php if($cat_ant != $reg['catalogo'] and $cat_ant != ''): ?>
						<tr>
							<td colspan="14">&nbsp;</td>
						</tr>
					<?php endif; ?>

					<tr>
						<td class="ac"><?php echo $reg['catalogo']; ?></td>
						<td class="al"><?php echo $reg['descripcion']; ?></td>
						<td class="ac"><?php echo $reg['lote']; ?></td>
						<td class="ac"><?php echo $reg['centro']; ?></td>
						<td class="ac"><?php echo $reg['almacen']; ?></td>
						<td class="ac"><?php echo $reg['ubicacion']; ?></td>
						<td class="ac"><?php echo $reg['hoja']; ?></td>
						<td class="ac"><?php echo $reg['um']; ?></td>
						<td class="ar"><?php echo number_format($reg['stock_sap'],0,',','.'); ?></td>
						<td class="ar"><?php echo number_format($reg['stock_fisico'],0,',','.'); ?></td>
						<td class="ac">
							<?php echo form_input('stock_ajuste_' . $reg['id'], set_value('stock_ajuste_' . $reg['id'], $reg['stock_ajuste']), 'class="ar" size="5" tabindex="' . $tab_index . '"'); ?>
							<?php echo form_error('stock_ajuste_' . $reg['id']); ?>
						</td>
						<td class="ac">
							<?php echo number_format($reg['stock_fisico'] - $reg['stock_sap'] + $reg['stock_ajuste'],0,',','.'); ?>
						</td>
						<td class="ac" style="<?php echo (($reg['stock_fisico'] - $reg['stock_sap'] + $reg['stock_ajuste']) > 0) ? 'background-color: yellow; color: black' : ((($reg['stock_fisico'] - $reg['stock_sap'] + $reg['stock_ajuste']) < 0) ? 'background-color: red; color: white' : 'background-color: green; color: white'); ?>">
							<strong><?php echo (($reg['stock_fisico'] - $reg['stock_sap'] + $reg['stock_ajuste']) > 0) ? 'Sobrante' : ((($reg['stock_fisico'] - $reg['stock_sap'] + $reg['stock_ajuste']) < 0) ? 'Faltante' : 'OK'); ?></strong>
						</td>
						<td class="al">
							<?php echo form_input('observacion_' . $reg['id'], set_value('observacion_' . $reg['id'], $reg['glosa_ajuste']), 'size="30" max_length="200" tabindex="' . ($tab_index + 10000) . '"'); ?>
						</td>
					</tr>
					<?php $sum_sap += $reg['stock_sap']; $sum_fisico += $reg['stock_fisico']; $sum_ajuste += $reg['stock_ajuste']?>
					<?php $tab_index += 1; ?>
					<?php $cat_ant = $reg['catalogo']; ?>
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