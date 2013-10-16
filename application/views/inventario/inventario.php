<?php echo form_open('/c_inventario/ingreso', 'id="frm_buscar" class="form-inline"'); ?>
<?php echo form_hidden('formulario','buscar'); ?>
<div class="row">
	<div class="col-md-3">
		<strong>Inventario</strong>
	</div>
	<div class="col-md-3">
		<strong>Hoja</strong>
	</div>
	<div class="col-md-3">
		<strong>Auditor</strong>
	</div>
</div>
<div class="row">
	<div class="col-md-3">
		<?php echo $nombre_inventario; ?>
	</div>

	<div class="col-md-3">
		<div class="input-group">
			<input type="text" name="hoja" value="<?php echo $hoja; ?>" maxlength="10" id="id_hoja" class="form-control input-sm">
			<span class="input-group-btn">
				<a href="#" class="btn btn-default btn-sm" id="btn_buscar">
					<i class="glyphicon glyphicon-search"></i>
				</a>
				<a href="<?php echo $link_hoja_ant; ?>" class="btn btn-default btn-sm" id="btn_hoja_ant">
					<i class="glyphicon glyphicon-chevron-left"></i> Ant
				</a>
				<a href="<?php echo $link_hoja_sig; ?>" class="btn btn-default btn-sm" id="btn_hoja_sig">
					Sig	<i class="glyphicon glyphicon-chevron-right"></i>
				</a>
			</span>
		</div>
		<?php echo form_error('hoja');?>
	</div>

	<div class="col-md-3">
		<?php echo $nuevo_detalle_inventario->print_form_field('auditor'); ?>
		<?php echo form_error('auditor');?>
	</div>

	<div class="col-md-3">
		<div class="pull-right">
			<a href="#" id="btn_mostrar_agregar" class="btn btn-default">
				<i class="glyphicon glyphicon-plus-sign"></i> Nuevo material...
			</a>
		</div>
	</div>
</div>
<?php echo form_close(); ?>

<div class="row">
	<div class="col-md-8 col-md-offset-2 well" style="display: none;" id="frm_agregar">
		<?php echo form_open("c_inventario/ingreso/$hoja/".time(), 'id=frm_agregar')?>
		<?php echo form_hidden('formulario','agregar'); ?>
		<?php echo form_hidden('accion','agregar'); ?>
		<?php echo form_hidden('hoja', $hoja); ?>
		<?php echo form_hidden('auditor', $id_auditor); ?>
		<?php echo form_hidden('id', ''); ?>
			<fieldset>
				<legend>Ingreso datos inventario</legend>

				<div class="row">
					<div class="col-md-6">
						<label class="control-label">
							<?php echo $nuevo_detalle_inventario->get_label_field('ubicacion'); ?>
							<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('ubicacion'); ?>
						</label>
						<?php echo $nuevo_detalle_inventario->print_form_field('ubicacion'); ?>
						<?php echo form_error('ubicacion'); ?>
					</div>
					<!--  20130624 DCR: ELIMINA CAMPO HU
					<div class="col-md-6">
						<label class="control-label">
							<?php //echo $nuevo_detalle_inventario->get_label_field('hu'); ?>
							<?php //echo $nuevo_detalle_inventario->get_marca_obligatorio_field('hu'); ?>
						</label>
						<?php //echo nuevo_detalle_inventario->print_form_field('hu'); ?>
						<?php //echo form_error('hu'); ?>
					</div>
					-->
				</div>

				<div class="row">
					<div class="col-md-12">
						<label class="control-label">
							Material
							<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('catalogo'); ?>
						</label>
					</div>
					<div class="col-md-4">
						<div class="input-group">
							<?php echo form_input('agr_filtrar', set_value('agr_filtrar'), 'class="form-control input-sm" id="agr_filtrar"'); ?>
							<span class="input-group-addon"><i class="glyphicon glyphicon-search"></i></span>
						</div>
					</div>
					<div class="col-md-8">
						<?php echo form_dropdown('catalogo', array('' => 'Buscar y seleccionar material...'), '', 'class="form-control" id="agr_material"'); ?>
						<?php echo form_error('catalogo'); ?>
					</div>
				</div>
				<div class="row">
					<div class="col-md-6">
						<label class="control-label">
							<?php echo $nuevo_detalle_inventario->get_label_field('lote'); ?>
							<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('lote'); ?>
						</label>
						<?php echo $nuevo_detalle_inventario->print_form_field('lote'); ?>
						<?php echo form_error('lote'); ?>
					</div>
					<div class="col-md-6">
						<label class="control-label">
							<?php echo $nuevo_detalle_inventario->get_label_field('um'); ?>
							<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('um'); ?>
						</label>
						<?php echo $nuevo_detalle_inventario->print_form_field('um'); ?>
						<?php echo form_error('um'); ?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<label class="control-label">
							<?php echo $nuevo_detalle_inventario->get_label_field('centro'); ?>
							<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('centro'); ?>
						</label>
						<?php echo $nuevo_detalle_inventario->print_form_field('centro'); ?>
						<?php echo form_error('centro'); ?>
					</div>
					<div class="col-md-6">
						<label class="control-label">
							<?php echo $nuevo_detalle_inventario->get_label_field('almacen'); ?>
							<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('almacen'); ?>
						</label>
						<?php echo $nuevo_detalle_inventario->print_form_field('almacen'); ?>
						<?php echo form_error('almacen'); ?>
					</div>
				</div>

				<div class="row">
					<div class="col-md-6">
						<label class="control-label">
							<?php echo $nuevo_detalle_inventario->get_label_field('stock_fisico'); ?>
							<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('stock_fisico'); ?>
						</label>
						<?php echo $nuevo_detalle_inventario->print_form_field('stock_fisico'); ?>
						<?php echo form_error('stock_fisico'); ?>
					</div>
					<div class="col-md-6">
						<label class="control-label">
							<?php echo $nuevo_detalle_inventario->get_label_field('observacion'); ?>
							<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('observacion'); ?>
						</label>
						<?php echo $nuevo_detalle_inventario->print_form_field('observacion'); ?>
						<?php echo form_error('observacion'); ?>
					</div>
				</div>

				<div class="pull-left">
					<a href="#" class="btn btn-danger" id="btn_borrar">
						<i class="glyphicon glyphicon-trash icon-white"></i>
						Borrar
					</a>
				</div>

				<div class="pull-right">
					<a href="#" class="btn btn-primary" id="btn_agregar">
						<i class="glyphicon glyphicon-plus-sign icon-white"></i>
						Agregar material
					</a>
					<a href="#" class="btn btn-default" id="btn_cancelar">
						<i class="glyphicon glyphicon-ban-circle"></i>
						Cancelar
					</a>
				</div>

				<div class="control-group">
				</div>

				<div class="control-group">
				</div>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->
