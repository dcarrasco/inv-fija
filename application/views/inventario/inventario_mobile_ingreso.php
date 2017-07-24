<div class="row">
	<div class="col-md-8 col-md-offset-2 well">

		<?= form_open('', 'class="form-horizontal" role="form"')?>

		<fieldset>

		{validation_errors}

		<div class="row">
			<p class="col-xs-3"><strong>{_inventario_digit_th_ubicacion_}</strong></p>
			<p class="col-xs-9"><?= $detalle_inventario->get_field_value('ubicacion'); ?></p>
		</div>

		<div class="row">
			<p class="col-xs-3"><strong>{_inventario_digit_th_material_}</strong></p>
			<p class="col-xs-9">
				<?= $detalle_inventario->catalogo; ?> <br>
				<?= $detalle_inventario->get_field_value('descripcion'); ?>
			</p>
		</div>

		<div class="row">
			<p class="col-xs-3"><strong>{_inventario_digit_th_lote_}</strong></p>
			<p class="col-xs-3"><?= $detalle_inventario->get_field_value('lote'); ?></p>

			<p class="col-xs-3"><strong>{_inventario_digit_th_UM_}</strong></p>
			<p class="col-xs-3"><?= $detalle_inventario->get_field_value('um'); ?></p>
		</div>

		<div class="row">
			<p class="col-xs-3"><strong>{_inventario_digit_th_centro_}</strong></p>
			<p class="col-xs-3"><?= $detalle_inventario->get_field_value('centro'); ?></p>

			<p class="col-xs-3"><strong>{_inventario_digit_th_almacen_}</strong></p>
			<p class="col-xs-3"><?= $detalle_inventario->get_field_value('almacen'); ?></p>
		</div>

		<div class="row">
			<p class="col-xs-3"><strong>{_inventario_digit_th_cant_sap_}</strong></p>
			<p class="col-xs-9"><?= $detalle_inventario->get_field_value('stock_sap'); ?></p>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">
				{_inventario_digit_th_cant_fisica_}
				<?= $detalle_inventario->get_field_marca_obligatorio('stock_fisico'); ?>
			</label>
			<div class="col-xs-9">
				<?= form_number('stock_fisico', request('stock_fisico', ($detalle_inventario->stock_fisico === 0) ? '' : $detalle_inventario->stock_fisico), 'id="id_stock_fisico" class="form-control"'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">
				{_inventario_digit_th_HU_}
				<?= $detalle_inventario->get_field_marca_obligatorio('hu'); ?>
			</label>
			<div class="col-xs-9">
				<?= form_number('hu', request('hu', $detalle_inventario->hu), 'id="id_hu" class="form-control"'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">
				{_inventario_digit_th_observacion_}
				<?= $detalle_inventario->get_field_marca_obligatorio('observacion'); ?>
			</label>
			<div class="col-xs-9">
				<?= form_input('observacion', request('observacion', $detalle_inventario->observacion), 'id="id_observacion" class="form-control"'); ?>
			</div>
		</div>

		<button type="submit" name="accion" value="agregar" class="btn btn-primary col-xs-12">
			<span class="fa fa-check"></span>
			{_inventario_form_new_button_edit_}
		</button>

		</fieldset>
		<?= form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->
</div>

<script type="text/javascript" src="{base_url}js/view_inventario.js"></script>
