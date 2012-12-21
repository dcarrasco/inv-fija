
<div class="content-module">

	<div class="content-module-heading cf">

<?php echo $menu_configuracion; ?>

		<?php echo form_open(); ?>
		<table>
			<tr>
				<th>Seleccionar Fechas</th>
				<th>
					Seleccionar detalle materiales
					<div class="mostrar-ocultar fr"><span>Ocultar</span></div>
				</th>
			</tr>
			<tr>
				<th>
					<?php echo form_radio('sel_fechas', 'ultimo_dia', set_radio('sel_fechas','ultimo_dia', TRUE)); ?>
					Seleccionar ultimo dia mes <br/>
					<?php echo form_radio('sel_fechas', 'todas', set_radio('sel_fechas','todas')); ?>
					Seleccionar todas las fechas <br/>

					<div id="show_fecha_ultimodia">
						<?php echo form_multiselect('fecha_ultimodia[]', $combo_fechas_ultimodia, set_value('fecha_ultimodia'),'size="10" class="form_edit round"'); ?>
					</div>
					<div id="show_fecha_todas">
						<?php echo form_multiselect('fecha_todas[]', $combo_fechas_todas, set_value('fecha_todas'),'size="10" class="form_edit round"'); ?>
					</div>
				</th>
				<th>
					<?php echo form_checkbox('tipo_stock', 'tipo_stock', set_checkbox('tipo_stock', 'tipo_stock')); ?> Desplegar detalle tipos stock <br/>
					<?php if($tipo_op == 'MOVIL'): ?>
						<?php echo '&nbsp;&nbsp;&nbsp;&nbsp;' ?>
						<?php echo form_checkbox('tipo_stock_equipos', 'tipo_stock_equipos', set_checkbox('tipo_stock_equipos', 'tipo_stock_equipos',TRUE))?> Equipos<br/>
						<?php echo '&nbsp;&nbsp;&nbsp;&nbsp;' ?>
						<?php echo form_checkbox('tipo_stock_simcard', 'tipo_stock_simcard', set_checkbox('tipo_stock_simcard', 'tipo_stock_simcard',TRUE))?> Simcard<br/>
						<?php echo '&nbsp;&nbsp;&nbsp;&nbsp;' ?>
						<?php echo form_checkbox('tipo_stock_otros', 'tipo_stock_otros', set_checkbox('tipo_stock_otros', 'tipo_stock_otros',TRUE))?> Otros<br/>
					<?php endif; ?>
					<?php //echo form_checkbox('tipo_articulo', 'tipo_articulo', set_checkbox('tipo_articulo', 'tipo_articulo')); ?> <!-- Desplegar detalle tipos articulo <br/> -->
					<?php echo form_checkbox('material', 'material', set_checkbox('material', 'material')); ?> Desplegar detalle materiales <br/>
					<?php echo form_checkbox('lote', 'lote', set_checkbox('lote', 'lote')); ?> Desplegar detalle lotes <br/>
					<br>
					<hr>
					<br>
					<div class="fr">
						<?php echo form_submit('excel', 'Exportar a Excel...', 'class="button b-active round ic-reporte"'); ?><br>
					</div>
					<div class="fr">
						<?php echo form_submit('submit', 'Reporte', 'class="button b-active round ic-reporte"'); ?><br/>
					</div>
					<!--
					<div class="ar">
						<a id="boton-submit" class="button b-active round ic-reporte" href="#">Reporte</a><br><br>
					</div>
					<br>
					<div class="ar">
						<a id="boton-reset" class="button b-active round ic-exportar" href="#">Exportar a Excel...</a>
					</div>
					-->
				</th>
			</tr>
		</table>
		<?php echo form_close(); ?>
		<br/>
		<script type="text/javascript" src="<?php echo base_url(); ?>js/ver_stock_form.js"></script>

	</div> <!-- fin content-module-heading -->