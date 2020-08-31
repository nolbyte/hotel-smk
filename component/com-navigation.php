
<ul class="sidebar-menu">
	<li class="header">NAVIGASI</li>
	<li><a href="index.php"><i class="fa fa-home"></i> Beranda</a></li>	
	<li class="treeview">
		<a href="#">
            <i class="fa fa-plug"></i> <span>Data Master</span>
            <span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			</span>
		</a>
		<ul class="treeview-menu">
            <li><a href="?module=data_bank&file=list"><i class="fa fa-circle-o text-red"></i> Data Bank</a></li>
            <li><a href="?module=data_trans&file=list"><i class="fa fa-circle-o text-aqua"></i> Jenis Transaksi</a></li>
			<li><a href="?module=cabang&file=list"><i class="fa fa-circle-o text-yellow"></i> Kantor Cabang</a></li>
		</ul>
	</li>
	<li class="treeview">
		<a href="#">
            <i class="fa fa-dollar"></i> <span>Transaksi</span>
            <span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			</span>
		</a>
		<ul class="treeview-menu">
            <li><a href="?module=transaksi&file=list"><i class="fa fa-dollar"></i> Data Transaksi</a></li>
            <li><a href="?module=transaksi&file=log"><i class="fa fa-circle-o text-aqua"></i> Log Transaksi</a></li>
		</ul>
	</li>	
	<li class="treeview">
		<a href="#">
            <i class="fa fa-user"></i> <span>Pengguna</span>
            <span class="pull-right-container">
				<i class="fa fa-angle-left pull-right"></i>
			</span>
		</a>
		<ul class="treeview-menu">
            <li><a href="?module=user&file=list"><i class="fa fa-circle-o text-green"></i>Data Pengguna</a></li>															
			<li><a href="logout.php"><i class="fa fa-circle-o text-yellow"></i> Logout</a></li>
		</ul>
	</li>
</ul>