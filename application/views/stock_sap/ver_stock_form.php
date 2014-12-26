<?php echo form_open(); ?>
<div class="accordion">
	<div class="panel panel-default">

		<div class="panel-heading">
			<div class="row">
				<div class="col-md-8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						<?php echo $this->lang->line('stock_sap_panel_params'); ?>
					</a>
				</div>
			</div>
		</div>

		<div class="panel-collapse collapse in" id="form_param">
			<div class="panel-body">
				<div class="col-md-4">
					<div class="form-group">
						<label>
							<?php echo $this->lang->line('stock_sap_label_dates'); ?>
						</label>
						<div class="radio">
							<?php echo form_radio('sel_fechas', 'ultimo_dia', set_radio('sel_fechas','ultimo_dia', TRUE)); ?>
							<?php echo $this->lang->line('stock_sap_radio_date1'); ?>
						</div>
						<div class="radio">
							<?php echo form_radio('sel_fechas', 'todas', set_radio('sel_fechas','todas')); ?>
							<?php echo $this->lang->line('stock_sap_radio_date2'); ?>
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
						<label>
							<?php echo $this->lang->line('stock_sap_label_alm'); ?>
						</label>
						<div class="radio">
							<?php echo form_radio('sel_tiposalm', 'sel_tiposalm', set_radio('sel_tiposalm','sel_tiposalm', TRUE)); ?>
							<?php echo $this->lang->line('stock_sap_radio_alm1'); ?>
						</div>
						<div class="radio">
							<?php echo form_radio('sel_tiposalm', 'sel_almacenes', set_radio('sel_tiposalm','sel_almacenes')); ?>
							<?php echo $this->lang->line('stock_sap_radio_alm2'); ?>
						</div>
						<div id="show_tiposalm">
							<?php echo form_multiselect('tipo_alm[]', $combo_tipo_alm, $this->input->post('tipo_alm'), 'size="10" class="form-control"'); ?>
							<div>
								<div class="checkbox">
									<?php echo form_checkbox('almacen', 'almacen', set_checkbox('almacen', 'almacen')); ?>
									<?php echo $this->lang->line('stock_sap_check_show_alm'); ?>
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
						<label>
							<?php echo $this->lang->line('stock_sap_label_mats'); ?>
						</label>

						<!--
						<div class="checkbox">
							<?php //echo form_checkbox('tipo_articulo', 'tipo_articulo', set_checkbox('tipo_articulo', 'tipo_articulo')); ?>
							Desplegar detalle tipos articulo
						</div>
						-->

						<div class="checkbox">
							<?php echo form_checkbox('material', 'material', set_checkbox('material', 'material')); ?>
							<?php echo $this->lang->line('stock_sap_check_mat'); ?>
						</div>
						<div class="checkbox">
							<?php echo form_checkbox('lote', 'lote', set_checkbox('lote', 'lote')); ?>
							<?php echo $this->lang->line('stock_sap_check_lotes'); ?>
						</div>
						<div class="checkbox">
							<?php echo form_checkbox('tipo_stock', 'tipo_stock', set_checkbox('tipo_stock', 'tipo_stock')); ?>
							<?php echo $this->lang->line('stock_sap_check_tipstock'); ?>
						</div>

						<?php if($tipo_op == 'MOVIL'): ?>
						<div>
							<div class="checkbox-inline">
								<?php echo form_checkbox('tipo_stock_equipos', 'tipo_stock_equipos', set_checkbox('tipo_stock_equipos', 'tipo_stock_equipos',TRUE))?>
								<?php echo $this->lang->line('stock_sap_radio_equipos'); ?>
							</div>
							<div class="checkbox-inline">
								<?php echo form_checkbox('tipo_stock_simcard', 'tipo_stock_simcard', set_checkbox('tipo_stock_simcard', 'tipo_stock_simcard',TRUE))?>
								<?php echo $this->lang->line('stock_sap_radio_sim'); ?>
							</div>
							<div class="checkbox-inline">
								<?php echo form_checkbox('tipo_stock_otros', 'tipo_stock_otros', set_checkbox('tipo_stock_otros', 'tipo_stock_otros',TRUE))?>
								<?php echo $this->lang->line('stock_sap_radio_otros'); ?>
							</div>
						</div>
						<?php endif; ?>
					</div>


					<hr/>
					<div class="form-group">
						<?php echo $this->lang->line('stock_sap_label_mostrar'); ?>
						<div class="radio-inline">
							<?php echo form_radio('mostrar_cant_monto', 'cantidad', TRUE); ?>
							<?php echo $this->lang->line('stock_sap_radio_cant'); ?>
						</div>
						<div class="radio-inline">
							<?php echo form_radio('mostrar_cant_monto', 'monto'); ?>
							<?php echo $this->lang->line('stock_sap_radio_monto'); ?>
						</div>
					</div>

					<hr/>
					<div class="pull-right">
						<button type="submit" name="submit" class="btn btn-primary">
							<span class="glyphicon glyphicon-list-alt"></span>
							<?php echo $this->lang->line('stock_sap_button_report'); ?>
						</button>
						<button type="submit" name="excel" value="excel" class="btn btn-default">
							<span class="glyphicon glyphicon-file"></span>
							<?php echo $this->lang->line('stock_sap_button_export'); ?>
						</button>
					</div>

				</div>
			</div>
		</div>
	</div>

</div>

<?php echo form_close(); ?>

<script type="text/javascript" src="<?php echo base_url(); ?>js/ver_stock_form.js"></script>
