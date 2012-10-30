
<div class="content-module">

	<div class="content-module-heading cf">
		<div class="cf">
			<div class="fl" style="margin-right: 10px;">
				<?php echo form_open('/inventario/ingreso', 'id="frm_buscar"'); ?>
				<?php echo form_hidden('formulario','buscar'); ?>
				<p>
					Hoja
					<?php echo $nuevo_detalle_inventario->print_form_field('hoja'); ?>
					<?php echo form_error('hoja');?>
					| Auditor
					<?php echo $nuevo_detalle_inventario->print_form_field('auditor'); ?>
					<?php echo form_error('auditor');?>
				</p>
			</div>
			<div class="fl" style="margin-right: 10px;">
				<?php echo anchor('#','Buscar', 'class="button b-active round ic-search fl" id="btn_buscar"'); ?>
				<?php echo anchor($link_hoja_ant,'Hoja Prev', 'class="button b-active round ic-hoja-ant fl" id="btn_hoja_ant"'); ?>
				<?php echo anchor($link_hoja_sig,'Hoja Sig', 'class="button b-active round ic-hoja-sig fl" id="btn_hoja_sig"'); ?>


				<?php echo form_close(); ?>
			</div>

			<div class="fl" style="margin-right: 10px;">
				Digitador: <?php echo $nombre_digitador; ?><br>
				Auditor:<?php echo $nombre_auditor; ?>
			</div>

			<div class="fr" style="margin-right: 10px;">
				<?php echo anchor('#','Nuevo material ...', 'class="button b-active round ic-desplegar fr" id="btn_mostrar_agregar"'); ?>
			</div>
		</div>
		<div class="msg-alerta cf ac">
			<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">

		<div class="content-module-main-agregar" style="display: none;">
			<?php echo form_open("inventario/ingreso/$hoja/$id_auditor/".time(), 'id=frm_agregar')?>
			<?php echo form_hidden('formulario','agregar'); ?>
			<?php echo form_hidden('accion','agregar'); ?>
			<?php echo form_hidden('hoja', $hoja); ?>
			<?php echo form_hidden('auditor', $id_auditor); ?>
			<?php echo form_hidden('id', ''); ?>
			<table>
				<tr>
					<th></th>
					<th class="ac">Ingreso datos inventario</th>
				</tr>

				<tr>
					<th class="ar"><?php echo $nuevo_detalle_inventario->get_label_field('ubicacion'); ?></th>
					<td><?php echo $nuevo_detalle_inventario->print_form_field('ubicacion'); ?>
						<?php echo form_error('ubicacion'); ?>
					</td>
				</tr>

				<tr>
					<th class="ar">material</th>
					<td>
						Filtrar <?php echo form_input('agr_filtrar', set_value('agr_filtrar'), 'id="agr_filtrar"'); ?>
						&nbsp;&nbsp;&nbsp;&nbsp;
						Seleccionar
						<?php echo form_dropdown('catalogo', array('' => 'Seleccionar material...'), '', 'id="agr_material"'); ?>
						<?php echo form_error('catalogo'); ?>
					</td>
				</tr>

				<tr>
					<th class="ar"><?php echo $nuevo_detalle_inventario->get_label_field('lote'); ?></th>
					<td><?php echo $nuevo_detalle_inventario->print_form_field('lote'); ?>
						<?php echo form_error('lote'); ?>
					</td>
				</tr>

				<tr>
					<th class="ar"><?php echo $nuevo_detalle_inventario->get_label_field('um'); ?></th>
					<td><?php echo $nuevo_detalle_inventario->print_form_field('um'); ?>
						<?php echo form_error('um'); ?>
					</td>
				</tr>

				<tr>
					<th class="ar"><?php echo $nuevo_detalle_inventario->get_label_field('centro'); ?></th>
					<td><?php echo $nuevo_detalle_inventario->print_form_field('centro'); ?>
						<?php echo form_error('centro'); ?>
					</td>
				</tr>

				<tr>
					<th class="ar"><?php echo $nuevo_detalle_inventario->get_label_field('almacen'); ?></th>
					<td><?php echo $nuevo_detalle_inventario->print_form_field('almacen'); ?>
						<?php echo form_error('almacen'); ?>
					</td>
				</tr>

				<tr>
					<th class="ar"><?php echo $nuevo_detalle_inventario->get_label_field('stock_fisico'); ?></th>
					<td><?php echo $nuevo_detalle_inventario->print_form_field('stock_fisico'); ?>
						<?php echo form_error('stock_fisico'); ?>
					</td>
				</tr>

				<tr>
					<th class="ar"><?php echo $nuevo_detalle_inventario->get_label_field('observacion'); ?></th>
					<td><?php echo $nuevo_detalle_inventario->print_form_field('observacion'); ?>
						<?php echo form_error('observacion'); ?>
					</td>
				</tr>

				<tr>
					<th></th>
					<td class="ac cf">
						<?php echo anchor('#','Agregar material', 'class="button b-active round ic-agregar fl" id="btn_agregar"'); ?>
						<?php echo anchor('#','Borrar', 'class="button b-active round ic-borrar fl" id="btn_borrar"'); ?>
					</td>
				</tr>
			</table>
			<?php echo form_close(); ?>
		</div> <!-- fin content-module-main-agregar -->

		<div class="content-module-main-principal">
		<?php echo form_open("inventario/ingreso/$hoja/$id_auditor/".time(), 'id="frm_inventario"'); ?>
		<?php echo form_hidden('formulario','inventario'); ?>
		<?php echo form_hidden('hoja', $hoja); ?>
		<?php echo form_hidden('auditor', $id_auditor); ?>
		<?php //echo form_error('sel_digitador'); ?>
		<?php //echo form_error('sel_auditor'); ?>
		<table>
			<thead>
				<tr>
					<th class="ac">ubicacion</th>
					<th>material</th>
					<th>descripcion</th>
					<th>lote</th>
					<th>centro</th>
					<th>almacen</th>
					<th>UM</th>
					<th>cantidad sap</th>
					<th>cantidad fisica</th>
					<th>observacion</th>
				</tr>
			</thead>
			<tbody>
				<?php $sum_sap = 0; $sum_fisico = 0;?>
				<?php $tab_index = 10; ?>
				<?php foreach ($detalle_inventario->get_model_all() as $linea_det): ?>
					<tr>
						<td class="ac">
							<?php echo $linea_det->ubicacion; ?>

							<?php if ($linea_det->reg_nuevo == 'S'):?>
							<?php echo anchor('#', 'Ed', 'class="button_mini b-active round ic-edit fl" id="btn_editar_id_' . $linea_det->id . '"'); ?>
							<?php echo form_hidden('ubicacion_'   . $linea_det->id, $linea_det->ubicacion); ?>
							<?php echo form_hidden('catalogo_'    . $linea_det->id, $linea_det->catalogo); ?>
							<?php echo form_hidden('descripcion_' . $linea_det->id, $linea_det->descripcion); ?>
							<?php echo form_hidden('lote_'        . $linea_det->id, $linea_det->lote); ?>
							<?php echo form_hidden('centro_'      . $linea_det->id, $linea_det->centro); ?>
							<?php echo form_hidden('almacen_'     . $linea_det->id, $linea_det->almacen); ?>
							<?php echo form_hidden('um_'          . $linea_det->id, $linea_det->um); ?>
							<?php endif; ?>
						</td>
						<td><?php echo $linea_det->catalogo; ?></td>
						<td><?php echo $linea_det->descripcion; ?></td>
						<td><?php echo $linea_det->lote; ?></td>
						<td><?php echo $linea_det->centro; ?></td>
						<td><?php echo $linea_det->almacen; ?></td>
						<td><?php echo $linea_det->um; ?></td>
						<td class="ar"><?php echo number_format($linea_det->stock_sap,0,',','.'); ?></td>
						<td>
							<?php echo form_input('stock_fisico_' . $linea_det->id, set_value('stock_fisico_' . $linea_det->id, $linea_det->stock_fisico), 'class="ar" size="10" tabindex="' . $tab_index . '"'); ?>
							<?php echo form_error('stock_fisico_' . $linea_det->id); ?>
						</td>
						<td>
							<?php echo form_input('observacion_' . $linea_det->id, set_value('observacion_' . $linea_det->id, $linea_det->observacion), 'size="20" max_length="100" tabindex="' . ($tab_index + 100) . '"'); ?>
						</td>
					</tr>
					<?php $sum_sap += $linea_det->stock_sap; $sum_fisico += $linea_det->stock_fisico;?>
					<?php $tab_index += 1; ?>
				<?php endforeach; ?>

				<!-- totales -->
				<tr>
					<td colspan="2">
					</td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td></td>
					<td class="ar"><strong><?php echo number_format($sum_sap,0,',','.'); ?></strong></td>
					<td class="ac"><strong><?php echo number_format($sum_fisico,0,',','.'); ?></strong></td>
					<td></td>
				</tr>
			</tbody>
		</table>
		<?php echo form_close(); ?>
	</div><!-- fin content-module-main-principal -->

	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
		<?php echo anchor('#','Guardar hoja', 'class="button b-active round ic-ok fr" id="btn_guardar"'); ?>
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->

<script type="text/javascript" src="<?php echo base_url(); ?>js/view_inventario.js"></script>
