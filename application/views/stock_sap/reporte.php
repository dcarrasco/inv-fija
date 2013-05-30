<div class="content-module">

	<div class="content-module-heading cf">
		<?php echo form_open('','id="frm_param"'); ?>
		<div class="">
			<ul>
				<li <?php echo ($nombre_reporte=='permanencia') ? 'class="selected"' : '' ?>>
					<?php echo anchor('/reporte_stock/listado/permanencia','Permanencia')?>
				</li>
			</ul>
		</div>

		<div class="fl" style="padding: 1em;">
			Seleccionar Almacenes
			<br/>
			<?php echo form_multiselect('tipo_alm[]', $combo_tipo_alm, $this->input->post('tipo_alm'), 'size="10" class="form_edit round"'); ?>
		</div>
		<div class="fl" style="padding: 1em;">
			Seleccionar Estados
			<br/>
			<?php echo form_multiselect('estado_sap[]', $combo_estado_sap, $this->input->post('estado_sap'), 'size="10" class="form_edit round"'); ?>
		</div>
		<div class="fl" style="padding: 1em;">
			<?php echo form_checkbox('incl_almacen', '1',set_checkbox('incl_almacen','1', false), 'id="incl_almacen"') ?> Mostrar almacenes<br>
			<?php echo form_checkbox('incl_estado', '1',set_checkbox('incl_estado','1', false), 'id="incl_estado"') ?> Mostrar estado SAP<br>
			<?php echo form_checkbox('incl_lote', '1',set_checkbox('incl_lote','1', false), 'id="incl_lote"') ?> Mostrar lotes <br>
			<?php echo form_checkbox('incl_modelos', '1',set_checkbox('incl_modelos','1', false), 'id="incl_modelos"') ?> Mostrar modelos equipos<br>
			<br/>
			<?php echo form_submit('submit', 'Reporte', 'class="button b-active round ic-reporte"'); ?><br/>
		</div>

		<div class="fl" style="padding: 1em;">
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Fecha reporte: <?php echo $fecha_reporte; ?>
		</div>

		<?php echo form_hidden('order_by', set_value('order_by','')); ?>
		<?php echo form_hidden('order_sort', set_value('order_sort','')); ?>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-heading -->


	<div class="content-module-main">
		<?php $totales    = array(); ?>
		<?php $subtotales = array(); ?>
		<?php $subtot_ant = array(); ?>
		<table class="reporte">
			<thead>
				<tr>
					<?php foreach ($arr_campos as $campo => $arr_param_campo): ?>
						<th <?php echo ($arr_param_campo == '') ? '' : 'class="' . $arr_param_campo['class'] . '"' ?>>
							<?php echo anchor('#', $arr_param_campo['titulo'], array('order_by' => $arr_link_campos[$campo], 'order_sort' => $arr_link_sort[$campo])); ?>
							<?php echo $arr_img_orden[$campo]; ?>
							<?php if ($arr_param_campo['tipo']=='numero' || $arr_param_campo['tipo']=='valor') { $totales[$campo] = 0; }?>
							<?php if ($arr_param_campo['tipo']=='numero' || $arr_param_campo['tipo']=='valor') { $subtotales[$campo] = 0; }?>
						</th>
					<?php endforeach; ?>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($datos_hoja as $reg): ?>
					<tr>
					<?php foreach ($arr_campos as $campo => $arr_param_campo): ?>
						<?php if ($arr_param_campo['tipo'] == 'subtotal'): ?>

							<?php if (!array_key_exists($campo, $subtot_ant)) $subtot_ant[$campo] = ''; ?>
							<?php if ($reg[$campo] != $subtot_ant[$campo]): ?>
								<?php if ($subtot_ant[$campo] != ''): ?>
									<?php foreach ($arr_campos as $c => $arr_c): ?>
										<td <?php echo ($arr_c == '') ? '' : 'class="subtotal ' . $arr_c['class'] . '"' ?>>
										<?php echo ($arr_c['tipo']=='numero' || $arr_c['tipo']=='valor') ? '<strong>' . number_format($subtotales[$c],0,',','.') . '</strong>' : ''; ?>
										</td>
									<?php endforeach; ?>
									</tr>
									<tr>
										<td colspan="<?php echo count($arr_campos); ?>" class="subtotal">&nbsp;</td>
									</tr>
									<tr>
								<?php endif; ?>
								<?php $subtot_ant[$campo] = $reg[$campo]; ?>
								<?php foreach ($arr_campos as $c => $arr_c): ?>
									<?php $subtotales[$c] = 0; ?>
								<?php endforeach; ?>
								<td colspan="<?php echo count($arr_campos); ?>" class="subtotal">
									<?php echo ($arr_param_campo['tipo'] == 'subtotal')  ? $reg[$campo] : ''; ?>
								</td>
								</tr><tr>
							<?php endif; ?>
						<?php endif; ?>
						<td <?php echo ($arr_param_campo == '') ? '' : 'class="' . $arr_param_campo['class'] . '"' ?>>
							<?php echo ($arr_param_campo['tipo'] == 'texto')  ? $reg[$campo] : ''; ?>
							<?php echo ($arr_param_campo['tipo'] == 'link')   ? anchor($arr_param_campo['href'] . $reg[$campo], $reg[$campo]) : ''; ?>
							<?php echo ($arr_param_campo['tipo'] == 'numero') ? number_format($reg[$campo],0,',','.') : ''; ?>
							<?php echo ($arr_param_campo['tipo'] == 'valor')  ? '$ ' . number_format($reg[$campo],0,',','.') : ''; ?>
							<?php echo ($arr_param_campo['tipo'] == 'valor_pmp') ? '$ ' . number_format($reg[$campo],0,',','.') : ''; ?>
							<?php if ($arr_param_campo['tipo']=='numero' || $arr_param_campo['tipo']=='valor') { $totales[$campo] += $reg[$campo]; }?>
							<?php if ($arr_param_campo['tipo']=='numero' || $arr_param_campo['tipo']=='valor') { $subtotales[$campo] += $reg[$campo]; }?>
						</td>
					<?php endforeach; ?>
					</tr>
				<?php endforeach; ?>

				<!-- ultima linea de subtotales -->
				<?php foreach ($arr_campos as $campo => $arr_param_campo): ?>
					<?php if ($arr_param_campo['tipo'] == 'subtotal'): ?>
						<?php foreach ($arr_campos as $c => $arr_c): ?>
							<td <?php echo ($arr_c == '') ? '' : 'class="subtotal ' . $arr_c['class'] . '"' ?>>
							<?php echo ($arr_c['tipo']=='numero' || $arr_c['tipo']=='valor') ? '<strong>' . number_format($subtotales[$c],0,',','.') . '</strong>' : ''; ?>
							</td>
						<?php endforeach; ?>
						</tr>
						<tr>
							<td colspan="<?php echo count($arr_campos); ?>" class="subtotal">&nbsp;</td>
						</tr>
					<?php endif; ?>
				<?php endforeach; ?>

				<tr> <!-- totales -->
					<?php foreach ($arr_campos as $campo => $arr_param_campo): ?>
						<td <?php echo ($arr_param_campo == '') ? '' : 'class="subtotal ' . $arr_param_campo['class'] . '"' ?>>
							<?php echo ($arr_param_campo['tipo']=='numero' || $arr_param_campo['tipo']=='valor') ? '<strong>' . number_format($totales[$campo],0,',','.') . '</strong>' : ''; ?>
						</td>
					<?php endforeach; ?>
				</tr>
			</tbody>
		</table>
	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->

