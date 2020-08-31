<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Data kamar berhasil diperbarui', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
?>
<div class="row">
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Daftar Kamar</h3>&nbsp;&nbsp;<a href="index.php?mod=kamar&hal=tambah" class="btn btn-info btn-flat">Tambah Kamar</a>
		</div>
		<div class="box-body">
			<?php
				$sql = $db->prepare("SELECT * FROM kamar k JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe ORDER BY nomor_kamar");
				$sql->execute();
			?>
			<table id="user" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>#Kamar</th>
						<th>Tipe Kamar</th>
						<th>Harga /Malam</th>
						<th>Max Dewasa</th>
						<th>Max Anak</th>
						<th>Status</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($sql->fetchAll() as $row){ ?>
					<tr>
						<td><?= $row['nomor_kamar']?></td>
						<td><?= $row['nama_kamar_tipe']?></td>
						<td><?= $row['harga_malam']?></td>
						<td><?= $row['max_dewasa']?></td>
						<td><?= $row['max_anak']?></td>
						<td>
							<?php
								if($row['status_kamar'] === 'TERSEDIA'){
									$status = 'green';
								}
								if($row['status_kamar'] === 'TERPAKAI'){
									$status = 'red';
								}
								if($row['status_kamar'] === 'KOTOR'){
									$status = 'yellow';
								}
							?>
							<span class="badge bg-<?= $status; ?>"><?= $row['status_kamar']; ?></span>
						</td>
						<td class="text-center">
							<div class="btn-group">
								<a class="btn btn-default btn-flat" href="index.php?mod=kamar&hal=update&id=<?=$row['id_kamar']?>"><i class="fa fa-edit"></i>
								</a>
								<a href="#" class="btn btn-default btn-del" data-id="<?=$row['id_kamar']?>"><i class="fa fa-trash" aria-hidden="true"></i>
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
							mod:'kamar',
							hal:'hapus',
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