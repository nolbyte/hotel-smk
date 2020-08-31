<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Tipe Kamar berhasil disimpan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
if(isset($_SESSION['errData'])){
	?>
	<script>toastr.error('<?php echo $_SESSION['errData']?>', 'Error!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['errData']);
}
?>
<div class="row">
	<div class="box">
		<div class="box-header with-border">
			<h3 class="box-title">Tipe Kamar</h3>&nbsp;&nbsp;<a href="" class="btn btn-info btn-flat" data-toggle="modal" data-target="#Tambah">Tambah Tipe Kamar</a>
		</div>
		<div class="box-body">
			<?php
				$sql = $db->prepare("SELECT * FROM kamar_tipe ORDER BY id_kamar_tipe ASC");
				$sql->execute();

			?>
			<table id="tipe" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Nomor</th>
						<th>Tipe Kamar</th>
						<th>Harga /Malam</th>
						<th>Harga /Orang</th>
						<th>Aksi</th>
					</tr>
					<tbody>
						<?php
							$no=1;
							foreach($sql->fetchAll() as $row){
						?>
						<tr>
							<td><?= $no++ ?></td>
							<td><?= $row['nama_kamar_tipe']?></td>
							<td><?= format_rp($row['harga_malam'])?></td>
							<td><?= format_rp($row['harga_orang'])?></td>
							<td>
								<div class="btn-group">
									<a href="" class="btn btn-default btn-flat" data-toggle="modal" data-target="#EdTipe"
									data-id_kamar_tipe="<?=$row['id_kamar_tipe']?>"
									data-nama_kamar_tipe="<?=$row['nama_kamar_tipe']?>"
									data-harga_malam="<?=$row['harga_malam']?>"
									data-harga_orang="<?=$row['harga_orang']?>"
									data-keterangan="<?=$row['keterangan']?>">
										<i class="fa fa-edit"></i>
									</a>
									<a href="#" class="btn btn-default btn-del" data-id="<?=$row['id_kamar_tipe']?>"><i class="fa fa-trash" aria-hidden="true"></i>
									</a>
								</div>
							</td>
						</tr>
						<?php
							}
						?>
					</tbody>
				</thead>
			</table>	
		</div>
	</div>
</div>
<!-- Tambah Tipe Kamar-->
<div class="modal fade" tabindex="-1" role="dialog" id="Tambah">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Tambah Tipe Kamar</h4>
      </div>
      <form method="post" action="post.php?mod=kamar&hal=tambah_tipe" id="frm-dtSiswa">
        <div class="modal-body">            
          <div class="form-group">
            <label for="">Nama Tipe Kamar</label>
            <input type="text" name="nama_kamar_tipe" class="form-control" required>
          </div>
          <div class="form-group">
          	<label class="control-label">Harga /Malam</label>
          	<input type="text" name="harga_malam" class="form-control" required>
          	<p class="help-text text-red">Tanpa tanda baca, contoh: 2000000</p>
          </div>             
          <div class="form-group">
          	<label class="control-label">Harga /Orang</label>
          	<input type="text" name="harga_orang" class="form-control" required>
          	<p class="help-text text-red">Tanpa tanda baca, contoh: 2000000</p>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn bg-orange btn-flat btn-simpan-brg" value="Simpan">
          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Edit Tipe Kamar-->
<div class="modal fade" tabindex="-1" role="dialog" id="EdTipe">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Tambah Tipe Kamar</h4>
      </div>
      <form method="post" action="post.php?mod=kamar&hal=simpan_tipe" id="frm-dtSiswa">
        <div class="modal-body">            
          <div class="form-group">
            <label for="">Nama Tipe Kamar</label>
            <input type="hidden" name="id_kamar_tipe">
            <input type="hidden" name="keterangan">
            <input type="text" name="nama_kamar_tipe" class="form-control" required>
          </div>
          <div class="form-group">
          	<label class="control-label">Harga /Malam</label>
          	<input type="text" name="harga_malam" class="form-control" required>
          	<p class="help-text text-red">Tanpa tanda baca, contoh: 2000000</p>
          </div>             
          <div class="form-group">
          	<label class="control-label">Harga /Orang</label>
          	<input type="text" name="harga_orang" class="form-control" required>
          	<p class="help-text text-red">Tanpa tanda baca, contoh: 2000000</p>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn bg-orange btn-flat btn-simpan-brg" value="Simpan">
          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
</div>
<script>
	$(document).ready(function(){
		var ot = $('#tipe').dataTable({"iDisplayLength": 20,});
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
							hal:'hapus_tipe',
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
		$('#EdTipe').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			$(this).find('input[name="id_kamar_tipe"]').val('')
			$(this).find('input[name="nama_kamar_tipe"]').val('')	
			$(this).find('input[name="harga_malam"]').val('')
			$(this).find('input[name="harga_orang"]').val('')
			$(this).find('input[name="keterangan"]').val('')	            
			if(button.data('id_kamar_tipe') != ''){
				var id_kamar_tipe = button.data('id_kamar_tipe')
				var nama_kamar_tipe = button.data('nama_kamar_tipe') 			
				var harga_malam = button.data('harga_malam')
				var harga_orang = button.data('harga_orang')
				var keterangan = button.data('keterangan')
				$(this).find('input[name="id_kamar_tipe"]').val(id_kamar_tipe)	
				$(this).find('input[name="nama_kamar_tipe"]').val(nama_kamar_tipe)
				$(this).find('input[name="harga_malam"]').val(harga_malam)
				$(this).find('input[name="harga_orang"]').val(harga_orang)
				$(this).find('input[name="keterangan"]').val(keterangan)		
			}
		});
	});
</script>