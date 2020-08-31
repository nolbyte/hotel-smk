<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Data booking berhasil disimpan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
$status = 'Check Out';
$sql = $db->prepare("SELECT * FROM reservasi r
	JOIN kamar k ON r.id_kamar=k.id_kamar
	JOIN kamar_tipe kt ON k.id_kamar_tipe=kt.id_kamar_tipe
	JOIN tamu t ON r.id_tamu=t.id_tamu ORDER BY id_reservasi DESC");
$sql->execute();
?>
<div class="row">
	<div class="box box-success">
		<div class="box-header with-border">
			<h3 class="box-title">Daftar Booking/Reservasi&nbsp;&nbsp;<a href="index.php?mod=transaksi&hal=booking_add" class="btn btn-info btn-flat"><i class="fa fa-plus"></i> Tambah Booking Baru</a></h3>
		</div>
		<div class="box-body">
			<table id="user" class="table table-striped">
				<thead>
					<tr>
						<th>#Kamar</th>
						<th>Nama Tamu</th>
						<th>Tanggal Checkin</th>
						<th>Tanggal Checkout</th>
						<th>Deposit</th>
						<th>Status</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($sql->fetchAll() as $row){ ?>
						<tr>
							<td><?= $row['nomor_kamar'].'-'.$row['nama_kamar_tipe']?></td>
							<td><?= $row['prefix'].'. '.$row['nama_depan'].' '.$row['nama_belakang']?></td>
							<td><?= $row['tanggal_checkin']?></td>
							<td><?= $row['tanggal_checkout']?></td>
							<td><?= format_rp($row['deposit'])?></td>
							<td><?= $row['status']?></td>
							<td>
								<div class="btn-group">
									<a href="index.php?mod=transaksi&hal=booking-detail&id=<?=$row['id_reservasi']?>" class="btn btn-default btn-flat"><i class="fa fa-search"></i></a>
									<a target="_blank" href="reservation.php?id=<?= $row['id_reservasi']?>" class="btn btn-default btn-flat"><i class="fa fa-print"></i></a>
									<a href="index.php?mod=transaksi&hal=booking-update&id=<?=$row['id_reservasi']?>" class="btn btn-default btn-flat"><i class="fa fa-edit"></i></a>
									<a href="#" class="btn btn-default btn-flat btn-del" data-id="<?=$row['id_reservasi']?>"><i class="fa fa-trash"></i></a>
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
		var ot = $('#user').dataTable({"iDisplayLength": 20,"ordering": false,});
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
							mod:'transaksi',
							hal:'booking-hapus',
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