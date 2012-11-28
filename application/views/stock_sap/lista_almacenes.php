
<div class="content-module">

	<div class="content-module-heading cf">
		<div class="fl">
			<?php echo $menu_configuracion; ?>
		</div>
		<div class="fr">
			<?php echo anchor('stock_sap/edita_almacenes/' . $tipo_op, 'Nuevo Almacen...', 'class="button b-active round ic-agregar fl"')?>
		</div>
	</div> <!-- fin content-module-heading -->

	<div class="content-module-main">


		<table>
			<tr>
				<th>centro</th>
				<th>cod_almacen</th>
				<th>des_almacenes</th>
				<th>grupos</th>
			</tr>
			<?php foreach($listado_almacenes as $reg): ?>
				<tr>
					<?php $class_alerta = (isset($detalle_grupos[$reg['centro'] . $reg['cod_almacen']])) ? '' : ' style="color:red; font-weight: bold;"';  ?>
					<td <?php echo $class_alerta; ?>><?php echo $reg['centro']?></td>
					<td <?php echo $class_alerta; ?>><?php echo $reg['cod_almacen']?></td>
					<td <?php echo $class_alerta; ?>><?php echo anchor('stock_sap/edita_almacenes/' . $tipo_op . '/' . $reg['centro'] . '/' . $reg['cod_almacen'], $reg['des_almacen']); ?></td>
					<td>
						<?php if(isset($detalle_grupos[$reg['centro'] . $reg['cod_almacen']])):  ?>
							<?php foreach($detalle_grupos[$reg['centro'] . $reg['cod_almacen']] as $reg_grupo): ?>
								<?php echo $reg_grupo['tipo']; ?><br />
							<?php endforeach; ?>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</table>


	</div> <!-- fin content-module-main -->

	<div class="content-module-footer cf">

	</div> <!-- fin content-module-footer -->


</div> <!-- fin content-module -->