</div>

<div>
	<?php echo form_open("c_inventario/ingreso/$hoja/$id_auditor/".time(), 'id="frm_inventario"'); ?>
	<?php echo form_hidden('formulario','inventario'); ?>
	<?php echo form_hidden('hoja', $hoja); ?>
	<?php echo form_hidden('auditor', $id_auditor); ?>
	<?php //echo form_error('sel_digitador'); ?>
	<?php //echo form_error('sel_auditor'); ?>
	<table class="table table-bordered table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th>ubicacion</th>
				<!-- <th>HU</th> -->
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
					<td>
						<?php echo $linea_det->get_valor_field('ubicacion'); ?>

						<?php if ($linea_det->reg_nuevo == 'S'):?>
							<a href="#" class="btn btn-default btn-xs" id="btn_editar_id_<?php echo $linea_det->id; ?>">
								<i class="glyphicon glyphicon-edit"></i>
							</a>
							<?php echo form_hidden('ubicacion_'   . $linea_det->id, $linea_det->ubicacion); ?>
							<?php //echo form_hidden('hu_'          . $linea_det->id, $linea_det->hu); ?>
							<?php echo form_hidden('catalogo_'    . $linea_det->id, $linea_det->catalogo); ?>
							<?php echo form_hidden('descripcion_' . $linea_det->id, $linea_det->descripcion); ?>
							<?php echo form_hidden('lote_'        . $linea_det->id, $linea_det->lote); ?>
							<?php echo form_hidden('centro_'      . $linea_det->id, $linea_det->centro); ?>
							<?php echo form_hidden('almacen_'     . $linea_det->id, $linea_det->almacen); ?>
							<?php echo form_hidden('um_'          . $linea_det->id, $linea_det->um); ?>
						<?php endif; ?>
					</td>
					<!-- <td><?php //echo $linea_det->hu; ?></td> -->
					<td><?php echo $linea_det->catalogo; ?></td>
					<td><?php echo $linea_det->get_valor_field('descripcion'); ?></td>
					<td><?php echo $linea_det->get_valor_field('lote'); ?></td>
					<td><?php echo $linea_det->get_valor_field('centro'); ?></td>
					<td><?php echo $linea_det->get_valor_field('almacen'); ?></td>
					<td><?php echo $linea_det->um; ?></td>
					<td class="text-center"><?php echo number_format($linea_det->stock_sap,0,',','.'); ?></td>
					<td class="text-center">
						<?php echo form_input('stock_fisico_' . $linea_det->id, set_value('stock_fisico_' . $linea_det->id, $linea_det->stock_fisico), 'class="input-sm form-control" tabindex="' . $tab_index . '"'); ?>
						<?php echo form_error('stock_fisico_' . $linea_det->id); ?>
					</td>
					<td>
						<?php echo form_input('observacion_' . $linea_det->id, set_value('observacion_' . $linea_det->id, $linea_det->observacion), 'class="input-sm form-control" max_length="100" tabindex="' . ($tab_index + 100) . '"'); ?>
					</td>
				</tr>
				<?php $sum_sap += $linea_det->stock_sap; $sum_fisico += $linea_det->stock_fisico;?>
				<?php $tab_index += 1; ?>
			<?php endforeach; ?>

			<!-- totales -->
			<tr>
				<td colspan="2">
				</td>
				<!-- <td></td> -->
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class="text-center"><strong><?php echo number_format($sum_sap,0,',','.'); ?></strong></td>
				<td class="text-center"><strong><?php echo number_format($sum_fisico,0,',','.'); ?></strong></td>
				<td>
					<div class="text-center">
						<a href="#" class="btn btn-primary" id="btn_guardar">
							<i class="glyphicon glyphicon-ok icon-white"></i>
							Guardar hoja
						</a>
					</div>
				</td>
			</tr>
		</tbody>
	</table>
	<?php echo form_close(); ?>
</div><!-- fin content-module-main-principal -->

<script type="text/javascript" src="<?php echo base_url(); ?>js/view_inventario.js"></script>
