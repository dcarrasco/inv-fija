<div>
	<?php $nlinea = 0; $sum_cant = 0; $sum_monto = 0; ?>
	<?php foreach ($reporte as $linea_detalle): ?>
	<?php if ($nlinea === 0): ?>
	<?php $acoord_x = $linea_detalle['acoord_x']; ?>
	<?php $acoord_y = $linea_detalle['acoord_y']; ?>
	<?php $cname    = $linea_detalle['cname']; ?>

	<div class="col-md-8 well form-horizontal">
		<fieldset>
			<legend>Cliente</legend>

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
				<div class="col-sm-4 col-xs-9">
					<p class="form-control-static">
						<span class="label label-default">BA</span><span class="label label-info"><?php echo $linea_detalle['XA_BROADBAND_TECHNOLOGY']; ?></span>
						<span class="label label-default">STB</span><span class="label label-info"><?php echo $linea_detalle['XA_TELEPHONE_TECHNOLOGY']; ?></span>
						<span class="label label-default">TV</span><span class="label label-info"><?php echo $linea_detalle['XA_TV_TECHNOLOGY']; ?></span>
					</p>
				</div>
			</div>

			<legend>Petici&oacute;n</legend>

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

	<div class="col-md-4">
		<div id="map_canvas" style="height: 400px"></div>
	</div>
</div>

<div class="col-md-12">
	<fieldset>
		<legend>Detalle materiales</legend>
	</fieldset>

	<table class="table table-striped table-hover table-condensed reporte">
		<thead>
			<tr>
				<th class="text-center">n</th>
				<th class="text-center">centro</th>
				<th class="text-center">cod mat</th>
				<th class="text-left">material</th>
				<th class="text-left">serie</th>
				<th class="text-center">lote</th>
				<th class="text-center">valor</th>
				<th class="text-center">unidad</th>
				<th class="text-center">cantidad</th>
				<th class="text-center">monto</th>
				<th class="text-center">mov SAP</th>
				<th class="text-center">desc mov SAP</th>
				<th class="text-center">PEP</th>
				<th class="text-center">doc SAP</th>
			</tr>
		</thead>
		<tbody>
		<?php endif; ?>

			<tr>
				<td class="text-center text-muted"><?php echo $nlinea+1; ?></td>
				<td class="text-center"><?php echo $linea_detalle['centro']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['material']; ?></td>
				<td class="text-left"><?php echo $linea_detalle['texto_material']; ?></td>
				<td class="text-left"><?php echo $linea_detalle['serie_toa']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['lote']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['valor']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['umb']; ?></td>
				<td class="text-center"><?php echo fmt_cantidad($linea_detalle['cant']); ?></td>
				<td class="text-center"><?php echo fmt_monto($linea_detalle['monto']); ?></td>
				<td class="text-center"><?php echo $linea_detalle['codigo_movimiento']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['texto_movimiento']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['elemento_pep']; ?></td>
				<td class="text-center"><?php echo $linea_detalle['documento_material']; ?></td>
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
				<th class="text-center"><?php echo fmt_cantidad($sum_cant) ?></th>
				<th class="text-center"><?php echo fmt_monto($sum_monto); ?></th>
				<th></th>
				<th></th>
				<th></th>
				<th></th>
			</tr>
		</tfoot>
	</table>
</div>

<script>
	function initMap() {
		var ubic = new google.maps.LatLng(<?php echo $acoord_y ?>, <?php echo $acoord_x ?>),
		    map = new google.maps.Map(document.getElementById('map_canvas'), {
				center: ubic,
				zoom: 14
			}),
			marker = new google.maps.Marker({
				position: ubic,
				title: '<?php echo $cname; ?>'
			});
		marker.setMap(map);
	}
</script>

<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?sensor=false&callback=initMap" defer async></script>
