<div class="content-module-main">

<div class="row">
	<div class="col-md-10 col-md-offset-1 well">

	<?php $nlinea = 0; ?>
	<?php foreach ($reporte as $linea_detalle): ?>

		<?php if ($nlinea === 0): ?>
		<fieldset>
			<legend>Detalle petici&oacute;n</legend>

			<div class="form-group">
				<label class="control-label col-sm-2">Documento Material</label>
				<div class="col-sm-4">
					<p class="form-control-static">
						<?php echo $linea_detalle['documento_material']; ?>
					</p>
				</div>
				<label class="control-label col-sm-2">Fecha</label>
				<div class="col-sm-4">
					<p class="form-control-static">
						<?php echo $linea_detalle['fecha']; ?>
					</p>
				</div>
			</div>

			<div class="form-group">
				<label class="control-label col-sm-2">Empresa</label>
				<div class="col-sm-4">
					<p class="form-control-static">
						<?php echo $linea_detalle['empresa']; ?>
					</p>
				</div>
				<label class="control-label col-sm-2">T&eacute;cnico</label>
				<div class="col-sm-4">
					<p class="form-control-static">
						<?php echo $linea_detalle['cliente']; ?> -
						<?php echo $linea_detalle['tecnico']; ?>
					</p>
				</div>
			</div>
		</fieldset>
<hr>
		<fieldset>
			<legend>Detalle materiales</legend>

			<table class="table table-striped reporte">
				<thead>
					<tr>
						<th class="text-center">n</th>
						<th class="text-center">centro</th>
						<th class="text-center">cod mat</th>
						<th class="text-left">material</th>
						<th class="text-center">lote</th>
						<th class="text-center">valor</th>
						<th class="text-center">unidad</th>
						<th class="text-center">cantidad</th>
						<th class="text-center">monto</th>
						<th class="text-center">mov SAP</th>
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
						<td class="text-center"><?php echo $linea_detalle['lote']; ?></td>
						<td class="text-center"><?php echo $linea_detalle['valor']; ?></td>
						<td class="text-center"><?php echo $linea_detalle['umb']; ?></td>
						<td class="text-center"><?php echo fmt_cantidad($linea_detalle['cant']); ?></td>
						<td class="text-center"><?php echo fmt_monto($linea_detalle['monto']); ?></td>
						<td class="text-center"><?php echo $linea_detalle['codigo_movimiento']; ?></td>
						<td class="text-center"><?php echo $linea_detalle['elemento_pep']; ?></td>
						<td class="text-center"><?php echo $linea_detalle['documento_material']; ?></td>
					</tr>
		<?php $nlinea += 1; ?>
	<?php endforeach; ?>
				</tbody>
			</table>

		</fieldset>
	</div>
</div>
</div> <!-- fin content-module-main -->
