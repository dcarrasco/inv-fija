<?php echo form_open($this->uri->segment(1) . '/ingreso', 'id="frm_buscar" role="form"'); ?>
<?php echo form_hidden('formulario','buscar'); ?>
<div class="row">

	<div class="col-md-5">
		<div class="form-group">
			<label>
				<?php echo $this->lang->line('inventario_inventario'); ?>
			</label>
			<p class="form-control-static"><?php echo $nombre_inventario; ?></p>
		</div>
	</div>

	<div class="col-md-2">
		<div class="form-group <?php echo form_has_error('hoja'); ?>">
			<label>
				<?php echo $this->lang->line('inventario_page'); ?>
			</label>
			<div class="input-group">
				<span class="input-group-btn">
					<a href="#" class="btn btn-default btn-sm" id="btn_buscar">
						<span class="glyphicon glyphicon-search"></span>
					</a>
				</span>
				<input type="text" name="hoja" value="<?php echo $hoja; ?>" maxlength="10" id="id_hoja" class="form-control input-sm">
				<span class="input-group-btn">
					<a href="<?php echo $link_hoja_ant; ?>" class="btn btn-default btn-sm" id="btn_hoja_ant">
						<span class="glyphicon glyphicon-chevron-left"></span>
					</a>
					<a href="<?php echo $link_hoja_sig; ?>" class="btn btn-default btn-sm" id="btn_hoja_sig">
							<span class="glyphicon glyphicon-chevron-right"></span>
					</a>
				</span>
			</div>
			<?php echo form_error('hoja');?>
		</div>
	</div>

	<div class="col-md-3">
		<div class="form-group <?php echo form_has_error('auditor'); ?>">
			<label>
				<?php echo $this->lang->line('inventario_auditor'); ?>
			</label>
			<?php echo $nuevo_detalle_inventario->print_form_field('auditor', FALSE, 'input-sm'); ?>
			<?php echo form_error('auditor');?>
		</div>
	</div>

	<div class="col-md-2">
		<div class="form-group">
			<label></label>
			<a href="#" id="btn_mostrar_agregar" class="btn btn-default pull-right class-toggle">
				<span class="glyphicon glyphicon-plus-sign"></span>
				<?php echo $this->lang->line('inventario_button_new_line'); ?>
			</a>
		</div>
	</div>
</div>
<?php echo form_close(); ?>

