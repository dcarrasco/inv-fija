<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			Sube stock: <?php echo $inventario_nombre;?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="msg-alerta cf ac">
		<?php echo ($msg_alerta == '') ? '' : '<p class="msg-alerta round">' . $msg_alerta . '</p>' ?>
	</div>

	<div class="content-module-main">
		<?php echo form_open_multipart('analisis/sube_stock/' . $inventario_id); ?>
		<?php echo form_hidden('formulario','upload'); ?>
		<table>
			<thead>
				<tr>
					<th>id</th>
					<th>Nombre</th>
					<th>Seleccionar archivo</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td><?php echo $inventario_id; ?></td>
					<td><?php echo $inventario_nombre; ?></td>
					<td>
						<span style="color: red; font-weight: bold">ADVERTENCIA</span><br>
						<span style="color: red";>Al subir un archivo se eliminar <strong>TODOS</strong> los registros asociados al inventario
							"<?php echo $inventario_nombre?>"</span>
						<br><br>
						<?php echo form_upload('upload_file','',' size=60'); ?>
						<br><br>
						Password de administrador <?php echo form_password('password',''); ?>
						<br><br>
						<?php echo $upload_error; ?>
						<?php echo $msj_error; ?>
						<div id="progreso_carga">
							<div id="barra"><div id="barra_progreso"></div></div>
							<div id="status_progreso">Cargando registros OK <span id="reg_actual">0</span> de <?php echo ($regs_OK); ?></div>
						</div>
					</td>
				</tr>

				<tr>
					<td colspan="4">
						<?php echo form_submit('submit', 'Subir archivo', ' class="button b-active round ic-ok fr" id="btn_guardar"'); ?>
					</td>
				</tr>

				<tr>
					<td colspan="4">
<pre>
Formato del archivo:
	Archivo de texto
	Extension .txt
	Campos separados por tabulación

	Campos
			Ubicacion
			Catalogo
			Descripcion catalogo
			Lote
			Centro
			Almacen
			Unidad de medida
			Stock SAP
			Hoja
</pre>
					</td>
				</tr>

		</tbody>
		</table>
		<?php echo form_close(); ?>

	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">
	</div> <!-- fin content-module-footer -->

	<form id="frm_aux">
		<input type="hidden" name="id" id="id_id" value="">
		<input type="hidden" name="id_inv" id="id_id_inv" value="">
		<input type="hidden" name="hoja" id="id_hoja" value="">
		<input type="hidden" name="aud" id="id_aud" value="">
		<input type="hidden" name="dig" id="id_dig" value="">
		<input type="hidden" name="ubic" id="id_ubic" value="">
		<input type="hidden" name="cat" id="id_cat" value="">
		<input type="hidden" name="desc" id="id_desc" value="">
		<input type="hidden" name="lote" id="id_lote" value="">
		<input type="hidden" name="cen" id="id_cen" value="">
		<input type="hidden" name="alm" id="id_alm" value="">
		<input type="hidden" name="um" id="id_um" value="">
		<input type="hidden" name="ssap" id="id_ssap" value="">
		<input type="hidden" name="sfis" id="id_sfis" value="">
		<input type="hidden" name="obs" id="id_obs" value="">
		<input type="hidden" name="fec" id="id_fec" value="">
		<input type="hidden" name="nvo" id="id_nvo" value="">
	</form>

</div> <!-- fin content-module -->


<script type="text/javascript">
$(document).ready(function() {
	$('#barra_progreso').css('height', '30px');
	$('#barra_progreso').css('width', '0px');
	$('#barra_progreso').css('background-color', '#236ab3');

	$('#barra').css('border', '1px solid black');
	$('#barra').css('height', '30px');
	$('#barra').css('width', '400px');
});

	var regs_procesados = 0;

	function proc_linea_carga(count, id, id_inv, hoja, aud, dig, ubic, cat, desc, lote, cen, alm, um, ssap, sfis, obs, fec, nvo) {
		var total_regs = <?php echo ($regs_OK); ?>;
		var total_carac = 40;
		$('#id_id').val(id);
		$('#id_id_inv').val(id_inv);
		$('#id_hoja').val(hoja);
		$('#id_aud').val(aud);
		$('#id_dig').val(dig);
		$('#id_ubic').val(ubic);
		$('#id_cat').val(cat);
		$('#id_desc').val(desc);
		$('#id_lote').val(lote);
		$('#id_cen').val(cen);
		$('#id_alm').val(alm);
		$('#id_um').val(um);
		$('#id_ssap').val(ssap);
		$('#id_sfis').val(sfis);
		$('#id_obs').val(obs);
		$('#id_fec').val(fec);
		$('#id_nvo').val(nvo);


		$.ajax({
			type:  "POST",
			url:   js_base_url + "analisis/inserta_linea_archivo",
			async: false,
			data:  $('#frm_aux').serialize(),
			success: function(datos) {
				regs_procesados += 1;
				var pixeles_progreso = parseInt(400 * regs_procesados/total_regs);
				$('#barra_progreso').css('width', pixeles_progreso + 'px');
				//$('#barra_progreso').text('['+Array(carac_progreso).join('*') + Array(total_carac - carac_progreso).join('-')+']');
				$('#reg_actual').text(regs_procesados);
				if (regs_procesados >= total_regs) {
					$('#status_progreso').html('Carga finalizada (' + total_regs + ' registros cargados)');
				}
				//alert( "Se guardaron los datos: " + datos);
			}
		});

	}
</script>


<script type="text/javascript">
$(document).ready(function() {
<?php echo $script_carga; ?>
//$('#barra_progreso').text('Proceso finalizado.');
});
</script>
