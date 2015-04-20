<div class="row">
	<div class="col-md-8 col-md-offset-2 well">

		<?php echo form_open('', 'class="form-horizontal" role="form"')?>

		<fieldset>
		<legend>
			<?php echo $this->lang->line('inventario_form_new'); ?>
		</legend>

		<?php if (validation_errors()): ?>
			<div class="alert alert-danger">
				<ul>
					<?php echo validation_errors(); ?>
				</ul>
			</div>
		<?php endif; ?>

		<div class="form-group <?php echo form_has_error('ubicacion') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $detalle_inventario->get_label_field('ubicacion'); ?>
				<?php echo $detalle_inventario->get_marca_obligatorio_field('ubicacion'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $detalle_inventario->print_form_field('ubicacion'); ?>
			</div>
		</div>

		<!--  20130624 DCR: ELIMINA CAMPO HU
		<div class="col-md-6">
			<label class="control-label">
				<?php //echo $detalle_inventario->get_label_field('hu'); ?>
				<?php //echo $detalle_inventario->get_marca_obligatorio_field('hu'); ?>
			</label>
			<?php //echo detalle_inventario->print_form_field('hu'); ?>
			<?php //echo form_error('hu'); ?>
		</div>
		-->

		<div class="form-group <?php echo form_has_error('catalogo') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $this->lang->line('inventario_form_new_material'); ?>
				<?php echo $detalle_inventario->get_marca_obligatorio_field('catalogo'); ?>
			</label>
			<div class="col-sm-3">
				<div class="input-group">
					<?php echo form_input('agr_filtrar', set_value('agr_filtrar'), 'class="form-control" id="agr_filtrar"'); ?>
					<span class="input-group-btn">
						<div class="btn btn-default">
							<span class="glyphicon glyphicon-search"></span>
						</div>
					</span>
				</div>
			</div>
			<div class="col-md-5">
				<?php echo form_dropdown('catalogo', $arr_catalogo, '', 'class="form-control" id="agr_material"'); ?>
				<?php echo form_error('catalogo'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('lote') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $detalle_inventario->get_label_field('lote'); ?>
				<?php echo $detalle_inventario->get_marca_obligatorio_field('lote'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $detalle_inventario->print_form_field('lote'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('um') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $detalle_inventario->get_label_field('um'); ?>
				<?php echo $detalle_inventario->get_marca_obligatorio_field('um'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $detalle_inventario->print_form_field('um'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('centro') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $detalle_inventario->get_label_field('centro'); ?>
				<?php echo $detalle_inventario->get_marca_obligatorio_field('centro'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $detalle_inventario->print_form_field('centro'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('almacen') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $detalle_inventario->get_label_field('almacen'); ?>
				<?php echo $detalle_inventario->get_marca_obligatorio_field('almacen'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $detalle_inventario->print_form_field('almacen'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('stock_fisico') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $detalle_inventario->get_label_field('stock_fisico'); ?>
				<?php echo $detalle_inventario->get_marca_obligatorio_field('stock_fisico'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $detalle_inventario->print_form_field('stock_fisico'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('hu') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $detalle_inventario->get_label_field('hu'); ?>
				<?php echo $detalle_inventario->get_marca_obligatorio_field('hu'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $detalle_inventario->print_form_field('hu'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('observacion') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $detalle_inventario->get_label_field('observacion'); ?>
				<?php echo $detalle_inventario->get_marca_obligatorio_field('observacion'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $detalle_inventario->print_form_field('observacion'); ?>
				<?php echo form_error('observacion'); ?>
			</div>
		</div>

		<div class="form-group">
			<label class="control-label col-sm-4">
			</label>
			<div class="col-sm-8">

				<?php if ($id): ?>
				<div class="pull-left">
					<button type="submit" name="accion" value="borrar" class="btn btn-danger">
						<span class="glyphicon glyphicon-trash"></span>
						<?php echo $this->lang->line('inventario_form_new_button_delete'); ?>
					</button>
				</div>
				<?php endif ?>

				<div class="pull-right">
					<button type="submit" name="accion" value="agregar" class="btn btn-primary">
						<span class="glyphicon glyphicon-plus-sign"></span>
						<?php echo $this->lang->line('inventario_form_new_button_add'); ?>
					</button>
					<a href="<?php echo site_url($this->router->class . '/ingreso/' . $hoja); ?>" class="btn btn-default">
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