<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Data tamu berhasil disimpan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
?>
<div class="row">
	<div class="box box-danger">
		<div class="box-header with-border">
			<h3 class="box-title">Daftar Tamu &nbsp;&nbsp;<a class="btn btn-success btn-flat" href="index.php?mod=tamu&hal=tamu-add">Tambah Tamu Baru</a></h3>
		</div>
		<div class="box-body">
			<table id="user" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Nomor</th>
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
						<td><?= $no++ ?></td>
						<td><?= $tamu['prefix'].'. '.$tamu['nama_depan'].'&nbsp;'.$tamu['nama_belakang']; ?></td>
						<td><?= $tamu['warga_negara']; ?></td>
						<td><?= $tamu['nomor_telp']; ?></td>
						<td><?= $tamu['email']; ?></td>
						<td>
							<div class="btn-group">
								<a class="btn btn-default btn-flat" href="index.php?mod=tamu&hal=tamu-update&id=<?= $tamu['id_tamu']; ?>"><i class="fa fa-edit"></i></a>
								<a href="#" class="btn btn-default btn-del" data-id="<?=$tamu['id_tamu']?>"><i class="fa fa-trash" aria-hidden="true"></i>
								</a>
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