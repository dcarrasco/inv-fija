{validation_errors}

<?php echo form_open('','id="frm_param" class="form-horizontal hidden-print"'); ?>
<div class="form-group">
	<label class="control-label col-md-2 col-md-offset-2">{_label_rut_retail_}</label>
	<div class="col-sm-6">
		<?php echo form_dropdown('rut_retail', $combo_retail, set_value('rut_retail'), 'class="form-control"'); ?>
	</div>
</div>
<div class="form-group">
	<label class="control-label col-md-2 col-md-offset-2">{_label_modelos_}</label>
	<div class="col-sm-6">
		<?php echo form_textarea('modelos', set_value('modelos'), 'class="form-control"'); ?>
	</div>
</div>
<div class="form-group">
	<label class="control-label col-md-2 col-md-offset-2">{_label_max_facturas_}</label>
	<div class="col-sm-6">
		<?php echo form_dropdown('max_facturas', $combo_max_facturas, set_value('max_facturas', 5), 'class="form-control"'); ?>
	</div>
</div>
<div class="form-group">
	<div class="col-md-6 col-md-offset-4">
		<button type="submit" name="btn_submit" class="btn btn-primary pull-right">
			<span class="fa fa-search"></span>
			{_label_submit_}
		</button>
	</div>
</div>
<?php echo form_close(); ?>

<div>
<?php if(count($facturas)): ?>
	<table class="table table-striped table-hover table-condensed reporte">
		<thead>
			<tr>
				<th>modelo</th>
				<th>operador</th>
				<th class="text-center">retail</th>
				<th class="text-center">cod_cliente</th>
				<?php for($i=0; $i<$this->despachos_model->limite_facturas; $i++): ?>
				<th class="text-center">factura <?php echo $i+1; ?></th>
				<?php endfor; ?>
			</tr>
		</thead>

		<tbody>
		<?php foreach($facturas as $modelo => $datos): ?>
			<tr>
				<td><?php echo $modelo; ?></td>
				<td><?php echo $datos['datos']['operador_c']; ?></td>
				<td class="text-center"><?php echo $datos['datos']['rut']; ?> <?php echo $datos['datos']['des_bodega']; ?></td>
				<td class="text-center"><?php echo $datos['datos']['cod_cliente']; ?></td>

				<?php for($i=0; $i<$this->despachos_model->limite_facturas; $i++): ?>
				<td class="text-center">
					<?php echo substr($datos['factura_'.$i]['fecha'],0,10); ?>
					<br/><?php echo $datos['factura_'.$i]['n_doc']; ?>
					<br/><?php echo $datos['factura_'.$i]['cod_sap']; ?>
					<br/><?php echo fmt_cantidad($datos['factura_'.$i]['cant']); ?>
				</td>
				<?php endfor; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>
</div> <!-- fin content-module-main -->
