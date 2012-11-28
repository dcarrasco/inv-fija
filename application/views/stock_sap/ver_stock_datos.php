	<div class="content-module-main">

		<?php $totales = array(); ?>
		<?php $campos  = array(); ?>
		<?php $campos_sumables = array('LU','BQ','CC','TT','OT','total','EQUIPOS','SIMCARD','OTROS','cantidad','VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','monto','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS'); ?>
		<?php $campos_montos   = array('VAL_LU','VAL_BQ','VAL_CC','VAL_TT','VAL_OT','monto','VAL_EQUIPOS','VAL_SIMCARD','VAL_OTROS'); ?>
		<table class="reporte">
			<?php foreach($stock as $key_reg => $reg): ?>
				<?php // ********************************************************* ?>
				<?php // Imprime encabezados                                       ?>
				<?php // ********************************************************* ?>
				<?php if ($key_reg == 0): ?>
					<thead>
						<tr>
						<?php foreach($reg as $key => $val): ?>
							<th <?php echo (in_array($key, $campos_sumables) ? 'class="ar"' : '')?>><?php echo str_replace('_', ' ', $key); ?></th>
							<?php array_push($campos, $key); ?>
						<?php endforeach; ?>
						</tr>
					</thead>
					<tbody>
				<?php endif; ?>


				<?php // ********************************************************* ?>
				<?php // Imprime linea normal con datos                            ?>
				<?php // ********************************************************* ?>
				<tr>
					<?php foreach($reg as $key => $val): ?>
						<?php if (in_array($key, $campos_sumables)): ?>
							<td class="ar">
								<?php echo anchor('stock_sap/detalle_series/' .
														(array_key_exists('centro', $reg) ? $reg['centro'] : '_') . '/' .
														(array_key_exists('cod_almacen', $reg) ? $reg['cod_almacen'] : '_') . '/' .
														(array_key_exists('cod_articulo', $reg) ? $reg['cod_articulo'] : '_') . '/' .
														(array_key_exists('lote', $reg) ? $reg['lote'] : '_'),
													((in_array($key, $campos_montos)) ? '$ ' : '') . number_format($val,0,',','.')
												); ?>
							</td>
							<?php if (!array_key_exists($key, $totales)) $totales[$key] = 0; ?>
							<?php $totales[$key] += $val; ?>
						<?php else: ?>
							<td><?php echo ($val); ?></td>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			</tbody>

			<?php // ********************************************************* ?>
			<?php // Imprime totales finales de la tabla                       ?>
			<?php // ********************************************************* ?>
			<tfoot>
				<tr>
					<?php foreach($campos as $val): ?>
						<?php if (in_array($val, $campos_sumables)): ?>
							<th class="ar">
									<?php if (in_array($val, $campos_montos)): ?> $ <?php endif; ?>
									<?php echo number_format($totales[$val],0,',','.'); ?>
							</th>
						<?php else: ?>
							<th></th>
						<?php endif; ?>
					<?php endforeach; ?>
				</tr>
			</tfoot>

		</table>

	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->
