<div class="row-fluid">
	<div class="span6">
		<?php echo form_open('/inventario/ingreso', 'id="frm_buscar"'); ?>
		<?php echo form_hidden('formulario','buscar'); ?>
		<div>
			Hoja
			<input type="text" name="hoja" value="<?php echo $hoja; ?>" maxlength="10" id="id_hoja" class="input-mini">
			<a href="#" class="btn" id="btn_buscar">
				<i class="icon-search"></i>
				Buscar
			</a>
			<a href="<?php echo $link_hoja_ant; ?>" class="btn" id="btn_hoja_ant">
				<i class="icon-chevron-left"></i>
				Hoja Prev
			</a>
			<a href="<?php echo $link_hoja_sig; ?>" class="btn" id="btn_hoja_sig">
				<i class="icon-chevron-right"></i>
				Hoja Sig
			</a>
		</div>
		<div>
			Auditor
			<?php echo $nuevo_detalle_inventario->print_form_field('auditor'); ?>
			<?php echo form_error('auditor');?>
		</div>
		<div>
			<?php echo form_error('hoja');?>

		</div>
		<?php echo form_close(); ?>
	</div>

	<div class="span3">
		Digitador: <?php echo $nombre_digitador; ?><br>
		Auditor:<?php echo $nombre_auditor; ?>
	</div>

	<div class="span3 text-right">
		<a href="#" id="btn_mostrar_agregar" class="btn">
			<i class="icon-plus-sign"></i>
			Nuevo material...
		</a>
	</div>
</div>

<div class="msg-alerta cf ac">
	<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
</div>

<div class="row-fluid">
<div class="span8 offset2" style="display: none;" id="frm_agregar">
	<?php echo form_open("inventario/ingreso/$hoja/$id_auditor/".time(), 'class="form-horizontal" id=frm_agregar')?>
	<?php echo form_hidden('formulario','agregar'); ?>
	<?php echo form_hidden('accion','agregar'); ?>
	<?php echo form_hidden('hoja', $hoja); ?>
	<?php echo form_hidden('auditor', $id_auditor); ?>
	<?php echo form_hidden('id', ''); ?>
		<fieldset>
			<legend>Ingreso datos inventario</legend>

			<div class="control-group">
				<label class="control-label">
					<?php echo $nuevo_detalle_inventario->get_label_field('ubicacion'); ?>
					<?php echo ($nuevo_detalle_inventario->get_es_obligatorio_field('ubicacion')) ? '[*]' : ''; ?>
				</label>
				<div class="controls">
					<?php echo $nuevo_detalle_inventario->print_form_field('ubicacion'); ?>
					<?php echo form_error('ubicacion'); ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo $nuevo_detalle_inventario->get_label_field('hu'); ?>
					<?php echo ($nuevo_detalle_inventario->get_es_obligatorio_field('hu')) ? '[*]' : ''; ?>
				</label>
				<div class="controls">
					<?php echo $nuevo_detalle_inventario->print_form_field('hu'); ?>
					<?php echo form_error('hu'); ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					Material
					<?php echo ($nuevo_detalle_inventario->get_es_obligatorio_field('catalogo')) ? '[*]' : ''; ?>
				</label>
				<div class="controls">
					<div class="input-append">
						<?php echo form_input('agr_filtrar', set_value('agr_filtrar'), 'class="input-small" id="agr_filtrar"'); ?>
						<span class="add-on"><i class="icon-search"></i></span>
					</div>
					<?php echo form_dropdown('catalogo', array('' => 'Buscar y seleccionar material...'), '', 'class="input-xlarge" id="agr_material"'); ?>
					<?php echo form_error('catalogo'); ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo $nuevo_detalle_inventario->get_label_field('lote'); ?>
					<?php echo ($nuevo_detalle_inventario->get_es_obligatorio_field('lote')) ? '[*]' : ''; ?>
				</label>
				<div class="controls">
					<?php echo $nuevo_detalle_inventario->print_form_field('lote'); ?>
					<?php echo form_error('lote'); ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo $nuevo_detalle_inventario->get_label_field('um'); ?>
					<?php echo ($nuevo_detalle_inventario->get_es_obligatorio_field('um')) ? '[*]' : ''; ?>
				</label>
				<div class="controls">
					<?php echo $nuevo_detalle_inventario->print_form_field('um'); ?>
					<?php echo form_error('um'); ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo $nuevo_detalle_inventario->get_label_field('centro'); ?>
					<?php echo ($nuevo_detalle_inventario->get_es_obligatorio_field('centro')) ? '[*]' : ''; ?>
				</label>
				<div class="controls">
					<?php echo $nuevo_detalle_inventario->print_form_field('centro'); ?>
					<?php echo form_error('centro'); ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo $nuevo_detalle_inventario->get_label_field('almacen'); ?>
					<?php echo ($nuevo_detalle_inventario->get_es_obligatorio_field('almacen')) ? '[*]' : ''; ?>
				</label>
				<div class="controls">
					<?php echo $nuevo_detalle_inventario->print_form_field('almacen'); ?>
					<?php echo form_error('almacen'); ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo $nuevo_detalle_inventario->get_label_field('stock_fisico'); ?>
					<?php echo ($nuevo_detalle_inventario->get_es_obligatorio_field('stock_fisico')) ? '[*]' : ''; ?>
				</label>
				<div class="controls">
					<?php echo $nuevo_detalle_inventario->print_form_field('stock_fisico'); ?>
					<?php echo form_error('stock_fisico'); ?>
				</div>
			</div>

			<div class="control-group">
				<label class="control-label">
					<?php echo $nuevo_detalle_inventario->get_label_field('observacion'); ?>
					<?php echo ($nuevo_detalle_inventario->get_es_obligatorio_field('observacion')) ? '[*]' : ''; ?>
				</label>
				<div class="controls">
					<?php echo $nuevo_detalle_inventario->print_form_field('observacion'); ?>
					<?php echo form_error('observacion'); ?>
				</div>
			</div>

			<div class="control-group">
				<div class="controls">
					<a href="#" class="btn btn-primary" id="btn_agregar">
						<i class="icon-plus-sign icon-white"></i>
						Agregar material
					</a>
					<a href="#" class="btn" id="btn_borrar">
						<i class="icon-trash"></i>
						Borrar
					</a>
				</div>
			</div>
	<?php echo form_close(); ?>