<script type="text/javascript">
$(document).ready(function() {
	$('#filtrar_material').keyup(function (event) {
		$('tr.not_found').show();
		$('tr.not_found').removeClass('not_found');

		var a_buscar = $('#filtrar_material').val().toUpperCase();
		if (a_buscar != '') {
			$('div.content-module-main tr').each(function() {
				var nodo_texto = $(this).children('td:eq(1)');
				if (nodo_texto.size() > 0) {
					if (nodo_texto.html().toUpperCase().indexOf(a_buscar) == -1) {
						$(this).addClass('not_found');
					}
				}
			});
			$('tr.not_found').hide();
			$('#filtrar_material').addClass('search_found');
		} else {
			$('#filtrar_material').removeClass('search_found');
		}
	});

	$('div.content-module-main th a').click(function (event) {
		event.preventDefault();
		$('form input[name="order_by"]').val($(this).attr('order_by'));
		$('form input[name="order_sort"]').val($(this).attr('order_sort'));
		$('form').submit();
	});

	$('div.content-module-main td a').click(function (event) {
		event.preventDefault();
		$('form').attr('action', ($(this).attr('href')));
		$('form input[name="order_by"]').val('');
		$('form input[name="order_sort"]').val('');
		$('form').submit();
	});


	$('td.subtotal[colspan]').parent().each(function(i) {
		if ($(this).size() == 1) {
			if ($(this).children().html() != '&nbsp;') {
				$(this).children(':first').html('<span>[-]</span>' + $(this).children(':first').html());
				$(this).addClass('tr_subtotal_' + i);
				$(this).children().addClass('tr_subtotal');
			}
		}
	});

	$('tr[class^="tr_subtotal_"]').each(function() {
		var clase = $(this).attr('class');
		$(this).nextUntil('tr[class^="tr_subtotal_"]').addClass(clase + '_data');
	})

	$('td.tr_subtotal').click(function() {
		var clase = $(this).parent().attr('class');
		var txt_expand = ($(this).parent().next().is(':visible')) ? '[+]' : '[-]';
		$(this).children('span').html(txt_expand);
		$('tr.' + clase + '_data').toggle();
	});



});
</script>