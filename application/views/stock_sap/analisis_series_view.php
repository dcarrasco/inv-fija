<div class="row-fluid">
	<?php //echo $menu_configuracion; ?>
</div>

<?php echo form_open('','id="frm_ppal"'); ?>
<div class="accordion">
	<div class="accordion-group">

		<div class="accordion-heading">
			<div class="row-fluid">
				<div class="span8">
					<a href="#form_param" class="accordion-toggle" data-toggle="collapse">
						Parametros consulta
					</a>
				</div>
				<div class="span4">
					<div class="pull-right">
						<button type="submit" name="submit" class="btn btn-primary" id="boton-submit">
							<i class="icon-list-alt icon-white"></i>
							Consultar
						</button>
						<button name="excel" class="btn" id="boton-reset">
							<i class="icon-refresh"></i>
							Limpiar
						</button>
					</div>
				</div>
			</div>
		</div>

		<div class="accordion-body collapse in" id="form_param">
			<div class="accordion-inner">
				<div class="row-fluid">
					<div class="span4">
						<div>
							<strong>Series</strong>
						</div>
						<div>
							<?php echo form_textarea(array(
									'id' => 'series',
									'name' => 'series',
									'rows' => '10',
									'cols' => '30',
									'value' => set_value('series')
								)); ?>
						</div>
					</div>

					<div class="span4">
						<div>
							<strong>Reportes</strong>
						</div>
						<div>
							<div class="checkbox">
								<?php echo form_checkbox('show_mov', 'show', set_value('show_mov'))?>
								Mostrar movimientos
							</div>
							<div class="checkbox">
								<?php echo form_checkbox('ult_mov', 'show', set_value('ult_mov'))?>
								Filtrar ultimo movimiento
							</div>
							<div class="checkbox">
								<?php echo form_checkbox('show_despachos', 'show', set_value('show_despachos'))?>
								Mostrar despachos
							</div>
							<div class="checkbox">
								<?php echo form_checkbox('show_stock_sap', 'show', set_value('show_stock_sap'))?>
								Mostrar stock SAP
							</div>
							<div class="checkbox">
								<?php echo form_checkbox('show_stock_scl', 'show', set_value('show_stock_scl'))?>
								Mostrar stock SCL
							</div>
							<div class="checkbox">
								<?php echo form_checkbox('show_trafico', 'show', set_value('show_trafico'))?>
								Mostrar trafico
								(ver <?php echo anchor('analisis_series/trafico_por_mes','detalle trafico'); ?>)
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php echo form_close(); ?>


