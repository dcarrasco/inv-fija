<div class="row-fluid">
	<?php echo $menu_configuracion; ?>
</div>

<?php echo form_open(); ?>
<div class="row-fluid">
	<div class="span3">
		<div>
			Seleccionar Fechas
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
				<?php echo form_multiselect('fecha_ultimodia[]', $combo_fechas_ultimodia, $this->input->post('fecha_ultimodia'),'class="input-large"'); ?>
			</div>
			<div id="show_fecha_todas">
				<?php echo form_multiselect('fecha_todas[]', $combo_fechas_todas, $this->input->post('fecha_todas'),'size="10" class="input-large"'); ?>
			</div>
		</div>
	</div>

	<div class="span4">
		<div>
			Seleccionar Almacenes
		</div>
		<div>
			<div class="radio">
				<?php echo form_radio('sel_tiposalm', 'sel_tiposalm', set_radio('sel_tiposalm','sel_tiposalm', TRUE)); ?>
				Seleccionar Tipos de Almacen
			</div>
			<div class="radio">
				<?php echo form_radio('sel_tiposalm', 'sel_almacenes', set_radio('sel_tiposalm','sel_almacenes')); ?>
				Seleccionar Almacenes
			</div>

			<div id="show_tiposalm">
				<?php echo form_multiselect('tipo_alm[]', $combo_tipo_alm, $this->input->post('tipo_alm'), 'size="10" class="input-xlarge"'); ?>
				<div class="checkbox">
					<?php echo form_checkbox('almacen', 'almacen', set_checkbox('almacen', 'almacen')); ?> Desplegar detalle almacenes
				</div>
			</div>
			<div id="show_almacenes">
				<?php echo form_multiselect('almacenes[]', $combo_almacenes, $this->input->post('almacenes'), 'size="10" class="input-xlarge"'); ?>
			</div>
		</div>
	</div>

	<div class="span4">
		<div>
			Seleccionar Detalle Materiales
		</div>

		<div>
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
				<div class="checkbox">
					<?php echo form_checkbox('tipo_stock_equipos', 'tipo_stock_equipos', set_checkbox('tipo_stock_equipos', 'tipo_stock_equipos',TRUE))?>
					Equipos
				</div>
				<div class="checkbox">
					<?php echo form_checkbox('tipo_stock_simcard', 'tipo_stock_simcard', set_checkbox('tipo_stock_simcard', 'tipo_stock_simcard',TRUE))?>
					Simcard
				</div>
				<div class="checkbox">
					<?php echo form_checkbox('tipo_stock_otros', 'tipo_stock_otros', set_checkbox('tipo_stock_otros', 'tipo_stock_otros',TRUE))?>
					Otros
				</div>
			<?php endif; ?>
		</div>

		<div>
			<button type="submit" name="submit" class="btn btn-primary">
				<i class="icon-list-alt icon-white"></i>
				Reporte
			</button>
		</div>
		<div>
			<button type="submit" name="excel" class="btn">
				<i class="icon-file"></i>
				Exportar a Excel...
			</button>
		</div>
	</div>

	<div class="span1">
		<div class="mostrar-ocultar fr"><span>Ocultar</span></div>
	</div>
</div>
<?php echo form_close(); ?>

<script type="text/javascript" src="<?php echo base_url(); ?>js/ver_stock_form.js"></script>
