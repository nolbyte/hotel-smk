<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Data kategori layanan berhasil disimpan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['SaveData']);
}
if(isset($_SESSION['errData'])){
	?>
	<script>toastr.error('<?php echo $_SESSION['errData']?>', 'Error!', {timeOut: 5000, progressBar: true})</script>
	<?php
	unset($_SESSION['errData']);
}
if(isset($_POST['save'])){
	$gump = new GUMP();
	$_POST = array(
		'nama_layanan_kategori' => $_POST['nama_layanan_kategori'],
		'keterangan' => $_POST['keterangan']
	);
	$_POST = $gump->sanitize($_POST);
	$gump->validation_rules(array(
		'nama_layanan_kategori' => 'required',
		'keterangan' => 'required'
	));
	$gump->filter_rules(array(
		'nama_layanan_kategori' => 'trim|sanitize_string',
		'keterangan' => 'trim|sanitize_string'
	));
	$ok = $gump->run($_POST);
	if($ok === false){
		$error[] = $gump->get_readable_errors(true);
	}else{
		$sql = $db->prepare("INSERT INTO layanan_kategori SET nama_layanan_kategori = ?, keterangan = ?");
		$sql->bindParam(1, $_POST['nama_layanan_kategori']);
		$sql->bindParam(2, $_POST['keterangan']);
		if(!$sql->execute()){
			print_r($sql->errorInfo());
		}else{
			?><script>toastr.success('Kategori layanan berhasil disimpan', 'Sukses!', {timeOut: 3000, progressBar: true})</script><?php
		}
	}
}
?>
<div class="row">
	<div class="col-md-4">
		<?php
		if(isset($error)){
			foreach($error as $error){
				?>
				<script>toastr.error('<?php echo $error ?>', 'Error!', {timeOut: 3000, progressBar: true})</script>
				<?php
			}
		}
		?>
		<div class="box box-info">
			<div class="box-header with-border">
				<h3 class="box-title">Tambah Kategori Layanan</h3>
			</div>
			<form method="post" action="">
				<div class="box-body">					
					<div class="form-group">
						<label class="control-label">Nama Kategori Layanan</label>
						<input type="text" name="nama_layanan_kategori" class="form-control" required>
					</div>
					<div class="form-group">
						<label class="control-label">Keterangan</label>
						<textarea class="form-control" name="keterangan" required></textarea>
					</div>			
				</div>
				<div class="box-footer">
					<button type="submit" class="btn btn-success" name="save">Simpan Kategori</button>
				</div>
			</form>
		</div>
	</div>
	<div class="col-md-8">
		<div class="box box-success">
			<div class="box-header with-border">
				<h3 class="box-title">Kategori Layanan</h3>
			</div>
			<div class="box-body">
				<?php
					$sql = $db->prepare("SELECT id_layanan_kategori, nama_layanan_kategori, keterangan FROM layanan_kategori ORDER BY id_layanan_kategori");
					$sql->execute();
				?>
				<table id="cat" class="table table-striped">
					<thead>
						<tr>
							<th>Nama Kategori Layanan</th>
							<th>Keterangan</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach($sql->fetchAll() as $row){ ?>
						<tr>
							<td><?= $row['nama_layanan_kategori']?></td>
							<td><?= nl2br($row['keterangan'])?></td>
							<td>
								<div class="btn-group">
									<a href="" class="btn btn-primary btn-flat" data-toggle="modal" data-target="#Edcat"
									data-id_layanan_kategori="<?=$row['id_layanan_kategori']?>"
									data-nama_layanan_kategori="<?=$row['nama_layanan_kategori']?>"
									data-keterangan="<?=$row['keterangan']?>">
										<i class="fa fa-edit"></i>
									</a>
									<a href="" class="btn btn-danger btn-flat btn-del" data-id="<?=$row['id_layanan_kategori']?>"><i class="fa fa-trash"></i></a>
								</div>
							</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
<!-- Edit Layanan-->
<div class="modal fade" tabindex="-1" role="dialog" id="Edcat">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Ubah Kategori Layanan</h4>
      </div>
      <form method="post" action="post.php?mod=layanan&hal=simpan_cat" id="frm-dtSiswa">
        <div class="modal-body">            
        	<div class="form-group">
        		<label class="control-label">Nama Kategori Layanan</label>
        		<input type="hidden" name="id_layanan_kategori">
        		<input type="text" name="nama_layanan_kategori" class="form-control" required>
        	</div>
        	<div class="form-group">
        		<label class="control-label">Keterangan</label>
        		<textarea class="form-control" name="keterangan" required></textarea>
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
		var ot = $('#cat').dataTable({"searching": false, "paging": false, "info": false, "ordering": false,});
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
							hal:'hapus_cat',
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
		$('#Edcat').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			$(this).find('input[name="id_layanan_kategori"]').val('')
			$(this).find('input[name="nama_layanan_kategori"]').val('')	
			$(this).find('textarea[name="keterangan"]').val('')						
			if(button.data('id_layanan_kategori') != ''){
				var id_layanan_kategori = button.data('id_layanan_kategori')
				var nama_layanan_kategori = button.data('nama_layanan_kategori') 
				var keterangan = button.data('keterangan')											
				$(this).find('input[name="id_layanan_kategori"]').val(id_layanan_kategori)					
				$(this).find('input[name="nama_layanan_kategori"]').val(nama_layanan_kategori)
				$(this).find('textarea[name="keterangan"]').val(keterangan)					
			}
		});
	});
</script>