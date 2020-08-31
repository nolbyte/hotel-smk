<?php
defined("RESMI") or die("Akses ditolak");
if(isset($_SESSION['SaveData'])){
	?>
	<script>toastr.success('Data berhasil disimpan', 'Sukses!', {timeOut: 5000, progressBar: true})</script>
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
	<div class="box box-success">
		<div class="box-header with-border">
			<h3 class="box-title">Pengguna Sistem</h3>&nbsp;&nbsp;<a href="#AddUser" class="btn btn-warning btn-flat" data-toggle="modal">Tambah User Baru</a>
		</div>
		<div class="box-body">
			<?php
				$sql1 = $db->prepare("SELECT * FROM user u
					JOIN user_role ur ON u.id_user_role=ur.id_user_role
					ORDER BY u.id_user_role ASC");
				$sql1->execute();
				$no = 1;
				//$usr=$sql1->fetch(PDO::FETCH_ASSOC);
			?>
			<table id="user" class="table table-bordered table-striped">
				<thead>
					<tr>
						<th>Urut</th>
						<th>Username Login</th>
						<th>Nama User</th>
						<th>Jabatan</th>
						<th>Hak Akses</th>						
						<th></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach($sql1->fetchAll() as $row){ ?>
						<tr>
							<td><?=$no++?></td>
							<td><?= $row['username']?></td>
							<td><?= $row['nama']?></td>
							<td><?= $row['jabatan']?></td>
							<td><?= $row['role_name']?></td>							
							<td class="text-center">
								<div class="btn-group">
									<a href="" class="btn btn-default btn-flat" data-toggle="modal"
									data-target="#Eduser"
									data-id_user="<?=$row['id_user']?>"
									data-username="<?=$row['username']?>"
									data-nama="<?=$row['nama']?>"
									data-jabatan="<?=$row['jabatan']?>"
									data-role_name="<?=$row['role_name']?>"
									data-id_user_role="<?=$row['id_user_role']?>"><i class="fa fa-edit"></i></a>
									<a href="" class="btn btn-default btn-flat" data-toggle="modal" data-target="#EdPass"
									data-id_user="<?=$row['id_user']?>"
									data-username="<?=$row['username']?>"><i class="fa fa-lock"></i></a>
									<a href="" class="btn btn-default btn-flat btn-del" data-id="<?=$row['id_user']?>"><i class="fa fa-trash" aria-hidden="true"></i></a>
								</div>
							</td>
						</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<!-- Edit Password-->
<div class="modal fade" tabindex="-1" role="dialog" id="EdPass">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Ubah Password Pengguna</h4>
      </div>
      <form method="post" action="post.php?mod=system&hal=simpan_pass" id="frm-dtSiswa">
        <div class="modal-body">            
          <div class="form-group">
            <label for="">Username</label>
            <input type="hidden" name="id_user">            
            <input type="text" name="username" class="form-control" readonly>
          </div>
          <div class="form-group">
          	<label class="control-label">Password</label>
          	<input type="password" name="password" class="form-control" required>
          	<p class="help-text text-red">Minimal 8 karakter, gabungkan a-z dan 0-9</p>
          </div>             
          <div class="form-group">
          	<label class="control-label">Ulangi Password</label>
          	<input type="password" name="password2" class="form-control" required>
          	<p class="help-text text-red">Minimal 8 karakter, gabungkan a-z dan 0-9</p>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn bg-orange btn-flat" value="Simpan">
          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Edit User-->
<div class="modal fade" tabindex="-1" role="dialog" id="Eduser">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Ubah Data User</h4>
      </div>
      <form method="post" action="post.php?mod=system&hal=simpan_user" id="frm-dtSiswa">
        <div class="modal-body">            
          <div class="form-group">
            <label for="">Username Login</label>
            <input type="hidden" name="id_user">            
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="form-group">
          	<label class="control-label">Nama User</label>
          	<input type="text" name="nama" class="form-control" required>          	
          </div>             
          <div class="form-group">
          	<label class="control-label">Jabatan</label>
          	<input type="text" name="jabatan" class="form-control" required>          	
          </div>
          <div class="form-group">
          	<label class="control-label">Hak Akses</label>
          	<?php
          		$sql2=$db->prepare("SELECT id_user_role, role_name FROM user_role");
          		$sql2->execute();
          	?>
          	<select name="id_user_role" class="form-control">
          		<option value="">--Pilih--</option>
          		<?php foreach($sql2->fetchAll() as $role){
          		?>
          		<option value="<?=$role['id_user_role']?>"><?=$role['role_name']?></option>
          		<?php } ?>
          	</select>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn bg-orange btn-flat" value="Simpan">
          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Tutup</button>
        </div>
      </form>
    </div>
  </div>
</div>
<!-- Add User-->
<div class="modal fade" tabindex="-1" role="dialog" id="AddUser">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Tambah User System</h4>
      </div>
      <form method="post" action="post.php?mod=system&hal=add_user">
        <div class="modal-body">            
          <div class="form-group">
            <label for="">Username Login</label>                       
            <input type="text" name="username" class="form-control" required>
          </div>
          <div class="form-group">
          	<label class="control-label">Nama User</label>
          	<input type="text" name="nama" class="form-control" required>          	
          </div>             
          <div class="form-group">
          	<label class="control-label">Jabatan</label>
          	<input type="text" name="jabatan" class="form-control" required>          	
          </div>
          <div class="form-group">
          	<label class="control-label">Hak Akses</label>
          	<?php
          		$sql2=$db->prepare("SELECT id_user_role, role_name FROM user_role");
          		$sql2->execute();
          	?>
          	<select name="id_user_role" class="form-control" required>
          		<option value="">--Pilih--</option>
          		<?php foreach($sql2->fetchAll() as $role){
          		?>
          		<option value="<?=$role['id_user_role']?>"><?=$role['role_name']?></option>
          		<?php } ?>
          	</select>
          </div>
          <div class="form-group">
          	<label class="control-label">Password</label>
          	<input type="password" name="password" class="form-control" required>
          	<p class="help-text text-red">Minimal 8 karakter, gabungkan a-z dan 0-9</p>
          </div>             
          <div class="form-group">
          	<label class="control-label">Ulangi Password</label>
          	<input type="password" name="password2" class="form-control" required>
          	<p class="help-text text-red">Minimal 8 karakter, gabungkan a-z dan 0-9</p>
          </div>
        </div>
        <div class="modal-footer">
          <input type="submit" class="btn bg-orange btn-flat" value="Simpan">
          <button type="button" class="btn btn-danger btn-flat" data-dismiss="modal">Tutup</button>
        </div>
      </form>
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
							mod:'system',
							hal:'hapus_user',
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
		$('#Eduser').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			$(this).find('input[name="id_user"]').val('')
			$(this).find('input[name="username"]').val('')	
			$(this).find('input[name="nama"]').val('')
			$(this).find('input[name="jabatan"]').val('')
			$(this).find('select[name="role_name"]').val('')
			$(this).find('select[name="id_user_role"]').val('')	            
			if(button.data('id_user') != ''){
				var id_user = button.data('id_user')
				var username = button.data('username') 			
				var nama = button.data('nama')
				var jabatan = button.data('jabatan')
				var role_name = button.data('role_name')
				var id_user_role = button.data('id_user_role')
				$(this).find('input[name="id_user"]').val(id_user)	
				$(this).find('input[name="username"]').val(username)
				$(this).find('input[name="nama"]').val(nama)
				$(this).find('input[name="jabatan"]').val(jabatan)
				$(this).find('input[select="role_name"]').val(role_name)
				$(this).find('select[name="id_user_role"]').val(id_user_role)		
			}
		});
		$('#EdPass').on('show.bs.modal', function (event) {
			var button = $(event.relatedTarget)
			$(this).find('input[name="id_user"]').val('')
			$(this).find('input[name="username"]').val('')					            
			if(button.data('id_user') != ''){
				var id_user = button.data('id_user')
				var username = button.data('username') 
				$(this).find('input[name="id_user"]').val(id_user)	
				$(this).find('input[name="username"]').val(username)						
			}
		});
	});
</script>