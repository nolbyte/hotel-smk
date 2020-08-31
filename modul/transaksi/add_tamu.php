<?php
defined("RESMI") or die("Akses ditolak");
?>
<div class="row">
	<div class="box box-danger">
		<div class="box-header with-border">
			<h3 class="box-title">Daftar Tamu</h3>
		</div>
		<div class="box-body">
			<div class="callout callout-warning">
				<h4><i class="icon fa fa-info"></i> Informasi</h4>				
				<p>Gunakan kolom Search sebelah kanan untuk pencarian cepat nama tamu dengan mengetik nama depan atau nama belakang tamu yang bersangkutan</p>
			</div>
			<table id="user" class="table table-bordered table-striped">
				<thead>
					<tr>						
						<th>Nama Tamu</th>
						<th>Warga Negara</th>
						<th>Nomor Telepon/HP</th>
						<th>Email</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$sql = $db->prepare("SELECT * FROM tamu ORDER BY nama_depan ASC");
						$sql->execute();
						$no = 1;
						foreach($sql->fetchAll() as $tamu){
					?>
					<tr>						
						<td><?= $tamu['prefix'].'. '.$tamu['nama_depan'].'&nbsp;'.$tamu['nama_belakang']; ?></td>
						<td><?= $tamu['warga_negara']; ?></td>
						<td><?= $tamu['nomor_telp']; ?></td>
						<td><?= $tamu['email']; ?></td>
						<td>
							<div class="btn-group">
								<a class="btn btn-default" href="index.php?mod=transaksi&hal=booking_add&id_tm=<?= $tamu['id_tamu']; ?>"><i class="fa fa-check"></i> Pilih</a>								
							</div>
						</td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script>
	$(document).ready(function(){
		var ot = $('#user').dataTable({"iDisplayLength": 20,});
		$('.btn-del').click(function(e){
			e.preventDefault()
			var button = $(this)
			bootbox.confirm({
				title: "Konfirmasi penghapusan",
				message: "Data yang terhapus tidak dapat dikembalikan, yakin ingin menghapus data ini?",
				buttons: {
					confirm: {
						label: 'Ya',
						className: 'btn-info btn-flat'
					},
					cancel: {
						label: 'Tidak',
						className: 'btn-danger btn-flat'
					},
				},
				callback: function (result) {
					if(result == true){
						var data = {
							mod:'tamu',
							hal:'tamu-hapus',
							id:button.data('id')
						}
						var sel_row = button.parent().parent().parent();
						$.get('post.php', data, function(hasil){
							ot.fnDeleteRow(sel_row);
							toastr["success"]("Data berhasil dihapus");              
						});
					}
				}
			});      
		});
	});
</script>