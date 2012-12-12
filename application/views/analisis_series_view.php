
<div class="content-module">

	<div class="content-module-heading cf">

		<div class="content-header formulario">
			<h3>Parametros consulta</h3>
			<span class="mostrar-ocultar">click para cerrar</span>
			<span class="mostrar-ocultar" style="display:none;">click para expandir</span>
			<div style="clear: both;"></div>
		</div> <!-- fin content-header -->
		<div id="area_formulario" class="cuerpo-formulario mostrar-ocultar">
		<?php echo form_open(); ?>
		<table>
			<tr>
				<th>
					Series: <br />
					<?php echo form_textarea(array(
							'id' => 'series',
							'name' => 'series',
							'rows' => '10',
							'cols' => '30',
							'value' => set_value('series')
						)); ?>
				</th>
				<th>
					<?php echo form_checkbox('show_mov', 'show', set_value('show_mov'))?> Mostrar movimientos<br />
					&nbsp;&nbsp;&nbsp;&nbsp;
					<?php echo form_checkbox('ult_mov', 'show', set_value('ult_mov'))?> Filtrar ultimo movimiento<br />
					<?php echo form_checkbox('show_despachos', 'show', set_value('show_despachos'))?> Mostrar despachos<br />
					<?php echo form_checkbox('show_stock_sap', 'show', set_value('show_stock_sap'))?> Mostrar stock SAP<br />
					<?php echo form_checkbox('show_stock_scl', 'show', set_value('show_stock_scl'))?> Mostrar stock SCL<br />
					<?php echo form_checkbox('show_trafico', 'show', set_value('show_trafico'))?> Mostrar trafico
						(ver <?php echo anchor('analisis_series/trafico_por_mes','detalle trafico'); ?>)
						<br />
						<br />
						<br />
					<?php echo anchor('#','Consulta','id="boton-submit" class="button b-active round ic-ok"'); ?>
					<?php echo anchor('#','Limpiar','id="boton-reset" class="button b-active round ic-reset"'); ?>
				</th>
			</tr>
		</table>
		<?php echo form_close(); ?>
		</div> <!-- fin cuerpo-formulario -->

	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">

</div>

<?php if (set_value('show_mov')): ?>
<div>
	<div class="content-header movimientos">
		<h3>Movimientos</h3>
		<span class="mostrar-ocultar">click para cerrar</span>
		<span class="mostrar-ocultar" style="display:none;">click para expandir</span>
		<div style="clear: both;"></div>
	</div> <!-- fin content-header -->
	<div id="res_movimientos" class="cuerpo-movimientos mostrar-ocultar">
		<table>
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
		<div style="clear: both;"></div>
	</div> <!-- fin cuerpo -->
</div>
<br />
<?php endif; ?>

<?php if (set_value('show_despachos')): ?>
<div>
	<div class="content-header despachos">
		<h3>Despachos</h3>
		<span class="mostrar-ocultar">click para cerrar</span>
		<span class="mostrar-ocultar" style="display:none;">click para expandir</span>
		<div style="clear: both;"></div>
	</div> <!-- fin content-header -->
	<div id="res_movimientos" class="cuerpo-despachos mostrar-ocultar">
		<table>
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
		<div style="clear: both;"></div>
	</div> <!-- fin cuerpo -->
</div>
<br />
<?php endif; ?>

<?php if (set_value('show_stock_sap')): ?>
<div>
	<div class="content-header stock-sap">
		<h3>Stock SAP</h3>
		<span class="mostrar-ocultar">click para cerrar</span>
		<span class="mostrar-ocultar" style="display:none;">click para expandir</span>
		<div style="clear: both;"></div>
	</div> <!-- fin content-header -->
	<div id="res_stock_sap" class="cuerpo-stock-sap mostrar-ocultar">
		<table>
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
	</div> <!-- fin cuerpo -->
</div>
<br />
<?php endif; ?>

<?php if (set_value('show_stock_scl')): ?>
<div>
	<div class="content-header stock-scl">
		<h3>Stock SCL</h3>
		<span class="mostrar-ocultar">click para cerrar</span>
		<span class="mostrar-ocultar" style="display:none;">click para expandir</span>
		<div style="clear: both;"></div>
	</div> <!-- fin content-header -->
	<div id="res_stock_sap" class="cuerpo-stock-scl mostrar-ocultar">
		<table>
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
	</div> <!-- fin cuerpo -->
</div>
<br />
<?php endif; ?>

<?php if (set_value('show_trafico')): ?>
<div>
	<div class="content-header trafico">
		<h3>Trafico</h3>
		<span class="mostrar-ocultar">click para cerrar</span>
		<span class="mostrar-ocultar" style="display:none;">click para expandir</span>
		<div style="clear: both;"></div>
	</div> <!-- fin content-header -->
	<div id="res_stock_sap" class="cuerpo-trafico mostrar-ocultar">
		<table>
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
	</div> <!-- fin cuerpo -->
	</div><!-- fin content-module-main-principal -->
<br />
<?php endif; ?>




	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

</div> <!-- fin content-module -->

<script type="text/javascript">
	$(document).ready(function() {
		if ($("#series").val() != "")
		{
			$("div.cuerpo-formulario").hide();
			$("div.formulario span").toggle();
		}

		$("#boton-submit").click(function(event) {
			event.preventDefault();
			$("form").submit();
		})

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
