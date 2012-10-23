
<div class="content-module">

	<div class="content-module-heading cf">
		<div class="cf">
			<div class="fl" style="margin-right: 10px;">
				<?php echo form_open('/inventario/ingreso', 'id="frm_buscar"'); ?>
				<?php echo form_hidden('formulario','buscar'); ?>
				<p>
					Hoja <?php echo form_input('sel_hoja', set_value('sel_hoja', $hoja), 'maxlength="3" size="3"'); ?>
					<?php echo form_error('sel_hoja');?>
					| Auditor <?php echo form_dropdown('sel_auditor', $combo_auditores, set_value('sel_auditor', $id_auditor)); ?>
					<?php echo form_error('sel_auditor');?>
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
			<?php echo form_open('','id=frm_agregar')?>
			<?php echo form_hidden('formulario','agregar'); ?>
			<?php echo form_hidden('accion','agregar'); ?>
			<?php echo form_hidden('sel_hoja', $hoja); ?>
			<?php //echo form_hidden('sel_digitador', $id_digitador); ?>
			<?php echo form_hidden('sel_auditor', $id_auditor); ?>
			<?php echo form_hidden('agr_id', 0); ?>
			<table>
				<tr>
					<th></th>
					<th class="ac">Ingreso datos inventario</th>
				</tr>
				<tr>
					<th class="ar">ubicacion</th>
					<td>
						<?php echo form_input('agr_ubicacion', set_value('agr_ubicacion'), 'size="10"'); ?>
						<?php echo form_error('agr_ubicacion'); ?>
					</td>
				</tr>
				<tr>
					<th class="ar">material</th>
					<td>
						Filtrar <?php echo form_input('agr_filtrar', set_value('agr_filtrar'), 'id="agr_filtrar"'); ?>
						&nbsp;&nbsp;&nbsp;&nbsp;
						Seleccionar
						<?php echo form_dropdown('agr_material', array('' => 'Seleccionar material...'), '', 'id="agr_material"'); ?>
						<?php echo form_error('agr_material'); ?>
					</td>
				</tr>
				<tr>
					<th class="ar">lote</th>
					<td>
						<?php echo form_input('agr_lote', set_value('agr_lote'), 'size="5"'); ?>
						<?php echo form_error('agr_lote'); ?>
					</td>
				</tr>
				<tr>
					<th class="ar">um</th>
					<td>
						<?php echo form_dropdown('agr_um', array('' => 'Seleccionar UM...', 'UN' => 'UN', 'M' => 'M', 'KG' => 'KG', 'ST' => 'ST', 'EA' => 'EA' ),set_value('agr_um')); ?>
						<?php echo form_error('agr_um'); ?>
					</td>
				</tr>
				<tr>
					<th class="ar">centro</th>
					<td>
						<?php echo form_dropdown('agr_centro', 
													array(
														''     => 'Seleccionar centro...', 
														'CH01' => 'CH01', 
														'CH02' => 'CH02', 
														'CH04' => 'CH04', 
														'CH05' => 'CH05', 
														'CH11' => 'CH11', 
														'CH19' => 'CH19', 
														'CH24' => 'CH24',
														'CH28' => 'CH28', 
														'CH29' => 'CH29',
													), 
													set_value('agr_centro')); ?>
						<?php echo form_error('agr_centro'); ?>
					</td>
				</tr>
				<tr>
					<th class="ar">almacen</th>
					<td>
						<?php echo form_dropdown('agr_almacen', 
													array(
														''     => 'Seleccionar almacen...', 
														'CM01' => 'CM01', 
														'CM02' => 'CM02', 
														'CM03' => 'CM03', 
														'CM04' => 'CM04', 
														'CM11' => 'CM11',
														'CM15' => 'CM15',
														'GM01' => 'GM01',
														'GM03' => 'GM03',
													),
													set_value('agr_almacen')); ?>
						<?php echo form_error('agr_almacen'); ?>
					</td>
				</tr>
				<tr>
					<th class="ar">cantidad</th>
					<td>
						<?php echo form_input('agr_cantidad', set_value('agr_cantidad'), 'size="10"'); ?>
						<?php echo form_error('agr_cantidad'); ?>
					</td>
				</tr>
				<tr>
					<th class="ar">observacion</th>
					<td>
						<?php echo form_input('agr_observacion', set_value('agr_observacion'), 'max'); ?>
						<?php echo form_error('agr_observacion'); ?>
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
		<?php echo form_hidden('sel_hoja', $hoja); ?>
		<?php //echo form_hidden('sel_digitador', $id_digitador); ?>
		<?php echo form_hidden('sel_auditor', $id_auditor); ?>
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
				<?php foreach ($datos_hoja as $reg): ?>
					<tr>
						<td class="ac">
							<?php echo $reg['ubicacion']; ?>

							<?php if ($reg['reg_nuevo'] == 'S'):?>
							<?php echo anchor('#', 'Ed', 'class="button_mini b-active round ic-edit fl" id="btn_editar_id_' . $reg['id'] . '"'); ?>
							<?php echo form_hidden('ubicacion_' . $reg['id'], $reg['ubicacion']); ?>
							<?php echo form_hidden('catalogo_' . $reg['id'], $reg['catalogo']); ?>
							<?php echo form_hidden('descripcion_' . $reg['id'], $reg['descripcion']); ?>
							<?php echo form_hidden('lote_' . $reg['id'], $reg['lote']); ?>
							<?php echo form_hidden('centro_' . $reg['id'], $reg['centro']); ?>
							<?php echo form_hidden('almacen_' . $reg['id'], $reg['almacen']); ?>
							<?php echo form_hidden('um_' . $reg['id'], $reg['um']); ?>
							<?php endif; ?>
						</td>
						<td><?php echo $reg['catalogo']; ?></td>
						<td><?php echo $reg['descripcion']; ?></td>
						<td><?php echo $reg['lote']; ?></td>
						<td><?php echo $reg['centro']; ?></td>
						<td><?php echo $reg['almacen']; ?></td>
						<td><?php echo $reg['um']; ?></td>
						<td class="ar"><?php echo number_format($reg['stock_sap'],0,',','.'); ?></td>
						<td>
							<?php echo form_input('stock_fisico_' . $reg['id'], set_value('stock_fisico_' . $reg['id'], $reg['stock_fisico']), 'class="ar" size="10" tabindex="' . $tab_index . '"'); ?>
							<?php echo form_error('stock_fisico_' . $reg['id']); ?>
						</td>
						<td>
							<?php echo form_input('observacion_' . $reg['id'], set_value('observacion_' . $reg['id'], $reg['observacion']), 'size="20" max_length="100" tabindex="' . ($tab_index + 100) . '"'); ?>
						</td>
					</tr>
					<?php $sum_sap += $reg['stock_sap']; $sum_fisico += $reg['stock_fisico'];?>
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
