<div class="row">
	<div class="col-md-8 col-md-offset-2 well" id="formulario_agregar">
		<?php echo form_open('', 'id="frm_agregar" class="form-horizontal" role="form"')?>
		<?php echo form_hidden('formulario','agregar'); ?>
		<?php echo form_hidden('accion','agregar'); ?>
		<?php echo form_hidden('hoja', $hoja); ?>
		<?php echo form_hidden('auditor', $id_auditor); ?>
		<?php echo form_hidden('id', ''); ?>

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
				<?php echo $nuevo_detalle_inventario->get_label_field('ubicacion'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('ubicacion'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $nuevo_detalle_inventario->print_form_field('ubicacion', FALSE, 'input-sm'); ?>
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

		<div class="form-group <?php echo form_has_error('catalogo') ? 'has-error' : ''; ?>">
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

		<div class="form-group <?php echo form_has_error('lote') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('lote'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('lote'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $nuevo_detalle_inventario->print_form_field('lote', FALSE, 'input-sm'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('um') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('um'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('um'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $nuevo_detalle_inventario->print_form_field('um', FALSE, 'input-sm'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('centro') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('centro'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('centro'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $nuevo_detalle_inventario->print_form_field('centro', FALSE, 'input-sm'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('almacen') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('almacen'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('almacen'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $nuevo_detalle_inventario->print_form_field('almacen', FALSE, 'input-sm'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('stock_fisico') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('stock_fisico'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('stock_fisico'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $nuevo_detalle_inventario->print_form_field('stock_fisico', FALSE, 'input-sm'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('hu') ? 'has-error' : ''; ?>">
			<label class="control-label col-sm-4">
				<?php echo $nuevo_detalle_inventario->get_label_field('hu'); ?>
				<?php echo $nuevo_detalle_inventario->get_marca_obligatorio_field('hu'); ?>
			</label>
			<div class="col-sm-8">
				<?php echo $nuevo_detalle_inventario->print_form_field('hu', FALSE, 'input-sm'); ?>
			</div>
		</div>

		<div class="form-group <?php echo form_has_error('observacion') ? 'has-error' : ''; ?>">
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
				<?php if ($reg): ?>
					<div class="pull-left">
						<a href="#" class="btn btn-danger" id="btn_borrar">
							<span class="glyphicon glyphicon-trash"></span>
							<?php echo $this->lang->line('inventario_form_new_button_delete'); ?>
						</a>
					</div>
				<?php endif ?>

				<div class="pull-right">
					<button type="submit" class="btn btn-primary" id="btn_agregar">
						<span class="glyphicon glyphicon-plus-sign"></span>
						<?php echo $this->lang->line('inventario_form_new_button_add'); ?>
					</button>
					<a href="<?php echo $link_cancelar; ?>" class="btn btn-default" id="btn_cancelar">
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


<script>
$('#agr_filtrar').bind('keypress', function (event) {
	if(event.keyCode === 13) {
		event.preventDefault();
		actualizaMateriales($('#agr_filtrar').val());
	}
});

$('#agr_filtrar').blur(function () {
	actualizaMateriales($('#agr_filtrar').val());
});

function actualizaMateriales(filtro) {
	var tt = new Date().getTime();
	var url_datos = js_base_url + 'inventario_digitacion/ajax_act_agr_materiales/' + filtro + '/' + tt;
	$.get(url_datos, function (data) {$('#agr_material').html(data); });
}


</script>
