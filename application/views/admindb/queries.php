<div>
<?php if(count($queries_data)): ?>
	<table class="table table-striped table-hover table-condensed reporte">
		<thead>
			<tr>
				<th>SPID</th>
				<th>status</th>
				<th>login</th>
				<th>host name</th>
				<th>db name</th>
				<th>command</th>
				<th>program name</th>
				<th>start time</th>
				<th>duration</th>
				<th>CPU time</th>
				<th>Disk IO</th>
				<th>reads</th>
				<th>writes</th>
				<th>SQL</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($queries_data as $query): ?>
			<tr>
				<td><?php echo $query['SPID']; ?></td>
				<td><?php echo $query['Status']; ?></td>
				<td><?php echo $query['Login']; ?></td>
				<td><?php echo $query['HostName']; ?></td>
				<td><?php echo $query['DBName']; ?></td>
				<td><?php echo $query['Command']; ?></td>
				<td><?php echo $query['ProgramName']; ?></td>
				<td><?php echo $query['start_time']; ?></td>
				<td><?php echo fmt_hora($query['total_elapsed_time']/1000); ?></td>
				<td><?php echo fmt_hora($query['cpu_time']/1000); ?></td>
				<td><?php echo fmt_cantidad($query['DiskIO']); ?></td>
				<td><?php echo fmt_cantidad($query['reads']); ?></td>
				<td><?php echo fmt_cantidad($query['writes']); ?></td>
				<td><?php echo $query['text']; ?></td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endif; ?>

</div> <!-- fin content-module-main -->
