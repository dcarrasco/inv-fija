<?php echo form_open($this->router->class.'/ingreso', 'id="frm_buscar" role="form"'); ?>
<?php echo form_hidden('formulario','buscar'); ?>
<div class="row">

	<div class="col-xs-6">
		<div class="form-group">
			<label>{_inventario_inventario_}</label>
			<p class="form-control-static">{nombre_inventario}</p>
		</div>
	</div>

	<div class="col-xs-6">
		<div class="form-group <?php echo form_has_error('hoja'); ?>">
			<label>{_inventario_page_}</label>
			<div class="input-group">
				<span class="input-group-btn">
					<a href="#" class="btn btn-default btn-sm" id="btn_buscar">
						<span class="glyphicon glyphicon-search"></span>
					</a>
				</span>
				<input type="text" name="hoja" value="{hoja}" maxlength="10" id="id_hoja" class="form-control input-sm">
				<span class="input-group-btn">
					<a href="{link_hoja_ant}" class="btn btn-default btn-sm" id="btn_hoja_ant">
						<span class="glyphicon glyphicon-chevron-left"></span>
					</a>
					<a href="{link_hoja_sig}" class="btn btn-default btn-sm" id="btn_hoja_sig">
						<span class="glyphicon glyphicon-chevron-right"></span>
					</a>
				</span>
			</div>
			<?php echo form_error('hoja');?>
		</div>
	</div>
</div>
<?php echo form_close(); ?>



<div id="formulario_digitador">
	<?php echo form_open($this->router->class . "/ingreso/$hoja/$id_auditor/".time(), 'id="frm_inventario"'); ?>
	<?php echo form_hidden('formulario','inventario'); ?>
	<?php echo form_hidden('hoja', $hoja); ?>
	<?php echo form_hidden('auditor', $id_auditor); ?>
	<table class="table table-striped table-hover table-condensed">
		<thead>
			<tr>
				<th class="text-center">{_inventario_digit_th_ubicacion_}</th>
				<th class="text-center">{_inventario_digit_th_material_}</th>
				<th class="text-left">{_inventario_digit_th_descripcion_}</th>
				<th class="text-right">{_inventario_digit_th_cant_sap_}</th>
				<th class="text-right">{_inventario_digit_th_cant_fisica_}</th>
			</tr>
		</thead>
		<tbody>
			<?php $sum_sap = 0; $sum_fisico = 0;?>
			<?php $tab_index = 10; ?>
			<?php foreach ($detalle_inventario as $linea_det): ?>
				<tr>
					<td class="text-center" nowrap><?php echo anchor('inventario_digitacion/ingreso_mobile/'.$hoja.'/'.$linea_det->id,$linea_det->get_valor_field('ubicacion')); ?></td>
					<td class="text-center"><?php echo $linea_det->catalogo; ?></td>
					<td><?php echo $linea_det->get_valor_field('descripcion'); ?></td>
					<td class="text-right"><?php echo fmt_cantidad($linea_det->stock_sap); ?></td>
					<td class="text-right"><?php echo fmt_cantidad($linea_det->stock_fisico); ?></td>
				</tr>
				<?php $sum_sap += $linea_det->stock_sap; $sum_fisico += $linea_det->stock_fisico;?>
				<?php $tab_index += 1; ?>
			<?php endforeach; ?>
		</tbody>

		<!-- totales -->
		<tfoot>
			<tr>
				<td></td>
				<td></td>
				<td></td>
				<td class="text-right"><strong><?php echo fmt_cantidad($sum_sap); ?></strong></td>
				<td class="text-right"><strong><?php echo fmt_cantidad($sum_fisico); ?></strong></td>
			</tr>
		</tfoot>

	</table>
	<?php echo form_close(); ?>
</div><!-- fin content-module-main-principal -->

<div class="row">
	<div class="col-xs-12">
		<div class="form-group">
			<label>&nbsp;</label>
			<div class="input-group">
				<a href="<?php echo site_url($this->router->class.'/editar/'.$hoja.'/'.$id_auditor) ?>" id="btn_mostrar_agregar" class="btn btn-default pull-right">
					<span class="glyphicon glyphicon-plus-sign"></span>
					{_inventario_button_new_line_}
				</a>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="{base_url}js/view_inventario.js"></script>
