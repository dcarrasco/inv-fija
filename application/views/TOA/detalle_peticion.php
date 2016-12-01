<div>
	<?php if ( ! $reporte): ?>
	<div class="col-md-10 col-md-offset-1 well">
		<?php echo form_open('','class="form-horizontal"'); ?>
		<fieldset>

			<legend>{_toa_consumo_peticion_legend_}</legend>

			{validation_errors}

			<div class="form-group <?php echo form_has_error('pag_desde'); ?>">
				<label class="control-label col-sm-4">{_toa_consumo_peticion_label_}</label>
				<div class="col-sm-8">
					<?php echo form_input('peticion', set_value('peticion'), 'class="form-control"'); ?>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-4">
				</label>
				<div class="col-sm-8">
					<button name="submit" type="submit" class="btn btn-primary pull-right" id="btn_buscar">
						<span class="fa fa-search"></span>
						{_toa_consumo_peticion_search_button_}
					</button>
				</div>
			</div>

		</fieldset>
		<?php echo form_close(); ?>
	</div>
	<?php else: ?>
	<?php $nlinea = 0; $sum_cant = 0; $sum_monto = 0; ?>
	<?php foreach ($reporte as $linea_detalle): ?>
	<?php if ($nlinea === 0): ?>
	<?php $acoord_x = $linea_detalle['acoord_x']; ?>
	<?php $acoord_y = $linea_detalle['acoord_y']; ?>
	<?php $cname    = $linea_detalle['cname']; ?>

	<div class="col-md-7 well form-horizontal">
		<fieldset>
			<legend>Datos petici&oacute;n</legend>

			<div class="form-group-sm">
				<label class="control-label col-sm-2 col-xs-3">ID Petici&oacute;n</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['referencia']; ?>
					</p>
				</div>
				<label class="control-label col-sm-2 col-xs-3">Fecha</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo fmt_fecha($linea_detalle['fecha']); ?>
					</p>
				</div>
			</div>

			<div class="form-group-sm">
				<label class="control-label col-sm-2 col-xs-3">RUT</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo fmt_rut($linea_detalle['customer_number']); ?>
					</p>
				</div>
				<label class="control-label col-sm-2 col-xs-3">Nombre</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['cname']; ?>
					</p>
				</div>
			</div>

			<div class="form-group-sm">
				<label class="control-label col-sm-2 col-xs-3">Tel&eacute;fono</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['cphone']; ?>
					</p>
				</div>
				<label class="control-label col-sm-2 col-xs-3">Celular</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['ccell']; ?>
					</p>
				</div>
			</div>

			<div class="form-group-sm">
				<label class="control-label col-sm-2 col-xs-3">e-mail</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['cemail']; ?>
					</p>
				</div>
				<label class="control-label col-sm-2 col-xs-3">Direcci&oacute;n</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['caddress']; ?>
					</p>
				</div>
			</div>

			<div class="form-group-sm">
				<label class="control-label col-sm-2 col-xs-3">Agencia</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['XA_ORIGINAL_AGENCY']; ?>
					</p>
				</div>
				<label class="control-label col-sm-2 col-xs-3">Ciudad</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['ccity']; ?>
					</p>
				</div>
			</div>

			<div class="form-group-sm">
				<label class="control-label col-sm-2 col-xs-3">Tecnolog&iacute;as</label>
				<div class="col-sm-10 col-xs-9">
					<p class="form-control-static">
						<span class="label label-default">BA</span><span class="label label-info"><?php echo $linea_detalle['XA_BROADBAND_TECHNOLOGY']; ?></span>
						<span class="label label-default">STB</span><span class="label label-info"><?php echo $linea_detalle['XA_TELEPHONE_TECHNOLOGY']; ?></span>
						<span class="label label-default">TV</span><span class="label label-info"><?php echo $linea_detalle['XA_TV_TECHNOLOGY']; ?></span>
					</p>
				</div>
			</div>

			<div class="form-group-sm">
				<label class="control-label col-sm-2 col-xs-3">Empresa</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['empresa']; ?>
					</p>
				</div>
				<label class="control-label col-sm-2 col-xs-3">T&eacute;cnico</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['cliente']; ?> -
						<?php echo $linea_detalle['tecnico']; ?>
					</p>
				</div>
			</div>

			<div class="form-group-sm">
				<label class="control-label col-sm-2 col-xs-3">Tipo de trabajo</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php $tt = new Tipo_trabajo_toa($linea_detalle['carta_porte']); echo $tt->mostrar_info(); ?>
					</p>
				</div>
				<label class="control-label col-sm-2 col-xs-3">Origen Peticion</label>
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<?php echo $linea_detalle['XA_CHANNEL_ORIGIN']; ?>
					</p>
				</div>
			</div>

		</fieldset>
	</div>

	<div class="col-md-5">
		<?php echo $google_maps; ?>
	</div>
</div>

<div class="col-md-12">

	<table class="table table-striped table-hover table-condensed reporte">
		<thead>
			<tr>
				<th class="text-center">item</th>
				<th class="text-center">cod mat</th>
				<th class="text-left">material</th>
				<th class="text-center">serie</th>
				<th class="text-center">centro</th>
				<th class="text-center">lote</th>
				<th class="text-center">valor</th>
				<th class="text-center">doc SAP</th>
				<th class="text-center">mov SAP</th>
				<th class="text-center">desc mov SAP</th>
				<th class="text-center">PEP</th>
				<th class="text-center">unidad</th>
				<th class="text-center">cantidad</th>
				<th class="text-center">monto</th>
			</tr>
		</thead>
		<tbody>
		<?php endif; ?>

			<tr>
				<td class="text-center text-muted"><?php echo $nlinea+1; ?></td>
				<td class="text-center"><?php echo $linea_detalle['material']; ?></td>
				<td class="text-left"><?php echo $linea_detalle['texto_material']; ?></td>
				<td class="text-center"><a href="#" class="detalle-serie" data-serie="<?php echo $linea_detalle['serie_toa']; ?>"><?php echo $linea_detalle['serie_toa']; ?></a></td>
				<td class="text-center"><?php echo $linea_detalle['centro']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['lote']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['valor']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['documento_material']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['codigo_movimiento']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['texto_movimiento']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['elemento_pep']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['umb']; ?></td>
				<td class="text-center"><?php echo fmt_cantidad($linea_detalle['cant']); ?></td>
				<td class="text-center"><?php echo fmt_monto($linea_detalle['monto']); ?></td>
			</tr>
		<?php $nlinea += 1; $sum_cant += $linea_detalle['cant']; $sum_monto += $linea_detalle['monto']; ?>
	<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
				<th class="text-center"><?php echo fmt_cantidad($sum_cant) ?></th>
				<th class="text-center"><?php echo fmt_monto($sum_monto); ?></th>
			</tr>
		</tfoot>
	</table>
	<?php endif; ?>
</div>

<?php echo form_open(site_url('stock_analisis_series'), 'id=id_detalle_serie'); ?>
<?php echo form_hidden('series', '1'); ?>
<?php echo form_hidden('show_mov', 'show'); ?>
<?php echo form_close(); ?>

<script>
$(document).ready(function () {

$('a.detalle-serie').click(function(event) {
	event.preventDefault();
	$('#id_detalle_serie > input[name="series"]').val($(this).data('serie'));
	$('#id_detalle_serie').submit();
});

});
</script>
