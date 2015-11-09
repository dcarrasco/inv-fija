<div class="row">
	<div class="col-md-8 col-md-offset-2 well">

		<?php echo form_open('', 'class="form-horizontal" role="form"')?>

		<fieldset>
		<legend>
			<?php echo $this->lang->line('inventario_form_new'); ?>
		</legend>

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
			<label class="control-label col-xs-3">Ce</label>
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
				<?php echo $detalle_inventario->print_form_field('stock_sap'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">
				HU <?php echo $detalle_inventario->get_marca_obligatorio_field('hu'); ?>

			</label>
			<div class="col-xs-9">
				<?php echo $detalle_inventario->print_form_field('hu'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-xs-3">
				Obs <?php echo $detalle_inventario->get_marca_obligatorio_field('observacion'); ?>

			</label>
			<div class="col-xs-9">
				<?php echo $detalle_inventario->print_form_field('observacion'); ?>
			</div>
		</div>

		<div class="form-group">
			<div class="col-xs-12">
				<div class="pull-right">
					<button type="submit" name="accion" value="agregar" class="btn btn-primary">
						<span class="glyphicon glyphicon-ok"></span>
						<?php echo $this->lang->line('inventario_form_new_button_edit'); ?>
					</button>
					<a href="<?php echo site_url($this->router->class . '/ingreso/' . $detalle_inventario->hoja); ?>" class="btn btn-default">
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

<script type="text/javascript" src="<?php echo base_url(); ?>js/view_inventario.js"></script>