<div class="accordion">
	<?php if (set_value('show_mov')): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<div class="row-fluid">
				<a href="#tabla_movimientos" class="accordion-toggle" data-toggle="collapse">
					Movimientos
				</a>
			</div>
		</div>

		<div class="accordion-body collapse in" id="tabla_movimientos">
			<div class="accordion-inner">
				<table class="table table-bordered table-striped table-hover table-condensed" style="white-space:nowrap;">
				<?php foreach($hist as $serie_hist): ?>
					<tr>
						<th>serie</th>
						<th>fecha entrada doc</th>
						<th>Fecha</th>
						<th>alm</th>
						<th>des_alm</th>
						<th>rec</th>
						<th>des_rec</th>
						<th>cmv</th>
						<th>desc cmv</th>
						<th>num doc</th>
						<th>pos</th>
						<th>material</th>
						<th>desc material</th>
						<th>lote</th>
						<th>cantidad</th>
						<th>usuario</th>
						<th>nom_usuario</th>
					</tr>
				<?php foreach($serie_hist as $reg_hist): ?>
					<tr>
						<td><?php echo $reg_hist['serie'] ?></td>
						<td><?php echo $reg_hist['fecha_entrada_doc'] ?></td>
						<td><?php echo $reg_hist['fec'] ?></td>
						<td><?php echo $reg_hist['alm'] ?></td>
						<td><?php echo $reg_hist['des_alm'] ?></td>
						<td><?php echo $reg_hist['rec'] ?></td>
						<td><?php echo $reg_hist['des_rec'] ?></td>
						<td><?php echo $reg_hist['cmv'] ?></td>
						<td><?php echo $reg_hist['des_cmv'] ?></td>
						<td><?php echo $reg_hist['n_doc'] ?></td>
						<td><?php echo $reg_hist['pos'] ?></td>
						<td><?php echo $reg_hist['codigo_sap'] ?></td>
						<td><?php echo $reg_hist['texto_breve_material'] ?></td>
						<td><?php echo $reg_hist['lote'] ?></td>
						<td><?php echo $reg_hist['cantidad'] ?></td>
						<td><?php echo $reg_hist['usuario'] ?></td>
						<td><?php echo $reg_hist['nom_usuario'] ?></td>
					</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if (set_value('show_despachos')): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<div class="row-fluid">
				<a href="#tabla_despachos" class="accordion-toggle" data-toggle="collapse">
					Despachos
				</a>
			</div>
		</div>

		<div class="accordion-body collapse in" id="tabla_despachos">
			<div class="accordion-inner">
				<table class="table table-bordered table-striped table-hover table-condensed" style="white-space:nowrap;">
					<tr>
						<th>serie</th>
						<th>cod_sap</th>
						<th>texto breve material</th>
						<th>lote</th>
						<th>operador</th>
						<th>fecha despacho</th>
						<th>cmv</th>
						<th>alm</th>
						<th>rec</th>
						<th>des bodega</th>
						<th>rut</th>
						<th>tipo servicio</th>
						<th>icc</th>
						<th>abonado</th>
						<th>n_doc</th>
						<th>referencia</th>
					</tr>
				<?php foreach($desp as $serie_desp): ?>
				<?php foreach($serie_desp as $reg_desp): ?>
					<tr>
						<td><?php echo $reg_desp['serie'] ?></td>
						<td><?php echo $reg_desp['cod_sap'] ?></td>
						<td><?php echo $reg_desp['texto_breve_material'] ?></td>
						<td><?php echo $reg_desp['lote'] ?></td>
						<td><?php echo $reg_desp['operador'] ?></td>
						<td><?php echo $reg_desp['fecha_desp'] ?></td>
						<td><?php echo $reg_desp['cmv'] ?></td>
						<td><?php echo $reg_desp['alm'] ?></td>
						<td><?php echo $reg_desp['rec'] ?></td>
						<td><?php echo $reg_desp['des_bodega'] ?></td>
						<td><?php echo $reg_desp['rut'] ?></td>
						<td><?php echo $reg_desp['tipo_servicio'] ?></td>
						<td><?php echo $reg_desp['icc'] ?></td>
						<td><?php echo $reg_desp['abonado'] ?></td>
						<td><?php echo $reg_desp['n_doc'] ?></td>
						<td><?php echo $reg_desp['referencia'] ?></td>
					</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if (set_value('show_stock_sap')): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<div class="row-fluid">
				<a href="#tabla_stock_sap" class="accordion-toggle" data-toggle="collapse">
					Stock SAP
				</a>
			</div>
		</div>

		<div class="accordion-body collapse in" id="tabla_stock_sap">
			<div class="accordion-inner">
				<table class="table table-bordered table-striped table-hover table-condensed" style="white-space:nowrap;">
					<tr>
						<th>fecha stock</th>
						<th>serie</th>
						<th>material</th>
						<th>des material</th>
						<th>centro</th>
						<th>almacen</th>
						<th>des_almacen</th>
						<th>lote</th>
						<th>status sistema</th>
						<th>estado stock</th>
						<th>modificado el</th>
						<th>modificado por</th>
						<th>nombre usuario</th>
					</th>
				<?php foreach($stock as $serie_stock): ?>
				<?php foreach($serie_stock as $reg_stock): ?>
					<tr>
						<td><?php echo $reg_stock['fecha'] ?></td>
						<td><?php echo $reg_stock['serie'] ?></td>
						<td><?php echo $reg_stock['material'] ?></td>
						<td><?php echo $reg_stock['des_articulo'] ?></td>
						<td><?php echo $reg_stock['centro'] ?></td>
						<td><?php echo $reg_stock['almacen'] ?></td>
						<td><?php echo $reg_stock['des_almacen'] ?></td>
						<td><?php echo $reg_stock['lote'] ?></td>
						<td><?php echo $reg_stock['status_sistema'] ?></td>
						<td><?php echo $reg_stock['estado_stock'] ?></td>
						<td><?php echo $reg_stock['modif_el'] ?></td>
						<td><?php echo $reg_stock['modificado_por'] ?></td>
						<td><?php echo $reg_stock['nom_usuario'] ?></td>
					</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if (set_value('show_stock_scl')): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<div class="row-fluid">
				<a href="#tabla_stock_scl" class="accordion-toggle" data-toggle="collapse">
					Stock SCL
				</a>
			</div>
		</div>

		<div class="accordion-body collapse in" id="tabla_stock_scl">
			<div class="accordion-inner">
				<table class="table table-bordered table-striped table-hover table-condensed" style="white-space:nowrap;">
					<tr>
						<th>fecha stock</th>
						<th>serie</th>
						<th>cod bodega</th>
						<th>des bodega</th>
						<th>tip bodega</th>
						<th>des tip bodega</th>
						<th>cod articulo</th>
						<th>des articulo</th>
						<th>tip stock</th>
						<th>des stock</th>
						<th>cod uso</th>
						<th>des uso</th>
						<th>cod estado</th>
						<th>des estado</th>
					</th>
				<?php foreach($stock_scl as $serie_stock): ?>
				<?php foreach($serie_stock as $reg_stock_scl): ?>
					<tr>
						<td><?php echo $reg_stock_scl['FECHA'] ?></td>
						<td><?php echo $reg_stock_scl['SERIE_SAP'] ?></td>
						<td><?php echo $reg_stock_scl['COD_BODEGA'] ?></td>
						<td><?php echo $reg_stock_scl['des_bodega'] ?></td>
						<td><?php echo $reg_stock_scl['TIP_BODEGA'] ?></td>
						<td><?php echo $reg_stock_scl['des_tipbodega'] ?></td>
						<td><?php echo $reg_stock_scl['COD_ARTICULO'] ?></td>
						<td><?php echo $reg_stock_scl['des_articulo'] ?></td>
						<td><?php echo $reg_stock_scl['TIP_STOCK'] ?></td>
						<td><?php echo $reg_stock_scl['desc_stock'] ?></td>
						<td><?php echo $reg_stock_scl['COD_USO'] ?></td>
						<td><?php echo $reg_stock_scl['desc_uso'] ?></td>
						<td><?php echo $reg_stock_scl['COD_ESTADO'] ?></td>
						<td><?php echo $reg_stock_scl['des_estado'] ?></td>
					</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
	<?php endif; ?>

	<?php if (set_value('show_trafico')): ?>
	<div class="accordion-group">
		<div class="accordion-heading">
			<div class="row-fluid">
				<a href="#tabla_trafico" class="accordion-toggle" data-toggle="collapse">
					Trafico
				</a>
			</div>
		</div>

		<div class="accordion-body collapse in" id="tabla_trafico">
			<div class="accordion-inner">
				<table class="table table-bordered table-striped table-hover table-condensed" style="white-space:nowrap;">
				<?php foreach($trafico as $serie_trafico): ?>
					<tr>
						<th>año</th>
						<th>mes</th>
						<th>serie</th>
						<th>celular</th>
						<th>seg entrada</th>
						<th>seg salida</th>
						<th>tipo cliente</th>
						<th>cod cliente</th>
						<th>rut cliente</th>
						<th>nombre cliente</th>
						<th>cod situacion</th>
						<th>fecha alta</th>
						<th>fecha baja</th>
						<th>causa baja</th>
					</th>
				<?php foreach($serie_trafico as $reg_trafico): ?>
					<tr>
						<td><?php echo $reg_trafico['ano'] ?></td>
						<td><?php echo $reg_trafico['mes'] ?></td>
						<td><?php echo $reg_trafico['imei'] ?></td>
						<td><?php echo $reg_trafico['celular'] ?></td>
						<td><?php echo $reg_trafico['seg_entrada'] ?></td>
						<td><?php echo $reg_trafico['seg_salida'] ?></td>
						<td><?php echo $reg_trafico['tipo'] ?></td>
						<td><?php echo $reg_trafico['cod_cliente'] ?></td>
						<td><?php echo $reg_trafico['num_ident'] ?></td>
						<td><?php echo $reg_trafico['nom_cliente'] . " " . $reg_trafico['ape1_cliente'] . " " . $reg_trafico['ape2_cliente']?></td>
						<td><?php echo $reg_trafico['cod_situacion'] ?></td>
						<td><?php echo $reg_trafico['fecha_alta'] ?></td>
						<td><?php echo $reg_trafico['fecha_baja'] ?></td>
						<td><?php echo $reg_trafico['des_causabaja'] ?></td>
					</tr>
				<?php endforeach; ?>
				<?php endforeach; ?>
				</table>
			</div>
		</div>
	</div>
	<?php endif; ?>
</div>


<script type="text/javascript">
	$(document).ready(function() {
		if ($("#series").val() != "")
		{
			$("div.cuerpo-formulario").hide();
			$("div.formulario span").toggle();
		}

		$("#boton-reset").click(function(event) {
			//event.preventDefault();
			$("#series").val("");
			$("#series").focus();
		})

		$("table tr").hover(function() {
			$(this).addClass("highlight");
		}, function() {
			$(this).removeClass("highlight");
		})

		$("div.content-header").click(function() {
			$(this).next("div.mostrar-ocultar").slideToggle("fast");
			$(this).children("span.mostrar-ocultar").toggle();
		})

	});
</script>