<div class="row">
	<div class="col-md-10 col-md-offset-1 well" style="display: none;" id="formulario_agregar">

		<?php echo form_open($this->uri->segment(1) . "/ingreso/$hoja/".time(), 'id="frm_agregar" class="form-horizontal" role="form"')?>
		<?php echo form_hidden('formulario','agregar'); ?>
		<?php echo form_hidden('accion','agregar'); ?>
		<?php echo form_hidden('hoja', $hoja); ?>
		<?php echo form_hidden('auditor', $id_auditor); ?>
		<?php echo form_hidden('id', ''); ?>

		<fieldset>
		<legend>
			<?php echo $this->lang->line('inventario_form_new'); ?>
		</legend>

		<div class="form-group <?php echo form_has_error('ubicacion'); ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('ubicacion'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('ubicacion'); ?>
			</label>
			<div class="col-sm-3">
				<?php echo $nuevo_detalle_inventario->print_form_field('ubicacion', FALSE, 'input-sm'); ?>
			</div>
			<div class="col-sm-5">
				<?php echo form_error('ubicacion'); ?>
			</div>
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

		<div class="form-group <?php echo form_has_error('catalogo'); ?>">
			<label class="control-label col-sm-4">
				<?php echo $this->lang->line('inventario_form_new_material'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('catalogo'); ?>
			</label>
			<div class="col-sm-3">
				<div class="input-group">
					<?php echo form_input('agr_filtrar', set_value('agr_filtrar'), 'class="form-control input-sm" id="agr_filtrar"'); ?>
					<span class="input-group-btn">
						<div class="btn btn-default btn-sm">
							<span class="glyphicon glyphicon-search"></span>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-5">
				<?php echo form_dropdown('catalogo', array('' => 'Buscar y seleccionar material...'), '', 'class="form-control input-sm" id="agr_material"'); ?>
				<?php echo form_error('catalogo'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('lote'); ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('lote'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('lote'); ?>
			</label>
			<div class="col-sm-3">
				<?php echo $nuevo_detalle_inventario->print_form_field('lote', FALSE, 'input-sm'); ?>
			</div>
			<div class="col-sm-5">
				<?php echo form_error('lote'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('um'); ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('um'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('um'); ?>
			</label>
			<div class="col-sm-3">
				<?php echo $nuevo_detalle_inventario->print_form_field('um', FALSE, 'input-sm'); ?>
				<?php echo form_error('um'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('centro'); ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('centro'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('centro'); ?>
			</label>
			<div class="col-sm-3">
				<?php echo $nuevo_detalle_inventario->print_form_field('centro', FALSE, 'input-sm'); ?>
				<?php echo form_error('centro'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('almacen'); ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('almacen'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('almacen'); ?>
			</label>
			<div class="col-sm-3">
				<?php echo $nuevo_detalle_inventario->print_form_field('almacen', FALSE, 'input-sm'); ?>
				<?php echo form_error('almacen'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('stock_fisico'); ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('stock_fisico'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('stock_fisico'); ?>
			</label>
			<div class="col-sm-3">
				<?php echo $nuevo_detalle_inventario->print_form_field('stock_fisico', FALSE, 'input-sm'); ?>
			</div>
			<div class="col-sm-5">
				<?php echo form_error('stock_fisico'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('hu'); ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('hu'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('hu'); ?>
			</label>
			<div class="col-sm-3">
				<?php echo $nuevo_detalle_inventario->print_form_field('hu', FALSE, 'input-sm'); ?>
			</div>
			<div class="col-sm-5">
				<?php echo form_error('hu'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('observacion'); ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('observacion'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('observacion'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $nuevo_detalle_inventario->print_form_field('observacion', FALSE, 'input-sm'); ?>
				<?php echo form_error('observacion'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-4">
			</label>
			<div class="col-sm-8">
				<div class="pull-left">
					<a href="#" class="btn btn-danger" id="btn_borrar">
						<span class="glyphicon glyphicon-trash"></span>
						<?php echo $this->lang->line('inventario_form_new_button_delete'); ?>
					</a>
				</div>

				<div class="pull-right">
					<a href="#" class="btn btn-primary" id="btn_agregar">
						<span class="glyphicon glyphicon-plus-sign"></span>
						<?php echo $this->lang->line('inventario_form_new_button_add'); ?>
					</a>
					<a href="#" class="btn btn-default" id="btn_cancelar">
						<span class="glyphicon glyphicon-ban-circle"></span>
						<?php echo $this->lang->line('inventario_form_new_button_cancel'); ?>
					</a>
				</div>
			</div>
		</div>
		</fieldset>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->
</div>

<div>
	<?php echo form_open($this->uri->segment(1) . "/ingreso/$hoja/$id_auditor/".time(), 'id="frm_inventario"'); ?>
	<?php echo form_hidden('formulario','inventario'); ?>
	<?php echo form_hidden('hoja', $hoja); ?>
	<?php echo form_hidden('auditor', $id_auditor); ?>
	<?php //echo form_error('sel_digitador'); ?>
	<?php //echo form_error('sel_auditor'); ?>
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_ubicacion'); ?>
				</th>
				<!-- <th>HU</th> -->
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_material'); ?>
				</th>
				<th>
					<?php echo $this->lang->line('inventario_digit_th_descripcion'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_lote'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_centro'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_almacen'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_UM'); ?>
				</th>
				<th class="text-right" nowrap>
					<?php echo $this->lang->line('inventario_digit_th_cant_sap'); ?>
				</th>
				<th class="text-right">
					<?php echo $this->lang->line('inventario_digit_th_cant_fisica'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_HU'); ?>
				</th>
				<th class="text-center">
					<?php echo $this->lang->line('inventario_digit_th_observacion'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<?php $sum_sap = 0; $sum_fisico = 0;?>
			<?php $tab_index = 10; ?>
			<?php foreach ($detalle_inventario->get_model_all() as $linea_det): ?>
				<tr>
					<td class="text-center" nowrap>
						<?php echo $linea_det->get_valor_field('ubicacion'); ?>

						<?php if ($linea_det->reg_nuevo == 'S'):?>
							<a href="#" class="btn btn-default btn-xs" id="btn_editar_id_<?php echo $linea_det->id; ?>">
								<span class="glyphicon glyphicon-edit"></span>
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
					<td class="text-center"><?php echo $linea_det->catalogo; ?></td>
					<td><?php echo $linea_det->get_valor_field('descripcion'); ?></td>
					<td class="text-center"><?php echo $linea_det->get_valor_field('lote'); ?></td>
					<td class="text-center"><?php echo $linea_det->get_valor_field('centro'); ?></td>
					<td class="text-center"><?php echo $linea_det->get_valor_field('almacen'); ?></td>
					<td class="text-center"><?php echo $linea_det->um; ?></td>
					<td class="text-right"><?php echo fmt_cantidad($linea_det->stock_sap); ?></td>
					<td class="text-center col-md-1">
						<?php echo form_input('stock_fisico_' . $linea_det->id, set_value('stock_fisico_' . $linea_det->id, $linea_det->stock_fisico), 'class="input-sm form-control text-right" tabindex="' . $tab_index . '"'); ?>
						<?php echo form_error('stock_fisico_' . $linea_det->id); ?>
					</td>
					<td class="text-center col-md-1">
						<?php echo form_input('hu_' . $linea_det->id, set_value('hu_' . $linea_det->id, $linea_det->hu), 'class="input-sm form-control" tabindex="' . $tab_index . '"'); ?>
						<?php echo form_error('hu_' . $linea_det->id); ?>
					</td>
					<td class="text-center">
						<?php echo form_input('observacion_' . $linea_det->id, set_value('observacion_' . $linea_det->id, $linea_det->observacion), 'class="input-sm form-control" maxlength="100" tabindex="' . ($tab_index + 100) . '"'); ?>
					</td>
				</tr>
				<?php $sum_sap += $linea_det->stock_sap; $sum_fisico += $linea_det->stock_fisico;?>
				<?php $tab_index += 1; ?>
			<?php endforeach; ?>
		</tbody>

		<!-- totales -->
		<tfoot>
			<tr>
				<td colspan="2">
				</td>
				<!-- <td></td> -->
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td class="text-right"><strong><?php echo fmt_cantidad($sum_sap); ?></strong></td>
				<td class="text-right"><strong><?php echo fmt_cantidad($sum_fisico); ?></strong></td>
				<td></td>
				<td>
					<div class="text-center">
						<a href="#" class="btn btn-primary" id="btn_guardar">
							<span class="glyphicon glyphicon-ok"></span>
							<?php echo $this->lang->line('inventario_digit_button_save_page'); ?>
						</a>
					</div>
				</td>
			</tr>
		</tfoot>

	</table>
	<?php echo form_close(); ?>
</div><!-- fin content-module-main-principal -->

<script type="text/javascript" src="<?php echo base_url(); ?>js/view_inventario.js"></script>
