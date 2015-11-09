<div class="row">
	<div class="col-md-8 col-md-offset-2 well">

		<?php echo form_open('', 'class="form-horizontal" role="form"')?>

		<fieldset>
		<?php echo print_validation_errors(); ?>

		<div class="form-group">
			<label class="control-label col-xs-3">Ubic</label>
			<div class="col-xs-9">
				<p class="form-control-static"><?php echo $detalle_inventario->get_valor_field('ubicacion'); ?></p>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">Mat</label>
			<div class="col-xs-9">
				<p class="form-control-static"><?php echo $detalle_inventario->catalogo; ?> - <?php echo $detalle_inventario->get_valor_field('descripcion'); ?></p>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">Lote</label>
			<div class="col-xs-3">
				<p class="form-control-static"><?php echo $detalle_inventario->get_valor_field('lote'); ?></p>
			</div>

			<label class="control-label col-xs-3">UM</label>
			<div class="col-xs-3">
				<p class="form-control-static"><?php echo $detalle_inventario->get_valor_field('um'); ?></p>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">Centro</label>
			<div class="col-xs-3">
				<p class="form-control-static"><?php echo $detalle_inventario->get_valor_field('centro'); ?></p>
			</div>

			<label class="control-label col-xs-3">Alm</label>
			<div class="col-xs-3">
				<p class="form-control-static"><?php echo $detalle_inventario->get_valor_field('almacen'); ?></p>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">Stock SAP</label>
			<div class="col-xs-9">
				<p class="form-control-static"><?php echo $detalle_inventario->get_valor_field('stock_sap'); ?></p>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">
				Stock Fisico <?php echo $detalle_inventario->get_marca_obligatorio_field('stock_fisico'); ?>
			</label>
			<div class="col-xs-9">
				<input type="number" name="stock_fisico" value="<?php echo set_value('stock_fisico', $detalle_inventario->stock_fisico) == 0 ? '' : set_value('stock_fisico', $detalle_inventario->stock_fisico) ?>" id="id_stock_fisico" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">
				HU <?php echo $detalle_inventario->get_marca_obligatorio_field('hu'); ?>
			</label>
			<div class="col-xs-9">
				<input type="number" name="hu" value="<?php echo set_value('hu', $detalle_inventario->hu) ?>" id="id_hu" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">
				Obs <?php echo $detalle_inventario->get_marca_obligatorio_field('observacion'); ?>
			</label>
			<div class="col-xs-9">
				<input type="text" name="observacion" value="<?php echo set_value('observacion', $detalle_inventario->observacion) ?>" id="id_observacion" class="form-control">
			</div>
		</div>

		<button type="submit" name="accion" value="agregar" class="btn btn-primary col-xs-12">
			<span class="glyphicon glyphicon-ok"></span>
			<?php echo $this->lang->line('inventario_form_new_button_edit'); ?>
		</button>

		</fieldset>
		<?php echo form_close(); ?>
	</div> <!-- fin content-module-main-agregar -->
</div>

<script type="text/javascript" src="<?php echo base_url(); ?>js/view_inventario.js"></script>
