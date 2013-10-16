<div class="row">
	<?php echo $menu_configuracion; ?>
</div>

<?php echo form_open(); ?>
<div class="accordion">
	<div class="accordion-group">

		<div class="accordion-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						Parametros consulta
					</a>
				</div>
				<div class="col-md-4">
					<div class="pull-right">
						<button type="submit" name="submit" class="btn btn-primary">
							<i class="glyphicon glyphicon-list-alt icon-white"></i>
							Reporte
						</button>
						<button type="submit" name="excel" class="btn btn-default">
							<i class="glyphicon glyphicon-file"></i>
							Exportar a Excel...
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="accordion-body collapse in" id="form_param">
			<div class="accordion-inner">
				<div class="row">
					<div class="col-md-4">
						<div>
							<strong>Seleccionar Fechas</strong>
						</div>
						<div>
							<div class="radio">
								<?php echo form_radio('sel_fechas', 'ultimo_dia', set_radio('sel_fechas','ultimo_dia', TRUE)); ?>
								Seleccionar ultimo dia mes
							</div>
							<div class="radio">
								<?php echo form_radio('sel_fechas', 'todas', set_radio('sel_fechas','todas')); ?>
								Seleccionar todas las fechas
							</div>

							<div id="show_fecha_ultimodia">
								<?php echo form_multiselect('fecha_ultimodia[]', $combo_fechas_ultimodia, $this->input->post('fecha_ultimodia'),'size="10" class="input-large"'); ?>
							</div>
							<div id="show_fecha_todas">
								<?php echo form_multiselect('fecha_todas[]', $combo_fechas_todas, $this->input->post('fecha_todas'),'size="10" class="input-large"'); ?>
							</div>
						</div>
					</div>

					<div class="col-md-4">
						<div>
							<strong>Seleccionar detalle materiales</strong>
						</div>
						<div>
							<div class="checkbox">
								<?php echo form_checkbox('tipo_stock', 'tipo_stock', set_checkbox('tipo_stock', 'tipo_stock')); ?>
								Desplegar detalle tipos stock <br/>
							</div>
							<div class="checkbox">
								<?php echo form_checkbox('material', 'material', set_checkbox('material', 'material')); ?>
								Desplegar detalle materiales <br/>
							</div>
							<div class="checkbox">
								<?php echo form_checkbox('lote', 'lote', set_checkbox('lote', 'lote')); ?>
								Desplegar detalle lotes <br/>
							</div>
						</div>
					</div>

					<div class="col-md-4">
					</div>

				</div>
			</div>
		</div>
	</div>

</div>
<?php echo form_close(); ?>

<script type="text/javascript" src="<?php echo base_url(); ?>js/ver_stock_form.js"></script>

