<!-- ============================== NAVBAR ============================== -->
<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
	<div class="container">

		<div class="navbar-header">
			<a class="navbar-brand" href="#">{titulo_modulo}</a>
			<button class="navbar-toggle" data-toggle="collapse" data-target=".navMenuCollapse">
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
		</div> <!-- DIV class="navbar-header" -->

		<div class="collapse navbar-collapse navMenuCollapse">
			<ul class="nav navbar-nav navbar-right">
			{navbar_menu}
				<li class="dropdown {selected}">
					<a class="dropdown-toggle" data-toggle="dropdown" href="#">
						<span class="{icono}"></span> {app} <b class="caret"></b>
					</a>

					<ul class="dropdown-menu">
						{modulos}
						<li class="{modulo_selected}">
							<a href="{modulo_url}"><span class="{modulo_icono}"></span> {modulo_nombre}</a>
						</li>
						{/modulos}
					</ul>
				</li>
			{/navbar_menu}
				<li>
					<a href="{logout_url}">
						<span class="glyphicon glyphicon-off"></span> Logout {user_firstname}
					</a>
				</li>

			</ul>
		</div> <!-- DIV class="collapse navbar-collapse navMenuCollapse" -->

	</div> <!-- DIV class="container" -->
</nav>
<!-- ============================== /NAVBAR ============================== -->
