<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Data layanan berhasil disimpan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
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
			<h3 class="box-title">Layanan</h3>&nbsp;&nbsp;<a href="" class="btn btn-info btn-flat" data-toggle="modal" data-target="#AddLy">Tambah Layanan</a>
		</div>
		<div class="box-body">
			<?php
				$sql = $db->prepare("SELECT * FROM layanan l JOIN layanan_kategori lk ON l.id_layanan_kategori=lk.id_layanan_kategori ORDER BY id_layanan");
				$sql->execute();
			?>
			<table id="lynn" class="table table-bordred table-striped">
				<thead>
					<tr>
						<th>Nomor</th>
						<th>Nama Layanan</th>
						<th>Kategori</th>
						<th>Harga</th>
						<th>Aksi</th>
					</tr>
				</thead>
				<tbody>
					<?php
						$no = 1;
						foreach($sql->fetchAll() as $row){
					?>
					<tr>
						<td><?= $no++ ?></td>
						<td><?= $row['nama_layanan']?></td>
						<td><?= $row['nama_layanan_kategori']?></td>
						<td><?= format_rp($row['harga_layanan']).' / '.$row['satuan']?></td>
						<td>
							<div class="btn-group">
								<a href="" class="btn btn-default btn-flat" data-toggle="modal" data-target="#EdLy"
								data-id_layanan="<?=$row['id_layanan']?>"								
								data-nama_layanan="<?=$row['nama_layanan']?>"
								data-nama_layanan_kategori="<?=$row['nama_layanan_kategori']?>"
								data-harga_layanan="<?=$row['harga_layanan']?>"
								data-satuan="<?=$row['satuan']?>">
									<i class="fa fa-edit"></i>
								</a>
								<a href="#" class="btn btn-default btn-del" data-id="<?=$row['id_layanan']?>"><i class="fa fa-trash" aria-hidden="true"></i>
								</a>
							</div>
						</td>
					</tr>
					<?php
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<!-- Tambah Layanan-->
<div class="modal fade" tabindex="-1" role="dialog" id="AddLy">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Tambah Data Layanan</h4>
      </div>
      <form method="post" action="post.php?mod=layanan&hal=tambah_layanan" id="frm-dtSiswa">
        <div class="modal-body">            
          <div class="form-group">
            <label for="">Nama Layanan</label>                        
            <input type="text" name="nama_layanan" class="form-control" required>
          </div>
          <div class="form-group">
          	<label class="control-label">Kategori</label>          	
          	<select name="id_layanan_kategori" class="form-control" required>
          		<?php
          			$sql = $db->prepare("SELECT id_layanan_kategori, nama_layanan_kategori FROM layanan_kategori");
          			$sql->execute();
          		?>
          		<option value="">--Pilih Kategori--</option>
          		<?php foreach($sql->fetchAll() as $row){
          		?>
          		<option value="<?=$row['id_layanan_kategori']?>"><?=$row['nama_layanan_kategori']?></option>
          		<?php } ?>
          	</select>         	
          </div>             
          <div class="form-group">
          	<label class="control-label">Harga Layanan</label>
          	<input type="text" name="harga_layanan" class="form-control" required>
          	<p class="help-text text-red">Tanpa tanda baca, contoh: 2000000</p>
          </div>
          <div class="form-group">
          	<label class="control-label">Satuan</label>
          	<input type="text" name="satuan" class="form-control" required>          	
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
<!-- Edit Layanan-->
<div class="modal fade" tabindex="-1" role="dialog" id="EdLy">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Ubah Data Layanan</h4>
      </div>
      <form method="post" action="post.php?mod=layanan&hal=simpan_layanan" id="frm-dtSiswa">
        <div class="modal-body">            
          <div class="form-group">
            <label for="">Nama Layanan</label>
            <input type="hidden" name="id_layanan">            
            <input type="text" name="nama_layanan" class="form-control" required>
          </div>
          <div class="form-group">
          	<label class="control-label">Kategori</label>
          	<input type="text" name="nama_layanan_kategori" class="form-control" readonly>
          	<select name="id_layanan_kategori" class="form-control" required>
          		<?php
          			$sql = $db->prepare("SELECT id_layanan_kategori, nama_layanan_kategori FROM layanan_kategori");
          			$sql->execute();
          		?>
          		<option value="">--Pilih Kategori--</option>
          		<?php foreach($sql->fetchAll() as $row){
          		?>
          		<option value="<?=$row['id_layanan_kategori']?>"><?=$row['nama_layanan_kategori']?></option>
          		<?php } ?>
          	</select>         	
          </div>             
          <div class="form-group">
          	<label class="control-label">Harga Layanan</label>
          	<input type="text" name="harga_layanan" class="form-control" required>
          	<p class="help-text text-red">Tanpa tanda baca, contoh: 2000000</p>
          </div>
          <div class="form-group">
          	<label class="control-label">Satuan</label>
          	<input type="text" name="satuan" class="form-control" required>          	
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
		var ot = $('#lynn').dataTable({"iDisplayLength": 20,});
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
							mod:'layanan',
							hal:'hapus_layanan',
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
		$('#EdLy').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			$(this).find('input[name="id_layanan"]').val('')
			$(this).find('input[name="nama_layanan"]').val('')	
			$(this).find('input[name="nama_layanan_kategori"]').val('')
			$(this).find('input[name="harga_layanan"]').val('')
			$(this).find('input[name="satuan"]').val('')			
			if(button.data('id_layanan') != ''){
				var id_layanan = button.data('id_layanan')
				var nama_layanan = button.data('nama_layanan') 
				var nama_layanan_kategori = button.data('nama_layanan_kategori')			
				var harga_layanan = button.data('harga_layanan')
				var satuan = button.data('satuan')				
				$(this).find('input[name="id_layanan"]').val(id_layanan)	
				$(this).find('input[name="nama_layanan"]').val(nama_layanan)
				$(this).find('input[name="nama_layanan_kategori"]').val(nama_layanan_kategori)
				$(this).find('input[name="harga_layanan"]').val(harga_layanan)	
				$(this).find('input[name="satuan"]').val(satuan)			
			}
		});
	});
</script>