</div> <!-- fin content-module-main-agregar -->
</div>

<div>
<?php echo form_open("inventario/ingreso/$hoja/$id_auditor/".time(), 'id="frm_inventario"'); ?>
	<?php echo form_hidden('formulario','inventario'); ?>
	<?php echo form_hidden('hoja', $hoja); ?>
	<?php echo form_hidden('auditor', $id_auditor); ?>
	<?php //echo form_error('sel_digitador'); ?>
	<?php //echo form_error('sel_auditor'); ?>
	<table class="table table-bordered table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th>ubicacion</th>
				<th>HU</th>
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
						<?php echo $linea_det->get_valor_field('ubicacion'); ?>

						<?php if ($linea_det->reg_nuevo == 'S'):?>
							<a href="#" class="btn btn-mini" id="btn_editar_id_<?php echo $linea_det->id; ?>">
								<i class="icon-edit"></i>
							</a>
							<?php echo form_hidden('ubicacion_'   . $linea_det->id, $linea_det->ubicacion); ?>
							<?php echo form_hidden('hu_'          . $linea_det->id, $linea_det->hu); ?>
							<?php echo form_hidden('catalogo_'    . $linea_det->id, $linea_det->catalogo); ?>
							<?php echo form_hidden('descripcion_' . $linea_det->id, $linea_det->descripcion); ?>
							<?php echo form_hidden('lote_'        . $linea_det->id, $linea_det->lote); ?>
							<?php echo form_hidden('centro_'      . $linea_det->id, $linea_det->centro); ?>
							<?php echo form_hidden('almacen_'     . $linea_det->id, $linea_det->almacen); ?>
							<?php echo form_hidden('um_'          . $linea_det->id, $linea_det->um); ?>
						<?php endif; ?>
					</td>
					<td class="ac"><?php echo $linea_det->hu; ?></td>
					<td><?php echo $linea_det->catalogo; ?></td>
					<td><?php echo $linea_det->get_valor_field('descripcion'); ?></td>
					<td><?php echo $linea_det->get_valor_field('lote'); ?></td>
					<td><?php echo $linea_det->get_valor_field('centro'); ?></td>
					<td><?php echo $linea_det->get_valor_field('almacen'); ?></td>
					<td><?php echo $linea_det->um; ?></td>
					<td class="ar"><?php echo number_format($linea_det->stock_sap,0,',','.'); ?></td>
					<td>
						<?php echo form_input('stock_fisico_' . $linea_det->id, set_value('stock_fisico_' . $linea_det->id, $linea_det->stock_fisico), 'class="input-mini" tabindex="' . $tab_index . '"'); ?>
						<?php echo form_error('stock_fisico_' . $linea_det->id); ?>
					</td>
					<td>
						<?php echo form_input('observacion_' . $linea_det->id, set_value('observacion_' . $linea_det->id, $linea_det->observacion), 'class="input-medium" max_length="100" tabindex="' . ($tab_index + 100) . '"'); ?>
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
				<td></td>
				<td class="ar"><strong><?php echo number_format($sum_sap,0,',','.'); ?></strong></td>
				<td class="ac"><strong><?php echo number_format($sum_fisico,0,',','.'); ?></strong></td>
				<td></td>
			</tr>
		</tbody>
	</table>
<?php echo form_close(); ?>
</div><!-- fin content-module-main-principal -->

<div>
	<a href="#" class="btn btn-primary" id="btn_guardar">
		<i class="icon-ok icon-white"></i>
		Guardar hoja
	</a>
</div> <!-- fin content-module-footer -->

<script type="text/javascript" src="<?php echo base_url(); ?>js/view_inventario.js"></script>
