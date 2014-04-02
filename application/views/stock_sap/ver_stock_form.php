<?php echo form_open(); ?>
<div class="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						Parametros consulta
					</a>
				</div>
			</div>
		</div>

		<div class="panel-collapse collapse in" id="form_param">
			<div class="panel-body">
				<div class="col-md-4">
					<div class="form-group">
						<label>Seleccionar Fechas</label>
						<div class="radio">
							<?php echo form_radio('sel_fechas', 'ultimo_dia', set_radio('sel_fechas','ultimo_dia', TRUE)); ?>
							Seleccionar ultimo dia mes
						</div>
						<div class="radio">
							<?php echo form_radio('sel_fechas', 'todas', set_radio('sel_fechas','todas')); ?>
							Seleccionar todas las fechas
						</div>
						<div id="show_fecha_ultimodia">
							<?php echo form_multiselect('fecha_ultimodia[]', $combo_fechas_ultimodia, $this->input->post('fecha_ultimodia'),'size="10" class="form-control"'); ?>
						</div>
						<div id="show_fecha_todas">
							<?php echo form_multiselect('fecha_todas[]', $combo_fechas_todas, $this->input->post('fecha_todas'),'size="10" class="form-control"'); ?>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>Seleccionar Almacenes</label>
						<div class="radio">
							<?php echo form_radio('sel_tiposalm', 'sel_tiposalm', set_radio('sel_tiposalm','sel_tiposalm', TRUE)); ?>
							Seleccionar Tipos de Almacen
						</div>
						<div class="radio">
							<?php echo form_radio('sel_tiposalm', 'sel_almacenes', set_radio('sel_tiposalm','sel_almacenes')); ?>
							Seleccionar Almacenes
						</div>
						<div id="show_tiposalm">
							<?php echo form_multiselect('tipo_alm[]', $combo_tipo_alm, $this->input->post('tipo_alm'), 'size="10" class="form-control"'); ?>
							<div>
								<div class="checkbox">
									<?php echo form_checkbox('almacen', 'almacen', set_checkbox('almacen', 'almacen')); ?> Desplegar detalle almacenes
								</div>
							</div>
						</div>
						<div id="show_almacenes">
							<?php echo form_multiselect('almacenes[]', $combo_almacenes, $this->input->post('almacenes'), 'size="10" class="form-control"'); ?>
						</div>
					</div>
				</div>

				<div class="col-md-4">
					<div class="form-group">
						<label>Seleccionar Detalle Materiales</label>

						<!--
						<div class="checkbox">
							<?php //echo form_checkbox('tipo_articulo', 'tipo_articulo', set_checkbox('tipo_articulo', 'tipo_articulo')); ?>
							Desplegar detalle tipos articulo
						</div>
						-->

						<div class="checkbox">
							<?php echo form_checkbox('material', 'material', set_checkbox('material', 'material')); ?>
							Desplegar detalle materiales
						</div>
						<div class="checkbox">
							<?php echo form_checkbox('lote', 'lote', set_checkbox('lote', 'lote')); ?>
							Desplegar detalle lotes
						</div>
						<div class="checkbox">
							<?php echo form_checkbox('tipo_stock', 'tipo_stock', set_checkbox('tipo_stock', 'tipo_stock')); ?>
							Desplegar detalle tipos stock
						</div>

						<?php if($tipo_op == 'MOVIL'): ?>
						<div>
							<div class="checkbox-inline">
								<?php echo form_checkbox('tipo_stock_equipos', 'tipo_stock_equipos', set_checkbox('tipo_stock_equipos', 'tipo_stock_equipos',TRUE))?>
								Equipos
							</div>
							<div class="checkbox-inline">
								<?php echo form_checkbox('tipo_stock_simcard', 'tipo_stock_simcard', set_checkbox('tipo_stock_simcard', 'tipo_stock_simcard',TRUE))?>
								Simcard
							</div>
							<div class="checkbox-inline">
								<?php echo form_checkbox('tipo_stock_otros', 'tipo_stock_otros', set_checkbox('tipo_stock_otros', 'tipo_stock_otros',TRUE))?>
								Otros
							</div>
						</div>
						<?php endif; ?>

					</div>


					<hr/>
					<div class="pull-right">
						<button type="submit" name="submit" class="btn btn-primary">
							<span class="glyphicon glyphicon-list-alt"></span>
							Reporte
						</button>
						<button type="submit" name="excel" value="excel" class="btn btn-default">
							<span class="glyphicon glyphicon-file"></span>
							Exportar a Excel...
						</button>
					</div>

				</div>
			</div>
		</div>
	</div>

</div>

<?php echo form_close(); ?>

<script type="text/javascript" src="<?php echo base_url(); ?>js/ver_stock_form.js"></script>
